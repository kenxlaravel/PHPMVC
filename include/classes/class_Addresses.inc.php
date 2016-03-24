<?php

	class Addresses {

		private $cid;


		function __construct($cid = NULL) {

			//Set the user id as a property if we have it
			if ($cid > 0) {
				$this->cid = $cid;
			} else {
				$this->cid = isset($_SESSION['CID']) ? $_SESSION['CID'] : NULL;
			}
		}


		/**
		 * Seeds the random generator
		 * @return    Float    Microtime
		 */
		private function makeSeed() {
			list($usec, $sec) = explode(' ', microtime());
			return (float) $sec + ((float) $usec * 100000);
		}

		/**
		 * Generates a random unique public address ID
		 * @return    string    unique public address ID
		 */
		public function getUniquePublicId() {

			//Seed the random generator
			mt_srand($this->makeSeed());

			//Alphanumeric upper/lower array
			$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
			$id = "";

			//Loop through and generate the random public id
			for($i = 0; $i < 32; $i ++) {
			  $id .= $alfa[mt_rand(0, strlen($alfa)-1)];
			}

			//If there is a duplicate, run this function recursively
			if(!$this->isPublicIdUnique($id)) {
				$id = $this->getUniquePublicId();
			}

			//Return the hash
			return $id;
		}



		/**
		 * This function takes a generated design id and checks to verify that it is unique
		 * @param     string    $design    [description]
		 * @return    bool                 true if unique, false if not
		 */
		private function isPublicIdUnique($id) {

			$sql = Connection::getHandle()->prepare(
                        "SELECT COUNT(*) FROM bs_customer_addresses WHERE public_id = ?");

			$sql->execute(array($id));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

			return ($row['count'] > 0 ? false : true);
		}



		/**
		 * Lists all addresses affiliate with the current user
		 * @return array|false array of customer addresses
		 */
		public function listAddresses($cid = null) {

            $addresses = array ();

            if( $this->cid > 0 && (!isset($cid)) ) {

                $sql = Connection::getHandle()->prepare(
                    "SELECT * FROM bs_customer_addresses WHERE cid = ? ORDER BY default_billing DESC, default_shipping DESC"
                );

                $sql->execute(array ($this->cid));

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                    $addresses[] = $row;
                }

                return $addresses;

            } else if(isset($cid)) {

                $sql = Connection::getHandle()->prepare(
                    "SELECT * FROM bs_customer_addresses WHERE cid = ? ORDER BY default_billing DESC, default_shipping DESC"
                );

                $sql->execute(array ($cid));

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                    $addresses[] = $row;
                }

                return $addresses;

            }else{

                return FALSE;
            }

		}


		/**
		 * Returns a list of all countries and whether or not they have affiliated zones
		 * @return [array] [array of countries]
		 */
		public function listCountries() {
			$sql = Connection::getHandle()->prepare("SELECT DISTINCT c.countries_id,
														c.countries_name,
														c.countries_iso_code_2,
														CASE WHEN z.zone_id > 0 THEN 'true' ELSE 'false' END AS zone
										FROM bs_countries c
										LEFT JOIN bs_zones z ON (c.countries_iso_code_2 = z.countries_id)
										GROUP BY c.countries_id
										ORDER BY c.countries_id");
			$sql->execute();

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$results[] = $row;
			}

			return $results;
		}



		/**
		 * Gets a list of all zones (states/territories)
		 * @return [array] list of zones
		 */
		public function listZones() {
			$sql = Connection::getHandle()->prepare("SELECT * from bs_zones ORDER BY countries_id DESC, zone_name ASC");
			$sql->execute();

			while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$results[] = $row;
			}

			return $results;
		}



		/**
		 * Deletes a saved address
		 * @param  [string] $public_id    array of postdata
		 */
		public function deleteAddress($public_id) {

			//Make sure that this is a real customer
			if ($_SESSION['CID'] > 0) {

				//Look for this address paired with the customers id to make sure it exists and the customer owns it
				$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
											WHERE public_id = ? AND cid = ?");
				$sql->execute(array($public_id, $_SESSION['CID']));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				//If the address does exist, and it belongs to this customer, let them delete it
				if ($row['total'] > 0) {

					//Delete the address
					$sql = Connection::getHandle()->prepare("DELETE FROM bs_customer_addresses WHERE public_id = ? AND cid = ?");
					$sql->execute(array($public_id, $_SESSION['CID']));

					//Tell the user what happened
					$_SESSION['successes'][] = "Your address has successfully been deleted.";

					//Redirect them
					$account = new Page('my-account');
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: ".$account->getUrl());
					exit;

				} else {
					//The address doesn't exist, or doesn't belong to this customer. Give them an unknown error
					$_SESSION['errors'][] = "Your address could not be deleted; an unknown error was encountered.";
				}

			//Otherwise, they are not a user and we have an unknown error
			} else {
				$_SESSION['errors'][] = "Your address could not be deleted; an unknown error was encountered.";
			}


			//Redirect them
			$link = new Page('my-account');
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header("Location: ".$link->getUrl());
			exit;
		}



		/**
		 * Take a form, and sets the user's default billing and/or shipping address
		 * @param [array] $formdata [form postdata]
		 */
		public function setDefaultAddress($formdata,$cid = NULL) {

			//Make sure this is a real customer
			if ($this->cid > 0) {

				//Make sure we have something useful from the form
				if (!empty($formdata['default_shipping']) || !empty($formdata['default_billing'])) {

					//Check if they are changing their default shipping address
					if (!empty($formdata['default_shipping'])) {
						if(isset($cid)){
		 					//Grab the address from the DB, and make sure it belongs to the customer
							$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
														WHERE public_id = ? AND cid = ?");
							$sql->execute(array($formdata['default_shipping'], $cid));
							$row = $sql->fetch(PDO::FETCH_ASSOC);

						}else{
							//Grab the address from the DB, and make sure it belongs to the customer
							$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
														WHERE public_id = ? AND cid = ?");
							$sql->execute(array($formdata['default_shipping'], $this->cid));
							$row = $sql->fetch(PDO::FETCH_ASSOC);
						}
							//If the user does not own this address, or it does not exist, throw an error
						if ($row['total'] <= 0) {
							return false;
						} else {
							if(isset($cid)){
								//Remove any defaults so we don't end up with more than one
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 0 WHERE cid = ?");
								$sql->execute(array($cid));

								//Update the address in the DB
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 1 WHERE public_id = ?");
								$sql->execute(array($formdata['default_shipping']));

							}else{
							//Remove any defaults so we don't end up with more than one
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 0 WHERE cid = ?");
								$sql->execute(array($this->cid));

								//Update the address in the DB
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 1 WHERE public_id = ? AND cid = ?");
								$sql->execute(array($formdata['default_shipping'], $this->cid));
							}
						}

					}

					//Check if they are changing their default billing address
					if (!empty($formdata['default_billing'])) {
						if(isset($cid)){
							 //Grab the address from the DB, and make sure it belongs to the customer
							$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
														WHERE public_id = ? AND cid = ?");
							$sql->execute(array($formdata['default_billing'], $cid));
							$row = $sql->fetch(PDO::FETCH_ASSOC);
						}else{
							//Grab the address from the DB, and make sure it belongs to the customer
							$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
														WHERE public_id = ? AND cid = ?");
							$sql->execute(array($formdata['default_billing'], $this->cid));
							$row = $sql->fetch(PDO::FETCH_ASSOC);
						}
						//If the user does not own this address, or it does not exist, throw an error
						if ($row['total'] <= 0) {
							return false;
						} else {
							if(isset($cid)){
								//Remove any defaults so we don't end up with more than one
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 0 WHERE cid = ?");
								$sql->execute(array($cid));

								//Update the address in the DB
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 1 WHERE public_id = ?");
								$sql->execute(array($formdata['default_billing']));
							}else{
								//Remove any defaults so we don't end up with more than one
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 0 WHERE cid = ?");
								$sql->execute(array($this->cid));

								//Update the address in the DB
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 1 WHERE public_id = ? AND cid = ?");
								$sql->execute(array($formdata['default_billing'], $this->cid));
							}
						}

					}


				} else {
					return false;
				}


			} else {
				return false;
			}


			return true;

		}




		/**
		 * Allows a user to update an address, or insert a new one depending on whether an address_id is provided
		 * @param  [type] $formdata    array of postdata
		 */
		public function modifyAddress($formdata) {

			//Make sure this is a user before continuing
			if ($_SESSION['CID'] > 0) {

				//Instantiate our form validator, and validate the user input
				$valid = new Validate($formdata);
				$valid->name('first')->required("You must provide your first name.");
				$valid->name('last')->required("You must provide your last name.");
				$valid->name('phone')->required("You must provide your phone number.")->phone("The number you have provided does not match a valid phone number format.");
				$formdata['phone'] = $valid->getParsed(); //Get the phone number parsed in our format
				$valid->name('address1')->required("You must provide a street address.");
				$valid->name('city')->required("You must provide a city.")->minLength(2, "The city you have provided is not valid.");
				$valid->name('state')->requiredWhenCountryHasZones("You must provide a state.");
				$valid->name('zip')->required("You must provide a zip code.")->minLength(5, "Valid zip codes contain 5 or more digits.")->maxLength(10, "The zip code you entered is invalid.");
				$valid->name('country')->required("You must provide a country.");
				$validate = $valid->validate();

				$formdata['default_shipping'] = ($formdata['default_shipping'] == 1 ? 1 : 0);
				$formdata['default_billing'] = ($formdata['default_billing'] == 1 ? 1 : 0);

				//If our form validates correctly so far
				if (empty($validate)) {


					//We need to check if the state is required, or if they are in a country where we should omit the state
					$sql = Connection::getHandle()->prepare("SELECT COUNT(*) AS total FROM bs_zones WHERE countries_id = ?");
					$sql->execute(array($formdata['country']));
					$result = $sql->fetch(PDO::FETCH_ASSOC);

					if ($result['total'] <= 0) {
						$formdata['state'] = NULL;
					}

					//We need to see if a specific address edit id was given to us from the form. If an edit id is present,
					//we are updating that address. If the edit id is 0, we are inserting a new address
					if (!empty($formdata['address_id'])) {

						//Check to make sure the address exists
						$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
													WHERE public_id = ? AND cid = ?");
						$sql->execute(array($formdata['address_id'], $_SESSION['CID']));
						$row = $sql->fetch(PDO::FETCH_ASSOC);


						//If the address does exist, and it belongs to this customer, let them update it
						if ($row['total'] > 0) {

							//If the customer is setting this as a default address, we first have to disable any other
							//defaults on their account of the same type
							if ($formdata['default_shipping'] == 1) {
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 0 WHERE cid = ?");
								$sql->execute(array($_SESSION['CID']));
							}

							if ($formdata['default_billing'] == 1) {
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 0 WHERE cid = ?");
								$sql->execute(array($_SESSION['CID']));
							}

							//Update the address
							$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses
														SET
															company           = :company,
															first_name        = :first,
															last_name         = :last,
															street_address    = :address1,
															suburb            = :address2,
															postcode          = :zip,
															city              = :city,
															state             = :state,
															country           = :country,
															phone             = :phone,
															fax               = :fax,
															default_shipping  = :default_shipping,
															default_billing   = :default_billing
														WHERE cid = :cid
														AND public_id = :id");

							$sql->execute(array(":cid"              => $_SESSION['CID'],
												":id"               => $formdata['address_id'],
												":company"          => $formdata['company'],
												":first"            => $formdata['first'],
												":last"             => $formdata['last'],
												":address1"         => $formdata['address1'],
												":address2"         => $formdata['address2'],
												":zip"              => $formdata['zip'],
												":city"             => $formdata['city'],
												":state"            => $formdata['state'],
												":country"          => $formdata['country'],
												":phone"            => $formdata['phone'],
												":fax"              => $formdata['fax'],
												":default_shipping" => $formdata['default_shipping'],
												":default_billing"  => $formdata['default_billing']));

							$_SESSION['successes'][] = "Your address has successfully been updated.";

							//Redirect them
							$link = new Page('my-account');
							header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
							header("Location: ".$link->getUrl());
							exit;

						} else {
							//The address doesn't exist, or doesn't belong to this customer. Give them an unknown error
							$_SESSION['errors'][] = "Your address could not be updated; An unknown error was encountered.";
						}

					//Inserting a new address
					} else {

						//First we'll check to make sure this address isn't already on the customer's account
						$sql = Connection::getHandle()->prepare("SELECT COUNT(id) AS total FROM bs_customer_addresses
													WHERE cid          = :cid
													AND company        = :company
													AND first_name     = :first
													AND last_name      = :last
													AND street_address = :address1
													AND suburb         = :address2
													AND postcode       = :zip
													AND city           = :city
													AND state          = :state
													AND country        = :country
													AND phone          = :phone
													AND fax            = :fax");
						$sql->execute(array(":cid"        => $_SESSION['CID'],
											":company"    => $formdata['company'],
											":first"      => $formdata['first'],
											":last"       => $formdata['last'],
											":address1"   => $formdata['address1'],
											":address2"   => $formdata['address2'],
											":zip"        => $formdata['zip'],
											":city"       => $formdata['city'],
											":state"      => $formdata['state'],
											":country"    => $formdata['country'],
											":phone"      => $formdata['phone'],
											":fax"        => $formdata['fax']));
						$row = $sql->fetch(PDO::FETCH_ASSOC);

						//Make sure this address is not already on the account
						if ($row['total'] <= 0 OR $row['total'] == NULL) {
							//If the customer is setting this as a default address, we first have to disable any other
							//defaults on their account of the same type
							if ($formdata['default_shipping'] == 1) {
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_shipping = 0 WHERE cid = ?");
								$sql->execute(array($_SESSION['CID']));
							}

							if ($formdata['default_billing'] == 1) {
								$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 0 WHERE cid = ?");
								$sql->execute(array($_SESSION['CID']));
							}

							$sql = Connection::getHandle()->prepare("INSERT INTO bs_customer_addresses
															(public_id, cid, company, first_name, last_name,
															street_address, suburb, postcode,
															city, state, country, phone, fax,
															default_shipping, default_billing)
														VALUES
															(:public_id, :cid, :company, :first, :last,
															:address1, :address2, :zip,
															:city, :state, :country, :phone, :fax,
															:default_shipping, :default_billing)");
							$sql->execute(array(":public_id"        => $this->getUniquePublicId(),
												":cid"              => $_SESSION['CID'],
												":company"          => $formdata['company'],
												":first"            => $formdata['first'],
												":last"             => $formdata['last'],
												":address1"         => $formdata['address1'],
												":address2"         => $formdata['address2'],
												":zip"              => $formdata['zip'],
												":city"             => $formdata['city'],
												":state"            => $formdata['state'],
												":country"          => $formdata['country'],
												":phone"            => $formdata['phone'],
												":fax"              => $formdata['fax'],
												":default_shipping" => $formdata['default_shipping'],
												":default_billing"  => $formdata['default_billing']));

							$_SESSION['successes'][] = "Your address has successfully been added.";

							//Redirect them
							$link = new Page('my-account');
							header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
							header("Location: ".$link->getUrl());
							exit;

						} else {
							//This address already exists for this customer
							$_SESSION['errors'][] = "This address already exists for your account.";
						}

					}

				//If there were validation errors
				} else {

					foreach($validate as $key => $error) {
						$_SESSION['errors'][] = $error;
					}
				}

			//Otherwise, they are not a user and we have an unknown error
			} else {

				//Check if they were updating or adding, and throw an error depending
				if (!empty($formdata['address_id'])) {
					$_SESSION['errors'][] = "Your address could not be updated; An unknown error was encountered.";
				} else {
					$_SESSION['errors'][] = "Your address could not be added; An unknown error was encountered.";
				}

			}

			$_SESSION['validate'] = $formdata;

			if (!empty($formdata['address_id'])) {
				$_SESSION['instructions'] = "Please click \"Update Address\" again to fix the following errors. ";
			} else {
				$_SESSION['instructions'] = "Please click \"Add An Address\" again to fix the following errors. ";
			}

			//Redirect them
			$link = new Page('my-account');
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header("Location: ".$link->getUrl());
			exit;
		}

		public function getDefaultShippingAddress(){

			$sql_default=Connection::getHandle()->prepare("SELECT * FROM bs_customer_addresses WHERE cid = :cid AND default_shipping = :default_shipping");
			$sql_default->execute(array(":cid"=>$_SESSION['CID'],":default_shipping"=>'1'));

			return $sql_default->fetchAll();
		}




	}
?>