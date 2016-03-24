<?php


class User {

// PROPERTIES [[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]

	private $dbh;
 	var $username;
	var $userpassword;
	var $firstname;
	var $lastname;
	var $email;
	var $createpassword;
	var $confirmpassword;
	var $remember;
	var $guest;

	//Variables for administrators
	var $admin; //Whether a user is an admin or not
	var $adminAccount; //The admin account info
	var $adminID; //The id

	//Hold any errors
	var $errors = array();
	var $flags = array();



// STATIC METHODS  [[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]

	public static function getEmailAddressById($id) {

		if ( isset($id) ) {

			// Get the email address from the database.
			$query = Connection::getHandle()->prepare('SELECT `username` FROM `bs_customers` WHERE `customers_id` = :id LIMIT 1');
			$query->bindParam(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$emailAddress = $query->fetchColumn();

		}

		return ( isset($emailAddress) && !empty($emailAddress) ) ? $emailAddress : null;

	}


// METHODS  [[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]


	/**
	 * Constructor
	 */
	public function __construct() {

		//Establish a database connection
		$this->dbh = Connection::getHandle();

	}

	/**
	 * Allow error retrieval
	 *
	 * @return    array    An array of errors
	 */
	public function getErrors() {
		return $this->errors;
	}



	/**
	 * Gets the user's CID (taking into account if they are an admin)
	 */
	public function getCID() {
		if ($_SESSION['admin'] === true AND $_SESSION['adminID'] > 0) {
			return $_SESSION['adminID'];
		} else {
			return $_SESSION['CID'];
		}
	}



	/**
	 * Allow error flag retrieval. Error flags work in conjunction with errors to highlight missing
	 * or invalid signin/login form fields.
	 *
	 * flag numbers correspond to the order that input boxes appear on the login and signin pages
	 *
	 * @return    array    An array of error flags
	 */
	public function getFlags() {
		return $this->flags;
	}



	/**
	 * This function handles returning user logins
	 *
	 * If the fourth argument ($reset) = 1, the user will be showed a 'password successfully reset message'
	 * once logged in.
	 */
	public function userLogin($email, $pass1, $remember, $ObjSession = NULL) {

		global $ObjShoppingCart;

		//Grab all the form fields and set as class variables; We'll need these later.
		$this->username = $email;
		$this->userpassword = $pass1;
		$this->remember = $remember;

		//Throw an error if either field is empty
		if ($this->username == '') {

            $this->errors[] .= "You must enter an email address to sign in.<br />";
			$this->flags[4] = 1;

		} else if ($this->valid_email($this->username) == FALSE) {

            $this->errors[] .= "&ldquo;" . htmlspecialchars($this->username, ENT_QUOTES, 'UTF-8') . "&rdquo; is not a valid email address. Enter a valid email address to sign in.";
			$this->flags[4] = 1;
		}

		if ($this->userpassword == '') {

            $this->errors[] .= "You must enter your password to sign in.<br />";
			$this->flags[5] = 1;
		}

        if( !$ObjSession instanceof Session) {

            $ObjSession = new Session();
        }

		//If there were no errors so far, poll the database and see if they are a real user
		if (count($this->errors) == 0)  {

			$sql = Connection::getHandle()->prepare(
                        "SELECT
                            c.customers_id AS customers_id,
                            username AS username,
                            password AS password,
                            user_type AS user_type,
                            ci.billing_first_name AS firstname, ci.billing_last_name AS lastname
                        FROM bs_customers c
                        LEFT JOIN bs_customer_info ci ON (ci.customers_id = c.customers_id)
                        WHERE username = :username
                        AND password = PASSWORD(:password)
                        AND user_type != 'G'
                        AND status = 'Y' "
            );

			$sql->bindParam(":username", $this->username, PDO::PARAM_STR);
			$sql->bindParam(":password", $this->userpassword, PDO::PARAM_STR);

            if( $sql->execute() ) {

                $data = $sql->fetch(PDO::FETCH_ASSOC);
                //If a customer was returned
                if( $data['customers_id'] > 0 ) {

                    $CID = $data["customers_id"];

                    //Unset the entire session
                    $ObjSession->unsetSession();
                    $_SESSION['CID'] = $CID;

                    if ( !empty($data["firstname"]) && !empty($data["lastname"]) ) {
                        $Username = $data["firstname"]." ".$data["lastname"];
                    } else {
                        $Username = $data["username"];
                    }

                    $_SESSION['Username'] = $Username;
                    $_SESSION['Useremail'] = $data["username"];
                    $_SESSION['UserType'] = $data["user_type"];
                    $_SESSION['timeout'] = time();

                    // Make sure cart row associated with customer has customer_id
                    if( $ObjShoppingCart instanceof Cart ) {
                        $ObjShoppingCart->setCustomerId($_SESSION['CID']);
                    }
                    $this->UserLastLoginDate($CID);
                    $this->UserRememberMe();
                    $this->sendToTargetURL();

                }else{

                    $forgotpassword_page = new Page('forgotpassword');
                    $contact_page = new Page('contact-us');
                    $this->errors[] = "Your email address or password is incorrect. You may <a href='".$forgotpassword_page->getUrl(
                        )."'>reset your password</a> or <a href='".$contact_page->getUrl(
                        )."'>contact Customer Service for assistance</a>.";
                }
            }
		}
	}


	/**
	 * This function handles new user registration
	 */
	public function userRegister($email, $pass1, $pass2, $ObjEmail = NULL, $ObjSession = NULL) {

		global $ObjShoppingCart;

		//Make sure they submit the signin form
		if (isset($_POST['signinsubmit'])) {

			//Grab all the form fields and set as class variables; We'll need these later.
			$this->email = $email;
			$this->createpassword = $pass1;
			$this->confirmpassword = $pass2;

			//Email validation
			if ($this->email == '') {
				$this->errors[] .= "You must enter an email address to create an account.";
				$this->flags[1] = 1;
			} else if ($this->valid_email($this->email) == FALSE) {
				$this->errors[] .= "You must enter a valid email address to create an account.";
				$this->flags[1] = 1;
			}

			//Password validation
			if ($this->createpassword == '') {
				$this->errors[] .= "You must enter a password to create an account.";
				$this->flags[2] = 1;
			} else if (mb_strlen($this->createpassword) < 8) {
				$this->errors[] .= "Your password must be at least 8 characters long.";
				$this->flags[2] = 1;
			} else if ($this->createpassword!=$this->confirmpassword) {
				$this->errors[] .= "The passwords you entered do not match.";
				$this->flags[2] = 1;
			}

			//If there were no errors so far...
			if (count($this->errors) == 0)  {

				$sql = Connection::getHandle()->prepare(
                        "SELECT customers_id AS customers_id, username AS username FROM bs_customers
                         WHERE username = ? AND user_type != 'G' AND status = 'Y' ");

				$sql->execute(array($this->email));

                $data = $sql->fetch(PDO::FETCH_ASSOC);

				//If this account already exists, throw an error
				if($data["customers_id"]) {

					$forgotpassword_page = new Page('forgotpassword');
					$contact_page = new Page('contact-us');

					$this->errors[] .= "An account is already registered to &ldquo;" . htmlspecialchars($this->email, ENT_QUOTES, 'UTF-8') . "&rdquo;. You can sign in below, <a href='".$forgotpassword_page->getUrl()."'>reset your password</a>, or <a href='" . $contact_page->getUrl() . "'>contact Customer Service for assistance</a>.";
					$this->flags[1] = 1;

				} else {

					//try {

						$sql = Connection::getHandle()->prepare(
                                    "INSERT INTO bs_customers (username, password, status, user_type, ip, last_login_date, last_login_ip)
                                    VALUES (:username, PASSWORD(:password), 'Y', 'U', :ip, NOW(), :ip) ");

                        $sql->bindParam(":username", $this->email, PDO::PARAM_STR);
                        $sql->bindParam(":password", $this->createpassword, PDO::PARAM_STR);
                        $sql->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                        $sql->bindParam(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);

						$sql->execute();

                        $CID = $this->dbh->lastInsertId();

				//	} catch (PDOException $e) {

						$contact_page = new Page('contact-us');

						$this->errors[] = "There was an error creating your account. Please try again or <a href='" . $contact_page->getUrl() . "'>contact Customer Service for assistance</a>.";
				//	}


					//If there is a customer ID, update their history, set their session, and redirect them
					if (isset($CID) && $CID > 0) {

						//Send a registration email to the user
						$ObjEmail->setUsername($this->email);
						$ObjEmail->sendRegistration();

						//Update customers history in DB
						$sql = $this->dbh->prepare("INSERT INTO bs_customer_histories
																(customers_id, date_of_last_logon, number_of_logons,
																 date_account_created, date_account_last_modified)
													VALUES
																(?, NOW(), '1', NOW(), NOW()) ");
						$sql->execute(array($CID));

						//Unset the entire session
						$ObjSession->unsetSession();

						//Set all the session variables
						$_SESSION['Username'] = $this->email;
						$_SESSION['UserType'] = "U";
						$_SESSION['Useremail'] = $this->email;
						$_SESSION['CID'] = $CID;

						//Set a session notice so they know they are being logged into their new account
						$_SESSION['notices'][] = 'accountcreated';

						// Make sure cart row associated with customer has customer_id
						if($ObjShoppingCart instanceof Cart){
							$ObjShoppingCart->setCustomerId($_SESSION['CID']);
						}

						//Send them back from whence they came
						$this->sendToTargetURL();

					} else {

						$contact_page = new Page('contact-us');
						$this->errors[] = "There was an error creating your account. Please try again or <a href='" . $contact_page->getUrl() . "'>contact Customer Service for assistance</a>.";

					}

				}

			}

		}

	}



	/**
	 * This function signs a user in as a guest
	 */
	public function userGuest($email) {

		global $ObjSession, $ObjShoppingCart;

		//Make sure they submit the signin form
		if (isset($_POST['signinsubmit'])) {

			//Grab the form field and set as class variable; We'll need this later.
			$this->email = $email;

			//Validate email
			if ($this->email == '') {
				$this->errors[] = "You must enter an email address to check out.";
				$this->flags[6] = 1;
			} else if ($this->valid_email($this->email) == FALSE) {
				$this->errors[] = "&ldquo;" . htmlspecialchars($this->email, ENT_QUOTES, 'UTF-8') . "&rdquo; is not a valid email address. You must enter a valid email address to check out.";
				$this->flags[6] = 1;
			}

			//If there were no errors so far...
			if (count($this->errors) == 0)  {

				$guestID = $this->getGuestID();

				try {
					//Insert into customers
					$sql = $this->dbh->prepare("INSERT INTO bs_customers
															(username, password, status,
														     user_type, ip, last_login_date,
														     last_login_ip)
												VALUES
															(?, PASSWORD(?), 'Y', 'G', ?, NOW(), ?) ");
					$sql->execute(array($this->email, $guestID, $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR']));

					//Get the customer's id
					$CID = $this->dbh->lastInsertId();

				} catch (PDOException $e) {

					$contact_page = new Page('contact-us');

					$this->errors[] = "There was an error with your guest sign in. Please try again or <a href='" . $contact_page->getUrl() . "'>contact Customer Service for assistance</a>.";
				}

				//If we could create them, update their history, set their session, and redirect them
				if ($CID > 0) {

					$sql = $this->dbh->prepare("INSERT INTO bs_customer_histories
															(customers_id, date_of_last_logon, number_of_logons,
															 date_account_created, date_account_last_modified)
												VALUES
															(?, NOW(), '1', NOW(), NOW()) ");
					$sql->execute(array($CID));

					//Unset the entire session
					$ObjSession->unsetSession();

					//Set their session variables
					$_SESSION['Useremail']=$this->email;
					$_SESSION['CID']=$CID;
					$_SESSION['UserType']='G';

					//Give them a notice they are logged into a guest account
					$_SESSION['notices'][] = 'guestlogin';

					// Make sure cart row is associated with guest and has customer_id
					if($ObjShoppingCart instanceof Cart){
						$ObjShoppingCart->setCustomerId($_SESSION['CID']);
					}

					//Send them back from whence they came
					$this->sendToTargetURL();

				}

			}

		}

	}



	/**
	 * This function is for returning users. It sets cookies based on whether the user
	 * chooses 'remember me' or not
	 */
	function userRememberMe() {

		//Get a new expiration date
		$time_of_expiry = $this->getExpiryDate();
		$date_of_expiry = date('Y-m-d H:i:s', $time_of_expiry);

		//If the user wants to be remembered and already has a cookie
		if (isset($this->remember) && $this->remember=='Y' && isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != '') {

			//Set the domain that cookies are available to
			$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

			//Break the cookie apart
			$cookie = explode('|', $_COOKIE['credentials']);
			$series = $cookie[0];
			$token = $cookie[1];
			$cid = $cookie[2];

			//Get a hash to use as a new token
			$new_token = $this->getHash();

			//Update the cookie tokens to match
			setcookie("credentials", $series . "|" . $new_token . "|" . $cid, $time_of_expiry, '/', $domain, true, true);

			$sql = $this->dbh->prepare("UPDATE bs_cookies SET
											   token = ?
											   expiry_date = ?
										WHERE series = ?
										AND customer_id = ?");
			$sql->execute(array($new_token, $date_of_expiry, $series, $cid));


		//The user wants to be remembered but does not have a cookie
		} else if (isset($this->remember) && $this->remember=='Y') {

				//Get a new series identifier and token
				$series = $this->getHash();
				$token = $this->getHash();

				//Update the database entry to reflect the new series and token, as well as a 30 day expiration time
				$this->setCredentials($series, $token, $date_of_expiry);
				$value = $series . "|" . $token . "|" . $_SESSION['CID'];

				//Set the cookie for the user
				setcookie("credentials", $value, $time_of_expiry, '/', $domain, true, true);

		//The user does not want to be remembered. Destroy their cookie.
		} else if (isset($_COOKIE['credentials'])) {

			$this->cookieDestroy();

		}

	}



	/**
	 * Matches a user's cookie to bs_cookies and returns their username if a match is found.
	 * If there is indication of a cookie-jacking or if no match is found, their cookie and session are destroyed.
	 *
	 * This function is called on login and signin pages to autofill a returning customer's username.
	 *
	 * @param     string    $hash    a hash from a cookie
	 * @return    string             username
	 */
	public function getUsernameFromCookie($hash) {

		//Make sure the user has the cookie before we start polling the database
		if (isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != '') {

			//Break the cookie apart
			$cookie = explode('|', $hash);
			$series = $cookie[0];
			$token = $cookie[1];
			$cid = $cookie[2];

			//Set the domain that cookies are available to
			$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

			//Get a new expiration date
			$time_of_expiry = $this->getExpiryDate();
			$date_of_expiry = date('Y-m-d H:i:s', $time_of_expiry);

			//Fetch the database entry associated with the user's cookie
			$sql = $this->dbh->prepare("SELECT c.customer_id, c.token, c.expiry_date, b.username
										FROM bs_cookies c
										LEFT JOIN bs_customers b ON (b.customers_id = c.customer_id)
										WHERE c.series = ?
										AND b.customers_id = ?
										LIMIT 1");
			$sql->execute(array($series, $cid));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

			//Check if the tokens match. The else case would indicate a cookie-jacking
			if ($row['token'] == $token) {

				$now = date('Y-m-d H:i:s');

				if ($row['expiry_date'] > $now) {

					//Get a hash to use as a new token
					$new_token = $this->getHash();

					//Update the cookie tokens to match
					setcookie("credentials", $series . "|" . $new_token . "|" . $cid, $time_of_expiry, '/', $domain, true, true);

					$sql = $this->dbh->prepare("UPDATE bs_cookies SET token = ?, expiry_date = ?
												WHERE series = ?
												AND customer_id = ?");
					$sql->execute(array($series, $cid));

					//Return the username
					return $row['username'];

				} else {
					$this->cookieDestroy();
				}

			} else {
				$this->cookieDestroy();
			}

		}

	}



	/**
	 * Takes a user ID and returns a username
	 * @param int $id [The user's ID]
	 * @return        [The username, or false in the case of failure]
	 */
	private function getUsernameFromUserId($id) {
		if ($id > 0) {
			$sql = $this->dbh->prepare("SELECT username AS username FROM bs_customers WHERE customers_id = ?");
			$sql->execute(array($id));
			$row = $sql->fetch(PDO::FETCH_ASSOC);
			return $row['username'];
		} else {
			return false;
		}
	}



	/**
	 * This function takes a series, token, and expiry, and sets them as a users's cookie in bs_cookies
	 * @param    string    $series            A series identifier for the user cookie
	 * @param    string    $token             A unique token for the cookie
	 * @param    string    $date_of_expiry    The date the cookie expires
	 */
	private function setCredentials($series, $token, $date_of_expiry) {

		$sql = $this->dbh->prepare("INSERT INTO bs_cookies
												(customer_id, series, token, expiry_date)
									VALUES
												(:cid, :series, :token, :date_of_expiry)");

		$sql->execute(array(":cid"            => $_SESSION['CID'],
							":series"         => $series,
							":token"          => $token,
							":date_of_expiry" => $date_of_expiry));

		$test = array(":cid"            => $_SESSION['CID'],
							":series"         => $series,
							":token"          => $token,
							":date_of_expiry" => $date_of_expiry);

	}



	/**
	 * This function gets an expiration date for cookies. By default it is set to 30 days, but this value can easily be changed
	 * @return    string    An expiration date
	 */
	Private function getExpiryDate() {
		$number_of_days = 30; //Change me as needed

		$expiry_date = time() + 60 * 60 * 24 * $number_of_days;
		return $expiry_date;
	}



	/**
	 * This function destroys a cookie/DB pair
	 */
	public function cookieDestroy() {

		if (isset($_COOKIE['credentials'])) {

			//Break the cookie apart
			$cookie = explode('|', $_COOKIE['credentials']);
			$series = $cookie[0];
			$token = $cookie[1];
			$cid = $cookie[2];

			//Get the current cookie details, so we can keep the same expiration date
			$sql = $this->dbh->prepare("DELETE FROM bs_cookies WHERE series = ? AND customer_id = ? LIMIT 1");
			$sql->execute(array($series, $cid));

			setcookie("credentials", '', 1, true, true);
		}

	}



	/**
	 * Generates a random unique hash
	 * @return    string    A random unique hash
	 */
	private function getHash() {

		//Seed the random generator
		mt_srand($this->make_seed());

		//Alphanumeric upper/lower array
		$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
		$hash = "";

		//Loop through and generate the random hash
		for($i = 0; $i < 32; $i ++) {
		  $hash .= $alfa[mt_rand(0, mb_strlen($alfa)-1)];
		}

		//If there is a duplicate, run this function recursively
		if(!$this->checkHash($hash)) {
			$hash = $this->getHash();
		}

		//Return the hash
		return $hash;
	}



	/**
	 * Generates a unique seed that can be used for the random generator
	 * @return    float    Unique seed that can be used to seed the random generator
	 */
	private function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}



	/**
	 * Checks to see if a hash is already in use as a series or token, or if it's unique
	 * @param     string    $hash    A hash to check
	 * @return    bool               true if the hash is unique, false if it is in use.
	 */
	private function checkHash($hash) {

		$sql = $this->dbh->prepare("SELECT COUNT(*) AS count FROM bs_cookies WHERE series = ? OR token = ?");
		$sql->execute(array($hash, $hash));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		if ($row['count'] > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}



	/**
	 * Updates the user's last login date, IP address; increments the number of logins;
	 * Concatinates the user's first and last names and sets to session 'Username'
	 * @param    int    $CID    The customer's ID
	 */
	function UserLastLoginDate($CID) {

		//Set last logon time and IP address
		$sql = $this->dbh->prepare("UPDATE bs_customers SET last_login_date = NOW(), last_login_ip = ? WHERE customers_id = ?");
		$sql->execute(array($_SERVER['REMOTE_ADDR'], $CID));


		//Get the number of logons
		$sql = $this->dbh->prepare("SELECT number_of_logons AS number_of_logons
									FROM bs_customer_histories
									WHERE customers_id = ? ");
		$sql->execute(array($CID));
		$history = $sql->fetch(PDO::FETCH_ASSOC);
		$number_of_logons = $history['number_of_logons']+1;


		//Update the user history
		$sql = $this->dbh->prepare("UPDATE bs_customer_histories
									SET date_of_last_logon = NOW(),
									    number_of_logons = ?
									WHERE customers_id = ?");
		$sql->execute(array($number_of_logons, $CID));


		// Grab the user's first and last name.
		$sql = $this->dbh->prepare("SELECT billing_first_name AS billing_first_name,
										   billing_last_name AS billing_last_name
									FROM bs_customer_info
									WHERE customers_id = ?");
		$sql->execute(array($CID));
		$data = $sql->fetch(PDO::FETCH_ASSOC);
		$Username = $data['billing_first_name'] . " " . $data['billing_last_name'];

		//Set the session username.
		$_SESSION['Username'] = $Username;

	}



	/**
	 * This function sends a user back to the page they were on, or to the account page in some cases
	 * @param  [reset] [If reset is set to 1, a flag is passed to the target page which informs the user
	 *                  that their password has been reset]
	 * @return [type] [description]
	 */
	public function sendToTargetURL() {

		if ( isset($_SESSION['target']) && !empty($_SESSION['target']) ) {
			$target = $_SESSION['target'];
		} else {
			$defaultPage = new Page($_SESSION['UserType'] == 'G' ? 'checkout' : 'my-account'); // If this is a guest with no target, send them to checkout. Otherwise, send them to the account page.
			$target = $defaultPage->getUrl();
		}

		// Reset the target and timeout.
		unset($_SESSION['target']);
		$_SESSION['timeout'] = time();

		// Send the user to the target URL.
		header("Location: " . $target);
		die();

	}



	/**
	 * Grabs all info for the user from bs_customer_info
	 * @param    int    $CID    The customer's id
	 */
	function UserBillingInformation($CID=0) {

		if ($CID > 0) {

			$sql = Connection::getHandle()->prepare("SELECT * FROM bs_customer_info WHERE customers_id = ?");
			$sql->execute(array($cid));

		} else {

			$sql = Connection::getHandle()->prepare("SELECT * FROM bs_customer_info WHERE customers_id = ?");
			$sql->execute(array($_SESSION['CID']));
		}

		while($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			$info[] = $row;
		}

		return $info;
	}



	/**
	 * Returns true if an email is valid, false if not
	 * @param     String    $str    Email address to validate
	 * @return    Bool              True if email is valid, false if not
	 */
	function valid_email($str) {
		return (filter_var($str, FILTER_VALIDATE_EMAIL)) ? TRUE : FALSE;
	}



	/**
	 * Updates bs_customer_info
	 */
	function GetUsesUpdate() {

		if($_POST['shipping_submit']=='Update Exemption Status') {

			$sql = $this->dbh->prepare("UPDATE bs_customer_info SET tax_exempt = ?
										WHERE customers_id = ?");
			$sql->execute(array($_POST['tax_exempt_status'], $_SESSION['CID']));
		}

		if($_POST['purchase_submit']=='Update') {

			$sql = $this->dbh->prepare("UPDATE bs_customer_info SET
											   purchase_order = ?
											   tag_job = ?
										WHERE customers_id = ?");
			$sql->execute(array($_POST['purchase_order'], $_POST['tag_job'], $_SESSION['CID']));
		}

		if($_POST['billing_submit']=='Update Payment Information') {

			$sql = $this->dbh->prepare("UPDATE bs_customer_info SET tax_exempt = ?
										WHERE customer_id = ?");
			$sql->execute(array($_POST['tax_exempt_status'], $_SESSION['CID']));

		}
	}



	/**
	 * Updates bs_customer_info with comments, purchase order, tag job, and expedited shipping
	 * @param    int       $CID                   The customer's ID
	 * @param    string    $comments              The customer's comments
	 * @param    ?         $expeditedshipping    Expedited shipping option
	 */
	function GetOrderComments($CID,$comments,$expeditedshipping) {

		$sql = $this->dbh->prepare("UPDATE bs_customer_info SET
										   purchase_order = ?,
										   tag_job = ?,
										   expedited_shipping = ?,
										   comments = ?,
									WHERE customers_id = ?");
		$sql->execute(array($_REQUEST['purchase_order'],
							$_REQUEST['tag_job'],
							$_REQUEST['expedited-shipping'],
							$_REQUEST['special_comments'],
							$_SESSION['CID']));
	}


	private function trimAndCompressWhiteSpace($string) {
		return trim(preg_replace('/\s\s+/u', ' ', (string) $string));
	}



	/**
 	 * Get's a username from a CID
 	 * @param    int    $CID    Customer's ID
 	 */
	function GetCustomerById($CID) {
		if(isset($_SESSION['CID'])) {
			$sql = $this->dbh->prepare("SELECT username FROM bs_customers WHERE customers_id = ?");
			$sql->execute(array($_SESSION['CID']));

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$customer_data = $row['username'];
			}

			return $customer_data;
		}
	}



	function getCustomerTypeById($CID) {

		if ($CID > 0) {
			$sql = $this->dbh->prepare("SELECT user_type FROM bs_customers WHERE customers_id = ? LIMIT 1");
			$sql->execute(array($CID));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

			return $row['user_type'];
		}

	}



	/**
	 * Generates a random guest ID
	 * @return    string    A randomly generated guest ID
	 */
	function getGuestID() {
		$guestNo="";
		$random_number="";

		if (!$guestNo) {

			$length = 10;
			for ($i = 1; $i < $length; $i++)
			$guestNo .= mt_rand(3, 9);
			$guestNo = substr($guestNo,2,6);
			$length = 30;
		}

		if (!$random_number) {
			mt_srand ((double) microtime()*1000000);
			$random_number = mt_rand();
			$guestNo.=substr($random_number,0,3);
		}

		return "Guest".$guestNo;
	}



	/**
	 * Handles password changing from within the account page
	 * @param  [array]    $formdata    an array of form fields from the password change form
	 */
	public function changePassword($formdata) {

		//Make sure this is a user before continuing
		if ($_SESSION['CID'] > 0) {

			$valid = new Validate($formdata);
			$valid->name('createpassword')->required("To change your password, you must enter a new one.")->minLength(8, "Your password must be 8 or more characters.");
			$valid->name('confirmpassword')->required("You must retype your new password.")->match("createpassword", "Your passwords do not match. Please try again.");
			$valid->name('oldpassword')->required("You must confirm your old password in order to set a new one.");
			$validate = $valid->validate();

			//If our form validates correct
			if (empty($validate)) {

			 	//Grab the row that matches their CID/old password combination
				$sql = $this->dbh->prepare("SELECT * FROM bs_customers WHERE customers_id = ? AND `password` = PASSWORD(?)");
				$sql->execute(array($_SESSION['CID'], $formdata['oldpassword']));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				//If we got a row back, the customer has authorized themselves and we can reset their password
				if ($row['customers_id'] > 0) {

					$sql = $this->dbh->prepare("UPDATE bs_customers SET password = PASSWORD(?) WHERE customers_id = ?");
					$sql->execute(array($formdata['confirmpassword'], $_SESSION['CID']));
					$_SESSION['successes'][] = "Your password has successfully been changed.";

					//Redirect them
					$account=new Page("my-account");
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: ".$account->getUrl());
					exit;

				//We have no rows for that CID/old password combination. Assume they typed the old password wrong and throw an error
				} else {
					$_SESSION['errors'][] = "Your old password does not match the one we have on file for this account.";
					$account=new Page("my-account");

					//Redirect them
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: ".$account->getUrl());
					exit;
				}

			//If there were validation errors
			} else {

				foreach($validate as $key => $error) {
					if (!in_array($error, $_SESSION['errors'])) { $_SESSION['errors'][] = $error; }
				}

				$account=new Page("my-account");

				//Redirect them
				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header("Location: ".$account->getUrl());
				exit;
			}

		//Otherwise, they are not a user and we have an unknown error
		} else {
			$_SESSION['errors'][] = "Your password could not be changed; an unknown error was encountered.";
		}

		//Redirect them
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$_SERVER['REQUEST_URI']);
		exit;
	}



	public function updateUser($formdata) {


		global $ObjSession, $ObjEmail;
		//Make sure this is a user before continuing
		if ($formdata['cid'] > 0) {

			$valid = new Validate($formdata);
			$valid->name('createpassword')->required("To change your password, you must enter a new one.")->minLength(8, "Your password must be 8 or more characters.");
			$valid->name('confirmpassword')->required("You must retype your new password.")->match("createpassword", "Your passwords do not match. Please try again.");
			$validate = $valid->validate();

			//If our form validates correct
			if (empty($validate)) {

				$sql = $this->dbh->prepare("SELECT customers_id AS customers_id,
												   username AS username
											FROM bs_customers
											WHERE username = ?
											AND user_type != 'G'
											AND status = 'Y' ");
				$sql->execute(array($formdata['email']));
				$data = $sql->fetch(PDO::FETCH_ASSOC);

				//If this account already exists, throw an error
				if($data["customers_id"]) {

					$forgotpassword_page = new Page('forgotpassword');
					$contact_page = new Page('contact-us');

					$error= "An account is already registered to &ldquo;" . htmlspecialchars($formdata['email'], ENT_QUOTES, 'UTF-8') . "&rdquo;. You can sign in below, <a href='".$forgotpassword_page->getUrl()."'>reset your password</a>, or <a href='" . $contact_page->getUrl() . "'>contact Customer Service for assistance</a>.";
					$this->flags[1] = 1;
					$_SESSION['errors']['userupdate']=$error;
					$account=new Page("order-confirmation");
					header("Location:".$account->getUrl()."?orderno=".$formdata['orderno']);
					exit;
				}

				// Turn the guest account into a user account
				$sql = $this->dbh->prepare("UPDATE bs_customers SET password = PASSWORD(?) , user_type= ? WHERE customers_id = ?");
				$sql->execute(array($formdata['confirmpassword'],'U', $formdata['cid']));

				//Send a registration email to the user
				$ObjEmail->setUsername($formdata['email']);
				$ObjEmail->sendRegistration();
				$cid=$formdata['customers_id'];

				//Update customers history in DB
				$sql = $this->dbh->prepare("INSERT INTO bs_customer_histories
														(customers_id, date_of_last_logon, number_of_logons,
														 date_account_created, date_account_last_modified)
											VALUES
														(?, NOW(), '1', NOW(), NOW()) ");
				$sql->execute(array($formdata['cid']));

				//Unset the entire session
				$ObjSession->unsetSession();

				// Get the customers ID
				$sql = $this->dbh->prepare("SELECT
                                                c.customers_id AS customers_id,
                                                username AS username,
                                                password AS password,
                                                user_type AS user_type,
                                                ci.billing_first_name AS firstname, ci.billing_last_name AS lastname
                                            FROM bs_customers c
                                            LEFT JOIN bs_customers_info ci ON (ci.customers_id = c.customers_id)
                                            WHERE username = ?
                                            AND password = PASSWORD(?)
                                            AND user_type != 'G'
                                            AND status = 'Y' ");

				$sql->execute(array($formdata['email'], $formdata['confirmpassword']));
				$data = $sql->fetch(PDO::FETCH_ASSOC);

				//If a customer was returned
				if($data['customers_id'] > 0) {

					$CID = $data["customers_id"];

					//Unset the entire session
					$ObjSession->unsetSession();

					$_SESSION['CID'] = $CID;

                    if ( !empty($data["firstname"]) && !empty($data["lastname"]) ) {
                        $Username = $data["firstname"]." ".$data["lastname"];
                    } else {
                        $Username = $data["username"];
                    }

					$_SESSION['Username'] = $Username;
					$_SESSION['Useremail'] = $data["username"];
					$_SESSION['UserType'] = $data["user_type"];
					$_SESSION['timeout'] = time();

					$this->UserLastLoginDate($CID);
					$this->UserRememberMe();
					$myaccount=new Page('my-account');


					// Redirect to the account page
					header("Location:".$myaccount->getUrl());
					exit;

				}

			}
			//If there were validation errors
			else {

				foreach($validate as $key => $error) {
					$_SESSION['errors'][] = $error;
				}
			}

		//Otherwise, they are not a user and we have an unknown error
		} else {
			$_SESSION['errors'][] = "Your account could not be registered.";
		}
	}



	/**
	 * Handles username changing from within the account page
	 * @param  [array]    $formdata    an array of form fields from the username change form
	 */
	public function changeUsername($formdata) {

		//Make sure this is a user before continuing
		if ($_SESSION['CID'] > 0) {

			$valid = new Validate($formdata);
			$valid->name('createusername')->required("You must provide a new email address.")->email("You must enter a valid email address");
			$valid->name('confirmusername')->required("You must confirm your new email address.")->match("createusername", "Your email addresses do not match.");
			$validate = $valid->validate();

			//If our form validates correctly
			if (empty($validate)) {

				//Grab their current username and compare it to what they are trying to set it to
				$sql = $this->dbh->prepare("SELECT username AS username FROM bs_customers WHERE customers_id = ?");
				$sql->execute(array($_SESSION['CID']));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				//If they are trying to set their username to what it already is, throw an error
				if ($row['username'] == $formdata['confirmusername']) {
					$_SESSION['errors'][] = "You are attempting to change your email address, but it is already set to " . $row['username'];

				//Otherwise, everything is good so far and we just have to make sure the username they want isn't in use already
				} else {
					$sql = $this->dbh->prepare("SELECT COUNT(*) AS total FROM bs_customers WHERE username = ? AND user_type=? ");
					$sql->execute(array($formdata['confirmusername'],'U'));
					$row = $sql->fetch(PDO::FETCH_ASSOC);

					//As long as it's not already taken, update theirs and their session with their new username
					if ($row['total'] <= 0) {

						$sql = $this->dbh->prepare("UPDATE bs_customers SET username = ? WHERE customers_id = ?");
						$sql->execute(array($formdata['confirmusername'], $_SESSION['CID']));
						$_SESSION['Username'] = $formdata['confirmusername'];
						$_SESSION['Useremail'] = $formdata['confirmusername'];
						$_SESSION['successes'][] = "Your email address has been changed successfully.";

						//Redirect them
						$link = new Page('my-account');
						header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
						header("Location: ".$link->getUrl());
						exit;

					//The username was already in use. Throw an error.
					} else {
						$_SESSION['errors'][] = "That email address is currently in use. Please choose a different one.";
					}
				}

			//If there were validation errors, pass the first error out to be displayed
			} else {
				foreach($validate as $key => $error) {
					$_SESSION['errors'][] = $error;
				}
			}

		//Otherwise, they are not a user and we have an unknown error
		} else {
			$_SESSION['errors'][] = "Could not change your email address; an unknown error was encountered.";
		}

		$_SESSION['validate'] = $formdata;

		//Redirect them
		$link = new Page('my-account');
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$link->getUrl());
		exit;

	}




	/**
	 * Allows a user to update an address, or insert a new one depending on whether an address_id is provided
	 * @param  [type] $formdata    array of postdata
	 */
	public function linkNet30($formdata) {

		//Make sure this is a user before continuing
		if ($_SESSION['CID'] > 0) {

			//Instantiate our form validator, and validate the user input
			$valid = new Validate($formdata);
			$valid->name('account')->required("You must provide an account number.");
			$valid->name('code')->required("You must provide a security code.");
			$validate = $valid->validate();

			//If our form validates correctly so far
			if (empty($validate)) {

				$account = base64_encode($formdata['account']);
				$code = base64_encode($formdata['code']);

				$sql = $this->dbh->prepare("SELECT id FROM bs_customer_brimar_net WHERE account_no = ? AND security_code = ?");
				$sql->execute(array($account, $code));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				//If the net30 account matches, link them
				if ($row['id'] > 0) {

					//Check if the customer already has an account linked
					$sql2 = $this->dbh->prepare("SELECT id FROM bs_customer_wallet WHERE cid = ?");
					$sql2->execute(array($_SESSION['CID']));
					$row2 = $sql2->fetch(PDO::FETCH_ASSOC);

					//The customer had an account linked, update it to use the new one
					if ($row2['id'] > 0) {

						$sql3 = $this->dbh->prepare("UPDATE bs_customer_wallet SET netid = ? WHERE cid = ?");
						$sql3->execute(array($row['id'], $_SESSION['CID']));

					//The customer didn't have an account linked, insert the new link
					} else {

						$sql3 = $this->dbh->prepare("INSERT INTO bs_customer_wallet (cid, netid, linked_date) values (?, ?, NOW())");
						$sql3->execute(array($_SESSION['CID'], $row['id']));

					}

					$_SESSION['successes'][] = "Your Net30 account has been successfully linked.";

					//Redirect them
					$link = new Page('my-account');
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: ".$link->getUrl());
					exit;

				//The net30 account does not match
				} else {
					$_SESSION['errors'][] = "We could not find a Net30 account with those credentials.";
				}

			//If there were validation errors
			} else {

				//Pass the first validation error out to be displayed
				$_SESSION['errors'][] = $validate[0];
			}

		//Otherwise, they are not a user and we have an unknown error
		} else {
			$_SESSION['errors'][] = "Your Net30 account could not be linked; an unknown error was encountered.";
		}

		//Redirect them
		$link = new Page('my-account');
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$link->getUrl());
		exit;

	}



	/**
	 * returns the linked net30 account for the current customer, or NULL if none is linked
	 * @param  [int]  $CID  [The customer id]
	 * @return [array]      [Information about the net30 account]
	 */
	public function getLinkedNet30($CID) {

		//If this is a customer
		if ($CID > 0) {

			//Poll the DB for a linked net30
			$sql = $this->dbh->prepare("SELECT n.* FROM bs_customer_wallet w LEFT JOIN bs_customer_brimar_net n ON (n.id = w.netid) WHERE w.cid = ?");
			$sql->execute(array($CID));

			$row = $sql->fetch(PDO::FETCH_ASSOC);

			//If the row existed, return the net30 info
			if ($row['id'] > 0) {
				return $row;
			} else {
				return false;
			}

		} else {
			return false;
		}

	}



	/**
	 * Unlinks any net30 accounts tied to the current customer
	 * @return [type] [description]
	 */
	public function unlinkNet30() {

		//If this is a customer
		if ($_SESSION['CID'] > 0) {

			//Delete any links the customer has to a net30
			$sql = $this->dbh->prepare("DELETE FROM bs_customer_wallet WHERE cid = ?");
			$sql->execute(array($_SESSION['CID']));

			$_SESSION['successes'][] = "The Net30 account has been successfully unlinked.";

			//Redirect them
			$link = new Page('my-account');
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header("Location: ".$link->getUrl());
			exit;

		} else {
			$_SESSION['errors'][] = "Your Net30 account could not be unlinked; an unknown error was encountered.";
		}

		//Redirect them
		$link = new Page('my-account');
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$link->getUrl());
		exit;

	}




	/**
	 * Called from the forgot password page on form submission
	 * @param    string    $email     The email of the account to reset
	 */
	function forgotPassword($email, $ObjEmail = NULL) {

		//global $ObjEmail;

		//Validate
		if ($email == '') {

			$this->errors[] = "Please enter an email address.";

		} else {

			if($this->valid_email($email) == FALSE) {

				$contact_page = new Page('contact-us');
    			$this->errors[] = "Please enter a valid email address or <a href='" . $contact_page->getUrl() . "'>contact Customer Service</a> for further assistance.";

			} else {


				//Check to see if this user exists
				$sql = $this->dbh->prepare("SELECT customers_id
											FROM bs_customers
											WHERE username = ?
											AND user_type != 'G'
											AND status = 'Y'
											LIMIT 1");
				$sql->execute(array($email));
				$data = $sql->fetch(PDO::FETCH_ASSOC);

				if ($data["customers_id"]) {

					//Grab the customer's ID
					$CID = $data["customers_id"];

					//Generate a reset hash
					$hash = $this->generateHash(50);

					//Update the database
					$this->forgotPasswordUpdate($CID, $hash);

					//Send an email with the reset link
					$ObjEmail->setUsername($email);
					$ObjEmail->setHash($hash);
					$ObjEmail->sendForgotPassword();

					//Redirect them so they see the message that they have received an email
					$forgot_page = new Page('forgotpassword');

					header("Location: " . $forgot_page->getUrl() . "?action=update");
					die();

				} else {

					$login_page = new Page('login');
					$this->errors[] = "No account is registered to &ldquo;" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "&rdquo;. Try again or <a href='" . $login_page->getUrl() . "'>create a new account</a>.";

				}

			}

		}

		//Redirect them
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
		header("Location: ".$_SERVER['REQUEST_URI']);
		exit;

	}



	/**
	 * Validates a password reset hash, and returns true if valid, false if not
	 * @param  [string]  $hash [The hash to validate]
	 * @return [boolean]       [whether the hash if valid]
	 */
	public function validateHash($hash) {

		if (!empty($hash)) {
			$sql = $this->dbh->prepare("SELECT c.customers_id AS id FROM bs_customer_forgotpassword p
										LEFT JOIN bs_customers c on (c.customers_id = p.customer_id)
										WHERE BINARY p.hash = ?
										AND p.active = 1
										AND p.expiry >= NOW()
										LIMIT 1");
			$sql->execute(array($hash));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

			return ($row['id'] > 0 ? $row['id'] : FALSE);
		} else {
			return FALSE;
		}

	}



	/**
	 * Takes a password and a reset hash, validates, and then resets the user's password in the database
	 * @param  [string] $pass1 [The password]
	 * @param  [string] $pass2 [The password, repeated]
	 * @param  [string] $hash  [The user's password reset hash]
	 * @return [bool]          [true if successfully reset, otherwise false]
	 */
	public function resetPassword($pass1, $pass2, $hash) {

		//Grab the userid (will return false if they cannot be validated)
		$userid = $this->validateHash($hash);

		if ($userid > 0) {

			//Grab all the form fields and set as class variables; We'll need these later.
			$this->createpassword = $pass1;
			$this->confirmpassword = $pass2;

			//Password validation
			if ($this->createpassword == '') {
				$this->errors[] .= "You must enter a password to create an account.";
				$this->flags[1] = 1;
			} else if (mb_strlen($this->createpassword) < 8) {
				$this->errors[] .= "Your password must be at least 8 characters long.";
				$this->flags[1] = 1;
			} else if ($this->createpassword!=$this->confirmpassword) {
				$this->errors[] .= "The passwords you entered do not match.";
				$this->flags[1] = 1;
			}

			if (count($this->errors) == 0) {

				if ($userid > 0) {
					//Change the password
					$sql = $this->dbh->prepare("UPDATE bs_customers SET password = PASSWORD(?) WHERE customers_id = ?");
					$sql->execute(array($pass1, $userid));

					//Disable any hashes affiliated with this user
					$sql2 = $this->dbh->prepare("UPDATE bs_customer_forgotpassword SET active = 0 WHERE customer_id = ?");
					$sql2->execute(array($userid));

					//Throw a flag so the user has a notice as to what happened
					$_SESSION['notices'][] = 'passwordreset';

					//Log the user in with their new credentials
					$this->userLogin($this->getUsernameFromUserId($userid), $this->confirmpassword, 'N');
					return true;
				}
			}
		} else {
			$contact_page = new Page('contact-us');

			$this->errors[] .= "An error has occurred resetting your password. Please try again or <a href='" . $contact_page->getUrl() . "'>contact customer service for assistance.</a>";
		}
	}



	/**
	 * This function updates a user's password
	 * @param     int       $CID      The customer's ID
	 * @param     string    $hash 	  The reset hash
	 */
	private function forgotPasswordUpdate($CID, $hash) {

		//Disable any other reset hashes affiliated with this user before inserting the new one
		$sql = $this->dbh->prepare("UPDATE bs_customer_forgotpassword SET active = 0 WHERE customer_id = ?");
		$sql->execute(array($CID));


		//Insert the new hash into the DB
		$sql2 = $this->dbh->prepare("INSERT INTO bs_customer_forgotpassword
												(customer_id, hash, active, expiry)
									VALUES
												(:cid, :hash, 1, date_add(NOW(), INTERVAL 24 HOUR))");
		$sql2->execute(array(":cid" => $CID,
							":hash" => $hash));

	}



	/**
	 * Generates a random hash
	 * @param     int       $length     How many characters to make the hash
	 * @return    string                The generated hash
	 */
	function generateHash($length=20) {

		mt_srand ((double) microtime() * 10000000);

		$alfa = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$hash = "";
		for($i = 0; $i < $length; $i ++) {
			$hash .= $alfa[mt_rand(0, strlen($alfa))];
		}

		//Make sure this isn't a duplicate
		$sql = $this->dbh->prepare("SELECT COUNT(*) AS count FROM bs_customer_forgotpassword WHERE BINARY hash = ?");
		$sql->execute(array($hash));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		//If there was a duplicate, call this function recursively
		if ($row['count'] > 0) {
			$hash = $this->generateHash(50);
		}

		return $hash;
	}



	/**
	 * This function will set $_SESSION[admin] to true if it was not already, as long as a user really is an admin
	 * Note that the admin session variables keep track of the user the admin is loggin in as, NOT the admin themselves.
	 */
	function isAdmin() {

		//If we do not know if they are an admin, check and set the $_SESSION[admin] = true
		if (!isset($_SESSION['admin'])) {

			//Check if they are an admin
			$admin = $this->checkAdmin();

 			//Set the session admin var to true if they are
			if ($admin) {
				$_SESSION['admin'] = true;
			}
		}
	}



	/**
	 * If a user's session says they are an admin, this will double check and verify them for security
	 */
	function checkAdmin() {

		//Check if they are an admin
		$sql = $this->dbh->prepare("SELECT is_admin AS is_admin FROM bs_customers WHERE customers_id = ?");
		$sql->execute(array($_SESSION['CID']));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		//Return true or false based on DB data
		if ($row['is_admin'] == "Y") {
			return true;
		} else {
			return false;
		}
	}



	/**
	 * This function logs an admin in as an existing user
	 */
	function loginAs() {

		//If they are an admin, check if they want to log in as another user, and get that user's info
		if ($_SESSION['admin'] == true) {
			if (isset($_SESSION['adminID'])) {
				if ($this->checkAdmin()) {

					//Pull their user account
					$sql = $this->dbh->prepare("SELECT COUNT(*) AS count,
													   username AS username
												FROM bs_customers
												WHERE customers_id = ?");
					$sql->execute(array($_SESSION['adminID']));
					$row = $sql->fetch(PDO::FETCH_ASSOC);
					$count = $row['count'];


					//See if the user does exist
					if (!$count > 0) {
						return false;
					} else {
						$_SESSION['adminAccount'] = $row['username'];
						return true;
					}

				}
			}
		}
	}



	/**
	 * This function returns a CID from the database based off a username
	 * @param     string    $username    The username to look up
	 * @return    int                    The customers id
	 */
	function getIdByCustomer($username) {

		$sql = $this->dbh->prepare("SELECT customers_id AS customers_id FROM bs_customers WHERE username = ? AND user_type='U'");
		$sql->execute(array($username));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		//Return the id for that username
		return $row['customers_id'];
	}



	/**
	 * This function returns all valid, current coupons for administrators to select from
	 * @return    array    An array of all valid coupons
	 */
	public function getCoupons() {

		$sql = $this->dbh->prepare("SELECT coupon_number AS coupon_number,
										   coupon_value AS coupon_value,
										   percent_discount AS percent_discount,
										   order_discount AS order_discount,
										   message AS message
									FROM bs_coupons
									WHERE active = 'Y' AND (expiration_date is null OR expiration_date > CONVERT_TZ(NOW(), CONCAT(TIMESTAMPDIFF(HOUR, UTC_TIMESTAMP, NOW()), ':00'), '-10:00'))");
		$sql->execute();
		$i = 0;
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			switch($row['percent_discount']) {
				case 0:
					$description = " - Save " . $row['coupon_value'] . "%";
					break;
				case 1:
					$description = " - $" . $row['coupon_value'] . " off.";
					break;
				case 2:
					$description = " - Free shipping on orders over $" . $row['order_discount'];
					break;
			}

			$coupon[$i][0] = $row['coupon_number'];
			$coupon[$i][1] = $description;
			$i++;
		}

		return $coupon;
	}



	/**
	 * This function returns a list of possible referrers from bs_referrers
	 * @return    array    Array of possible referrers
	 */
	function getReferrers() {

		//Poll the DB for coupons
		$sql = $this->dbh->prepare("SELECT referrer AS referrer FROM bs_referrers");
		$sql->execute();

		//Loop through and return coupons
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$referrer[] = $row['referrer'];
		}

		return $referrer;
	}



	/**
	 * This function runs when an admin creates a guest account for checkout, and verifies the email address
	 */
	function adminGuest(){

		//Globals
		global $error_msg;

		$ObjShoppingCart = new Cart();

		if (isset($_POST['admincheckout-new-email'])) {

			$this->email = $_POST['admincheckout-new-email'];

			//Validate the email
			if ($this->email != "" AND $this->valid_email($this->email)==TRUE) {

				//Insert them into the database
				$CID=$this->adminGuestInsert($this->email);

				//Set some session variables with the new guest info
				$_SESSION['adminID'] = $CID;
				$_SESSION['adminAccount'] = $this->email;

				// Assign the appropriate customer id to the cart
				if($ObjShoppingCart instanceof Cart){
					$ObjShoppingCart->setCustomerId($_SESSION['adminID']);
				}

				$admin_guest_page=new Page('checkout');

				//Head them to checkout.php
				header("Location: " .$admin_guest_page->getUrl());
				die();
			}
		}
	}



	/**
	 * This function inserts a guest account into the DB
	 * @param     string    $email    Guest email address
	 * @return    int                 CID of the guest
	 */
	function adminGuestInsert($email) {

		//Get a random guest password
		$guestID=$this->getGuestID();

		//Insert the guest into the DB
		$sql = $this->dbh->prepare("INSERT INTO bs_customers
												(username, password, status, user_type, ip,
												 last_login_date, last_login_ip)
									VALUES
												(:username, PASSWORD(:password), 'Y', 'G',
												 :ip, NOW(), :ip) ");

		$sql->execute(array(":username" => $this->email,
							":password" => $guestID,
							":ip" => $_SERVER['REMOTE_ADDR']));
		$sql->execute();
		$CID = $this->dbh->lastInsertId();

		//Update their history
		$sql = $this->dbh->prepare("INSERT INTO bs_customer_histories
												(customers_id, date_of_last_logon, number_of_logons,
												 date_account_created, date_account_last_modified)
									VALUES
												(:cid, NOW(), '1', NOW(), NOW())");
		$sql->execute(array(":cid" => $CID));

		//Return
		return $CID;
	}



	/**
	 * This functions registers a new user for an admin to checkout as
	 * @return    bool    returns false if the user could not be registered
	 */
	function adminUserRegister() {

		//Globals
		global $error_msg;

		$ObjShoppingCart = new Cart();

		if (isset($_POST['admincheckout-new-email'])) {
			$this->email = $_POST['admincheckout-new-email'];

			//Validate the email
			if ($this->email != "" AND $this->valid_email($this->email)==TRUE) {

				//If the email is not already taken
				if (!$this->adminUserExist($this->email)) {

					//Insert them into the database
					$CID=$this->adminUserInsert($this->email);
					//Set some session variables with the new guest info
					$_SESSION['adminID'] = $CID;
					$_SESSION['adminAccount'] = $this->email;

					// Assign the appropriate customer id to the cart
					if($ObjShoppingCart instanceof Cart){
						$ObjShoppingCart->setCustomerId($_SESSION['adminID']);
					}

					$admin_user_page=new Page('checkout');

					//Head them to checkout.php
					header("Location: ".$admin_user_page->getUrl());
					die();
				} else {
					return false;
				}
			}
		}
	}



	/**
	 * This function makes sure a user does not already exist with those credentials
	 * @param     string    $email    Email address to check
	 * @return    bool                True if the user already exists, false if not
	 */
	function adminUserExist($email) {

		//Poll the DB for user
		$sql = $this->dbh->prepare("SELECT COUNT(*) AS count FROM bs_customers
									WHERE username = ?
									AND user_type = 'U'
									AND status = 'Y' ");
		$sql->execute(array($email));
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$count = $row['count'];

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}



	/**
	 * This function inserts a guest account into the DB
	 * @param     string    $email    Guest email to insert
	 * @return    int                 CID of the new guest account
	 */
	function adminUserInsert($email) {

		global $ObjEmail;

		//Get a random guest password
		$password=$this->generatePassword();

		//Send the user an email letting them know an admin created an account for them
		$ObjEmail->setUsername($email);
		$ObjEmail->setPassword($password);
		$ObjEmail->sendAdminRegistration();

		//Insert the guest into the DB
		$sql = $this->dbh->prepare("INSERT INTO bs_customers
												(username, password, status, user_type,
													last_login_date, last_login_ip)
									VALUES
												(:username, PASSWORD(:password),
												 'Y', 'U', NOW(), :ip) ");
		$sql->execute(array(":username" => $this->email,
							":password" => $password,
							":ip" => $_SERVER['REMOTE_ADDR']));
		$CID = $this->dbh->lastInsertId();


		//Update their history
		$sql = $this->dbh->prepare("INSERT INTO bs_customer_histories
												(customer_id, date_of_last_logon, number_of_logons,
												 date_account_created, date_account_last_modified)
									VALUES
												(?, NOW(), '1', NOW(), NOW()) ");
		$sql->execute(array($CID));


		//Return
		return $CID;
	}



	/**
	 * Generate a random password
	 * @return    string    The random password
	 */
	function generatePassword() {

		$random_pass_length = 8;
		$random_pass_chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789';
		$random_pass_end = mb_strlen($random_pass_chars) - 1;
		$random_pass = '';
		for ($i = 0; $i < $random_pass_length; $i++) { $random_pass .= $random_pass_chars[mt_rand(0, $random_pass_end)]; }

		return $random_pass;
	}



	/**
	 * After an admin is done, any user-specific session data is unset
	 */
	function clearAdmin() {
		unset($_SESSION['adminAccount']);
		unset($_SESSION['adminID']);
	}


	/**
	 * Gets a list of all ordered carts for the customer
	 * @param $cid
	 * @return array
	 */
	public function getOrdersByCid($cid) {

        $results = array();

		$sql = Connection::getHandle()->prepare(
                "SELECT c.*, o.*, SUM(cp.quantity) AS item_count, h.hash FROM bs_orders o LEFT JOIN
                 bs_carts c ON (c.id = o.cart_id) LEFT JOIN bs_cart_hashes h ON (h.id = c.hash_id)
                 LEFT JOIN bs_cart_skus cp ON cp.cart_id = c.id WHERE o.customers_id = ?
                 AND c.ordered = 1 AND c.saved = 0 GROUP BY o.orders_id ORDER BY c.modification_time DESC");

		$sql->execute(array($cid));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

            $results[] = $row;
		}

		return $results;

	}

    /**
     * Gets the number of orders made by the customer
     * @param int $cid
     * @return int $count
     */
    public static function getOrderCount($cid) {

        $dbh = Connection::getHandle();

        $sql = $dbh->prepare("SELECT COUNT(orders_id) FROM bs_orders WHERE customers_id = ?");

        $sql->execute(array($cid));

        $count = $sql->fetch(PDO::FETCH_ASSOC);

        return $count;

    }




    // Return information on saved carts of some customer
	public function getSavedCarts($cid) {

        $savedCarts = array();

		$sql = Connection::getHandle()->prepare(
                "SELECT c.id AS cart_id, cp.id AS cart_product_id, c.name AS cart_name, c.note AS cart_note,
                 c.modification_time AS modification_time, ch.hash as cart_hash, SUM(cp.quantity) AS quantity_sum
                 FROM bs_carts c LEFT JOIN bs_cart_hashes ch ON ch.id = c.hash_id LEFT JOIN bs_cart_skus cp ON cp.cart_id = c.id
                 WHERE c.customer_id = ? AND c.saved = 1 AND c.ordered = 0 GROUP BY c.id ORDER BY c.modification_time DESC");

		$sql->execute(array($cid));

		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$savedCarts[$row['cart_id']]['cart_hash'] = $row['cart_hash'];
			$savedCarts[$row['cart_id']]['cart_name'] = $row['cart_name'];
			$savedCarts[$row['cart_id']]['cart_note'] = $row['cart_note'];
			$savedCarts[$row['cart_id']]['modification_time'] = $row['modification_time'];
			$savedCarts[$row['cart_id']]['quantity_sum'] = $row['quantity_sum'];
		}

		return $savedCarts;
	}



	//return count of all saved carts that pertain to customer $cid.
	public function getSavedCartsCount($cid) {

		$dbh = Connection::getHandle();

		$sql = $dbh->prepare("SELECT count(*) as saved_carts FROM bs_carts
							  WHERE customer_id = ?
							  	AND saved = 1");

		$sql->execute(array($cid));

		return $sql->fetchColumn();
	}
}
