<?php

class UpsAddress
    {
        var $accessKey;
        var $userId;
        var $password;
        var $url;
        var $company;
		var $address1;
		var $address2;
        var $city;
        var $state;
        var $zip;
		var $country;
        var $type;
        var $statuscode;
        var $statusdescription;
        var $error;
        var $list = array();

		function UpsAddress($key, $user, $password, $url)
        {
            $this->accessKey=ups_address_key;
            $this->userId=ups_login;
            $this->password=ups_password;
            $this->url=ups_api_address_validation;
        }

        function setCompany($company)
        {
            $this->company = $company;
        }

        function setAddress1($address1)
        {
            $this->address1 = $address1;
        }

		function setAddress2($address2)
        {
            $this->address2 = $address2;
        }

        function setCity($city)
        {
            $this->city = $city;
        }

        function setState($state)
        {
            $this->state = $state;
        }

        function setZip($zip)
        {
            $this->zip = $zip;
        }

        function setCountry($country)
        {
            $this->country = $country;
        }

        function getResponse()  {

            $request =  "<?xml version=\"1.0\"?>\n".
                        "<AccessRequest>\n".
                        "   <AccessLicenseNumber>".ups_address_key."</AccessLicenseNumber>\n".
                        "   <UserId>".ups_login."</UserId>\n".
                        "   <Password>".ups_password."</Password>\n".
                        "</AccessRequest>\n".
                        "<?xml version=\"1.0\"?>\n".
                        "<AddressValidationRequest xml:lang=\"en-US\">\n".
                        "   <Request>\n".
                        "       <TransactionReference>\n".
                        "           <XpciVersion>1.0001</XpciVersion>\n".
                        "       </TransactionReference>\n".
                        "       <RequestAction>XAV</RequestAction>\n";
                        $request.="<RequestOption>3</RequestOption>\n";
						$request.="   </Request>\n";
                        $request.="<MaximumListSize>3</MaximumListSize>\n";
                        $request.="<AddressKeyFormat>\n";
			if(!empty($this->company)) $request  .= "<ConsigneeName>".$this->company."</ConsigneeName>\n";
			if(!empty($this->address1)) $request  .= "<AddressLine>".$this->address1."</AddressLine>\n";
			if(!empty($this->address2)) $request  .= "<AddressLine>".$this->address2."</AddressLine>\n";
			if(!empty($this->city)) $request  .= "<PoliticalDivision2>".$this->city."</PoliticalDivision2>\n";
            if(!empty($this->state)) $request .= "<PoliticalDivision1>".$this->state."</PoliticalDivision1>\n";
            if(!empty($this->zip)) $request   .= "<PostcodePrimaryLow>".$this->zip."</PostcodePrimaryLow>\n";
			if(!empty($this->country)) $request   .= "<CountryCode>".$this->country."</CountryCode>\n";

			$request .= "   </AddressKeyFormat>\n".
                        "</AddressValidationRequest>";
            $header[] = "Host: www.safetysign.com";
            $header[] = "MIME-Version: 1.0";
            $header[] = "Content-type: multipart/mixed; boundary=----doc";
            $header[] = "Accept: text/xml";
            $header[] = "Content-length: ".strlen($request);
            $header[] = "Cache-Control: no-cache";
            $header[] = "Connection: close \r\n";
            $header[] = $request;


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_URL,$this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 8);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                print curl_error($ch);
            } else {
                $xmlParser = new xmlParser();
                $array = $xmlParser->GetXMLTree($data);

                $this->type=$array['ADDRESSVALIDATIONRESPONSE'][0]['ADDRESSCLASSIFICATION'][0]['CODE'][0]['VALUE'];
                $this->statuscode = $array['ADDRESSVALIDATIONRESPONSE'][0]['RESPONSE'][0]['RESPONSESTATUSCODE'][0]['VALUE'];
                $this->statusdescription = $array['ADDRESSVALIDATIONRESPONSE'][0]['RESPONSE'][0]['RESPONSESTATUSDESCRIPTION'][0]['VALUE'];

                if(count($array['ADDRESSVALIDATIONRESPONSE'][0]['RESPONSE'][0]['ERROR']))
                {
                    $error_array = $array['ADDRESSVALIDATIONRESPONSE'][0]['RESPONSE'][0]['ERROR'][0];
                    $error = new error();
                    $error->serverity = $error_array['ERRORSEVERITY'][0]['VALUE'];
                    $error->code = $error_array['ERRORCODE'][0]['VALUE'];
                    $error->description = $error_array['ERRORDESCRIPTION'][0]['VALUE'];
                    $this->error = $error;
                }
                if(count($array['ADDRESSVALIDATIONRESPONSE'][0]['ADDRESSKEYFORMAT']) && $this->statuscode ==1 )
                {

                    foreach($array['ADDRESSVALIDATIONRESPONSE'][0]['ADDRESSKEYFORMAT'] as $key1 => $result)
                    {
                       $address = new address();
					   $address->codes = $result['ADDRESSCLASSIFICATION'][0]['CODE'][0]['VALUE'];
				       $address->descriptions = $result['ADDRESSCLASSIFICATION'][0]['DESCRIPTION'][0]['VALUE'];
					   $address->addressline = $result['ADDRESSLINE'][0]['VALUE'];
                       $address->region = $result['REGION'][0]['VALUE'];
					   $address->politicaldivisions1 = $result['POLITICALDIVISION1'][0]['VALUE'];
                       $address->politicaldivisions2 = $result['POLITICALDIVISION2'][0]['VALUE'];
                       $address->postcodeprimarylow = $result['POSTCODEPRIMARYLOW'][0]['VALUE'];
                       $address->postcodeextenedlow = $result['POSTCODEEXTENDEDLOW'][0]['VALUE'];
                       $address->countrycode = $result['COUNTRYCODE'][0]['VALUE'];

                       $this->list[] = $address;
                    }

               }


                return $this;
            }
        }

    }


class error{
    var $serverity;
    var $code;
    var $description;
}

class address{
    var $codes;
    var $descriptions;
    var $addressline;
    var $region;
    var $politicaldivisions1;
    var $politicaldivisions2;
    var $postcodeprimarylow;
    var $postcodeextenedlow;
}