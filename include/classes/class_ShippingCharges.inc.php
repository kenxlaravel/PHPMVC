<?php

	class ShippingCharges {

		// Databse handler
		private $dbh;

		// User-specific
		private $session_id;

		// Cart-specific
		private $zipcode = null;
		private $city = null;
		private $state = null;
		private $country = null;
		private $address1 = null;
		private $address2 = null;
		private $international = null;
		private $total_weight = null;
		private $free_shipping = false;
		public $shipping_account = null;

		// UPS-specific
		private $ups_estimates = array();
		private $ups_delivery_dates_by_service = array();
		private $ups_rates_by_service = array();
		private $ups_error;

		// FedEx-specific
		private $fedex_estimates = array();
		private $fedex_rates_by_service = array();
		private $fedex_rates_by_service_residential = array();
		private $fedex_rates_by_service_commercial = array();
		private $fedex_smart_post = array();
		private $fedex_error ;


        /**
         * Our constructor
         *
         * @param null $zipcode
         * @param null $type
         * @param null $address1
         * @param null $address2
         * @param null $city
         * @param null $state
         * @param null $country
         */
		public function __construct($zipcode = NULL, $type = NULL, $address1=null, $address2=null, $city=null, $state=null, $country=null) {

			// Establish a database connection
			$this->setDatabase();

			// Set our session id
			$this->session_id = session_id();

			// Set the zipcode
			if(!empty($zipcode)) {
				$this->zipcode = $zipcode;
			}

			// If either the state or country was no supplied, grab it based on the zipcode
			if (empty($state) || empty($country)) {
				$zipcode_array = $this->zipcodeSearch($zipcode);
			}

			// Set address line 1
			if (!empty($address1)) {
				$this->address1 = $address1;
			}

			// Set address line 2
			if (!empty($address2)) {
				$this->address2 = $address2;
			}

			// Set the city
			if (empty($city)) {
				$this->city = $zipcode_array['city'];
			} else {
				$this->city = $city;
			}

			//If the state was not supplied, use our zipcode-based state
			if (empty($state)) {
				$this->state = $zipcode_array['state'];
			} else {
				$this->state = $state;
			}

			// If the country was no supplied, use our zipcode-based country
			if (empty($country)) {
				$this->country = $zipcode_array['country'];
			} else {
				$this->country = $country;
			}

			// Set whether the address is international
			if (mb_strtolower($this->country) == 'us') {
				$this->international = false;
			} else {
				$this->international = true;
			}

			// Set our address type (residential / commercial)
			if (!empty($type)) {
				$this->type = $type;
			}

		}



		// Setter functions:
		public function setAddress1($address1){
			$this->address1 = $address1;
		}

		public function setAddress2($address2){
			$this->address2 = $address2;
		}

		public function setZipcode($zipcode) {
			$this->zipcode = $zipcode;
		}
		public function setCity($city) {
			$this->city= $city;
		}

		public function setState($state) {
			$this->state = $state;
		}

		public function setCountry($country) {
			$this->country = $country;
		}

		public function setFreeShipping($free) {
			$this->free_shipping = $free;
		}

		public function setType($type) {
			$this->type= $type;
		}



		/**
		 * This function to set shipping account number
		 * @param  [string] $str
		 * @return [int]
		 */
		public function setShippingAccount($str){

			$this->shipping_account = preg_replace("/[^0-9a-z]/","", mb_strtolower($str));
		}




		public function ValidateShippingAccount(){

			if( ( strlen($this->shipping_account) === 6 ) || ( strlen($this->shipping_account) === 9) ) {

				return true;
		    }else{

		    	return false;
		    }


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
		* This function calls our UPS and FedEx rate functions, normalizes results, and returns a shipping array
		* @return array with shipping information
		*/
		public function shippingCalc($requires_address, $ignore_rates = false) {

			global $ObjShoppingCart;

			//Sets the cart weight as a class variable
			$this->getCartWeight();

			// If we have a zipcode, set it in the user session as well
			if (!empty($this->zipcode)) { $this->setSessionZipcode($this->zipcode); }

			// Grab some info about the zipcode
			$zipcode_detail = $this->zipcodeSearch($this->zipcode);
			$pickupAvailable = false;

			// If we have some zipcode info, the zipcode matches the state in the US, or the country is not US, continue.
			if (empty($zipcode_detail) ||
					(!empty($zipcode_detail) &&
						((mb_strtolower($this->country) == 'us' && $zipcode_detail['state'] == $this->state ) ||
						(mb_strtolower($this->country) != 'us'))
			    	)
				){


				// If we have all the info we need, proceed
				if (!empty($this->country) && !empty($this->city) && !empty($this->zipcode)) {

					// Set our class variables now that we know where we need to ship to
					$objUps = new UpsRateService($this->zipcode, $this->state, $this->country);
					$objUps->setAddress1($this->address1);
					$objUps->setAddress2($this->address2);
					$objUps->setZipcode($this->zipcode);
					$objUps->setCity($this->city);
					$objUps->setState($this->state);
					$objUps->setCountry($this->country);
					$objUps->setType($this->type);

					// Set our class variables now that we know where we need to ship to
					$objFedex = new FedExRateService();
					$objFedex->setAddress1($this->address1);
					$objFedex->setAddress2($this->address2);
					$objFedex->setZipcode($this->zipcode);
					$objFedex->setCity($this->city);
					$objFedex->setState($this->state);
					$objFedex->setCountry($this->country);
					$objFedex->setType($this->type);

					// Shipping rates called from the shopping cart
					if (!$requires_address) {
						// Grab our UPS and FedEx estimates

						$ups_estimates= $objUps->getUpsRates(false, false);

						$fedex_results = $objFedex->getFedexRates(false, false);

						//retrun error if both results are empty
						if( (!empty($fedex_results)) || (!empty($ups_estimates)) )	{

							//If any results are false, set them to null so we can still work with them
							if ($ups_estimates == false)
								$ups_estimates = (array) null;

							if ($fedex_results == false)
								$fedex_results = (array) null;

							// get name from db for fedex
							$sql= $this->dbh->prepare("SELECT name,code,tier,transit_time,use_transit_time,hint FROM bs_shipping_config WHERE active = 1 ");
							$sql -> execute();

							// Loop through our results, and create associative arrays for each property we have.
							// Use code as the key so we can always trace each element back to its service
							while($row = $sql->fetch(PDO::FETCH_ASSOC)){
								$name[$row['code']] = $row['name'];
								$tier[$row['code']] = $row['tier'];
								$transit_time[$row['code']] = $row['transit_time'];
								$use_transit_time[$row['code']] = $row['use_transit_time'];
								$hint[$row['code']] = $row['hint'];
							}

							if (mb_strtolower($this->state)=='nj' && mb_strtolower($this->country) == 'us' ) {
								$pickupAvailable = true;
							}
							// Check if we have fedex results. If we do, we are going to sort them and format them
							if(!empty ($fedex_results) ){


								// Start looping through our FedEx results
								foreach ($fedex_results as $key => $fedex_value) {
								 	foreach ($fedex_value as $key1 => $value) {

								 		//Set name
								 		if ($key1 == 'name') {
								 			foreach ($name as $key2 => $value2) {

									 			if($value == $key2 ) {
									 				if($value2 != ''){
									 					$fedex_results[$key][$key1] = $value2;
									 				}

									 			}
									 		}

									 		//Set tier
										 	foreach ($tier as $tier_key => $tier_value) {
										 		if($tier_key == $value){
										 			$fedex_results[$key]['tier'] =  $tier_value;
										 		}
										 	}

										 	//Set use_transit_time
										 	foreach ($use_transit_time as $use_transit_time_key => $use_transit_time_value) {
										 		if($use_transit_time_key == $value){
										 			$fedex_results[$key]['use_transit_time'] =  $use_transit_time_value;
										 		}
										 	}

										 	//Set transit time
										 	foreach ($transit_time as $transit_time_key => $transit_time_value) {
										 		if($transit_time_key == $value){
										 			$fedex_results[$key]['transit_time'] =  $transit_time_value;
										 		}
										 	}

										 	//Set hint
										 	foreach ($hint as $hint_key => $hint_value) {
										 		if($hint_key == $value){
										 			$fedex_results[$key]['hint'] =  $hint_value;
										 		}
										 	}


									 	}


								 	}

								}

								//Use transit time if use_transit_time = 1
								foreach($fedex_results as $fedex_key => $fedex_value) {
									foreach($fedex_value as $fedex_key2 => $fedex_value2) {
										if ($fedex_key2 == 'use_transit_time' && $fedex_value2 == 1) {
											$transitTime = $fedex_results[$fedex_key]['transit_time'];
											$carrierDays = $fedex_results[$fedex_key]['carrier_days'];
											$shipdate = $ObjShoppingCart->getEstimatedDate(null, $transitTime, $carrierDays) ;
											$fedex_results[$fedex_key]['arrivalDate'] = strtotime($shipdate['shipdate_formatted']);
										}
									}
								}

								//Unset unescessary elements
								foreach($fedex_results as $fedex_key => $fedex_value) {
									foreach($fedex_value as $fedex_key2 => $fedex_value2) {
										unset($fedex_results[$fedex_key]['use_transit_time']);
										unset($fedex_results[$fedex_key]['transit_time']);
									}
								}


								//remove value from array if service doesn't supported by us.
								foreach($fedex_results as $key1 => $result) {

									if (!in_array($result['name'], $name)) {
										unset($fedex_results[$key1]);

									}
								}

								// Renumber the array now that it has been sorted
								$fedex_results=array_values(array_filter($fedex_results));

							}



							// Check if we have UPS estimates. If we do, we are going to sort and format them
							if (!empty($ups_estimates)) {

								// Start looping through our UPS results
								foreach ($ups_estimates as $key => $ups_estimate) {

									// Format the arrival date
									$utc_date = gmdate('Y-m-d', strtotime($ups_estimate['arrivalDate']));

									$ups_estimate_array[]=array(
										"carrier" => (string) 'UPS',
										"name"=>(string)$ups_estimate['name'],
										"price"=>(float)$ups_estimate['price'],
										"arrivalDate"=>(int) strtotime($utc_date),
										"carrier_days"=> $ups_estimate['upsDays']
									);

									//Set name
									foreach ($name as $name_key => $name_value) {
							 			if($ups_estimate['name'] == $name_key ) {
							 				if($name_value != ''){
							 					$ups_estimate_array[$key]['name'] = $name_value;
							 				}

							 			}
							 		}


							 		//Set tier
								 	foreach ($tier as $tier_key => $tier_value) {
								 		if($tier_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['tier'] =  $tier_value;
								 		}
								 	}

								 	//Set use_transit_time
								 	foreach ($use_transit_time as $use_transit_time_key => $use_transit_time_value) {
								 		if($use_transit_time_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['use_transit_time'] =  $use_transit_time_value;
								 		}
								 	}

								 	//Set transit time
								 	foreach ($transit_time as $transit_time_key => $transit_time_value) {
								 		if($transit_time_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['transit_time'] =  $transit_time_value;
								 		}
								 	}
								 	//Set hint
								 	foreach ($hint as $hint_key => $hint_value) {
								 		if($hint_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['hint'] =  $hint_value;
								 		}
								 	}

								}

								//Use transit time if use_transit_time = 1
								foreach($ups_estimate_array as $ups_estimate_key => $ups_estimate_value) {
									foreach ($ups_estimate_value as $ups_estimate_key2 => $ups_estimate_value2) {

										if ($ups_estimate_key2 == 'use_transit_time' && $ups_estimate_value2 == 1) {
											$transitTime = $ups_estimate_array[$ups_estimate_key]['transit_time'];
											$carrierDays = $ups_estimate_array[$ups_estimate_key]['carrier_days'];
											$shipdate = $ObjShoppingCart->getEstimatedDate(null, $transitTime, $carrierDays);
											$ups_estimate_array[$ups_estimate_key]['arrivalDate'] = strtotime($shipdate['shipdate_formatted']);
										}

									}

								}

								//Unset unescessary elements
								foreach($ups_estimate_array as $ups_estimate_key => $ups_estimate_value) {
									foreach ($ups_estimate_value as $ups_estimate_key2 => $ups_estimate_value2) {

										unset($ups_estimate_array[$ups_estimate_key]['use_transit_time']);
										unset($ups_estimate_array[$ups_estimate_key]['transit_time']);
									}
								}

							}

							if(!empty ($fedex_results) || !empty($ups_estimate_array)) {

								//Merge the results we have into a final array
								if (!empty($ups_estimate_array) && !empty($fedex_results)) {

									$rates = array_merge($ups_estimate_array, $fedex_results); //Merge

								} else if (!empty($ups_estimate_array) && empty($fedex_results)) {

									$rates = $ups_estimate_array; //UPS only

								} else if (!empty($fedex_results) && empty($ups_estimate_array)) {

									$rates = $fedex_results; //FedEx only

								}

								//Loop through the different elements and save these as separate arrays.
								//This is used by array_multisort so we can sort by price ASC, arrivaldate ASC, name ASC.
								foreach($rates as $key => $row) {

									$price[$key] = $row['price'];
									$arrivaldate[$key] = $row['arrivaldate'];
									$name[$key] = $row['name'];
									$tier_rate[$key] = $row['tier'];

								}


								// Our sorting function for rates.
								// Sorts based on tier, then price, then arrivaldate, then name
								function sort_rates($a, $b) {

									if ( $a['tier'] < $b['tier'] ) {
										return -1;
									} elseif ( $a['tier'] > $b['tier'] ) {
										return 1;
									} else {

										// Tiers are equal
										if ( $a['price'] < $b['price'] ) {
											return -1;
										} elseif ( $a['price'] > $b['price'] ) {
											return 1;
										} else {

											// Prices are equal
											if ( $a['arrivaldate'] < $b['arrivaldate'] ) {
												return -1;
											} elseif ( $a['arrivaldate'] > $b['arrivaldate'] ) {
												return 1;
											} else {

												// Names are equal
												if ( mb_strtolower($a['name']) < mb_strtolower($b['name']) ) {
													return -1;
												} elseif ( mb_strtolower($a['name']) > mb_strtolower($b['name']) ) {
													return 1;
												} else {
													return 0;
												}

											}

										}

									}

								}


								// Call our sort function which our shipping carriers will learn to hate so dearly
								$sorted = usort($rates, "sort_rates");


								$maxTier=0;
								// Find the highest tier.
								for ( $i = 0 ; $i < count($rates); $i++ ) {
									if ( $rates[$i]['tier'] > $maxTier ) {
										$maxTier = $rates[$i]['tier'];
									}
								}


								// Go through the rates by tier. Remove any rates in higher tiers unless they are priced lower
								// than the lowest rate in the lower tier.
								for ( $tier = $maxTier; $tier > 0; $tier-- ) {

									// Look at each rate of the current tier.
									for ( $i = 0 ;$i < count($rates); $i++ ) {

										if ( $rates[$i]['tier'] === $tier ) {

											// Go through each lower tier for comparison.
											for ( $lowerTier = $tier - 1; $lowerTier > 0; $lowerTier-- ) {

												// Look at each rate of the comparison tier.
												for ( $j = 0; $j < count($rates); $j++ ) {

													// If the rate in the comparison tier has an equal or better price than the rate in the current tier, remove the latter.
													if ( $rates[$j]['tier'] == $lowerTier && $rates[$j]['price'] <= $rates[$i]['price'] ) {

														unset($rates[$i]);

													}

												}

											}

										}

									}

								}

								// Loop through our results and remove tiers now that we have used them for sorting and are now done with them
								foreach ($rates as $rates_key => $rates_value) {
									foreach ($rates_value as $rates_key2 => $rates_value2) {
										unset ($rates[$rates_key]['tier']);
									}
								}

							} else {
								$errors[]="We are unable to retrieve shipping rates at this time. Please contact customer service for further assistance.";
							}

						} else {
							$errors[] = "We are unable to retrieve shipping rates at this time. Please contact customer service for further assistance.";
						}

					// Shipping rates called from the checkout page
					} else if ($requires_address && !empty($this->address1)) {
						// Get our UPS / FedEx Rates

						$ups_estimates = $objUps->getUpsRates(false, false);

						$fedex_results = $objFedex->getFedexRates(false, false);

						//retrun error if both results are empty
						if( (!empty($fedex_results)) || (!empty($ups_estimates)) )	{

							//If any results are false, set them to null so we can still work with them
							if ($ups_estimates == false)
								$ups_estimates = (array) null;

							if ($fedex_results == false)
								$fedex_results = (array) null;

							// Grab our shipping config data from the DB
							$sql= $this->dbh->prepare("SELECT name, code, tier, transit_time, use_transit_time, hint FROM bs_shipping_config WHERE active = 1 ");
							$sql->execute();

							// Loop through our results, and create associative arrays for each property we have.
							// Use code as the key so we can always trace each element back to its service
							while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
								$name[$row['code']] = $row['name'];
								$tier[$row['code']] = $row['tier'];
								$transit_time[$row['code']] = $row['transit_time'];
								$use_transit_time[$row['code']] = $row['use_transit_time'];
								$hint[$row['code']] = $row['hint'];
							}

							// Check if we have FedEx results
							if(!empty($fedex_results)){

								//Loop through each result for correct naming from database
								foreach ($fedex_results as $key => $fedex_value) {
								 	foreach ($fedex_value as $key1 => $value) {

								 		//Set name
								 		if($key1 == 'name'){
								 			foreach ($name as $key2 => $value2) {

									 			if($value == $key2 ) {
									 				if($value2 != ''){
									 					$fedex_results[$key][$key1] = $value2;
									 				}

									 			}
									 		}

									 		//Set tier
										 	foreach ($tier as $tier_key => $tier_value) {
										 		if($tier_key == $value){
										 			$fedex_results[$key]['tier'] =  $tier_value;
										 		}
										 	}

										 	//Set use_transit_time
										 	foreach ($use_transit_time as $use_transit_time_key => $use_transit_time_value) {
										 		if($use_transit_time_key == $value){
										 			$fedex_results[$key]['use_transit_time'] =  $use_transit_time_value;
										 		}
										 	}

										 	//Set transit time
										 	foreach ($transit_time as $transit_time_key => $transit_time_value) {
										 		if($transit_time_key == $value){
										 			$fedex_results[$key]['transit_time'] =  $transit_time_value;
										 		}
										 	}

										 	//Set hint
										 	foreach ($hint as $hint_key => $hint_value) {
										 		if($hint_key == $value){
										 			$fedex_results[$key]['hint'] =  $hint_value;
										 		}
										 	}

									 	}


								 	}

								}

								//Use transit time if use_transit_time = 1
								foreach($fedex_results as $fedex_key => $fedex_value) {
									foreach($fedex_value as $fedex_key2 => $fedex_value2) {
										if ($fedex_key2 == 'use_transit_time' && $fedex_value2 == 1) {
											$transitTime = $fedex_results[$fedex_key]['transit_time'];
											$carrierDays = $fedex_results[$fedex_key]['carrier_days'];
											$shipdate = $ObjShoppingCart->getEstimatedDate(null, $transitTime, $carrierDays);
											$fedex_results[$fedex_key]['arrivalDate'] = strtotime($shipdate['shipdate_formatted']);
										}
									}
								}

								//Unset unescessary elements
								foreach($fedex_results as $fedex_key => $fedex_value) {
									foreach($fedex_value as $fedex_key2 => $fedex_value2) {
										unset($fedex_results[$fedex_key]['use_transit_time']);
										unset($fedex_results[$fedex_key]['transit_time']);
									}
								}


								//remove value from array if service doesn't supported by us.
								foreach($fedex_results as $key1 => $result){

									if(!in_array($result['name'], $name)){
										unset($fedex_results[$key1]);
									}
								}

								// Renumber the array now that it has been sorted
								$fedex_results=array_values(array_filter($fedex_results));

							}

							// Check if we have UPS results
							if (!empty($ups_estimates)) {

								if($this->type== 'residential'){
									$residential = true;
									$commercial = false;
								}else if($this->type == 'commercial') {
									$residential = false;
									$commercial = true;
								}

								// Loop through our UPS results
								foreach ($ups_estimates as $key => $ups_estimate) {

									// Format the arrival date
									$utc_date = gmdate('Y-m-d', strtotime($ups_estimate['arrivalDate']));

									$ups_estimate_array[]=array(
										"carrier" => (string) 'UPS',
										"name"=>(string)$ups_estimate['name'],
										"price"=>(float)$ups_estimate['price'],
										"arrivalDate"=>(int) strtotime($utc_date)
									);

									//Set name
									foreach ($name as $name_key => $name_value) {

							 			if($ups_estimate['name'] == $name_key ) {
							 				if($name_value != ''){
							 					$ups_estimate_array[$key]['name'] = $name_value;
							 				}

							 			}
							 		}

							 		//Set tier
								 	foreach ($tier as $tier_key => $tier_value) {
								 		if($tier_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['tier'] =  $tier_value;
								 		}
								 	}

								 	//Set use_transit_time
								 	foreach ($use_transit_time as $use_transit_time_key => $use_transit_time_value) {
								 		if($use_transit_time_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['use_transit_time'] =  $use_transit_time_value;
								 		}
								 	}

								 	//Set transit time
								 	foreach ($transit_time as $transit_time_key => $transit_time_value) {
								 		if($transit_time_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['transit_time'] =  $transit_time_value;
								 		}
								 	}
								 	//Set hint
								 	foreach ($hint as $hint_key => $hint_value) {
								 		if($hint_key == $ups_estimate['name']){
								 			$ups_estimate_array[$key]['hint'] =  $hint_value;
								 		}
								 	}

								}

								//Use transit time if use_transit_time = 1
								foreach($ups_estimate_array as $ups_estimate_key => $ups_estimate_value) {
									foreach ($ups_estimate_value as $ups_estimate_key2 => $ups_estimate_value2) {

										if ($ups_estimate_key2 == 'use_transit_time' && $ups_estimate_value2 == 1) {
											$transitTime = $ups_estimate_array[$ups_estimate_key]['transit_time'];
											$carrierDays = $ups_estimate_array[$ups_estimate_key]['carrier_days'];
											$shipdate = $ObjShoppingCart->getEstimatedDate(null, $transitTime, $carrierDays);
											$ups_estimate_array[$ups_estimate_key]['arrivalDate'] = strtotime($shipdate['shipdate_formatted']);
										}

									}
								}


								//Unset unescessary elements
								foreach($ups_estimate_array as $ups_estimate_key => $ups_estimate_value) {
									foreach ($ups_estimate_value as $ups_estimate_key2 => $ups_estimate_value2) {
										unset($ups_estimate_array[$ups_estimate_key]['use_transit_time']);
										unset($ups_estimate_array[$ups_estimate_key]['transit_time']);
									}
								}

							}



							if (mb_strtolower($this->state)=='nj' && mb_strtolower($this->country) == 'us' ) {
								$pickupAvailable = true;
							}




							if(!empty ($fedex_results) || !empty($ups_estimate_array)) {


								//Merge the results we have into a final array
								if (!empty($ups_estimate_array) && !empty($fedex_results)) {

									$rates = array_merge($ups_estimate_array, $fedex_results); //Merge

								} else if (!empty($ups_estimate_array) && empty($fedex_results)) {

									$rates = $ups_estimate_array; //UPS only

								} else if (!empty($fedex_results) && empty($ups_estimate_array)) {

									$rates = $fedex_results; //FedEx only

								}

								//Loop through the different elements and save these as separate arrays.
								//This is used by array_multisort so we can sort by price ASC, arrivaldate ASC, name ASC.
								foreach($rates as $key => $row) {
									$price[$key] = $row['price'];
									$arrivaldate[$key] = $row['arrivaldate'];
									$name[$key] = $row['name'];
									$tier_rate[$key] = $row['tier'];
								}


								// Our sorting function for rates.
								// Sorts based on tier, then price, then arrivaldate, then name
								function sort_rates($a, $b) {

									if ( $a['tier'] < $b['tier'] ) {
										return -1;
									} elseif ( $a['tier'] > $b['tier'] ) {
										return 1;
									} else {

										// Tiers are equal
										if ( $a['price'] < $b['price'] ) {
											return -1;
										} elseif ( $a['price'] > $b['price'] ) {
											return 1;
										} else {

											// Prices are equal
											if ( $a['arrivaldate'] < $b['arrivaldate'] ) {
												return -1;
											} elseif ( $a['arrivaldate'] > $b['arrivaldate'] ) {
												return 1;
											} else {

												// Names are equal
												if ( mb_strtolower($a['name']) < mb_strtolower($b['name']) ) {
													return -1;
												} elseif ( mb_strtolower($a['name']) > mb_strtolower($b['name']) ) {
													return 1;
												} else {
													return 0;
												}

											}

										}

									}

								}


								// Call our sort function which our shipping carriers will learn to hate so dearly
								$sorted = usort($rates, "sort_rates");

								$maxTier=0;
								// Find the highest tier.
								for ( $i = 0 ; $i < count($rates); $i++ ) {
									if ( $rates[$i]['tier'] > $maxTier ) {
										$maxTier = $rates[$i]['tier'];
									}
								}


								// Go through the rates by tier. Remove any rates in higher tiers unless they are priced lower
								// than the lowest rate in the lower tier.
								for ( $tier = $maxTier; $tier > 0; $tier-- ) {

									// Look at each rate of the current tier.
									for ( $i = 0 ;$i < count($rates); $i++ ) {

										if ( $rates[$i]['tier'] === $tier ) {

											// Go through each lower tier for comparison.
											for ( $lowerTier = $tier - 1; $lowerTier > 0; $lowerTier-- ) {

												// Look at each rate of the comparison tier.
												for ( $j = 0; $j < count($rates); $j++ ) {

													// If the rate in the comparison tier has an equal or better price than the rate in the current tier, remove the latter.
													if ( $rates[$j]['tier'] == $lowerTier && $rates[$j]['price'] <= $rates[$i]['price'] ) {

														unset($rates[$i]);

													}

												}

											}

										}

									}

								}

								// Loop through our results and remove tiers now that we have used them for sorting and are now done with them
								foreach ($rates as $rates_key => $rates_value) {
									foreach ($rates_value as $rates_key2 => $rates_value2) {
										unset ($rates[$rates_key]['tier']);
									}
								}

								// If someone added a shipping account, we are going to ignore the rates we get and display 0.00 instead.
								if ($ignore_rates) {

									// Loop through our results and zero out the prices
									foreach ($rates as $rates_key => $rates_value) {
										foreach ($rates_value as $rates_key2 => $rates_value2) {
											$rates[$rates_key]['price'] = 0.00;
										}
									}

								}


							} else {
								$errors[]="We are unable to retrieve shipping rates at this time. Please contact customer service for further assistance.";
							}

						} else {
							$errors[] = "We are unable to retrieve shipping rates at this time. Please contact customer service for further assistance.";
						}

					}

				} else {
					$errors[] = 'Please enter a valid US ZIP code.';
				}

			} else if ($zipcode_detail['state'] != $this->state) {
				$errors[] = 'Please enter a valid address.';
			} else {
				$errors[] = 'Please enter a valid US ZIP code.';
			}

			if ($this->type != 'residential')
				$address_type = 'commercial';
			else
				$address_type ='residential';

			$shipping_rate_estimate = array(
				"addressType" => (string) $address_type,
				"pickupAvailable" => $pickupAvailable,
				"shipping_rates" => $rates,
				"errors" =>$errors
			);

			// Return our finalized rates and end the function
			return $shipping_rate_estimate;

		}



		/**
		 * Gets state and country based on zipcode
		 * @param  string $zipcode
		 * @return array containing state and country
		 */
		public function zipcodeSearch($zipcode) {

            $row = NULL;
			// Get the full ZIP code.
            $sessZipCode = isset($_SESSION['zip-code']) ? $_SESSION['zip-code'] : NULL;

			$fullzip = ( isset($zipcode) ? $zipcode : $sessZipCode );

			// Extract the five-digit ZIP code from the 'zipcode' session variable.
			$zipcheck = preg_match("/^(\d{5})([-\s]\d{4})?$/u", trim($fullzip), $matches);

			if ($zipcheck) {

				$fivedigitzip = $matches[1];

				$sql = Connection::getHandle()
                            ->prepare("SELECT zip.CityName as city, zip.StateAbbr AS state, z.countries_id AS country
                                        FROM bs_usa_zip_codes zip LEFT JOIN bs_zones z ON (z.zone_code = zip.StateAbbr)
                                        WHERE zip.ZIPCode = ? LIMIT 1");

                if( $sql->execute(array($fivedigitzip)) ) {

                    $row = $sql->fetch(PDO::FETCH_ASSOC);

                    return $row;
                }
			} else {

				return false;
			}
		}

		/**
		*This function gets weight in to packages
		*/
		public function getWeight($shipmentWeight, $packageWeightLimit = 20) {

			$packageCount = ceil($shipmentWeight/$packageWeightLimit);
			return $packageCount > 0 ? $packageCount : 1;

		}



		/**
		 * Clears out a user's session from bs_sessions
		 */
		public function emptySessionShippingDetails() {

			$_SESSION['shipping_services']     = '';
			$_SESSION['shipping_charges']      = '';
			$_SESSION['shipping_carrier']      = '';
			$_SESSION['shipping_services_pre'] = '';
			$_SESSION['shipping_charges_pre']  = '';
			$_SESSION['shipping_carrier_pre']  = '';

			Session::updateDatabase();

		}



		/**
		* This function to get weight if dim weight is N, based on session
		* @return array $weight_total
		*/
		public function getCartWeight() {

			$ObjShoppingCart = Cart::getFromSession(FALSE);
			$total_weight = 0;

			//global $ObjShoppingCart;

			foreach($ObjShoppingCart->products AS $product){
				$total_weight += $product->weight;
			}

			if($total_weight < 1) { $total_weight = 1; }

			$this->total_weight = $total_weight;
			return $total_weight;
		}



		/**
		 * Updates the user's zipcode in bs_sessions
		 * @param int $zipcode
		 */
		public function setSessionZipcode($zipcode) {

			$_SESSION['zipcode'] = $zipcode;
			Session::updateDatabase();

		}



		public function getShippingChargesBySession() {

			return array(
				'shipping_services_pre'     => isset($_SESSION['shipping_services_pre']) ? $_SESSION['shipping_services_pre'] : NULL,
				'shipping_charges_pre'      => isset($_SESSION['shipping_charges_pre']) ? $_SESSION['shipping_charges_pre'] : NULL,
				'shipping_carrier_pre'      => isset($_SESSION['shipping_carrier_pre']) ? $_SESSION['shipping_carrier_pre'] : NULL,
				'shipping_arrival_estimate' => isset($_SESSION['shipping_arrival_estimate']) ? $_SESSION['shipping_arrival_estimate'] : NULL
			);

		}

		/**
		 * [getTaxExemptBySession description]
		 * @return [type]
		 */

		public function getTaxExemptBySession(){

			return $_SESSION['tax_exempt'];

		}

	}
