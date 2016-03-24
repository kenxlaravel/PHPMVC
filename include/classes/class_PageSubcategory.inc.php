<?php

/**
 * Class PageSubcategory
 */
class PageSubcategory {

    //Properties
    public $imagePath = array();
    public $id;
    public $pagetype;

    //Pulled from database
    public $name;
    public $slug;
    public $grouping_id;
    public $template;
    public $page_title;
    public $page_heading;
    public $meta_keywords;
    public $meta_description;
    public $image;
    public $snippet;
    public $description_text_html;
    public $description_image;
    public $description_more_info_html;
    public $geotarget_grid_size;
    public $geotarget_state_list_header;
    public $geotarget_state_list_intro;
    public $geotarget_dropdown_snippet;
    public $geotarget_dropdown_button;
    public $geotarget_grid_header;
    public $geotarget_grid_intro;
    public $geotarget_grid_alignment;
    public $geotarget_show_product_number;
    public $geotarget_show_quickview;
    public $geotarget_show_filter;
    public $geotarget_show_sort;
    public $grid_header;
    public $grid_intro;
    public $grid_size;
    public $grid_alignment;
    public $show_product_number;
    public $show_quickview;
    public $show_filter;
    public $show_sort;
    public $federal_enabled;
    public $target;
    public $detailed;
    public $special_header_class_name;
    public $intro_supplement_html;
    public $popup_html;
    private $dbh;
    private $force;


    //Constructor

    public function __construct($id, $pagetype, $force = false) {

        //Establish a database connection
        $this->setDatabase();

        $this->force = !!$force;

        //Some functions can be used without a id, but a number of core properties will not exist
        if ($id) {
            $this->id = $id;
            $this->pagetype = $pagetype;
            $this->getProperties();
        }


        //Set the image paths
        $this->imagePath['grid'] = IMAGE_URL_PREFIX . '/images/catlog/product/small/';
        $this->imagePath['description'] = IMAGE_URL_PREFIX;

    }


    /**
     * This function checks to make sure we have a PDO instance, and sets our class variable
     * If we do not, we instantiate a new connection
     */
    private function setDatabase() {

        global $dbh;

        if ($dbh instanceof PDO) {
            $this->dbh = $dbh;
        } else {
            $Connection = new Connection();
            $this->dbh = $Connection->PDO_Connection();
        }

    }


    /**
     * Main properties function to get most of what we need about the page on instantiation
     * and set as class variables.
     */
    private function getProperties() {

        global $dbh;

        //If we do not already have this info for the current page, grab it
        if (empty($this->name)) {

            //If this is a geotargeted page, the properties are a little different
            if ($this->pagetype == 'geotarget') {
                $stmt = $this->dbh->prepare("SELECT s.name AS name, t.target AS target, t.slug AS slug,
														s.grouping_id AS grouping_id,
														s.template AS template,
														t.page_title AS page_title,
														t.page_heading AS page_heading,
														t.meta_keywords AS meta_keywords,
														t.meta_description AS meta_description,
														s.image AS image,
														s.snippet AS snippet,
														t.description_text_html AS description_text_html,
														t.description_image AS description_image,
														t.description_more_info_html AS description_more_info_html,
														t.grid_size AS geotarget_grid_size,
														s.geotarget_state_list_header AS geotarget_state_list_header,
														s.geotarget_state_list_intro AS geotarget_state_list_intro,
														s.geotarget_dropdown_snippet AS geotarget_dropdown_snippet,
														s.geotarget_dropdown_button AS geotarget_dropdown_button,
														s.grid_header AS grid_header,
														s.grid_intro AS grid_intro,
														s.grid_size AS grid_size,
														s.grid_alignment AS grid_alignment,
														s.show_product_number AS show_product_number,
														s.show_quickview AS show_quickview,
														s.show_filter AS show_filter,
														s.show_sort AS show_sort,
														t.special_header_class_name AS special_header_class_name,
														t.federal_enabled AS federal_enabled,
														t.grid_header AS geotarget_grid_header,
														t.grid_intro AS geotarget_grid_intro,
														t.grid_size AS geotarget_grid_size,
														t.grid_alignment AS geotarget_grid_alignment,
														t.show_product_number AS geotarget_show_product_number,
														t.show_quickview AS geotarget_show_quickview,
														t.show_filter AS geotarget_show_filter,
														t.show_sort AS geotarget_show_sort

												 FROM bs_subcategories_geotargeted t
												 LEFT JOIN bs_subcategories s ON (t.subcategory_id = s.id)
												 WHERE t.id=?
												 LIMIT 1");
            } else {
                $stmt = $this->dbh->prepare("SELECT s.*, COUNT(d.id) AS detailed FROM bs_subcategories s
												 LEFT JOIN bs_subcategories_detailed d ON (s.id = d.subcategory_id)
												 WHERE s.id=?
												 LIMIT 1");
            }

            $stmt->execute(array($this->id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            //Set class properties
            $this->name = $row['name'];
            $this->slug = $row['slug'];
            $this->grouping_id = $row['grouping_id'];
            $this->template = $row['template'];
            $this->page_title = $row['page_title'];
            $this->page_heading = $row['page_heading'];
            $this->meta_keywords = $row['meta_keywords'];
            $this->meta_description = $row['meta_description'];
            $this->image = $row['image'];
            $this->snippet = $row['snippet'];
            $this->description_text_html = $row['description_text_html'];
            $this->description_image = $row['description_image'];
            $this->image_template = $row['image_template'] ? trim($row['image_template']) . '-banner' : 'basic-banner';
            $this->description_more_info_html = $row['description_more_info_html'];
            $this->geotarget_grid_size = $row['geotarget_grid_size'];
            $this->geotarget_state_list_header = $row['geotarget_state_list_header'];
            $this->geotarget_state_list_intro = $row['geotarget_state_list_intro'];
            $this->geotarget_dropdown_snippet = $row['geotarget_dropdown_snippet'];
            $this->geotarget_dropdown_button = $row['geotarget_dropdown_button'];
            $this->grid_header = $row['grid_header'];
            $this->grid_intro = $row['grid_intro'];
            $this->grid_size = $row['grid_size'];
            $this->grid_alignment = $row['grid_alignment'];
            $this->show_product_number = $row['show_product_number'];
            $this->show_quickview = $row['show_quickview'];
            $this->show_filter = $row['show_filter'];
            $this->show_sort = $row['show_sort'];
            $this->special_header_class_name = $row['special_header_class_name'];
            $this->intro_supplement_html = $row['intro_supplement_html'];
            $this->popup_html = $row['popup_html'];


            //Geotarget-specific properties that will not exist when this is not a state-specific page
            if (!empty($row['federal_enabled'])) {
                $this->federal_enabled = $row['federal_enabled'];
            } else {
                $this->federal_enabled = NULL;
            }
            if (!empty($row['geotarget_grid_header'])) {
                $this->geotarget_grid_header = $row['geotarget_grid_header'];
            } else {
                $this->geotarget_grid_header = NULL;
            }
            if (!empty($row['geotarget_grid_intro'])) {
                $this->geotarget_grid_intro = $row['geotarget_grid_intro'];
            } else {
                $this->geotarget_grid_intro = NULL;
            }
            if (!empty($row['grid_size'])) {
                $this->geotarget_grid_size = $row['geotarget_grid_size'];
            } else {
                $this->geotarget_grid_size = NULL;
            }
            if (!empty($row['grid_alignment'])) {
                $this->geotarget_grid_alignment = $row['geotarget_grid_alignment'];
            } else {
                $this->geotarget_grid_alignment = NULL;
            }
            if (!empty($row['target'])) {
                $this->target = $row['target'];
            } else {
                $this->target = 'federal';
            }

            $this->geotarget_show_product_number = $row['geotarget_show_product_number'];
            $this->geotarget_show_quickview = $row['geotarget_show_quickview'];
            $this->geotarget_show_filter = $row['geotarget_show_filter'];
            $this->geotarget_show_sort = $row['geotarget_show_sort'];

            //Detailed
            if (!empty($row['detailed'])) {
                $this->detailed = $row['detailed'];
            } else {
                $this->detailed = FALSE;
            }
        }
    }


    /**
     * Used to get listings based off of a location on the page. Example: 'grid' will return all
     * listings on the sub-category page grid (aka products)
     *
     * @param     string $location The location on the page that we need listings for
     * @param     int    $refid    And additional id for reference, if needed.
     * @return    array            And array of all the listings
     */
    public function getListings($location, $refid = NULL) {

        global $dbh, $ObjPageProduct;

        switch ($location) {

            case 'grid':

                $product_row = array();

                if ($this->pagetype == 'subcategory') {

                    // Note: Because product canonicalization queries are slow and unnescessary on subcategory
                    // pages, this query always uses the products_id as the canonical rather than the true canonical.
                    $stmt = $this->dbh->prepare("SELECT
															c.products_id AS id,
															c.products_id AS canonical,
															t.pagetype AS pagetype,
															IF(COUNT(c.products_id) > 0, TRUE, FALSE) AS validity,
															c.product_nickname AS nickname,
															c.product_nickname AS name,
															u.url AS short_url,
															t.template_secure AS secure,
															t.template_filename AS filename,
															c.product_title AS title,
															c.product_meta_description AS meta_description,
															c.product_meta_keyword AS meta_keywords,
															c.page_heading AS heading,
															t.allow_target AS allow_target,
															t.requires_login AS requires_login,
															t.disallow_guests AS disallow_guests,
															c.xml_show AS visibility,
															c.pri AS priority,
															c.freq AS change_frequency,
															c.on_sale AS on_sale,
															cat.name AS category_proper,
															grp.name AS grouping_proper,
															sub.name AS subcategory_proper,
															GROUP_CONCAT(DISTINCT l2.language) AS `languages`,
															l1.`language` AS `language`,
															COUNT(DISTINCT l2.product_number_language) AS language_count,
															b.lightweight,
															CASE
																WHEN c.new_product_expire_date > CURDATE() THEN c.new_product_expire_date
																ELSE '0'
															END AS expiration,
															(SUM(IF(`psd`.`limited_inventory` = TRUE AND `psd`.`inventory` > 0, 1, 0)) > 0) AS `is_limited`,
															(SUM(IF((`psd`.`inventory` > 0 OR `psd`.`limited_inventory` = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0) AS `is_stocked`,
															IF(MIN(IF(`psd`.`ul_recognized` = TRUE, 1, 0)) = 1, TRUE, FALSE) AS fully_ulrecognized,
								             				CASE WHEN SUM(`psd`.`ul_recognized`) > 0 THEN 1 ELSE 0 END AS ulrecognized,
															c.*
														FROM bs_products c
														LEFT JOIN bs_products_sku_description psd ON (c.product_number = psd.product_number AND psd.`active` = 'Y')
														INNER JOIN bs_pagetypes t ON ( t.pagetype = 'product' )
														LEFT JOIN bs_builder b ON (b.builder_ref = c.builder_ref AND b.active = 'Y')
														LEFT JOIN bs_products_languages l1 ON (c.product_number = l1.product_number_language AND l1.active='Y')
														LEFT JOIN bs_products_languages l2 ON (l2.product_number = l1.product_number)
														LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.products_id)
														LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id)
														LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
														LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
														WHERE cat.active = TRUE
														AND (c.subcategory_detailed_id IS NULL OR c.subcategory_detailed_id = 0)
														AND grp.active = TRUE" . ($this->force ? ' ' : " AND sub.active = TRUE ") . "AND c.grid_display = TRUE
														AND c.active = 'Y'
														AND sub.id = ?
														AND (c.target IS NULL OR c.target = '')
														AND ( c.expiration_date IS NULL OR c.expiration_date = '0000-00-00' OR c.expiration_date > CURDATE() )
														GROUP BY c.products_id" . ($this->force ? ' ' : " HAVING SUM(IF((psd.inventory > 0 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0 ") . "ORDER BY c.position, c.product_number ASC");

                    $stmt->execute(array($this->id));

                } else if ($this->pagetype == 'geotarget') {

                    // Note: Because product canonicalization queries are slow and unnescessary on geotargeted
                    // pages, this query always uses the products_id as the canonical rather than the true canonical.

                    $stmt = $this->dbh->prepare("SELECT c.products_id AS id, c.products_id AS canonical, t.pagetype AS pagetype,
                                                 IF(COUNT(c.products_id) > 0, TRUE, FALSE) AS validity, c.product_nickname AS nickname,
                                                 c.product_nickname AS name, u.url AS short_url, t.template_secure AS secure, t.template_filename AS filename,
                                                 c.product_title AS title, c.product_meta_description AS meta_description, c.product_meta_keyword AS meta_keywords,
                                                 c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,
                                                 t.disallow_guests AS disallow_guests, c.xml_show AS visibility, c.pri AS priority, c.freq AS change_frequency,
                                                 c.on_sale AS on_sale, cat.name AS category_proper, grp.name AS grouping_proper, sub.name AS subcategory_proper,
                                                 GROUP_CONCAT(DISTINCT l2.language) AS `languages`, l1.`language` AS `language`, COUNT(DISTINCT l2.product_number_language) AS language_count,
                                                 b.lightweight, CASE WHEN c.new_product_expire_date > CURDATE() THEN c.new_product_expire_date ELSE '0' END AS expiration,
                                                 (SUM(IF(`psd`.`limited_inventory` = TRUE AND `psd`.`inventory` > 0, 1, 0)) > 0) AS `is_limited`,
                                                 (SUM(IF((`psd`.`inventory` > 0 OR `psd`.`limited_inventory` = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0) AS `is_stocked`,
                                                 IF(MIN(IF(`psd`.`ul_recognized` = TRUE, 1, 0)) = 1, TRUE, FALSE) AS fully_ulrecognized,
                                                 CASE WHEN SUM(`psd`.`ul_recognized`) > 0 THEN 1 ELSE 0 END AS ulrecognized, c.* FROM bs_products c
                                                 LEFT JOIN bs_products_sku_description psd ON (c.product_number = psd.product_number AND psd.`active` = 'Y')
                                                 INNER JOIN bs_pagetypes t ON ( t.pagetype = 'product' )
                                                 LEFT JOIN bs_subcategories_geotargeted subg ON (c.subcategory_id = subg.subcategory_id)
                                                 LEFT JOIN bs_builder b ON (b.builder_ref = c.builder_ref AND b.active = 'Y')
                                                 LEFT JOIN bs_products_languages l1 ON (c.product_number = l1.product_number_language AND l1.active='Y')
                                                 LEFT JOIN bs_products_languages l2 ON (l2.product_number = l1.product_number)
                                                 LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.products_id)
                                                 LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id) LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
                                                 LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
                                                 WHERE cat.active = TRUE AND grp.active = TRUE " . ($this->force ? ' ' : " AND sub.active = TRUE ") . " AND c.active = 'Y' AND subg.id = ?
                                                 AND c.grid_display= TRUE AND c.target = ? AND (c.subcategory_detailed_id = 0 OR c.subcategory_detailed_id IS NULL)
                                                 AND ( c.expiration_date IS NULL OR c.expiration_date = '0000-00-00' OR c.expiration_date > CURDATE() )
                                                 GROUP BY c.products_id" . ($this->force ? ' ' : " HAVING SUM(IF((psd.inventory > 0
                                                 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0 ") . "ORDER BY c.position, c.product_number ASC");

                    $stmt->execute(array($this->id, $this->target));
                }

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    if ($row['products_id'] > 0) {
                        $product_row[] = $row;
                    }
                }

                return $product_row;

                break;


            case 'federal':

                // Note: Because product canonicalization queries are slow and unnescessary on geotargeted
                // pages, this query always uses the products_id as the canonical rather than the true canonical.
                $stmt = $this->dbh->prepare("SELECT
													c.products_id AS id,
													c.products_id AS canonical,
													t.pagetype AS pagetype,
													IF(sub_g.federal_enabled = 1, c.products_id, 0) AS id_id,
													IF(COUNT(c.products_id) > 0, TRUE, FALSE) AS validity,
													c.product_nickname AS nickname,
													c.product_nickname AS name,
													u.url AS short_url,
													t.template_secure AS secure,
													t.template_filename AS filename,
													c.product_title AS title,
													c.product_meta_description AS meta_description,
													c.product_meta_keyword AS meta_keywords,
													c.page_heading AS heading,
													t.allow_target AS allow_target,
													t.requires_login AS requires_login,
													t.disallow_guests AS disallow_guests,
													c.xml_show AS visibility,
													c.pri AS priority,
													c.freq AS change_frequency,
													c.on_sale AS on_sale,
													cat.name AS category_proper,
													grp.name AS grouping_proper,
													sub.name AS subcategory_proper,
													GROUP_CONCAT(DISTINCT l2.language) AS `languages`,
													l1.`language` AS `language`,
													COUNT(DISTINCT l2.product_number_language) AS language_count,
													b.lightweight,
													CASE WHEN c.new_product_expire_date > CURDATE() THEN c.new_product_expire_date
														ELSE '0'
													END AS expiration,
													(SUM(IF(`psd`.`limited_inventory` = TRUE AND `psd`.`inventory` > 0, 1, 0)) > 0) AS `is_limited`,
													(SUM(IF((`psd`.`inventory` > 0 OR `psd`.`limited_inventory` = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0) AS `is_stocked`,
													IF(MIN(IF(`psd`.`ul_recognized` = TRUE, 1, 0)) = 1, TRUE, FALSE) AS fully_ulrecognized,
								             		CASE WHEN SUM(`psd`.`ul_recognized`) > 0 THEN 1 ELSE 0 END AS ulrecognized,
													c.*
												FROM bs_products c
												LEFT JOIN bs_products_sku_description psd ON (c.product_number = psd.product_number AND psd.active = 'Y')
												INNER JOIN bs_pagetypes t ON ( t.pagetype = 'product' )
												LEFT JOIN bs_builder b ON (b.builder_ref = c.builder_ref AND b.active = 'Y')
												LEFT JOIN bs_products_languages l1 ON (c.product_number = l1.product_number_language AND l1.active='Y')
												LEFT JOIN bs_products_languages l2 ON (l2.product_number = l1.product_number)
												LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.products_id)
												LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id)
												LEFT JOIN bs_subcategories_geotargeted sub_g ON (c.subcategory_id = sub_g.subcategory_id)
												LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
												LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
												WHERE cat.active = TRUE
												AND grp.active = TRUE" . ($this->force ? ' ' : " AND sub.active = TRUE ") . "AND c.active = 'Y'
												AND sub_g.id = ?
												AND c.grid_display = TRUE
												AND (c.target IS NULL OR c.target='')
												AND (c.subcategory_detailed_id = 0 OR c.subcategory_detailed_id IS NULL)
												AND ( c.expiration_date IS NULL OR c.expiration_date = '0000-00-00' OR c.expiration_date > CURDATE() )
												GROUP BY c.products_id" . ($this->force ? ' ' : " HAVING SUM(IF((psd.inventory > 0 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0 ") . "ORDER BY c.position, c.product_number ASC");

                $stmt->execute(array($this->id));

                //Loop through the results and return
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    //products_id will be zero if federal_enabled is 0. Check before returning an array of 0
                    if ($row['products_id'] > 0) {
                        $product_row[] = $row;
                    }
                }

                return $product_row;

                break;


            case 'sidebar':

                //Use the category id to get all listings in that category
                $stmt = $this->dbh->prepare("SELECT id AS id,category_id AS category_id,categorygroup_name,position,image1,has_custom
												 FROM bs_groupings
												 WHERE active=1
												 AND category_id=?
												 AND has_group='Y'
												 ORDER BY cat_priority ASC,
														  categorygroup_name ASC");

                $stmt->execute(array($this->getTopLevelCategory()));

                while ($results = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $row[] = $results;
                }

                return $row;

                break;


            case 'sidebarsub':
                $sql = "SELECT id AS id, category_id AS category_id, grouping_id, name, image1, has_custom
							FROM bs_subcategories
							WHERE id = ?" . ($this->force ? ' ' : " AND active = TRUE ") . "ORDER BY name";

                $stmt = $this->dbh->prepare($sql);
                $stmt->execute(array($this->id));

                while ($results = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $row[] = $results;
                }

                return $row;
                break;


            case 'detailed':
                $sql = "SELECT id AS id, name AS name, image AS image, grid_subhead AS grid_subhead, description AS description, more_info_text AS more_info_text,
								   more_info_href AS more_info_href, grid_per_row AS per_row, grid_size AS grid_size, grid_alignment AS grid_alignment,
								   show_product_number AS show_product_number, show_quickview AS show_quickview , show_filter AS show_filter , show_sort AS show_sort
							FROM bs_subcategories_detailed
							WHERE subcategory_id=?
							AND active=?
							ORDER BY position";

                $stmt = $this->dbh->prepare($sql);
                $stmt->execute(array($this->id, 1));

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $results[] = $row;
                }

                return $results;

                break;


            case 'detailed_products':

                // Note: Because product canonicalization queries are slow and unnescessary on subcategory
                // pages, this query always uses the products_id as the canonical rather than the true canonical.
                $sql = "SELECT
								t.pagetype AS pagetype,
								c.products_id AS id,
								c.products_id AS canonical,
								IF(COUNT(c.products_id) > 0, TRUE, FALSE) AS validity,
								c.product_nickname AS nickname,
								c.product_nickname AS name,
								u.url AS short_url,
								t.template_secure AS secure,
								t.template_filename AS filename,
								c.product_title AS title,
								c.product_meta_description AS meta_description,
								c.product_meta_keyword AS meta_keywords,
								c.page_heading AS heading,
								t.allow_target AS allow_target,
								t.requires_login AS requires_login,
								t.disallow_guests AS disallow_guests,
								c.xml_show AS visibility,
								c.pri AS priority,
								c.freq AS change_frequency,
								cat.name AS category_proper,
								c.on_sale AS on_sale,
							    grp.name AS grouping_proper,
							    sub.name AS subcategory_proper,
								GROUP_CONCAT(DISTINCT l2.language) AS `languages`,
								l1.`language` AS `language`,
								COUNT(DISTINCT l2.product_number_language) AS language_count,
								b.lightweight,
								CASE WHEN c.new_product_expire_date > CURDATE() THEN c.new_product_expire_date
									ELSE '0'
								END AS expiration,
								(SUM(IF(`psd`.`limited_inventory` = TRUE AND `psd`.`inventory` > 0, 1, 0)) > 0) AS `is_limited`,
								(SUM(IF((`psd`.`inventory` > 0 OR `psd`.`limited_inventory` = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0) AS `is_stocked`,
								IF(MIN(IF(`psd`.`ul_recognized` = TRUE, 1, 0)) = 1, TRUE, FALSE) AS fully_ulrecognized,
								CASE WHEN SUM(`psd`.`ul_recognized`) > 0 THEN 1 ELSE 0 END AS ulrecognized,
								c.*
							FROM bs_products c
							LEFT JOIN bs_products_sku_description psd ON (psd.product_number = c.product_number AND psd.active = 'Y')
							INNER JOIN bs_pagetypes t ON ( t.pagetype = 'product' )
							LEFT JOIN bs_builder b ON (b.builder_ref = c.builder_ref AND b.active = 'Y')
							LEFT JOIN bs_products_languages l1 ON (c.product_number = l1.product_number_language AND l1.active='Y')
							LEFT JOIN bs_products_languages l2 ON (l2.product_number = l1.product_number)
							LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.products_id)
							LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id)
							LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
							LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
							WHERE cat.active = TRUE
							AND grp.active = TRUE" . ($this->force ? ' ' : " AND sub.active = TRUE ") . "AND c.active = 'Y'
							AND	c.subcategory_id=?
							AND (target IS NULL OR target = '')
							AND c.grid_display = TRUE
							AND ( c.expiration_date IS NULL OR c.expiration_date = '0000-00-00' OR c.expiration_date > CURDATE() )
							GROUP BY c.products_id" . ($this->force ? ' ' : " HAVING SUM(IF((psd.inventory > 0 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0 ") . "ORDER BY c.position";

                $stmt = $this->dbh->prepare($sql);
                $stmt->execute(array($refid));

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['products_id'] > 0) {
                        $product_row[] = $row;
                    }
                }

                return $product_row;

                break;
        }


    }

    /**
     * Gets the top-most level (base) id
     *
     * @return    id    The id of the base level
     */
    public function getTopLevelCategory() {

        if ($this->top_level_category) {
            return $this->top_level_category;
        } else {
            $sql = "SELECT category_id AS top
				        FROM bs_subcategories
				        WHERE id=? LIMIT 1";

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute(array($this->id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['top'];
        }
    }

    /**
     * Takes a given product id, and returns true if the product's Builder is supported (if $pid is absent, checks for
     * full Builder support)
     *
     * @param  int     $pid               The product to check
     * @param  boolean $check_lightweight (optional) Whether or not to poll the database for the product's lightweight
     *                                    status
     * @param  boolean $lightweight       (optional) If the products's lightweight status is already known, it can be
     *                                    passed in
     * @return boolean                          Whether or not the builder is supported
     */
    function builderSupported($check_lightweight = true, $lightweight) {

        $supported = false;

        if (BUILDER_SUPPORTED) {
            if (BUILDER_SUPPORT_PARTIAL) {
                if ($check_lightweight == true) {
                    $supported = $lightweight;
                } else {
                    $supported = false;
                }
            } else {
                $supported = true;
            }
        }

        return $supported;

    }

    /**
     * Converts an integer number into a string of words
     *
     * @param     int $number Number to be converted
     * @return    string                String converted to words
     */
    public function convert_number_to_words($number) {

        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'fourty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety', 100 => 'hundred', 1000 => 'thousand', 1000000 => 'million', 1000000000 => 'billion', 1000000000000 => 'trillion', 1000000000000000 => 'quadrillion', 1000000000000000000 => 'quintillion');

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error('convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
            return false;
        }

        if ($number < 0) {
            return $negative . convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }


    /**
     * Gets a list of zones for a geotarget
     *
     * @param     int $id ID of geotarget
     * @return    array           Array of zones
     */
    public function getGeotargetList($id) {

        global $dbh;

        $sql = $dbh->prepare("SELECT g.*, z.zone_name AS zone_name FROM bs_subcategories_geotargeted g
								  LEFT JOIN bs_zones z ON (g.target = z.zone_code)
								  WHERE g.subcategory_id = ?
								  AND g.active = 1
								  ORDER BY z.zone_name");
        $sql->execute(array($id));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * Gets the subcategory of a geotargeted page
     *
     * @return [type] [description]
     */
    public function getGeotargetSubcategory($id) {

        global $dbh;

        $sql = $dbh->prepare("SELECT subcategory_id FROM bs_subcategories_geotargeted WHERE id=? LIMIT 1");
        $sql->execute(array($id));
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        return $row['subcategory_id'];
    }


}
