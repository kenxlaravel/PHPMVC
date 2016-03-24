<?php

class Tracking {

	private $dbh;

	//UPS-specific
	private $ups_userid_name;
	private $ups_userid_pass;
	private $ups_access_key;
	private $upsResponse;

	//FedEx-specific

	private $fedexResponse;
	private $fedex_activity_code = array();


	private $current_tag;
 	private $tagtracker;
 	private $identifier;


	public function __construct() {

		$this->setDatabase();

		$this->ups_userid_name = ups_login;
		$this->ups_userid_pass = ups_password;
		$this->ups_access_key = ups_accesskey;


		$this->current_tag=array();
		$this->tagtracker = array();

		$this->fedex_activity_code = array(
			'AA' => 'At Airport',
			'AR' => 'Arrived at FedEx location',
			'AD' => 'At Delivery',
			'AF' => 'At FedEx Facility',
			'AA' => 'At Pickup',
			'CA' => 'Shipment Canceled',
			'CH' => 'Location Changed',
			'DE' => 'Delivery Exception',
			'DL' => 'Delivered',
			'DP' => 'Departed FedEx Location',
			'DR' => 'Vehicle Furnished, Not Used',
			'DS' => 'Vehical Dispatched',
			'DY' => 'Delay',
			'EA' => 'Enroute to Airport delay',
			'ED' => 'Enroute to Delivery',
			'EO' => 'Enroute to Origin airport',
			'EP' => 'Enroute to Pickup',
			'FD' => 'At Fedex Destination',
			'HL' => 'Hold at Location',
			'IT' => 'In Transit',
			'LO' => 'Left Origin',
			'OC' => 'Order Created',
			'OD' => 'Out for Delivery',
			'PF' => 'Plane in Flight',
			'PL' => 'Plane Landed',
			'PU' => 'Picked Up',
			'RS' => 'Return to Shipper',
			'SE' => 'Shipment Exception',
			'SF' => 'At Sort Facility',
			'SP' => 'Split status - multiple statuses',
			'TR' => 'Transfer'
			);
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
	*This function returns UPS Tracking no based on supplied orderno
	*@param string $orderno
	*@return string ups tracking number
	*/

	public function getTracknum($orderno) {

		$stmt_tracknum=$this->dbh->prepare("SELECT tracking_number, shipping_carrier
											FROM bs_orders
											WHERE order_no=:order_no
											LIMIT 1");
		$stmt_tracknum->execute(array(":order_no"=>$orderno));
		$row_tracknum = $stmt_tracknum->fetch(PDO::FETCH_ASSOC);

		return $row_tracknum;

	}

	/**
	*This function instantiates CURL for ups tracking
	*@param string $tracking_number
	*/
	public function setUpsXml($tracking_number) {

		global $xml;

		$activity = "activity";

		$y = "<?xml version=\"1.0\"?><AccessRequest xml:lang=\"en-US\"><AccessLicenseNumber>".ups_accesskey."</AccessLicenseNumber><UserId>".ups_login."</UserId><Password>".ups_password."</Password></AccessRequest><?xml version=\"1.0\"?><TrackRequest xml:lang=\"en-US\"><Request><TransactionReference><CustomerContext>Example 1</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference><RequestAction>Track</RequestAction><RequestOption>".$activity."</RequestOption></Request><TrackingNumber>".$tracking_number."</TrackingNumber></TrackRequest>";

		$ch = curl_init(); /// initialize a cURL session
		curl_setopt ($ch, CURLOPT_URL,ups_api_track); /// set the post-to url (do not include the ?query+string here!)
		curl_setopt ($ch, CURLOPT_HEADER, 0); /// Header control
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);/// Use this to prevent PHP from verifying the host (later versions of PHP including 5)
		curl_setopt($ch, CURLOPT_POST, 1);  /// tell it to make a POST, not a GET
		curl_setopt($ch, CURLOPT_POSTFIELDS, $y);  /// put the query string here starting with "?"
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); /// This allows the output to be set into a variable $xyz
		$this->upsResponse = curl_exec ($ch); /// execute the curl session and return the output to a variable $xyz
		curl_close ($ch); /// close the curl session

	}



	/**
	*This function instantiates CURL for ups tracking
	*@param string $tracking_number
	*/
	public function setFedExXml($tracking_number) {

		global $xml;

		$activity = "activity";

		$fedexURL = "https://ws.fedex.com:443/web-services"; /// This will be provided to you by FedEx

		$y = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v6="http://fedex.com/ws/track/v6">
				<soapenv:Header/>
				<soapenv:Body>
				<v6:TrackRequest>
					<v6:WebAuthenticationDetail>
						<v6:UserCredential>
							<v6:Key>'.fedex_accesskey_prod.'</v6:Key>
							<v6:Password>'.fedex_password_prod.'</v6:Password>
						</v6:UserCredential>
					</v6:WebAuthenticationDetail>
					<v6:ClientDetail>
						<v6:AccountNumber>'.fedex_account_prod.'</v6:AccountNumber>
						<v6:MeterNumber>'.fedex_meter_prod.'</v6:MeterNumber>
						<v6:IntegratorId></v6:IntegratorId>
						<v6:Localization>
							<v6:LanguageCode>EN</v6:LanguageCode>
							<v6:LocaleCode>US</v6:LocaleCode>
						</v6:Localization>
					</v6:ClientDetail>
					<v6:TransactionDetail>
						<v6:CustomerTransactionId>Track By Number</v6:CustomerTransactionId>
						<v6:Localization>
						<v6:LanguageCode>EN</v6:LanguageCode>
						<v6:LocaleCode>US</v6:LocaleCode>
						</v6:Localization>
					</v6:TransactionDetail>
					<v6:Version>
						<v6:ServiceId>trck</v6:ServiceId>
						<v6:Major>6</v6:Major>
						<v6:Intermediate>0</v6:Intermediate>
						<v6:Minor>0</v6:Minor>
					</v6:Version>
					<v6:PackageIdentifier>
						<v6:Value>'.$this->encode_xml_string($tracking_number).'</v6:Value>
						<v6:Type>TRACKING_NUMBER_OR_DOORTAG</v6:Type>
					</v6:PackageIdentifier>
					<v6:IncludeDetailedScans>1</v6:IncludeDetailedScans>
				</v6:TrackRequest>
				</soapenv:Body>
				</soapenv:Envelope>';

		// Initialize the request and set options.
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$fedexURL );
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "$y");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

		// Execute the request and store the response.
		$tracking = curl_exec ($ch);


		// Close the request.
		curl_close ($ch); /// close the curl session
		$this->fedexResponse = $tracking;

	}



	/**
	* This function constructs xml based on the data/ups response
	*@param string $data/ups_response
	*@param string identifier defined identifier for xml
	*
	*/
	public function getXml($data,$identifier='xml') {
 		global $xml;

 		$this->identifier = $identifier;

 		// create parser object
 		$this->xml_parser = xml_parser_create();

 		// set up some options and handlers
 		xml_set_object($this->xml_parser,$this);
 		xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,0);
 		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
 		xml_set_character_data_handler($this->xml_parser, "characterData");

 		if (!xml_parse($this->xml_parser, $data, TRUE)) {
 			sprintf("XML error: %s at line %d",
 			xml_error_string(xml_get_error_code($this->xml_parser)),
 			xml_get_current_line_number($this->xml_parser));
 		}

 		// we are done with the parser, so let's free it
 		xml_parser_free($this->xml_parser);

 	}  // end constructor: function xml()


 	/**
 	*This fucntion is a callable function derived for xml_set_element_handler at start of an element
 	*@param string $parser specifies XML parser calling the handler
 	*@param string $name specifies name of the elements, that triggers this function from the XML file as a string
 	*@param array $attrs specifies an array containing the elements attributes from the XML file as a string
 	*/
 	public function startElement($parser, $name, $attrs) {

 		array_push($this->current_tag, $name);

 		$curtag = implode("_",$this->current_tag);

 		if(isset($this->tagtracker[$curtag])) {
 			$this->tagtracker[$curtag]++;
 		} else {
 			$this->tagtracker[$curtag]=0;
 		}
 	}

 	/**
 	*This function is a callable function derived for xml_set_element_handler at end of an element
 	*@param $parser specifies a variable containing the XML parser calling the handler
 	*@param string $name specifies a variable containing the name of the elements, that triggers this function, from the XML file as a string
 	*/

 	public function endElement($parser, $name) {

 		$curtag = implode("_",$this->current_tag); 	// piece together tag
 								// before we pop it off,
 								// so we can get the correct
 								// cdata

 		if(!$this->tagdata[$curtag]) {
 			$popped = array_pop($this->current_tag); // or else we screw up where we are
 			return; 	// if we have no data for the tag
 		} else {
 			//set tagdata in to local variable
 			$TD = $this->tagdata[$curtag];
 			unset($this->tagdata[$curtag]);

 			//pop an element from array
 			$popped = array_pop($this->current_tag);

 			$curtag = implode("_",$this->current_tag);

 			$j = $this->tagtracker[$curtag];
			if(!$j) $j = 0;

			if(!is_object($GLOBALS[$this->identifier][$curtag][$j])) {
 				$GLOBALS[$this->identifier][$curtag][$j] = new xml_container; // new class requires as data needs to be accessible via global variables
 			}
 			//store values in global identifier
 			$GLOBALS[$this->identifier][$curtag][$j]->store($name,$TD);

			//unset($TD);*/
			return true;

 		}
 	}

 	/* when expat finds some internal tag character data,
 	   it fires up this function */
 	public function characterData($parser, $cdata) {
 		$curtag = implode("_",$this->current_tag); // piece together tag
 		$this->tagdata[$curtag] .= $cdata;
 	}

 	/**
 	* This function is to display information of tracking from xml
 	* @param string $tracking array of shipping carrier and tracking number
 	* @return array $response array value of returned from xml
 	*/
 	public function getDisplay($tracking, $carrier) {

		global $xml;


 		if (!empty($tracking['tracking_number'])) {

 			//If this is UPS tracking, format it based on the UPS spec
 			if (mb_strtolower($carrier) == 'ups') {

	 			//get upsresponse in to xml
				$this->getXml($this->upsResponse,'xml');
				if(empty($xml)){
					$error = "api";
					$response_array=array(
			 							"Error"=>$error
			 						);

				}else{
					//get error if any
					$error=$xml["TrackResponse_Response_Error"][0]->ErrorSeverity[0];

					//check tracking response code
			 		$response = trim($xml["TrackResponse_Response"][0]->ResponseStatusCode[0]);
					$shippedon=trim($xml["TrackResponse_Shipment"][0]->PickupDate[0]);
					$shippeddeliveryon=trim($xml["TrackResponse_Shipment"][0]->ScheduledDeliveryDate[0]);

					//if tracking response returned 1 then collect respective information in output array
					if($response == "1"){

						$addressLine1 = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->AddressLine1[0] . "\n";
						$addressLine2 = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->AddressLine2[0] . "\n";
						$city = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->City[0] . "\n";
						$state = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->StateProvinceCode[0] . "\n";
						$zipcode = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->PostalCode[0] . "\n"; //three
						$country = $xml["TrackResponse_Shipment_ShipTo_Address"][0]->CountryCode[0] . "\n";
						$uoMeasurement = $xml["TrackResponse_Shipment_Package_PackageWeight_UnitOfMeasurement"][0]->Code[0] . "\n"; //twelve
						$packageWeight = $xml["TrackResponse_Shipment_Package_PackageWeight"][0]->Weight[0] . "\n"; //eleven
						$service_description= $xml["TrackResponse_Shipment_Service"][0]->Description[0] . "\n";//thirteen
						///current location
						$activity_description = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation"][0]->Description[0] . "\n"; //fourteen
						$activity_city = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][0]->City[0] . "\n"; //eighteen
						$activity_country = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][0]->CountryCode[0] . "\n";//nineteen
						$activity_state = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][0]->StateProvinceCode[0] . "\n"; //twenty
						$activity_signedfor = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation"][0]->SignedForByName[0] . "\n";//fifteen
						// end location
						$activity_status_description = $xml["TrackResponse_Shipment_Package_Activity_Status_StatusType"][0]->Description[0] . "\n"; //sixteen
						$activity_status_code = $xml["TrackResponse_Shipment_Package_Activity_Status_StatusType"][0]->Code[0] . "\n";//seventeen
						$activity_date = $xml["TrackResponse_Shipment_Package_Activity"][0]->Date[0] . "\n";//twentyfour
						$activity_time = $xml["TrackResponse_Shipment_Package_Activity"][0]->Time[0] . "\n";//twentyfive

						$yearx = substr($activity_date, 0, 4);
						$monthx = substr($activity_date, 4, 2);
						$dayx = substr($activity_date, 6, 2);
						$hhx = substr($activity_time, 0, 2);
						$mmx = substr($activity_time, 2, 2);
						$ssx = substr($activity_time, 4, 2);

						$year = substr($activity_date, 0, 4);
						$month = substr($activity_date, 4, 2);
						$day = substr($activity_date, 6, 2);

						$shippedyear=substr($shippeddeliveryon, 0, 4);
						$shippedmonth=substr($shippeddeliveryon, 4, 2);
						$shippedday= substr($shippeddeliveryon, 6, 2);

						$activity_status_code = trim($activity_status_code);
						switch($activity_status_code)
						{
							case I:	$stat = "In transit";
									break;
							case D:	$stat = "Delivered";
									break;
							case X:	$stat = "Exception";
									break;
							case P:	$stat = "Pickup";
									break;
							case M:	$stat = "Manifest Pickup";
									break;
						}

						if($shippeddeliveryon!='')
							$schedule_date=$shippedmonth.'/'.$shippedday.'/'.$shippedyear;
						else
							$schedule_date=$monthx.'/'.$dayx.'/'.$yearx;

						//create an output array
						$response_array=array(
								"addressLine1"=>$addressLine1,
								"addressLine2"=>$addressLine2,
								"city"=>$city,
								"state"=>$state,
								"zipcode"=>$zipcode,
								"country"=>$country,
								"uoMeasurement"=>$uoMeasurement,
								"packageWeight"=>$packageWeight,
								"service_description"=>$service_description,
								"activity_city"=>$activity_city,
								"activity_country"=>$activity_country,
								"activity_state"=>$activity_state,
								"activity_signedfor"=>$activity_signedfor,
								"activity_status_description"=>$activity_status_description,
								"activity_status_code"=>$activity_status_code,
								"schedule_date"=>$schedule_date,
								"stat"=>$stat,
								"detailed_activity"=>array()
							);
						//Loop throug each detailed activity array
						for($i=0;$i<count($xml["TrackResponse_Shipment_Package_Activity"]);$i++)
						{

							$detailed_description = $xml["TrackResponse_Shipment_Package_Activity_Status_StatusType"][$i]->Description[0] . "\n";
							$detailed_date = $xml["TrackResponse_Shipment_Package_Activity"][$i]->Date[0] . "\n";
							$detailed_time = $xml["TrackResponse_Shipment_Package_Activity"][$i]->Time[0] . "\n";
							$detailed_city = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][$i]->City[0] . "\n";
							$detailed_state = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][$i]->StateProvinceCode[0] . "\n";
							$detailed_country = $xml["TrackResponse_Shipment_Package_Activity_ActivityLocation_Address"][$i]->CountryCode[0] . "\n";

							$year = substr("$detailed_date", 0, 4);
							$month = substr("$detailed_date", 4, 2);
							$day = substr("$detailed_date", 6, 2);
							$date= $month."/".$day."/".$year;

							$hh = substr("$detailed_time", 0, 2);
							$mm = substr("$detailed_time", 2, 2);
							$ss = substr("$detailed_time", 4, 2);

							$am=date("a", mktime($hh, $mm, $ss, $month , $day, $year));
							if($am=="am")
								$timeampm="A.M.";
							else
								 $timeampm="P.M.";

							$time=$hh.":".$mm." ".$timeampm;

							$response_array['detailed_activity'][]=array(
									"detailed_description"=>$detailed_description,
									"detailed_city"=>$detailed_city,
									"detailed_state"=>$detailed_state,
									"detailed_country"=>$detailed_country,
									"date"=>$date,
									"time"=>$time
							);
						}


			 		}else if(!empty($error)) {
			 			$response_array=array(
			 							"Error"=>$error
			 						);
			 		}
			 	}


			//Otherwise, this is FedEx and we should format that a little differently
			} else if (mb_strtolower($carrier) == 'fedex') {

	 			//get upsresponse in to xml
				$this->setFedExXml($tracking['tracking_number']);
				if($this->fedexResponse){
					$xmlParser = new xmlParser();
				 	$array = $xmlParser->GetXMLTree($this->fedexResponse);

				 	//Grab the name of the service. This will need to be updated to look up the human-readable service
	            	//name from the database
	            	$service_type = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['SERVICETYPE'][0]['VALUE'];

					$notification= $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['NOTIFICATIONS'][0]['SEVERITY'][0]['VALUE'];

	            	if (mb_strtolower($notification)== 'success' || mb_strtolower($notification) == 'warning' || mb_strtolower($notification) == 'notification')  {

	            		$objOrder= new Orders();
	            		$order=$objOrder->GetCustomerOrder($_REQUEST['orderno']);

	            		//grab no of packages , unit of measurement , package weight from fedex
						$package= $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['PACKAGECOUNT'][0]['VALUE'];
						$packageWeight = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['PACKAGEWEIGHT'][0]['VALUE'][0]['VALUE'];
						$uoMeasurement= $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['PACKAGEWEIGHT'][0]['UNITS'][0]['VALUE'];
						//grab if signature available
						$signedBy = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['DELIVERYSIGNATURENAME'][0]['VALUE'];
						$signed_proof =$array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['SIGNATUREPROOFOFDELIVERYAVAILABLE'][0]['VALUE'];

						//grab status detail based on code from fed ex response
						$activity_status_code = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['STATUSCODE'][0]['VALUE'];
						$stat = $this->fedex_activity_code[$activity_status_code];

							$actualDeliveryDate = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['ACTUALDELIVERYTIMESTAMP'][0]['VALUE'];
							$estimatedDeliveryDate = $array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['ESTIMATEDDELIVERYTIMESTAMP'][0]['VALUE'];

							$schedule_date = $actualDeliveryDate ? $actualDeliveryDate : $estimatedDeliveryDate;

						//Add a if statement for Fedex carrier with status of exception
						if ($stat == "Shipment Exception" && empty($schedule_date)){
							$actual_schedule_date = NULL;
						} else {

								$date = explode('T',$schedule_date);
								$actual_schedule_date = date("m/d/Y",strtotime($date[0]));

						}

						 //create an output array
						$response_array=array(
								"addressLine1"=>$order['shipping_street_address'],
								"addressLine2"=>$order['shipping_suburb'],
								"city"=>$order['shipping_city'],
								"state"=>$order['shipping_state'],
								"zipcode"=>$order['shipping_postcode'],
								"country"=>$order['shipping_country'],
								"uoMeasurement"=>$uoMeasurement,
								"packageWeight"=>$packageWeight,
								"service_description"=>$order['shipping_services'],
								"activity_city"=>$activity_city,
								"activity_country"=>$activity_country,
								"activity_state"=>$activity_state,
								"activity_signedfor"=>$signedBy,
								"activity_status_description"=>$activity_status_description,
								"activity_status_code"=>(bool) $signed_proof,
								"schedule_date"=> $actual_schedule_date,
								"stat"=>$stat,
								"detailed_activity"=>array()
							);


	            		//Loop through each tracking event
	            		foreach($array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['TRACKDETAILS'][0]['EVENTS'] AS $key => $rate_reply) {


	            			$timestamp = $rate_reply['TIMESTAMP'][0]['VALUE'];
	            			$timestamp = explode('T', $timestamp);
	            			$date = $timestamp[0];
	            			$time = $timestamp[1];


	            			$year = mb_substr($date, 0, 4);
	            			$month = mb_substr($date, 5, 2);
	            			$day = mb_substr($date, 8, 2);

	            			$hour = mb_substr($time, 0, 2);
	            			$minute = mb_substr($time, 3, 2);
	            			$second = mb_substr($time, 6, 2);


	            			$am = date("a", mktime($hour, $minute, $second, $month , $day, $year));
							if($am=="am")
								$timeampm="A.M.";
							else
								$timeampm="P.M.";

							//Form all the data we need for our event array
	            			$detailed_description = $rate_reply['EVENTDESCRIPTION'][0]['VALUE'] . "\n";
	            			$detailed_city = $rate_reply['ADDRESS'][0]['CITY'][0]['VALUE'] . "\n";
	            			$detailed_state = $rate_reply['ADDRESS'][0]['STATEORPROVINCECODE'][0]['VALUE'] . "\n";
	            			$detailed_country = $rate_reply['ADDRESS'][0]['COUNTRYCODE'][0]['VALUE'] . "\n";
	            			$detailed_date = $month . "/" . $day . "/" . $year . "\n";
							$detailed_time=$hour.":".$minute." ".$timeampm . "\n";


							//Form the response array. This should be updated to match the UPS response array as closely as possible
							$response_array['detailed_activity'][]=array(
								"detailed_description"=>$detailed_description,
								"detailed_city"=>$detailed_city,
								"detailed_state"=>$detailed_state,
								"detailed_country"=>$detailed_country,
								"date"=>$detailed_date,
								"time"=>$detailed_time
							);
	            		}
	            	}
	            	else{
	            		//grab error if any
	            		$error=$array['SOAP-ENV:ENVELOPE'][0]['SOAP-ENV:BODY'][0]['TRACKREPLY'][0]['NOTIFICATIONS'][0]['MESSAGE'][0]['VALUE'];
	            		$response_array =array ("Error" => $error);
	            	}
	            }else{
	            		$error='api';
	            		$response_array =array ("Error" => $error);
	            }
			}

			return $response_array;
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


    function order_number_list($CID) {

        $results = array();

        $sql= Connection::getHandle()->prepare(
                "SELECT order_no, total_amount, date_purchased FROM bs_orders WHERE customers_id= $CID
                 ORDER BY date_purchased DESC LIMIT 10");

        if( $sql->execute() ) {

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                $results[] = $row;
            }
        }

        return $results;
	}


} //eof Bs_tracking class

class xml_container {
 	function store($k,$v) {
 		$this->{$k}[] = $v;
 	}
}
