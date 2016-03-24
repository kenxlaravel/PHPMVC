<?php


	class FedExAddress {

		private $path_to_wsdl;

		private $company;
		private $address1;
		private $address2;
        private $city;
        private $state;
        private $zip;
		private $country;


		public function __construct($properties) {

			//$this->$path_to_wsdl = "AddressValidationService_v2.wsdl";

			$this->company = $properties['company'];
			$this->address1 = $properties['address1'];
			$this->address2 = $properties['address2'];
			$this->city = $properties['city'];
			$this->state = $properties['state'];
			$this->zipcode = $properties['zipcode'];
			$this->country = $properties['country'];

		}

		public function validateAddress() {

			$y='<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://fedex.com/ws/addressvalidation/v2">
				<SOAP-ENV:Body>
				<AddressValidationRequest>
				<WebAuthenticationDetail>
				<UserCredential>
				<Key>'.fedex_accesskey_prod.'</Key>
				<Password>'.fedex_password_prod.'</Password>
				</UserCredential>
				</WebAuthenticationDetail>
				<ClientDetail>
				<AccountNumber>'.fedex_account_prod.'</AccountNumber>
				<MeterNumber>'.fedex_meter_prod.'</MeterNumber>
				</ClientDetail>
				<TransactionDetail>
				<CustomerTransactionId>WSVC_addressvalidation</CustomerTransactionId>
				</TransactionDetail>
				<Version>
				<ServiceId>aval</ServiceId>
				<Major>2</Major>
				<Intermediate>0</Intermediate>
				<Minor>0</Minor>
				</Version>
				<RequestTimestamp>2009-07-28T09:30:47-05:00</RequestTimestamp>
				<Options>
				<VerifyAddresses>1</VerifyAddresses>
				<CheckResidentialStatus>1</CheckResidentialStatus>
				<MaximumNumberOfMatches>10</MaximumNumberOfMatches>
				<StreetAccuracy>EXACT</StreetAccuracy>
				<DirectionalAccuracy>EXACT</DirectionalAccuracy>
				<CompanyNameAccuracy>EXACT</CompanyNameAccuracy>
				<ConvertToUpperCase>1</ConvertToUpperCase>
				<RecognizeAlternateCityNames>1</RecognizeAlternateCityNames>
				<ReturnParsedElements>1</ReturnParsedElements>
				</Options>
				<AddressesToValidate>
				<AddressId>String</AddressId>
				<CompanyName>String</CompanyName>
				<Address>
					<StreetLines>'. $this->address1 .'</StreetLines>
					<City>'. $this->city.'</City>
					<StateOrProvinceCode>'. $this->state .'</StateOrProvinceCode>
					<PostalCode>'. $this->zipcode .'</PostalCode>
					<UrbanizationCode>'. $this->state .'</UrbanizationCode>
					<CountryCode>'. $this->country.'</CountryCode>
				</Address>
				</AddressesToValidate>
				</AddressValidationRequest>
				</SOAP-ENV:Body>
				</SOAP-ENV:Envelope>';

			// Initialize the request and set options.
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, "https://ws.fedex.com:443/web-services");
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, "$y");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 8);
	    	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			// Execute the request and store the response.
			$curl_response = curl_exec ($ch);

			// Close the request.
			curl_close ($ch); /// close the curl session

			$xmlParser = new xmlParser();
            $array = $xmlParser->GetXMLTree($curl_response);

            $notification= $array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:NOTIFICATIONS'][0]['V2:SEVERITY'][0]['VALUE'];

            if (mb_strtolower($notification)!= 'failure' && mb_strtolower($notification) != 'error')  {

            	$address['address_line1'] = $array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:ADDRESS'][0]['V2:STREETLINES'][0]['VALUE'];
            	$address['city'] = $array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:ADDRESS'][0]['V2:CITY'][0]['VALUE'];
            	$address['state'] = $array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:ADDRESS'][0]['V2:STATEORPROVINCECODE'][0]['VALUE'];
            	$address['zipcode'] = mb_substr($array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:ADDRESS'][0]['V2:POSTALCODE'][0]['VALUE'], 0, 5);
            	$address['country'] = $array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:ADDRESS'][0]['V2:COUNTRYCODE'][0]['VALUE'];
            	$address['residential'] =$array['SOAPENV:ENVELOPE'][0]['SOAPENV:BODY'][0]['V2:ADDRESSVALIDATIONREPLY'][0]['V2:ADDRESSRESULTS'][0]['V2:PROPOSEDADDRESSDETAILS'][0]['V2:RESIDENTIALSTATUS'][0]['VALUE'];


            }


		return $address;
		}



	}
