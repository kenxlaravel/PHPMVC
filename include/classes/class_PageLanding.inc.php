<?php


	class PageLanding {

		//Properties
		private $dbh;
		public $id;
		public $name;
		public $short_name;
		public $product_grid_header;
		public $product_grid_intro;
		public $product_grid_alternate;
		public $grid_size;
		public $show_product_number;
		public $show_quickview;
		public $show_sort;
		public $show_filter;
		public $image;
		public $snippet;
		public $description_text_html;
		public $description_image;
		public $description_more_info_html;
		public $special_header_class_name;
		public $imagePath = array();
		public $intro_supplement_html;
		public $popup_html;

		//Constructor
		public function __construct($id = NULL) {

			//Establish a database connection
			$this->setDatabase();

			//Some functions can be used without an id, but a number of core properties will not exist
			if ($id) {
				$this->id = $id;
				$this->getProperties();
			}

			$this->imagePath['grid'] = IMAGE_URL_PREFIX . '';
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

			//If we do not already have this info for the current page, grab it
			if (empty($this->name)) {
				$stmt = $this->dbh->prepare("SELECT * FROM bs_landings WHERE id=? LIMIT 1");
				$stmt->execute(array($this->id));
				$row = $stmt->fetch();

				//Set class properties
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->short_name = $row['short_name'];
				$this->grid_alternate = $row['grid_alternate'];
				$this->product_grid_header = $row['product_grid_header'];
				$this->product_grid_intro = $row['product_grid_intro'];
				$this->product_grid_size = $row['product_grid_size'];
				$this->show_product_number = $row['show_product_number'];
				$this->show_quickview = $row['show_quickview'];
				$this->show_filter = $row['show_filter'];
				$this->show_sort = $row['show_sort'];
				$this->image = $row['image'];
				$this->snippet = $row['snippet'];
				$this->description_text_html = $row['description_text_html'];
				$this->description_image = $row['description_image'];
				$this->image_template = $row['image_template'] ? trim($row['image_template']).'-banner' : 'basic-banner';
				$this->description_more_info_html = $row['description_more_info_html'];
				$this->special_header_class_name = $row['special_header_class_name'];
				$this->intro_supplement_html = $row['intro_supplement_html'];
				$this->popup_html = $row['popup_html'];

			}
		}



		/**
		 * Used to get listings based off of a location on the page. Example: 'grid' will return all
		 * listings on the landing page grid
		 * @param     string    $location    The location on the page that we need listings for
		 * @return    array                  And array of all the listings
		 */
		public function getListings($location) {

			switch($location) {
				case 'grid':
					$sql = "SELECT *
							FROM bs_landings_grid
							WHERE active = TRUE
							AND landing_id = ?
							ORDER BY position";
							$stmt = $this->dbh->prepare($sql);
							$stmt->execute(array($this->id));

							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$product_row[]=$row;
							}
				break;

				case 'products':

					// Note: Because product canonicalization queries are slow and unnescessary on landing
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
								(SUM(IF(psd.limited_inventory = TRUE AND psd.inventory > 0, 1, 0)) > 0) AS `is_limited`,
								(SUM(IF((psd.inventory > 0 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0) AS `is_stocked`,
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
							LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id AND sub.active = TRUE)
							LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id AND grp.active = TRUE)
							LEFT JOIN bs_categories cat ON (cat.id = grp.category_id AND cat.active = TRUE)
							LEFT JOIN bs_landings_products lp ON (lp.product_id=c.products_id )
							WHERE cat.active = TRUE AND grp.active = TRUE
							AND sub.active = TRUE AND c.active = 'Y' AND lp.landing_id = ?
							AND ( c.expiration_date IS NULL OR c.expiration_date = '0000-00-00' OR c.expiration_date > CURDATE() )
							GROUP BY c.products_id
							HAVING SUM(IF((psd.inventory > 0 OR psd.limited_inventory = FALSE) AND c.in_stock = 'Y', 1, 0)) > 0
							ORDER BY lp.position";

							$stmt = $this->dbh->prepare($sql);
							$stmt->execute(array($this->id));

							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

								if ($row['products_id'] > 0) {
									$product_row[] = $row;
								}

							}

				break;
			}


			return $product_row;
		}



	} //The end.