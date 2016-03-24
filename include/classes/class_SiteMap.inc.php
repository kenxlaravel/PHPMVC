<?php


/**
 * Class SiteMap
 */
class SiteMap {

	private $dbh;
	private $xml_cache;
	private $image_url;
	private $error;


	public function __construct() {

		// Set private variables.
		$this->xml_cache =  APP_ROOT . '/cache/sitemap.xml';
		$this->image_url = IMAGE_URL_PREFIX_FULL . "/images/catlog/product/medium/";
	}

	/**
	*This function gets list of all landing page URL & Name
	*/
	public function getLandingPages() {

		//Declare output array
        $landingpages = array();

		// Query the database to get landing page id
		$stmt = "SELECT
			        t.pagetype AS type, l.id AS id, n.nickname AS nickname, IF(COUNT(l.id) > 0, TRUE, FALSE) AS validity,
			        l.name AS name, u.url AS short_url, t.template_secure AS secure, l.slug AS slug, t.template_filename AS filename,
			        l.id AS canonical, l.page_title AS title, l.meta_description AS meta_description, l.meta_keywords AS meta_keywords,
			        l.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,t.disallow_guests AS disallow_guests,
			        l.sitemap_show AS visibility, l.sitemap_page_priority AS priority, l.sitemap_page_change_frequency AS change_frequency
		         FROM bs_landings l JOIN bs_pagetypes t
                 LEFT JOIN bs_page_urls u ON (u.id = l.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = l.id)
		         LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND l.id = n.pageid) WHERE l.active = 1 AND t.pagetype = 'landing'
		         GROUP BY l.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            //Loop through each results
            foreach ($data as $row) {

                //Instantiate Page class
                $Landing = Page::create('landing', $row['id'], $row);

                if( $Landing->getVisibility() ) {
                    //Insert values to output array
                    $landingpages[] = array (
                        "name"       => $Landing->getName(),
                        "url"        => $Landing->getUrl(),
                        "changefreq" => $Landing->getChangeFrequency(),
                        "priority"   => $Landing->getPriority()
                    );
                }

            }
        }
		//sort result
		sort($landingpages);

		//return results
		return $landingpages;
	}

	/**
	* This function gets URL and name for all the categories
	*/
	public function getCategoryAndSubCategoryPages(){

		//Declare output array
		$categories = array();

		//Query Database to get category page id
 		$stmt = "SELECT 'subcategory' as `pagetype` ,sb.id as pageid ,gr.category_id as category_id FROM bs_subcategories sb
                 LEFT JOIN bs_groupings gr ON gr.id=sb.grouping_id
                 LEFT JOIN bs_categories c ON c.id=gr.category_id WHERE sb.active=1 and gr.active=1 and c.active=1
				 UNION SELECT 'grouping' as 'pagetype', gr.id as pageid , gr.category_id as category_id FROM bs_groupings gr
                 LEFT JOIN bs_subcategories sb on sb.grouping_id=gr.id LEFT JOIN bs_categories c ON c.id=gr.category_id
                 WHERE gr.active=1 and c.active=true and sb.active=1";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            //Loop through each result
            while ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                $id = $row['category_id'];

                //check if array already exists
                if( !isset($categories[$id]) ) {

                    //Instantiate Page class with pagetype
                    $Category = new Page("category", $id);

                    //Check if sitemap visible true/false
                    if( $Category->getVisibility() ) {

                        //Insert values to output array
                        $categories[$id] = array (
                            "name"          => $Category->getName(),
                            "url"           => $Category->getUrl(),
                            "changefreq"    => $Category->getChangeFrequency(),
                            "priority"      => $Category->getPriority(),
                            "subcategories" => array ()
                        );
                    }
                }
                //Instantiate Page class for pagetype subcategory
                $Subcategory = Page::create($row["pagetype"], $row["pageid"]);
                //Check if sitemap visible true/false
                if( $Subcategory->getVisibility() && $Category->getVisibility() ) {
                    //Insert values to output array
                    $categories[$id]["subcategories"][] = array (
                        "name"       => $Subcategory->getName(),
                        "url"        => $Subcategory->getUrl(),
                        "changefreq" => $Subcategory->getChangeFrequency(),
                        "priority"   => $Subcategory->getPriority()
                    );
                }
            }
        }

		sort($categories);

		return $categories;
	}


	/**This function returns list of category pages */
	public function getCategoryPages() {

		//Declare output array
		$groupings = array();

		//Query database to get category pages id
		$stmt = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
			        c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
			        c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
			        c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
			        c.sitemap_show AS visibility, c.sitemap_page_priority AS priority, c.sitemap_page_change_frequency AS change_frequency
		         FROM bs_categories c JOIN bs_pagetypes t
                 LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
		         LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid) WHERE c.active = 1 AND t.pagetype = 'category'
		         GROUP BY c.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                //Instantiate page class for pagetype
                $Category = Page::create('category', $row['id'], $row);

                //Check visibility for sitemap
                if( $Category->getVisibility() ) {

                    //Insert data to output array
                    $categories[] = array (
                        "name"       => $Category->getName(),
                        "url"        => $Category->getUrl(),
                        "changefreq" => $Category->getChangeFrequency(),
                        "priority"   => $Category->getPriority()
                    );
                }
            }
        }

		//Sort array by name
		sort($categories);

		return $categories;
	}


	/**This function returns list of grouping pages */
	public function getGroupingPages() {

		//Declare output array
		$groupings = array();

		//Query database to get grouping pages id
		$stmt = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
			        c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
			        c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
			        c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
			        c.sitemap_show AS visibility, c.sitemap_page_priority AS priority, c.sitemap_page_change_frequency AS change_frequency
		         FROM bs_groupings c JOIN bs_pagetypes t
                 LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
		         LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
                 LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
		         WHERE cat.active = 1 AND c.active = 1 AND t.pagetype = 'grouping' GROUP BY c.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            //Loop through each result
            while ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                //Instantiate page class for pagetype
                $Grouping = Page::create('grouping', $row['id'], $row);

                //Check visibility for sitemap
                if( $Grouping->getVisibility() ) {

                    //Insert data to output array
                    $groupings[] = array (
                        "name"       => $Grouping->getName(),
                        "url"        => $Grouping->getUrl(),
                        "changefreq" => $Grouping->getChangeFrequency(),
                        "priority"   => $Grouping->getPriority()
                    );
                }
            }
        }

		//Sort array by name
		sort($groupings);

		return $groupings;
	}

	/**This function returns list of subcategory pages */
	public function getSubCategoryPages(){

		//Declare output arrays
		$subcategories = array();
		$geotargeted = array();

		//Query database to get subcategory page id
		$stmt = "SELECT
                    t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
			        c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
			        c.id AS canonical, c.page_title AS title,c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
			        c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
			        c.sitemap_show AS visibility, c.sitemap_page_priority AS priority, c.sitemap_page_change_frequency AS change_frequency
		         FROM bs_subcategories c JOIN bs_pagetypes t
		         LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
		         LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id) LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
		         LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
		         WHERE cat.active = 1 AND grp.active = 1 AND c.active = 1 AND t.pagetype = 'subcategory' GROUP BY c.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {
            //Loop through each result
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                //Instantiate page class for subcategory
                $Subcategory = new Page('subcategory', $row['id'], $row);
                //Check if visible for sitemap
                if( $Subcategory->getVisibility() ) {
                    //Insert data to resulting array
                    $subcategories[] = array (
                        "name"       => $Subcategory->getName(),
                        "url"        => $Subcategory->getUrl(),
                        "changefreq" => $Subcategory->getChangeFrequency(),
                        "priority"   => $Subcategory->getPriority()
                    );

                }

            }
        }

		//Query database to get all geotargeted pages
		$stmt = "SELECT
			        t.pagetype AS type,
			        c.id AS id,
			        n.nickname AS nickname,
			        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
			        z.zone_name AS name,
			        u.url AS short_url,
			        t.template_secure AS secure,
			        c.slug AS slug,
			        t.template_filename AS filename,
			        c.id AS canonical,
			        c.page_title AS title,
			        c.meta_description AS meta_description,
			        c.meta_keywords AS meta_keywords,
			        c.page_heading AS heading,
			        t.allow_target AS allow_target,
			        t.requires_login AS requires_login,
			        t.disallow_guests AS disallow_guests,
			        c.sitemap_show AS visibility,
			        c.sitemap_page_priority AS priority,
			        c.sitemap_page_change_frequency AS change_frequency
		        FROM bs_subcategories_geotargeted c
		        JOIN bs_pagetypes t
		        LEFT JOIN bs_zones z ON (z.zone_code = c.target)
		        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
		        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
		        LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id)
		        LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
		        LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
		        WHERE cat.active = 1
		        AND grp.active = 1
		        AND sub.active = 1
		        AND c.active = 1
		        AND t.pagetype = 'geotarget'
		        GROUP BY c.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {
            //Loop through each result
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                //Instantiate page class for pagetype
                $Geotarget = new Page('geotarget', $row['id'], $row);
                //Check for sitemap visibility
                if( $Geotarget->getVisibility() ) {
                    //Insert data to resulting array
                    $geotargeted[] = array (
                        "name"       => $Geotarget->getName(),
                        "url"        => $Geotarget->getUrl(),
                        "changefreq" => $Geotarget->getChangeFrequency(),
                        "priority"   => $Geotarget->getPriority()
                    );

                }

            }
        }

		$merged = array_merge($subcategories, $geotargeted);

		sort($merged);

		return $merged;

	}

	/**This function returns list of product pages */
	public function getProductPages() {

		//Declare output array
		$products = array();

		//Query database to get product id
		$stmt = "SELECT
					c.by_legend AS image_caption,
					c.page_subtitle AS image_title,
					c.search_thumbnail AS image_loc,
					IF(c.custom = 0, TRUE, FALSE) AS stock_product,
				    t.pagetype AS type,
				    c.id AS id,
				    n.nickname AS nickname,
				    IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
				    c.default_product_name AS name,
				    u.url AS short_url,
				    t.template_secure AS secure,
				    c.url_slug AS slug,
				    t.template_filename AS filename,
				    c.id AS canonical,
				    c.page_title AS title,
				    c.meta_description AS meta_description,
				    c.meta_keywords AS meta_keywords,
				    h.name AS heading,
				    t.allow_target AS allow_target,
				    t.requires_login AS requires_login,
				    t.disallow_guests AS disallow_guests,
				    c.sitemap_show AS visibility,
				    c.page_priority AS priority,
				    cf.name AS change_frequency
				FROM bs_products c
				LEFT JOIN bs_headers h ON (h.id = c.header_id)
				LEFT JOIN bs_change_frequencies cf ON (cf.id = c.change_frequency_id)
				JOIN bs_pagetypes t
				LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url_id AND u.pagetype = t.pagetype AND u.pageid = c.id)
				LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
				LEFT JOIN bs_subcategory_products sp ON (sp.product_id = c.id)
				LEFT JOIN bs_subcategories sub ON (sub.id = sp.subcategory_id)
				LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
				LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
				WHERE cat.active = 1
				AND grp.active = 1
				AND sub.active = 1
				AND c.active = 1
				AND t.pagetype = 'product'
				AND ( c.expiration IS NULL OR c.expiration = '0000-00-00' OR c.expiration > CURDATE() )
				GROUP BY c.product_number";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {
            //Loop through each result
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                //Instantiate page class for product
                $Product = new Page('product', $row['id'], $row);
                //check for product visibility for sitemap & if product is canonical
                if( $Product->getVisibility() && $Product->isCanonical() ) {
                    //Insert data to resulting array
                    $thisproduct = array (
                        "name"       => $Product->getName(),
                        "url"        => $Product->getUrl(),
                        "changefreq" => $Product->getChangeFrequency(),
                        "priority"   => $Product->getPriority()
                    );
                    if( $row['stock_product'] && !empty($row['image_loc']) ) {
                        $thisproduct["image_loc"] = $this->image_url.rawurlencode($row['image_loc']);
                        $thisproduct["image_caption"] = $row["image_caption"];
                        $thisproduct["image_title"] = $row["image_title"];
                    }
                    $products[] = $thisproduct;

                }

            }
        }

		//sort resulting array
		sort($products);

		return $products;

	}

	/**
	* This function gets all the page URL and name
	*/
	public function getStaticPages() {

		//Declare resulting array
        $query     = NULL;
		$pages     = array();
		$helppages = array();

		//Query database to get pages
		$stmt = "SELECT
				    t.pagetype AS type,
				    c.id AS id,
				    n.nickname AS nickname,
				    IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
				    c.name AS name,
				    u.url AS short_url,
				    c.secure AS secure,
				    NULL AS slug,
				    c.filename AS filename,
				    c.id AS canonical,
				    c.title AS title,
				    c.meta_description AS meta_description,
				    c.meta_keywords AS meta_keywords,
				    c.heading AS heading,
				    c.allow_target AS allow_target,
				    c.requires_login AS requires_login,
				    c.disallow_guests AS disallow_guests,
				    c.sitemap_show AS visibility,
				    c.sitemap_page_priority AS priority,
				    c.sitemap_page_change_frequency AS change_frequency
				FROM bs_pages c
				JOIN bs_pagetypes t
				LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
				LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
				WHERE t.pagetype = 'page'
				GROUP BY c.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            //Loop through each result
            while  ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                //Instantiate page class for pagetype
                $static_page = new Page('page', $row['id'], $row);

                //Check for sitemap visibility
                if( $static_page->getVisibility() ) {

                    //Insert values to output array
                    $pages[] = array (
                        "name"       => $static_page->getName(),
                        "url"        => $static_page->getUrl(),
                        "changefreq" => $static_page->getChangeFrequency(),
                        "priority"   => $static_page->getPriority()
                    );

                }

            }
        }
		//Query database to get help pages
		$stmt = "SELECT
				    t.pagetype AS type,
				    h.id AS id,
				    n.nickname AS nickname,
				    IF(COUNT(h.id) > 0, TRUE, FALSE) AS validity,
				    h.name AS name,
				    u.url AS short_url,
				    t.template_secure AS secure,
				    h.slug AS slug,
				    t.template_filename AS filename,
				    h.id AS canonical,
				    h.page_title AS title,
				    h.meta_description AS meta_description,
				    h.meta_keywords AS meta_keywords,
				    h.page_heading AS heading,
				    t.allow_target AS allow_target,
				    t.requires_login AS requires_login,
				    t.disallow_guests AS disallow_guests,
				    h.sitemap_show AS visibility,
				    h.sitemap_page_priority AS priority,
				    h.sitemap_page_change_frequency AS change_frequency
				FROM bs_help h
				JOIN bs_pagetypes t
				LEFT JOIN bs_page_urls u ON (u.id = h.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = h.id)
				LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND h.id = n.pageid)
				WHERE t.pagetype = 'help'
				GROUP BY h.id";

        $query = Connection::getHandle()->prepare($stmt);

        if( $query->execute() ) {

            //Loop through each result
            while ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                //Instantiate page class for pagetype
                $help = new Page('help', $row['id'], $row);

                //Check for sitemap visibility
                if( $help->getVisibility() ) {

                    //Insert values to output array
                    $helppages[] = array (
                        "name"       => $help->getName(),
                        "url"        => $help->getUrl(),
                        "changefreq" => $help->getChangeFrequency(),
                        "priority"   => $help->getPriority()
                    );
                }
            }
        }

		$merged=array_merge($pages, $helppages);
		sort($merged);

		return $merged;

	}

	/**
	 * This function gets the XML from the cache file (or creates a new cache file if one doesn't already
	 * exist), then returns it as a string.
	 */
	public function getXml() {

		// If the cache file doesn't already exist, create it.
		if (!file_exists($this->xml_cache)) {
			$this->cacheXml();
		}

		// Return the contents of the cache file as a string.
		return file_get_contents($this->xml_cache);

	}

	private function toXml() {

		//create new instance for XML DOMDocument
		$XML = new DOMDocument("1.0", "UTF-8");

		//append name space to dom element
		$root = $XML->appendChild($XML->createElementNS("http://www.sitemaps.org/schemas/sitemap/0.9", "urlset"));
		$root->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:image", "http://www.google.com/schemas/sitemap-image/1.1");

		// SEO landing pages
		foreach ($this->getLandingPages() as $landing) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($landing["url"])));
			$url->appendChild($XML->createElement("changefreq", $landing["changefreq"]));
			$url->appendChild($XML->createElement("priority", $landing["priority"]));

		}

		// Category pages
		foreach ($this->getCategoryPages() as $category) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($category["url"])));
			$url->appendChild($XML->createElement("changefreq", $category["changefreq"]));
			$url->appendChild($XML->createElement("priority", $category["priority"]));

		}

		// Grouping pages
		foreach ($this->getGroupingPages() as $grouping) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($grouping["url"])));
			$url->appendChild($XML->createElement("changefreq", $grouping["changefreq"]));
			$url->appendChild($XML->createElement("priority", $grouping["priority"]));

		}

		// Subcategory & Geotargeted pages
		foreach ($this->getSubCategoryPages() as $subcategory) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($subcategory["url"])));
			$url->appendChild($XML->createElement("changefreq", $subcategory["changefreq"]));
			$url->appendChild($XML->createElement("priority", $subcategory["priority"]));

		}

		// Product pages
		foreach ($this->getProductPages() as $product) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($product["url"])));
			$url->appendChild($XML->createElement("changefreq", $product["changefreq"]));
			$url->appendChild($XML->createElement("priority", $product["priority"]));

			//check if image exists
			if (!empty($product["image_loc"])) {

				$image = $url->appendChild($XML->createElement("image:image"));

				$image->appendChild($XML->createElement("image:loc", $this->encode_xml_string($product["image_loc"])));
				$image->appendChild($XML->createElement("image:caption", $this->encode_xml_string($product["image_caption"])));
				$image->appendChild($XML->createElement("image:title", $this->encode_xml_string($product["image_title"])));

			}

		}

		// Static pages
		foreach ($this->getStaticPages() as $static) {

			$url = $root->appendChild($XML->createElement("url"));

			$url->appendChild($XML->createElement("loc", $this->encode_xml_string($static["url"])));
			$url->appendChild($XML->createElement("changefreq", $static["changefreq"]));
			$url->appendChild($XML->createElement("priority", $static["priority"]));

		}

		return $XML->saveXml();

	}

	//**This function is to create XML for Sitemap*/
	private function cacheXml() {

		$directory = pathinfo($this->xml_cache, PATHINFO_DIRNAME);

		// If the directory doesn't already exist, try to create it (return false on failure).
		if (!is_dir($directory)) {
			if (!mkdir($directory, 0777, true)) {
				return false;
			}
		}

		// If the directory has the wrong permissions, try fixing them (return false on failure).
		if (!is_writable($directory)) {
			if (!chmod($directory, 0777)) {
				return false;
			}
		}

		// Try creating the cache file (return false on failure).
		if (!file_put_contents($this->xml_cache, $this->toXml())) {
			return false;
		}

		// Try to fix the cache file permissions, but don't return false on failure; just proceed.
		chmod($this->xml_cache, 0777);

		// Notify the search engines of the new file.
		$this->ping();

		// If the function got here it means the cache file was successfully created, so return true.
		return true;

	}

	public function updateXmlCache() {
		return $this->cacheXml();
	}

	private function ping() {

		$public_url = URL_PREFIX_HTTP . "/sitemap.xml";

		$urls = array();
		$urls['Google'] = "http://www.google.com/webmasters/tools/ping?sitemap=" . rawurlencode($public_url);
		$urls['Bing'] = "http://www.bing.com/webmaster/ping.aspx?siteMap=" . rawurlencode($public_url);
		$urls['Ask'] = "http://submissions.ask.com/ping?sitemap=" . rawurlencode($public_url);

		foreach ($urls as $site => $ping_url) {

			$ch = curl_init($ping_url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if (200 != $retcode) {
				error_log(date("F j, Y, g:i a") . ' [error] Could not ping sitemap to ' . $site . ' URL: ' . $ping_url);
			}

		}

	}

	private function encode_xml_string($string) {

		foreach (array( "&" => "amp", "\"" => "quot", "'" => "apos", "<" => "lt", ">" => "gt" ) as $char => $name) {

			$char_len = mb_strlen($char);
			$entity = "&" . $name . ";";
			$entity_len = mb_strlen($entity);
			$pos = mb_strpos($string, $char);

			while ($pos !== false) {
				$string = mb_substr($string, 0, $pos) . $entity . mb_substr($string, $pos + $char_len);
				$pos = mb_strpos($string, $char, $pos + $entity_len);
			}

		}

		return $string;

	}

}
