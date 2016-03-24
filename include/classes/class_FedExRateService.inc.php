<?php

class FedexRateService {

	public $rates;

	// Cart-specific
	private $zipcode = null;
	private $city = null;
	private $state = null;
	private $country = null;
	private $address1 = null;
	private $address2 = null;


	// FedEx-specific
	private $fedex_estimates = array();
	private $fedex_rates_by_service = array();
	private $fedex_rates_by_service_residential = array();
	private $fedex_rates_by_service_commercial = array();
	private $fedex_smart_post = array();


	public function __construct() {
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

	public function setAddress1($address1){
		$this->address1 = $address1;
	}

	public function setAddress2($address2){
		$this->address2 = $address2;
	}

	public function setType($type){
		$this->type = $type;
	}



	public function getFedexEstimate() {

		// Instantiate our shipping charges class
		$ObjShippingCharges = new ShippingCharges();

		// Get the total cart weight
		$this->total_weight = $ObjShippingCharges->getCartWeight();


		if (!$this->address1) {

			if ($this->type == 'residential') {
				$residential = 1 ;
			} else if ($this->type == 'commercial') {
				$residential = 0 ;
			}

			$this->fedex_rates_by_service = $this->getRates($residential, $this->address1, $this->address2, $this->zipcode, $this->city, $this->state, $this->country);

			if (!empty($this->fedex_rates_by_service))
				return true;
			else
				return false;

		} else {

			$properties['address1']           = $this->address1;
			$properties['address2']           = $this->address2;
			$properties['city']               = $this->city;
			$properties['state']              = $this->state;
			$properties['zipcode']            = $this->zipcode;
			$properties['country']            = $this->country;

			$fedex = new FedExAddress($properties);
			$fedex_address = $fedex->validateAddress();

			if(mb_strtolower($fedex_address['residential']) == 'business')
				$residential=0;
			else
				$residential = 1;

			$this->fedex_rates_by_service = $this->getRates($residential, $this->address1 , $this->address2,  $this->zipcode, $this->city, $this->state, $this->country);

			if(!empty($this->fedex_rates_by_service))
				return true;
			else
				return false;
		}

	}



	/**
	 * Gets FedEx shipping rates
	 * @param  [int]     $zip           [zipcode]
	 * @param  [string]  $state         [state]
	 * @param  [string]  $country       [country]
	 * @param  boolean   $free_shipping [whether shipping is free or not]
	 * @param  boolean   $checkout      [whether this function is called from the checkout page]
	 * @return [array]                  [array of ups rates]
	 */
	public function getFedexRates($free_shipping = false, $checkout = false) {

		$ObjShoppingCart = Cart::getFromSession(FALSE);

		if (!empty($this->zipcode)) {

			// Grab the dimensional weight
			$dim_weight = $ObjShoppingCart->getDimCharges('fedex');

			// Instantiate some needed objects
			$ObjShippingCharges = new ShippingCharges();
			$xmlParser = new xmlParser();

			// get subtotal of shopping cart items
			$subtotal = $ObjShoppingCart->getSubtotal();

			// Query the FED Ex API for the shipping rate/time estimates and store responses as class variables
			$success = $this->getFedexEstimate();

			//get markup percentage ,excluded state etc for fedex
			$data = $this->getFedexServiceData();

			if ($success) {

				if (!empty($this->zipcode)) {

					$array = $xmlParser->GetXMLTree($this->fedex_rates_by_service);

					// Create an array of transit time strings to integers
					$transit = array(
						'ONE_DAY' => 1,
						'TWO_DAYS' => 2,
						'THREE_DAYS' => 3,
						'FOUR_DAYS' => 4,
						'FIVE_DAYS' => 5,
						'SIX_DAYS' => 6,
						'SEVEN_DAYS' => 7,
						'EIGHT_DAYS' => 8,
						'NINE_DAYS' => 9,
						'TEN_DAYS' => 10
					);

					$notification= $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['RATEREPLY'][0]['NOTIFICATIONS'][0]['SEVERITY'][0]['VALUE'];

					if (mb_strtolower($notification)!= 'failure' && mb_strtolower($notification) != 'error') {

						foreach($array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['RATEREPLY'][0]['RATEREPLYDETAILS'] AS $key => $rate_reply) {

							// Negotiated rates were returned from response and added handling charges.
							$negotiated_rates = $rate_reply['RATEDSHIPMENTDETAILS'][0]['SHIPMENTRATEDETAIL'][0]['TOTALNETCHARGE'][0]['AMOUNT'][0]['VALUE'] + $dim_weight['charges'];

							//excluded state for fedex service
							$exclude_service = false;

							foreach($data as $key =>$value){
								if($value['code'] == $rate_reply['SERVICETYPE'][0]['VALUE'] && in_array($this->state,$value['excludestates'] ) ) {
									$exclude_service = true;
								}
							}

							$sql = $this->dbh->prepare("SELECT * FROM bs_shipping_config WHERE code =? AND active = 1 ");
							$sql->execute(array($rate_reply['SERVICETYPE'][0]['VALUE']));
							$row = $sql->fetch(PDO::FETCH_ASSOC);

							if (!empty($row)) {

								// Determine the adjusted price as a percentage (from the database) of the negotiated rates.
								$adjusted_price = round((($row['markup_percentage'] / 100) * $negotiated_rates), 2);

								//set temporary variable
								$ideal_price = $adjusted_price;

								// Determine the minimum price as a percentage (from the database) of the negotiated rates.
								$min_price = round((($row['min_markup_percentage'] / 100) * $negotiated_rates), 2);

								// Determine the maximum price as a percentage (from the database) of the subtotal.
								$max_price = round((($row['max_order_percentage'] / 100) * $subtotal), 2);

								// if ideal price is higher or equal to minimum price && lower or equal to maximum price then charge ideal price
								if ( $ideal_price >= $min_price   && $ideal_price  <= $max_price ){

									$adjusted_price = $ideal_price;

								// if ideal price higher or equal to maximum price AND maximum price is higher than minimum price then charge maximum price
								} else if ( $ideal_price >= $max_price && $max_price > $min_price ) {

									$adjusted_price = $max_price;

								//else charge minimum price
								} else {

									$adjusted_price = $min_price;

								}

							}

							//Put fedex days of delivery in an array
										$carrier_days = array( !!$row['sunday'],
															   !!$row['monday'],
															   !!$row['tuesday'],
															   !!$row['wednesday'],
															   !!$row['thursday'],
															   !!$row['friday'],
															   !!$row['saturday']
															);

							//use transit time if available
							if (!empty($rate_reply['TRANSITTIME'][0]['VALUE'])) {

								if (array_key_exists($rate_reply['TRANSITTIME'][0]['VALUE'],$transit)) {

									$transitTime = $transit[$rate_reply['TRANSITTIME'][0]['VALUE']];
									$arrival_timestamp =$ObjShoppingCart->getEstimatedDate(0,$transitTime,$carrier_days);

									$arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
									$arrival_month = substr($arrival_timestamp['shipdate_formatted'], 4, 2);
									$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);

									$arrival_date_final = mktime(0, 0, 0, $arrival_month, $arrival_date, $arrival_year);
									$utc_date = strtotime(gmdate('Y-m-d', $arrival_date_final));
								}

							// If we don't have an arrival date from the estimate, grab it from the database
							} else {
								$transit_time = $row['transit_time'];
								$arrival_timestamp = $ObjShoppingCart->getEstimatedDate(0,$transit_time,$carrier_days);

								$arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
								$arrival_month = substr($arrival_timestamp['shipdate_formatted'], 4, 2);
								$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);

								$arrival_date_final = mktime(0, 0, 0, $arrival_month, $arrival_date, $arrival_year);
								$utc_date = strtotime(gmdate('Y-m-d', $arrival_date_final));
							}

							if (!$exclude_service) {
								$rates[] = array(
								"carrier" => (string) 'FedEx',
								"name" => (string) $rate_reply['SERVICETYPE'][0]['VALUE'],
								"price" => (float) $adjusted_price,
								"arrivalDate" => (int) $utc_date,
								"carrier_days" => $carrier_days
								);
							}
						}

						return $rates;

					} else {
						return false;
					}

				}

			} else {

				$this->fedex_error =' No Results from Fedex.';
				return false;
			}

		} else {

			$this->fedex_error ='Please enter a valid US ZIP code.';
			return false;

		}

	}



	/**
	* [get max weight for each package of FedEx shipment ]
	* @return [int] [max package weight]
	*/
	public function getMaxFedExPackageWeight() {

		$sql= $this->dbh->prepare("SELECT value as max_weight FROM bs_config WHERE setting = 'maxfedexweight' LIMIT 1");
		$sql->execute();
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return $row['max_weight'];

	}



	/**
	 * [get service rates from fedex ]
	 * @param  [bool] $residential
	 * @param  [string] $address1
	 * @param  [string] $address2
	 * @param  [string] $zipcode
	 * @param  [string] $city
	 * @param  [string] $state
	 * @param  [string] $country
	 * @param  [int] $total_weight
	 * @return [array] $rates  [fedex shipping service rates]
	 */
	public function getRates($residential, $address1, $address2, $zipcode, $city, $state, $country) {

		$ObjShippingCharges = new ShippingCharges();
		$ObjShoppingCart = Cart::getFromSession(FALSE);

		// Grab today's date
		$currentdate = $ObjShoppingCart->getEstimatedDate(0);
		$arrival_date = substr($currentdate['shipdate_formatted'], 6, 2);
		$arrival_month = substr($currentdate['shipdate_formatted'],4, 2);
		$arrival_year = substr($currentdate['shipdate_formatted'], 0, 4);
		$currentdate = $arrival_year.'-'.$arrival_month .'-'.$arrival_date;

		// Grab an array of packages/weights based on the max FedEx package weight
		$packages = $ObjShoppingCart->getPackages($this->getMaxFedExPackageWeight());

		// Make an empty string that will hold all our package XML
		$packageCode = '';

		// Iterator
		$i = 0;


		foreach($packages as $package) {
			$total_packages += $package['number_of_packages'];
		}

		// Loop through every package, constructing the XML for each
		foreach($packages as $package) {

			for($a = 1; $a <= $package['number_of_packages']; $a++) {

				// If the package weighs less than a pound, round it up to a pound so UPS doesn't throw a fit
				if ($package['weight_per_package'] < 1) {
					$package['weight_per_package'] = 1;
				}

				$packageCode .= '<v13:RequestedPackageLineItems>
									<v13:SequenceNumber>' . $this->encode_xml_string($i+1) . '</v13:SequenceNumber>
									<v13:GroupNumber>1</v13:GroupNumber>
									<v13:GroupPackageCount>1</v13:GroupPackageCount>
									<v13:Weight>
										 <v13:Units>LB</v13:Units>
										<v13:Value>'. $this->encode_xml_string($package['weight_per_package']) .'</v13:Value>
									</v13:Weight>
								</v13:RequestedPackageLineItems>';

				$i++;

			}

		}


		// Construct the XML
		$y = '
		<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v13="http://fedex.com/ws/rate/v13">
		<soapenv:Header/>
			<soapenv:Body>
				<v13:RateRequest>
					<v13:WebAuthenticationDetail>
						<v13:UserCredential>
							<v13:Key>'.fedex_accesskey_prod.'</v13:Key>
							<v13:Password>'.fedex_password_prod.'</v13:Password>
						</v13:UserCredential>
					</v13:WebAuthenticationDetail>
					<v13:ClientDetail>
						<v13:AccountNumber>'.fedex_account_prod.'</v13:AccountNumber>
						<v13:MeterNumber>'.fedex_meter_prod.'</v13:MeterNumber>
					</v13:ClientDetail>
					<v13:TransactionDetail>
						<v13:CustomerTransactionId>Rate a Single Package v13</v13:CustomerTransactionId>
					</v13:TransactionDetail>
					<v13:Version>
						<v13:ServiceId>crs</v13:ServiceId>
						<v13:Major>13</v13:Major>
						<v13:Intermediate>0</v13:Intermediate>
						<v13:Minor>0</v13:Minor>
					</v13:Version>
					<v13:ReturnTransitAndCommit>1</v13:ReturnTransitAndCommit>
					<v13:RequestedShipment>
						<v13:ShipTimestamp>' . $this->encode_xml_string($currentdate) . 'T' . date('H:i:s') . '</v13:ShipTimestamp>
						<v13:DropoffType>REGULAR_PICKUP</v13:DropoffType>
						<v13:Shipper>
							<v13:AccountNumber></v13:AccountNumber>
							<v13:Tins>
								<v13:TinType>PERSONAL_STATE</v13:TinType>
								<v13:Number>1057</v13:Number>
								<v13:Usage>ShipperTinsUsage</v13:Usage>
							</v13:Tins>
							<v13:Address>
								<v13:StreetLines>64 Outwater Lane</v13:StreetLines>
								<v13:StreetLines></v13:StreetLines>
								<v13:City>Garfield</v13:City>
								<v13:StateOrProvinceCode>NJ</v13:StateOrProvinceCode>
								<v13:PostalCode>07026</v13:PostalCode>
								<v13:UrbanizationCode>NJ</v13:UrbanizationCode>
								<v13:CountryCode>US</v13:CountryCode>
								<v13:Residential>0</v13:Residential>
							</v13:Address>
						</v13:Shipper>
						<v13:Recipient>
							<v13:Address>
								<v13:StreetLines>' . $this->encode_xml_string($address1) . '</v13:StreetLines>
								<v13:StreetLines>' . $this->encode_xml_string($addess2) . '</v13:StreetLines>
								<v13:City>' . $this->encode_xml_string($city) . '</v13:City>
								<v13:StateOrProvinceCode>' . $this->encode_xml_string($state) . '</v13:StateOrProvinceCode>
								<v13:PostalCode>' . $this->encode_xml_string($zipcode) . '</v13:PostalCode>
								<v13:UrbanizationCode>' . $this->encode_xml_string($state) . '</v13:UrbanizationCode>
								<v13:CountryCode>' . $this->encode_xml_string($country) . '</v13:CountryCode>
								<v13:Residential>' . $this->encode_xml_string($residential) . '</v13:Residential>
							</v13:Address>
						</v13:Recipient>
						<v13:RecipientLocationNumber>DEN001</v13:RecipientLocationNumber>
						<v13:Origin>
							<v13:Address>
								<v13:StreetLines>64 Outwater Lane</v13:StreetLines>
								<v13:StreetLines></v13:StreetLines>
								<v13:City>Garfield</v13:City>
								<v13:StateOrProvinceCode>NJ</v13:StateOrProvinceCode>
								<v13:PostalCode>07026</v13:PostalCode>
								<v13:UrbanizationCode></v13:UrbanizationCode>
								<v13:CountryCode>US</v13:CountryCode>
								<v13:Residential>0</v13:Residential>
							</v13:Address>
						</v13:Origin>
						<v13:ShippingChargesPayment>
							<v13:PaymentType>SENDER</v13:PaymentType>
							<v13:Payor>
								<v13:ResponsibleParty>
									<v13:AccountNumber></v13:AccountNumber>
									<v13:Tins>
										<v13:TinType>BUSINESS_STATE</v13:TinType>
										<v13:Number>123456</v13:Number>
									</v13:Tins>
								</v13:ResponsibleParty>
							</v13:Payor>
						</v13:ShippingChargesPayment>
						<v13:SmartPostDetail>
							<v13:Indicia>PARCEL_SELECT</v13:Indicia>
							<v13:AncillaryEndorsement>ADDRESS_CORRECTION</v13:AncillaryEndorsement>
							<v13:HubId>5185</v13:HubId>
						</v13:SmartPostDetail>
						<v13:LabelSpecification>
							<v13:LabelFormatType>COMMON2D</v13:LabelFormatType>
							<v13:ImageType>PNG</v13:ImageType>
							<v13:LabelStockType>PAPER_4X6</v13:LabelStockType>
						</v13:LabelSpecification>
						<v13:RateRequestTypes>ACCOUNT</v13:RateRequestTypes>
						<v13:PackageCount>'.$this->encode_xml_string($i).'</v13:PackageCount>';
						$y.=$packageCode;
						$y.='
					</v13:RequestedShipment>
				</v13:RateRequest>
			</soapenv:Body>
		</soapenv:Envelope>';

		// Initialize the request and set options.
		$ch = curl_init();


		//curl_setopt ($ch, CURLOPT_URL, "https://gatewaybeta.fedex.com:443/web-services");
		curl_setopt ($ch, CURLOPT_URL, "https://ws.fedex.com:443/web-services");
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "$y");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		// Execute the request and store the response.
		$rates = curl_exec ($ch);

		// Close the request.
		curl_close ($ch); /// close the curl session

		return $rates;

	}



	/**
	 * This function returns data for fedex service configuration from database
	 * @return [array] $data
	 */
	public function getFedexServiceData() {

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
										 min_markup_percentage
				   				  FROM bs_shipping_config
				   				  WHERE active = 1
				   				  AND carrier= 'FedEx' ");
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

			// Add each row's data to the output array.
			$data[] = array(
				'name' => $name,
				'code' => $code,
				'defaulttime' => $transit_time,
				'excludestates' => $excluded_states,
				'markup_percentage' => $markup_percentage,
				'max_order_percentage' => $max_order_percentage,
				'min_markup_percentage' => $min_markup_percentage
			);

		}

		return $data;

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
