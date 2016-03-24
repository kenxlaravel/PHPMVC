<?php

class CcGateway
{
	// bill information variables
	var $bEmail=Null;
	var $bNewsLetter=Null;
	var $bCompany=Null;
	var $bFirstName=Null;
	var $bLastName=Null;
	var $bAddress1= Null;
	var $bAddress2= Null;
	var $bCity= Null;
	var $bState= Null;
	var $bZip= Null;
	var $bCountry= Null;
	var $bPhoneNumber= Null;
	var $bFaxNumber= Null;

	// shipping information variables
	var $sCopyBilling=Null;
	var $sShipCompany=Null;
	var $sFirstName=Null;
	var $sLastName=Null;
	var $sShipAddress1=Null;
	var $sShipAddress2=Null;
	var $sShipCity=Null;
	var $sShipState=Null;
	var $sShipZip=Null;
	var $sShipPhoneNumber=Null;
	var $sShipFaxNumber=Null;
	var $sShipCountry=Null;
	var $sShippingAddressType=Null;
	var $sPurchaseOrder=Null;
	var $sJobName=Null;
	var $sExpeditedShipping=Null;
	var $sShippingMethod=Null;
	var $couponCode=Null;
	var $taxExemptStatus=Null;
	var $shipping_account=Null;

	// credit card variables
	var $ccName = Null;
	var $ccNum = Null;
	var $ccType = Null;
	var $ccExpMonth = Null;
	var $ccExpYear = Null;
	var $secureCode = Null;
	var $brimarCardNum= Null;
	var $brimarSecurityNum=Null;
	var $brimarCardNumLastFour=Null;
	var $specialComments=Null;
	var $saveCreditCard=Null;
	var $orderDesc = "Safetysign.com";
	var $orderNo =Null;
	var $salestax=Null;
	var $order_total=Null;

	//Authorize.net
	private $auth_trans_id = null;
	private $auth_trans_note = null;
	private $auth_trans_status = null;
	private $auth_trans_code = null;
	// admin variables
	var $admin=NULL;
	var $adminID=NULL;
	var $adminComment=NULL;
	var $adminReferrer=NULL;

	//Properties
	private $dbh;

	/**
	 * Constructor
	 */
	public function __construct() {

		//Establish a database connection
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

	public function CreditCard(){

		global $error_msg;

			if(isset($_POST['submit']) && $_POST['submit'] == "Place Order") {

				foreach($_POST as $key => $value)
					{
						/*Billing information */
						if($key=="email")
						{
						$this->bEmail=$value;
						}

						else if($key=="news_letter")
						{
						$this->bNewsLetter=$value;
						}

						else if($key=="company")
						{
						$this->bCompany=$value;
						}
						else if($key=="firstname")
						{
						$this->bFirstName=$value;
						}
						else if($key=="lastname")
						{
						$this->bLastName=$value;
						}
						else if($key=="address1")
						{
						$this->bAddress1=$value;
						}
						else if($key=="address2")
						{
						$this->bAddress2=$value;
						}
						else if($key=="city2")
						{
						$this->bCity=$value;
						}
						else if($key=="state")
						{
						$this->bState=$value;
						}
						else if($key=="zipcode")
						{
						$this->bZip=$value;
						}
						else if($key=="country")
						{
						$this->bCountry=$value;
						}
						else if($key=="phonenumber")
						{
						$this->bPhoneNumber=$value;
						}

						else if($key=="billfaxnumber")
						{
						$this->bFaxNumber=$value;
						}
						/*Shipping information */
						else if($key=="copy_billing")
						{
						$this->sCopyBilling=$value;
						}

						else if($key=="shipcompany")
						{
						$this->sShipCompany=$value;
						}
						else if($key=="shipfirstname")
						{
						$this->sFirstName=$value;
						}
						else if($key=="shiplastname")
						{
						$this->sLastName=$value;
						}
						else if($key=="shipaddress1")
						{
						$this->sShipAddress1=$value;
						}
						else if($key=="shipaddress2")
						{
						$this->sShipAddress2=$value;
						}
						else if($key=="shipphonenumber")
						{
						$this->sShipPhoneNumber=$value;
						}
						else if($key=="shipfaxnumber")
						{
						$this->sShipFaxNumber=$value;
						}
						else if($key=="shipcity")
						{
						$this->sShipCity=$value;
						}
						else if($key=="sstate")
						{
						$this->sShipState=$value;
						}
						else if($key=="shipzip")
						{
						$this->sShipZip=$value;
						}
						else if($key=="shipcountry")
						{
						$this->sShipCountry=$value;
						}
						else if($key=="shipping-address-type")
						{
						$this->sShippingAddressType=$value;
						}
						else if($key=="applied-shipping-account")
						{
						$this->shipping_account=$value;
						}
						else if($key=="purchase_order")
						{
						$this->sPurchaseOrder=$value;
						}
						else if($key=="tag_job")
						{
						$this->sJobName=$value;
						}
						else if($key=="expedited-shipping")
						{
						$this->sExpeditedShipping=$value;
						}
						else if($key=="shippingmethod")
						{
						$this->sShippingMethod=$value;
						}
						else if($key=="couponcode")
						{
						$this->couponCode=$value;
						}
						else if($key=="tax_exempt_status")
						{
						$this->taxExemptStatus=$value;
						}
						else if($key=="payment")
						{
						$this->ccType=$value;
						}
						else if($key=="credit_card_number")
						{
						$this->ccNum=$value;
						}
						else if($key=="security_code")
						{
						$this->secureCode=$value;
						}
						else if($key=="CCExpiresMonth")
						{
						$this->ccExpMonth=$value;
						}
						else if($key=="CCExpiresYear")
						{
						$this->ccExpYear=$value;
						}
						else if($key=="brimar_card_number")
						{
						$this->brimarCardNum=$value;
						$this->brimarCardNumLastFour=$value;
						}
						else if($key=="brimar_security_number")
						{
						$this->brimarSecurityNum=$value;
						}
						else if($key=="special_comments")
						{
						$this->specialComments=$value;
						}
						else if($key=="savecreditcard")
						{
						$this->saveCreditCard=$value;
						}
						else if ($key == "adminComment") {
							$this->adminComment = $value;
						}
						else if ($key == "referrer") {
							$this->adminReferrer = $value;
						}




					}

				//Set admin session data
				if (isset($_SESSION['admin'])) {
					$this->admin = $_SESSION['admin'];
				}
				if (isset($_SESSION['adminID'])) {
					$this->adminID = $_SESSION['CID'];
				}

				$this->ccName=$this->bFirstName.$this->bLastName;
				$ccName=$this->bFirstName.$this->bLastName;
			}
	}



	function getCustomerIP() { return $this->customerIP=$_SERVER['REMOTE_ADDR']; }



	function setAuthorizerTransactionData($id, $note, $status, $code) {

		//Sets private class variables with authorize.net response data
		$this->auth_trans_id = $id;
		$this->auth_trans_note = $note;
		$this->auth_trans_status = $status;
		$this->auth_trans_code = $note;
	}



	public function getLastFourCcNumDecrypt($ccNum)
	{
	$stlen=strlen($this->ccNum);
	$minfour=($stlen-4);
	$last_four_no=substr($this->ccNum,$minfour,$stlen);
	$last_four_cc_num_decrypt = base64_encode($last_four_no);
	return $last_four_cc_num_decrypt;
	}



	// End user order credit card information store
	public function SaveCustomerInfo(){

		$order_id=$this->CustomerOrder();
		$this->getCustomerOrderCreditCard($order_id);
		$this->SaveNewsLetter();

	}



	private function insertCustomerAddress() {

		global $objUser;

		$objUserAddress=new Addresses();
		$cid=$objUser->getCID();

		$public_id_billing=$objUserAddress->getUniquePublicId();
		$public_id_shipping=$objUserAddress->getUniquePublicId();

		$sql_address_billing_insert=$this->dbh->prepare("INSERT INTO bs_customer_addresses
											(cid,public_id,company,first_name,last_name,street_address,suburb,
											postcode,city,state,country,phone,fax,default_billing,default_shipping)
											VALUES(
											:cid,:public_id,:company,:first_name,:last_name,:street_name,:suburb,
											:postcode,:city,:state,:country,:phone,:fax,:default_billing,
											:default_shipping
											)");
		$sql_address_billing_insert->execute(array(":cid"=>$cid,
										":public_id"=>$public_id_billing,
										":company"=>$this->bCompany,
										":first_name"=>$this->bFirstName,
										":last_name"=>$this->bLastName,
										":street_name"=>$this->bAddress1,
										":suburb"=>$this->bAddress2,
										":postcode"=>$this->bZip,
										":city"=>$this->bCity,
										":state"=>$this->bState,
										":country"=>$this->bCountry,
										":phone"=>$this->bPhoneNumber,
										":fax"=>$this->bFaxNumber,
										":default_billing"=>1,
										":default_shipping"=>0
										));

		$sql_address_billing_insert=$this->dbh->prepare("INSERT INTO bs_customer_addresses
											(cid,public_id,company,first_name,last_name,street_address,suburb,
												 postcode,city,state,country,phone,default_billing,default_shipping)
											VALUES(
											:cid,:public_id,:company,:first_name,:last_name,:street_address,:suburb,
											:postcode,:city,:state,:country,:phone,:billing,:shipping
											)");

		$sql_address_billing_insert->execute(array(":cid"=>$cid,
										":public_id"=>$public_id_shipping,
										":company"=>$this->sShipCompany,
										":first_name"=>$this->sFirstName,
										":last_name"=>$this->sLastName,
										":street_address"=>$this->sShipAddress1,
										":suburb"=>$this->sShipAddress2,
										":postcode"=>$this->sShipZip,
										":city"=>$this->sShipCity,
										":state"=>$this->sShipState,
										":country"=>$this->sShipCountry,
										":phone"=>$this->sShipPhoneNumber,
										":billing"=>0,
										":shipping"=>1

										));


	}


	/*start user before order credit card information store*/
	public function getCustomerOrderCreditCard($order_id){

		global $objUser;

		if($this->ccNum=='' && $this->brimarCardNum!=''){
		$account= base64_encode($this->brimarCardNum);
		$security=base64_encode($this->brimarSecurityNum);

		$sql_net=$this->dbh->prepare("SELECT netid FROM bs_customer_wallet WHERE cid=:cid");
		$sql_net->execute(array(":cid"=>$objUser->getCID()));
		$brimar_net=$sql_net->fetch(PDO::FETCH_ASSOC);

			if(empty($brimar_net['netid'])){
			$sql=$this->dbh->prepare("SELECT id FROM bs_customer_brimar_net WHERE account_no=:account_no AND security_code=:security_code ");
			$sql->execute(array(
							":account_no"=>$account,
							":security_code"=>$security
							));
			$row_id =$sql->fetch(PDO::FETCH_ASSOC);
			$sql_update=$this->dbh->prepare("INSERT INTO bs_customer_wallet (cid,netid,linked_date) VALUES (:cid,:netid,:ldate)");
			$sql_update->execute(array(
									":cid"=>$objUser->getCID(),
									":netid"=>$row_id['id'],
									":ldate"=>date('Y-m-d')
									));
			}
		}else{

			$this->ccLastFourNum=$this->getLastFourCcNumDecrypt($this->ccNum);
		}


	}
	/*end user before order credit card information store*/

	public function UpdateOrderType($order_id)
	{
		if($order_id!=''){
			$sql_update=$this->dbh->prepare("UPDATE bs_orders SET order_type=:type WHERE orders_id=:orders_id");
			$sql_update->execute(array(":type"=>'paypal',":orders_id"=>$order_id));
		}
	}


	public function SaveNewsLetter(){

		$newsletter=$this->bNewsLetter;
		if($newsletter=="yes"){
			$sql_email_exist=$this->dbh->prepare("SELECT email FROM bs_newsletter WHERE email=:email");
			$sql_email_exist->execute(array(":email"=>$this->bEmail));
			$emailcount=$sql_email_exist->rowCount();
			if($emailcount=="0"){
				$sql=$this->dbh->prepare("INSERT INTO bs_newsletter (email,active,create_date)
										VALUES (:email,:active,:cdate)"
										);
				$sql->execute(array(":email"=>$this->bEmail,
									":active"=>1,
									":cdate"=>date('Y-m-d')
							));
			}
		}
	}



	public function GetBrimarNet($accountno,$securitycode,$total) {

		global $error_brimar;

		$sql_brimar_net=$this->dbh->prepare("SELECT account_no, security_code, credit_amount
											FROM bs_customer_brimar_net
											WHERE account_no=:account_no
											AND security_code=:security
											AND active=:active");
		$sql_brimar_net->execute(array(
								":account_no"=>$accountno,
								":security"=>$securitycode,
								":active"=>1
								));

		$account_count = $sql_brimar_net->rowCount();

		if ($account_count > 0) {

			$brimar_account=$sql_brimar_net->fetch(PDO::FETCH_ASSOC);

			if($total>=$brimar_account['credit_amount']) {

				$error_brimar.="Net30 account credit limit has been reached.";

			} else {

				$account_balance=$brimar_account['credit_amount']-$total;
				$sql_update_balance=$this->dbh->prepare("UPDATE bs_customer_brimar_net
														SET credit_amount=:credit WHERE account_no=:account
														AND security_code=:security
														");
				$sql_update_balance->execute(array(
											":credit"=>$account_balance,
											":account"=>$accountno,
											":security"=>$securitycode
											));
			}
		}

		if ($account_count == 0) {
			$error_brimar.="Net30 Account Number or Security Code incorrect.
			If you would like to use your Net30 Account online please contact Brimar at 800-274-6271 to set up your account.";
		}

	}



	public function getLastFourBrimarCardNumDecrypt($brimar_account) {

		$stlen=strlen($this->brimarCardNumLastFour);
		$minfour=($stlen-4);
		$last_four_no=substr($this->brimarCardNumLastFour,$minfour,$stlen);
		$last_four_brimar_num_decrypt = base64_encode($last_four_no);

		return $last_four_brimar_num_decrypt;

	}



	function generateInvalidPhoneNumber() {

		// Start with an invalid zero or one.
		$phoneNumber = (string) mt_rand(0, 1);

		// Add nine other digits.
		for ( $i = 0; $i < 9; $i++ ) {
			$phoneNumber .= mt_rand(0, 9);
		}

		return $phoneNumber;

	}



	function getUniqueInvalidPhoneNumber() {

		// Generate an invalid phone number.
		$phoneNumber = $this->generateInvalidPhoneNumber();

		// Do so recursively until a unique invalid phone number is generated, then return that.
		return $this->checkIfPhoneNumberIsUnique($phoneNumber) ? $phoneNumber : $this->getUniqueInvalidPhoneNumber();

	}



	function checkIfPhoneNumberIsUnique($phoneNumber) {

		// Assume the number is not unique by default.
		$unique = false;

		if ( !empty($phoneNumber) ) {

			// Check if the phone number has already been used in the database
			$sth = $this->dbh->prepare('SELECT COUNT(`orders_id`) AS `count` FROM `bs_orders` WHERE `billing_phone` = ?');
			$sth->execute(array($phoneNumber));
			$results = $sth->fetch(PDO::FETCH_ASSOC);

			// Note if the number is unique.
			if ( $results['count'] == 0 ) {
				$unique = true;
			}

		}

		return $unique;

	}



	private function purchase_date_time() {
		$houradd=date('H')+1;
		$purchased_date_time=date('Y-m-d').' '.$houradd.date(':i:s');
		return $purchased_date_time;
	}



	// Start user order credit card information store
	public function getOrderCreditCard($order_id) {


		$sql_billing_address=$this->dbh->prepare("SELECT * FROM bs_customer_addresses WHERE cid=:customers_id AND default_billing=:default");
		$sql_billing_address->execute(array(":customers_id"=>$this->getCID(),":default"=>1));
		$row_billing_address=$sql_billing_address->fetch(PDO::FETCH_ASSOC);

		$objUser=new User();
		$brimar=$objUser->getLinkedNet30($objUser->getCID());
		if(!empty($brimar)){
			$ccType='Brimar';
			$lastfour=$this->getLastFourBrimarCardNumDecrypt($net30['account_no']);
			if($row_billing_address){

				$sql_credit_card=$this->dbh->prepare("INSERT INTO bs_credit_card
												(orders_id,payment_method,name ,ccType,
												brimar_card_number,brimar_security_number,
												last_four_brimar_cc_num)
												VALUES (:orders_id,:payment_method,:name,:ccType,
													:brimar_card_number,:brimar_security_number,:last_four_brimar_cc_num)
												");
				$sql_credit_card->execute(array(
									":orders_id"=>$order_id,
									":payment_method"=>'Brimar',
									":name"=>$row_billing_address['name'],
									":ccType"=>$ccType,
									":brimar_card_number"=>$this->brimarCardNum,
									":brimar_security_number"=>$this->brimarSecurityNum,
									":last_four_brimar_cc_num"=>$lastfour
									));
			}
		}

	}



	public function getOrderCreditCardCCEmpty($order_id) {

		if(isset($order_id)){
			$ObjCCtype =new CreditCardType();
			$sql_cc_type=$this->dbh->prepare("SELECT id,orders_id,ccNum,ccType FROM bs_credit_card WHERE orders_id=:orderid");
			$sql_cc_type->execute(array(":orderid"=>$orderid));

			$cc_type_row=$sql_cc_type->fetch(PDO::FETCH_ASSOC);

			if(base64_decode($cc_type_row['ccType'])!='Brimar' && $cc_type_row['orders_id']!=''){
				$number=base64_decode($cc_type_row['ccNum']);
				list($type,$valid)=$ObjCCtype->validateCC($number);
				if($valid){
					$cc_type_code=base64_encode($type);
					$sql_order=$this->dbh->prepare("SELECT orders_id,customers_id FROM bs_orders
													WHERE orders_id=:orderid AND order_type=:type");
					$sql_order->execute(array(
											":orderid"=>$cc_type_row['orders_id'],
											":type"=>'website'
										));
					$order_row = $sql_order->fetch(PDO::FETCH_ASSOC);

					if($order_row){
						$sql_order_update=$this->dbh->prepare("UPDATE bs_orders SET ccType=:cctype
															WHERE orders_id=:orders_id
														");
						$sql_order_update->execute(array(
													":orders_id"=>$cc_type_row['orders_id']
													));

						$sql_credit_update=$this->dbh->prepare("UPDATE bs_credit_card SET ccType=:ccType
																WHERE orders_id=:orders_id");
						$sql_credit_update->execute(array(
													":ccType"=>$cc_type_code,
													":orders_id"=>$cc_type_row['orders_id']
													));
					}

				}
			}

			$sql_cc_update=$this->dbh->prepare("UPDATE bs_credit_card SET ccNum='', brimar_card_number=''
												WHERE orders_id=:orders_id");
			$sql_cc_update->execute(array(":orders_id"=>$order_id));
		}

	}



	public function SaveCustomerCreditCardResponse($CID) {

		// Send a text to Michael and Mery if the Authorize.net API goes down.
		if ($this->auth_trans_code == '38') {
			$email=new Email();
			$email->getCreditCardAPIAlert();
		}

		// Add Authorize.net response data to the bs_customers_info table.
		$this->getCCCreditCardStatusUpdate();

		// Empty data for customer ID #0.
		$this->getCCEmptyZeroCustomerID();

	}



	private function getCCCreditCardStatusUpdate() {

		global $objUser;

		// Add Authorize.net response data to the bs_customer_info table.
		$sql_reason_update=$this->dbh->prepare("UPDATE bs_customer_info SET cc_code=:cc_code,
												cc_reasoncode=:reason_code,
												cc_reasontext=:reason_text,
												transactionid=:transactionid,
												date_purchased=:cdate
												WHERE customers_id=:cid");
		$sql_reason_update->execute(array(
									":cc_code"=>$this->auth_trans_status,
									":reason_code"=>$this->auth_trans_code,
									":reason_text"=>$this->auth_trans_note,
									":transactionid"=>$this->auth_trans_id,
									":cid"=>$objUser->getCID()
									));
	}



	private function  getCCEmptyZeroCustomerID() {

		// Empty data for customer ID #0.
		$sql_customers_info_delete=$this->dbh->prepare("DELETE FROM bs_customer_info WHERE customers_id=:cid");
		$sql_customers_info_delete->execute(array(":cid"=>'0'));

		$sql_customer_address=$this->dbh->prepare("DELETE FROM bs_customer_addresses WHERE cid=:cid");
		$sql_customer_address->execute(array(":cid"=>'0'));

	}
}