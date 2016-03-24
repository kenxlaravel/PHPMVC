<?php

    /*******************************************************************************
     *                Authorize.net AIM Interface using CURL
     *******************************************************************************
     *      File:       authorizenet.class.php
     *      Version:    1.0.1
     *                  You are free to use, distribute, and modify this software
     *                  under the terms of the GNU General Public License.  See the
     *                  included license.txt file.
     *
     *******************************************************************************
     *  REQUIREMENTS:
     *      - PHP4+ with CURL and SSL support
     *      - An Authorize.net AIM merchant account
     *      - (optionally) http://www.authorize.net/support/AIM_guide.pdf
     *
     *******************************************************************************
     *  VERION HISTORY:
     *
     *      v1.0.1 [01.19.2006] - Fixed urlencode glitch (finally)
     *      v1.0.0 [04.07.2005] - Initial Version
     *
     *******************************************************************************
     *  DESCRIPTION:
     *
     *      This class was developed to simplify interfacing a PHP script to the
     *      authorize.net AIM payment gateway.  It does not do all the work for
     *      you as some of the other scripts out there do.  It simply provides
     *      an easy way to implement and debug your own script.
     *
     *******************************************************************************
     */

    class Authorizenet extends CcGateway {

        var $field_string;
        //   var $first_name;
        //   var $last_name;
        //   var $ccNum;
        var $fields = array();

        var $response_string;
        var $response = array();
        var $card_type;
        /*Live account*/
        var $gateway_url = "https://secure.authorize.net/gateway/transact.dll";

        /*Test account*/
        //   var $gateway_url = "https://test.authorize.net/gateway/transact.dll";

        function Authorizenet() {

            // some default values

            $this->add_field('x_version', '3.1');
            $this->add_field('x_delim_data', 'TRUE');
            $this->add_field('x_delim_char', '|');
            $this->add_field('x_url', 'FALSE');
            $this->add_field('x_type', 'AUTH_ONLY');
            $this->add_field('x_method', 'CC');
            $this->add_field('x_relay_response', 'FALSE');

        }

        function add_field($field, $value) {

            // adds a field/value pair to the list of fields which is going to be
            // passed to authorize.net.  For example: "x_version=3.1" would be one
            // field/value pair.  A list of the required and optional fields to pass
            // to the authorize.net payment gateway are listed in the AIM document
            // available in PDF form from www.authorize.net

            $this->fields["$field"] = $value;

        }

        function process() {

            // This function actually processes the payment.  This function will
            // load the $response array with all the returned information.  The return
            // values for the function are:
            // 1 - Approved
            // 2 - Declined
            // 3 - Error

            // construct the fields string to pass to authorize.net
            foreach ( $this->fields as $key => $value ) {
                $this->field_string .= "$key=" . urlencode( $value ) . "&";
            }

            // execute the HTTPS post via CURL
            $ch = curl_init($this->gateway_url); // initiate curl object

            curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
            curl_setopt($ch, CURLOPT_POSTFIELDS,  rtrim( $this->field_string, "& " )); // use HTTP POST to send form data

            $bs_config_setting = 'authorizenet_ssl_check';

            $runWithoutVerification = FALSE;

            // See if we should verify the certificate or not
            $checkCertificate = Settings::getSettingValue($bs_config_setting);

            if ( $checkCertificate ) {

                // Run the cURL request.
                $response = curl_exec($ch);

                // If there was an error, and that error was one of the cURL SSL-related error numbers...
                if ( $response === FALSE && in_array(curl_errno($ch), array(51, 52, 56, 60, 77, 58, 59, 35, 82, 66, 53, 54, 83, 80, 64)) ) {

                    // Send email warning
                    $email = new Email();
                    $email->sendSslCertFail($bs_config_setting, curl_errno($ch));

                    $runWithoutVerification = TRUE;
                }

            } else {

                $runWithoutVerification = TRUE;

            }

            if ( $runWithoutVerification ) {

                // turn off verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                // Run the cURL request.
                $response = curl_exec($ch);

            }

            $this->response_string = urldecode($response);

            if ( curl_errno($ch) ) {
                $this->response['Response Reason Text'] = curl_error($ch);
                return 3;
            } else {
                curl_close($ch);
            }

          // load a temporary array with the values returned from authorize.net
          $temp_values = explode('|', $this->response_string);

          // load a temporary array with the keys corresponding to the values
          // returned from authorize.net (taken from AIM documentation)
          $temp_keys= array (
              "Response Code", "Response Subcode", "Response Reason Code", "Response Reason Text",
              "Approval Code", "AVS Result Code", "Transaction ID", "Invoice Number", "Description",
              "Amount", "Method", "Transaction Type", "Customer ID", "Cardholder First Name",
              "Cardholder Last Name", "Company", "Billing Address", "City", "State",
              "Zip", "Country", "Phone", "Fax", "Email", "Ship to First Name", "Ship to Last Name",
              "Ship to Company", "Ship to Address", "Ship to City", "Ship to State",
              "Ship to Zip", "Ship to Country", "Tax Amount", "Duty Amount", "Freight Amount",
              "Tax Exempt Flag", "PO Number", "MD5 Hash", "Card Code (CVV2/CVC2/CID) Response Code",
              "Cardholder Authentication Verification Value (CAVV) Response Code"
          );

          // add additional keys for reserved fields and merchant defined fields
          for ($i=0; $i<=10; $i++) {
              array_push($temp_keys, 'Reserved Field '.$i);
          }

          array_push($temp_keys, 'Card Type');

          // add additional keys for reserved fields and merchant defined fields
          for ($i=0; $i<=15; $i++) {
              array_push($temp_keys, 'Reserved Field '.$i);
          }


          $i=0;
          while (sizeof($temp_keys) < sizeof($temp_values)) {
              array_push($temp_keys, 'Merchant Defined Field '.$i);
              $i++;
          }

          // combine the keys and values arrays into the $response array.  This
          // can be done with the array_combine() function instead if you are using
          // php 5.
          for ($i=0; $i<sizeof($temp_values);$i++) {
              $this->response["$temp_keys[$i]"] = $temp_values[$i];
          }


          $this->card_type = $this->response['Card Type'];

          // Swap an AVS mismatch error out with a custom error message so it makes sense to our customers
          if ($this->response['Response Reason Code'] == "27") {
              $this->response['Response Reason Text'] = "Credit card authorization failed because of issues with your billing address. Make sure the billing address you entered below exactly matches the one your credit card company has on file, including your Apartment Number, Suite Number, or Floor Number if applicable.";
          }

          // Return the response code.
          return $this->response['Response Code'];

       }

        function get_response_reason_text() {
            return $this->response['Response Reason Text'];
        }

        function dump_fields() {

            // Used for debugging, this function will output all the field/value pairs
            // that are currently defined in the instance of the class using the
            // add_field() function.

            echo "<h3>authorizenet_class->dump_fields() Output:</h3>";
            echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";

            foreach ($this->fields as $key => $value) {
                echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
            }

            echo "</table><br>";
        }

        function dump_response() {

            // Used for debuggin, this function will output all the response field
            // names and the values returned for the payment submission.  This should
            // be called AFTER the process() function has been called to view details
            // about authorize.net's response.

            $i = 0;

            foreach ($this->response as $key => $value) {

                if($key=="Response Code" && $value=="1") {
                    $results["status"] = "Approved";
                } else if ($key=="Response Code" && $value=="2") {
                    $results["status"] = "Declined";
                } else if ($key=="Response Code" && $value=="3") {
                    $results["status"] = "Error";
                } else if ($key=="Response Code" && $value=="4") {
                    $results["status"] = "Held for Review";
                }

                if($key=="Response Reason Code") {
                    $results["subCode"] = $value;
                }

                if($key=="Response Reason Text") {
                    $results["reasonCode"] = $value;
                }

                if($key=="Transaction ID") {
                    $results["transactionId"] = $value;
                }

            }

            return $results;

        }

    }



