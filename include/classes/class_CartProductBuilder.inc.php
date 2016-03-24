<?php

// TODO: Query check.

class CartProductBuilder extends CartProduct {

	// Hard-coded tool type so we can differentiate between this and other product types
	public $type = 'builder';

	// From bs_cart_products
	public $comments;
	public $proofRequested;
	public $designService;

	// From bs_cart_product_attributes
	public $settings = array();

	// from bs_cart_product_attributes
	public $upcharges = array();

	// Design info from bs_designs joined on bs_designs.id = cart.design_id
	public $designInfo = array();

	// The rendered builder image from bs_designs
	public $customImage = array();

	// Upload info from bs_builder_uploads joined on bs_builder_uploads.hash = this.builderValue
	public $uploads = array();

	// Custom variables
	public $editUrl;
	public $adminEditUrl;



	/**
	 * Constructor
	 *
	 * If a data array is passed in, our class will be set from the data.
	 * Otherwise we will run a query to get all of the info about this product.
	 *
	 * @param [int]    $cartProductId [ID of the product from bs_cart_products]
	 * @param [object] $objCart       [Handle for the cart object that this object is linked to]
	 * @param [array]  $data          [Optional array of class properties so we can avoid querying for them]
	 */
	public function __construct($cartProductId, $objCart, $data = array()) {

		// Set the class properties we will always need
		$this->cartObject = $objCart;
		$this->cartProductId = $cartProductId;

		// Pass the info up to the parent cart product
		parent::__construct($cartProductId, $objCart);

		// Get or set properties
		if (!empty($data) && $cartProductId > 0) {
			$this->setProperties($data);
		} else if ($cartProductId > 0) {
			$this->setProperties();
		}

	}



	/**
	 * Sets the class properties from a data array passed in
	 * @param array $data [array of class properties]
	 * @return bool [true / false on success or failure]
	 */
	public function setProperties($data = null) {

		// If we have no data array, we have to query the database for it
		if ($data == null) {

			// Keep track of whether or not we've successfully pulled data rows from the DB
			$success = false;

			// Query not done, missing a few fields
			// Select our cart product row from the DB
			$sql = Connection::getHandle()->prepare("SELECT DISTINCT
			p.id AS id,
			p.cart_id AS cart_id,
			p.product_id AS product_id,
			p.source_product_id,
			p.source_product_recommendation_id,
			p.source_accessory_family_product_id,
			p.source_installation_accessory_id,
			p.source_landing_product_id,
			p.source_subcategory_product_id,
			tt.`name` AS tool_type,
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

			-- a.streetsign_upcharge_id AS upcharge_id,
			-- a.builder_upcharge_id AS builder_upcharge_id,

			a.upcharge_price AS upcharge_price,
			u.hash AS uploadHash,
			u.name AS uploadName,
			u.original_filename AS uploadOriginalFilename,
			u.original_directory AS uploadOriginalDirectory,
			u.converted_filename AS uploadConvertedFilename,
			u.converted_directory AS uploadConvertedDirectory,
			d.time AS design_time,
			d.hash AS design_hash,
			pr.expiration AS expiration_date,
			pr.page_subtitle AS subtitle,
			pr.product_number AS product_number,
			pr.page_title AS nickname,
			sd.small_image AS product_image,
			pr.active AS active,

			sd.shipping_weight AS weight,
			sd.weight AS true_weight,
			sd.dedicated_package_count AS number_pkgs,

			sd.requires_freight AS freight_shipping,
			sd.inventory AS inventory,
			sd.limited_inventory AS limited_inventory,
			sd.name AS sku_code,
			sz.name AS size,
			prr.material_code AS material_code,
			bm.description AS material_description,
			sd.id AS sku_id,
			s.name AS subcategory_name,
			a.builder_setting AS builder_setting,
			a.builder_value AS builder_value,
			a.builder_subsetting AS builder_subsetting,
			a.builder_label AS builder_label,
			a.builder_value_text AS builder_value_text,
			a.builder_setting_display AS builder_setting_display,
			a.builder_font AS builder_font,
			a.builder_fontsize AS builder_fontsize,
			a.builder_color AS builder_color,
			a.builder_alignment AS builder_alignment,
			a.builder_setbyuser AS builder_setbyuser,
			a.builder_font_setbyuser AS builder_font_setbyuser,
			a.builder_fontsize_setbyuser AS builder_fontsize_setbyuser,
			a.builder_alignment_setbyuser AS builder_alignment_setbyuser,
			a.builder_option_type AS builder_option_type,
			mha.`name` AS builder_upcharge_name

		FROM bs_cart_skus p

		LEFT JOIN bs_products pr ON (pr.id = p.product_id)

		LEFT JOIN bs_subcategory_products sp ON (pr.id = sp.product_id)

		LEFT JOIN bs_subcategories s ON (s.id = sp.subcategory_id AND s.active = 1)
		LEFT JOIN bs_product_skus sdp ON (sdp.product_id = p.product_id and sdp.sku_id = p.sku_id)
		LEFT JOIN bs_skus sd ON (sd.id = p.sku_id AND sd.active = 1)
		LEFT JOIN bs_pricing prr ON (sd.pricing_id = pr.id)
		LEFT JOIN bs_pricing_tiers prt ON(prr.id = prt.pricing_id)
		LEFT JOIN bs_cart_sku_data a ON (a.cart_sku_id = p.id)
		LEFT JOIN bs_builder_skus bs ON (bs.sku_id = sd.id  AND bs.sku_id = p.sku_id)
		LEFT JOIN bs_tool_types tt ON(tt.id = p.tool_type_id)
		LEFT JOIN bs_sizes sz ON (sd.size_id = sz.id)
		LEFT JOIN bs_materials ms ON (sd.material_id = ms.id)
		LEFT JOIN bs_builder_materials bm ON (bm.material_id = ms.id)

		LEFT JOIN bs_builder_skus bls ON (bls.product_id = p.product_id AND bls.product_id = p.product_id AND bls.sku_id = p.sku_id)

		LEFT JOIN bs_builder_mounting_hole_arrangements bma ON (bls.mounting_hole_arrangement_ref = bma.mounting_hole_arrangement_ref)
		LEFT JOIN bs_mounting_hole_arrangements mha ON (mha.id = bma.mounting_hole_arrangement_id)
		LEFT JOIN bs_builder_laminates bl ON (bl.laminate_ref = bls.laminate_ref)

		LEFT JOIN bs_designs d ON (d.id = p.design_id)
		LEFT JOIN bs_builder_uploads u ON (u.hash = a.builder_value)

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
		WHERE p.id = ? ");

			$sql->execute(array($this->cartProductId));

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $row;
			}

		}

		// Loop through the data and decipher it
		foreach($data as $row) {

			// From bs_cart_products
			$this->comments          = $row['comments'];
			$this->designService 	 = $row['design_service'];
			$this->proofRequested	 = (mb_strpos(mb_strtolower($this->comments), 'proof') !== FALSE ? TRUE : FALSE); //Determines whether any items in the cart have proofs requested



			// From bs_cart_product_attributes
			$this->settings[] = array('attribute_id'              => $row['attribute_id'],
									  'builderSetting'            => $row['builder_setting'],
									  'builderValue'              => $row['builder_value'],
									  'builderSubsetting'         => $row['builder_subsetting'],
									  'builderLabel'              => $row['builder_label'],
									  'builderValueText'          => $row['builder_value_text'],
									  'builderSettingDisplay'     => $row['builder_setting_display'],
									  'builderFont'               => $row['builder_font'],
									  'builderFontsize'           => $row['builder_fontsize'],
									  'builderColor'              => $row['builder_color'],
									  'builderAlignment'          => $row['builder_alignment'],
									  'builderSetbyuser'          => $row['builder_setbyuser'],
									  'builderFontSetbyuser'      => $row['builder_font_setbyuser'],
									  'builderAlignmentSetbyuser' => $row['builder_alignment_setbyuser'],
									  'builderOptionType'         => $row['builder_option_type']);


			// If this is a builder option that means it is an upcharge
			if ($row['builder_setting'] == 'option') {

				// Set properties from bs_cart_product_attributes
				$this->upcharges[$row['builder_subsetting']] = array('attributeId' => $row['attribute_id'],
																     'id'          => $row['builder_upcharge_id'],
																     'price'       => $row['upcharge_price'],
																     'name'        => $row['builder_upcharge_name']);

			}


			// If this is an upload grab the upload info
			if ($row['builder_setting'] == 'upload') {

				// Upload info from bs_builder_uploads
				$this->uploads[] = array('hash'                 => $row['uploadHash'],
										 'name'                 => $row['uploadName'],
										 'originalFilename'     => $row['uploadOriginalFilename'],
										 'originalDirectory'    => $row['uploadOriginalDirectory'],
										 'convertedFilename'    => $row['uploadConvertedFilename'],
										 'convertedDirectory'   => $row['uploadConvertedDirectory']);

			}



			// Custom Images >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

				// Save the custom image if there is one and we do not already have it saved
				if (!empty($row['design_id'])) {

					// Set our custom image info if there is one
					$this->customImage = array('designId'       => $row['design_id'],
											   'customImage'    => '/images/designs/' . $row['design_hash'] . '.' . strtotime($row['design_time']) . '.png',
											   'customImagePDF' => '/images/designs/' . $row['design_hash'] . '.' . strtotime($row['design_time']) . '.pdf');

				}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// Design info from bs_designs joined on bs_designs.id = cart.design_id
			$this->designInfo = array('hash' => $row['design_hash'],
									  'id'   => $row['design_id']);


			// Set parent properties
			$this->isCustom			         = 1;
			$this->cartId                    = $row['cart_id'];
			$this->productId 		         = $row['product_id'];
			$this->active                    = $row['active'];
			$this->materialCode              = $row['material_code'];
			$this->creationTime              = $row['creation_time'];
			$this->modificationTime          = $row['modification_time'];
			$this->savingsPercentage         = $row['savings_percentage'];
			$this->productNumber	         = $row['product_number'];
			$this->nickname                  = $row['nickname'];
			$this->designService             = $row['design_service'];
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
			$this->dimWeightUps              = $row['dim_weight_ups'];
			$this->dimChargesUps             = $row['dim_charges_ups'];
			$this->dimWeightFedex            = $row['dim_weight_fedex'];
			$this->dimChargesFedex           = $row['dim_charges_fedex'];
			$this->freightShipping           = ($row['freight_shipping'] == 'Y' ? true : false);
			$this->skuId                     = $row['sku_id'];
			$this->subcategoryName           = $row['subcategory_name'];
            $this->stateParameters = array("sourceProduct"  => ( isset($row['source_product_id'])? $row['source_product_id'] : NULL),
            "sourceProductRecommendation"  => (isset($row['source_product_recommendation_id'])? $row['source_product_recommendation_id'] : NULL),
            "sourceAccessoryFamilyProduct" => (isset($row['source_accessory_family_product_id'])? $row['source_accessory_family_product_id'] : NULL),
            "sourceInstallationAccessory"  => (isset($row['source_installation_accessory_id'])? $row['source_installation_accessory_id'] : NULL),
            "sourceLandingProduct"         => (isset($row['source_landing_product_id'])? $row['source_landing_product_id']:NULL),
            "sourceSubcategoryProduct"     => (isset($row['source_subcategory_product_id'])? $row['source_subcategory_product_id']:NULL));

			// Since we have now successfully pulled a row of data from the DB we set success to true
			$success = true;

		}

		// Instantiate a product so we can get the URL
		$p = new Page('product', $this->productId);

		// Form the edit URL for the builder
		$hash = $this->getHashByDesignID($this->designInfo['id']);
		$url = $p->getUrl().'?mode=edit&design='.$hash. '&s=' .ProductStateParameter::encode($this->stateParameters);

		// Custom variables
		$this->editUrl = $url;
		$this->adminEditUrl = URL_PREFIX_HTTPS . 'ssctl/orders/builderedit.php?design=' . $this->designInfo['id'];
		$this->productLink = $p->getUrl();

		return $success;

	}



	// Calls the parent remove() and then removes its own attributes on success
	public function remove() {

		// Remove from all our custom tables, and then run the parent remove function
		$sql = Connection::getHandle()->prepare("DELETE FROM bs_builder_renderdata WHERE design_id = ?");

		if ($sql->execute(array($this->designInfo['id']))) {

			if($parentSuccess = parent::remove()){

				$dSql = Connection::getHandle()->prepare("DELETE FROM bs_designs WHERE id = ?");

				if(!$dSql->execute(array($this->designInfo['id']))){
					 return false;
				}
			}

			return $parentSuccess;

		} else {
			return false;
		}


	}




}
