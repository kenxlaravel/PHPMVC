<?php

	class Help {

		//Properties
		private $dbh;
		public $imagePath = array();

		//Pulled from database
		public $id;
		public $name;
		public $content_header;
		public $content_intro_html;
		public $template;
		public $section_name;
		public $section_id;


		//Constructor
		public function __construct($id) {

			//Some functions can be used without an id, but a number of core properties will not exist
			if ($id) {

				$this->id = (int)$id;
				$this->pagetype = isset($pagetype) ? $pagetype : NULL;
				$this->getProperties();
			}
		}

		/**
		 * Main properties function to get most of what we need about the page on instantiation
		 * and set as class variables.
		 */
		private function getProperties() {

			//If we do not already have this info for the current page, grab it
			if (empty($this->name)) {

				$stmt = Connection::getHandle()->prepare(
                    "SELECT h.*, s.name AS section_name, s.id AS section_id FROM bs_help h
                    LEFT JOIN bs_help_sections s ON (h.help_section_id = s.id) WHERE h.id=? LIMIT 1");

                $stmt->execute(array($this->id));

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

				//Set class properties
				$this->name = $row['name'];
				$this->content_header = $row['content_header'];
				$this->content_intro_html = $row['content_intro_html'];
				$this->template = $row['template'];
				$this->section_name = $row['section_name'];
				$this->section_id = $row['section_id'];
			}
		}

		public function getListings($location) {

            $results = array();

			switch($location) {

				case 'sidebar':

					$stmt = Connection::getHandle()->prepare("SELECT * FROM bs_help_sections ORDER BY position");
					$stmt->execute();

					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

						$results[] = $row;
					}

					$count = 0;

					foreach ($results AS $key => $value) {

						$stmt = Connection::getHandle()->prepare("SELECT * FROM bs_help WHERE help_section_id=? ORDER BY position");

                        if( $stmt->execute(array($value['id'])) ) {

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                $results[$count]['sections'][] = $row;
                            }

                            $count++;
                        }
					}

					return $results;
				break;


				case 'main_page':

					$stmt = Connection::getHandle()->prepare("SELECT * FROM bs_help_sections ORDER BY main_page_position");
					$stmt->execute();

					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$results[] = $row;
					}

					$count = 0;

					foreach ($results AS $key => $value) {
						$stmt = Connection::getHandle()->prepare("SELECT * FROM bs_help WHERE help_section_id=? ORDER BY position");
						$stmt->execute(array($value['id']));

						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$results[$count]['sections'][] = $row;
						}

						$count++;
					}

					return $results;
				break;
			}
		}
	}