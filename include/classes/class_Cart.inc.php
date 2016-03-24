<?php

class Cart {

	const FREIGHT_WEIGHT = 200;
	const BUFFER_SIZE = 10000;

	public $id;
	public $cartHash;
	public $saved;
	public $ordered;
	public $name;
	public $note;
	public $customerId;
	public $creationTime;
	public $modificationTime;
	public $products = array(); //AN array of objects (builder, flash, or streetname, or cartProducts)

	/**
	 * Cart constructor
	 * @param [string] $cartHash  [the hash for the cart to instantiate]
	 * @param [array]  $cartArray [Array of cart info so we do not need to run a query in the setProperties function]
	 * @param [bool] $saved     [true/false on saved/not saved]
	 * @param [bool] $ordered   [true/false on ordered/not ordered]
	 */
	public function __construct($cartHash = NULL, $cartArray = array(), $saved = null, $ordered = null) {

		// If no cart hash is passed in, generate a new cart hash and insert the hash into our cart table
		if (empty($cartHash)) {

			// Set the cart state
			$this->saved = $saved;
			$this->ordered = $ordered;
			$this->customerId = isset($_SESSION['CID']) ? $_SESSION['CID'] : NULL;
			$this->creationTime = date('Y-m-d H:i:s');
			$this->modificationTime = date('Y-m-d H:i:s');

			// Insert cart into bs_carts
			$this->id = (int) $this->insertCartRow();

			// Make sure we have an id
			if ($this->id > 0) {

				// Generate a new hash
				$this->assignHash();
			}

		} else {

			$this->cartHash = $cartHash;

			// Get the cart properties if they were not passed in, else set them
			if (empty($cartArray)) {

				$this->setProperties(null);

			} else {

				$this->setProperties($cartArray);
			}

		}

	}

    //todo: audited
	private function assignHash() {

		$sql = Connection::getHandle()->prepare("UPDATE bs_carts c JOIN (SELECT id FROM bs_cart_hashes
                                    WHERE in_use = 0 ORDER BY id ASC LIMIT 1 ) q LEFT JOIN bs_cart_hashes ch ON (ch.id = q.id)
									SET c.`hash_id` = q.`id`, ch.in_use = 1 WHERE c.id = ?");

		if ($sql->execute(array((int)$this->id))) {

			$propSql = Connection::getHandle()->prepare("SELECT ch.hash FROM bs_cart_hashes ch
											LEFT JOIN bs_carts c ON c.hash_id = ch.id WHERE c.id = ?");

			$propSql->execute(array((int)$this->id));
			$this->cartHash = $propSql->fetchColumn();

			$this->setProperties();

			return true;

		} else {

			return false;
		}
	}

    //todo: audited
	public function setProperties($data = null) {

		// Empty out the products array if there was one, and we will repopulate
		$this->products = array();

		// Check if a data array was passed in
		if (empty($data)) {

			// Select everything from the cart
			$sql = Connection::getHandle()->prepare(
					"SELECT c.id AS cart_id, c.name AS cart_name, c.note AS cart_note, c.customer_id AS customer_id,
					 c.creation_time AS creation_time, c.modification_time as modification_time,
                     ch.id AS hash_id, ch.hash AS hash, ch.in_use, cs.id as cart_sku_id, cs.sku_id as sku_id,  cs.product_id AS product_id,
                     cs.source_product_id, cs.source_product_recommendation_id, cs.source_accessory_family_product_id, cs.source_installation_accessory_id,
			         cs.source_landing_product_id, cs.source_subcategory_product_id,
                     cs.quantity AS quantity, cs.total_price AS total_price, cs.unit_price AS unit_price,
                     cs.comments AS comments, cs.creation_time AS cs_creation_time,
                     cs.custom_image_id AS custom_image_id, cs.design_id AS design_id, cs.design_service AS design_service,
                     cs.eps AS eps, cs.modification_time AS cs_modification_time, cs.pdf AS pdf,
                     cs.savings_percentage AS savings_percentage, cs.streetsign_background AS streetsign_background,
                     cs.streetsign_color AS streetsign_color, cs.streetsign_custom_copy1 AS streetsign_custom_copy1,
                     cs.streetsign_custom_copy2 AS streetsign_custom_copy2, cs.streetsign_font AS streetsign_font,
                     cs.streetsign_left_arrow AS streetsign_left_arrow, cs.streetsign_num AS streetsign_num,
                     cs.streetsign_position AS streetsign_position, cs.streetsign_prefix AS streetsign_prefix,
                     cs.streetsign_right_arrow AS streetsign_right_arrow, cs.streetsign_suffix AS streetsign_suffix,
                     pr.active AS active, pr.expiration AS expiration_date,
                     pr.search_thumbnail AS product_image, coalesce(pr.default_flash_tool_id , pr.default_streetsign_tool_id ) AS layout,
                     pr.default_product_name AS nickname, pr.product_number AS product_number, pr.default_subtitle AS subtitle,
                     s.name AS subcategory_name,
                     sd.requires_freight AS freight_shipping, sd.inventory AS inventory,
                     sd.limited_inventory AS limited_inventory, sd.material_id AS material_id, sd.size_id AS size_id,
                     sd.name AS sku_code, sd.fedex_shipping_surcharge AS dim_charges_fedex,
                     sd.ups_shipping_surcharge AS dim_charges_ups, sd.shipping_weight AS weight, sd.weight AS true_weight,
                     sd.laminate_id, sd.mounting_hole_arrangement_id, su.file_name AS upload_name, su.file_size AS upload_size,
                     sd.dedicated_package_count AS number_pkgs,
                     csd.builder_alignment AS builder_alignment,
                     csd.builder_alignment_setbyuser AS builder_alignment_setbyuser,
                     csd.builder_color AS builder_color, csd.builder_font AS builder_font,
                     csd.builder_font_setbyuser AS builder_font_setbyuser, csd.builder_fontsize AS builder_fontsize,
                     csd.builder_fontsize_setbyuser AS builder_fontsize_setbyuser,  csd.builder_label AS builder_label,
                     csd.builder_option_type AS builder_option_type,  csd.builder_setbyuser AS builder_setbyuser,
                     csd.builder_setting AS builder_setting, csd.builder_setting_display AS builder_setting_display,
                     csd.builder_subsetting AS builder_subsetting, csd.builder_value AS builder_value,
                     csd.builder_value_text AS builder_value_text, csd.id AS attribute_id,
                     tt.name AS tool_type, tt.id AS tool_type_id,
                     pc.custom_product_id AS custom_product_id, pc.custom_image AS custom_image,
                     d.hash AS design_hash, d.time AS design_time,
                     bu.converted_directory AS uploadConvertedDirectory,
                     bu.converted_filename AS uploadConvertedFilename, bu.hash AS uploadHash, bu.name AS uploadName,
                     bu.original_directory AS uploadOriginalDirectory, bu.original_filename AS uploadOriginalFilename,
                     su.file_type AS upload_type, su.id AS streetsign_upload_id,
                     sz.name AS size_name, prs.material_code, m.name,
					 IF(CHAR_LENGTH(mg.description) < 15, mg.description, mg.name) AS material_description
                     FROM bs_carts c
                     LEFT JOIN bs_cart_hashes ch ON (ch.id = c.hash_id)
                     LEFT JOIN bs_cart_skus cs ON (cs.cart_id = c.id)
                     LEFT JOIN bs_tool_types tt ON (cs.tool_type_id = tt.id)
                     LEFT JOIN bs_products pr ON (pr.id = cs.product_id)
                     LEFT JOIN bs_subcategories s ON (s.id = cs.subcategory_id AND s.active = 1)
                     LEFT JOIN bs_skus sd ON (sd.id = cs.sku_id)
					 LEFT JOIN bs_materials m ON(m.id = sd.material_id)
					 LEFT JOIN bs_material_groups mg ON(m.material_group_id = mg.id)
                     LEFT JOIN bs_sizes sz ON (sd.size_id = sz.id)
                     LEFT JOIN bs_pricing prs ON (prs.id = sd.pricing_id)
                     LEFT JOIN bs_cart_sku_data csd ON (csd.cart_sku_id = cs.id)
                     LEFT JOIN bs_product_custom pc ON (pc.custom_product_id = cs.custom_image_id)
                     LEFT JOIN bs_designs d ON (d.id = cs.design_id)
                     LEFT JOIN bs_builder_uploads bu ON (bu.`hash` = csd.builder_value)
                     LEFT JOIN bs_product_streetsign_tools pst ON (pst.product_id = cs.product_id)
                     LEFT JOIN bs_streetsign_uploads su ON (cs.streetsign_upload_id = su.id)
                     LEFT JOIN bs_streetsign_control sc ON (sc.streetsign_tool_id = pst.streetsign_tool_id)

                     -- LEFT JOIN bs_builder_laminates bl ON (bl.laminate_id = sd.laminate_id)

                     WHERE ch.hash = ?
                     ORDER BY cs.creation_time DESC, cs.id, csd.id ");


			if ($sql->execute(array($this->cartHash))) {

				// Loop through the data and save it to our data array

				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

					$data[] = $row;
				}
			}
		}

		// Flag for whether cart is empty or not
		$hasProducts = FALSE;

		// Group data by cart product id
		foreach($data AS $row){

			$productObjects[(int) $row['cart_sku_id']][] = $row;

		}

		// Loop through cart products and instantiate appropriate objects
		foreach($productObjects as $cartProductId => $productObject) {

			if($cartProductId > 0){

				$hasProducts = TRUE;

                if( isset($productObject[0]['tool_type']) ) {

                    switch ($productObject[0]['tool_type']) {

                        case 'stock':
                            $this->products[(int)$cartProductId] = new CartProductStock(
                                $cartProductId, $this, $productObject
                            );
                            break;

                        case 'flash':
                            $this->products[$cartProductId] = new CartProductFlash(
                                $cartProductId, $this, $productObject
                            );
                            break;

                        case 'streetname':
                            $this->products[$cartProductId] = new CartProductStreetname(
                                $cartProductId, $this, $productObject
                            );
                            break;

                        case 'builder':
                            $this->products[$cartProductId] = new CartProductBuilder(
                                $cartProductId, $this, $productObject
                            );
                            break;

                    }
                }
			}
		}
		// Set cart ID, name, and note if they exist
		$this->id = (empty($this->id) ? $row['cart_id'] : (int) $this->id);
		$this->name = (!empty($row['cart_name']) ? $row['cart_name'] : NULL);
		$this->note = (!empty($row['cart_note']) ? $row['cart_note'] : NULL);
		$this->customerId = $row['customer_id'];
		$this->creationTime = $row['creation_time'];
		$this->modificationTime = $row['modification_time'];

		if(!$hasProducts){
			$this->products = array();
		}

	}



	/**
	 * Sets the cart as saved
	 * @return [bool] [true/false on success/error]
	 */
	public function setSaved() {
		return $this->setState(true, false);
	}

	public function setOrdered() {

		if($this->setState(false, true)){

			// Update inventory levels in sku table
			$update_sql = Connection::getHandle()->prepare(

						"UPDATE bs_skus sk
							LEFT JOIN bs_product_skus psd ON sk.id = psd.sku_id

						INNER JOIN (
							SELECT id as cart_id, product_id, sku_id,SUM(quantity) AS total_quantity
							FROM bs_cart_skus GROUP BY id) cp ON sk.id = cp.sku_id AND psd.product_id = cp.product_id

							LEFT JOIN bs_products p ON (p.id = cp.product_id)
 							AND (psd.product_id = cp.product_id AND sk.id = psd.sku_id)

							LEFT JOIN bs_pricing pr ON (pr.id = sk.pricing_id) SET sk.inventory = sk.inventory - cp.total_quantity

						WHERE cp.cart_id = :cart_id");


			$update_sql->execute(array(':cart_id' => (int) $this->id));

			return true;

		} else {

			return false;
		}
	}



	/**
	 * Sets or changes the cart state
	 * @param [bool] $saved [The cart saved state]
	 * @param [bool] $ordered [The cart ordered state]
	 */
	private function setState($saved, $ordered) {

		// Update DB
		$sql = Connection::getHandle()->prepare("UPDATE bs_carts SET saved = :saved, ordered = :ordered WHERE id = :id");
		if ($sql->execute(array(":saved"   => $saved,
								":ordered" => $ordered,
								":id"      => (int) $this->id)) ) {

			// The DB update was a success, set the class properties
			$this->saved = $saved;
			$this->ordered = $ordered;

			return true;

		} else {

			return false;
		}

	}

	public function setCustomerId($cid){

		$set = FALSE;
		$sql = Connection::getHandle()->prepare("UPDATE bs_carts SET customer_id = :cid WHERE id = :id");

		if( $sql->execute(array(":cid" => $cid, ":id" => (int)$this->id)) ) {

			$et = TRUE;
		}

		return $set;

	}



	/**
	 * Delete this cart (remove it from bs_carts and delete all of its products from bs_cart_products)
	 * @return [bool] [success or failure]
	 */
	public function delete() {

		if ($this->emptyProducts()) {

			$sql = Connection::getHandle()->prepare("DELETE FROM bs_carts WHERE id = ?");
			if ($sql->execute(array((int)$this->id)) ) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}

	}



	/**
	 * Inserts a row into bs_carts with the current cart hash
	 * @return [bool] [true or false on success or failure]
	 */
	private function insertCartRow() {


		$sql = Connection::getHandle()->prepare("INSERT INTO bs_carts
									(name, note, hash_id, customer_id, saved, ordered, creation_time, modification_time)
									VALUES
									(:name, :note, :hash_id, :customer_id, :saved, :ordered, :creation_time, :modification_time)");

		if($sql->execute(array(":name" => (!empty($this->name) ? trim($this->name) : NULL),
							   ":note" => (!empty($this->note) ? trim($this->note) : NULL),
							   ":hash_id" => NULL,
							   ":customer_id" => (!empty($this->customerId) ? $this->customerId : NULL),
							   ":saved" => (!empty($this->saved) ? $this->saved : 0),
							   ":ordered" => (!empty($this->ordered) ? $this->ordered : NULL),
							   ":creation_time" => $this->creationTime,
							   ":modification_time" => $this->modificationTime))){

			return Connection::getHandle()->lastInsertId();

		} else {

			return FALSE;
		}
	}



	/**
	 * Sets the cart name
	 * @param [string] $name [a name for the cart]
	 */
	public function setName($name) {

		// Update the name in the DB
		$sql = Connection::getHandle()->prepare("UPDATE bs_carts SET name = ? WHERE id = ?");
		if ($sql->execute(array($name, (int)$this->id))) {

			// Update the class property
			$this->name = $name;
			return true;

		} else {
			return false;
		}

	}



	/**
	 * Sets the cart note
	 * @param [string] $note [a note for the cart]
	 */
	public function setNote($note) {

		// Update the note in the DB
		$sql = Connection::getHandle()->prepare("UPDATE bs_carts SET note = ? WHERE id = ?");
		if ($sql->execute(array($note, (int)$this->id))) {

			// Update the class property
			$this->note = $note;
			return true;

		} else {
			return false;
		}

	}



	/**
	 * Whether or not the cart requires freight shipping
	 * @return [bool] [true or false]
	 */
	public function requiresFreight() {

		$freight = false;
		$weight = 0;

		foreach($this->products as $product) {

			if ($product->freightShipping) { $freight = true; }
			$weight += $product->weight * $product->quantity;

		}

		return (($freight || $weight > self::FREIGHT_WEIGHT) ? true : false);

	}



	/**
	* Gets special message for tips & freight shipment
	* @return  array of messages
	*/
	public function getMessage() {

		if ($this->requiresFreight()) {

			$freight_message="After placing your order, you will not be charged until Customer Service calls you to discuss shipping arrangements and pricing.";

		}

		$message= array('tip'          => $this->tipList(),
						'freight_item' => isset($freight_message) ? $freight_message : NULL);

		return $message;

	}



	/**
	 * Gets tip message
	 * @return array of tip values
	 */
	public function tipList() {

        $tip_row = array();

		$sql = Connection::getHandle()->query(
            "SELECT name FROM bs_tips WHERE id = 1 AND active = 1 LIMIT 1"
        );

		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return isset($tip_row['name']) ? $tip_row['name'] : NULL;

	}


	/**
	 * Gets the total quantity of all items in the cart (sum qty column)
	 * @return [int] [total quantity]
	 */
	public function getTotalQuantity() {

		$totalQty = 0;

		foreach($this->products as $product) {
			$totalQty += $product->quantity;
		}

		return $totalQty;

	}



	/**
	 * Gets a count of all items in the cart
	 * @return [int] [count of line-items in cart]
	 */
	public function getLineItemCount() {
		return count($this->products);
	}



	/**
	 * [Returns number of stock items in cart]
	 * @return [int] [number of stock items]
	 */
	public function getStockCount() {

		$stockCount = 0;

		foreach($this->products as $product) {

			if (!$product->isCustom) {
				$stockCount++;
			}

		}

		return $stockCount;

	}



	/**
	 * [Returns number of custom items in cart]
	 * @return [int] [number of custom items]
	 */
	public function getCustomCount() {

		$customCount = 0;

		foreach($this->products as $product) {

			if ($product->isCustom) {
				$customCount++;
			}

		}

		return $customCount;

	}



	/**
	 * Returns quantity sum for specific sku
	 * @param  [string] [sku code]
	 * @return [type] [quantity of that sku]
	 */
	public function getQuantityBySku($skuCode) {

		$quantity = 0;

		foreach($this->products as $product) {
			if ($product->skuCode == $skuCode) {
				$quantity += $product->quantity;
			}
		}

		return $quantity;

	}



	/**
	 * Removes all products from cart
	 * @return bool [true/false on success/failure]
	 */
	public function emptyProducts() {

		foreach($this->products as $product) {

			if( !$product->remove() ) {

				return false;
			}
		}

		// Update the cart contents
		$this->setProperties();

		return true;
	}

	/**
	 * Gets cart subtotal
	 * @return float [subtotal of all items in the cart]
	 */
	public function getSubtotal() {

		$subtotal = 0;

		foreach($this->products as $product) {
			$subtotal += $product->totalPrice;
		}

		return $subtotal;

	}


	/**
	 * Gets an array of packages and weights per package
	 * @param  type $max_weight [description]
	 * @return type             [description]
	 */
	public function getPackages($maxWeight) {

		$maxWeight = (float) $maxWeight;

		$packages = array();

		// 'Calculated' packages refers to packages with no set numberOfPackages
		$calculatedPackages = 0;
		$calculatedPackagesWeight = 0;

		// 'Preset' packages refers to packages with a set numberOfPackages from bs_products_price
		$presetPackages = 0;
		$presetPackagesWeight = 0;

		// Loop through all the products
		foreach($this->products as $product) {

			// Add up preset packages
			if ($product->numberOfPackages > 0) {

				$presetPackages += $product->numberOfPackages * $product->quantity;
				$presetPackagesWeight += $product->weight * $product->quantity;

			// Add up our calculated packages
			} else {

				$calculatedPackagesWeight += $product->weight * $product->quantity;

			}

		}

		// Take the calculatedPackagesWeight and divide evenly into packages
		$calculatedPackages = floor($calculatedPackagesWeight / $maxWeight);

		// If there is a remainder, create another package
		if( fmod($calculatedPackagesWeight, $maxWeight) > 0 ) {

			$calculatedPackages++;

		}

		// Calculate the weight
		$weightPerCalculatedPackage = ceil($calculatedPackagesWeight / $calculatedPackages);
		$weightPerPresetPackage = ceil($presetPackagesWeight / $presetPackages);

		if ($calculatedPackages > 0) {
			$packages[] = array('number_of_packages' => $calculatedPackages,
								'weight_per_package' => $weightPerCalculatedPackage);
		}

		if ($presetPackages > 0) {
			$packages[] = array('number_of_packages' => $presetPackages,
								'weight_per_package' => $weightPerPresetPackage);
		}

		return $packages;


	}



	/**
	 * Returns date based on delay of production, transit time, and business delivery days
	 * @param [int] Extra delay time passed based on context
	 * @param [int] Carrier transit time
	 * @param [array] Array of booleans representing days of the week carrier service is in business
	 * @return array Date in multiple formats
	 */
	public function getEstimatedDate($productionDelay=null, $arrivalDelay=null, $arrivalDays=array()) {

		$brimarDaysOfOperation = array(FALSE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE);

		$currentDate = date('Y-m-d');
		$month = date('m',strtotime($currentDate));
		$date = date('d',strtotime($currentDate));
		$year = date('Y',strtotime($currentDate));

		// Set the initial delay as the next business day.
		$brimarDelay = $productionDelay + 1;


		// Add the production delay (in business days) from the database.
		$productiondelay = Settings::getSettingValue('productiondelay');
		$brimarDelay += (int) $productiondelay;


		// Determine how many custom items are in the cart.
		$custom_product_count = $this->getCustomCount();


		// Add an additional delay if the item requires special freight arrangements.
		if ( (isset($orders_id) && Orders::orderRequiresFreight($orders_id)) || (!isset($orders_id) && $this->requiresFreight()) ) {
			$brimarDelay += Settings::getSettingValue('freightdelay');
		}

		// If there are any custom items add that production delay (in business days) from the database.
		if ($custom_product_count > 0) {
			$customproductdelay = Settings::getSettingValue('customproductdelay');
			$brimarDelay += (int) $customproductdelay;
		}

		//Determine the date Brimar will ship
		$finalDelay = $this->calculateDelay($brimarDelay, $currentDate, $brimarDaysOfOperation);

		if ($arrivalDelay) {

			$formattedBrimarShipdate = date('Y-m-d', mktime(0, 0, 0, $month, ($date + $finalDelay), $year));

			//Determine the date carrier deliver
			$finalDelay += $this->calculateDelay($arrivalDelay, $formattedBrimarShipdate, $arrivalDays);
		}

		/*************************************************** Finalize dates **************************************************/
		$deliveryDate = mktime(0, 0, 0, $month, ($date + $finalDelay), $year);

		return $this->finalizeDate($deliveryDate);

	}

	/**
	 * Returns hash of product based on design id
	 * @return hash
	 */
	private function getHashByDesignID($design_id) {

		$stmt=Connection::getHandle()->prepare("SELECT hash FROM bs_designs WHERE id=:design_id LIMIT 1");
		$stmt->execute(array(":design_id"=>$design_id));
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$hash=$row['hash'];
		}

		return $hash;
	}

	public function calculateDelay( $delay, $initialDate, $shipDays = array(FALSE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE) ) {

		// Preventing infinite loops incase of all false array
		if( in_array(TRUE, $shipDays) === FALSE ){

			return FALSE;
		}

		global $ObjSession;

		$month = date('m',strtotime($initialDate));
		$date = date('d',strtotime($initialDate));
		$year = date('Y',strtotime($initialDate));
		$day = date('w',strtotime($initialDate));

		// Prepare the database query to check if each day is a holiday.
		$sql = Connection::getHandle()->prepare('SELECT COUNT(holiday_date) AS count FROM bs_holidays WHERE holiday_date = :date');

		for ($i = 1; $i <= $delay; $i++) {

			// Check if this day is a holiday.
			$sql->execute(array(
				':date' => date('Y-m-d', mktime(0, 0, 0, $month, ($date + $i), $year))
			));

			// If the day is a non-shipping day or a holiday, add an additional day of delay.
			if ( !$shipDays[($day + $i) % 7] || $sql->fetchColumn() > 0 ) {
				$delay++;
			}

		}

		return $delay;
	}



	public function finalizeDate($arrivalDate, $delay=null, $shipDays=array(FALSE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE)) {

		if($delay){

			$month = date('m',strtotime($arrivalDate));
			$date = date('d',strtotime($arrivalDate));
			$year = date('Y',strtotime($arrivalDate));

			$newDelay = $this->calculateDelay($delay, $arrivalDate, $shipDays);
			$arrivalDate = mktime(0, 0, 0, $month, ($date + $newDelay), $year);
		}

		// Convert the timestamp to the format required by the UPS API.
		$shipdate_formatted = date("Ymd", $arrivalDate);

		$shipdate_utc=gmdate('Y-m-d', $arrivalDate);

		$arrivalDate= strtotime($shipdate_utc) * 1000;
		$estimated_date=date("F jS, Y",strtotime($shipdate_utc));
	 	$shipdate_array=array(
		 	"shipdate"=>$arrivalDate,
		 	"estimated_date"=>$estimated_date,
		 	"shipdate_formatted" =>$shipdate_formatted
		 	);

		return $shipdate_array;

	}



	/**
	 * Allows copying one or all items from one cart to another
	 * @param [object] The target cart object to copy the product(s) to
	 * @param [bool] Whether or not to preserve pricing
	 * @param [int] $cartProductId The ID of a row in shopping cart permanent
	 * @return bool [true/false on success/failure]
	 */
	function copyProducts($targetCart, $preservePricing, $cartProductId = NULL) {

		$ObjOrders = new Orders();

		// Make sure we have a target cart to copy to
		if ($targetCart instanceof Cart) {

			// Make an array to hold the products we are copying
			$products = array();

			// If there is a cart product id, copy just that product
			if ($cartProductId > 0) {

				$products[] = $this->products[$cartProductId];

			// Otherwise, they want to copy every product
			} else {

				$products = $this->products;

			}

			// Keep an array of items in the target cart (grouped by sku) so we can rapidly check for duplicate skus
			foreach ($targetCart->products AS $cart_product_id => $prod) {
				$tcProducts[$prod->skuCode] = array('cart_product_id' => $cart_product_id);
			}

			// Grab the product row(s)
			foreach($products as $product) {

				// Figure out if we are attempting to copy the same product from one cart to another
				// If so, update quantity and pricing instead of adding

                if( isset($tcProducts) && is_array($tcProducts) ) {

                    if( array_key_exists($product->skuCode, $tcProducts) && $product->type == 'stock' ) {

                        // Add the new quantity to the old, and update the product
                        $newQuantity = $product->quantity + $targetCart->products[$tcProducts[$product->skuCode]['cart_product_id']]->quantity;
                        $targetCart->updateProductQuantity(

                            $tcProducts[$product->skuCode]['cart_product_id'], $newQuantity, FALSE
                        );

                        continue;
                    }
                }

				// Instantiate a page for the product so we can ensure its validity
				$pageObj = Page::create('product', $product->productId);

				// Ensure its validitiy
				if ($pageObj->getValidity()) {

					// If this is a builder, we have to duplicate the design
					if ($product->type == 'builder') {

						$Objrender = new Render();

						$image = $Objrender->duplicateDesign($product->designInfo['id']);

						if ($image == false) { return false; }

					// If this is a streetname or flash product, duplicate the custom information and rendered image
					} else if ($product->type == 'streetname' || $product->type == 'flash') {

						$custom_image = $ObjOrders->duplicateCustom($product->customImage['customImageId']);
						if ($custom_image == false) { return false; }

					}

					$TmpObjProduct =  ProductPage::create((int)$product->productId);

					$attributes_row = $TmpObjProduct->getProductAttributeList($product->materialCode);

					// If we are not preserving pricing, pull the most updated prices
					if (!$preservePricing) {

						// Look up new pricing
						$price_row = $TmpObjProduct->getFromProductsPriceWithQuantity($product->productNumber, $product->quantity, $product->skuCode);

						// Check for upcharge prices
						$sql_upcharge = Connection::getHandle()->prepare(
                            "SELECT SUM(csd.upcharge_price) AS upcharge_price FROM bs_cart_skus cs

                             INNER JOIN bs_cart_sku_data csd ON(cs.id = csd.cart_sku_id)
                             WHERE cs.product_id = ? GROUP BY cs.product_id");

						$sql_upcharge->execute(array((int)$product->id));
						$row_upcharge = $sql_upcharge->fetch(PDO::FETCH_ASSOC);

                        //Check if we got any price data. If we did not, then use the current price in the cart
                        if ( !empty($price_row) ) {

                            $price = $price_row['price'];

                        }
                        else {

                            $price = $product->unitPrice;

                        }

						$price += $row_upcharge['upcharge_price'];
						$total = $price * $product->quantity;

					} else {

						$price = $product->unitPrice;
						$total = $product->totalPrice;

					}

					$freight_shipping = ($price_row['freight_shipping']=== TRUE ? 1 : 0);

					$cdate = date('Y-m-d');

					$sql2 = Connection::getHandle()->prepare(

					         "INSERT INTO bs_cart_skus (cart_id, sku_id, product_id, tool_type_id, quantity,
					         unit_price, total_price, savings_percentage, design_id, custom_image_id,
                             eps, pdf, streetsign_custom_copy1, streetsign_custom_copy2, streetsign_num,
                             streetsign_background, streetsign_suffix, streetsign_prefix, streetsign_left_arrow,
                             streetsign_right_arrow, streetsign_position, streetsign_color, streetsign_font,
                             streetsign_upload_id, design_service, comments, creation_time, modification_time)

                             VALUES (:cart_id, :sku_id, :product_id, :tool_type_id, :quantity, :unit_price, :total_price,
                                     :savings_percentage, :design_id, :custom_image_id, :eps, :pdf,
                                     :streetsign_custom_copy1, :streetsign_custom_copy2, :streetsign_num, :streetsign_background,
                                     :streetsign_suffix, :streetsign_prefix, :streetsign_left_arrow, :streetsign_right_arrow,
                                     :streetsign_position, :streetsign_color, :streetsign_font, :streetsign_upload_id, :design_service,
                                     :comments, NOW(), NOW())");


					if (!$sql2->execute(
							array(
								":cart_id"                 => isset($targetCart->id) ? $targetCart->id : NULL,
								":design_id"               => ($product->type == 'builder' ? $image : NULL),
								":sku_id"                  => isset($product->skuId) ? $product->skuId : NULL,
								":product_id"              => isset($product->productId) ? $product->productId : NULL,
								":product_id"              => isset($product->productId) ? $product->productId : NULL,
								":tool_type_id"            => isset($product->type_id) ? $product->type_id : 3,
								":quantity"                => isset($product->quantity) ? $product->quantity : NULL,
								":unit_price"              => isset($price) ? $price : NULL,
								":total_price"             => isset($total) ? $total : NULL,
								":savings_percentage"      => isset($product->savingsPercentage) ? $product->savingsPercentage : NULL,
								":custom_image_id"         => ($product->type != 'stock' && $product->type != 'builder' ? $custom_image[0] : $product->customImage['customProductId']),
								":eps"                     => isset($product->eps) ? $product->eps : NULL,
								":pdf"                     => isset($product->pdf) ? $product->pdf : NULL,
								":streetsign_custom_copy1" => isset($product->customCopy1) ? $product->customCopy1 : NULL,
								":streetsign_custom_copy2" => isset($product->customCopy2) ? $product->customCopy2 : NULL,
								":streetsign_num"          => isset($product->streetNumber) ? $product->streetNumber : NULL,
								":streetsign_background"   => isset($product->background) ? $product->background : NULL,
								":streetsign_suffix"       => isset($product->suffix) ? $product->suffix : NULL,
								":streetsign_prefix"       => isset($product->prefix) ? $product->prefix : NULL,
								":streetsign_left_arrow"   => isset($product->leftArrow) ? $product->leftArrow : NULL,
								":streetsign_right_arrow"  => isset($product->rightArrow) ? $product->rightArrow : NULL,
								":streetsign_position"     => isset($product->position) ? $product->position : NULL,
								":streetsign_color"        => isset($product->color) ? $product->color : NULL,
								":streetsign_font"         => isset($product->font) ? $product->font : NULL,
								":streetsign_upload_id"    => ($product->type == 'streetname' && !empty($product->fileUpload['id']) ? $product->duplicateFileUploadRow() : NULL),
								":design_service"          => ($product->designService ? 1 : 0),
								":comments"                => $product->comments) )
					) {

						return false;
					}

					//Keep the last inserted id
					$sid = Connection::getHandle()->lastInsertId();

					// Holds a final array of attributes
					$attributes_array = array();

					// Custom cases for each product type
					if ($product->type == 'builder') {

						foreach($product->settings as $setting ) {

							if($setting['builderSetting'] == 'option' && array_key_exists($setting['builderSubsetting'],$product->upcharges)){
								$setting['builder_upcharge_id'] = $product->upcharges[$setting['builderSubsetting']]['id'];
								$setting['upcharge_price']		= $product->upcharges[$setting['builderSubsetting']]['price'];
							}

							$settings_array[] = $setting;
						}

						$attributes_array = $settings_array;

					} else if ($product->type == 'flash') {

                        $attributes_array = $product->upcharges;

					} else if ($product->type == 'streetname') {

                        $attributes_array = $product->upcharges;
					}

					// Loop through the attributes and insert
					foreach($attributes_array as $attribute) {

						// Insert the row
						$sql4 = Connection::getHandle()->prepare("INSERT INTO bs_cart_sku_data
													 (cart_sku_id,

													  builder_upcharge_id,
													  upcharge_price,
													  builder_setting,
													  builder_value,
													  builder_subsetting,
													  builder_label,
													  builder_value_text,
													  builder_setting_display,
													  builder_font,
													  builder_fontsize,
													  builder_color,
													  builder_alignment,
													  builder_setbyuser,
													  builder_font_setbyuser,
													  builder_fontsize_setbyuser,
													  builder_alignment_setbyuser,
													  builder_option_type)
													 VALUES
													 (:cart_sku_id,

													  :builder_upcharge_id,
													  :upcharge_price,
													  :builder_setting,
													  :builder_value,
													  :builder_subsetting,
													  :builder_label,
													  :builder_value_text,
													  :builder_setting_display,
													  :builder_font,
													  :builder_fontsize,
													  :builder_color,
													  :builder_alignment,
													  :builder_setbyuser,
													  :builder_font_setbyuser,
													  :builder_fontsize_setbyuser,
													  :builder_alignment_setbyuser,
													  :builder_option_type)");

						if (!$sql4->execute(array(":cart_sku_id"        =>  $sid,

											 ":builder_upcharge_id"         => ($product->type == 'builder' && $attribute['builder_upcharge_id'] ? $attribute['builder_upcharge_id'] : NULL),
											 ":upcharge_price"              => $attribute['upcharge_price'],
											 ":builder_setting"             => $attribute['builderSetting'],
											 ":builder_value"               => $attribute['builderValue'],
											 ":builder_subsetting"          => $attribute['builderSubsetting'],
											 ":builder_label"               => $attribute['builderLabel'],
											 ":builder_value_text"          => $attribute['builderValueText'],
											 ":builder_setting_display"     => $attribute['builderSettingDisplay'],
											 ":builder_font"                => $attribute['builderFont'],
											 ":builder_fontsize"            => $attribute['builderFontsize'],
											 ":builder_color"               => $attribute['builderColor'],
											 ":builder_alignment"           => $attribute['builderAlignment'],
											 ":builder_setbyuser"           => $attribute['builderSetbyuser'],
											 ":builder_font_setbyuser"      => $attribute['builderFontSetbyuser'],
											 ":builder_fontsize_setbyuser"  => $attribute['builderFontsize_Setbyuser'],
											 ":builder_alignment_setbyuser" => $attribute['builderAlignmentSetbyuser'],
											 ":builder_option_type"         => $attribute['builderOptionType'])) ) {

							return false;
						}

					}

				}

				// Unset settings_array
				unset($settings_array);
			}

			// Refresh the cart data
			$this->setProperties();

			//Success!
			return true;


		// Otherwise there is a problem with the target cart and we can do nothing
		} else {
			return false;
		}

	}



	/**
	 * Adds a stock item to the cart
	 * @param [int]    $pid          [product id]
	 * @param [string] $materialCode [product material code]
	 * @param [int]    $qty          [product quantity]
	 */
	public function addStock($pid, $skuId, $qty, $stateParameters) {
		return $this->add('stock', $pid, $skuId, $qty, $stateParameters, null, null, null, null, null, null, null, null, null);
	}



	/**
	 * Adds a flash item to the cart
	 * @param [int]    $pid           [product ID]
	 * @param [string] $materialCode  [product material code]
	 * @param [int]    $qty           [quantity]
	 * @param [array]  $upcharges     [array of upcharges]
	 * @param [string] $designService [whether or not to invoke our design service]
	 * @param [int]    $cpi           [custom product id]
	 */
	public function addFlash($pid, $skuId, $qty, $stateParameters, $upcharges, $designService, $cpi) {
		return $this->add('flash', $pid, $skuId, $qty, $stateParameters, $upcharges, $designService, null, $cpi, null, null, null, null, null);
	}



	/**
	 * Adds a streetname item to the cart
	 * @param [int]    $pid                [product id]
	 * @param [string] $materialCode       [product material code]
	 * @param [int]    $qty                [product quantity]
	 * @param [array]  $upcharges          [array of upcharges]
	 * @param [string] $designService      [whether or not to invoke our design service]
	 * @param [array]  $streetnameEditData [array of streetsign data]
	 */
	public function addStreetname($pid, $skuId, $qty, $stateParameters, $stid, $designService, $streetnameEditData) {
		return $this->add('streetname', $pid, $skuId, $qty, $stateParameters, $stid, $designService, $streetnameEditData);
	}



	/**
	 * Adds a builder item to the cart
	 * @param [int]    $originalProductId [original product id]
	 * @param [int]    $qty               [product quantity]
	 * @param [array]  $dataArray         [builder data array]
	 * @param [array]  $renderData        [builder renderdata array]
	 * @param [string] $mode              [builder mode ('add', 'edit', 'adminedit')]
	 * @param [string] $designId          [builder design id]
	 * @param [string] $builderRef        [builder reference]
	 */
	public function addBuilder($qty, $stateParameters, $builderArray, $renderData, $mode, $designId, $builderRef) {
		return $this->add('builder', null, null, $qty, $stateParameters, null, null, null, null, $builderArray, $renderData, $mode, $designId, $builderRef);
	}



	/**
	 * Adds an item to the cart
	 *
	 * It receives information on the item and quantity to be added, as well as optional parameters
	 * used for upcharges and for more complicated items like streetname. It then adds the item(s)
	 * to the user's cart and returns an array to be interpreted by javascript.
	 *
	 * @param string  $type         the type of item (options include: 'stock', 'flash', 'streetname')
	 * @param int     $id           the id of the item (for builders this is the original product id)
	 * @param string  $material     the item material code
	 * @param int     $qty          the quantity to add to the cart
	 * @param array   $upcharges    an array of upcharge ids
	 * @param string  $design       whether or not to invoke our design service (options: 'adjust', 'approved', '')
	 * @param array   $editdata     an array of editdata for streetname signs
	 * @param integer $cpi          the id of a custom product image
	 * @param array   builderArray  builder data array
	 * @param array   $renderData   builder renderdata array
	 * @param string  $mode         builder mode of action
	 * @param string  $designId     design id
	 * @param string  $builderRef   builder reference
	 */
	function add($type, $id, $skuId, $qty, $stateParameters, $stid=NULL, $design=NULL, $editdata=NULL, $cpi=0, $builderArray=NULL, $renderData=NULL, $mode=NULL, $designId=NULL, $builderRef=NULL) {

		if ($type != 'builder') {

			//Instantiate the product so we can work with it
			$TmpObjSku = Sku::create($skuId);
			$TmpObjProduct = Product::create($id,NULL,$stateParameters);
			$TmpObjProductPage = ProductPage::create($id);
			if (!is_null($stid)) {$ObjStreetsign = StreetNameTool::create($stid); }
			$toolTypeId = (int) $TmpObjProduct->getToolTypeId();

			//Grab the sku description row that matches this material
			$sku_rows = array("sku_code" => $TmpObjSku->getName(),
				"active" => $TmpObjSku->isActive(),
				"small_image" => $TmpObjSku->getSmallImage(),
				"medium_image" => $TmpObjSku->getMediumImage(),
				"large_image" => $TmpObjSku->getLargeImage(),
				"artwork_product_file" => $TmpObjSku->getArtworkProductionFile(),
				"requires_freight" => $TmpObjSku->getRequiresFreight(),
				"inventory" => $TmpObjSku->getInventory(),
				"limited_inventory" => $TmpObjSku->getLimitedInventory(),
				"max_chars_upper" => $TmpObjSku->getMaxCharsUpper(),
				"absolute_maximum" => $TmpObjSku->getAbsoluteMaximum(),
				"shipping_weight" => $TmpObjSku->getShippingWeight(),
				"shipping_weight_display_unit_id" => $TmpObjSku->getShippingWeightDisplayUnitId(),
				"weight" => $TmpObjSku->getWeight(),
				"weight_display_unit_id" => $TmpObjSku->getWeightDisplayUnitId(),
				"dedicated_package_count" => $TmpObjSku->getDedicatedPackageCount(),
				"ups_shipping_surcharge" => $TmpObjSku->getUpsShippingSurcharge(),
				"fedex_shipping_surcharge" => $TmpObjSku->getFedexShippingSurcharge(),
				"accessory_material_header" => $TmpObjSku->getAccessoryMaterialHeader(),
				"accessory_material_description" => $TmpObjSku->getAccessoryMaterialDescription(),
				"accessory_size_description" => $TmpObjSku->getAccessorySizeDescription(),
				"streetsign_accessory_display" => $TmpObjSku->getStreetsignAccessoryDisplay(),
				"package_inclusion_note" => $TmpObjSku->getPackageInclusionNote(),
				"size" => $TmpObjSku->getSize(),
				"corner_radius_id" => $TmpObjSku->getCornerRadiusId(),
				"material" => $TmpObjSku->getMaterial(),
				"package_id" => $TmpObjSku->getPackagingId(),
				"pricing_id" => $TmpObjSku->getPricingId(),
				"inner_units" => $TmpObjSku->getInnerunits(),
				"advertising_category_id" => $TmpObjSku->getAdvertisingCategoryId(),
				"sku_type_id" => $TmpObjSku->getSkuTypeName(),
				"mounting_hole_arrangement_id" => $TmpObjSku->getMountingHoleArrangementId(),
				"laminate_id" => $TmpObjSku->getLaminateId(),
				"lead_time" => $TmpObjSku->getLeadTime(),
				"accessory_family_id" => $TmpObjSku->getAccessoryFamilyId());

			$product_rows = array("sale_percentage" => $TmpObjProduct->getSavings(), "expiration_date" => $TmpObjProduct->getExpiration(), "product_number" => $TmpObjProduct->getProductNumber());
			$attributes_row = array_merge($sku_rows, $product_rows);

			//retrieve savings percentage and inventory level for product if applicable
			$savings_percentage = (isset($attributes_row['sale_percentage']) ? $attributes_row['sale_percentage'] : NULL);
			$inventory = (isset($attributes_row['inventory']) ? $attributes_row['inventory'] : NULL);
			$limited_inventory = (isset($attributes_row['limited_inventory']) ? $attributes_row['limited_inventory'] : FALSE);
			$expiration_date = (isset($attributes_row['expiration_date']) ? $attributes_row['expiration_date'] : NULL);

			$qtyGrtThnInventory = FALSE;
			$tooManyInCart = FALSE;

			//Determine if product is in stock
			$inStock = ($TmpObjSku->isInStock() || $qty <= $inventory ? TRUE : FALSE);

			//Determine if product expired
			$expired = $TmpObjProduct->isExpired($expiration_date);

			//Determine if sum of current qty and existing qty exceed that of what we have in inventory
			$sku_total_quantity = $this->getQuantityBySku($attributes_row['sku_code']);
			$enghInventory = TRUE;

			if($limited_inventory && $sku_total_quantity > 0){
				$enghInventory = ($sku_total_quantity + $qty  <= $inventory ? TRUE : FALSE);
			}


			//The product attributes
			$attributes_array = array();
			if ($attributes_row['size']) {$attributes_array[] = array("attr" => "Size", "val" => $attributes_row['size']->getName()); }
			$attributes_array[] = array("attr" => "Material", "val" => $attributes_row['material']->getName());

			// First check if it is a stock item and it matches one in the cart. If so, update instead of inserting
			if ($type == 'stock') {

				foreach($this->products as $product) {

					// Figure out if we are adding the same product to cart
					// If so, update quantity and price rather than add a new product row
					if ($product->skuCode == $attributes_row['sku_code'] && $enghInventory) {

						// Sum new quantity for update
						$newQty = $product->quantity + $qty;

						$updateContent = $this->updateProductQuantity($product->id, $newQty);

						// If the item update was successful
						if ($updateContent) {

							//Instantiate a cart page so we can pass the cart URL into the response array
							$CartPage = new Page('cart', null, null);

							//The product image
							$return_image = $TmpObjProductPage->getImagePath('grid') . $TmpObjProductPage->getImage();

							$response = array('success' => TRUE,
							  'image'       => $return_image,
							  'oldQuantity' => (int) $product->quantity,
							  'newQuantity' => (int) $updateContent['newQty'],
							  'attrs'       => $attributes_array,
							  'cartcount'   => (int) $this->getTotalQuantity(),
							  'unitprice'   => (float) $updateContent['unitPrice'],
							  'errors'      => array(),
							  'carturl'     => $CartPage->getUrl(),
							  'savings'     => ($savings_percentage > 0 ? $savings_percentage : NULL ),
							  'subtotal'    => $this->getSubTotal());

						} else {

							$response = array('success' => FALSE,
							  'cartcount' => (int) $this->getTotalQuantity(),
							  'errors' => 'Could not add item to cart.');
						}


						// Return an array so the process file knows what happened
						return $response;
					}
				}

			}

			//Grab the sku description row that matches this material
			$attributes_row = $sku_rows;

			//Grab the price row that matches the quantity for this material
			$price_row =$TmpObjSku->getFromSkusPriceWithQuantity($qty);

			//If there are upcharges, poll the db for information on them and add their prices
			//together as upcharge_total which we'll add to the product price later
			// upcharges no longer exist in the new database only use this for streetsign skus
			$upcharges = isset($_POST['upcharges'])? $_POST['upcharges'] : NULL;
			$upcharges = array_filter($upcharges); //Clean out empty values

			// Keep a total of the upcharge price

			if ( $type == 'streetname' ) {
					$upcharge_array = $TmpObjSku->getStreetnameUpcharges($upcharges);
				}

			//Create a status for whether the product has attribute upcharges or not
			$has_attributes = (count($upcharge_array) > 0 ? 'Y' : 'N');

			//Whether or not to invoke our design service
			$designService = ($design == 'adjust' ? 1 : 0);

			//Stock or custom
			$stock_custom = ($type == 'stock' ? 'S' : 'C');

			//Get information about price, weight, and packages
			$unit_price = number_format(round($price_row['price'], 2), 2);
			$price = $price_row['price'];
			$total = $price * $qty;
			$weight = $attributes_row['shipping_weigh'] * $qty;
			$true_weight = $attributes_row['weight'] * $qty;
			$number_of_packages = $attributes_row['dedicated_package_count'] * $qty;
			$freight_shipping = (bool) $attributes_row['requires_freight'];

			////////////////////////////////////////////////////////////////////////////////////////////
			// STREETNAME PRODUCT SECTION (There is another streetname section further in this function)
			////////////////////////////////////////////////////////////////////////////////////////////
			if ($type == 'streetname') {

				//Generate a new design id
				$design_id = $this->getUniqueDesignId();
				//Create our streetname image
				$streetname_image = $ObjStreetsign->createimg($attributes_row['sku_code'], $TmpObjProduct->getStreetsignToolId(), $editdata, $design_id);

				//If uppercase is set, strtoupper our text lines
				$editdata['line_1']   = ($editdata['textupper'] == 'Y' ? mb_strtoupper($editdata['line_1']) : $editdata['line_1']);
				$editdata['line_2']   = ($editdata['textupper'] == 'Y' ? mb_strtoupper($editdata['line_2']) : $editdata['line_2']);
				$editdata['sidetext'] = ($editdata['textupper'] == 'Y' ? mb_strtoupper($editdata['sidetext']) : $editdata['sidetext']);
				$editdata['prefix']   = ($editdata['textupper'] == 'Y' ? mb_strtoupper($editdata['prefix']) : $editdata['prefix']);
				$editdata['suffix']   = ($editdata['textupper'] == 'Y' ? mb_strtoupper($editdata['suffix']) : $editdata['suffix']);

				//Check if there are arrows
				if (mb_strpos($editdata['prefix'], "Arrow") !== FALSE) { $editdata['leftarrow'] = TRUE; }
				if (mb_strpos($editdata['suffix'], "Arrow") !== FALSE) { $editdata['rightarrow'] = TRUE; }

				//Empty the prexif and suffix if they are set to 'none'
				if ($editdata['prefix'] == 'NONE') { $editdata['prefix'] = ''; }
				if ($editdata['suffix'] == 'NONE') { $editdata['suffix'] = ''; }

				//Insert into `bs_product_custom`
				$sql = Connection::getHandle()->prepare("INSERT INTO bs_product_custom
										  	      (`design_id`,`custom_image`,`active`,`product_id`,
										  	       `session_id`,`ip`,`comments`,
										  	       `customers_id`,`created_date`,`last_modified`,`tool_type_id`)
										    VALUES
										    	  (:design_id,:custom_image,:active,:product_id,
										  	       :session_id,:ip,:comments,
										  	       :customers_id,:created_date,:last_modified,:tool_type_id) ");

				$sql->execute(array(':design_id' => $design_id,
									':custom_image' => $streetname_image,
									':active' => 1,
									':product_id' => $id,
									':session_id' => session_id(),
									':ip' => $_SERVER['REMOTE_ADDR'],
									':comments' => $editdata['special_comment'],
									':customers_id' => (!empty($_SESSION['CID']) ? $_SESSION['CID'] : NULL),
									':created_date' => date("Y-m-d H:i:s"),
									':last_modified' => date("Y-m-d H:i:s"),
									':tool_type_id' =>  $toolTypeId
								  ));

				//Grab the last insert ID as the cart id
				$streetname_image_id = Connection::getHandle()->lastInsertId();

				//Check if the user uploaded an image
				if ($editdata['uploadfileid'] > 0 && !empty($editdata['sign_background'])) {

					//Grab data from `bs_streetsign_uploads`
					$sql = Connection::getHandle()->prepare("SELECT * FROM bs_streetsign_uploads WHERE id = ?");

					if ($sql->execute(array($editdata['uploadfileid']))) {
						$uploaded_image = $sql->fetch(PDO::FETCH_ASSOC);

						if ($uploaded_image['id'] > 0) {
							$cpi = $uploaded_image['id'];
						}
						if (!empty($uploaded_image['file_name'])) {
							$custom_image = $uploaded_image['file_name'];
						}

						//Construct paths for where the file is, and where it is to be copied to
						$file_temp = $TmpObjProductPage->getImagePath('upload_temp') . $uploaded_image['file_name'];
						$file_permanent = $TmpObjProductPage->getImagePath('upload_perm') . $uploaded_image['file_name'];

						//Copy the file, and then unlink the old temp file
						copy($file_temp, $file_permanent);
						unlink($file_temp);
					}

				}
				$cpi = $streetname_image_id;
				$custom_image = $streetname_image;

				// Retrieve streetname image sample to display it in the add to cart dialog
				$accuracyImage = $ObjStreetsign->getAccuracyImage($id);

			}


			if ($type == 'flash') {

				$sql=Connection::getHandle()->prepare("SELECT * FROM bs_product_custom WHERE custom_product_id= ? AND session_id = ?");
				$sql->execute(array($cpi,session_id()));

				$row=$sql->fetch(PDO::FETCH_ASSOC);
				$custom_image=$row['custom_image'];
				$cpi=$row['custom_product_id'];
				$save_design= $row['save_design'];

				$ObjOrders = new Orders();
				$design = $ObjOrders->duplicateCustom($cpi, false);
                //todo rename this from cpi to design_cpi
				$design_cpi = $design[0];

				$sql_cpi = Connection::getHandle()->prepare("SELECT * FROM bs_product_custom WHERE custom_product_id= ? AND session_id = ?");
				$sql_cpi->execute(array($cpi,session_id()));

				$row_cpi = $sql_cpi->fetch(PDO::FETCH_ASSOC);
				$custom_image = $row_cpi['custom_image'];


				$update = Connection::getHandle()->prepare("UPDATE bs_product_custom SET custom_image=:custom_image WHERE custom_product_id=:cpi");
				$update->execute(array(":custom_image"=>$custom_image,":cpi"=>$cpi));

			}

			////////////////////////////////////////////////////////////////////////////////////////////
			// MAIN SHOPPING CART INSERT
			////////////////////////////////////////////////////////////////////////////////////////////

			//Check to make sure nobody gets anything for free
			if ($price > 0 && $total > 0 && $true_weight > 0 && count($attributes_row) > 0) {

				$qtyGrtThnInventory = FALSE;
				$tooManyInCart = FALSE;

				//Check if enough is in stock and that it is not expired
				if ( $inStock AND !$expired AND $enghInventory) {

					$sql = Connection::getHandle()->prepare("INSERT INTO bs_cart_skus
											        (`cart_id`,
											         `product_id`,
											         `source_product_id`,
											         `source_product_recommendation_id`,
											         `source_accessory_family_product_id`,
											         `source_installation_accessory_id`,
											         `source_landing_product_id`,
											         `source_subcategory_product_id`,
											         `tool_type_id`,
											         `streetsign_tool_id`,
											         `quantity`,
											         `unit_price`,
											         `total_price`,
											         `design_id`,
											         `custom_image_id`,
											         `sku_id`,
											         `eps`,
											         `pdf`,
											         `streetsign_custom_copy1`,
											         `streetsign_custom_copy2`,
											         `streetsign_num`,
											         `streetsign_background`,
											         `streetsign_suffix`,
											         `streetsign_prefix`,
											         `streetsign_left_arrow`,
											         `streetsign_right_arrow`,
											         `streetsign_position`,
											         `streetsign_color`,
											         `streetsign_font`,
											         `streetsign_upload_id`,
											         `design_service`,
											         `comments`,
											         `savings_percentage`,
											         `creation_time`,
											         `modification_time`)
										         VALUES
										            (:cart_id,
													 :product_id,
													 :source_product_id,
                                                     :source_product_recommendation_id,
                                                     :source_accessory_family_product_id,
                                                     :source_installation_accessory_id,
                                                     :source_landing_product_id,
                                                     :source_subcategory_product_id,
													 :tool_type_id,
													 :streetsign_tool_id,
													 :quantity,
													 :unit_price,
													 :total_price,
													 :design_id,
													 :custom_image_id,
													 :sku_id,
													 :eps,
													 :pdf,
													 :streetsign_custom_copy1,
													 :streetsign_custom_copy2,
													 :streetsign_num,
													 :streetsign_background,
													 :streetsign_suffix,
													 :streetsign_prefix,
													 :streetsign_left_arrow,
													 :streetsign_right_arrow,
													 :streetsign_position,
													 :streetsign_color,
													 :streetsign_font,
													 :streetsign_upload_id,
													 :design_service,
													 :comments,
													 :savings_percentage,
													 :creation_time,
													 :modification_time) ");


					$sql->execute(array(':cart_id'                 => (int)$this->id,
										':product_id'              => $id,
                                        ':source_product_id'       => $stateParameters['sourceProduct'],
                                        ':source_product_recommendation_id' => $stateParameters['sourceProductRecommendation'],
                                        ':source_accessory_family_product_id' => $stateParameters['sourceAccessoryFamilyProduct'],
                                        ':source_installation_accessory_id' => $stateParameters['sourceInstallationAccessory'],
                                        ':source_landing_product_id'    => $stateParameters['sourceLandingProduct'],
                                        ':source_subcategory_product_id' =>  $stateParameters['sourceSubcategoryProduct'],
										':tool_type_id'            => $toolTypeId,
                                        ':streetsign_tool_id'      => $stid,
										':quantity'                => $qty,
										':unit_price'              => $price,
										':total_price'             => $total,
										':design_id'               => NULL,
										':custom_image_id'         => $cpi,
										':sku_id'          		   => $skuId,
										':eps'                     => NULL,
										':pdf'                     => NULL,
										':streetsign_custom_copy1' => (!empty($editdata['line_1']) ? $editdata['line_1'] : NULL),
										':streetsign_custom_copy2' => (!empty($editdata['line_2']) ? $editdata['line_2'] : NULL),
										':streetsign_num'          => (!empty($editdata['sidetext']) ? $editdata['sidetext'] : NULL),
										':streetsign_background'   => (!empty($editdata['sign_background']) ? $editdata['sign_background'] : NULL),
										':streetsign_suffix'       => ((!empty($editdata['suffix']) && !$editdata['rightarrow']) ? $editdata['suffix'] : NULL),
										':streetsign_prefix'       => ((!empty($editdata['prefix']) && !$editdata['leftarrow']) ? $editdata['prefix'] : NULL),
										':streetsign_left_arrow'   => ($editdata['leftarrow'] == TRUE ? $editdata['prefix'] : NULL),
										':streetsign_right_arrow'  => ($editdata['rightarrow'] == TRUE ? $editdata['suffix'] : NULL),
										':streetsign_position'     => (!empty($editdata['position']) ? $editdata['position'] : NULL),
										':streetsign_color'        => (!empty($editdata['sign_color']) ? $editdata['sign_color'] : NULL),
										':streetsign_font'         => (!empty($editdata['sign_font']) ? $editdata['sign_font'] : NULL),
										':streetsign_upload_id'    => (!empty($uploaded_image['id']) ? $uploaded_image['id'] : NULL),
										':design_service'          => $designService,
										':comments'                => (!empty($editdata['special_comment']) ? $editdata['special_comment'] : $row['comments']),
										':savings_percentage'      => $savings_percentage,
										':creation_time'           => date("Y-m-d H:i:s"),
										':modification_time'       => date("Y-m-d H:i:s")));

				//Grab the last insert ID as the cart id
				$cart_sku_id = Connection::getHandle()->lastInsertId();

				} else if(!$enghInventory) {
					$tooManyInCart = TRUE;
				} else {
					$qtyGrtThnInventory = TRUE;
				}

			}


			////////////////////////////////////////////////////////////////////////////////////////////
			// ATTRIBUTES INSERT
			////////////////////////////////////////////////////////////////////////////////////////////
			if ((count($upcharges) > 0 || $type == 'streetname') && $cart_sku_id > 0) {

				//Loop through any upcharges and insert as new rows in bs_cart_product_attributes
				foreach($upcharge_array as $value) {

					$sql = Connection::getHandle()->prepare("INSERT INTO bs_cart_sku_data
											        (`cart_sku_id`,
											         `upcharge_price`,
											         `builder_setting`,
											         `builder_value`,
											         `builder_subsetting`,
											         `builder_label`,
											         `builder_value_text`,
											         `builder_setting_display`,
											         `builder_font`,
											         `builder_fontsize`,
											         `builder_color`,
											         `builder_alignment`,
											         `builder_setbyuser`,
											         `builder_font_setbyuser`,
											         `builder_fontsize_setbyuser`,
											         `builder_alignment_setbyuser`,
											         `builder_option_type`)
										        VALUES
											        (:cart_sku_id,
													 :upcharge_price,
													 :builder_setting,
													 :builder_value,
													 :builder_subsetting,
													 :builder_label,
													 :builder_value_text,
													 :builder_setting_display,
													 :builder_font,
													 :builder_fontsize,
													 :builder_color,
													 :builder_alignment,
													 :builder_setbyuser,
													 :builder_font_setbyuser,
													 :builder_fontsize_setbyuser,
													 :builder_alignment_setbyuser,
													 :builder_option_type) ");


					$sql->execute(array('cart_sku_id'             => $cart_sku_id,
										'upcharge_price'              => floatval(ltrim($value['note'],"$")),
										'builder_setting'             => NULL,
										'builder_value'               => NULL,
										'builder_subsetting'          => NULL,
										'builder_label'               => NULL,
										'builder_value_text'          => NULL,
										'builder_setting_display'     => NULL,
										'builder_font'                => NULL,
										'builder_fontsize'            => NULL,
										'builder_color'               => NULL,
										'builder_alignment'           => NULL,
										'builder_setbyuser'           => NULL,
										'builder_font_setbyuser'      => NULL,
										'builder_fontsize_setbyuser'  => NULL,
										'builder_alignment_setbyuser' => NULL,
										'builder_option_type'         => NULL));

					$attributes_id = Connection::getHandle()->lastInsertId();

				}
			}


			////////////////////////////////////////////////////////////////////////////////////////////
			// RESPONSE
			////////////////////////////////////////////////////////////////////////////////////////////

			//The product image
			if ($type == 'streetname') {
				$return_image = $TmpObjProductPage->getImagePath('streetname_small') . $streetname_image;
			} else if($type=='flash') {
				$return_image = $TmpObjProductPage->getImagePath('streetname_small') . $custom_image;
			} else {
				$return_image = $TmpObjProductPage->getImagePath('grid') . $TmpObjProduct->image1_thumbnail;
			}

			//Array of non-fatal errors
			$errors_array = array();
			if (count($upcharges) > 0 && $cart_sku_id > 0) {
				if ($attributes_id <= 0) {
					$errors_array[] = "Could not save product settings.";
				}
				if (($type == 'streetname' && empty($streetname_image)) || ($type == 'flash' && empty($custom_image))) {
					$errors_array[] = "Could not save an image preview of your design.";
				}
			}

			//Add any upcharges to the attributes array
			foreach($upcharge_array as $value) {
				if ($type == 'streetname') {
					$attributes_array[] = array("attr" => $value['type'], "val" => $value['name'] .' - '. $value['note']);
				} else {
					$attributes_array[] = array("attr" => $value['type'], "val" => $value['name']);
				}
			}


			// Update the cart contents
			$this->setProperties();

			//Instantiate a cart page so we can pass the cart URL into the response array
			$cart = new Page('cart', null, null);

				//Build the response array
			if ($cart_sku_id > 0) {

				//Instantiate a cart page so we can pass the cart URL into the response array
				$cart = new Page('cart', null, null);

				$response = array('success'       => TRUE,
								  'image'         => $return_image,
								  'oldQuantity'   => (int) 0,
								  'newQuantity'   => (int) $qty,
								  'attrs'         => $attributes_array,
								  'cartcount'     => (int) $this->getTotalQuantity(),
								  'unitprice'     => (float) round($price, 2),
								  'errors'        => $errors_array,
								  'carturl'       => $cart->getUrl(),
								  'savings'       => ($savings_percentage > 0 ? $savings_percentage : NULL ),
								  'subtotal'      => $this->getSubTotal(),
								  'accuracyImage' => (string) $accuracyImage);


			} else if ( $qtyGrtThnInventory ) {

				$response = array('success'   => FALSE,
								  'cartcount' => (int) $this->getTotalQuantity(),
								  'errors'    => array('Sorry, we don\'t have ' . $qty . ' in stock. Please enter a quantity ' . $inventory . ' or less.'));

			} else if ($tooManyInCart) {

				if ($inventory === $sku_total_quantity || $inventory - $sku_total_quantity == 0) {
					$error_msg = 'Only '.$inventory.' left in stock. You already have '.$sku_total_quantity.' in your cart';
				} else {
					$error_msg = 'Only '.$inventory.' left in stock. You already have '.$sku_total_quantity.' in your cart. Please enter a quantity '.($inventory - $sku_total_quantity).' or fewer.';
				}

				$response = array('success'   => FALSE,
								  'cartcount' => (int) $this->getTotalQuantity(),
								  'errors'    => array($error_msg));

			} else {

				$response = array('success'   => FALSE,
							      'cartcount' => (int) $this->getTotalQuantity(),
							      'errors'    => array('Could not add item to cart.'));

			}


			//Send back the response
			return $response;


		// Builder case
		} else {

			// Variable declaration
			$raster  = '';
			$vector  = '';
			$display = '';
			$names   = '';
			$design  = '';
			$ip      = '';
			$queries = array();

			// get values in to variable
			$size = $builderArray['size']['value'];
			$materials = $builderArray['material']['value'];
			$schemes = $builderArray['scheme']['value'];

			// Query database to get material code
			$sql_code = Connection::getHandle()->prepare("SELECT bsku.sku_id, bsku.product_id FROM bs_builder_skus bsku
											 INNER JOIN bs_product_skus ps ON (ps.product_id = bsku.product_id AND ps.sku_id = bsku.sku_id)
											 INNER JOIN bs_products p ON (p.id = ps.product_id AND p.active = TRUE)
											 INNER JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = TRUE)
											 WHERE bsku.size_ref = ?
											 AND bsku.scheme_ref = ?
											 AND bsku.material_ref = ?
											 AND bsku.builder_ref = ?
											 AND bsku.active= TRUE ");

			$sql_code->execute(	array($size, $schemes, $materials, $builderRef ));
			$row_code = $sql_code->fetch(PDO::FETCH_ASSOC);
			$skuId = (int) $row_code['sku_id'];
			$productId = (int) $row_code['product_id'];

			// Create sku object to get the material name
			$ObjSku = Sku::create($skuId);
			$ObjMaterial = $ObjSku->getMaterial();
			$materialName = $ObjMaterial->getName();

			//Query database to get products information
			$sql_productref = Connection::getHandle()->prepare("SELECT product_ref
											FROM bs_builder_skus
											WHERE size_ref=:sizes
											AND material_ref=:materials
											AND scheme_ref=:schemes
											AND builder_ref=:builder_ref");
			$sql_productref->execute(array(':sizes'       => $size,
									':materials'   => $materials,
									':schemes'     => $schemes,
									':builder_ref' => $builderRef));

			$row_productref = $sql_productref->fetch(PDO::FETCH_ASSOC);
			$prod_ref = $row_productref['products_ref'];
			$skuCode = $ObjSku->getName();

			$commented = isset($builderArray['instructions']['value'])? $builderArray['instructions']['value'] : "";
			$commented_display = $builderArray['instructions']['display'];

			$designservice = $builderArray['designservice']['value'];
			$designservice_display = $builderArray['designservice']['display'];

			$custom_approved = ($designservice ? 'adjust' : 'approved');
			$designService = ($custom_approved == 'adjust' ? true : false);

			// Ensure there is a quantity
			if ($qty != '') {

				// Save the product information
				// TODO change all the values with actual values
				$ObjProduct        = Product::create($productId,NULL,$stateParameters);
				$productName 	   = $ObjProduct->getProductName();
				$toolTypeId		   = $ObjProduct->getToolTypeId();
				$bestSeller		   = $ObjProduct->getBestSeller();
				$productSubtitle   = $ObjProduct->getProductSubtitle();
				$subcategoryId	   = $ObjProduct->getSubcategoryId();
				$landingId		   = $ObjProduct->getLandingId();
				$custom            = $ObjProduct->getCustom();


				//Query database to get price, weight etc
				$sql_check_attributes=Connection::getHandle()->prepare("SELECT pt.price, sku.inventory, sku.limited_inventory FROM bs_pricing_tiers pt
																		INNER JOIN bs_skus sku ON (sku.pricing_id = pt.pricing_id AND sku.active = TRUE)
																		WHERE sku.id = :skuId
																		AND pt.minimum_quantity <= :qty
																		ORDER BY pt.minimum_quantity DESC limit 1");

				$sql_check_attributes->execute( array(':qty'            => $qty,
													  ':skuId'       => $skuId) );

				$row = $sql_check_attributes->fetch(PDO::FETCH_ASSOC);

					if ( !$row['limited_inventory'] || ($row['limited_inventory'] && $row['inventory'] >= $qty) ) {

						$price            = $row['price'];
						$weight           = $ObjSku->getWeight() * $qty;
						$number_pkg       = $ObjSku->getDedicatedPackageCount() * $qty;
						$freight_shipping = $ObjSku->getRequiresFreight();

					}

				$total = $qty * $price;

			}

			//Throw an error for no quantity
			if (!($qty >= 0)) {
				$errors[] = "No quantity was entered.";
			}

			//attribute status for builder always true
			$attributes_status='Y';
			$now=date("Y-m-d H:i:s");
			$ip=$_SERVER['REMOTE_ADDR'];
			$session_id=session_id();

			//prepare query to insert for shopping cart
			$sql_insert_cart = Connection::getHandle()->prepare("INSERT INTO bs_cart_skus
										               (`cart_id`,
										                `product_id`,
										                `source_product_id`,
                                                        `source_product_recommendation_id`,
                                                        `source_accessory_family_product_id`,
                                                        `source_installation_accessory_id`,
                                                        `source_landing_product_id`,
                                                        `source_subcategory_product_id`,
										                `sku_id`,
										                `subcategory_id`,
										                `landing_id`,
										                `tool_type_id`,
										                `quantity`,
										                `unit_price`,
										                `total_price`,
										                `savings_percentage`,
										                `design_id`,
										                `custom_image_id`,
										                `eps`,
										                `pdf`,
										                `streetsign_custom_copy1`,
										                `streetsign_custom_copy2`,
										                `streetsign_num`,
										                `streetsign_background`,
										                `streetsign_suffix`,
										                `streetsign_prefix`,
										                `streetsign_left_arrow`,
										                `streetsign_right_arrow`,
										                `streetsign_position`,
										                `streetsign_color`,
										                `streetsign_font`,
										                `design_service`,
										                `builder_ref`,
										                `comments`,
										                `creation_time`,
										                `modification_time`)
									                VALUES
									                   (:cart_id,
												        :product_id,
												        :source_product_id,
                                                        :source_product_recommendation_id,
                                                        :source_accessory_family_product_id,
                                                        :source_installation_accessory_id,
                                                        :source_landing_product_id,
                                                        :source_subcategory_product_id,
												        :sku_id,
												        :subcategory_id,
												        :landing_id,
												        :tool_type_id,
												        :quantity,
												        :unit_price,
												        :total_price,
												        :savings_percentage,
												        :design_id,
												        :custom_image_id,
												        :eps,
												        :pdf,
												        :streetsign_custom_copy1,
												        :streetsign_custom_copy2,
												        :streetsign_num,
												        :streetsign_background,
												        :streetsign_suffix,
												        :streetsign_prefix,
												        :streetsign_left_arrow,
												        :streetsign_right_arrow,
												        :streetsign_position,
												        :streetsign_color,
												        :streetsign_font,
												        :design_service,
												        :builder_ref,
												        :comments,
												        :creation_time,
												        :modification_time) ");

			if ($mode == 'add') {

				if ($productId && $skuId) {

					//execute insert
					if(empty($errors)) {

						$execute = $sql_insert_cart->execute(array(":cart_id"                 => (int)$this->id,
																   ":product_id"              => $productId,
                                                                   ":source_product_id"       => $stateParameters['sourceProduct'],
                                                                   ":source_product_recommendation_id" => $stateParameters['sourceProductRecommendation'],
                                                                   ":source_accessory_family_product_id" => $stateParameters['sourceAccessoryFamilyProduct'],
                                                                   ":source_installation_accessory_id" => $stateParameters['sourceInstallationAccessory'],
                                                                   ":source_landing_product_id"    => $stateParameters['sourceLandingProduct'],
                                                                   ":source_subcategory_product_id" =>  $stateParameters['sourceSubcategoryProduct'],
																	":sku_id"				  => $skuId,
																	":subcategory_id"		  => $subcategoryId,
																	"landing_id"			  => $landingId,
																   	":tool_type_id"            => $toolTypeId,
																   	":quantity"                => $qty,
																   	":unit_price"              => $price,
																   	":total_price"             => $total,
																   	":savings_percentage"      => NULL,
																   	":design_id"               => NULL,
																   	":custom_image_id"         => NULL,
																   	":eps"                     => NULL,
																   	":pdf"                     => NULL,
																   	":streetsign_custom_copy1" => NULL,
																   	":streetsign_custom_copy2" => NULL,
																   	":streetsign_num"          => NULL,
																   	":streetsign_background"   => NULL,
																   	":streetsign_suffix"       => NULL,
																   	":streetsign_prefix"       => NULL,
																   	":streetsign_left_arrow"   => NULL,
																   	":streetsign_right_arrow"  => NULL,
																   	":streetsign_position"     => NULL,
																   	":streetsign_color"        => NULL,
																   	":streetsign_font"         => NULL,
																   	":design_service"          => $designService,
																	":builder_ref"			   => $builderRef,
																   	":comments"                => $commented,
																   	":creation_time"           => $now,
																   	":modification_time"       => $now));

					}
				}

				//Throw an error
				if($sql_insert_cart->rowCount() < 1) {
					$errors[] = "An unknown error has occurred.";
				}


				$cart_sku_id = Connection::getHandle()->lastInsertId();

			} else if ($mode=='edit' || $mode=="adminedit") {

				$sql_cart = Connection::getHandle()->prepare("SELECT d.id AS design_id,
													    csku.id AS cart_sku_id
											    FROM bs_designs d
											    LEFT JOIN bs_cart_skus csku ON (csku.design_id = d.id)
											    WHERE d.hash = :hash");
				$stmt = $sql_cart->execute(array(":hash" => $designId));


				// Loop thorugh the results
				while ($row_cart = $sql_cart->fetch(PDO::FETCH_ASSOC)) {

					$design_id = $row_cart['design_id'];
					$cart_sku_id = $row_cart['cart_sku_id'];

				}

				//if the design id exists we will update
				if ($design_id) {

					if ($mode == "adminedit" || $mode == "edit") {

						$sql = "UPDATE bs_cart_skus
								SET product_id    = :product_id,
								tool_type_id      = :tool_type_id,
								quantity          = :quantity,
								unit_price        = :unit_price,
								total_price       = :total_price,
								design_id         = :design_id,
								sku_id     		  = :sku_id,
								design_service    = :design_service,
								comments          = :comments,
								modification_time = :modification_time
								WHERE id = :id";



						$stmt = Connection::getHandle()->prepare($sql);

						// If there are no errors so far, execute the update
						if (empty($errors)) {

							$execute = $stmt->execute(array(":product_id"  => $productId,
												":tool_type_id"            => $toolTypeId,
												":quantity"                => $qty,
												":unit_price"              => $price,
												":total_price"             => $total,
												":design_id"               => $design_id,
												":sku_id"           	   => $skuId,
												":design_service"          => $designservice,
												":comments"                => $commented,
												":modification_time"       => $now,
												":id"                      => $cart_sku_id));

						}

					}

					//Throw an error
					if (!$execute) {
						$errors[] = "An unknown error has occurred.";
					}

				//else insert
				} else {

					// Make sure there were no errors before we do the insert
					if (empty($errors)) {

						// Insert
						$execute1 = $sql_insert_cart->execute(array(":cart_id"                 => (int)$this->id,
																    ":product_id"              => $productId,
                                                                    ":source_product_id"       => $stateParameters['sourceProduct'],
                                                                    ":source_product_recommendation_id" => $stateParameters['sourceProductRecommendation'],
                                                                    ":source_accessory_family_product_id" => $stateParameters['sourceAccessoryFamilyProduct'],
                                                                    ":source_installation_accessory_id" => $stateParameters['sourceInstallationAccessory'],
                                                                    ":source_landing_product_id"    => $stateParameters['sourceLandingProduct'],
                                                                    ":source_subcategory_product_id" =>  $stateParameters['sourceSubcategoryProduct'],
																    ":tool_type_id"            => $toolTypeId,
																    ":quantity"                => $qty,
																    ":unit_price"              => $price,
																    ":total_price"             => $total,
																    ":savings_percentage"      => NULL,
																    ":design_id"               => NULL,
																    ":custom_image_id"         => NULL,
																    ":sku_id"           	   => $skuId,
																    ":eps"                     => NULL,
																    ":pdf"                     => NULL,
																    ":streetsign_custom_copy1" => NULL,
																    ":streetsign_custom_copy2" => NULL,
																    ":streetsign_num"          => NULL,
																    ":streetsign_background"   => NULL,
																    ":streetsign_suffix"       => NULL,
																    ":streetsign_prefix"       => NULL,
																    ":streetsign_left_arrow"   => NULL,
																    ":streetsign_right_arrow"  => NULL,
																    ":streetsign_position"     => NULL,
																    ":streetsign_color"        => NULL,
																    ":streetsign_font"         => NULL,
																    ":design_service"          => $designService,
																    ":comments"                => $commented,
																    ":creation_time"           => $now,
																    ":modification_time"       => $now));

						$cart_sku_id = Connection::getHandle()->lastInsertId();

					}

					//Throw an error
					if (!$execute1) {
						$errors[] = "An unknown error has occurred.";
					}

				}

			}

			$status = 'Y';

			// todo waiting on Jason to figure out how to handle upcharge for builder product
			$up_price = null;

			if ($builderArray['size']) {

				// $up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
				$opt_type             = null;
				$bsubset              = 'size';
				$bsetting             = 'size';
				$bvalue               = $builderArray['size']['value'];

				if($builderArray['size']['setbyuser'])
					$setbyusers=1;
				else
					$setbyusers=0;

				if($builderArray['size']['display'])
					$display=1;
				else
					$display=0;

				//Query database to get name of size
				$sql_size = Connection::getHandle()->prepare("SELECT name
												 FROM bs_builder_sizes
												 WHERE size_ref=:bvalue
												 AND active= TRUE
												 LIMIT 1");
				$sql_size->execute(array(':bvalue' => $bvalue));

				$row_size = $sql_size->fetch(PDO::FETCH_ASSOC);
				$name = $row_size['name'];

				//Query Database to get control name based on control type
				$sql_ui=Connection::getHandle()->prepare("SELECT control_name
											 FROM bs_builder_ui
											 WHERE control_type='size'
											 AND builder_ref=:builder_ref
											 AND active= TRUE
											 LIMIT 1");
				$sql_ui->execute(array(':builder_ref' => $builderRef));

				$row_ui = $sql_ui->fetch(PDO::FETCH_ASSOC);
				$control_name = $row_ui['control_name'];

				//Array to hold value of an Object
				$queries[] = array("sess_id"              => $session_id,
								   "prod_id"              => $productId,
								   "sku_id"               => $skuId,
								   "status"               => $status,
								   "cdate"                => $now,
								   "bsetting"             => $bsetting,
								   "bvalue"               => $bvalue,
								   "bsubsetting"          => $bsubset,
								   "blabel"               => $control_name,
								   "bvalue_text"          => $name,
								   "bsetting_display"     => $display,
								   "bsetbyuser"           => $setbyusers,
								   "bfont"                => $fonts,
								   "bfont_size"           => $font_size,
								   "bcolor"               => $bcolor,
								   "balignment"           => $align,
								   "bfont_setbyuser"      => $font_setbyusers,
								   "bfontsize_setbyuser"  => $fontsize_setbyusers,
								   "balignment_setbyuser" => $textalign_setbyusers);
								   //"material_price"       => $up_price);
			}


			if($builderArray['material']) {

				// $up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
				$opt_type             = null;
				$bsubset              = 'material';
				$bsetting             = 'material';
				$bvalue               = $builderArray['material']['value'];

				if ($builderArray['material']['setbyuser'])
					$setbyusers = 1;
				else
					$setbyusers = 0;

				if ($builderArray['material']['display'])
					$display = 1;
				else
					$display = 0;

				//Query database to get name of material
				$sql_material = Connection::getHandle()->prepare("SELECT name
													 FROM bs_builder_materials
													 WHERE material_ref=:bvalue
													 AND active= TRUE
													 LIMIT 1");
				$sql_material->execute(array(':bvalue' => $bvalue));

				$row_material = $sql_material->fetch(PDO::FETCH_ASSOC);
				$name = $row_material['name'];


				//Query Database to get control name based on control type
				$sql_ui = Connection::getHandle()->prepare("SELECT control_name
											   FROM bs_builder_ui
											   WHERE control_type='material'
											   AND builder_ref=:builder_ref
											   AND active = TRUE
											   LIMIT 1");
				$sql_ui->execute(array(':builder_ref' => $builderRef));

				$row_ui = $sql_ui->fetch(PDO::FETCH_ASSOC);
				$control_name = $row_ui['control_name'];

				//Array to hold value of an Object
				$queries[] = array("sess_id"              => $session_id,
								   "prod_id"              => $productId,
								   "material"             => $materials,
								   "sku_id"               => $skuId,
								   "status"               => $status,
								   "cdate"                => $now,
								   "bsetting"             => $bsetting,
								   "bvalue"               => $bvalue,
								   "bsubsetting"          => $bsubset,
								   "blabel"               => $control_name,
								   "bvalue_text"          => $name,
								   "bsetting_display"     => $display,
								   "bsetbyuser"           => $setbyusers,
								   "bfont"                => $fonts,
								   "bfont_size"           => $font_size,
								   "bcolor"               => $bcolor,
								   "balignment"           => $align,
								   "bfont_setbyuser"      => $font_setbyusers,
								   "bfontsize_setbyuser"  => $fontsize_setbyusers,
								   "balignment_setbyuser" => $textalign_setbyusers);
								   //"material_price"       => $up_price);
			}


			if($builderArray['scheme']) {

				//$up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
				$opt_type             = null;
				$bsubset              = 'scheme';
				$bsetting             = 'scheme';
				$bvalue               = $builderArray['scheme']['value'];
				$setbyusers           = $builderArray['scheme']['setbyuser'] ? 1 : 0;
				$display              = $builderArray['scheme']['display'] ? 1 : 0;

				//Query database to get name of scheme
				$sql_scheme = Connection::getHandle()->prepare("SELECT name
												   FROM bs_builder_schemes
												   WHERE scheme_ref=:bvalue
												   AND active = 1
												   LIMIT 1 ");
				$sql_scheme->execute(array(':bvalue' => $bvalue));

				$row_scheme = $sql_scheme->fetch(PDO::FETCH_ASSOC);
				$name = $row_scheme['name'];

				//Query Database to get control name based on control type
				$sql_ui=Connection::getHandle()->prepare("SELECT control_name
											 FROM bs_builder_ui
											 WHERE control_type='scheme'
											 AND builder_ref=:builder_ref
											 AND active = TRUE
											 LIMIT 1");
				$sql_ui->execute(array(':builder_ref' => $builderRef));

				$row_ui = $sql_ui->fetch(PDO::FETCH_ASSOC);
				$control_name = $row_ui['control_name'];

				//Array to hold values for object
				$queries[] = array("sess_id"              => $session_id,
								   "prod_id"              => $productId,
								   "material"             => $materials,
								   "sku_id"               => $skuId,
								   "status"               => $status,
								   "cdate"                => $now,
								   "bsetting"             => $bsetting,
								   "bvalue"               => $bvalue,
								   "bsubsetting"          => $bsubset,
								   "blabel"               => $control_name,
								   "bvalue_text"          => $name,
								   "bsetting_display"     => $display,
								   "bsetbyuser"           => $setbyusers,
								   "bfont"                => $fonts,
								   "bfont_size"           => $font_size,
								   "bcolor"               => $bcolor,
								   "balignment"           => $align,
								   "bfont_setbyuser"      => $font_setbyusers,
								   "bfontsize_setbyuser"  => $fontsize_setbyusers,
								   "balignment_setbyuser" => $textalign_setbyusers);
								   //"material_price"       => $up_price);

			}



			if ($builderArray['layout']) {

				//$up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
				$opt_type             = null;
				$bsubset              = 'layout';
				$bsetting             = 'layout';
				$bvalue               = $builderArray['layout']['value'];
				$setbyusers           = $builderArray['layout']['setbyuser'] ? 1 : 0;
				$display              = $builderArray['layout']['display'] ? 1 : 0;


				//Query database to get name of layout
				$sql_layout=Connection::getHandle()->prepare("SELECT name AS name
												 FROM bs_builder_layouts
												 WHERE layout_ref=:bvalue
												 AND active= TRUE
												 LIMIT 1");
				$sql_layout->execute(array(':bvalue' => $bvalue));

				$row_layout=$sql_layout->fetch(PDO::FETCH_ASSOC);
				$name=$row_layout['name'];

				//Query Database to get control name based on control type
				$sql_ui=Connection::getHandle()->prepare("SELECT control_name AS control_name
											 FROM bs_builder_ui
											 WHERE control_type='layout'
											 AND builder_ref=:builder_ref
											 AND active= TRUE
											 LIMIT 1");
				$sql_ui->execute(array(':builder_ref' => $builderRef));

				$row_ui=$sql_ui->fetch(PDO::FETCH_ASSOC);
				$control_name=$row_ui['control_name'];

				//Array to hold values for object
				$queries[] = array("sess_id"              => $session_id,
								   "prod_id"              => $productId,
								   "material"             => $materials,
								   "sku_id"             => $skuId,
								   "status"               => $status,
								   "cdate"                => $now,
								   "bsetting"             => $bsetting,
								   "bvalue"               => $bvalue,
								   "bsubsetting"          => $bsubset,
								   "blabel"               => $control_name,
								   "bvalue_text"          => $name,
								   "bsetting_display"     => $display,
								   "bsetbyuser"           => $setbyusers,
								   "bfont"                => $fonts,
								   "bfont_size"           => $font_size,
								   "bcolor"               => $bcolor,
								   "balignment"           => $align,
								   "bfont_setbyuser"      => $font_setbyusers,
								   "bfontsize_setbyuser"  => $fontsize_setbyusers,
								   "balignment_setbyuser" => $textalign_setbyusers);
								  // "material_price"       => $up_price);

			}


			if($builderArray['options']) {

				// $up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$bsubset              = null;
				$bvalue               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;

				$bsetting = 'option';

				foreach($builderArray['options'] as $keys => $value){

					foreach ($value AS $key => $value1) {

						$total_upcharge = '';
						$opt_type       = '';
						$bsubset        = $keys;
						$bvalue         = $value['value'];


						if ($bsubset == "antigraffiti") {
							//Query database to get the laminate name,display etc for options
							$sql_laminate = Connection::getHandle()->prepare("SELECT blam.name AS lam_name,
														 blam.builder_preview_display,
														 blam.option_value
												  FROM bs_builder_laminates blam
												  WHERE
												  blam.option_value = :bvalue
												  AND blam.builder_ref = :builder_ref
												  LIMIT 1");

							$sql_laminate->execute(array(
								':bvalue' => $bvalue,
								':builder_ref' => $builderRef));

                            while ($row = $sql_laminate->fetch(PDO::FETCH_ASSOC)) {

                                $laminate_name = $row['lam_name'];

                            }
						}

						if ($bsubset == "mountingoptions") {
							//Query database to get the mounting hole arrangement name,display etc for options
							$sql_mounting = Connection::getHandle()->prepare("SELECT bmha.name AS mounting_name,
														 bmha.builder_preview_display,
														 bmha.option_value
												  FROM bs_builder_mounting_hole_arrangements bmha
												  WHERE
												  bmha.option_value = :bvalue
												  AND bmha.builder_ref = :builder_ref
												  LIMIT 1");

							$sql_mounting->execute(array(
								':bvalue' => $bvalue,
								':builder_ref' => $builderRef));

                            while ($row = $sql_mounting->fetch(PDO::FETCH_ASSOC)) {

                                $mounting_name = $row['mounting_name'];

                            }
						}
							



						//Query database to get name of control for option
						$sql_control=Connection::getHandle()->prepare("SELECT control_name
														  FROM bs_builder_ui
														  WHERE control_type = 'option'
														  AND control_target = :bsubset
														  AND builder_ref = :builder_ref
														  AND active = TRUE
														  LIMIT 1");
						$sql_control->execute(array(':bsubset'     => $bsubset,
													':builder_ref' => $builderRef));

						$row_control=$sql_control->fetch(PDO::FETCH_ASSOC);
						$blabel=$row_control['control_name'];

						if($value['setbyuser'])
							$setbyusers=1;
						else
							$setbyusers=0;
						if($value['display'])
							$display=1;
						else
							$display=0;

					}

					//Array to hold values for object
					$queries[] = array("sess_id"              => $session_id,
									   "prod_id"              => $productId,
									   "material"             => $materials,
									   "sku_id"             => $skuId,
									   "status"               => $status,
									   "cdate"                => $now,
									   "bsetting"             => $bsetting,
									   "bvalue"               => $bvalue,
									   "bsubsetting"          => $bsubset,
									   "blabel"               => $blabel,
									   "bvalue_text"          => $name,
									   "bsetting_display"     => $display,
									   "bsetbyuser"           => $setbyusers,
									   "bfont"                => $fonts,
									   "bfont_size"           => $font_size,
									   "bcolor"               => $bcolor,
									   "balignment"           => $align,
									   "bfont_setbyuser"      => $font_setbyusers,
									   "bfontsize_setbyuser"  => $fontsize_setbyusers,
									   "balignment_setbyuser" => $textalign_setbyusers);
									   // "material_price"       => $up_price);
				}
			}

			if($builderArray['elements']) {

				//$up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$bsubset              = null;
				$bvalue               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
			 	$opt_type             = null;
				$bsetting             = 'elements';

				foreach ($builderArray['elements'] as $keys => $value) {

					foreach ($value as $key => $value1) {

						$name      = '';
						$type      = $value['type'];
						$bsetting  = $value['type'];
						$bsubset   = $keys;
						$bvalue    = $value['value'];
						$bcolor    = $value['color'];
						$fonts     = $value['font'];
						$font_size = $value['fontsize'];
						$align     = $value['textalign'];

						if ($type == 'artwork' || $type == 'upload') {

							$control_type = 'artwork';

							//Query Database to get control name based on control type
							$sql_artwork_ui = Connection::getHandle()->prepare("SELECT control_name
																   FROM bs_builder_ui
																   WHERE control_type = :type
																   AND control_target = :bsubset
																   AND builder_ref    = :builder_ref
																   AND active         = 'Y'");
							$sql_artwork_ui->execute(array(':type'        => $control_type,
														   ':bsubset'     => $bsubset,
														   ':builder_ref' => $builderRef));

							while($row_artwork_ui = $sql_artwork_ui->fetch(PDO::FETCH_ASSOC)) {
								$blabel = $row_artwork_ui['control_name'];
							}


							//Query Database get name for artwork
							$sql_artwork = Connection::getHandle()->prepare("SELECT name
																FROM bs_builder_artwork
																WHERE artwork_ref = :bvalue
																AND active = 'Y'");
							$sql_artwork->execute(array(':bvalue' => $bvalue));

							while ($row_artwork = $sql_artwork->fetch(PDO::FETCH_ASSOC)) {
								$name = $row_artwork['name'];
							}

						}

						if ($type == 'upload') {

							//Query Database get name  for uploads
							$sql_upload = Connection::getHandle()->prepare("SELECT name
															   FROM bs_builder_uploads
															   WHERE hash=:bvalue");
							$sql_upload->execute(array(':bvalue' => $bvalue));

							while($row_upload=$sql_upload->fetch(PDO::FETCH_ASSOC)) {
								$name=$row_upload['name'];
							}

						}

						if ($type == 'text') {

							//Query Database get control_name details for text or textarea
							$sql_text_ui = Connection::getHandle()->prepare("SELECT control_name
																FROM bs_builder_ui
																WHERE (control_type IN ('text','textarea','textselect'))
																AND control_target = :bsubset
																AND builder_ref = :builder_ref
																AND active = 'Y'");
							$sql_text_ui->execute(array(':bsubset' => $bsubset,
														':builder_ref' => $builderRef));

							while($row_text_ui = $sql_text_ui->fetch(PDO::FETCH_ASSOC)) {
								$blabel = $row_text_ui['control_name'];
							}

							$name = $bvalue;
						}

						//Store values to temporary variable
						if($value['setbyuser'])
							$setbyusers=1;
						else
							$setbyusers=0;

						if($value['display'])
							$display=1;
						else
							$display=0;

						if($value['font_setbyuser'])
							$font_setbyusers=1;
						else
							$font_setbyusers=0;

						if($value['fontsize_setbyuser'])
							$fontsize_setbyusers=1;
						else
							$fontsize_setbyusers=0;

						if($value['textalign_setbyuser'])
							$textalign_setbyusers=1;
						else
							$textalign_setbyusers=0;

						if($name === NULL)
							$name = $bvalue;
						if($name == '')
							$name = $bvalue;
						if($bsubset == '')
							$bsubset = $type;

					}

					//Array to hold value of an Object
					$queries[] = array("sess_id"              => $session_id,
									   "prod_id"              => $productId,
									   "material"             => $materials,
									   "sku_id"               => $skuId,
									   "status"               => $status,
									   "cdate"                => $now,
									   "bsetting"             => $bsetting,
									   "bvalue"               => $bvalue,
									   "bsubsetting"          => $bsubset,
									   "blabel"               => $blabel,
									   "bvalue_text"          => $name,
									   "bsetting_display"     => $display,
									   "bsetbyuser"           => $setbyusers,
									   "bfont"                => $fonts,
									   "bfont_size"           => $font_size,
									   "bcolor"               => $bcolor,
									   "balignment"           => $align,
									   "bfont_setbyuser"      => $font_setbyusers,
									   "bfontsize_setbyuser"  => $fontsize_setbyusers,
									   "balignment_setbyuser" => $textalign_setbyusers
									   // "material_price"       => $up_price
					);

				}

			}

			if($builderArray['schemecolors']) {

				//$up_price             = null;
				$control_name         = null;
				$bcolor               = null;
				$bsubset              = null;
				$bvalue               = null;
				$fonts                = null;
				$align                = null;
				$name                 = null;
				$font_size            = null;
				$display              = null;
				$setbyusers           = null;
				$font_setbyusers      = null;
				$fontsize_setbyusers  = null;
				$textalign_setbyusers = null;
				$opt_type             = null;
				$bsetting             = 'schemecolor';

				// Loop through the scheme colors
				foreach($builderArray['schemecolors'] as $keys => $value) {

					$bvalue = ($value['value'] === null ? '' : $value['value']);
					$bsubset = $keys;

					//Query database to get name of colors
					$sql_color_name = Connection::getHandle()->prepare("SELECT name
														   FROM bs_builder_colors
														   WHERE colors_ref=:bvalue");
					$sql_color_name->execute(array(':bvalue' => $bvalue));

					while ($row_color_name = $sql_color_name->fetch(PDO::FETCH_ASSOC)) {
						$name = $row_color_name['name'];
					}

					if($value['setbyuser'])
						$setbyusers=1;
					else
						$setbyusers=0;

					if($value['display'])
						$display=1;
					else
						$display=0;

					//Query database to get control_name for color
					$sql_text_ui=Connection::getHandle()->prepare("SELECT control_name
													  FROM bs_builder_ui
													  WHERE control_type='color'
													  AND control_target=:bsubset
													  AND builder_ref=:builder_ref");
					$exuc=$sql_text_ui->execute(array(':bsubset' => $bsubset,
													  ':builder_ref' => $builderRef));


					while($row_text_ui = $sql_text_ui->fetch(PDO::FETCH_ASSOC)) {
						$control_name = $row_text_ui['control_name'];
					}

					if($control_name==null)
						$control_name='';

					//Array to hold values of object
					$queries[] = array("sess_id"              => $session_id,
									   "prod_id"              => $productId,
									   "sku_id"               => $skuId,
									   "status"               => $status,
									   "cdate"                => $now,
									   "bsetting"             => $bsetting,
									   "bvalue"               => $bvalue,
									   "bsubsetting"          => $bsubset,
									   "blabel"               => $control_name,
									   "bvalue_text"          => $name,
									   "bsetting_display"     => $display,
									   "bsetbyuser"           => $setbyusers,
									   "bfont"                => $fonts,
									   "bfont_size"           => $font_size,
									   "bcolor"               => $bcolor,
									   "balignment"           => $align,
									   "bfont_setbyuser"      => $font_setbyusers,
									   "bfontsize_setbyuser"  => $fontsize_setbyusers,
									   "balignment_setbyuser" => $textalign_setbyusers);
									   //"material_price"       => $up_price);
				}

			}

			if ($mode == 'add') {

				//Loop through each value of an array
				foreach ($queries as $query) {

					$sess_id              = $query['sess_id'];
					$prod_id              = $query['prod_id'];
					$material             = $query['material'];
					$sku_id             = $query['sku_id'];
					$status               = $query['status'];
					$cdate                = $query['cdate'];
					$bsetting             = $query['bsetting'];
					$bvalue               = $query['bvalue'];
					$bsubsetting          = $query['bsubsetting'];
					$blabel               = $query['blabel'];
					$bvalue_text          = $query['bvalue_text'];
					$bsetting_display     = $query['bsetting_display'];
					$bsetbyusers          = $query['bsetbyuser'];
					$bfont                = $query['bfont'];
					$bfont_size           = $query['bfont_size'];
					$bcolor               = $query['bcolor'];
					$balignment           = $query['balignment'];
					$bfont_setbyuser      = $query['bfont_setbyuser'];
					$bfontsize_setbyuser  = $query['bfontsize_setbyuser'];
					$balignment_setbyuser = $query['balignment_setbyuser'];
					// $material_price       = $query['material_price'];

					//if ($material_price == NULL)
						//$material_price = 0.00;

					$sql_insert = "INSERT INTO bs_cart_sku_data (
								   cart_sku_id,
								   upcharge_price,
								   builder_setting,
								   builder_value,
								   builder_subsetting,
								   builder_label,
								   builder_value_text,
								   builder_setting_display,
								   builder_setbyuser,
								   builder_font,
								   builder_fontsize,
								   builder_color,
								   builder_alignment,
								   builder_font_setbyuser,
								   builder_fontsize_setbyuser,
								   builder_alignment_setbyuser)
								   VALUES (
								   :cart_sku_id,
								   :upcharge_price,
								   :builder_setting,
								   :builder_value,
								   :builder_subsetting,
								   :builder_label,
								   :builder_value_text,
								   :builder_setting_display,
								   :builder_setbyuser,
								   :builder_font,
								   :builder_fontsize,
								   :builder_color,
								   :builder_alignment,
								   :builder_font_setbyuser,
								   :builder_fontsize_setbyuser,
								   :builder_alignment_setbyuser)";


					$stmt_insert = Connection::getHandle()->prepare($sql_insert);

					//execute the query
					$stmt_insert->execute(array(
						":cart_sku_id"			   	   => $cart_sku_id,
						":upcharge_price"			   => NULL,
						":builder_setting"             => $bsetting,
						":builder_value"               => $bvalue,
						":builder_subsetting"          => $bsubsetting,
						":builder_label"               => $blabel,
						":builder_value_text"          => $bvalue_text,
						":builder_setting_display"     => $bsetting_display,
						":builder_setbyuser"           => $bsetbyusers,
						":builder_font"                => $bfont,
						":builder_fontsize"            => $bfont_size,
						":builder_color"               => $bcolor,
						":builder_alignment"           => $balignment,
						":builder_font_setbyuser"      => $bfont_setbyuser,
						":builder_fontsize_setbyuser"  => $bfontsize_setbyuser,
						":builder_alignment_setbyuser" => $balignment_setbyuser));


				}

			} else if($mode=="edit" || $mode=="adminedit") {

				//Loop through each value of an array
				foreach($queries as $query) {

					$cart_id              = '';
					$sess_id              = $query['sess_id'];
					$prod_id              = $query['prod_id'];
					$material             = $query['material'];
					$skuId                = $query['sku_id'];
					$status               = $query['status'];
					$cdate                = $query['cdate'];
					$bsetting             = $query['bsetting'];
					$bvalue               = $query['bvalue'];
					$bsubsetting          = $query['bsubsetting'];
					$blabel               = $query['blabel'];
					$bvalue_text          = $query['bvalue_text'];
					$bsetting_display     = $query['bsetting_display'];
					$bsetbyusers          = $query['bsetbyuser'];
					$bfont                = $query['bfont'];
					$bfont_size           = $query['bfont_size'];
					$bcolor               = $query['bcolor'];
					$balignment           = $query['balignment'];
					$bfont_setbyuser      = $query['bfont_setbyuser'];
					$bfontsize_setbyuser  = $query['bfontsize_setbyuser'];
					$balignment_setbyuser = $query['balignment_setbyuser'];
					// $material_price       = $query['material_price'];

					//if ($material_price == NULL)
					//	$material_price = 0.00;

					//Query database to get exact matching id for subsetting
					$sql_cart = Connection::getHandle()->prepare("SELECT id
													 FROM bs_cart_sku_data
													 WHERE cart_sku_id = :sid
													 AND builder_subsetting = :bsubsetting");


					$result_cart = $sql_cart->execute(array(":sid" => $cart_sku_id,
															":bsubsetting" => $bsubsetting));

					while($row_cart = $sql_cart->fetch(PDO::FETCH_ASSOC)) {
						$cart_id = $row_cart['id'];
					}


					//Check if already existing id for subsetting
					if ($cart_id) {

						if ($mode == 'adminedit') {

							//Prepare update query
							$sql_update = "UPDATE bs_cart_sku_data
										   SET builder_setting             = :bsetting,
											   builder_value               = :bvalue,
											   builder_subsetting          = :bsubset,
											   builder_label               = :blabel,
											   builder_value_text          = :bvalue_text,
											   builder_setting_display     = :bsetting_display,
											   builder_setbyuser           = :bsetbyuser,
											   builder_font                = :bfont,
											   builder_fontsize            = :bfont_size,
											   builder_color               = :bcolor,
											   builder_alignment           = :balign,
											   builder_font_setbyuser      = :bfont_setbyuser,
											   builder_fontsize_setbyuser  = :bfontsize_setbyuser,
											   builder_alignment_setbyuser = :balignment_setbyuser,
											   upcharge_price              = :upcharge_price
										   WHERE cart_sku_id = :sid
										   AND id = :cartid";



							$stmt_update = Connection::getHandle()->prepare($sql_update);

							//Execute update query
							$execute = $stmt_update->execute(array(":sid"                  => $cart_sku_id,
																   ":cartid"               => $cart_id,
																   ":bsetting"             => $bsetting,
																   ":bvalue"               => $bvalue,
																   ":bsubset"              => $bsubsetting,
																   ":blabel"               => $blabel,
																   ":bvalue_text"          => $bvalue_text,
																   ":bsetting_display"     => $bsetting_display,
																   ":bsetbyuser"           => $bsetbyusers,
																   ":bfont"                => $bfont,
																   ":bfont_size"           => $bfont_size,
																   ":bcolor"               => $bcolor,
																   ":balign"               => $balignment,
																   ":bfont_setbyuser"      => $bfont_setbyuser,
																   ":bfontsize_setbyuser"  => $bfontsize_setbyuser,
																   ":balignment_setbyuser" => $balignment_setbyuser));
																   //":upcharge_price"       => $material_price));
						} else if($mode == "edit") {

							//Prepare update query
							$sql_update = "UPDATE bs_cart_sku_data
										   SET
											   builder_setting             = :bsetting,
											   builder_value               = :bvalue,
											   builder_subsetting          = :bsubset,
											   builder_label               = :blabel,
											   builder_value_text          = :bvalue_text,
											   builder_setting_display     = :bsetting_display,
											   builder_setbyuser           = :bsetbyuser,
											   builder_font                = :bfont,
											   builder_fontsize            = :bfont_size,
											   builder_color               = :bcolor,
											   builder_alignment           = :balign,
											   builder_font_setbyuser      = :bfont_setbyuser,
											   builder_fontsize_setbyuser  = :bfontsize_setbyuser,
											   builder_alignment_setbyuser = :balignment_setbyuser
											WHERE cart_sku_id = :sid
											AND id = :cartid";

							$stmt_update = Connection::getHandle()->prepare($sql_update);


							//Execute update query
							$execute = $stmt_update->execute(array(":sid"                  => $cart_sku_id,
																	":cartid"               => $cart_id,
																    ":bsetting"             => $bsetting,
																    ":bvalue"               => $bvalue,
																    ":bsubset"              => $bsubsetting,
																    ":blabel"               => $blabel,
																    ":bvalue_text"          => $bvalue_text,
																    ":bsetting_display"     => $bsetting_display,
																    ":bsetbyuser"           => $bsetbyusers,
																    ":bfont"                => $bfont,
																    ":bfont_size"           => $bfont_size,
																    ":bcolor"               => $bcolor,
																    ":balign"               => $balignment,
																    ":bfont_setbyuser"      => $bfont_setbyuser,
																    ":bfontsize_setbyuser"  => $bfontsize_setbyuser,
																    ":balignment_setbyuser" => $balignment_setbyuser));
																    // ":material_price"       => $material_price));

						}

					} else {

						$stmt_insert = Connection::getHandle()->prepare("INSERT INTO bs_cart_sku_data (
																cart_sku_id,
																builder_setting,
																builder_value,
																builder_subsetting,
																builder_label,
																builder_value_text,
																builder_setting_display,
																builder_setbyuser,
																builder_font,
																builder_fontsize,
																builder_color,
																builder_alignment,
																builder_font_setbyuser,
																builder_fontsize_setbyuser,
																builder_alignment_setbyuser,
															)
															VALUES
															(
																:sid,
																:bsetting,
																:bvalue,
																:bsubsetting,
																:blabel,
																:bvalue_text,
																:bsetting_display,
																:bsetbyuser,
																:bfont,
																:bfont_size,
																:bcolor,
																:balignment,
																:bfont_setbyuser,
																:bfontsize_setbyuser,
																:balignment_setbyuser)");

						//execute insert query
						$stmt_insert->execute(array(":sid"                  => $cart_sku_id,
													":bsetting"             => $bsetting,
													":bvalue"               => $bvalue,
													":bsubsetting"          => $bsubsetting,
													":blabel"               => $blabel,
													":bvalue_text"          => $bvalue_text,
													":bsetting_display"     => $bsetting_display,
													":bsetbyuser"           => $bsetbyusers,
													":bfont"                => $bfont,
													":bfont_size"           => $bfont_size,
													":bcolor"               => $bcolor,
													":balignment"           => $balignment,
													":bfont_setbyuser"      => $bfont_setbyuser,
													":bfontsize_setbyuser"  => $bfontsize_setbyuser,
													":balignment_setbyuser" => $balignment_setbyuser));

					}

				}

			}


			//Instantiate render class
			$Objrender = new Render();

			if ($mode == 'edit' || $mode == 'adminedit') {

				if ($mode == 'adminedit') {

					$stmt = Connection::getHandle()->prepare(
                                "SELECT hash FROM bs_designs d
												 LEFT JOIN bs_cart_skus s
												 ON (d.id = s.design_id)
												 WHERE s.id = :sid");

					$s = $stmt->execute(array(":sid" => $cart_sku_id));

				} else {

					$stmt = Connection::getHandle()->prepare(
                                'SELECT hash FROM bs_designs d  LEFT JOIN bs_cart_skus s ON d.id = s.design_id
                                 WHERE s.id = :sid'
                    );

                    $s = $stmt->execute(array(":sid" => $cart_sku_id));

				}

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$hash = $row['hash'];
				}

				//Generate the rendered file
				$generated = $Objrender->generateFile($renderData, $hash);

				//Error checking
				if ($generated) {

					//Insert all the new renderdata and link it to the same hash
					$render_id = $Objrender->renderBuilder($renderData, $hash);

				} else {

					$errors[] = "An unknown error has occurred.";

				}


			} else {

				//Generates a new hash, calls generateFile() on the renderdata, and returns the hash
				$hash = $Objrender->createFile($renderData);

				//Check if the file was created successfully
				if ($hash) {

					//Insert all the new renderdata and link it to the same hash
					$render_id = $Objrender->renderBuilder($renderData, $hash);

					//Get the design id from the new data
					$sql1 = Connection::getHandle()->prepare("SELECT id
												 FROM bs_designs
												 WHERE hash=:hash");
					$stmt1=$sql1->execute(array(":hash" => $hash));

					while ($row_id = $sql1->fetch(PDO::FETCH_ASSOC)) {
						$did = $row_id['id'];
					}

					//Update the shopping cart to link to the new design id
					$stmt2 = Connection::getHandle()->prepare("UPDATE bs_cart_skus SET design_id=:did WHERE id=:sid");
					$stmt2->execute(array(":did"=>$did,":sid"=>$cart_sku_id));

				//If the file was not created successfully, we will remove the item from the cart and throw an error
				} else {

					$sql1 = Connection::getHandle()->prepare("DELETE FROM bs_cart_skus WHERE id = ?");
					$sql1->execute(array($cart_sku_id));
					$errors[] = "An unknown error has occurred.";

				}

			}

			//Get the url for the new image
			$image_path = $Objrender->imageURL($hash);

			//Any notices that need to be output
			$notices[] = "Custom items require an additional 3 business days to produce.";

			// Update the cart contents
			$this->setProperties();

			//Pull cart total items and total value
			$count = $this->getTotalQuantity(); //All items in cart
			$subtotal = $this->getSubTotal(); //Total price of cart

			//Set success based on any returned errors
			if (empty($errors)) { $success = true; } else { $success = false; }

			//Return array to be parsed as JSON
			return (array('designid' => $hash,
						  'success'  => $success,
						  'notices'  => $notices,
						  'errors'   => $errors,
						  'image'    => $image_path,
						  'count'    => $count,
						  'subtotal' => $subtotal));

		}


	}



	/**
	 * Returns an array of freight info for the cart (used in admin checkout admin insight section)
	 * @return array - freight info
	 */
	public function getFreightInfo() {

		foreach($this->products as $product) {
			$row[] = array('true_weight' => $product->trueWeight * $product->quantity,
						   'number_pkgs' => $product->numberOfPackages,
						   'dim_weight_ups' => $product->dimWeightUps,
						   'dim_weight_fedex' => $product->dimWeightFedex);

		}

		return $row;

	}


	/*
	 * Gets dimensional charges for the cart based on a shipping carrier
	 * @param string $carrier
	 * @return array
	 */
	public function getDimCharges($carrier) {

		$weight = 0;
		$upsCharges = 0;
		$fedexCharges = 0;
		$pkgs = 0;

		foreach($this->products AS $product) {

			$weight += $product->weight * $product->quantity;
			$upsCharges += (mb_strtolower($product->dimWeightUps) == 'y' ? $product->dimChargesUps * $product->quantity : 0);
			$fedexCharges += (mb_strtolower($product->dimWeightFedex) == 'y' ? $product->dimChargesUps * $product->quantity : 0);
			$pkgs += $product->numberOfPackages * $product->quantity;

		}

		$row['weight'] = $weight;
		$row['charges'] = (mb_strtolower($carrier) == 'ups' ? $upsCharges : $fedexCharges);
		$row['pkgs'] = $pkgs;

		return $row;

	}



	/**
	 * Determines whether any items in the cart have proofs requested.
	 * @return bool true if any items have proofs requested, false if none do.
	 */
	public function proofsRequested() {

		$proof = false;

		foreach($this->products AS $product) {
			if (mb_strpos($product->comments, 'proof') !== false) {
				$proof = true;
			}
		}

		return $proof;

	}


	/**
	 * Seeds the random generator
	 * @return    Float    Microtime
	 */
	private static function makeSeed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}


	/**
	 * Generates a random unique design ID
	 * @return    string    unique design ID
	 */
	public function getUniqueDesignId() {
		//Seed the random generator
		mt_srand(self::makeSeed());
		//Alphanumeric upper/lower array
		$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
		$design = "";
		//Loop through and generate the random design id
		for($i = 0; $i < 32; $i ++) {
		  $design .= $alfa[mt_rand(0, strlen($alfa)-1)];
		}
		//If there is a duplicate, run this function recursively
		if(!$this->isDesignIdUnique($design)) {
			$design = $this->getUniqueDesignId();
		}
		//Return the hash
		return $design;
	}



	/**
	 * This function takes a generated design id and checks to verify that it is unique
	 * @param     string    $design    [description]
	 * @return    bool                 true if unique, false if not
	 */
	private function isDesignIdUnique($design) {
		$sql = Connection::getHandle()->prepare(
                "SELECT count(*) AS count FROM bs_product_custom WHERE design_id = ?"
        );

		$sql->execute(array($design));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return ($row['count'] > 0 ? false : true);
	}



	/**
	 * Generates a random unique hash
	 * @return    string    unique hash
	 */
	static function getCartHash() {

		//Seed the random generator
		mt_srand(self::makeSeed());

		//Alphanumeric upper/lower array
		$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
		$hash = "";

		//Loop through and generate the random hash
		for($i = 0; $i < 32; $i ++) {
		  $hash .= $alfa[mt_rand(0, strlen($alfa)-1)];
		}

		//If there is a duplicate, run this function recursively
		if(!self::isHashUnique($hash)) {
			$hash = self::getCartHash();
		}

		//Return the hash
		return $hash;
	}



	/**
	 * This function takes a generated hash and checks to verify that it is unique
	 * @param     string    $hash    [a hash to check for]
	 * @return    bool               [true if unique, false if not]
	 */
	static function isHashUnique($hash) {


		$sql = Connection::getHandle()->prepare(
                    "SELECT count(*) AS count FROM bs_cart_hashes WHERE hash = ?"
        );

		$sql->execute(array($hash));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return ($row['count'] > 0 ? false : true);
	}



	/**
	 * Updates the amount of available hashes to be used by cart
	 */
	public static function updateHashBuffer() {

		$sql = Connection::getHandle()->prepare("SELECT count(id) as count FROM bs_cart_hashes WHERE in_use IS NOT TRUE");

        if( $sql->execute() ) {

            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $count = $row['count'];

            if( $count < self::BUFFER_SIZE ) {

                $hashes_to_generate = self::BUFFER_SIZE - $count;

                for ($i = 0; $i < $hashes_to_generate; $i++) {

                    $hash = self::getCartHash();
                    $sql = Connection::getHandle()->prepare("INSERT INTO bs_cart_hashes (hash, in_use) VALUES (?, 0)");
                    $sql->execute(array ($hash));
                }
            }
        }
	}



	/**
	 * Gets a cart by its name and customer id
	 * @param  [string] $name [The cart name]
	 * @param  [int] $customerId [The customer id]
	 * @return [int] [the id of the cart if one does exist, otherwise false]
	 */
	static function getCartByName($name, $customerId) {

		$sql = Connection::getHandle()->prepare(
                "SELECT hash as hash FROM bs_cart_hashes ch
                 LEFT JOIN bs_carts c ON c.hash_id = ch.id WHERE c.name = ? AND c.customer_id = ?"
        );

		$sql->execute(array($name, $customerId));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		// Check if this is a cart. If so, instantiate the cart and return it. Otherwise return false;
		if (!empty($row['hash'])) {

			return new Cart($row['hash']);

		} else {
			return false;
		}

	}

    /**
     * Gets a saved cart by its name and customer id
     * @param  [string] $name [The cart name]
     * @param  [int] $customerId [The customer id]
     * @return [boolean] [the id of the hash if one does exist, otherwise false]
     */
    static function getSavedCart($name, $customerId) {

        $sql = Connection::getHandle()->prepare(
            "SELECT hash as hash FROM bs_cart_hashes ch
                 LEFT JOIN bs_carts c ON c.hash_id = ch.id WHERE c.name = ? AND c.customer_id = ? AND c.saved = TRUE"
        );

        $sql->execute(array($name, $customerId));
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        // Check if this is a cart. If so, instantiate the cart and return it. Otherwise return false;
        if (!empty($row['hash'])) {

            return new Cart($row['hash']);

        } else {
            return false;
        }

    }

	/**
	 * This serves as a wrapper for product remove() functions.
	 * It checks to make sure a product exists before it tries to call the product remove function
	 * to avoid 500 errors
	 *
	 * @param  int $cartProductId [id of the cart product]
	 * @return bool                [true/false on success/failure]
	 */
	public function removeProduct($cartProductId) {

		// Check to make sure that they are referring to a real product before we call the remove function
		if (!empty($this->products[$cartProductId])) {

			// Call the remove function on the product now that we are sure it exists
			$success = $this->products[$cartProductId]->remove();

			// Update our cart object so it is in sync
			$this->setProperties();

			// Return
			return $success;

		} else {

			return false;
		}

	}



	/**
	 * @param $cartProductId - The id of the cart product
	 * @param $quantity - the quantity to update it to
	 * @param $updateCartProperties - Whether to update the cart properties or not
	 *                                (Sometimes when updating many product quantities in a loop, we do not want our cart updated on each iteration (this mimizes DB calls))
	 *
	 * @return bool
	 */
	public function updateProductQuantity($cartProductId, $quantity, $updateCartProperties = TRUE) {

		// Check to make sure that they are referring to a real product before we call the updateQuantity function
		if (!empty($this->products[$cartProductId])) {

			// Call the updateQuantity on the product now that we are sure it exists
			$success = $this->products[$cartProductId]->updateQuantity($quantity, $this->products[$cartProductId]);

			// Ensure that our update was successful, and that we want to update our cart properties
			if ($success && $updateCartProperties) {

				// Update our cart object so it is in sync
				$this->setProperties();
			}
			// Return
			return $success;

		} else {

			return false;
		}

	}



	public function getSalesTax($shippingArray, $taxExempt) {

		global $objCheckout;

		// Gather all the needed info
		$subtotal = $this->getSubtotal();
		$shippingCharges = isset($_SESSION['shipping_charges_pre']) ? $_SESSION['shipping_charges_pre'] : NULL;
		$zip = isset($_SESSION['postcode']) ? $_SESSION['postcode'] : NULL;
		$state = isset($_SESSION['state']) ? $_SESSION['state'] : NULL;

		// Pull tax exempt status and zip code from the HTTP request if they weren't passed into the function as arguments.
		if (!isset($taxExempt)) { $taxExempt = $objCheckout->tax_exempt; }
		if (!isset($zip)) { $zip = $objCheckout->shipping_address['zip']; }

		// If a zip code is available, use that to check tax instead of whatever was passed as an argument or HTTP request.
		if (isset($zip)) {

			// Get a valid five-digit zip code string from the $zip variable.
			preg_match("/[0-9]{5}/", $zip, $matches);
			$fivedigitzip = $matches[0];

			// Poll the DB for the state from the zip
			$sql_zipcode_exist = Connection::getHandle()->prepare(
                        "SELECT state FROM bs_zipcodes WHERE zip=:zipcode LIMIT 1"
            );

			$sql_zipcode_exist->execute(array(":zipcode"=>$fivedigitzip));

			$zipcode_data = $sql_zipcode_exist->fetch(PDO::FETCH_ASSOC);

			$state = $zipcode_data['state'];

		} else {

			// Pull the state from the HTTP request if it wasn't passed into the function as an argument.
			if (!isset($state)) {
				$state = $objCheckout->shipping_address['state'];
			}

		}

		// If they live in NJ and are NOT tax exempt, calculate the tax
		if ($state == "NJ" && ($taxExempt != 'Y' && $taxExempt !== true)) {

			$salesTax = round(($subtotal / 100) * 7, 2);

		// Otherwise no tax
		} else {
			$salesTax = 0;
		}

		return $salesTax;

	}



	/**
	 * Returns the cart product id of a builder with a specific design ID if a match is found,
	 * otherwise returns false
	 *
	 * @param  designId $designId [The builder design ID]
	 * @return id                 [The cart product id (or false, if none found)]
	 */
	public function getProductIdByDesign($designId) {

		foreach($this->products as $key => $product) {
			if ($designId == $product->designInfo['hash']) {
				return $key;
			}
		}

		return false;
	}

    public function getSkuIdBySkuCode($skuCode){

        $sql = "SELECT id FROM bs_skus WHERE name = ? AND active = TRUE";

        $sql_query = Connection::getHandle()->prepare($sql);

        $sql_query->execute(array($skuCode));
        $row = $sql_query->fetch(PDO::FETCH_ASSOC);

        return $row['id'];
    }



	public function storeInSession() {

		$_SESSION['cartHash'] = $this->cartHash;
		return $this;

	}

	public function removeFromSession() {

		$_SESSION['cartHash'] = NULL;
		return $this;

	}

	public static function checkHash($hash) {

		$sth = Connection::getHandle()->prepare(
		            'SELECT COUNT(*) FROM bs_cart_hashes h
		                INNER JOIN bs_carts c ON ( c.hash_id = h.id )
                     WHERE h.hash = :hash AND h.in_use = 1'
        );

		$sth->execute(array( ':hash' => $hash ));
		return (bool) $sth->fetchColumn();

	}



	public static function getFromSession($createIfNecessary = FALSE) {

		if ( isset($_SESSION, $_SESSION['cartHash']) && !empty($_SESSION['cartHash']) && self::checkHash($_SESSION['cartHash']) ) {

			$cart = new self($_SESSION['cartHash']);

		} elseif ( $createIfNecessary ) {

			$cart = self::createNew()->storeInSession();

		} else {

			$cart = NULL;

		}
		return $cart;
	}



	public static function createNew($saved = FALSE, $ordered = FALSE) {

		return new self(NULL, NULL, (bool) $saved, (bool) $ordered);

	}



	/**
	 * Takes an order ID and returns the cart hash to go with it
	 * @param  int    $orderId [ID from bs_orders]
	 * @return string          [Cart hash]
	 */
	public static function getCartFromOrderId($orderId) {

		$sql = Connection::getHandle()->prepare("SELECT ch.hash AS hash FROM bs_orders o LEFT JOIN bs_carts c ON (c.id = o.cart_id)
							  LEFT JOIN bs_cart_hashes ch ON (ch.id = c.hash_id) WHERE o.orders_id = ?");

		$sql->execute(array($orderId));

		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return new self(!empty($row['hash']) ? $row['hash'] : false);

	}



	/**
	 * Takes an order number and returns the cart hash to go with it
	 * @param  int    $orderNumber [ID from bs_orders]
	 * @return string              [Cart hash]
	 */
	public static function getCartFromOrderNumber($orderNumber) {

		$sql = Connection::getHandle()->prepare("SELECT ch.hash AS hash FROM bs_orders o
							  LEFT JOIN bs_carts c ON (c.id = o.cart_id)
							  LEFT JOIN bs_cart_hashes ch ON (ch.id = c.hash_id)
							  WHERE o.order_no = ?");

		$sql->execute(array($orderNumber));

		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return new self(!empty($row['hash']) ? $row['hash'] : false);

	}
}
