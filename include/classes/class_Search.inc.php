<?php

class Search
{

    public function getSearchResults($keywords, $QueryString)
    {

        $return_data = $this->setSearchResult($keywords, $QueryString);
        $data = $this->getResults($return_data['XMLData'], $return_data['QueryString']);

        return $data;

    }

    /**
     * This function returns parsed XML
     * @param keyword [string]
     * @param querystring [string]
     * @return xmldata [string]
     */
    private function setSearchResult($keywords, $QueryString)
    {

        //Variables for the search to use
        $sites[] = "http://ecommerce-search.nextopiasoftware.com/return-results.php";
        $sites[] = "http://ecommerce-search.nextopiasoftware.net/return-results.php";
        $sites[] = "http://ecommerce-search-dyn.nextopia.net/return-results.php";

        $CLICKLOG_ON = TRUE; // TRUE or FALSE
        $CLICKLOG_COOKIES = 1; // 0 = No, 1 = yes
        $MD5_CLIENT_ID = ""; // Public Client ID
        $USE_CURL = 1;
        $result_per_page = 60;
        //Use this to rename refines. Ex: $visible_refine_names['Productline'] = "Product Line";
        $visible_refine_names = array();

        if (isset($keywords)) {

            if (!isset($QueryString)) $QueryString = $_SERVER['QUERY_STRING'];
            if (strpos($QueryString, "page") === FALSE) {
                $QueryString = "page=1&" . $QueryString;
            }


            $URLToXML = "?xml=1&client_id=" . NEXTOPIA_PRIVATE_ID . "&" . $QueryString . "&res_per_page=" . $result_per_page . "&searchtype=1";

            //Getting the IP and User Agent of the user performing the search
            $sIPAddress = $_SERVER['REMOTE_ADDR'];
            $sUserAgent = $_SERVER['HTTP_USER_AGENT'];

            $URLToXML .= "&ip=" . rawurlencode($sIPAddress) . "&user_agent=" . rawurlencode($sUserAgent);

            $URLToXML .= "&requested_refines=" . NEXTOPIA_REQUESTED_REFINES .
                "&requested_fields=" . NEXTOPIA_REQUESTED_FIELDS .
                "&trimmed_fields=" . NEXTOPIA_TRIM_FIELDS .
                "&abstracted_fields=" . NEXTOPIA_ABSTRACTED_FIELD .
                "&trim_length=" . NEXTOPIA_TRIM_LENGTH .
                "&force_sku_search=1";

            //Send the XML Query String
            $connected = FALSE;
            $times_failed = 0;
            for ($i = 0; $i < count($sites); $i++) {
                $FULLURL = $sites[$i] . $URLToXML;
                if ($USE_CURL == 1) {
                    $ch = curl_init($FULLURL);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    $XML = curl_exec($ch);
                    curl_close($ch);
                } else {
                    //-- Socket connect to the URL
                    $XML = f_socket($FULLURL, '', '', '', 5);
                    //-- Check if the connection was made properly
                    if ($XML['result'] != "Error" && $XML['error'] == "") {
                        //-- Set XML to the result and trim out the headers that come at the start of the return & check if it's valid
                        $XML = $XML['result'];
                    } else {
                        $XML = "";
                    }
                }
                if (!(strpos($XML, "<xml_feed_done>1</xml_feed_done>") == FALSE)) {

                    $XMLHeader = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
                    $XML = substr($XML, strpos($XML, $XMLHeader));
                    $connected = TRUE;

                    break;
                }
            }//for ends

            //Code to instead go to a local search would go here
            If ($connected == FALSE) {
                //-- Remove this when a proper redirect header is provided
                echo "Could not connect to servers.";
                exit;
            }


            $XMLData = XML_unserialize($XML);
        }

        $data = array(
            "XMLData" => $XMLData,
            "QueryString" => $QueryString
        );

        return $data;
    }


    /**
     * This function returns results array which contains product detail, sorting urls, breadcrum urls, sidebar array, product count
     * @param XMLData [parsed xml]
     * @param QueryString [string]
     * @return data [array]
     */
    private function getResults($XMLData, $QueryString)
    {

        $CLICKLOG_ON = "";
        $CLICKLOG_COOKIES = "";
        $MD5_CLIENT_ID = "";
        $result_per_page = 60;
        $PageOneNoSortUrl = "";
        $ProductMax = "";
        $ProductMin = "";
        $CustomSynonyms = array();

        $search_page = new Page('search');
        $search_url = $search_page->getUrl();
        $found = 0;
        //-- Merchandizing first since it could be a redirect link

        $Merchandizing = $XMLData['xml']['merchandizing']['html_code'];

        if (!empty($Merchandizing)) {

            if (strpos($Merchandizing, "link:") === 0) {

                header("Location: " . str_replace("link:", "", $Merchandizing));
                exit;
            }
        }


        //-- Preg_replace patterns to alter the URL as needed
        $replace_keywords = "/keywords=(.*?)(&|$)/";
        $replace_page = "/page=[0-9]+/";
        $replace_sorting = "/&sort_by_field=(.*?)(ASC|DESC)/";

        //-- Suggested Spelling
        $SuggestedSpelling = $XMLData['xml']['suggested_spelling'];

        //-- Custom Synonyms - it's a string if 0/1 result, an array if > 1
        if (!is_array($XMLData['xml']['custom_synonyms']['synonym']) && $XMLData['xml']['custom_synonyms']['synonym'] != '') {
            $CustomSynonyms[0] = $XMLData['xml']['custom_synonyms']['synonym'];
        } else {
            $CustomSynonyms = $XMLData['xml']['custom_synonyms']['synonym'];
        }

        //The ScratchUrl is used for spellchecked/synonym word searches as well as user search depth as we want to wipe the refinements
        $ScratchUrl = '' . $search_url . "?page=1&keywords=" . urlencode($_GET['keywords']) . '&force_sku_search=1';
        if (isset($_GET['sort_by_field']) && $_GET['sort_by_field'] != "") {
            $ScratchUrl .= "&sort_by_field=" . $_GET['sort_by_field'];
        }

        //Create the string for spellcheck and synonyms
        if ($SuggestedSpelling != "") {
            //Replace the keyword with the spellchecked word
            $CurrUrl = preg_replace($replace_keywords, "keywords=" . $SuggestedSpelling . "\\2", $ScratchUrl);
            $SuggString = "<A href='" . $CurrUrl . "&spellcheck=y'>" . $SuggestedSpelling . "</A>, ";
        }

        if (is_array($CustomSynonyms)){
            foreach ($CustomSynonyms as $syn) {
                $CurrUrl = preg_replace($replace_keywords, "keywords=" . $syn . "\\2", $ScratchUrl);
                $SuggString .= "<A href='" . $CurrUrl . "&synonym=y'>" . $syn . "</A>, ";
            }
        }

        if ($SuggString != "") {
            $SuggString = rtrim($SuggString, ", ");
            $SuggString = "Did you mean: <em>" . $SuggString . "</em>? ";
        }
        //-- Searched in field
        $SearchedIn = $XMLData['xml']['searched_in_field'];

        //'If there are > 0 total products, continue, otherwise stop
        if (intval($XMLData['xml']['pagination']['total_products']) <= 0) {

            if ($SearchedIn <> "") {
                $SearchedInString = " in " . $SearchedIn;
            }

            //Create breadcrums
            $searchBreadcrumbs = array(
                array(
                    'label' => 'Search Results',
                    'name' => $_GET['keywords'],
                    'url' => $ScratchUrl
                )
            );

        } else {

            //-- Results, different for 1 and > 1, since we know there's atleast one, we can skip checking if it's an array
            if (isset($XMLData['xml']['results']['result']['rank'])) {

                //-- Just one
                $Results[0] = $XMLData['xml']['results']['result'];
                if (NEXTOPIA_REDIRECT_ON_ONE_MATCH === TRUE && intval($XMLData['xml']['pagination']['total_products']) == 1) {

                    //redirect to page directly in case of just one product
                    $product = $Results[0]['Sku'];
                    $found = 1;
                    $sql_new_search = Connection::getHandle()->prepare(
                                       "select distinct
                                                p.id AS products_id
                                        from
                                                `bs_products` `p`
                                                inner join 
                                                    (
                                                        select 
                                                                bs_product_skus.product_id 
                                                            ,   if (count(bs_product_skus.product_id) > 0, true, false) as validity
                                                            ,   bs_skus.active
                                                            ,   group_concat(bs_skus.`name` order by bs_skus.`name`) as `name`
                                                        from
                                                                bs_product_skus 
                                                                inner join
                                                                bs_skus 
                                                                on
                                                                bs_product_skus.sku_id = bs_skus.id
                                                        where
                                                                bs_skus.active = 1
                                                        group by
                                                                bs_product_skus.product_id
                                                            ,   bs_skus.active
                                                    ) as sku                    
                                                on 
                                                (`p`.`id` = `sku`.`product_id`)

                                        where
                                                `p`.`active` = 1
                                                and 
                                                `p`.searchable = 1
                                                and 
                                                `sku`.`active` = 1
                                                and 
                                                (
                                                    p.expiration is null
                                                    or p.expiration = '0000-00-00'
                                                    or p.expiration > curdate()
                                                )");

					$sql_new_search->execute(array($product));

					$row = $sql_new_search->fetch(PDO::FETCH_ASSOC);
					$product_id = $row['products_id'];

                    $url = new Page('product', $product_id);

                    echo "<pre>".print_r($product_id, 1)."</pre>";

					if ($url->getUrl() != "" && strpos($url->getUrl(), "http://") === 0) {

                        header("Location: " . $url->getUrl());
                        exit;

                    } else {
                        //One product, but not found in database
                        $found = 0;
                    }
				}
            } else {

                //-- Numerous
                $Results = $XMLData['xml']['results']['result'];

                $ProductNum = 0;
                $product_number = array();
                for ($i = 0; $i < 100; $i++) {
                    if (isset($Results[$ProductNum])) {
                        $product_number[] = $Results[$ProductNum]['Sku'];
                        $found = (!empty($Results[$ProductNum]['Sku']) ? 1 : 0);
                    }
                    $ProductNum++;
                }

                // Query database to get product ids based on product numbers
                // prepare a string that contains ":id_0,..,:id_n" and include it in the SQL
                $prod_num = implode(",", $product_number);

                $plist = ':id_' . implode(',:id_', array_keys($product_number));

                $stmt1 = Connection::getHandle()->prepare(
                    "select distinct
                            p.id AS products_id
                        ,   p.product_number as product_number
                        ,   p.display_number AS display_number
                        ,   p.default_product_name AS name
                        ,   p.default_product_name AS nickname
                        ,   p.default_subtitle AS title
                        ,   p.search_thumbnail AS image
                        ,   case
                                when
                                        p.new_until > CURDATE() 
                                then
                                        p.new_until
                                else
                                        '0'
                            end as expiration
                    from
                            `bs_products` `p`
                            inner join 
                            (
                                select 
                                        bs_product_skus.product_id 
                                    ,   if (count(bs_product_skus.product_id) > 0, true, false) as validity
                                    ,   bs_skus.active
                                    ,   group_concat(bs_skus.`name` order by bs_skus.`name`) as `name`
                                from
                                        bs_product_skus 
                                        inner join
                                        bs_skus 
                                        on
                                        bs_product_skus.sku_id = bs_skus.id
                                where
                                        bs_skus.active = 1
                                group by
                                        bs_product_skus.product_id
                                    ,   bs_skus.active
                                ) as sku                    
                                on 
                                (`p`.`id` = `sku`.`product_id`)
                    where
                            `p`.`active` = 1
                            and 
                            `p`.searchable = 1
                            and 
                            `sku`.`active` = 1
                            and 
                            (
                                p.expiration is null
                                or p.expiration = '0000-00-00'
                                or p.expiration > curdate()
                            ) 
                            and p.product_number IN ($plist)
                    group by
                            p.product_number
                    order by
                            FIND_IN_SET( p.product_number, :prod )");

                $params = array_merge(
                    array_combine(explode(",", $plist), $product_number), array(":prod" => $prod_num)
                );

                $stmt1->execute($params);
                $product = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }

            //-- Pagination
            $TotalProducts = intval($XMLData['xml']['pagination']['total_products']);
            $ProductMin = intval($XMLData['xml']['pagination']['product_min']);
            $ProductMax = intval($XMLData['xml']['pagination']['product_max']);
            $CurrentPage = intval($XMLData['xml']['pagination']['current_page']);
            $TotalPages = intval($XMLData['xml']['pagination']['total_pages']);
            $NextPage = intval($XMLData['xml']['pagination']['next_page']);
            $PrevPage = intval($XMLData['xml']['pagination']['prev_page']);

            //-- Since we use a URL with page 1 in it a lot, just store the URL instead of doing preg_replace a lot
            $PageOneUrl = '' . $search_url . "?" . preg_replace($replace_page, "page=1", $QueryString);

            //-- Since when changing the sort method we need a url with no sorting in it, alter the PageOneUrl
            $PageOneNoSortUrl = preg_replace($replace_sorting, "", $PageOneUrl);

            //User Search Depth - this creates the links shown after Search: above the results, it allows a user to see what refinements they've done
            //For this we have to use the ScratchUrl since it cannot use the query string, which would have the refines in it already

            //Create breadcrums
            $searchBreadcrumbs = array(
                array(
                    'label' => 'Search Results',
                    'name' => $_GET['keywords'],
                    'url' => $ScratchUrl
                )
            );
            $DepthUrl = $ScratchUrl . "&refine=y";

            //-- The search depth differs in the array if there's 0, 1 or > 1 , changes depth value when we navigate to subcategory or category
            if (is_array($XMLData['xml']['user_search_depth'])) {
                if (isset($XMLData['xml']['user_search_depth']['item']['rank'])) {
                    //-- Just one
                    $DepthArray[0] = $XMLData['xml']['user_search_depth']['item'];
                } else {
                    //-- Numerous
                    $DepthArray = $XMLData['xml']['user_search_depth']['item'];
                }
                foreach ($DepthArray as $DepArr) {
                    if ($visible_refine_names[$DepArr['key']] != "") {
                        $shown_name = $visible_refine_names[$DepArr['key']];
                    } else {
                        $shown_name = $DepArr['key'];
                    }
                    $DepthUrl .= "&" . $DepArr['key'] . "=" . urlencode($DepArr['value']);
                    $searchBreadcrumbs[] = array(
                        'label' => $shown_name,
                        'name' => $DepArr['value'],
                        'url' => $DepthUrl
                    );
                }
            }

            //-- Currently Sorted By
            $SortedBy = $XMLData['xml']['currently_sorted_by'];

            //-- Fields to add to the sort by dropdown, while they can be done manually, this ensures that the fields exist

            //-- This changes when there are 0, 1, >1 sort by fields returned
            if (isset($XMLData['xml']['sort_bys']['sort_by'])) {
                if (!is_array($XMLData['xml']['sort_bys']['sort_by'])) {
                    //-- Just one
                    $SortBys[0] = $XMLData['xml']['sort_bys']['sort_by'];
                } else {
                    //-- Numerous
                    $SortBys = $XMLData['xml']['sort_bys']['sort_by'];
                }
            }

            $Refinables = array();
            //-- Refinables, different for 0, 1, >1
            if (is_array($XMLData['xml']['refinables'])) {
                if (isset($XMLData['xml']['refinables']['refinable']['name'])) {
                    //-- Just one
                    $Refinables[0] = $XMLData['xml']['refinables']['refinable'];
                } else {
                    //-- Numerous
                    $Refinables = $XMLData['xml']['refinables']['refinable'];
                }
            }

            /* create an array for side bar based on refinables */
            foreach ($Refinables as $key => $refinable_search) {

                $main_name = $refinable_search['name'];
                $last_value = $key;

                if (!isset($sidebar_array[$main_name])) {
                    $sidebar_array[$main_name] = array();
                }

                if ($last_event !== $last_value) {
                    $last_event = $last_value;

                    $sidebar_array[$main_name][] = array(
                        'name' => $main_name, 'subvalue' => array()
                    );
                }

                foreach ($refinable_search as $refine_key => $refine_value) {

                    foreach ($refine_value as $key => $value) {

                        $sub_count = count($value);
                        $refinevar = count($sidebar_array[$main_name]) - 1;

                        foreach ($value as $key1 => $value1) {
                            //generate url for subcategory or category refines
                            if (isset($_GET['refine'])) {
                                $subcategory_link = $PageOneUrl . "&";
                            } else {
                                $subcategory_link = $PageOneUrl . "&refine=y&";
                            }

                            $sidebar_array[$main_name][$refinevar]['subvalue'][] = array(
                                'sub_name' => $value1['name'],
                                'num' => $value1['num'],
                                'url' => $subcategory_link . urlencode($main_name) . '=' . urlencode($value1['name'])
                            );

                        }
                    }

                }
            }
        }
        // total product count check ends
        $data = array (
            'total_products'     => isset($TotalProducts) ? $TotalProducts : NULL,
            'product_min'        => isset($ProductMin) ? $ProductMin : NULL,
            'product_max'        => isset($ProductMax) ? $ProductMax : NULL,
            'product'            => isset($product) ? $product : NULL,
            'subcategory_link'   => isset($subcategory_link) ? $subcategory_link : NULL,
            'breadcrumbs'        => isset($searchBreadcrumbs) ? $searchBreadcrumbs : NULL,
            'total_pages'        => isset($TotalPages) ? $TotalPages : NULL,
            'current_page'       => isset($CurrentPage) ? $CurrentPage : NULL,
            'replace_page'       => isset($replace_page) ? $replace_page : NULL,
            'PageOneUrl'         => isset($PageOneUrl) ? $PageOneUrl : NULL,
            'sortUrl'            => isset($PageOneNoSortUrl) ? $PageOneNoSortUrl : NULL,
            'suggested_spelling' => isset($SuggestedSpelling) ? $SuggestedSpelling : NULL,
            'sugg_string'        => isset($SuggString) ? $SuggString : NULL,
            'clicklog'           => isset($CLICKLOG_ON) ? $CLICKLOG_ON : NULL,
            'click_cookies'      => isset($CLICKLOG_COOKIES) ? $CLICKLOG_COOKIES : NULL,
            'MD_CLIENT_ID'       => isset($MD5_CLIENT_ID) ? $MD5_CLIENT_ID : NULL,
            'page_limit'         => isset($result_per_page) ? $result_per_page : NULL,
            'sidebar_array'      => isset($sidebar_array) ? $sidebar_array : NULL,
            'result_found'       => isset($found) ? $found : NULL
        );

        return $data;

    }//function ends here
}