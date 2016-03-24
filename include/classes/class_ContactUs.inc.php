<?php

class ContactUs {

	private $contact_name;
	private $contact_email;
	private $contact_company;
	private $contact_phone;
	private $contact_department;
	private $preferred_method_of_contact;
	private $returnreason;
	private $order_number;
	private $message;
	private $error;
	private $dbh;


	public function __construct(){

		if(isset($_REQUEST['contact_name']))					$this->contact_name=$_REQUEST['contact_name'];
		if(isset($_REQUEST['contact_phone']))					$this->contact_phone=$_REQUEST['contact_phone'];
		if(isset($_REQUEST['contact_department']))				$this->contact_department=$_REQUEST['contact_department'];
		if(isset($_REQUEST['contact_company']))					$this->contact_company=$_REQUEST['contact_company'];
		if(isset($_REQUEST['preferred_method_of_contact']))		$this->contact_method=$_REQUEST['preferred_method_of_contact'];
		if(isset($_REQUEST['contact_email']))					$this->contact_email=$_REQUEST['contact_email'];
		if(isset($_REQUEST['message']))							$this->message=$_REQUEST['message'];
		if(isset($_REQUEST['order_number']))					$this->order_number=$_REQUEST['order_number'];
		if(isset($_REQUEST['return-reason-selector']))			$this->returnreason=$_REQUEST['return-reason-selector'];

		$this->error=array();

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


	/**
	*This function checks for empty field
	*@param string $data
	*@param string $content
	*/
	private function isEmptyCheck($data,$content){

		if($data==""){
			 $this->error[] ="The " .$content . " field is empty.";
		}
		if($content=='Email' && $data!=''){

			if(filter_var($data, FILTER_VALIDATE_EMAIL) === FALSE) {
				$this->error[] = "Please enter a valid email address.";
			}
		}
		if($content=='Phone Number' && $data!=""){
			$this->validatePhone($data);
		}
		if($content=='Order Number' && $data!=""){
			$orderno_count=$this->validOrderNo($data);
			if($orderno_count==0){
   				$this->error[]="The order number you entered is invalid or does not exist.";
   			}
		}

	}


	/**
	*This function checks for matching recaptcha
	*/
	private function reCaptchaCheck(){
		$resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["recaptcha_challenge_field"],
		                                $_POST["recaptcha_response_field"]
		                              );
		if (!$resp->is_valid) {
		  $this->error[] = "The reCAPTCHA wasn't entered correctly. Please try again.";
		}
	}


	/**
	* Validate phone number
	*@param $phone
	*/
	private function validatePhone($phone){

		$phone_number=array('phone'=>$phone);
		$valid = new Validate($phone_number);
		$valid->name('phone')->required("You must provide your phone number.")->phone("The number you have provided does not match a valid phone number format.");
		$phone_number['phone'] = $valid->getParsed(); //Get the phone number parsed in our format

		$confidence_level=$valid->getConfidenceLevel();

		if($confidence_level==1){
			$error=$valid->validate();
		}
		else{
			$error='';
		}

		if(!empty($error))
			$this->error[]=implode('',$error);

	}

	/**
	*This function gets contact validation
	*@return $error if validation returns false
	*/
	public function getContactCheck(){

		//validate all required fields
		$this->isEmptyCheck($this->contact_name,"Name");
		$this->isEmptyCheck($this->contact_email,"Email");
		$this->isEmptyCheck($this->contact_phone,"Phone Number");
		//check recaptcha entered correctly
		$this->reCaptchaCheck();

		//check for any random activity
		if($_POST['contact'] != ''){
   			$this->error[] =("An unknown error was encountered.");
   		}

		//If no error , send return mail
		if (empty($this->error)){

			//create an array of input values for contact form
			$contact_data=array(
				"name"=>$this->contact_name,
				"email"=>$this->contact_email,
				"company"=>$this->contact_company,
				"phone"=>$this->contact_phone,
				"department"=>$this->contact_department,
				"contact_me"=>$this->contact_method,
				"message"=>$this->message
				);


			$mail=new Email();

			//send email for contact form
			$msg=$mail->sendContactUs($contact_data);

			//check if any error occurs while sending mail
			if(!$msg){
				$this->error[] ='Your email cannot be sent at this time. Please <a href="' . $contact_page->getUrl() . '"">contact Customer Service for assistance</a>' ;
				return $this->error;
			}
		}//else return error
		else if(!(empty($this->error))){
			 return $this->error;
		}

	 }

	/**
	*This function checks for valid order number supplied
	*@param string $orderno
	*@return boolean true/false
	*/
	private function validOrderNo($orderno){

		$stmt_orderno=$this->dbh->prepare("SELECT count(order_no) AS order_no FROM bs_orders WHERE order_no=:orderno LIMIT 1");
		$stmt_orderno->execute(array(":orderno"=>$orderno));
		$row_orderno=$stmt_orderno->fetch(PDO::FETCH_ASSOC);

		return $row_orderno['order_no'];

	}

		/**
	*This function gets return validation
	*@return $error if validation returns false
	*/
	public function getReturnCheck(){

		//validate all required fields
		$this->isEmptyCheck($this->contact_name,"Name");
		$this->isEmptyCheck($this->contact_email,"Email");
		$this->isEmptyCheck($this->contact_phone,"Phone Number");
	 	$this->isEmptyCheck($this->order_number,"Order Number");

		//check recaptcha entered correctly
		$this->reCaptchaCheck();

		//check for any random activity
		if($_POST['return'] != ''){
			$this->error[]= ("An unknown error was encountered.");
		}

		//If no error , send return mail
		if (empty($this->error)){

			//create an array of input values for return form
			$return_data=array(
				"name"=>$this->contact_name,
				"email"=>$this->contact_email,
				"phone"=>$this->contact_phone,
				"company"=>$this->contact_company,
				"orderno"=>$this->order_number,
				"reason"=>$this->returnreason,
				"message"=>$this->message
				);

			$mail=new Email();
			//send email for contact form
			$return_value=$mail->sendReturnMsg($return_data);

			//check if any error occurs while sending mail
			if(!$return_value){
				$this->error[] ='Your email cannot be sent at this time. Please <a href="' . $contact_page->getUrl() . '"">contact Customer Service for assistance</a>' ;
				return $this->error;
			}

		}//else return error
		else if(!(empty($this->error))){
			return $this->error;
		}

	}

}