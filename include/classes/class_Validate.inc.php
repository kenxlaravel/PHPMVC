<?php


	class Validate {

		//Properties
		private $dbh;
		private $form;
		private $name;
		private $parsed;
		private $errors = array();
		private $confidence_level;
		//Patterns
		private $pattern_phone = "/^\+\d(\d{3})(\d{3})(\d{4})$/";


		//Constructor - Generally a form $_POST is passed into the constructor
		public function __construct($form) {
			$this->form = $form;
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



		//Sets the name of the input we want to work with
		public function name($name) {
			$this->name = $name;

			return $this;
		}



		//Makes sure the input is not empty
		public function required($error) {
			if ($this->form[$this->name] == '') {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is not empty if the 'WhenFieldPresent' field is present
		public function requiredWhenFieldPresent($field, $error) {
			if ($this->form['$field'] != '') {
				if ($this->form[$this->name] == '') {
					$this->errors[] = $error;
				}
			}

			return $this;
		}



		//Makes sure the input is not empty if the country has zones (states/territories)
		public function requiredWhenCountryHasZones($error) {

			$this->setDatabase();

			$sql = $this->dbh->prepare("SELECT COUNT(*) AS total FROM bs_zones WHERE countries_id = ?");
			$sql->execute(array($this->form['country']));

			$row = $sql->fetch(PDO::FETCH_ASSOC);

			if ($row['total'] > 0) {
				if ($this->form[$this->name] == '') {
					$this->errors[] = $error;
				}
			}
		}



		//Makes sure the input matches a second $value2 exactly
		public function match($value2, $error) {
			if ($this->form[$this->name] !== $this->form[$value2]) {
				$this->errors[] = $error;
			}

			return $this;
		}



		// Validates a shipping account (checks to make sure it is 6 or 9 characters in length)
		public function shippingAccount($error) {
			if (strlen($this->form[$this->name]) !== 6 && strlen($this->form[$this->name]) !== 9) {
				$this->errors[] = $error;
			}

			return $this;
		}



		// Make sure the input is the exact length specified
		public function exactLength($length, $error) {
			if (strlen($this->form[$this->name]) !== $length) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is longer than a minimum $length
		public function minLength($length, $error) {
			if (strlen($this->form[$this->name]) < $length) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is shorter than a maximum $length
		public function maxLength($length, $error) {
			if (strlen($this->form[$this->name]) > $length) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is an email address
		public function email($error) {
			if (!filter_var($this->form[$this->name], FILTER_VALIDATE_EMAIL)) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is a URL
		public function url($error) {
			if (!filter_var($this->form[$this->name], FILTER_VALIDATE_URL)) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Makes sure the input is an IP address
		public function ip($error) {
			if (!filter_var($this->form[$this->name], FILTER_VALIDATE_IP)) {
				$this->errors[] = $error;
			}

			return $this;
		}



		private function trimAndCompressWhiteSpace($string) {
			return trim(preg_replace('/\s\s+/u', ' ', (string) $string));
		}



		//Takes a regexp and runs preg_match on the input
		public function regex($regex, $error) {
			if (!preg_match($regex, $this->form[$this->name])) {
				$this->errors[] = $error;
			}

			return $this;
		}



		//Validates phone numbers
		public function phone($error) {

			// Regular Expressions
			$regex_domestic = '/^(?:\+?1[,-.\/ ]*)?\(?([0-9]{3})\)?[,-.\/ ]*([0-9]{3})[,-.\/ ]*([0-9]{4})(?:[,-.\/ ]*(?:e|x|ex|ext|xt|extension)\.?[,-.\/ ]*([0-9]+))?\s*$/i'; // 1 = area code; 2 = first three local; 3 = last four local; 4 = extension (optionally)
			$regex_international = '/^\+?((?:[0-9][,-.\/ ]*){7,15})(?:[,-.\/ ]*(?:e|x|ex|ext|xt|extension)\.?[,-.\/ ]*([0-9]+))?\s*$/i'; // 1 = number; 2 = extension

			// Check data against the format analyzer
			if ( preg_match($regex_domestic, $this->form[$this->name], $matches) ) {
				$this->parsed = $matches[1] . '-' . $matches[2] . '-' . $matches[3] . ( !empty($matches[4]) ? (' Ext. '.$matches[4]) : '' );
				$this->confidence_level = 1;
			} else if ( preg_match($regex_international, $this->form[$this->name]) ) {
				$this->parsed = $this->form[$this->name];
				$this->confidence_level = 2;
			} else {
				$this->confidence_level = 3;
				$this->errors[] = $error;
			}
			return $this;

		}



		public function getConfidenceLevel(){
			return $this->confidence_level;
		}



		//Validates the entire form (passes any errors back that have risen)
		public function validate() {
			return $this->errors;
		}



		public function getParsed() {
			return $this->parsed;
		}
	}