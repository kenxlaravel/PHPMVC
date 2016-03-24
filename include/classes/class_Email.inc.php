<?php

/**
 * Class Email
 */
class Email {

	//Properties
	/**
	 * @var
     */
	private $mail;
	/**
	 * @var
     */
	private $username;

	/**
	 * @var
     */
	private $hash;
	/**
	 * @var
     */
	private $password;
	/**
	 * @var
     */
	private $dbh;


	/**
	 *
     */
	public function __construct() {

		//Set a class var with the mailer
		$this->setMailer();
		$this->setDatabase();
	}

	//Makes sure we have an instance of our mailer as a class var
	/**
	 *
     */
	private function setMailer() {

		if (!($this->mail instanceof Mandrill)) {
			$this->mail = new Mandrill(MANDRILL_API_KEY);
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

	//Setters
	/**
	 * @param $username
     */
	public function setUsername($username) { $this->username = $username; }

	/**
	 * @param $hash
     */
	public function setHash($hash) { $this->hash = $hash; }

	/**
	 * @param $password
     */
	public function setPassword($password) { $this->password = $password; }

	//composes the email
	/**
	 * @param $recipients
	 * @param $sender
	 * @param $html_body
	 * @param $txt_body
	 * @param $subject
	 * @param array $tags
	 * @param null $campaign
	 * @param array $headers
	 * @param null $bccAddress
     * @return array
     */
	function composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags=array('safetysign'), $campaign=NULL, $headers=array(), $bccAddress=NULL){
		$message = array(
			'html' => $html_body,
			'text' => $txt_body,
			'subject' => $subject,
			'from_email' => $sender['email'],
			'from_name' => $sender['name'],
			'to' => $recipients,
			'headers' => $headers,
			'track_opens' => true,
			'track_clicks' => true,
			'inline_css' => true,
			'url_strip_qs' => true,
			'bcc_address' => $bccAddress,
			'tracking_domain' => 'track.safetysign.com',
			'signing_domain' => null,
			'return_path_domain' => 'mailer.safetysign.com',
			'tags' => $tags,
			'subaccount' => 'safetysign',
			'google_analytics_domains' => array('safetysign.com'),
			'subaccount' => 'safetysign',
			'google_analytics_domains' => array(WEBSITE_DOMAIN),
			'google_analytics_campaign' => $campaign,

		);
		return $message;
	}

/************** Recipient array for following emails are to be indexed from zero **************/

	/**
	 * Sends a registration email to a new user
	 * @return    bool    [Whether or not the email was successful]
	 */
	public function sendRegistration() {

		$vars = array(
			'homelink' => URL_PREFIX_HTTP,
			'username' => $this->username
		);

		//To, from
		$recipients[0]['email'] = $this->username;
		$sender['email'] = EMAIL_ACCOUNTS;
		$sender['name'] = EMAIL_ACCOUNTS_NAME;

		//Subject line
		$subject = 'Welcome to SafetySign.com!';

		//Prepare email
		$html_template_file = 'registration.html.php';
		$txt_template_file = 'registration.txt.php';

		$tags = array('safetysign','registration');
		$campaign = 'registration';

		$html_body = $this->getContent($html_template_file, $vars);
		$txt_body = $this->getContent($txt_template_file, $vars);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	 * Emails a user a link to reset their password
	 * @return    bool    [Whether or not the email was successful]
	 */
	public function sendForgotPassword() {

		//Make sure we have values for all the needed class properties
		if (!empty($this->hash) &&
			!empty($this->username)) {

			$vars = array(
				'hash' => $this->hash
			);

		//To, from
		$recipients[0]['email'] = $this->username;
		$sender['email'] = EMAIL_ACCOUNTS;
		$sender['name'] = EMAIL_ACCOUNTS_NAME;

		//Subject line
		$subject = 'Your SafetySign.com Password';

		//Prepare email
		$html_template_file = 'forgotpassword.html.php';
		$txt_template_file = 'forgotpassword.txt.php';

		$tags = array('safetysign','passwordReset');
		$campaign = 'passwordReset';
		$html_body = $this->getContent($html_template_file, $vars);
		$txt_body = $this->getContent($txt_template_file, $vars);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

		} else {
			return false;
		}

	}



	/**
	 * This function emails new users with account and password information when an admin registers them
	 * @return    bool    [Whether or not the email was successful]
	 */
	function sendAdminRegistration() {

		if (!empty($this->username) &&
			!empty($this->password)) {

			$vars = array(
				'username' => $this->username,
				'password' => $this->password
			);

		// To, from
		$recipients[0]['email'] = $this->username;
		$sender['email'] = EMAIL_ACCOUNTS;
		$sender['name'] = EMAIL_ACCOUNTS_NAME;

		//Subject line
		$subject = 'Your SafetySign.com Password';

		//Prepare email
		$html_template_file = 'adminregistration.html.php';
		$txt_template_file = 'adminregistration.txt.php';

		$tags = array('safetysign','adminRegistration');
		$campaign = 'adminRegistration';
		$html_body = $this->getContent($html_template_file, $vars);
		$txt_body = $this->getContent($txt_template_file, $vars);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

		} else {
			return false;
		}

	}



	/**
	*This function creates & send actual contact msg
	* @param array contact form data
	* @return    bool    [Whether or not the email was successful]
	*/
	public function sendContactUs($contact) {

		switch ($contact['department']) {

		case 'sales':             $department_email = EMAIL_SALES;    $email_name = EMAIL_SALES_NAME;    $department = 'Web Sales'; break;
		case 'customer_service':  $department_email = EMAIL_SERVICE;  $email_name = EMAIL_SERVICE_NAME;  $department = 'Customer Service'; break;
		case 'billing':           $department_email = EMAIL_BILLING;  $email_name = EMAIL_BILLING_NAME;  $department = 'Billing and Credit'; break;

		default:                  $department_email = EMAIL_SERVICE;  $email_name = EMAIL_SERVICE_NAME;  $department = 'Customer Service';

		}

		$vars = array(
		'department' => $department,
		'name' => $contact['name'],
		'email' => $contact['email'],
		'comment' => $contact['message'],
		'company' => $contact['company'],
		'phone' => $contact['phone'],
		'contact_me' => $contact['contact_me']
		);

		// To, Bcc, Cc's, and from
		$recipients[0]['email'] = $department_email;
		$recipients[0]['name'] = $email_name;

		$recipients[1]['email'] = EMAIL_SERVICEMANAGER;
		$recipients[1]['name'] = EMAIL_SERVICEMANAGER_NAME;
		$recipients[1]['type'] = 'bcc';

		$recipients[2]['email'] = EMAIL_MANAGER;
		$recipients[2]['name'] = EMAIL_MANAGER_NAME;
		$recipients[2]['type'] = 'cc';

		$recipients[3]['email'] = EMAIL_WEBMASTER;
		$recipients[3]['name'] = EMAIL_WEBMASTER_NAME;
		$recipients[3]['type'] = 'cc';

		$recipients[4]['email'] = EMAIL_MERCHANDISING;
		$recipients[4]['name'] = EMAIL_MERCHANDISING_NAME;
		$recipients[4]['type'] = 'cc';

        $recipients[5]['email'] = EMAIL_SALESMANAGER;
        $recipients[5]['name'] = EMAIL_SALESMANAGER_NAME;
        $recipients[5]['type'] = 'cc';

		$headers['Reply-To'] = $contact['name'].' <'.$contact['email'].'>';

		$sender['email'] = EMAIL_CONTACTUS;
		$sender['name'] = EMAIL_CONTACTUS_NAME;

		//Subject line
		$subject = 'SafetySign.com Contact Us Form';

		//Prepare email
		$html_template_file = 'contact.html.php';
		$txt_template_file = 'contact.txt.php';

		$tags = array('safetysign','contactUs');
		$campaign = 'contactUs';
		$html_body = $this->getContent($html_template_file, $vars);
		$txt_body = $this->getContent($txt_template_file, $vars);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign, $headers);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	*This function creates & send actual return msg
	* @param array return form data
	* @return    bool    [Whether or not the email was successful]
	*/
	public function sendReturnMsg($return_data) {

		$vars = array(
		'name' => $return_data['name'],
		'email' => $return_data['email'],
		'company' => $return_data['company'],
		'phone' => $return_data['phone'],
		'orderno' => $return_data['orderno'],
		'comments' => $return_data['message'],
		'reason' => $return_data['reason']
		);

		// To, reply-to, from, CC, BCC
		$recipients[0]['email'] = EMAIL_SERVICE;
		$recipients[0]['name'] = EMAIL_SERVICE_NAME;

		$recipients[1]['email'] = EMAIL_MANAGER;
		$recipients[1]['name'] = EMAIL_MANAGER_NAME;
		$recipients[1]['type'] = 'cc';

        $recipients[2]['email'] = EMAIL_SALESMANAGER;
        $recipients[2]['name'] = EMAIL_SALESMANAGER_NAME;
        $recipients[2]['type'] = 'cc';

		$headers['Reply-To'] = $return_data['name'].' <'.$return_data['email'].'>';

		$sender['email'] = EMAIL_RETURNS;
		$sender['name'] = EMAIL_RETURNS_NAME;

		//Subject line
		$subject = "SafetySign.com Return Form";

		//Prepare email
		$html_template_file = 'return.html.php';
		$txt_template_file = 'return.txt.php';

		$tags = array('safetysign','returnMsg');
		$campaign = 'returnMsg';
		$html_body = $this->getContent($html_template_file, $vars);
		$txt_body = $this->getContent($txt_template_file, $vars);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign, $headers);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	* This function sends Order tracking email to customer with all details of order
	* @param string $orderno
	*/
	public function sendOrderTrackingEmail($order_content,$customer_email) {

		//To, from
		$recipients[0]['email'] = $order_content['customer_email'];
		$recipients[0]['name'] = $order_content['name'];

		$sender['email'] = EMAIL_ORDERS;
		$sender['name'] = EMAIL_ORDERS_NAME;

		//Subject line
		$subject = "Your SafetySign.com Order Has Shipped (".$order_content['orderno'].")";

		//Prepare email
		$html_template_file = 'ordertracking.html.php';
		$txt_template_file = 'ordertracking.txt.php';

		$tags = array('safetysign','orderTracking');
		$campaign = 'orderTracking';
		$html_body = $this->getContent($html_template_file, $order_content);
		$txt_body = $this->getContent($txt_template_file, $order_content);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);
	}



	/**
	* This function sends Order tracking email to customer with all details of order
	* @param string $orderno
	*/
	public function sendOrderConfirmationEmail($order_content) {

		// To, from
		$recipients[0]['email'] = $order_content['customer_email'];
		$recipients[0]['name'] = $order_content['name'];

		$sender['email'] = EMAIL_ORDERS;
		$sender['name'] = EMAIL_ORDERS_NAME;

		//Subject line
		$subject = "Your SafetySign.com Order (".$order_content['orderno'].")";

		//Prepare email
		$html_template_file = 'orderconfirmation.html.php';
		$txt_template_file = 'orderconfirmation.txt.php';

		$tags = array('safetysign','orderConfirmation');
		$campaign = 'orderConfirmation';
		$html_body = $this->getContent($html_template_file, $order_content);
		$txt_body = $this->getContent($txt_template_file, $order_content);

		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	* This function sends email for customer pickup
	* @param string $orderno
	*/
	public function sendPickUpEmail($data) {

		//To, From
		$recipients[0]['email'] = $data['customer'];
		$recipients[0]['name'] = $data['name'];

		$sender['email'] = EMAIL_ORDERS;
		$sender['name'] = EMAIL_ORDERS_NAME;

		//Subject line
		$subject = "Your SafetySign.com Order Is Ready For Pickup (".$data['orderno'].")";

		//Prepare email
		$html_template_file = 'customerpickup.html.php';
		$txt_template_file = 'customerpickup.txt.php';

		$tags = array('safetysign','pickUp');
		$campaign = 'pickUp';
		$html_body = $this->getContent($html_template_file, $data);
		$txt_body = $this->getContent($txt_template_file, $data);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	* This function sends reminder email for customner pickup
	*@param array 	$data
	*/
	public function sendPickupReminderEmail($data) {

		// To, from
		$recipients[0]['email'] = $data['customer'];
		$recipients[0]['name'] = $data['name'];

		$sender['email'] = EMAIL_ORDERS;
		$sender['name'] = EMAIL_ORDERS_NAME;

		//Subject line
		$subject = "Reminder: Your SafetySign.com Order Is Ready For Pickup (".$data['orderno'].")";

		//Prepare email
		$html_template_file = 'customerpickupreminder.html.php';
		$txt_template_file = 'customerpickupreminder.txt.php';

		$tags = array('safetysign','pickUpReminder');
		$campaign = 'pickUpReminder';
		$html_body = $this->getContent($html_template_file, $data);
		$txt_body = $this->getContent($txt_template_file, $data);
		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

	}



	/**
	* This function sends saved cart details
	*@param array $data
	*/
	public function sendSavedCartEmail($to, $from, $message, $self, $data) {

		// To, from
		$recipients[0]['email'] = $to;

		// If the customer wants a copy sent to themselves, include that, too
		if ($self) {
			$recipients[1]['email'] = $from;
		}

		$sender['email'] = EMAIL_NOREPLY;
		$sender['name'] = (!empty($data['customerName']) ? $data['customerName'] : $from);

		//Subject line
		$subject = (!empty($data['customerName']) ? $data['customerName'] : $from) . " wants you to see " . (count($data['products']) > 1 ? "these items" : "this item") . " from SafetySign.com";

		//Prepare email
		$html_template_file = 'savedcart.html.php';
		$txt_template_file = 'savedcart.txt.php';

		$tags = array('safetysign','savedCart');
		$campaign = 'savedCart';
		$html_body = $this->getContent($html_template_file, $data);
		$txt_body = $this->getContent($txt_template_file, $data);

		$message = $this->composeMessage($recipients, $sender, $html_body, $txt_body, $subject, $tags, $campaign);

		return (bool) $this->mail->messages->send($message);

	}


	/**
	 * @param $_template
	 * @param $_vars
	 * @return string
     */
	private function getContent($_template, $_vars) {

		if (isset($_template)) {

			// Set variables
			if (!empty($_vars)) {
				extract($_vars);
			}

			// Generate the email and return it to a string.
			ob_start();			include APP_ROOT . "/templates/emails/" . $_template;
			$_content = ob_get_contents();
			ob_end_clean();

		}

		return $_content;

	}


	/**
	 * @return bool
     */
	public function getCreditCardAPIAlert(){

		$michael_number = "5514861601@txt.att.net";
		$mery_number = "2012489138@vtext.com";
		$jason_number = "9737694085@txt.att.net";

		//To, From
		$recipients[0]['email'] = $michael_number;

		$recipients[1]['email'] = $mery_number;
		$recipients[1]['type'] = 'cc';

		$recipients[2]['email'] = $jason_number;
		$recipients[2]['type'] = 'cc';

		$sender['email'] = EMAIL_WEBMASTER;
		$sender['name'] = EMAIL_WEBMASTER_NAME;

		//Subject line
		$subject = "Safetysign.com Alert";

		//Prepare email
		$txt_body = "Credit Card API is down";
		$tags = array('safetysign','CCapiAlert');
		$campaign = 'CCapiAlert';
		$message = $this->composeMessage($recipients, $sender, NULL, $txt_body, $subject, $tags);

		return (bool) $this->mail->messages->send($message);

	}

	// Email that may be sent when a cURL SSL error occurs
	/**
	 * @param $bs_config_setting
	 * @param $curlErrNo
	 * @return bool
     */
	public function sendSslCertFail($bs_config_setting, $curlErrNo){

		//To, From
		$recipients[0]['email'] = EMAIL_WEBMASTER;
		$recipients[0]['name'] = EMAIL_WEBMASTER_NAME;

		$sender['email'] = EMAIL_NOREPLY;
		$sender['name']  = EMAIL_NOREPLY_NAME;

		//Subject line
		$subject = 'Authorize.Net SSL Certificate Not Valid!';

		$tags = array('safetysign', 'AuthorizenetSslFail');

		$txt_body = 'The Authorize.Net certificate verification failed with cURL Error #: '.$curlErrNo.'. A fallback to the no-SSL cURL transaction just occurred.
Look into the issue and change bs_config.value to 0 for the '.$bs_config_setting.' row if we should continue
to skip SSL verification for future transactions temporarily.';
		$message = $this->composeMessage($recipients, $sender, NULL, $txt_body, $subject, $tags);

		return (bool) $this->mail->messages->send($message);

	}

}
