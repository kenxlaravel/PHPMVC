<?php

class Session {

	private static $commonReferrerDomains = array(

		array(
			'domains' => array('google', 'doubleclick', 'googleadservices', 'googleusercontent'),
			'source' => 'google',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('aol'),
			'source' => 'google',
			'keywordParameters' => array('query', 'encquery', 'q')
		),
		array(
			'domains' => array('yahoo'),
			'source' => 'yahoo',
			'keywordParameters' => array('p')
		),
		array(
			'domains' => array('altavista'),
			'source' => 'yahoo',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('bing'),
			'source' => 'bing',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('msn'),
			'source' => 'bing',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('lycos'),
			'source' => 'other',
			'keywordParameters' => array('query')
		),
		array(
			'domains' => array('ask'),
			'source' => 'other',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('netscape'),
			'source' => 'other',
			'keywordParameters' => array('q')
		),
		array(
			'domains' => array('streamsend', 'campaign-archive1'),
			'source' => 'email',
			'keywordParameters' => array()
		)
	);

	private $updateOnDestruct;

	/**
	 * The constructor function sets a property to trigger or suppress the database sync
	 * operation that can occur in the destructor.
     *
     * @param int|bool $updateOnDestruct
	 */
	public function __construct($updateOnDestruct = FALSE) {

		$this->updateOnDestruct = $updateOnDestruct ? TRUE : FALSE;
	}

	/**
	 * The destructor function helps to keep the database in sync with the session by calling
	 * updateDatabase() whenever a BS_Session instance is destroyed (including during the shutdown sequence).
	 */
	public function __destruct() {

		if ( $this->updateOnDestruct ) {

			$this->updateDatabase();
		}
	}

	/**
	 * This function is called from bs_common and updates a user's session timeout. It also kicks
	 * timed out or not logged in users to login or signin if they attempt to access a page that
	 * requires it
     *
     * @param Page()
	 */
	public function heartbeat($page) {

		// Check if the user is logged in and if his/her credentials are still current.
		$loggedIn = isset($_SESSION['CID']) && $_SESSION['CID'] > 0 ? TRUE : FALSE;

        if( !isset($_SESSION['timeout']) ) {

            $_SESSION['timeout'] = 0;
        }

		$active = (time() - $_SESSION['timeout']) < Settings::getSettingValue('sessiontimeout') ? TRUE : FALSE;

        if( !isset($_SERVER['HTTPS']) ) {

            $_SERVER['HTTPS'] = NULL;
        }

        $currentUrl = ( $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ) . $_SERVER['SERVER_NAME'] . ( $_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '' ) . $_SERVER['REQUEST_URI'];

		// Check to see if this is a defined page.
		if ( $page instanceof Page && $page->getAllowTarget() ) {

			// Parse the user agent string to get the browser and operating system;
			$br = new Browser();
			$br->browser();

			if ( !isset($_SESSION['referrerTracked']) || !$_SESSION['referrerTracked'] ) {

				// Parse the referrer URL to get the source and keyword.
				$parsedReferrerUrl = self::parseReferrerUrl($_SERVER['HTTP_REFERER']);

				// Update their session.
				$_SESSION['Referrer']         = $_SERVER['HTTP_REFERER'];
				$_SESSION['refpage']          = $_SERVER['SERVER_NAME'] . $_SERVER['QUERY_STRING'];
				$_SESSION['search_engine']    = $parsedReferrerUrl['source'];
				$_SESSION['keyword']          = $parsedReferrerUrl['keyword'];
				$_SESSION['referrerTracked']  = TRUE;
			}

			// Update their session.
			$_SESSION['ip_address']       = $_SERVER['REMOTE_ADDR'];
			$_SESSION['browser']          = $br->Name . ' ' . $br->Version;
			$_SESSION['os']               = $br->Platform;
			$_SESSION['target']           = $currentUrl;
			$_SESSION['current_url']      = $currentUrl;
			$_SESSION['current_url_time'] = date('Y-m-d') . ' ' . (date('H') + 1) . date(':i:s');

			// Sync the session changes to the database.
			self::updateDatabase();
		}

		// If the user is active, update their session.
		if ( $active ) {

			$_SESSION['timeout'] = time();
		}

		// Check if the user is allowed on this page.
		if ( $page instanceof Page && $page->getRequiresLogin() && (!$loggedIn || !$active) ) {

			// If the user timed out, this will cause a flag to be thrown alerting the user they have been timed out.
			if ( $loggedIn ) {
				$_SESSION['notices'][] = 'timeouterror';
			}

			// Unset all session variables.
			$this->unsetSession();

			// Parse the URL.
			$parsedUrl = parse_url($currentUrl);

			// Send them to login and if this is the checkout or admin_checkout page, throw the flag that allows guest access.
			header('Location: ' . Page::getPageUrlFromNickname('sign-in') . ($parsedUrl['path'] == '/admin_checkout' || $parsedUrl['path'] == '/checkout' ? '?checkout' : ''));
			die();

		}

		// Check to see if the user is a guest on a page that does not allow guests.
		if ( $page instanceof Page && $page->getDisallowGuests() && ($_SESSION['UserType'] == 'G' || $_SESSION['UserType'] == '') ) {
			header('Location: ' . URL_PREFIX_HTTP);
			die();
		}
	}

	public function getSession() {

		return array(
			'session_id'            => session_id(),
			'invoice_total'         => $_SESSION['invoice_total'],
			'expiry'                => $_SESSION['expiry'],
			'location'              => $_SESSION['location'],
			'ip_address'            => $_SESSION['ip_address'],
			'Referrer'              => $_SESSION['Referrer'],
			'refpage'               => $_SESSION['refpage'],
			'browser'               => $_SESSION['browser'],
			'os'                    => $_SESSION['os'],
			'shipping_carrier'      => $_SESSION['shipping_carrier'],
			'shipping_services'     => $_SESSION['shipping_services'],
			'shipping_charges'      => $_SESSION['shipping_charges'],
			'tax_exempt'            => $_SESSION['tax_exempt'],
			'sales_tax'             => $_SESSION['sales_tax'],
			'zipcode'               => $_SESSION['zipcode'],
			'shipping_carrier_pre'  => $_SESSION['shipping_carrier_pre'],
			'shipping_services_pre' => $_SESSION['shipping_services_pre'],
			'shipping_charges_pre'  => $_SESSION['shipping_charges_pre'],
			'shipping_arrival_date' => $_SESSION['shipping_arrival_date'],
			'shipping_zipcode'      => $_SESSION['shipping_zipcode'],
			'coupon_number'         => $_SESSION['coupon_number'],
			'coupon_value'          => $_SESSION['coupon_value'],
			'search_engine'         => $_SESSION['search_engine'],
			'keyword'               => $_SESSION['keyword'],
			'session_date'          => $_SESSION['session_date'],
			'current_url'           => $_SESSION['current_url'],
			'current_file'          => $_SESSION['current_file'],
			'current_url_time'      => $_SESSION['current_url_time'],
			'cart_hash'             => $_SESSION['cartHash']
		);
	}

	/**
	 * This function unsets all session variables except timeouterror and target. It does not destroy the
	 * session, as this would erase a customer's shopping cart as well.
	 */
	public function unsetSession() {

		// General session stuff
		unset($_SESSION['timeout']);

		// Account stuff
		unset($_SESSION['Username']);
		unset($_SESSION['Useremail']);
		unset($_SESSION['CID']);
		unset($_SESSION['UserType']);
		unset($_SESSION['admin']);
		unset($_SESSION['adminAccount']);
		unset($_SESSION['adminID']);

		// Checkout stuff
		unset($_SESSION['zipcode']);
		unset($_SESSION['shipping_services']);
		unset($_SESSION['tax_exempt']);
		unset($_SESSION['shipping_charges']);
		unset($_SESSION['shipping_carrier']);
		unset($_SESSION['shipping_arrival_date']);
		unset($_SESSION['checkout_form']);
		unset($_SESSION['coupon']);
		unset($_SESSION['status']);
		unset($_SESSION['sales_tax']);
		unset($_SESSION['coupon_value']);
		unset($_SESSION['coupon_number']);
		unset($_SESSION['total']);
	}

	/**
	 * Unsets data in the session related to checkout coupons
	 */
	public function unsetSessionCouponData() {

		unset($_SESSION['coupon']);
		unset($_SESSION['coupon_value']);
		unset($_SESSION['coupon_number']);
	}

	/**
	 * Unsets all data in the session related to checkout
	 */
	public function unsetSessionCheckoutData() {

		// Checkout stuff
		unset($_SESSION['zipcode']);
		unset($_SESSION['shipping_services']);
		unset($_SESSION['tax_exempt']);
		unset($_SESSION['shipping_charges']);
		unset($_SESSION['shipping_carrier']);
		unset($_SESSION['shipping_arrival_date']);
		unset($_SESSION['checkout_form']);
		unset($_SESSION['coupon']);
		unset($_SESSION['status']);
		unset($_SESSION['sales_tax']);
		unset($_SESSION['coupon_value']);
		unset($_SESSION['coupon_number']);
		unset($_SESSION['total']);

		// If this is a guest, clear out the guest session data (effectively logging them out)
		if (mb_strtolower($_SESSION['UserType']) == "g") {

			unset($_SESSION['UserType']);
			unset($_SESSION['CID']);
			unset($_SESSION['Useremail']);
		}
	}

	/**
	 * This function writes the session to the database.
	 */
	public static function updateDatabase() {

		return Connection::getHandle()
                ->prepare('INSERT INTO bs_sessions (`session_id`,`invoice_total`,`expiry`,`location`,`ip_address`,
		                    `Referrer`,`refpage`,`browser`,`os`,`shipping_carrier`,`shipping_services`,`shipping_charges`,`tax_exempt`,
		                    `sales_tax`,`zipcode`,`shipping_carrier_pre`,`shipping_services_pre`,`shipping_charges_pre`,`shipping_arrival_date`,
		                    `shipping_zipcode`,`coupon_number`,`coupon_value`,`search_engine`,`keyword`,`session_date`,`current_url`,`current_file`,
		                    `current_url_time`,`cart_hash`,`modification_time`)

                           VALUES (:session_id,:invoice_total,:expiry,:location,:ip_address,:Referrer,:refpage,:browser,:os,:shipping_carrier,
                                :shipping_services,:shipping_charges,:tax_exempt,:sales_tax,:zipcode,:shipping_carrier_pre,:shipping_services_pre,
                                :shipping_charges_pre,:shipping_arrival_date,:shipping_zipcode,:coupon_number,:coupon_value,:search_engine,
                                :keyword,:session_date,:current_url,:current_file,:current_url_time,:cart_hash,NOW())

                           ON DUPLICATE KEY UPDATE `invoice_total` = VALUES(`invoice_total`), `expiry` = VALUES(`expiry`), `location` = VALUES(`location`),
                                `ip_address` = VALUES(`ip_address`), `Referrer` = VALUES(`Referrer`), `refpage` = VALUES(`refpage`),
                                `browser` = VALUES(`browser`), `os` = VALUES(`os`), `shipping_carrier` = VALUES(`shipping_carrier`),
                                `shipping_services` = VALUES(`shipping_services`), `shipping_charges` = VALUES(`shipping_charges`), `tax_exempt` = VALUES(`tax_exempt`),
                                `sales_tax` = VALUES(`sales_tax`), `zipcode` = VALUES(`zipcode`), `shipping_carrier_pre` = VALUES(`shipping_carrier_pre`),
                                `shipping_services_pre` = VALUES(`shipping_services_pre`), `shipping_charges_pre` = VALUES(`shipping_charges_pre`),
                                `shipping_arrival_date` = VALUES(`shipping_arrival_date`), `shipping_zipcode` = VALUES(`shipping_zipcode`),
                                `coupon_number` = VALUES(`coupon_number`), `coupon_value` = VALUES(`coupon_value`), `search_engine` = VALUES(`search_engine`),
                                `keyword` = VALUES(`keyword`), `session_date` = VALUES(`session_date`), `current_url` = VALUES(`current_url`),
                                `current_file` = VALUES(`current_file`), `current_url_time` = VALUES(`current_url_time`), `cart_hash` = VALUES(`cart_hash`),
                                `modification_time` = VALUES(`modification_time`)')

                           ->execute(array(
                                ':session_id'            => session_id(),
                                ':invoice_total'         => isset($_SESSION['invoice_total']) ? $_SESSION['invoice_total'] : 0,
                                ':expiry'                => isset($_SESSION['expiry']) ? $_SESSION['expiry'] : NULL,
                                ':location'              => isset($_SESSION['location']) ? $_SESSION['location'] : NULL,
                                ':ip_address'            => isset($_SESSION['ip_address']) ? $_SESSION['ip_address'] : NULL,
                                ':Referrer'              => isset($_SESSION['Referrer']) ? $_SESSION['Referrer'] : NULL,
                                ':refpage'               => isset($_SESSION['refpage']) ? $_SESSION['refpage'] : NULL,
                                ':browser'               => isset($_SESSION['browser']) ? $_SESSION['browser'] : NULL,
                                ':os'                    => isset($_SESSION['os']) ? $_SESSION['os'] : NULL,
                                ':shipping_carrier'      => isset($_SESSION['shipping_carrier']) ? $_SESSION['shipping_carrier'] : NULL,
                                ':shipping_services'     => isset($_SESSION['shipping_services']) ? $_SESSION['shipping_services'] : NULL,
                                ':shipping_charges'      => isset($_SESSION['shipping_charges']) ? $_SESSION['shipping_charges'] : NULL,
                                ':tax_exempt'            => isset($_SESSION['tax_exempt']) && mb_strtolower($_SESSION['tax_exempt']) === 'y' ? 1 : 0,
                                ':sales_tax'             => isset($_SESSION['sales_tax']) ? $_SESSION['sales_tax'] : NULL,
                                ':zipcode'               => isset($_SESSION['zipcode']) ? $_SESSION['zipcode'] : NULL,
                                ':shipping_carrier_pre'  => isset($_SESSION['shipping_carrier_pre']) ? $_SESSION['shipping_carrier_pre'] : NULL,
                                ':shipping_services_pre' => isset($_SESSION['shipping_services_pre']) ? $_SESSION['shipping_services_pre'] : NULL,
                                ':shipping_charges_pre'  => isset($_SESSION['shipping_charges_pre']) ? $_SESSION['shipping_charges_pre'] : NULL,
                                ':shipping_arrival_date' => isset($_SESSION['shipping_arrival_date']) ? $_SESSION['shipping_arrival_date'] : NULL,
                                ':shipping_zipcode'      => isset($_SESSION['shipping_zipcode']) ? $_SESSION['shipping_zipcode'] : NULL,
                                ':coupon_number'         => isset($_SESSION['coupon_number']) ? $_SESSION['coupon_number'] : NULL,
                                ':coupon_value'          => isset($_SESSION['coupon_value']) ? $_SESSION['coupon_value'] : NULL,
                                ':search_engine'         => isset($_SESSION['search_engine']) ? $_SESSION['search_engine'] : NULL,
                                ':keyword'               => isset($_SESSION['keyword']) ? $_SESSION['keyword'] : NULL,
                                ':session_date'          => isset($_SESSION['session_date']) ? $_SESSION['session_date'] : NULL,
                                ':current_url'           => isset($_SESSION['current_url']) ? $_SESSION['current_url'] : NULL,
                                ':current_file'          => isset($_SESSION['current_file']) ? $_SESSION['current_file'] : NULL,
                                ':current_url_time'      => isset($_SESSION['current_url_time']) ? $_SESSION['current_url_time'] : NULL,
                                ':cart_hash'             => isset($_SESSION['cartHash']) ? $_SESSION['cartHash'] : NULL
                           ));

	}



	public static function parseReferrerUrl($referrerUrl) {

		if ( isset($referrerUrl) && !empty($referrerUrl) ) {

			$referrerUrlParts = parse_url($referrerUrl);

			// Attempt to get the primary domain from the hostname. Note that this only currently supports hostnames in the .tld and .co.tld spaces.
			preg_match('/^(?:.*?)([^.]+)\.(?:co\.[^.]+|[^.]+)$/i', $referrerUrlParts['host'], $domainLevels);

			// If the primary domain was found...
			if ( isset($domainLevels, $domainLevels[1]) ) {

				// Normalize the primary domain.
				$primaryDomain = mb_strtolower($domainLevels[1]);

				// Compare the parsed referrer URL against the common sources to determine the associated source and keyword parameters.
				foreach ( self::$commonReferrerDomains as $commonReferrerDomain ) {
					if ( in_array($primaryDomain, $commonReferrerDomain['domains']) ) {
						$source = $commonReferrerDomain['source'];
						$keywordParameters = $commonReferrerDomain['keywordParameters'];
						break;
					}
				}

				// If the source wasn't matched above, use default values.
				if ( !isset($source) ) {
					$keywordParameters = array('q');
				}

				// Only proceed if the necessary information was supplied.
				if ( isset($referrerUrlParts['query']) && !empty($referrerUrlParts['query']) && !empty($keywordParameters) ) {

					// Parse the query string so that we can look for the search keyword.
					parse_str($referrerUrlParts['query'], $queryStringParsed);

					// For each paramater...
					foreach ( $keywordParameters as $parameter ) {

						// Only proceed if the parameter is a string or number.
						if ( is_string($parameter) || is_numeric($parameter) ) {

							// If the paramater has a value in the query string...
							$parameterString = (string) $parameter;
							if ( isset($queryStringParsed, $queryStringParsed[$parameterString]) && !empty($queryStringParsed[$parameterString]) ) {

								// Get the keyword and break out of the loop.
								$keyword = $queryStringParsed[$parameterString];
								break;

							}

						}

					}

				}
			}
		}

		return array(
			'source' => isset($source) ? $source : 'other',
			'keyword' => isset($keyword) ? $keyword : NULL
		);

	}



}
