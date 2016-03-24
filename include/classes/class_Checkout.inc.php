<?php


	// The checkout class handles the checkout page (form validation, etc.)
	class Checkout {


		// The type of payment
		// 'creditcard' - Credit Card
		// 'net30' - Brimar Net30
		// 'paypal' - PayPal
		public $payment_method;

		// Customer's addresses
		public $shipping_address;
		public $billing_address;

		// Holds shipping carrier, method, account
		public $shipping;

		// Extra data from checkout form
		public $email;
		public $news_letter;
		public $purchase_order;
		public $tag_job;
		public $expedited_shipping;
		public $comments;
		public $admin_comments;
		public $admin;
		public $adminID;
		public $referrer;

		// Credit card
		public $creditcard;

		// Net30
		public $net30;

		// Paypal
		public $paypal;

		// Hold all the validation errors
		public $errors = null;



		/**
		 * Constructor
		 */
		public function __construct($formdata) {

			// Establish a database connection
			$this->setDatabase();

			// Find out what method of payment this is (card, net30, paypal)
			if ($formdata['paypal_submit'] == 1) {
				$this->payment_method = 'paypal'; // paypal
			} else if ($formdata['payment'] == 'Brimar') {
				$this->payment_method = 'net30'; // net30
			} else {
				$this->payment_method = 'creditcard'; // credit card
			}

			// Set shipping address properties from form data
			$this->shipping_address['company'] = $formdata['shipcompany'];
			$this->shipping_address['firstname'] = $formdata['shipfirstname'];
			$this->shipping_address['lastname'] = $formdata['shiplastname'];
			$this->shipping_address['phone'] = $formdata['shipphonenumber'];
			$this->shipping_address['address1'] = $formdata['shipaddress1'];
			$this->shipping_address['address2'] = $formdata['shipaddress2'];
			$this->shipping_address['city'] = $formdata['shipcity'];
			$this->shipping_address['state'] = $formdata['sstate'];
			$this->shipping_address['zip'] = $formdata['shipzip'];
			$this->shipping_address['country'] = $formdata['shipcountry'];

			// If this is not a paypal order, grab the billing data as well
			if ($this->payment_method !== 'paypal') {

				// Set billing address properties from form data
				$this->billing_address['company'] = $formdata['company'];
				$this->billing_address['firstname'] = $formdata['firstname'];
				$this->billing_address['lastname'] = $formdata['lastname'];
				$this->billing_address['phone'] = $formdata['phonenumber'];
				$this->billing_address['address1'] = $formdata['address1'];
				$this->billing_address['address2'] = $formdata['address2'];
				$this->billing_address['city'] = $formdata['city2'];
				$this->billing_address['state'] = $formdata['state'];
				$this->billing_address['zip'] = $formdata['zipcode'];
				$this->billing_address['country'] = $formdata['country'];
				$this->billing_address['fax'] = $formdata['billfaxnumber'];

			}

			// Set shipping properties from form data
			$this->shipping['carrier'] = $formdata['shipping_carrier'];
			$this->shipping['method'] = $formdata['shipping_method'];
			$this->shipping['account'] = $formdata['applied-shipping-account'];

			// Set extra properties from form data
			$this->email = $formdata['email'];
			$this->news_letter = ($formdata['news_letter'] == 'yes' ? 1 : 0);
			$this->purchase_order = $formdata['purchase_order'];
			$this->tag_job = $formdata['tag_job'];
			$this->expedited_shipping = $formdata['expedited-shipping'];
			$this->comments = $formdata['comments'];
			$this->admin_comments = $formdata['adminComment'];
			$this->admin = (mb_strtolower($formdata['admin']) == 'true' ? TRUE : NULL);
			$this->adminID = ($_SESSION['adminID'] > 0 && $_SESSION['adminID'] != $_SESSION['CID'] ? $_SESSION['CID'] : NULL);
			$this->special_comments = $formdata['special_comments'];
			$this->referrer = $formdata['referrer'];
			$this->tax_exempt = $formdata['tax_exempt_status'];

			// If this is a credit card, grab the card info
			if ($this->payment_method == 'creditcard') {
				$this->creditcard['card'] = $formdata['credit_card_number'];
				$this->creditcard['security'] = $formdata['security_code'];
				$this->creditcard['month'] = $formdata['CCExpiresMonth'];
				$this->creditcard['year'] = $formdata['CCExpiresYear'];
			}

			// If this is a net30 order, grab the net30 account info
			if ($this->payment_method == 'net30') {
				$this->net30['card'] = $formdata['brimar_card_number'];
				$this->net30['security'] = $formdata['brimar_security_number'];
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
		 * Validates the checkout form before placing a PayPal order
		 */
		public function validateCheckoutForm() {

			// Globals for stuff we need
			global $ObjShippingCharges, $ObjShoppingCart;

			// Get the cart's URL for usage in some error messages.
			$cart = new Page('cart');
			$cartUrl = $cart->getUrl();

			// Validate the email address.
			$emailValidate = new Validate(array('email' => $this->email));
			$emailValidate->name('email')->email('Please enter a valid email address.');
			$emailValidateResults = $emailValidate->validate();
			if ( !empty($emailValidateResults) ) {
				$this->getErrors($emailValidate);
			} elseif ( $_SESSION['admin'] === TRUE && $_SESSION['adminID'] != $_SESSION['CID'] && mb_strtolower(trim($this->email)) === mb_strtolower(trim(User::getEmailAddressById($_SESSION['CID']))) ) {
				$this->errors .= 'You cannot enter your own email address when doing an admin checkout for a customer. Please enter a different, unique email address (e.g. firstname.lastname@brimar.com).<br>';
			}
			$emailValidate = null;

			// Loop through cart products and check for expired products or those that we no longer have in inventory
			foreach($ObjShoppingCart->products as $product) {

				if (($product->expirationDate <= date("Y-m-d") && $product->expirationDate !== NULL && $product->expirationDate != '0000-00-00') || ($product->inventory <= 0 && $product->limitedInventory)){
					$this->errors .= 'Item # '.$product->skuCode.' is now out of stock. Please order a similar item. <a href="' . htmlspecialchars($cartUrl, ENT_QUOTES, 'UTF-8') . '" class="small button green">Return To Shopping Cart</a><br/>';
				} else if($product->inventory < $product->quantity && $product->limitedInventory) {
					$this->errors .= 'There ' . ($product->inventory > 1 ? 'are' : 'is') . ' only '.$product->inventory.' of #'.$product->skuCode.' available. Please update your quantity. <a href="' . htmlspecialchars($cartUrl, ENT_QUOTES, 'UTF-8') . '" class="small button green">Return To Shopping Cart</a><br/>';
				}

			}

			// Instantiate our contact page so we can include it in errors
			$contact_page = new Page('contact-us');

			// Save extra comments to bs_customer_info
			$this->saveComments();

			// If this is paypal, we'll only have shipping address fields, and the error messages won't be shipping-specific
			if ($this->payment_method == 'paypal') {

				// Instantiate a validator for the form data
				$valid = new Validate($this->shipping_address);
				$valid->name('firstname')->required('First name is required');
				$valid->name('lastname')->required('Last name is required');
				$valid->name('address1')->required('Address is required');
				$valid->name('city')->required('City is required');

                if ( $this->shipping_address['country'] == 'US' ) {
                    $valid->name('state')->required('State is required');
                }

				$valid->name('zip')->required('ZIP code is required');
				$valid->name('phone')->required("You must provide your phone number")->phone("The phone number you have provided does not match a valid phone number format");
				$this->shipping_address['phone'] = $valid->getParsed(); //Get the phone number parsed in our format
				$shipping_confidence_level = $valid->getConfidenceLevel();
				$billing_confidence_level = 3; // We don't have a billing number for PayPal orders, so we're not at all confident in its validity

				// Retrieve any validation errors
				$this->getErrors($valid);

				// If the address was provided but neither is a non-PO address, return an error.
				$po_regex = '/^(box|(p\.?[. ]?o\.?[- ]?|post office )b(\.|ox)?)[^a-z0-9]*[0-9]+[^a-z0-9]*$/ui';
				if ( (!empty($this->shipping_address['address1']) || !empty($this->shipping_address['address2']))
					&& (empty($this->shipping_address['address1']) || preg_match($po_regex, $this->shipping_address['address1']))
					&& (empty($this->shipping_address['address2']) || preg_match($po_regex, $this->shipping_address['address2'])) ) {

					$contact_page = new Page('contact-us');
					$this->errors .= "We are unable to ship to P.O. boxes. Please enter a valid shipping address or contact our <a href='" . $contact_page->getUrl() . "' target='_blank'>customer service department</a> for assistance. <br>";
				}

				// Empty out the validator now that we have the errors we need
				$valid = null;

			} else {

				// Instantiate a validator for the shipping data
				$valid = new Validate($this->shipping_address);
				$valid->name('firstname')->required('Shipping first name is required');
				$valid->name('lastname')->required('Shipping last name is required');
				$valid->name('address1')->required('Shipping address is required');
				$valid->name('city')->required('Shipping city is required');

                if ( $this->shipping_address['country'] == 'US' ) {
                    $valid->name('state')->required('Shipping state is required');
                }

				$valid->name('zip')->required('Shipping ZIP code is required');
				$valid->name('phone')->required("You must provide your shipping phone number")->phone("The shipping phone number you have provided does not match a valid phone number format");
				$this->shipping_address['phone'] = $valid->getParsed(); //Get the phone number parsed in our format
				$shipping_confidence_level = $valid->getConfidenceLevel();

				// Retrieve any validation errors
				$this->getErrors($valid);

				// If the address was provided but neither is a non-PO address, return an error.
				$po_regex = '/^(box|(p\.?[. ]?o\.?[- ]?|post office )b(\.|ox)?)[^a-z0-9]*[0-9]+[^a-z0-9]*$/ui';
				if ( (!empty($this->shipping_address['address1']) || !empty($this->shipping_address['address2']))
					&& (empty($this->shipping_address['address1']) || preg_match($po_regex, $this->shipping_address['address1']))
					&& (empty($this->shipping_address['address2']) || preg_match($po_regex, $this->shipping_address['address2'])) ) {

					$contact_page = new Page('contact-us');
					$this->errors .= "We are unable to ship to P.O. boxes. Please enter a valid shipping address or contact our <a href='" . $contact_page->getUrl() . "' target='_blank'>customer service department</a> for assistance. <br>";
				}

				// Empty out the validator now that we have the errors we need
				$valid = null;

				// Instantiate a validator for the billing data
				$valid = new Validate($this->billing_address);
				$valid->name('firstname')->required('Billing first name is required');
				$valid->name('lastname')->required('Billing last name is required');
				$valid->name('address1')->required('Billing address is required');
				$valid->name('city')->required('Billing city is required');

                if ( $this->billing_address['country'] == 'US' ) {
                    $valid->name('state')->required('Billing state is required');
                }

				$valid->name('zip')->required('Billing ZIP code is required');
				$valid->name('phone')->required("You must provide your billing phone number.")->phone("The billing phone number you have provided does not match a valid phone number format.");
				$this->billing_address['phone'] = $valid->getParsed(); //Get the phone number parsed in our format
				$billing_confidence_level = $valid->getConfidenceLevel();

				// Retrieve any validation errors
				$this->getErrors($valid);

				// Empty out the validator now that we have the errors we need
				$valid = null;

			}

			// If this is a credit card, validate it
			if ($this->payment_method == 'creditcard') {

				$valid = new Validate($this->creditcard);
				$valid->name('card')->required('Credit card number is required');
				$valid->name('security')->required('Security code is required');

				// Retrieve any validation errors
				$this->getErrors($valid);

				// Empty out the validator now that we have the errors we need
				$valid = null;
			}

			// If this is a net30 order, validate it
			if ($this->payment_method == 'net30') {

				$valid = new Validate($this->net30);
				$valid->name('card')->required('Account number is required');
				$valid->name('security')->required('Security code is required');

				// Retrieve any validation errors
				$this->getErrors($valid);

				// Empty out the validator now that we have the errors we need
				$valid = null;
			}

			// If there is no shipping service or shipping charge, the user probably entered their address wrong
			if ($_SESSION['shipping_services_pre'] == '' && ($_SESSION['shipping_charges_pre'] == '' || $_SESSION['shipping_charges_pre'] == '0.00')) {
				$this->errors.="Please select a shipping method by entering a valid shipping address and then waiting for the shipping rates to load before proceeding. <br />";
			}

			// If the user is not tax exempt, is shipping to NJ, and have no sales tax amount, there is a problem
			if ($_SESSION['tax_exempt'] == 'N' && $_SESSION['sales_tax'] == '' && $this->shipping_address['state'] == 'NJ' && $this->shipping_address['country'] == 'US') {
				$this->errors.="Sales tax charges are required. <br />";
			}

            // If the user entered a shipping account, we'll have to verify it.
            if ($this->shipping['account'] != '') {

                $valid = new Validate($this->shipping);
                $valid->name('account')->shippingAccount('The account number you entered is invalid. Please enter your six-digit UPS or nine-digit FedEx account number.');

            }

            //If the shipping method is not 'Customer Pickup' and there are still no shipping charges,
            //prevent checkout and show an error to the user.
            if ( mb_strtolower($_SESSION['shipping_services_pre']) != 'customer pickup' && $this->shipping['account'] == '' && empty($_SESSION['shipping_charges']) ) {
                $this->errors.="Shipping charges are required. <br />";
            }

			// Retrieve any validation errors
			$this->getErrors($valid);

			// Empty out the validator now that we have the errors we need
			$valid = null;

			// If we had a shipping account error, set user_ship to true (used on checkout page)
			if (strpos($this->errors, 'The account number you entered is invalid. Please enter your six-digit UPS or nine-digit FedEx account number.') !== FALSE) {
				$user_ship = true;
			}

			$error_array = array(
				"error_msg" => $this->errors,
				"user_ship" => $user_ship,
				"level" => array(
					'shipping_level' => $shipping_confidence_level,
					'billing_level' => $billing_confidence_level
				)
			);

			return $error_array;

		}



		/**
		 * This function takes an instance of the validator as an argument, pulls
		 * all errors, and adds them onto our error property
		 */
		private function getErrors($valid) {

			// Make sure our validate object is, in fact, and instance of the validator before proceeding
			if ($valid instanceof Validate) {

				// Retrieve any validation errors
				$errors = $valid->validate();

				// Loop through any errors and add to one unified error var with line breaks
				foreach ($errors as $error) {
					$this->errors .= $error . '<br />';
				}

			}

		}



		/**
		 * Updates bs_customer_info with checkout comments
		 */
		private function saveComments() {

			$sql = Connection::getHandle()->prepare(

						"UPDATE bs_customer_info SET purchase_order = ?, tag_job = ?,
							expedited_shipping = ?, comments = ?
						WHERE customers_id = ?"
			);

			$sql->execute(

				array (
					$this->purchase_order,
					$this->tag_job,
					$this->expedited_shipping,
					$this->special_comments,
					$_SESSION['CID']
				)
			);

		}



		public static function getCouponBySession() {

			global $ObjShoppingCart;
			$dbh = Connection::getHandle();

			// Check if the user has a coupon (saved in their session)
			if (!empty($_SESSION['coupon_number'])) {

				$sql = $dbh->prepare("SELECT coupon_number, coupon_value, percent_discount, order_discount
									  FROM bs_coupons
									  WHERE coupon_number = ?
									  AND active = 1 AND (expiration_date is null OR expiration_date > CONVERT_TZ(NOW(), CONCAT(TIMESTAMPDIFF(HOUR, UTC_TIMESTAMP, NOW()), ':00'), '-10:00')) ");
				$sql->execute(array($_SESSION['coupon_number']));
				$coupon = $sql->fetch();

				// We'll need the cart subtotal to calculate coupon savings on
				$subtotal = $ObjShoppingCart->getSubtotal();

				// Do some coupon math
				$coupon_discount = (($subtotal)/100 * $coupon['coupon_value']);

				// Set the coupon discount for the user's session
				$_SESSION['coupon_value'] = number_format($coupon_discount, 2);

				// Return the coupon data
				return $coupon;

			} else {
				return false;
			}

		}



		/**
		 * Once we have tax, shipping, coupons, zipcode, and tax exempt status
		 * we can add up the order total. This function returns the order total,
		 * as well as updates 'invoice_total' and 'sales_tax' columns in bs_sessions
		 *
		 */
		public static function calculateTotal($ObjShoppingCart = NULL) {

			$dbh = Connection::getHandle();
            $fivedigitzip = 0;

			$ObjShoppingCart = Cart::getFromSession(FALSE);

			// Get a valid five-digit zip code string from the $zip variable.
			if( isset($_SESSION['zipcode']) && preg_match("/[0-9]{5}/", $_SESSION['zipcode'], $matches) ) {

                $fivedigitzip = $matches[0];
            }
			// Poll the DB for the state from the zip
			$sql_zipcode_exist=$dbh->prepare("SELECT state FROM bs_zipcodes WHERE zip=:zipcode LIMIT 1");
			$sql_zipcode_exist->execute(array(":zipcode"=>$fivedigitzip));

			$zipcode_data = $sql_zipcode_exist->fetch(PDO::FETCH_ASSOC);

			$zipcode = isset($_SESSION['zipcode']) ? $_SESSION['zipcode'] : NULL;
			$state = $zipcode_data['state'];
			$taxExempt = isset($_SESSION['tax_exempt']) ? $_SESSION['tax_exempt'] : 'N';
			$couponValue = isset($_SESSION['coupon_value']) ? $_SESSION['coupon_value'] : NULL;
			$shipping = isset($_SESSION['shipping_charges']) ? $_SESSION['shipping_charges'] : 0;
			$subtotal = $ObjShoppingCart->getSubtotal();

			// Add up the order, and take whether or not we have to charge tax into account
			if (mb_strtolower($state) == 'nj' && mb_strtolower($taxExempt) != 'y') {

				$total = round(($subtotal + $shipping - $couponValue) * 1.07, 2);
				$sales_tax = round(($subtotal + $shipping - $couponValue) * .07, 2);

			} else {

				$total = round(($subtotal + $shipping - $couponValue), 2);
				$sales_tax = (float) 0.00;

			}

			$_SESSION['sales_tax'] = $sales_tax;
			$_SESSION['invoice_total'] = $total;

			return $total;

		}

	}