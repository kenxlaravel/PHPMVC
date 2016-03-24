<?php

	class PageGrouping {

		//Properties

		public $imagePath = array();

		//Pulled from database
		public $id;
		public $name;
		public $slug;
		public $accessory;
		public $category_id;
		public $page_title;
		public $page_heading;
		public $meta_keywords;
		public $meta_description;
		public $image;
		public $snippet;
		public $description_text_html;
		public $description_image;
		public $description_more_info_html;
		public $special_header_class_name;
		public $intro_supplement_html;
		public $popup_html;


		//Constructor
		public function __construct($id = NULL) {

			//Some functions can be used without a id, but a number of core properties will not exist
			if ($id) {
				$this->id = $id;
				$this->getProperties();
			}

			//Set the image paths
			$this->imagePath['grid'] = IMAGE_URL_PREFIX . '';
			$this->imagePath['description'] = IMAGE_URL_PREFIX . '';
		}


		/**
		 * Main properties function to get most of what we need about the page on instantiation
		 * and set as class variables.
		 */
		private function getProperties() {

			//If we do not already have this info for the current page, grab it
			if (empty($this->name)) {
				$stmt = Connection::getHandle()->prepare("SELECT * FROM bs_groupings WHERE id=? LIMIT 1");
				$stmt->execute(array($this->id));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//Set class properties
				$this->name = $row['name'];
				$this->accessory = $row['accessory'];
				$this->category_id = $row['category_id'];
				$this->page_title = $row['page_title'];
				$this->page_heading = $row['page_heading'];
				$this->meta_description = $row['meta_description'];
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
		 * listings on the grouping page grid (aka subcategories)
		 * @param     string    $location    The location on the page that we need listings for
		 * @return    array                  And array of all the listings
		 */
		public function getListings($location) {

            $stmt = NULL;
            $results = NULL;

			switch($location) {

				case 'grid':

					$sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname,
                             IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity, c.name AS name, u.url AS short_url,
                             t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename, c.id AS canonical,
                             c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                             c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,
                             t.disallow_guests AS disallow_guests, c.xml_show AS visibility, c.xml_page_priority AS priority,
                             c.xml_page_change_frequency AS change_frequency, c.* FROM bs_subcategories c JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id) LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND c.active = TRUE AND grp.id = ? AND t.pagetype = 'subcategory'
					        GROUP BY c.id ORDER BY c.position ASC, c.name ASC";

					$stmt = Connection::getHandle()->prepare($sql);

				break;

				case 'sidebar':

					$sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                             c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                             c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                             c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,
                             t.disallow_guests AS disallow_guests, c.xml_show AS visibility, c.xml_page_priority AS priority,
                             c.xml_page_change_frequency AS change_frequency
					        FROM bs_subcategories c JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id) LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND c.active = TRUE AND grp.id = ? AND t.pagetype = 'subcategory'
					        GROUP BY c.id ORDER BY c.name ASC";

					$stmt = Connection::getHandle()->prepare($sql);

				break;

			}

            if( $stmt->execute(array($this->id)) ) {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $results[] = $row;
                }

            }
			return $results;
		}


        public static function create($id = NULL) { return new self($id); }
	}