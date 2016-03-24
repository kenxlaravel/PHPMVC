<?php


class UpsRateService {

	private $dbh;
	private $s_zip;
	private $s_state;
	private $s_country;
	private $pickuptype ;
	private $t_zip;
	private $t_state;
	private $t_country;
	private $type;
	private $request;
	private $service;
	private $residential;
	private $weight;
	private $error;
	private $errormsg;
	private $xmlarray;
	private $xmlreturndata;

	var	$userid = ups_login;
	var	$passwd = ups_password;
	var	$accesskey = ups_accesskey;
	var	$upstool = ups_api_timeintransit;
	var	$upstoolrate = ups_api_rate;
	var $shippernum = ups_shippernum;

	// Cart-specific
	private $zipcode = null;
	private $city = null;
	private $state = null;
	private $country = null;
	private $address1 = null;
	private $address2 = null;
	public $shipping_account = null;

	// UPS-specific
	private $ups_estimates = array();
	private $ups_delivery_dates_by_service = array();
	private $ups_rates_by_service = array();
	private $ups_error;



	public function __construct($szip = NULL, $sstate = NULL, $scountry = NULL) {

		$this->s_zip = $szip;
		$this->s_state = $sstate;
		$this->s_country = $scountry;
		$this->pickuptype = '1';
		$this->xmlarray = array();
		$this->xmlreturndata = '';
		$this->setDatabase();

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



	public function setZipcode($zipcode) {
		$this->zipcode = $zipcode;
	}



	public function setCity($city) {

		$ObjShippingCharges = new ShippingCharges();

		if (empty($city) && !empty($this->zipcode)) {

			$zipcode_detail=$ObjShippingCharges->zipcodeSearch($this->zipcode);
			$this->city = $zipcode_detail['city'];

		} else {
			$this->city= $city;
		}

	}



	public function setState($state) {

		$ObjShippingCharges = new ShippingCharges();

		if (empty($state) && !empty($this->zipcode) ) {
			$zipcode_detail=$ObjShippingCharges->zipcodeSearch($this->zipcode);
			$this->state = $zipcode_detail['state'];
		} else {
			$this->state = $state;
		}

	}



	public function setCountry($country) {

		$ObjShippingCharges = new ShippingCharges();

		if (empty($country) && !empty($this->zipcode)) {
			$zipcode_detail=$ObjShippingCharges->zipcodeSearch($this->zipcode);
			$this->country = $zipcode_detail['country'];
		} else {
			$this->country = $country;
		}

	}



	public function setFreeShipping($free) {
		$this->free_shipping = $free;
	}

	public function setAddress1($address1){
		$this->address1 = $address1;
	}

	public function setAddress2($address2){
		$this->address2 = $address2;
	}

	public function setType($type){
		$this->type = $type;
	}



	/**
	 * Gets UPS shipping rates
	 * @param  [int]     $zip           [zipcode]
	 * @param  [string]  $state         [state]
	 * @param  [string]  $country       [country]
	 * @param  boolean   $free_shipping [whether shipping is free or not]
	 * @param  boolean   $checkout      [whether this function is called from the checkout page]
	 * @return [array]                  [array of ups rates]
	 */
	public function getUpsRates($free_shipping = false, $checkout = false) {

		$ObjShoppingCart = Cart::getFromSession(FALSE);

		if (!empty($this->zipcode)) {

			// Grab the dimensional weight
			$dim_weight = $ObjShoppingCart->getDimCharges('ups');

			// Instantiate some needed objects
			$ObjShippingCharges = new ShippingCharges();
			$ObjShippingCharges->setSessionZipcode($this->zipcode);
			$ups_service = $this->getUpsServiceData();

			// Query the UPS API for the shipping rate/time estimates and store responses as class variables
			$this->ups_rates_by_service = $this->getUpsEstimate();

			// get subtotal from shopping cart
			$subtotal = $ObjShoppingCart->getSubtotal();

			for ($i = 0; $i < count($this->ups_rates_by_service); $i++) {

				if (!empty($ups_service[$this->ups_rates_by_service[$i]['service']])) {

					if (isset($this->ups_rates_by_service[$i]['negotiated'])) {

						// Negotiated rates were returned from response and added handling charges.
						$negotiated_rates = $this->ups_rates_by_service[$i]['negotiated'] + $dim_weight['charges'];

						// Determine the adjusted price as a percentage (from the database) of the negotiated rates.
						$adjusted_price = round((($ups_service[$this->ups_rates_by_service[$i]['service']]['markup_percentage'] / 100) * $negotiated_rates), 2);

						//set temporary variable
						$ideal_price = $adjusted_price;

						// Determine the minimum price & minimum price as a percentage (from the database) of the negotiated rates.
						$min_price = round((($ups_service[$this->ups_rates_by_service[$i]['service']]['min_markup_percentage'] / 100) * $negotiated_rates), 2);

						// Determine the maximum price & minimum price as a percentage (from the database) of the subtotal.
						$max_price = round((($ups_service[$this->ups_rates_by_service[$i]['service']]['max_order_percentage'] / 100) * $subtotal), 2);

						// if ideal price is higher or equal to minimum price && lower or equal to maximum price then charge ideal price
                        if ( $ideal_price >= $min_price   && $ideal_price  <= $max_price ) {

                            $adjusted_price = $ideal_price;

                        // if ideal price higher or equal to maximum price AND maximum price is higher than minimum price than charge maximum price
                        } else if ( $ideal_price >= $max_price && $max_price > $min_price ) {

                            $adjusted_price = $max_price;

                        //else charge minimum price
                        } else {

                        	$adjusted_price = $min_price;

                        }

					} else {

						// Use the original UPS prices if negotiated rates were not returned.
						$adjusted_price = $this->ups_rates_by_service[$i]['basic'];

					}

					// Declare a flag to set when a match is found in the UPS time estimate array.
					$basic = false;

					// Declare a flag to set when a match is found in the UPS time estimate array.
					$arrival_received = false;

					//Put UPS days of delivery in an array
					$carrier_days = $ups_service[$this->ups_rates_by_service[$i]['service']]['carrier_days'];



					// Navigate to the correct portion of the UPS time estimate array.
					foreach ($this->ups_delivery_dates_by_service as $key1 => $result1) {

						foreach ($result1['ServiceSummary'] as $key2 => $result2) {

							// Look for a match against the current rate name and then set days, dates, days_count, month, and day1 variables according to the UPS response.
							if ($ups_service[$this->ups_rates_by_service[$i]['service']]['code'] === $result2['Service'][$key1]['Code']) {

								$transit_time = $result2['EstimatedArrival'][$key1]['BusinessTransitDays'];
								$arrival_timestamp=	$ObjShoppingCart->getEstimatedDate(null, $transit_time, $carrier_days);
								$arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
								$arrival_month = substr($arrival_timestamp['shipdate_formatted'],4, 2);
								$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);
								$arrival_received = true;
							}

						}

					}

					// If we don't have an arrival date from the estimate, grab it from the database
					if (!$arrival_received) {

						// Use the default transit time for this service to estimate the shipping time.
						$transitTime = $ups_service[$this->ups_rates_by_service[$i]['service']]['defaulttime'];
						$arrival_timestamp = $ObjShoppingCart->getEstimatedDate(null, $transitTime, $carrier_days);
						$arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
						$arrival_month = substr($arrival_timestamp['shipdate_formatted'], 4, 2);
						$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);

					}

					$arrival_formatted = date("F jS, Y", mktime(0, 0, 0, $arrival_month, $arrival_date, $arrival_year));

					// Don't display expensive shipping options for states that will already have two- or three-day service on regular shipping.
					if (!in_array($this->t_state, $ups_service[$this->ups_rates_by_service[$i]['service']]['excludestates'])) {

						$ups_estimates[] = array(
							'carrier' => (string) 'UPS',
							'arrivalDate' => $arrival_formatted,
							'name' => $ups_service[$this->ups_rates_by_service[$i]['service']]['code'],
							'price' => $adjusted_price,
							'upsDays' => $carrier_days
						);
					}
				}
			}
		}


		return  $ups_estimates;

	}



	public function getUpsEstimate() {

		$ObjShippingCharges = new ShippingCharges();

		$total_weight = $ObjShippingCharges->getCartWeight();


		$this->ups_delivery_dates_by_service = $this->ratetime('', $this->zipcode, $this->state, $this->country, $total_weight, 1);

		return $this->raterate('', $this->zipcode, $this->state, $this->country, $total_weight, 1);

	}



	/*
	 * This is adds the contents of the return xml into the array for rate time
	 *
	 */
	public function _get_xml_array_time($data) {

		$values = array();
		$index = array();
		$array = array();
		$parser = xml_parser_create();

		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $data, $values, $index);
		xml_parser_free($parser);
		$i = 0;
		$name = $values[$i]['tag'];
		$array[$name] = $values[$i]['attributes'];
		$array[$name] = $this->__get_xml_array_time($values, $i);

		return $array;

	}



	/*
	 * Adds the contents of the return xml into the array for rate time
	 *
	 * @param    array    $values this is the xml data in an array
	 * @param    int      $i    this is the current location in the array
	 * @return   Array
	 */
	public function __get_xml_array_time($values, &$i) {

		$child = array();
		if ($values[$i]['value']) array_push($child, $values[$i]['value']);

		while (++$i < count($values)) {
			switch ($values[$i]['type']) {
				case 'cdata':
					array_push($child, $values[$i]['value']);
				break;

				case 'complete':
					$name = $values[$i]['tag'];
					$child[$name]= $values[$i]['value'];

					if ($values[$i]['attributes']) {
						$child[$name] = $values[$i]['attributes'];
					}
				break;

				case 'open':
					$name = $values[$i]['tag'];
					$size = sizeof($child[$name]);

					if ($values[$i]['attributes']) {
						$child[$name][$size] = $values[$i]['attributes'];
						$child[$name][$size] = $this->__get_xml_array_time($values, $i);
					} else {
						$child[$name][$size] = $this->__get_xml_array_time($values, $i);
					}
				break;

				case 'close':
					return $child;
				break;
			}
		}

		return $child;
	}



	/**
	 *  get max package weight for ups
	 */
	public function getMaxUpsPackageWeight(){

		$sql= $this->dbh->prepare("SELECT value as max_weight FROM bs_config WHERE setting = 'maxupsweight' LIMIT 1");
		$sql->execute();
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return $row['max_weight'];

	}


	/**
	*This function is to construct requested XML for rate
	*/
	public function construct_request_xmlrate() {

		global $residential, $weight_dim;

		$ObjShoppingCart = Cart::getFromSession(FALSE);

		$ObjShippingCharges = new ShippingCharges();

      	// Get today's date
		$currentdate = $ObjShoppingCart->getEstimatedDate();

        //Grabs an array of packages based on our max UPS package weight
        $packages = $ObjShoppingCart->getPackages($this->getMaxUpsPackageWeight());

    	// Make an empty string that will hold all our package XML
		$packageCode = '';

		// Loop through every package, constructing the xml for each
		foreach($packages as $package) {

			for($a = 1; $a <= $package['number_of_packages']; $a++) {

				// If the package weighs less than a pound, round it up to a pound so UPS doesn't throw a fit
				if ($package['weight_per_package'] < 1) {
					$package['weight_per_package'] = 1;
				}

				$packageCode .= '<Package>
								<PackagingType><Code>02</Code></PackagingType>
								<PackageWeight>
									<UnitOfMeasurement><Code>LBS</Code></UnitOfMeasurement>
									<Weight>'.$this->encode_xml_string($package['weight_per_package']).'</Weight>
								</PackageWeight>
								</Package>';

			}

		}


        // Generate the XML
        $xml = "<?xml version=\"1.0\"?>
				<AccessRequest xml:lang=\"en-US\">
					<AccessLicenseNumber>".$this->encode_xml_string($this->accesskey)."</AccessLicenseNumber>
					<UserId>".$this->encode_xml_string($this->userid)."</UserId>
					<Password>".$this->encode_xml_string($this->passwd)."</Password>
				</AccessRequest>
				<?xml version=\"1.0\"?>
				<RatingServiceSelectionRequest xml:lang=\"en-US\">
					<Request>
						<TransactionReference>
							<CustomerContext>Rating and Service</CustomerContext>
							<XpciVersion>1.0001</XpciVersion>
						</TransactionReference>
						<RequestAction>Rate</RequestAction>
						<RequestOption>".$this->encode_xml_string($this->request)."</RequestOption>
					</Request>
					<PickupType>
						<Code>".$this->encode_xml_string($this->pickuptype)."</Code>
					</PickupType>
					<Shipment>
						<RateInformation>
							<NegotiatedRatesIndicator />
						</RateInformation>
						<Shipper>
							<ShipperNumber>".$this->encode_xml_string($this->shippernum)."</ShipperNumber>
							<Address>
								<StateProvinceCode>NJ</StateProvinceCode>
								<PostalCode>07026</PostalCode>
								<CountryCode>US</CountryCode>
							</Address>
						</Shipper>
						<ShipTo>
							<Address>
							    <AddressLine1>".$this->encode_xml_string($this->address1)."</AddressLine1>
								<StateProvinceCode>".$this->encode_xml_string($this->t_state)."</StateProvinceCode>
								<PostalCode>".$this->encode_xml_string($this->t_zip)."</PostalCode>
								<CountryCode>".$this->encode_xml_string($this->t_country)."</CountryCode>";
								if($this->type == 'residential'){
									$xml.="<ResidentialAddressIndicator></ResidentialAddressIndicator>";
								}
							$xml.="
							</Address>
						</ShipTo>
						<Service>
							<Code>".$this->encode_xml_string($this->service)."</Code>
						</Service>
						<ShipmentServiceOptions>
							<OnCallAir>
								<Schedule>
									<PickupDay>".$this->encode_xml_string($currentdate['shipdate_formatted'])."</PickupDay>
								</Schedule>
							</OnCallAir>
						</ShipmentServiceOptions>
						$packageCode
					</Shipment>
				</RatingServiceSelectionRequest>";

		return $xml;

	}



	public function __runCurlrate() {

		// Construct the XML
		$y = $this->construct_request_xmlrate();

		// Initialize the request and set options.
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $this->upstoolrate); // set the post-to url (do not include the ?query+string here!)
		curl_setopt ($ch, CURLOPT_HEADER, 0); // Header control
		curl_setopt ($ch, CURLOPT_POST, 1);  // tell it to make a POST, not a GET
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "$y");  // put the querystring here starting with "?"
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); // This allows the output to be set into a variable $xyz
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 8);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		// Execute the request and store the response.
		$this->xmlreturndata = curl_exec ($ch);

		// Close the request.
		curl_close ($ch); /// close the curl session

	}



	/*
	 * __get_xml_array($values, &$i)
	 *
	 * This is adds the contents of the return xml into the array for easier processing.
	 *
	 * @access    private
	 * @param    array    $values this is the xml data in an array
	 * @param    int    $i    this is the current location in the array
	 * @return    Array
	 */
	public function __get_xml_arrayrate($values, &$i) {

		$child = array();
		if ($values[$i]['value']) array_push($child, $values[$i]['value']);
		while (++$i < count($values)) {
			switch ($values[$i]['type']) {

				case 'cdata':
					array_push($child, $values[$i]['value']);
				break;

				case 'complete':
					$name = $values[$i]['tag'];
					$child[$name]= $values[$i]['value'];

					if ($values[$i]['attributes']) {
						$child[$name] = $values[$i]['attributes'];
					}
				break;

				case 'open':
					$name = $values[$i]['tag'];
					$size = sizeof($child[$name]);

					if ($values[$i]['attributes']) {
						$child[$name][$size] = $values[$i]['attributes'];
						$child[$name][$size] = $this->__get_xml_arrayrate($values, $i);
					} else {
						$child[$name][$size] = $this->__get_xml_arrayrate($values, $i);
					}
				break;

				case 'close':
					return $child;
				break;

			}
		}

		return $child;

	}



	public function _get_xml_arrayrate($data) {

		$values = array();
		$index = array();
		$array = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $data, $values, $index);
		xml_parser_free($parser);
		$i = 0;
		$name = $values[$i]['tag'];
		$array[$name] = $values[$i]['attributes'];
		$array[$name] = $this->__get_xml_arrayrate($values, $i);

		return $array;

	}



	/*
	* This function gets rates from UPS
	*/
	public function raterate($service='', $tzip,$tstate='', $tcountry='US', $weight, $residential=0, $packagetype='02') {

		global $residential;

		// Set variables.
		$this->request = ($service == '' ? 'shop' : 'rate');
		$this->service = $service;
		$this->t_zip = $tzip;
		$this->t_state = $tstate;
		$this->t_country = $tcountry;
		$this->weight = $weight;
		$this->residential = $residential;
		$this->package_type = $packagetype;

		// Request the rate estimate from UPS.
		$this->__runCurlrate();

		// Parse the response into an array.
		$this->xmlarray = $this->_get_xml_arrayrate($this->xmlreturndata);

		// Check for empty response error
		if ($this->xmlarray == "") {
			$this->error = 0;
			$this->errormsg = "Unable to retrieve the Shipping info";
			return NULL;
		}

		// Grab the response data.
		$values = $this->xmlarray['RatingServiceSelectionResponse']['Response'][0];

		// Check for error in response.
		if ($values['ResponseStatusCode'] == 0) {
			$this->error = $values['Error'][0]['ErrorCode'];
			$this->errormsg = $values['Error'][0]['ErrorDescription'];
			return NULL;
		}

		// Return the content portion of the response.
		return $this->get_ratesrate();

	}



   	public function get_ratesrate() {

        $retArray=array();

        $values = $this->xmlarray['RatingServiceSelectionResponse']['RatedShipment'];
      	$ct = count($values);

        for ($i=0;$i<$ct;$i++) {
			$current=&$values[$i];
			$retArray[$i]['service'] = $current['Service'][0]['Code'];
			$retArray[$i]['basic'] = $current['TransportationCharges'][0]['MonetaryValue'];
			$retArray[$i]['option'] = $current['ServiceOptionsCharges'][0]['MonetaryValue'];
			$retArray[$i]['total'] = $current['TotalCharges'][0]['MonetaryValue'];
			$retArray[$i]['negotiated'] = $current['NegotiatedRates'][0]['NetSummaryCharges'][0]['GrandTotal'][0]['MonetaryValue'];
			$retArray[$i]['days'] =  $current['GuaranteedDaysToDelivery'];
			$retArray[$i]['time'] = $current['ScheduledDeliveryTime'];
        }

        unset($values);

        return $retArray;

    }



	/*
	*This function is to construct requested XML for rate time
	*/
	public function construct_request_xmltime() {

		$ObjShoppingCart = Cart::getFromSession(FALSE);

		// Get the pickup date.
		$currentdate = $ObjShoppingCart->getEstimatedDate();

		// Generate the XML using s_country, s_zip, t_country, t_zip, and the result of getEstimatedDate()
		$xml = "<?xml version=\"1.0\"?>
				<AccessRequest xml:lang=\"en-US\">
					<AccessLicenseNumber>".$this->encode_xml_string($this->accesskey)."</AccessLicenseNumber>
					<UserId>".$this->encode_xml_string($this->userid)."</UserId>
					<Password>".$this->encode_xml_string($this->passwd)."</Password>
				</AccessRequest>
				<?xml version=\"1.0\"?>
				<TimeInTransitRequest xml:lang=\"en-US\">
					<Request>
						<TransactionReference>
							<CustomerContext>TNT_D Origin Country Code</CustomerContext>
							<XpciVersion>1.0002</XpciVersion>
						</TransactionReference>
						<RequestAction>TimeInTransit</RequestAction>
					</Request>
					<TransitFrom>
						<AddressArtifactFormat>
							<PoliticalDivision2>Garfield</PoliticalDivision2>
							<PoliticalDivision1>NJ</PoliticalDivision1>
							<CountryCode>US</CountryCode>
							<PostcodePrimaryLow>07026</PostcodePrimaryLow>
						</AddressArtifactFormat>
					</TransitFrom>
					<TransitTo>
						<AddressArtifactFormat>
							<PoliticalDivision2>".$this->encode_xml_string($this->city)."</PoliticalDivision2>
							<PoliticalDivision1>".$this->encode_xml_string($this->state)."</PoliticalDivision1>
							<CountryCode>".$this->encode_xml_string($this->t_country)."</CountryCode>
							<PostcodePrimaryLow>".$this->encode_xml_string($this->t_zip)."</PostcodePrimaryLow>
						</AddressArtifactFormat>
					</TransitTo>
					<PickupDate>".$this->encode_xml_string($currentdate['shipdate_formatted'])."</PickupDate>
						<ShipmentWeight>
							<UnitOfMeasurement>
								<Code>LBS</Code>
							</UnitOfMeasurement>
							<Weight>".$this->encode_xml_string($this->weight)."</Weight>
						</ShipmentWeight>
					<DocumentsOnlyIndicator />
				</TimeInTransitRequest>";

		return $xml;

	}



	public function __runCurltime() {

		// Construct the XML.
		$y = $this->construct_request_xmltime();

		// Initialize the request and set options.
		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_URL, "$this->upstool"); // set the post-to url (do not include the ?query+string here!)
		curl_setopt ($ch, CURLOPT_HEADER, 0); // Header control
		curl_setopt ($ch, CURLOPT_POST, 1);  // tell it to make a POST, not a GET
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "$y");  // put the querystring here starting with "?"
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); // This allows the output to be set into a variable $xyz
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 8);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		// Execute the request and store the response.
		$this->xmlreturndata = curl_exec ($ch);

		// Close the request.
		curl_close ($ch);

	}



	public function get_ratestime() {

		$retArray = array();
		$values = $this->xmlarray['TimeInTransitResponse']['TransitResponse'];

	    return $values;

	}



	/*
	*This function is to get rate time from UPS
	*/
	public function ratetime($service='',$tzip,$tstate='',$tcountry='US',$weight,$residential=0,$packagetype='02') {

		// Set variables.
		$this->request = ($service == '' ? 'shop' : 'rate');
		$this->service = $service; // Service will be whatever was supplied in argument (even empty string), not always 'shop' or 'rate'...
		$this->t_zip = $tzip;
		$this->t_state = $tstate;
		$this->t_country = $tcountry;
		$this->weight = $weight;
		$this->residential = $residential;
		$this->package_type = $packagetype;

		// Request the time estimate from UPS.
		$this->__runCurltime();

		// Parse the response into an array.
		$this->xmlarray = $this->_get_xml_array_time($this->xmlreturndata);

		// Check for empty response error
		if ($this->xmlarray == "") {
			$this->error = 0;
			$this->errormsg = "Unable to retrieve the Shipping info";
			return NULL;
		}

		// Grab the response data.
		$values = $this->xmlarray['TimeInTransitResponse']['Response'][0];

		// Check for error in response.
		if ($values['ResponseStatusCode'] == 0) {
			$this->error=$values['Error'][0]['ErrorCode'];
			$this->errormsg=$values['Error'][0]['ErrorDescription'];
			return NULL;
		}

		// Return the content portion of the response.
		return $this->get_ratestime();

	}



	/*
	*This function to get ups service data
	*/
	public	function getUpsServiceData() {

		// Prepare the output array.
		$data = array();

		// Query the database.
		$sql=$this->dbh->prepare("SELECT code_num,
										 code,
										 name,
										 transit_time,
										 excluded_states,
										 markup_percentage,
										 max_order_percentage,
										 min_markup_percentage,
										 sunday,
										 monday,
										 tuesday,
										 wednesday,
										 thursday,
										 friday,
										 saturday
								  FROM bs_shipping_config
								  WHERE active='Y'
								  AND carrier = 'UPS'");
		$sql->execute();

		while ($row =$sql->fetch(PDO::FETCH_ASSOC)) {

			// Parse each row's data.
			$code_num = $row['code_num'];
			$code = $row['code'];
			$name = $row['name'];
			$transit_time = (int) $row['transit_time'];
			$excluded_states = ( $row['excluded_states'] == NULL ? array() : explode(',', $row['excluded_states']) );
			$markup_percentage = (float) $row['markup_percentage'];
			$max_order_percentage = (float) $row['max_order_percentage'];
			$min_markup_percentage = (float) $row['min_markup_percentage'];

			$carrier_days = array( !!$row['sunday'],
								   !!$row['monday'],
								   !!$row['tuesday'],
								   !!$row['wednesday'],
								   !!$row['thursday'],
								   !!$row['friday'],
								   !!$row['saturday']
								);

			// Add each row's data to the output array.
			$data[$code_num] = array(
				'name' => $name,
				'code' => $code,
				'defaulttime' => $transit_time,
				'excludestates' => $excluded_states,
				'markup_percentage' => $markup_percentage,
				'max_order_percentage' => $max_order_percentage,
				'min_markup_percentage' => $min_markup_percentage,
				'carrier_days' => $carrier_days
			);
		}

		return $data;
	}


	/**
	 * This function takes an xml string and encodes it
	 * @param string $string
	 * @return string $string
	 */
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