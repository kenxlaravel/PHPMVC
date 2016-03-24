<?php

	class Orders
	{

		var $orderno = Null;
        protected $formData;

        public function __construct($formData = NULL) {

            if ( isset($formData) ) {
                $this->formData = $formData;
            }

        }

		function getOrdersId($orderno){

			$sql=Connection::getHandle()->prepare("SELECT orders_id FROM bs_orders WHERE order_no= ? LIMIT 1");
			$sql->execute(array($orderno));
			$row=$sql->fetch(PDO::FETCH_ASSOC);

			return $row['orders_id'];

		}

        //todo: this quiery has already been checked, remove comment when class has been fully audited
		function getCustomerOrderConfirmation(){

			// Determine the customer ID based on whether the current user is an admin doing and an admin checkout or not.
			$cid = $_SESSION['adminID'] ? $_SESSION['adminID'] : $_SESSION['CID'];

			// Prepare the query.
			$sql = Connection::getHandle()->prepare("SELECT orders_id AS orders_id,
		                                   order_no AS order_no,
		                                   customers_email AS customers_email,
		                                   shipping_first_name AS shipping_first_name,
		                                   shipping_last_name AS shipping_last_name,
		                                   shipping_state AS shipping_state,
		                                   shipping_country AS shipping_country,
		                                   orders_status AS orders_status,
		                                   total_amount AS total_amount,
		                                   tracking_number AS tracking_number,
		                                   shipping_carrier AS shipping_carrier,
		                                   shipping_services AS shipping_services,
		                                   shipping_account AS shipping_account,
		                                   tax_exempt AS tax_exempt,
		                                   freight_shipment AS freight_shipment,
		                                   newsletter_flag AS newsletter,
		                                   shipping_pickup_estimate AS shipping_pickup_estimate,
		                                   shipping_arrival_estimate AS shipping_arrival_estimate,
		                                   proofs_requested AS proofs_requested
		                            FROM bs_orders
		                            WHERE order_no = ? AND orders_status != '5' AND customers_id = ?");

			// Execute the query.
			$sql->execute(array(
							  $_REQUEST['orderno'],
							  $cid
						  ));

			// Parse the results.
			while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {
				$row['proofs_requested'] = $row['proofs_requested'] === '1' ? TRUE : FALSE;
				$order[] = $row;
			}

			return $order;
		}

		public function GetCustomerOrder($orderno) {

            $order = array();

			if( $orderno ) {

				$sql = Connection::getHandle()->prepare(
                            "SELECT * FROM bs_orders WHERE order_no = :orderno AND orders_status != '5' ");

                $sql->bindParam(":orderno", $orderno, PDO::PARAM_STR);

                if( $sql->execute() ) {

                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                        $order[] = $row;
                    }

                    return $order;
                }
            } else {

                return false;
            }
		}



		function getNewUser(){

			$sql=Connection::getHandle()->prepare("SELECT count(*) AS count FROM bs_customers WHERE username= ? AND user_type='U'");
			$sql->execute(array($_SESSION['Useremail']));

			$row=$sql->fetch(PDO::FETCH_ASSOC);

			return $row;
		}


		function getValidOrder($orderno){

			if(isset($_REQUEST['orderno'])){
				$orderno=$_REQUEST['orderno'];
			}

			$sql_order=Connection::getHandle()->prepare("SELECT count(*) AS count FROM bs_orders WHERE order_no =:orderno");
			$sql_order->execute(array(":orderno"=>$orderno));
			$row=$sql_order->fetch(PDO::FETCH_ASSOC);

			$order_valid=$row['count'];

			return $order_valid;
		}



		function GetOrderInvoice($orderno, $objUser) {

			if(isset($_GET['orderno'])) {
				$sql = Connection::getHandle()->prepare(
                          "SELECT c.user_type, o.* FROM bs_orders o LEFT JOIN bs_customers c USING (customers_id)
                          WHERE o.order_no = ? LIMIT 1");

				$sql->execute(array($_GET['orderno']));

                while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$order[] = $row;
				}

				if ($sql->rowCount()>0) {

					// Make sure that this order has a customer id
					if ($order[0]['customers_id'] > 0) {

						// Make sure that the user owns the order, the order was placed as a guest (anyone can view guest invoices),
						// or the user is an admin (admins can view anyone's invoice)
						if ($order[0]['customers_id'] == $_SESSION['CID'] || mb_strtolower($order[0]['user_type']) == 'g' || $objUser->checkAdmin()) {
							return $order;
						} else {
							return 'mismatch';
						}

					} else {
						return false;
					}

				}
			}

		}



		function GetOrderInvoiceEmail($orderno) {

			if(isset($orderno) && isset($_SESSION['CID'])) {

				$id = (isset($_SESSION['adminID']) ? $_SESSION['adminID'] : $_SESSION['CID']);

				$sql = Connection::getHandle()->prepare(
                                "SELECT r.reason, o.*, c.user_type
										FROM bs_orders o
										LEFT JOIN bs_customers c USING(customers_id)
										LEFT JOIN bs_paypal_pending_reasons r ON (r.id = o.paypal_pending_status)
										WHERE o.order_no = ?
										AND o.customers_id = ?
										AND o.orders_status != '5' ");
				$sql->execute(array($orderno, $id));

				while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$row['proofs_requested'] = $row['proofs_requested'] === '1' ? TRUE : FALSE;
					$order[] = $row;
				}

				return $order;
			}

		}



		function GetOrderInvoiceTrackingEmail($orderno) {

			if(isset($orderno)) {

				$sql = Connection::getHandle()->prepare(
                            "SELECT * FROM bs_orders WHERE order_no = ? AND orders_status != '5' ");

				$sql->execute(array($orderno));

				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$order[] = $row;
				}

				return $order;
			}

		}



		function GetCustomerOrderList($CID, $limit=NULL) {

			$stmt = "SELECT *
				 FROM bs_orders
				 WHERE customers_id = ?
				 ORDER by orders_id DESC";

			if ($limit > 0) { $stmt .= ' LIMIT ' . $limit; }


			$sql = Connection::getHandle()->prepare($stmt);
			$sql->execute(array($CID));

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$order[] = $row;
			}

			return $order;
		}



		//Gets a count of orders for a given CID
		public function getOrderCount($CID) {

			$sql = Connection::getHandle()->prepare(
                        "SELECT COUNT(*) AS total FROM bs_orders WHERE customers_id = ? AND orders_status != '5' ");

			$sql->execute(array($CID));

			$row = $sql->fetch(PDO::FETCH_ASSOC);

			return $row['total'];
		}


		function GetCustomerLastOrderUpdate($CID) {

			if (isset($_SESSION['CID'])) {

				$sql = Connection::getHandle()->prepare(
                            "UPDATE bs_customer_shipping_methods SET shipping_services = ? WHERE customer_id = ?"
                );

				$sql->execute(array($_REQUEST['shippingmethod'], $_SESSION['CID']));


				header("Location: " . https . "myaccount.php");
				die();
			}

		}



		function GetCustomerShippingMethodList($CID) {

			if (isset($_SESSION['CID'])) {

				$sql = Connection::getHandle()->prepare(
                            "SELECT shipping_method_id AS shipping_method_id, customers_id AS customers_id,
                             shipping_postcode AS shipping_postcode, shipping_services AS shipping_services
                             FROM bs_customer_shipping_methods WHERE customers_id = ?");

				$sql->execute(array($_SESSION['CID']));

				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                       $shipping_method[] = $row;
				}

				return $shipping_method;
			}
		}



		function GetCreditCard($orderid) {

			if( isset($_GET['orderno']) || $orderid ) {

				$sql = Connection::getHandle()->prepare("SELECT * FROM bs_credit_card WHERE orders_id = ?");
				$sql->execute(array($orderid));

				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

					$order[] = $row;
				}

				return $order;
			}
		}

		function ProductImage($p_id) {

            $row = array();

			$sql = Connection::getHandle()->prepare(
                        "SELECT DISTINCT p.id AS products_id, p.product_number AS product_number, s.small_image AS image1_thumbnail

                         FROM bs_products p

                         LEFT JOIN bs_product_skus ps ON (ps.product_id = p.id)
                         LEFT JOIN bs_skus s ON (s.id = ps.sku_id) WHERE p.id =  ?"
            );

			$sql->execute(array($p_id));

			$row = $sql->fetch(PDO::FETCH_ASSOC);

            return $row;
		}


		/**
		 * Displays order status
		 * @param [string] $status [order status name]
		 */
		function OrderStatus($status) {

			$sql = Connection::getHandle()->prepare("SELECT * FROM bs_order_statuses WHERE orders_status_id = ?");
			$s   = $sql->execute(array($status));

            $row = $sql->fetch(PDO::FETCH_ASSOC);
			$order_status = $row['orders_status_name'];

			return $order_status;
		}


		/**
		 * Gets order purchase date as datetime
         *
		 * @param string $date [description]
         * @return string $data
		 */
		function OrderPurchasedDate($date) {
			$timestamp = strtotime($date);
			$date = date('F d, Y ', $timestamp);
			return $date;
		}



		/*Function display order purchasedate convert into datetime */
		function OrderDate($date) {
			$timestamp = strtotime($date);
			$date = date('m/d/Y ', $timestamp);
			return $date;
		}



		/*Function display order purchasedate convert into datetime */
		function OrderHistoryDate($date) {
			$year = substr("$date", 0, 4);
			$month = substr("$date", 5,2 );
			$day = substr("$date", 8, 2);
			$date=date('M dS, Y', mktime(0, 0, 0, $month, $day, $year));
			return $date;
		}



		/**
		 * Duplicates a custom sign (flash or streetname) from an order so it can be reordered
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		public function duplicateCustom($id, $save_design = false) {

            $ObjShoppingCart = Cart::getFromSession();
            $ObjFlash = new FlashDesign();

			//Generate a new ID to use
			$design_id = $ObjShoppingCart->getUniqueDesignId();

			//Get some information about the product
			$sql = Connection::getHandle()->prepare("SELECT * FROM bs_product_custom WHERE custom_product_id = ?");
			$sql->execute(array($id));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

            if ( file_exists(APP_ROOT.'/design/save/previews/' . $row["custom_image"]) ) {

                //Duplicate the old file, and save it with the new id as the filename
                if (copy(APP_ROOT . '/design/save/previews/' . $row["custom_image"], APP_ROOT . '/design/save/previews/' . $design_id . '.jpg')) {

                    copy(APP_ROOT . '/design/save/previews/small/' . $row["custom_image"], APP_ROOT . '/design/save/previews/small/' . $design_id . '.jpg');
                    copy(APP_ROOT . '/design/save/previews/medium/' . $row["custom_image"], APP_ROOT . '/design/save/previews/medium/' . $design_id . '.jpg');

                    //Check and see if we have a zip file
                    if (!empty($row['pdf_file'])) {

                        if (file_exists(APP_ROOT . '/design/save/zips/' . $row["pdf_file"])) {

                            //We have a zip file, so we'll duplicate it
                            if (copy(APP_ROOT . '/design/save/zips/' . $row["pdf_file"], APP_ROOT . '/design/save/zips/' . $design_id . '.zip')) {

                                $custom_zip = $design_id . ".zip";

                                //It couldn't be duplicated, the function failed
                            } else {
                                return false;
                            }

                        }

                    } else {
                        $custom_zip = '';
                    }

                    if ($save_design === false) {
                        $save_design = 0;
                    } else if ($save_design === true) {
                        $save_design = 1;
                    }

                    //Insert the product into bs_products_custom
                    $sql = Connection::getHandle()->prepare(
                        "INSERT INTO bs_product_custom (design_id, custom_image, custom_xml,
																		pdf_file, active, product_id, session_id,
																		ip, customers_id, created_date,
																		comments, url, save_design,
																		last_modified, tool_type_id, background_id)
										VALUES
																	   (:design_id, :custom_image, :custom_xml,
																		:pdf_file, :active, :product_id, :session_id,
																		:ip, :customers_id, :created_date,
																		:comments, :url, :save_design, NOW(),
																		:tool_type_id, :background_id)");
                    $sql->execute(array(":design_id" => $design_id,
                        ":custom_image" => $design_id . ".jpg",
                        ":custom_xml" => $design_id . "/canvas.xml",
                        ":pdf_file" => !empty($custom_zip) ? $custom_zip : '',
                        ":active" => 1,
                        ":product_id" => $row['product_id'],
                        ":session_id" => session_id(),
                        ":ip" => $_SERVER['REMOTE_ADDR'],
                        ":customers_id" => $_SESSION['CID'] > 0 ? $_SESSION['CID'] : NULL,
                        ":created_date" => $row['created_date'],
                        ":comments" => $row['comments'],
                        ":url" => 'N',
                        ":save_design" => $save_design,
                        ":tool_type_id" => $row['tool_type_id'],
                        ":background_id" => $row['background_id']
                    ));

                    $cpi = Connection::getHandle()->lastInsertId();

                    // Make sure the row insterted
                    if ($cpi > 0) {

                        // If this was a flash product we'll have to unzip it and do inserts into the flash design tables
                        if (!empty($custom_zip)) {

                            $file = APP_ROOT . '/design/save/zips/' . $custom_zip;
                            $ObjFlash->extract_canvas_data($file, $design_id, $cpi);

                        }

                        // Return
                        return array($cpi, $design_id . ".jpg");

                    } else {
                        return false;
                    }

                    // It couldn't be duplicated, the function failed
                } else {

                    return false;
                }

            }

		}


		function GetOrderTrackingDetails($orderid) {

			$sth = Connection::getHandle()->prepare("SELECT `o`.`orders_id` AS `orderid`,
									 `o`.`order_no` AS `orderno`,
									 `o`.`total_amount` AS `total`,
									 `o`.`sales_tax` AS `tax`,
									 `o`.`shipping_charges` AS `shipping`,
									 `o`.`shipping_city` AS `city`,
									 `o`.`shipping_state` AS `state`,
									 `o`.`shipping_country` AS `country`,
									 `o`.`customers_email` AS `email`,
									 `o`.`coupon_value` AS `coupon`,
									 `o`.`shipping_pickup_estimate` AS `shipping_pickup_estimate`
							FROM `bs_orders` `o`
							WHERE `o`.`orders_id`= ?");

            $sth->execute(array(isset($orderid) ? $orderid : $_GET['orderno']));

			$order = array();

			if ($row = $sth->fetch()) {

				// Add the main order details.
				$order['orderid'] = $row['orderid'];
				$order['orderno'] = $row['orderno'];
				$order['total'] = $row['total'];
				$order['tax'] = $row['tax'];
				$order['shipping'] = $row['shipping'];
				$order['city'] = $row['city'];
				$order['state'] = $row['state'];
				$order['country'] = $row['country'];
				$order['email'] = $row['email'];
				$order['coupon'] = $row['coupon'];
				$order['shipping_pickup_estimate'] = $row['shipping_pickup_estimate'];

			}

			return $order;

		}


		/**
		 * This function gets tracking flag to track calls to google
		 * @param $orderid Orders id for order
		 * @return tracking flag value 1/0
		 */
		function getTrackingFlag($orderid){


			$sql_order=Connection::getHandle()->prepare("SELECT tracking_flag AS tracking_flag FROM bs_orders WHERE orders_id=?");
			$sql_order->execute(array(isset($orderid) ? $orderid : $_GET['orderno']));

			$row_order=$sql_order->fetch(PDO::FETCH_ASSOC);
			$tracking_flag=$row_order['tracking_flag'];

			return $tracking_flag;

		}



		/**
		 * This function sets tracking flag to track calls to google
		 * @param $orderid Orders id for order
		 */

		function setTrackingFlag($orderid){

			$sql_order_flag=Connection::getHandle()->prepare("UPDATE bs_orders SET tracking_flag=1 WHERE orders_id = ? ");
			$sql_order_flag->execute(array(isset($orderid) ? $orderid : $_GET['orderno']));

		}



		/**
		 *This function collects information to send email for order confirmation
		 * @param orderno string
		 * @return array
		 */
		public function OrderTrackingEmailContent($orderno){

			$objCountry = new Countries();

			$track   = Page::create('tracking');
			$account = Page::create('my-account');

			// Instantiate the cart
			$ObjShoppingCart = Cart::getCartFromOrderNumber($orderno);

			$order = $this->GetOrderInvoiceTrackingEmail($orderno);

			$count = count($order);

			// Make sure we have items in the cart
			if($count>0){
				foreach($order as $key => $value){
					$orderid=$value["orders_id"];
				}
				$stock=0;
				$custom=0;

				$shipping_country=$objCountry->CountryCodeList($value["shipping_country"]);
				$billing_country=$objCountry->CountryCodeList($value["billing_country"]);
				$orderstatus=$this->OrderStatus($value['orders_status']);
				$shippeddate=substr($value["last_modified"],0,10);

				$track_num        = $value['tracking_number'];
				$orderno          = $value['order_no'];
				$shipdate         = $this->OrderPurchasedDate($shippeddate);
				$ship_method      = $value['shipping_services'];
				$shipping_carrier = $value['shipping_carrier'];
				$status           = $this->OrderStatus($value['orders_status']);
				$customer_email   = $value["customers_email"];
				$purchase_order   = $value['purchase_order'];
				$coupon_value     = $value['coupon_value'];
				$tag_job          = $value['tag_job'];
				$shipping_charges = $value["shipping_charges"];
				$salestax         = $value['sales_tax'];
				$total_amount     = $value['total_amount'];
				$exp_shipping     = $value["expedited_shipping"];
				$comments         = $value["comments"];
				$shipping_account = $value['shipping_account'];


				//shipping detail array
				$shipaddress=array(
					'ship_name'=>$value["shipping_name"],
					'shipping_company'=>$value["shipping_company"],
					'shipping_street_address'=>$value['shipping_street_address'],
					'shipping_suburb'=>$value["shipping_suburb"],
					'shipping_city'=>$value['shipping_city'],
					'shipping_state'=>$value['shipping_state'],
					'shipping_postcode'=>$value['shipping_postcode'],
					'shipping_country'=>$shipping_country["countries_name"],
					'shipping_phone'=>$value["shipping_phone"],
					'shipping_fax'=>$value["shipping_fax"],
					'shipping_country_code'=>$value["shipping_country"]
				);


				//billing detail array
				$billaddress=array(
					'bill_name'=>$value['billing_name'],
					'billing_company'=>$value['billing_company'],
					'billing_street_address'=>$value['billing_street_address'],
					'billing_suburb'=>$value['billing_suburb'],
					'billing_city'=>$value['billing_city'],
					'billing_state'=>$value['billing_state'],
					'billing_postcode'=>$value['billing_postcode'],
					'billing_country'=>$billing_country["countries_name"],
					'billing_phone'=>$value['billingphone_display'],
					'billing_country_code'=>$value["billing_country"]
				);


				if (mb_strtolower($value['ccType']) == 'brimar') {
					$cctype = "Net30 Account";
				} else if (mb_strtolower($value['ccType']) == 'paypal') {
					$cctype = "PayPal";
				} else {
					$cctype = "Creditcard";
				}

				$cardtype=$value['ccType'];
				$cardNum=$value['lastFourCcNum'];
				$expiration=$value['ccExpire'];
				$total_amount=$value['total_amount'];

				//payment detail array
				$paymentinfo=array(
					'cctype'=>$ccType,
					'cardtype'=>$cardtype,
					'cardNum'=>$cardNum,
					'expiration'=>$expiration,
					'total_amount'=>$total_amount
				);

				// Loop through each item in the order
				foreach ($ObjShoppingCart->products AS $product) {

					$attributes = array();

					switch ($product->type) {

						case 'stock':
							$attributes = array();
							break;


						case 'builder':
							foreach($product->settings as $setting) {
								$label = $setting['builderLabel'];
								if ($setting['builderSettingDisplay'] == true) {
									if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
										$attributes[$label] = $setting['builderValueText'];
									}
								}
							}
							break;


						case 'flash':
							foreach($product->upcharges as $upcharge) {
								if (!empty($upcharge['name'])) {
									$attributes[$upcharge['type']] = $upcharge['name'];
								}
							}
							break;


						case 'streetname':
							foreach ($product->getAdditionalDetails() as $key => $att_value) {
								$attributes[$key] = $att_value;
							}
							foreach ($product->upcharges AS $upcharge) {
								if (!empty($upcharge['name'])) {
									$attributes[$upcharge['type']] = $upcharge['name'];
								}
							}
							break;

					}

					$cart[]=array(
						'sku_code'=>$product->skuCode,
						'size'=>$product->size,
						'material'=>$product->materialDescription,
						'attribute'=>$attributes,
						'builder_attributes'=>$product->settings,
						'stock_custom'=>($product->isCustom ? 'C' : 'S'),
						'design_service'=>$product->designService,
						'product_type'=>$product->type,
						'comment'=>$product->comments,
						'quantity'=>$product->quantity,
						'price'=>$product->unitPrice,
						'total'=>$product->totalPrice,
						'file_name'=>(!empty($product->uploads[0]['hash']) ? TRUE : FALSE)
					);

				}

				// Natural business delay
				$ship_time = 1;

				// General production delay
				$ship_time +=  Settings::getSettingValue('productiondelay');

				$item_type = 1;

				// Add preset delay to produce custom items if applicable
				if($ObjShoppingCart->getCustomCount() > 0){
					$ship_time += Settings::getSettingValue('customproductdelay');
					$item_type = 2;
				}

				$guest = ($value['user_type']=='G') ? TRUE : FALSE;
				//final output array with all details
				$order_track=array(
					"trackingnumber"=>$track_num,
					"orderno"=>$orderno,
					"name"=>$billaddress['bill_name'],
					"shipdate"=>$shipdate,
					"shipmethod"=>$ship_method,
					"shipping_carrier" => $shipping_carrier,
					"status"=>$status,
					"shipaddress"=>$shipaddress,
					"billaddress"=>$billaddress,
					"customer_email"=>$customer_email,
					"paymentinfo"=>$paymentinfo,
					"cart"=>$cart,
					"purchase_order"=>$purchase_order,
					"tag_job"=>$tag_job,
					"subtotal"=>$ObjShoppingCart->getSubtotal(),
					"coupon_value"=>$coupon_value,
					"shippingcharge"=>$shipping_charges,
					"salestax"=>$salestax,
					"invoicetotal"=>$total_amount,
					'expedited_shipping'=>$exp_shipping,
					"comments"=>$comments,
					"ship_time"=>$ship_time,
					"item_type"=>$item_type,
					"track_url"=>$track->getUrl() . '?orderno=' . $orderno,
					"account_url"=>$account->getUrl(),
					"guest"=>$guest,
					"shipping_account"=>$shipping_account
				);

				return $order_track;
			}
		}



		/**
		 * Calls/sends email according to email type
		 */
		function OrderTrackingEmail()
		{

            $shipping = Page::create('shipping');
            $faqs     = Page::create('faqs');
            $privacy  = Page::create('privacy-policy');
            $help     = Page::create('help');
            $custom   = Page::create('custom-products');

            $ObjCcType = new CreditCardType();
            $ObjMenu   = new Menu();


            $ObjCcType->GetUpdateCcType();

            //get urls for header
			$menu['Custom Signs'] = $custom->getUrl();

			// Grab a list of main menu categories
			$main_menu = $ObjMenu->MenuList();

			// As long as we have some categories, continue
			if (count($main_menu) > 0) {

				// Loop through the menu items
				foreach($main_menu as $key => $value) {

					//The name of the menu item
					$menu_name=$value['name'];

					//The id of the menu item
					$main_category_id = $value['primary_link_pageid'];

					//Instantiate a new link from the page class so we can get a constructed URL
					$link = new Page('category', $main_category_id);
					$menu[$menu_name]=$link->getUrl();

				}
			}


			$sql= Connection::getHandle()->prepare(
                        "SELECT 'tracking' as `emailtype`, orders_id, order_no,orders_status  FROM
                         bs_orders WHERE orders_status = 3 AND tracking_status = 1 AND order_type = 'website'
                         UNION
                         SELECT 'pickup' as `emailtype`, orders_id, order_no,orders_status  FROM
                         bs_orders WHERE orders_status = 6 AND order_type = 'website' AND email_count = 0
                         UNION
                         SELECT 'pickupreminder' as `emailtype`, orders_id, order_no,orders_status FROM
                         bs_orders WHERE orders_status = 6 AND order_type = 'website' AND email_count > 0 AND email_count < 3
                         AND date_email_sent != '0000-00-00'  AND date_email_sent <= DATE(NOW() - INTERVAL 3 DAY) AND date_email_sent!=CURDATE()");

			if( $sql->execute() ) {

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                    $orderno = $row['order_no'];
                    $status = $row['orders_status'];
                    $orderid = $row['orders_id'];
                    $email_type = $row['emailtype'];
                    $order = $this->GetOrderInvoiceTrackingEmail($orderno);

                    $data = array (
                        'orderno' => $orderno,
                        'customer' => $order['customers_email'],
                        'name' => $order['billing_name'],
                        'shipping' => $shipping->getUrl(),
                        'faqs' => $faqs->getUrl(),
                        'privacy' => $privacy->getUrl(),
                        'help' => $help->getUrl(),
                        'menu' => $menu
                    );
                    if( $email_type == 'tracking' ) {

                        $track_data = $this->OrderTrackingEmailContent($orderno);
                        $customer_email = $order['customers_email'];

                        $ObjEmail = new Email();

                        $mail_sent = $ObjEmail->sendOrderTrackingEmail($track_data, $customer_email);

                        if( $mail_sent ) {

                            $sqlSend = Connection::getHandle()->prepare(
                                "UPDATE bs_orders SET tracking_status=:tracking_status WHERE orders_id = :orders_id"
                            );
                            $sqlSend->execute(array (":tracking_status" => '2', ":orders_id" => $orderid));
                        }

                    }else if( $email_type == 'pickup' ) {

                        $ObjEmail = new Email();
                        $mail_sent=$ObjEmail->sendPickUpEmail($data);
                    }else if( $email_type == 'pickupreminder' ) {

                        $ObjEmail = new Email();
                        $mail_sent=$ObjEmail->sendPickupReminderEmail($data);
                    }

                    if( $mail_sent ) {

                        $sqlSend = Connection::getHandle()->prepare(

                            "UPDATE bs_orders SET date_email_sent = ?, email_count = email_count+1 WHERE orders_id = ?"
                        );
                        $sqlSend->execute(array (date('Y-m-d'), $orderid));
                    }

                }
            }

		}



		function GetOrderHistory() {

			if (!empty($_REGUEST['orderno'])) {

                $sql = Connection::getHandle()->prepare(
                            "SELECT * FROM bs_orders WHERE customers_id = ? AND orders_status != '5' AND order_no = ?
                            ORDER BY orders_id DESC");

				$sql->execute(array($_SESSION['CID'], $_REQUEST['orderno']));

			} else {

                $sql = Connection::getHandle()->prepare(
                            "SELECT * FROm bs_orders WHERE customers_id = ? AND orders_status != '5' ORDER BY orders_id DESC");

                $sql->execute(array($_SESSION['CID']));
			}

			while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$order[] = $row;
			}

		}


		public function GetDatePickup($orderno) {

            $date_pickup = "";

			$sql = Connection::getHandle()->prepare(
                        "SELECT date_pickedup FROM bs_orders WHERE order_no = ? AND orders_status = '7'
                            AND date_pickedup != '0000-00-00' ");

            $sql->execute(array($orderno));

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

					$date_pickup = $row['date_pickedup'];
			}

			return $date_pickup;
		}



		public function getOrdernoExist($orderno) {

			$sql_exists=Connection::getHandle()->prepare("SELECT count(*) AS count FROM bs_orders WHERE order_no=:order_no");
			$sql_exists->execute(array(":order_no"=>$orderno));
			$row=$sql_exists->fetch(PDO::FETCH_ASSOC);
			if($row['count']==0)
				return false;
			else
				return true;
		}



		public function placeOrder($level, $payment_type, $card_type, $order_number = NULL, $paypal_response = NULL, $authorization = NULL) {

			// Global shopping cart so we have access to it
			//global $ObjShoppingCart;
			$ObjShoppingCart = Cart::getFromSession(FALSE);

			// Save the order to the orders table
			$order_array = $this->saveOrder($level, $payment_type, $card_type, $order_number, $paypal_response, $authorization);

			// Check for success
			if ($order_array != FALSE) {

				// Mark the cart as ordered
				$ObjShoppingCart->setOrdered();

				// Disassociate the just-ordered cart from customer's current cart
				$ObjShoppingCart->removeFromSession();

				// Send the order confirmation email
				$this->GetOrderEmail($order_array['order_number']);

				// Return the order id
				return $order_array['order_number'];

			} else {

				return false;

			}

		}



		private function saveOrder($level, $payment_method, $card_type, $order_number = NULL, $paypal_response = NULL, $authorization = NULL) {

			//Payment methods:
			// 1 - Credit Card
			// 2 - Brimar Net30
			// 3 - PayPal

            $objCheckout = new Checkout($this->formData);

			$objUser = new User();
			$ObjShoppingCart = Cart::getFromSession(FALSE);

			// Instantiate a user address object
			$objAddress = new Addresses();

			// Instantiate a shipping charges object
			$ObjShippingCharges = new ShippingCharges();

			//Standardize card type names
			if ($card_type == 'MasterCard') {

                $card_type = 'Mastercard';

			} else if ($card_type == 'American Express') {

                $card_type = 'Amex';
			}

			// Grab a list of available countries
			$countries = $objAddress->listCountries();

			// if this is not a PayPal checkout, check the billing country and state
			if ($payment_method !== 'paypal') {

				// Loop through countries. If we have a match and the country has zones, keep state. Otherwise we can drop state
				foreach($countries as $country) {

					if ($country['countries_iso_code_2'] == $objCheckout->billing_address['country']) {

                        if ($country['zone'] == "false") {

                            $objCheckout->billing_address['state'] = '';
						}
					}

				}

			}

			// If an order number was not passed in, generate one
			if ($order_number == NULL) {

				// Generate a new unique order number to use
				$order_number = $this->generateOrderNumber();

			}

			// Get the order total
			$order_total = Checkout::calculateTotal();

			// Get order sales tax
			$sales_tax = $_SESSION['sales_tax'];

			// Check if this order requires freight or LTL / Freight Carrier was selected as a shipment method
			$freight_shipment = ( $ObjShoppingCart->requiresFreight() ||  mb_strtolower($_SESSION['shipping_services_pre']) == 'ltl / freight carrier' ? 1 : 0 );

			// If the admin chose a referrer, overwrite the automatic referrer detection
			if ($_SESSION['admin'] === true AND isset($objCheckout->referrer) AND $objCheckout->referrer != "") {
				$_SESSION['search_engine'] = $objCheckout->referrer;
			}

			// Default order status is 2 (processing), unless a PayPal payment is pending (1)
			$order_status = 2;

			// If PayPal returns a pending response, grab the reason and associate it with a reason code
			// from bs_paypal_pending_reasons. Otherwise, set this to zero
			if (mb_strtolower($paypal_response['pendingReason']) != 'none' && mb_strtolower($paypal_response['pendingReason']) != NULL && $paypal_response['pendingReason'] != '' && mb_strtolower($paypal_response['pendingReason']) != 'authorization') {

				// Grab the reason code from the bs_paypal_pending_reasons table
				$sql = Connection::getHandle()->prepare("SELECT id FROM bs_paypal_pending_reasons WHERE reason = ?");
				$sql->execute(array($paypal_response['pendingReason']));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				// The pending reason is the ID of the reason row
				$pending_status = $row['id'];

				// Set the order to pending
				$order_status = 1;

				// Otherwise, the order is not pending
			} else {
				$pending_status = 0;
			}

			// Grab the shipping phone number
			$shipPhone = $objCheckout->shipping_address['phone'];

			// Check the billing phone number level, and generate a unique invalid phone number if the one supplied was unusable
			if ($level['billing_level'] == 1) {
				$billPhone = $this->phoneFormat($objCheckout->billing_address['phone']);
			} else {
				$billPhone = $this->getUniqueInvalidPhoneNumber();
			}

			// Check the shipping phone number level, and generate a unique invalid phone number if the one spplied was unusable
			if ($level['shipping_level'] == 1) {
				$shipPhone = $this->phoneFormat($objCheckout->shipping_address['phone']);
			} else {
				$shipPhone = $this->getUniqueInvalidPhoneNumber();
			}

			// Credit card
			if ($payment_method == 'creditcard') {

				$ccType = $objCheckout->ccType;
				$stlen = strlen($objCheckout->creditcard['card']);
				$minfour = ($stlen - 4);
				$last_four = substr($objCheckout->creditcard['card'], $minfour, $stlen);
				$year = $objCheckout->creditcard['year'];
				$month = $objCheckout->creditcard['month'];
				$card_expire_date = $month . '/' . $year;

				// Brimar Net30
			} else if ($payment_method == 'net30') {

				$ccType = $objCheckout->ccType;
				$stlen = strlen($objCheckout->net30['card']);
				$minfour = ($stlen - 4);
				$last_four = substr($objCheckout->net30['card'], $minfour, $stlen);
				$card_expire_date = NULL;

				// PayPal
			} else if ($payment_method == 'paypal') {

				$ccType = 'Paypal';
				$last_four = NULL;
				$card_expire_date = NULL;

			}

			// Newsletter flag
			$newsletter_flag = $objCheckout->news_letter;

			// Shipping address info
			$properties['address1'] = $objCheckout->shipping_address['address1'];
			$properties['address2'] = $objCheckout->shipping_address['address2'];
			$properties['city']     = $objCheckout->shipping_address['city'];
			$properties['state']    = $objCheckout->shipping_address['state'];
			$properties['zipcode']  = $objCheckout->shipping_address['zipcode'];
			$properties['country']  = $objCheckout->shipping_address['country'];

			// Check the shipping carrier, pass the address to that carrier's API for address verification
			// First case is for Fedex carrier
			if (mb_strtolower($objCheckout->shipping['carrier']) == 'fedex') {

				// Instantiate a FedEx Address and pass in our address properties
				$fedex = new FedExAddress($properties);

				// Validate the address
				$fedex_address = $fedex->validateAddress();

				// Check the type of address (residential or business)
				if(mb_strtolower($fedex_address['residential']) == 'business')
					$residential = 'Business Address';
				else
					$residential = 'Residential Address';

				// The carrier is UPS
			} else if (mb_strtolower($objCheckout->shipping['carrier']) == 'ups') {

				//UPS will not validate an international address. Skip those and fall back to business
				if (mb_strtolower($objCheckout->shipping_address['country']) == 'us' || mb_strtolower($objCheckout->shipping_address['country']) == 'pr') {

					// Instantiate a UPS address object
					$objUpsAddress = new UpsAddress();

					// Pass the customer's shipping address info to UPS for validation
					$objUpsAddress->setAddress1($objCheckout->shipping_address['address1']);
					$objUpsAddress->setAddress2($objCheckout->shipping_address['address2']);
					$objUpsAddress->setCity($objCheckout->shipping_address['city']);
					$objUpsAddress->setState($objCheckout->shipping_address['state']);
					$objUpsAddress->setZip($objCheckout->shipping_address['zip']);
					$objUpsAddress->setCountry($objCheckout->shipping_address['country']);

					// Grab the address validation response
					$objUpsAddress->getResponse();

					// Grab the type code for what type of address this is
					$address_type_code = $objUpsAddress->type;

					// Based on the code, we know that this is a residential or business address
					if($address_type_code == 2)
						$residential = 'Residential Address';
					else
						$residential = 'Business Address';

					// If the address was international we have no way to validate it and have to assume it's a business address
				} else {
					$residential = 'Business Address';
				}

				// Our backup case. If we couldn't figure out what the address was, we'll assume it's business
			} else {
				$residential = 'Business Address';
			}

			// Calculate pickup / shipping date based on Shipment method
			if ( mb_strtolower($_SESSION['shipping_services_pre']) == 'customer pickup' ) {

				$pickup_date = $ObjShoppingCart->getEstimatedDate(1);

			} else if ( mb_strtolower($_SESSION['shipping_services_pre']) == 'ltl / freight carrier' && !$ObjShoppingCart->requiresFreight() ){

				$pickup_date = $ObjShoppingCart->getEstimatedDate(Settings::getSettingValue('freightdelay'));

			} else {

				//get estimated pickup date based on items from cart
				$pickup_date = $ObjShoppingCart->getEstimatedDate();

			}

			$shipping_pickup_estimate = date("Y-m-d",strtotime($pickup_date['estimated_date']) );

			// Store our delivery estimate in a variable
			$shipping_delivery_estimate = date("Y-m-d",$_SESSION['shipping_arrival_date'] );

			// Determine whether or not proofs were requested.
			$proofsRequested = $ObjShoppingCart->proofsRequested() || mb_strpos(mb_strtolower($objCheckout->special_comments), 'proof') !== FALSE || mb_strpos(mb_strtolower($objCheckout->admin_comments), 'proof') !== FALSE || mb_strpos(mb_strtolower($objCheckout->expedited_shipping), 'proof') !== FALSE;

			// Add a delay if proofing was requested.
			if ( $proofsRequested ) {

				// Get the proofing delay from the database.
				$proofingDelay = (int) Settings::getSettingValue('proofingdelay');

				// Determine which days the carrier can deliver the shipment so we can add a delay and recalculate the delivery date.
				if ( $freight_shipment || mb_strtolower($_SESSION['shipping_services_pre']) == 'customer pickup' ) {

					// Assume Monday-Friday.
					$carrier_days = array( FALSE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE);

				} else {

					// Query the databse.
					$sql = Connection::getHandle()->prepare("SELECT * FROM bs_shipping_config WHERE carrier = ? AND name = ?");
					$sql->execute(array(
									  $_SESSION['shipping_carrier_pre'],
									  $_SESSION['shipping_services_pre']
								  ));
					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// Build an array of bools for each day of the week.
					$carrier_days = array(
						(bool) $row['sunday'],
						(bool) $row['monday'],
						(bool) $row['tuesday'],
						(bool) $row['wednesday'],
						(bool) $row['thursday'],
						(bool) $row['friday'],
						(bool) $row['saturday']
					);

				}

				// Get estimated pickup date based on items from cart.
				$shipping_pickup_estimate = $ObjShoppingCart->finalizeDate($shipping_pickup_estimate, $proofingDelay);
				$shipping_delivery_estimate = $ObjShoppingCart->finalizeDate($shipping_delivery_estimate, $proofingDelay, $carrier_days);

				// Format the dates so we can use them.
				$shipping_pickup_estimate = date("Y-m-d", strtotime($shipping_pickup_estimate['shipdate_formatted']) );
				$shipping_delivery_estimate = date("Y-m-d", strtotime($shipping_delivery_estimate['shipdate_formatted']) );

			}

			// Add 5 business days if the order is paypal and pending
			if ($pending_status > 0 && $payment_method == 'paypal') {

				// Grab the delivery days array for this shipping method so we can accurately predict the delivery date
				$sql = Connection::getHandle()->prepare("SELECT * FROM bs_shipping_config WHERE carrier = ? AND name = ?");
				$sql->execute(array($_SESSION['shipping_carrier_pre'], $_SESSION['shipping_services_pre']));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				// Build an array of bools for each day of the week
				$carrier_days = array(
					(bool) $row['sunday'],
					(bool) $row['monday'],
					(bool) $row['tuesday'],
					(bool) $row['wednesday'],
					(bool) $row['thursday'],
					(bool) $row['friday'],
					(bool) $row['saturday']
				);

				// Get the PayPal pending order delay from the database.
				$paypalPendingDelay = (int) Settings::getSettingValue('paypalpendingdelay');

				//get estimated pickup date based on items from cart
				$shipping_pickup_estimate = $ObjShoppingCart->finalizeDate($shipping_pickup_estimate, $paypalPendingDelay);
				$shipping_delivery_estimate = $ObjShoppingCart->finalizeDate($shipping_delivery_estimate, $paypalPendingDelay, $carrier_days);

				// Format the dates so we can use them
				$shipping_pickup_estimate = date("Y-m-d", strtotime($shipping_pickup_estimate['shipdate_formatted']) );
				$shipping_delivery_estimate = date("Y-m-d", strtotime($shipping_delivery_estimate['shipdate_formatted']) );

			}

			//get formatted shipping account if valid ship account entered
			$ObjShippingCharges->setShippingAccount($objCheckout->shipping['account']);
			$shipaccount = $ObjShippingCharges->ValidateShippingAccount();

			// If the shipping account is valid
			if ($shipaccount ){
				$shipping_account = $ObjShippingCharges->shipping_account;
			}

			$shipping_charges = ($freight_shipment ? 1.00 : (float) $_SESSION['shipping_charges_pre']);
			$shipping_services = ($freight_shipment ? 'Freight Shipment (Actual Cost TBD)' : $_SESSION['shipping_services_pre']);

			if($_SESSION['admin'] === true){
				$shipping_charges = $_SESSION['shipping_charges_pre'];
				$shipping_services = $_SESSION['shipping_services_pre'];
			}

			// Do the insert into bs_orders
			$sql_order = Connection::getHandle()->prepare("INSERT INTO bs_orders (
										cart_id,customers_id,customers_email,billing_first_name,billing_last_name,billing_name,
										billing_company,billing_street_address,billing_suburb,
										billing_city,billing_postcode,billing_state,
										billing_country,billing_phone,billingphone_display,billing_fax,
										shipping_first_name, shipping_last_name, shipping_name, shipping_company,
										shipping_street_address, shipping_suburb, shipping_city, shipping_country, shipping_postcode,
										shipping_state, shipping_phone, shipping_fax, shipping_address_type,
										comments,tax_exempt,sales_tax,
										freight_shipment,shipping_services,shipping_charges,shipping_carrier,
										shipping_arrival_estimate,shipping_pickup_estimate,shipping_account,
										coupon_number,coupon_value,total_amount,
										order_no,paypal_order_no,date_purchased,last_modified,orders_status, paypal_pending_status, auth_trans_id, auth_trans_note,
										ip_address,Referrer,refpage,browser,
										os,search_engine,keyword,admin,adminID,adminComment,purchase_order,tag_job,
										expedited_shipping, proofs_requested,
										ccType,lastFourCcNum,ccExpire,purchased_date_created,newsletter_flag )
										VALUES (
										:cart_id,:customers_id,:customers_email,:billing_first_name,:billing_last_name,:billing_name,
										:billing_company,:billing_street_address,:billing_suburb,
										:billing_city,:billing_postcode,:billing_state,
										:billing_country,:billing_phone,:billingphone_display,:billing_fax,
										:shipping_first_name, :shipping_last_name, :shipping_name, :shipping_company,
										:shipping_street_address, :shipping_suburb, :shipping_city, :shipping_country, :shipping_postcode,
										:shipping_state, :shipping_phone, :shipping_fax, :shipping_address_type,
										:comments,:tax_exempt,:sales_tax,
										:freight_shipment,:shipping_services,:shipping_charges,:shipping_carrier,:shipping_arrival_estimate,
										:shipping_pickup_estimate,:shipping_account,:coupon_number,:coupon_value,:total_amount,
										:order_no,:paypal_order_no,NOW(),NOW(),:orders_status, :paypal_pending_status, :auth_trans_id, :auth_trans_note,
										:ip_address,:Referrer,:refpage,:browser,
										:os,:search_engine,:keyword,:admin,:adminID,:adminComment,:purchase_order,:tag_job,
										:expedited_shipping, :proofs_requested, :cctype,:lastFourCcNum,:expire,NOW(),:newsletter_flag
										)
									");
			$sql_order->execute(array(
									":cart_id"                   => $ObjShoppingCart->id,
									":customers_id"              => $objUser->getCID(),
									":customers_email"           => $objCheckout->email,
									":billing_first_name"        => ($payment_method == 'paypal' ? $objCheckout->shipping_address['firstname'] : $objCheckout->billing_address['firstname']),
									":billing_last_name"         => ($payment_method == 'paypal' ? $objCheckout->shipping_address['lastname'] : $objCheckout->billing_address['lastname']),
									":billing_name"              => ($payment_method == 'paypal' ? $objCheckout->shipping_address['firstname'] . ' ' . $objCheckout->shipping_address['lastname'] : $objCheckout->billing_address['firstname'].' '.$objCheckout->billing_address['lastname']),
									":billing_company"           => ($payment_method == 'paypal' ? $objCheckout->shipping_address['company'] : $objCheckout->billing_address['company']),
									":billing_street_address"    => ($payment_method == 'paypal' ? $objCheckout->shipping_address['address1'] : $objCheckout->billing_address['address1']),
									":billing_suburb"            => ($payment_method == 'paypal' ? $objCheckout->shipping_address['address2'] : $objCheckout->billing_address['address2']),
									":billing_city"              => ($payment_method == 'paypal' ? $objCheckout->shipping_address['city'] : $objCheckout->billing_address['city']),
									":billing_postcode"          => ($payment_method == 'paypal' ? $objCheckout->shipping_address['zip'] : $objCheckout->billing_address['zip']),
									":billing_state"             => ($payment_method == 'paypal' ? $objCheckout->shipping_address['state'] : $objCheckout->billing_address['state']),
									":billing_country"           => ($payment_method == 'paypal' ? $objCheckout->shipping_address['country'] : $objCheckout->billing_address['country']),
									":billing_phone"             => ($payment_method == 'paypal' ? $shipPhone : $billPhone),
									":billingphone_display"      => ($payment_method == 'paypal' ? $objCheckout->shipping_address['phone'] : $objCheckout->billing_address['phone']),
									":billing_fax"               => ($payment_method == 'paypal' ? $objCheckout->shipping_address['fax'] : $objCheckout->billing_address['fax']),
									":shipping_first_name"       => $objCheckout->shipping_address['firstname'],
									":shipping_last_name"        => $objCheckout->shipping_address['lastname'],
									":shipping_name"             => $objCheckout->shipping_address['firstname'] . ' ' . $objCheckout->shipping_address['lastname'],
									":shipping_company"          => $objCheckout->shipping_address['company'],
									":shipping_street_address"   => $objCheckout->shipping_address['address1'],
									":shipping_suburb"           => $objCheckout->shipping_address['address2'],
									":shipping_city"             => $objCheckout->shipping_address['city'],
									":shipping_postcode"         => $objCheckout->shipping_address['zip'],
									":shipping_state"            => $objCheckout->shipping_address['state'],
									":shipping_country"          => $objCheckout->shipping_address['country'],
									":shipping_phone"            => $objCheckout->shipping_address['phone'],
									":shipping_fax"              => $objCheckout->shipping_address['fax'],
									":shipping_address_type"     => $residential,
									":comments"                  => $objCheckout->special_comments,
									":tax_exempt"                => $objCheckout->tax_exempt,
									":sales_tax"                 => (float) $sales_tax,
									":freight_shipment"          => $freight_shipment,
									":shipping_services"         => $shipping_services,
									":shipping_charges"          => $shipping_charges,
									":shipping_carrier"          => ($freight_shipment ? '' : (string) $_SESSION['shipping_carrier_pre']),
									":shipping_arrival_estimate" => $shipping_delivery_estimate,
									":shipping_pickup_estimate"  => $shipping_pickup_estimate,
									":shipping_account"          => $shipping_account,
									":coupon_number"             => $_SESSION['coupon_number'],
									":coupon_value"              => (float) $_SESSION['coupon_value'],
									":total_amount"              => (float) $order_total,
									":order_no"                  => $order_number,
									":paypal_order_no"           => $paypal_response['transactionId'],
									":orders_status"             => $order_status,
									":paypal_pending_status"     => $pending_status,
									":auth_trans_id"             => ($payment_method == 'paypal' ? $paypal_response['transactionId'] : $authorization['id']),
									":auth_trans_note"           => $authorization['note'],
									":ip_address"                => isset($_SESSION['ip_address']) ? $_SESSION['ip_address'] : '',
									":Referrer"                  => $_SESSION['Referrer'],
									":refpage"                   => $_SESSION['refpage'],
									":browser"                   => $_SESSION['browser'],
									":os"                        => $_SESSION['os'],
									":search_engine"             => $_SESSION['search_engine'],
									":keyword"                   => $_SESSION['keyword'],
									":admin"                     => $objCheckout->admin,
									":adminID"                   => $objCheckout->adminID,
									":adminComment"              => $objCheckout->admin_comments,
									":purchase_order"            => $objCheckout->purchase_order,
									":tag_job"                   => $objCheckout->tag_job,
									":expedited_shipping"       => $objCheckout->expedited_shipping,
									":proofs_requested"          => $proofsRequested ? 1 : 0,
									":cctype"                    => (!empty($card_type) ? $card_type : $ccType),
									":lastFourCcNum"             => $last_four,
									":expire"                    => $card_expire_date,
									":newsletter_flag"           => is_null($newsletter_flag) ? 0 : $newsletter_flag
								));


			// Grab the order id from the insert
			$order_id = Connection::getHandle()->lastInsertId();

			// Check for success
			if ($order_id > 0) {

				// Check if this customer has a row in the customers_info table
				$sql_customer_info=Connection::getHandle()->prepare("SELECT * FROM bs_customer_info WHERE customers_id=:cid");
				$sql_customer_info->execute(array(":cid"=>$objUser->getCID()));
				$customer_count=$sql_customer_info->rowCount();
				$billing_name=$this->bFirstName.' '.$this->bLastName;
				$shipping_name=$this->sFirstName.' '.$this->sLastName;
				$session_id=session_id();

				// Get the customer ID, taking into account whether this is an admin checkout or not
				if (isset($_SESSION['adminID']))
					$cid=$_SESSION['adminID'];
				else
					$cid=$objUser->getCID();

				// There was already a row in the table. Update it
				if($customer_count>0) {

					$customer_info = $sql_customer_info->fetch(PDO::FETCH_ASSOC);
					$sql_customer_update = Connection::getHandle()->prepare('UPDATE bs_customer_info
				                                            SET session_id              = :session_id,
				                                                customers_email         = :customer_email,
				                                                billing_first_name      = :first_name,
				                                                billing_last_name       = :last_name,
				                                                billing_name            = :name,
				                                                billing_company         = :company,
				                                                billing_street_address  = :address1,
				                                                billing_suburb          = :address2,
				                                                billing_city            = :city,
				                                                billing_postcode        = :zipcode,
				                                                billing_state           = :state,
				                                                billing_country         = :country,
				                                                billing_phone           = :phone,
				                                                billing_fax             = :fax,
				                                                shipping_first_name     = :shipping_first_name,
				                                                shipping_last_name      = :shipping_last_name,
				                                                shipping_name           = :shipping_name,
				                                                shipping_company        = :shipping_company,
				                                                shipping_street_address = :shipping_address1,
				                                                shipping_suburb         = :shipping_address2,
				                                                shipping_city           = :shipping_city,
				                                                shipping_postcode       = :shipping_zipcode,
				                                                shipping_state          = :shipping_state,
				                                                shipping_country        = :shipping_country,
				                                                shipping_phone          = :shipping_phone,
				                                                tax_exempt              = :tax_exempt,
				                                                shipping_carrier        = :shipping_carrier,
				                                                shipping_services       = :shipping_services,
				                                                shipping_charges        = :shipping_charges,
				                                                shipping_address_type   = :shipping_address_type,
				                                                date_purchased          = NOW(),
				                                                comments                = :comment
				                                            WHERE customers_id = :cid');

					$sql_customer_update->execute(array(
													  ":session_id"            => session_id(),
													  ":customer_email"        => $objCheckout->email,
													  ":first_name"            => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['firstname']),
													  ":last_name"             => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['lastname']),
													  ":name"                  => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['firstname'].' '.$objCheckout->billing_address['lastname']),
													  ":company"               => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['company']),
													  ":address1"              => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['address1']),
													  ":address2"              => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['address2']),
													  ":city"                  => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['city']),
													  ":zipcode"               => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['zip']),
													  ":state"                 => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['state']),
													  ":country"               => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['country']),
													  ":phone"                 => $billPhone,
													  ":fax"                   => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['fax']),
													  ":shipping_first_name"   => $objCheckout->shipping_address['firstname'],
													  ":shipping_last_name"    => $objCheckout->shipping_address['lastname'],
													  ":shipping_name"         => $objCheckout->shipping_address['firstname'] . ' ' . $objCheckout->shipping_address['lastname'],
													  ":shipping_company"      => $objCheckout->shipping_address['company'],
													  ":shipping_address1"     => $objCheckout->shipping_address['address1'],
													  ":shipping_address2"     => $objCheckout->shipping_address['address2'],
													  ":shipping_city"         => $objCheckout->shipping_address['city'],
													  ":shipping_zipcode"      => $objCheckout->shipping_address['zip'],
													  ":shipping_state"        => $objCheckout->shipping_address['state'],
													  ":shipping_country"      => $objCheckout->shipping_address['country'],
													  ":shipping_phone"        => $objCheckout->shipping_address['phone'],
													  ":tax_exempt"            => $objCheckout->tax_exempt,
													  ":shipping_carrier"      => (string) $_SESSION['shipping_carrier_pre'],
													  ":shipping_services"     => $_SESSION['shipping_services_pre'],
													  ":shipping_charges"      => (float) $_SESSION['shipping_charges_pre'],
													  ":shipping_address_type" => $residential,
													  ":comment"               => $objCheckout->comments,
													  ":cid"                   => $cid
												  ));
					$customer_info_id = $customer_info['order_id'];

					// There is not yet a row in the customers_info table. Insert one.
				} else {

					$sql_customer = Connection::getHandle()->prepare('INSERT INTO bs_customer_info (
				                                         session_id,
				                                         customers_id,
				                                         customers_email,
				                                         billing_first_name,
				                                         billing_last_name,
				                                         billing_name,
				                                         billing_company,
				                                         billing_street_address,
				                                         billing_suburb,
				                                         billing_city,
				                                         billing_postcode,
				                                         billing_state,
				                                         billing_country,
				                                         billing_phone,
				                                         billing_fax,
				                                         shipping_first_name,
				                                         shipping_last_name,
				                                         shipping_name,
				                                         shipping_company,
				                                         shipping_street_address,
				                                         shipping_suburb,
				                                         shipping_city,
				                                         shipping_postcode,
				                                         shipping_state,
				                                         shipping_country,
				                                         shipping_phone,
				                                         shipping_address_type,
				                                         tax_exempt,
				                                         shipping_carrier,
				                                         shipping_services,
				                                         shipping_charges,
				                                         coupon_number,
				                                         coupon_value,
				                                         total_amount,
				                                         order_no,
				                                         date_purchased,
				                                         orders_status,
				                                         ip_address
				                                     ) VALUES (
				                                         :session_id,
				                                         :cid,
				                                         :c_email,
				                                         :first_name,
				                                         :last_name,
				                                         :name,
				                                         :company,
				                                         :street_address,
				                                         :suburb,
				                                         :city,
				                                         :postcode,
				                                         :state,
				                                         :country,
				                                         :phone,
				                                         :fax,
				                                         :sfirst_name,
				                                         :slast_name,
				                                         :sname,
				                                         :scompany,
				                                         :sstreet_address,
				                                         :ssuburb,
				                                         :scity,
				                                         :spostcode,
				                                         :sstate,
				                                         :scountry,
				                                         :sphone,
				                                         :address_type,
				                                         :tax_exempt,
				                                         :shipping_carrier,
				                                         :shipping_services,
				                                         :shipping_charges,
				                                         :coupon_number,
				                                         :coupon_value,
				                                         :total_amount,
				                                         :order_no,
				                                         NOW(),
				                                         :orders_status,
				                                         :ip
				                                     )');
					if (!$sql_customer->execute(array(
											   ":session_id"        => session_id(),
											   ":cid"               => $objUser->getCID(),
											   ":c_email"           => is_null($objCheckout->email) ? 0 : $objCheckout->email,
											   ":first_name"        => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['firstname']),
											   ":last_name"         => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['lastname']),
											   ":name"              => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['firstname'].' '.$objCheckout->billing_address['lastname']),
											   ":company"           => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['company']),
											   ":street_address"    => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['address1']),
											   ":suburb"            => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['address2']),
											   ":city"              => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['city']),
											   ":postcode"          => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['zip']),
											   ":state"             => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['state']),
											   ":country"           => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['country']),
											   ":phone"             => $billPhone,
											   ":fax"               => ($payment_method == 'paypal' ? NULL : $objCheckout->billing_address['fax']),
											   ":sfirst_name"       => $objCheckout->shipping_address['firstname'],
											   ":slast_name"        => $objCheckout->shipping_address['lastname'],
											   ":sname"             => $objCheckout->shipping_address['firstname'] . ' ' . $objCheckout->shipping_address['lastname'],
											   ":scompany"          => $objCheckout->shipping_address['company'],
											   ":sstreet_address"   => $objCheckout->shipping_address['address1'],
											   ":ssuburb"           => $objCheckout->shipping_address['address2'],
											   ":scity"             => $objCheckout->shipping_address['city'],
											   ":spostcode"         => $objCheckout->shipping_address['zip'],
											   ":sstate"            => $objCheckout->shipping_address['state'],
											   ":scountry"          => $objCheckout->shipping_address['country'],
											   ":sphone"            => $objCheckout->shipping_address['phone'],
											   ":address_type"      => $residential,
											   ":tax_exempt"        => $objCheckout->tax_exempt,
											   ":shipping_carrier"  => (string) $_SESSION['shipping_carrier_pre'],
											   ":shipping_services" => $_SESSION['shipping_services_pre'],
											   ":shipping_charges"  => (float) $_SESSION['shipping_charges_pre'],
											   ":coupon_number"     => !empty($_SESSION['coupon_number']) ? $_SESSION['coupon_number'] : '',
											   ":coupon_value"      => (float) $_SESSION['coupon_value'],
											   ":total_amount"      => (float) $order_total,
											   ":order_no"          => $order_number,
											   ":orders_status"     => $order_status,
											   ":ip"                => isset($_SESSION['ip_address']) ? $_SESSION['ip_address'] : ''
										   ))) {
                        echo "<pre>".print_r(Connection::getHandle()->errorInfo(), 1)."</pre>";
                    }

					$customer_info_id = Connection::getHandle()->lastInsertId();

				}


                if ( $payment_method != 'paypal' ) {
                    $Username = $objCheckout->billing_address['firstname'] . ' ' . $objCheckout->billing_address['lastname'];
                } else {
                    $Username = User::getEmailAddressById($_SESSION['CID']);
                }

                $_SESSION['Username'] = $Username;

				// The customer's ID
				$cid = $objUser->getCID();

				// Get the default billing and shipping address IDs for this customer
				$sql = Connection::getHandle()->prepare("SELECT public_id AS default_billing_id FROM bs_customer_addresses WHERE default_billing = 1 AND cid = ?
										UNION ALL
										SELECT public_id AS default_shipping_id FROM bs_customer_addresses WHERE default_shipping = 1 AND cid = ? ");
				$sql->execute(array($cid, $cid));
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				// Whether or not to set new addresses to default billing, shipping, both, or none
				$shipping = ($row['default_shipping_id'] > 0 ? 0 : 1);
				$billing = ($row['default_billing_id'] > 0 ? 0 : 1);

				// Create new addresses for the customer if they did not already have them saved
				if ($payment_method == 'paypal') {

					$sql = Connection::getHandle()->prepare("SELECT public_id AS id
										FROM bs_customer_addresses
										WHERE company = :company,
										first_name = :first_name,
										last_name = :last_name,
										street_address = :street_address,
										suburb = :suburb,
										postcode = :postcode,
										city = :city,
										state = :state,
										country = :country,
										phone = :phone,
										fax = :fax");
					$sql->execute(array(":first_name" => $objCheckout->shipping_address['firstname'],
										":last_name" => $objCheckout->shipping_address['lastname'],
										":street_address" => $objCheckout->shipping_address['address1'],
										":suburb" => $objCheckout->shipping_address['address2'],
										":postcode" => $objCheckout->shipping_address['zip'],
										":city" => $objCheckout->shipping_address['city'],
										":state" => $objCheckout->shipping_address['state'],
										":country" => $objCheckout->shipping_address['country'],
										":phone" => $objCheckout->shipping_address['phone'],
										":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : ''
                        )
                    );

					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// If the address does not exist
					if (!($row['id'] > 0)) {

						$sql = Connection::getHandle()->prepare("INSERT INTO bs_customer_addresses
												(company, first_name, last_name,
												street_address, suburb, postcode,
												city, state, country, phone,
												fax, default_shipping, default_billing)
												VALUES
												(:company, :first_name, :last_name,
												:street_address, :suburb, :postcode,
												:city, :state, :country, :phone,
												:fax, :default_shipping, default_billing)");
						$sql->execute(array(":first_name" => $objCheckout->shipping_address['firstname'],
											":last_name" => $objCheckout->shipping_address['lastname'],
											":street_address" => $objCheckout->shipping_address['address1'],
											":suburb" => $objCheckout->shipping_address['address2'],
											":postcode" => $objCheckout->shipping_address['zip'],
											":city" => $objCheckout->shipping_address['city'],
											":state" => $objCheckout->shipping_address['state'],
											":country" => $objCheckout->shipping_address['country'],
											":phone" => $objCheckout->shipping_address['phone'],
											":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : '',
											":default_shipping" => isset($shipping) ? $shipping : 0,
											":default_billing" => isset($billing) ? $billing : 0,
                            )

                        );

					}

					// Not PayPal
				} else {

					// Shipping
					$sql = Connection::getHandle()->prepare("SELECT public_id AS id
						FROM bs_customer_addresses
						WHERE company = :company AND
						first_name = :first_name AND
						last_name = :last_name AND
						street_address = :street_address AND
						suburb = :suburb AND
						postcode = :postcode AND
						city = :city AND
						state = :state AND
						country = :country AND
						phone = :phone AND
						fax = :fax");
					$sql->execute(array(":company" => $objCheckout->shipping_address['company'],
										":first_name" => $objCheckout->shipping_address['firstname'],
										":last_name" => $objCheckout->shipping_address['lastname'],
										":street_address" => $objCheckout->shipping_address['address1'],
										":suburb" => $objCheckout->shipping_address['address2'],
										":postcode" => $objCheckout->shipping_address['zip'],
										":city" => $objCheckout->shipping_address['city'],
										":state" => $objCheckout->shipping_address['state'],
										":country" => $objCheckout->shipping_address['country'],
										":phone" => $objCheckout->shipping_address['phone'],
										":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : ''
                        )

                    );

					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// If the address does not exist
					if (!($row['id'] > 0)) {

						$sql = Connection::getHandle()->prepare("INSERT INTO bs_customer_addresses
												(company, first_name, last_name,
												street_address, suburb, postcode,
												city, state, country, phone,
												fax, default_shipping, default_billing)
												VALUES
												(:company, :first_name, :last_name,
												:street_address, :suburb, :postcode,
												:city, :state, :country, :phone,
												:fax, :default_shipping, default_billing)");

						$sql->execute(array(":company" => $objCheckout->shipping_address['company'],
											":first_name" => $objCheckout->shipping_address['firstname'],
											":last_name" => $objCheckout->shipping_address['lastname'],
											":street_address" => $objCheckout->shipping_address['address1'],
											":suburb" => $objCheckout->shipping_address['address2'],
											":postcode" => $objCheckout->shipping_address['zip'],
											":city" => $objCheckout->shipping_address['city'],
											":state" => $objCheckout->shipping_address['state'],
											":country" => $objCheckout->shipping_address['country'],
											":phone" => $objCheckout->shipping_address['phone'],
											":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : '',
                                            ":default_shipping" => isset($shipping) ? $shipping : 0,
                            )

                        );

						$insert_id = Connection::getHandle()->lastInsertId();

					}


					// Billing
					$sql = Connection::getHandle()->prepare("SELECT public_id AS id
						FROM bs_customer_addresses
						WHERE company = :company AND
						first_name = :first_name AND
						last_name = :last_name AND
						street_address = :street_address AND
						suburb = :suburb AND
						postcode = :postcode AND
						city = :city AND
						state = :state AND
						country = :country AND
						phone = :phone AND
						fax = :fax");

					$sql->execute(array(":company" => $objCheckout->billing_address['company'],
									  	":first_name" => $objCheckout->billing_address['firstname'],
										":last_name" => $objCheckout->billing_address['lastname'],
										":street_address" => $objCheckout->billing_address['address1'],
										":suburb" => $objCheckout->billing_address['address2'],
										":postcode" => $objCheckout->billing_address['zip'],
										":city" => $objCheckout->billing_address['city'],
										":state" => $objCheckout->billing_address['state'],
										":country" => $objCheckout->billing_address['country'],
										":phone" => $objCheckout->billing_address['phone'],
										":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : ''
                        )

                    );

					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// If the address does not exist
					if (!($row['id'] > 0)) {

						$sql = Connection::getHandle()->prepare("INSERT INTO bs_customer_addresses
												(company, first_name, last_name,
												street_address, suburb, postcode,
												city, state, country, phone,
												fax, default_shipping, default_billing)
												VALUES
												(:company, :first_name, :last_name,
												:street_address, :suburb, :postcode,
												:city, :state, :country, :phone,
												:fax, :default_shipping, :default_billing)");

						$sql->execute(array(":company" => $objCheckout->billing_address['company'],
										  	":first_name" => $objCheckout->billing_address['firstname'],
											":last_name" => $objCheckout->billing_address['lastname'],
											":street_address" => $objCheckout->billing_address['address1'],
											":suburb" => $objCheckout->billing_address['address2'],
											":postcode" => $objCheckout->billing_address['zip'],
											":city" => $objCheckout->billing_address['city'],
											":state" => $objCheckout->billing_address['state'],
											":country" => $objCheckout->billing_address['country'],
											":phone" => $objCheckout->billing_address['phone'],
											":fax" => isset($objCheckout->shipping_address['fax']) ? $objCheckout->shipping_address['fax'] : '',
											":default_shipping" => isset($objCheckout->billing_address['shipping']) ? $objCheckout->billing_address['shipping'] : 0,
											":default_billing" => isset($billing) ? $billing : 0));

					}


					// In case there was no default shipping or billing, but the addresses entered were the same so we only did one insert. We'll
					// have to update that one to reflect default billing as well.
					// Get the default billing and shipping address IDs for this customer
					$sql = Connection::getHandle()->prepare("SELECT public_id AS default_billing_id FROM bs_customer_addresses WHERE default_billing = 1 AND cid = ?");
					$sql->execute(array($cid));
					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// If there is not a default billing address, set the shipping address as default billing
					if (!($row['default_billing_id'] > 0)) {

						$sql = Connection::getHandle()->prepare("UPDATE bs_customer_addresses SET default_billing = 1 WHERE id = ?");
						$sql->execute(array($insert_id));

					}

				}

				// Build an array with the order id and the order number to return. We'll need both of these
				$order_array = array('order_id'     => $order_id,
									 'order_number' => $order_number);

				// Make sure we have an order id and order number
				if (!empty($order_array['order_id']) && !empty($order_array['order_number'])) {

					// Return our array
					return $order_array;

				} else {
					return false;
				}

			} else {
				return false;
			}


		}



		public function GetOrderEmail($orderno) {

			$order_data = $this->getOrderEmailContent($orderno);
			$ObjEmail = new Email();
			$mail_sent = $ObjEmail->sendOrderConfirmationEmail($order_data);

		}



		public function getOrderEmailContent($orderno) {

			// Instantiate our needed classes
			$ObjOrder=new Orders();
			$objCountry=new Countries();

			// Instantiate the cart
			$ObjShoppingCart = Cart::getCartFromOrderNumber($orderno);

			// Get the content for the email
			$order=$ObjOrder->GetOrderInvoiceEmail($orderno);


			$count=count($order);
			$track=new Page('tracking');
			$account=new Page('my-account');
			$home=new Page('home');
			$invoice=new Page('invoice');


			if($count>0){

				foreach($order as $key => $value)
				{
					$orderid=$value['orders_id'];
					$order_credit_card=$ObjOrder->GetCreditCard($orderid);
					foreach($order_credit_card as $key => $creditcardvalue){}
				}

				$orderno=$value['order_no'];
				$customer_email=$value['customers_email'];
				$order_date=$ObjOrder->OrderPurchasedDate($value['date_purchased']);

				$shipping_country=$objCountry->CountryCodeList($value["shipping_country"]);
				$billing_country=$objCountry->CountryCodeList($value["billing_country"]);

				$ship_method=$value['shipping_services'];
				$shipping_account = $value['shipping_account'];

				$purchase_order=$value['purchase_order'];
				if($value['coupon_value'])
					$coupon_value=$value['coupon_value'];
				else
					$coupon_value=0.00;
				$tag_job=$value['tag_job'];
				$shipping_charges=$value["shipping_charges"];
				$shipping_carrier = $value['shipping_carrier'];
				$salestax=$value['sales_tax'];
				$total_amount=$value['total_amount'];
				$exp_shipping=$value["expedited_shipping"];
				$proofsRequested = $value['proofs_requested'];
				$comments=$value["comments"];
				$track_num=$value['tracking_number'];
				if($value['freight_shipment'])
					$freight='Your order is being held until Customer Service contacts you. You will not be charged until shipping is arranged.';
				if($value['tax_exempt']=='Y')
					$tax_exempt='Please fax your tax exempt certificate to 800-279-6897 within 24 hours or sales tax will be charged to your order.';
				$guest = ($value['user_type']=='G') ? TRUE :FALSE;

				//shipping detail array
				$shipaddress=array(
					'ship_name'=>$value["shipping_name"],
					'shipping_company'=>$value["shipping_company"],
					'shipping_street_address'=>$value['shipping_street_address'],
					'shipping_suburb'=>$value["shipping_suburb"],
					'shipping_city'=>$value['shipping_city'],
					'shipping_state'=>$value['shipping_state'],
					'shipping_postcode'=>$value['shipping_postcode'],
					'shipping_country'=>$shipping_country["countries_name"],
					'shipping_phone'=>$value["shipping_phone"],
					'shipping_fax'=>$value["shipping_fax"],
					'shipping_country_code' => $value['shipping_country']
				);
				//billing detail array
				$billaddress=array(
					'bill_name'=>$value['billing_name'],
					'billing_company'=>$value['billing_company'],
					'billing_street_address'=>$value['billing_street_address'],
					'billing_suburb'=>$value['billing_suburb'],
					'billing_city'=>$value['billing_city'],
					'billing_state'=>$value['billing_state'],
					'billing_postcode'=>$value['billing_postcode'],
					'billing_country'=>$billing_country["countries_name"],
					'billing_phone'=>$value['billingphone_display'],
					'billing_country_code' => $value['billing_country']
				);

				if($value['ccType']=='Brimar'){
					$cctype="Net30 Account";
				}
				else{
					$cctype="Creditcard";
				}
				$cardtype=$value['ccType'];
				$cardNum=$value['lastFourCcNum'];
				$expiration=$value['ccExpire'];
				$total_amount=$value['total_amount'];

				//payment detail array
				$paymentinfo=array(
					'cctype'=>$ccType,
					'cardtype'=>$cardtype,
					'cardNum'=>$cardNum,
					'expiration'=>$expiration,
					'total_amount'=>$total_amount
				);


				// Loop through each item in the order
				foreach ($ObjShoppingCart->products AS $product) {

					$attributes = array();

					switch ($product->type) {

						case 'stock':
							$attributes = array();
							break;


						case 'builder':
							foreach($product->settings as $setting) {
								$label = $setting['builderLabel'];
								if ($setting['builderSettingDisplay'] == true) {
									if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
										$attributes[$label] = $setting['builderValueText'];
									}
								}
							}
							break;


						case 'flash':
							foreach($product->upcharges as $upcharge) {
								$attributes[$upcharge['type']] = $upcharge['name'];
							}
							break;


						case 'streetname':

							foreach ($product->getAdditionalDetails() as $key => $att_value) {
								$attributes[$key] = $att_value;
							}

							foreach ($product->upcharges AS $upcharge) {
								if (!empty($upcharge['name'])) {
									$attributes[$upcharge['type']] = $upcharge['name'];
								}
							}

							break;

					}

					$cart[]=array(
						'sku_code'=>$product->skuCode,
						'size'=>$product->size,
						'material'=>$product->materialDescription,
						'attribute'=>$attributes,
						'builder_attributes'=>$product->settings,
						'stock_custom'=>($product->isCustom ? 'C' : 'S'),
						'design_service'=>$product->designService,
						'product_type'=>$product->type,
						'comment'=>$product->comments,
						'quantity'=>$product->quantity,
						'price'=>$product->unitPrice,
						'total'=>$product->totalPrice,
						'file_name'=>(!empty($product->uploads[0]['hash']) ? TRUE : FALSE)
					);

				}


				if($ship_method!='Customer Pickup'){

					//Estimated availability date for customer pickup---- //
					$ObjShippingCharges = new ShippingCharges();
					$arrival_timestamp = $ObjShoppingCart->getEstimatedDate(1,null,$orderid);


					$arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
					$arrival_month = substr($arrival_timestamp['shipdate_formatted'], 4, 2);
					$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);

					$arrival_formatted = date("F jS, Y", mktime(0, 0, 0, $arrival_month, $arrival_date, $arrival_year));
				}

				// Natural business delay
				$ship_time = 1;

				// General production delay
				$ship_time +=  Settings::getSettingValue('productiondelay');

				$item_type = 1;

				// Add preset delay to produce custom items if applicable
				if($ObjShoppingCart->getCustomCount() > 0){
					$ship_time += Settings::getSettingValue('customproductdelay');
					$item_type = 2;
				}

				//final output array with all details
				$order_confirm=array(
					"orderno"=>$orderno,
					"name"=>$billaddress['bill_name'],
					"track_num"=>$track_num,
					"order_date"=>$order_date,
					"shipmethod"=>$ship_method,
					"shipping_account"=>$shipping_account,
					"shipaddress"=>$shipaddress,
					"billaddress"=>$billaddress,
					"customer_email"=>$customer_email,
					"paymentinfo"=>$paymentinfo,
					"cart"=>$cart,
					"purchase_order"=>$purchase_order,
					"tag_job"=>$tag_job,
					"subtotal"=>$ObjShoppingCart->getSubtotal(),
					"coupon_value"=>$coupon_value,
					"shippingcharge"=>$shipping_charges,
					"shipping_carrier"=>$shipping_carrier,
					"salestax"=>$salestax,
					"invoicetotal"=>$total_amount,
					'expedited_shipping'=>$exp_shipping,
					'proofsRequested' => $proofsRequested,
					"comments"=>$comments,
					"ship_time"=>$ship_time,
					"track_url"=>$track->getUrl(),
					"account_url"=>$account->getUrl(),
					"guest"=>$guest,
					"arrival_date"=>$arrival_formatted,
					"invoice_url"=>$invoice->getUrl().'?orderno='.$orderno,
					"freight"=>$freight,
					"tax_exempt"=>$tax_exempt,
					"home"=>$home->getUrl()
				);

				return $order_confirm;
			}

		}



		// Generates an order number, checks if it is in use,
		// and calls itself recursively if it is until we have a unique number
		public function generateOrderNumber() {

			// Generate Number
			$length = 9;
			$chars = '2346789';
			$lastcharpos = strlen($chars) - 1;
			$order_number = 'SS';

			// Loop through and add random characters onto the order number up to the specified length
			for ($i = 0; $i < $length; $i++) {
				$order_number .= $chars[mt_rand(0, $lastcharpos)];
			}

			//Check for duplicates
			$sql = Connection::getHandle()->prepare("SELECT COUNT(*) AS count FROM bs_orders WHERE order_no=:orderno");
			$sql->execute(array(":orderno"=>$order_number));
			$row = $sql->fetch(PDO::FETCH_ASSOC);

			// If there was a duplicate
			if($row['count'] > 0) {

				// If it was already in use, try the whole thing over again.
				$order_number = $this->generateOrderNumber();

			}

			// Return the order number.
			return $order_number;

		}



		public function phoneFormat($phone) {

			$patterns = array('![^0-9xX#+]!','!-+!','!-?[xX#]-?!','!#+!','!#!','!^-!','! !');
			$replacements = array("-","-",'#','#',' #','');
			$clean_num = preg_replace($patterns,$replacements,$phone);

			if(strpos($clean_num,'#')>0){

				$num=substr($clean_num,0,(strpos($clean_num,'#')));
				$num=str_replace('-','',$num);

			} else if((strpos($clean_num,'+')==0)){

				$str=strrev($clean_num);
				$str=str_replace('-','',$str);
				$num=strrev(substr($str,0,10));

			} else {

				$num=str_replace('-','',$clean_num);
			}

			if(strlen($num)>10){
				$num=$this->phoneFormat($num);
			}

			return $num;

		}



		public function getPaypalError($error_number) {

			$sql = Connection::getHandle()->prepare("SELECT brimar_error FROM bs_paypal_response_codes WHERE response_code = ?");
			$sql->execute(array($error_number));

			$row = $sql->fetch(PDO::FETCH_ASSOC);

			if (!empty($row['brimar_error'])) {
				return $row['brimar_error'];
			} else {
				return 'An unknown error has occurred. Please try again or call customer service toll free at 800-274-6271';
			}
		}



		public function generateInvalidPhoneNumber() {

			// Start with an invalid zero or one.
			$phoneNumber = (string) mt_rand(0, 1);

			// Add nine other digits.
			for ( $i = 0; $i < 9; $i++ ) {
				$phoneNumber .= mt_rand(0, 9);
			}

			return $phoneNumber;

		}



		public function getUniqueInvalidPhoneNumber() {

			// Generate an invalid phone number.
			$phoneNumber = $this->generateInvalidPhoneNumber();

			// Do so recursively until a unique invalid phone number is generated, then return that.
			return $this->checkIfPhoneNumberIsUnique($phoneNumber) ? $phoneNumber : $this->getUniqueInvalidPhoneNumber();

		}



		public function checkIfPhoneNumberIsUnique($phoneNumber) {

			// Assume the number is not unique by default.
			$unique = false;

			if ( !empty($phoneNumber) ) {

				// Check if the phone number has already been used in the database
				$sth = Connection::getHandle()->prepare('SELECT COUNT(`orders_id`) AS `count` FROM `bs_orders` WHERE `billing_phone` = ?');
				$sth->execute(array($phoneNumber));
				$results = $sth->fetch(PDO::FETCH_ASSOC);

				// Note if the number is unique.
				if ( $results['count'] == 0 ) {
					$unique = true;
				}

			}

			return $unique;

		}



		public static function orderRequiresFreight($orderId) {

			// Get the database handle.
			$dbh = Connection::getHandle();

			// Query the database.
			$sth = $dbh->prepare('SELECT freight_shipment FROM bs_orders WHERE orders_id = :orderId LIMIT 1');
			$sth->execute(array(
							  ':orderId' => $orderId
						  ));

			// Return as a boolean.
			return $sth->fetchColumn() === '1' ? TRUE : FALSE;

		}

		public static function checkIfCustomerOwnsOrder($customerId, $orderNumber) {

			$sth = Connection::getHandle()->prepare('SELECT COUNT(*) FROM bs_orders WHERE order_no = :orderNumber AND orders_status != 5 AND customers_id = :customerId');
			$sth->execute(array(
							  ':orderNumber' => $orderNumber,
							  ':customerId'  => $customerId
						  ));

			return intval($sth->fetchColumn()) > 0 ? TRUE : FALSE;

		}



    }

