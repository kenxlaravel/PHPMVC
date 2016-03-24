<?php


class CartProductStreetname extends CartProduct {

	// Hard-coded tool type so we can differentiate between this and other product types
	public $type = 'streetname';

	// from bs_cart_products
	public $streetsign_custom_copy1;
	public $streetsign_custom_copy2;
	public $streetsign_num;
	public $streetsign_background;
	public $streetsign_suffix;
	public $streetsign_prefix;
	public $streetsign_left_arrow;
	public $streetsign_right_arrow;
	public $streetsign_position;
	public $streetsign_color;
	public $streetsign_font;
	public $comments;
	public $proofRequested;
	public $accuracyImage;

	// from bs_cart_product_attributes
	public $upcharges = array();

	// From bs_streetsign_uploads
	public $fileUpload = array();

	// From bs_product_custom
	public $customImage = array();



	/**
	 * Constructor
	 *
	 * If a data array is passed in, our class properties will be set from the data.
	 * Otherwise we will run a query to get all of the info about this product.
	 *
	 * @param [int]   $id       [ID of the product from bs_cart_products]
	 * @param [type]  $objCart  [Handle for the cart object that this object is linked to]
	 * @param [array] $data     [Optional array of class properties so we can avoid querying for them]
	 */
	public function __construct($id, $objCart, $data = array()) {

		// Pass the info up to the parent cart product
		parent::__construct($id, $objCart);

		// Get or set properties
		if (!empty($data) && $id > 0) {
			$this->setProperties($data);
		} else if ($id > 0) {
			$this->setProperties();
		}

	}



	/**
	 * Sets class properties from the results of a DB query
	 * @return [bool] [true / false on success or failure]
	 */
	public function setProperties($data = null) {

		// If no data was passed in, we will have to query the DB
		if ($data == null) {

			// Select what we need from the database
			$sql = Connection::getHandle()->prepare("SELECT
											p.id AS id,
											p.cart_id AS cart_id,
											p.product_id AS product_id,
											p.source_product_id,
			                                p.source_product_recommendation_id,
			                                p.source_accessory_family_product_id,
			                                p.source_installation_accessory_id,
			                                p.source_landing_product_id,
			                                p.source_subcategory_product_id,
											tt.name AS tool_type,
											p.quantity AS quantity,
											p.unit_price AS unit_price,
											p.total_price AS total_price,
											p.savings_percentage AS savings_percentage,
											p.design_id AS design_id,
											p.custom_image_id AS custom_image_id,
											prr.material_code AS material_code,
											p.eps AS eps,
											p.pdf AS pdf,
											p.streetsign_custom_copy1 AS streetsign_custom_copy1,
											p.streetsign_custom_copy2 AS streetsign_custom_copy2,
											p.streetsign_num AS streetsign_num,
											p.streetsign_background AS streetsign_background,
											p.streetsign_suffix AS streetsign_suffix,
											p.streetsign_prefix AS streetsign_prefix,
											p.streetsign_left_arrow AS streetsign_left_arrow,
											p.streetsign_right_arrow AS streetsign_right_arrow,
											p.streetsign_position AS streetsign_position,
											p.streetsign_color AS streetsign_color,
											p.streetsign_font AS streetsign_font,
											p.design_service AS design_service,
											p.comments AS comments,
											p.creation_time AS creation_time,
											p.modification_time AS modification_time,
											a.id AS attribute_id,

											-- a.streetsign_upcharge_id AS streetname_upcharge_id,

											a.upcharge_price AS upcharge_price,

											st.name AS streetname_upcharge_name,
											sd.name AS streetname_upcharge_type,

											c.custom_product_id AS custom_image_id,
											c.custom_image AS custom_image,

											u.id AS streetsign_upload_id,
											u.file_name AS upload_name,
											u.file_size AS upload_size,
											u.file_type AS upload_type,

											pr.expiration AS expiration_date,
											pr.page_subtitle AS subtitle,
											pr.page_title AS nickname,

											pr.product_number AS product_number,
											pr.active AS active,

											sd.small_image AS product_image,
											sd.shipping_weight AS weight,
											sd.weight AS true_weight,
											sd.dedicated_package_count AS number_pkgs,

											sd.ups_shipping_surcharge AS dim_charges_ups,
											sd.fedex_shipping_surcharge AS dim_charges_fedex,

											sd.requires_freight AS freight_shipping,

											sd.inventory AS inventory,
											sd.limited_inventory AS limited_inventory,

											sd.name AS sku_code,
											sz.name AS size,

											sd.accessory_material_description AS material_description,
											sd.id AS sku_id,

											s.name AS subcategory_name,
											sc.accuracy_image AS accuracy_image

										FROM bs_cart_skus p

										LEFT JOIN bs_products pr ON (pr.id = p.product_id AND pr.active = 1)
										LEFT JOIN bs_streetsign_tools sst ON (sst.id = p.streetsign_tool_id)
										LEFT JOIN bs_streetsign_control sc ON (sc.id = sst.id)
										LEFT JOIN bs_subcategory_products sp ON (sp.product_id = pr.id)
										LEFT JOIN bs_subcategories s ON (s.id = sp.subcategory_id AND s.active = 1)

										LEFT JOIN bs_product_skus sdp ON (sdp.product_id = p.product_id and sdp.sku_id = p.sku_id)
										LEFT JOIN bs_skus sd ON (sd.id = p.sku_id )
										LEFT JOIN bs_sku_types st ON (sd.sku_type_id = st.id)
										LEFT JOIN bs_pricing prr ON (sd.pricing_id = pr.id)
										LEFT JOIN bs_pricing_tiers prt ON(prr.id = prt.pricing_id)

										LEFT JOIN bs_sizes sz ON (sz.id = sd.size_id)
										LEFT JOIN bs_tool_types tt ON (p.tool_type_id = tt.id)

										LEFT JOIN bs_cart_sku_data a ON (a.cart_sku_id = p.id)

										LEFT JOIN bs_product_custom c ON (c.custom_product_id = p.custom_image_id)
										LEFT JOIN bs_streetsign_uploads u ON (p.streetsign_upload_id = u.id)

		left outer join bs_pricing as pricing
		on
		sd.pricing_id = pricing.id

		left outer join
		(
			select
				min_pricing.id
			,	min_pricing.pricing_id
			,	min_pricing.minimum_quantity
			,	coalesce (
					max_pricing.minimum_quantity - 1,
					min_pricing.minimum_quantity * 1000
				) as maximum_quantity
			,	min_pricing.price
			,	min_pricing.streetsign_accessory_display
			,	min_pricing.active
			from
				(
					select
						*
					from
						bs_pricing_tiers
				) as min_pricing

				left outer join

				(
					select
						*
					from
						bs_pricing_tiers
				) as max_pricing
			on
			min_pricing.pricing_id = max_pricing.pricing_id
			and
			max_pricing.id = min_pricing.id + 1
		)
		as pricing_tiers
		on
		sd.pricing_id = pricing_tiers.pricing_id
		and
		pricing_tiers.active = 1

	 	and quantity >= pricing_tiers.minimum_quantity
 		and quantity <= pricing_tiers.maximum_quantity
										where p.id = ?");

			$test = $sql->execute(array((int) $this->id));

			// Get the row
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $row;
			}

		}


		// If we got some data, set our properties
		if (!empty($data)) {

			// Loop through our data
			foreach($data as $row) {

				// Set properties from bs_cart_products
				$this->customCopy1  	= $row['streetsign_custom_copy1'];
				$this->customCopy2  	= $row['streetsign_custom_copy2'];
				$this->streetNumber 	= $row['streetsign_num'];
				$this->background   	= $row['streetsign_background'];
				$this->prefix       	= $row['streetsign_prefix'];
				$this->suffix       	= $row['streetsign_suffix'];
				$this->leftArrow    	= $row['streetsign_left_arrow'];
				$this->rightArrow   	= $row['streetsign_right_arrow'];
				$this->position     	= $row['streetsign_position'];
				$this->color        	= $row['streetsign_color'];
				$this->font         	= $row['streetsign_font'];
				$this->comments			= $row['comments'];
				$this->accuracyImage    = $row['accuracy_image'];
				$this->proofRequested	= (mb_strpos(mb_strtolower($this->comments), 'proof') !== FALSE ? TRUE : FALSE); //Determines whether any items in the cart have proofs requested


				// Upcharges >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

					// Decide whether or not to save the upcharge based on whether we already have it saved or not
					$saveUpcharge = true;
					foreach($this->upcharges as $upcharge) {
						if ($row['streetname_upcharge_id'] == $upcharge['id']) {
							$saveUpcharge = false;
						}
					}

					// Save the upcharge if there is one and we do not already have it saved
					if (!empty($row['streetname_upcharge_id']) && $saveUpcharge) {

						// Set properties from bs_cart_product_attributes
						$this->upcharges[] = array('id'                => $row['streetname_upcharge_id'],
												   'upcharge_price'    => $row['upcharge_price'],
												   'name'              => $row['streetname_upcharge_name'],
												   'type'              => $row['streetname_upcharge_type']);

					}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// fileUpload<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

					$this->fileUpload = array('id' => $row['streetsign_upload_id'],
											 'name' => $row['upload_name'],
											 'size' => $row['upload_size'],
											 'type' => $row['upload_type']);

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// Image uploads >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

					// Decide whether or not to save the image upload based on
					// whether we already have it saved or not
					$saveUpload = true;
					foreach($this->imageUpload as $image) {
						if ($row['file_name'] == $image['name']
							&& $row['file_size'] == $image['size']
							&& $row['file_type'] == $image['type']) {

							$saveUpload = false;
						}
					}

					// Save the uploaded image if there is one and we do not already have it saved
					if (!empty($row['file_name']) && $saveUpload) {
						// Set our image upload info
						$this->imageUpload[] = array('customerId'    => $row['customer_id'],
													 'username'      => $row['username'],
													 'name'          => $row['file_name'],
													 'size'          => $row['file_size'],
													 'type'          => $row['file_type'],
													 'export_brimar' => $row['export_brimar']);
					}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Custom Images >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

					// Decide whether or not to save the custom image based on whether
					// we already have it saved or not
					$saveImage = true;
					foreach($this->customImage as $image) {
						if ($row['custom_image'] == $image['customImage']) {
							$saveImage = false;
						}
					}

					// Save the custom image if there is one and we do not already have it saved
					if (!empty($row['custom_image']) && $saveImage) {

						// Set our custom image info if there is one
						$this->customImage = array('designId'      => $row['design_id'],
												   'customImageId' => $row['custom_image_id'],
												   'customImage'   => $row['custom_image'],
												   'customXml'     => $row['custom_xml'],
												   'pdfFile'       => $row['pdf_file'],
												   'active'        => $row['active'],
												   'ip'            => $row['ip'],
												   'customersId'   => $row['customers_id'],
												   'createdDate'   => $row['created_date'],
												   'comments'      => $row['comments'],
												   'url'           => $row['url'],
												   'saveDesign'    => $row['save_design'],
												   'backgroundId'  => $row['background_id'],
												   'lastModified'  => $row['last_modified']);

					}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			}

			// Set parent properties
			$this->isCustom                  = 1;
			$this->cartId                    = $row['cart_id'];
			$this->productId 		         = $row['product_id'];
			$this->active                    = $row['active'];
			$this->materialCode              = $row['material_code'];
			$this->creationTime              = $row['creation_time'];
			$this->modificationTime          = $row['modification_time'];
			$this->savingsPercentage         = $row['savings_percentage'];
			$this->productNumber	         = $row['product_number'];
			$this->nickname                  = $row['nickname'];
			$this->designService             = TRUE;
			$this->eps                       = $row['eps'];
			$this->pdf                       = $row['pdf'];
			$this->createdDate               = $row['created_date'];
			$this->comments                  = $row['comments'];
			$this->totalPrice                = $row['total_price'];
			$this->unitPrice                 = $row['unit_price'];
			$this->quantity                  = ( isset($row['quantity']) ? intval($row['quantity']) : NULL );
			$this->expirationDate            = $row['expiration_date'];
			$this->skuCode 		             = $row['sku_code'];
			$this->inventory                 = $row['inventory'];
			$this->limitedInventory          = $row['limited_inventory'];
			$this->subtitle                  = $row['subtitle'];
			$this->productImage              = $row['product_image'];
			$this->size                      = $row['size_name'];
			$this->materialDescription       = $row['material_description'];
			$this->weight                    = $row['weight'];
			$this->trueWeight                = $row['true_weight'];
			$this->numberOfPackages          = $row['number_pkgs'];
			$this->dimChargesUps             = $row['dim_charges_ups'];
			$this->dimChargesFedex           = $row['dim_charges_fedex'];
			$this->freightShipping           = ($row['freight_shipping'] == 1 ? true : false);
			$this->skuId                     = $row['sku_id'];
			$this->subcategoryName           = $row['subcategory_name'];
            $this->stateParameters  = array("sourceProduct"  => ( isset($row['source_product_id'])? $row['source_product_id'] : NULL),
                "sourceProductRecommendation"  => (isset($row['source_product_recommendation_id'])? $row['source_product_recommendation_id'] : NULL),
                "sourceAccessoryFamilyProduct" => (isset($row['source_accessory_family_product_id'])? $row['source_accessory_family_product_id'] : NULL),
                "sourceInstallationAccessory"  => (isset($row['source_installation_accessory_id'])? $row['source_installation_accessory_id'] : NULL),
                "sourceLandingProduct"         => (isset($row['source_landing_product_id'])? $row['source_landing_product_id']:NULL),
                "sourceSubcategoryProduct"     => (isset($row['source_subcategory_product_id'])? $row['source_subcategory_product_id']:NULL));

			// Instantiate a product so we can get the URL
			$p = new Page('product', $this->productId);
			$this->productLink = $p->getUrl().'?s=' .ProductStateParameter::encode($this->stateParameters);

			// Everything was successful
			return true;

		} else {

			// Something went wrong
			return false;

		}

	}



	/**
	 * Takes what we know about the streetsign, saves just the valid information to an array with
	 * keys that we can print directly on templates. This is mainly used to get additional details lists
	 * @return array [array of additional details with keys that can be output directly on page]
	 */
	public function getAdditionalDetails() {

		$details = array();

		if (!empty($this->customCopy1)) { $details['Custom Copy 1'] = $this->customCopy1; }
		if (!empty($this->customCopy2)) { $details['Custom Copy 2'] = $this->customCopy2; }
		if (!empty($this->streetNumber)) { $details['Street Number'] = $this->streetNumber; }
		if (!empty($this->background)) { $details['Background'] = $this->background; }
		if (!empty($this->prefix)) { $details['Prefix'] = $this->prefix; }
		if (!empty($this->suffix)) { $details['Suffix'] = $this->suffix; }
		if (!empty($this->leftArrow)) { $details['Left Arrow'] = $this->leftArrow; }
		if (!empty($this->rightArrow)) { $details['Right Arrow'] = $this->rightArrow; }
		if (!empty($this->position)) { $details['Position'] = $this->position; }
		if (!empty($this->color)) { $details['Color'] = $this->color; }
		if (!empty($this->font)) { $details['Font'] = $this->font; }

		return $details;

	}




	// Calls the parent remove() and then removes its own attributes on success
	public function remove() {

		$sql = Connection::getHandle()->prepare("DELETE FROM bs_product_custom WHERE custom_product_id = ?");

		if ($sql->execute(array($this->customImage['customImageId']))) {

			if($parentSuccess = parent::remove()){

				if ($this->fileUpload['streetsign_upload_id'] > 0) {

					$uSql = Connection::getHandle()->prepare("DELETE FROM bs_streetsign_uploads WHERE id = ?");

					if(!$uSql->execute(array($this->fileUpload['id']))){
						return false;
					}

				}

			}

			return $parentSuccess;

		} else {
			return false;
		}

	}


	public function duplicateFileUploadRow(){

		// Define file path and original file name
		$TmpObjProduct = ProductPage::create($this->productId);
		$uploadFilePath = $TmpObjProduct->getImagePath('upload_perm');
		$OrgFileName = $this->fileUpload['name'];

		// Get new file name $newFileName
		$newFileName = preg_replace("/[^A-Z0-9._-]/iu", "_", $OrgFileName);

		$i = 0;
		$parts = pathinfo($OrgFileName);
		while (file_exists($uploadFilePath . $newFileName)) {
			$i++;
			$newFileName = $parts["filename"] . "-" . $i . "." . $parts["extension"];
		}


		// Create row with new file_name and creation_time
		$insertSql = Connection::getHandle()->prepare("INSERT INTO bs_streetsign_uploads (file_name, file_size, file_type, creation_time)
										  SELECT ? AS file_name,
										  file_size AS file_size,
										  file_type AS file_type,
										  NOW() AS creation_time
										  FROM bs_streetsign_uploads
										  WHERE id = ?");

		// Copy file contents into the new name
		if($insertSql->execute(array($newFileName, $this->fileUpload['id']))){

			$source = $TmpObjProduct->getImagePath('upload_perm') . $this->fileUpload['name'];
			$dest = $TmpObjProduct->imagePath('upload_perm') . $newFileName;

			copy($source, $dest);

		} else {
			return false;
		}

		// Return id to link cart_product
		return Connection::getHandle()->lastInsertId();

	}


}
