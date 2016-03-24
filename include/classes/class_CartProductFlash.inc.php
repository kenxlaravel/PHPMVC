<?php

class CartProductFlash extends CartProduct {

	// Hard-coded tool type so we can differentiate between this and other product types
	public $type = 'flash';

	// from bs_cart_products
	public $eps;
	public $pdf;
	public $designService;
	public $comments;
	public $proofRequested;

	// Settings
	public $settings = array();

	// from bs_cart_product_attributes
	public $upcharges = array();

	// From bs_products_custom
	public $customImage = array();

	// Custom variables
	public $editUrl;
	public $adminEditUrl;



	/**
	 * Constructor
	 *
	 * If a data array is passed in, our class properties will be set from the data.
	 * Otherwise we will run a query to get all of the info about this product.
	 *
	 * @param [int]    $id      [ID of the product from bs_cart_products]
	 * @param [object] $objCart [Handle for the cart object that this object is linked to]
	 * @param [array]  $data    [Optional array of class properties so we can avoid querying for them]
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
	 * Sets the class properties from a data array passed in
	 * @param [array] $data [array of class properties]
	 * @return [bool] [true / false on success or failure]
	 */
	public function setProperties($data) {

		// If there is no data we have to query the DB for it
		if ($data == null) {

			$sql = $this->dbh->prepare("select
		p.id as id
	,	p.cart_id as cart_id
	,	p.product_id as product_id
	,    p.source_product_id,
	,	p.source_product_recommendation_id,
	,	p.source_accessory_family_product_id,
	,	p.source_installation_accessory_id,
	,	p.source_landing_product_id,
	,	p.source_subcategory_product_id,
	,	tool_types.`name` as tool_type
	,	p.quantity as quantity
	,	p.unit_price as unit_price
	,	p.total_price as total_price
	,	p.savings_percentage as savings_percentage
	,	p.design_id as design_id
	,	p.custom_image_id as custom_image_id
	,	pricing.material_code as material_code
	,	p.eps as eps
	,	p.pdf as pdf
	,	p.streetsign_custom_copy1 as streetsign_custom_copy1
	,	p.streetsign_custom_copy2 as streetsign_custom_copy2
	,	p.streetsign_num as streetsign_num
	,	p.streetsign_background as streetsign_background
	,	p.streetsign_suffix as streetsign_suffix
	,	p.streetsign_prefix as streetsign_prefix
	,	p.streetsign_left_arrow as streetsign_left_arrow
	,	p.streetsign_right_arrow as streetsign_right_arrow
	,	p.streetsign_position as streetsign_position
	,	p.streetsign_color as streetsign_color
	,	p.streetsign_font as streetsign_font
	,	p.design_service as design_service
	,	p.comments as comments
	,	p.creation_time as creation_time
	,	p.modification_time as modification_time
 	,	a.id as attribute_id
 	,	'' as flash_upcharge_id				-- 	a.flash_upcharge_id as flash_upcharge_id
 	,	a.upcharge_price as upcharge_price
 	,	'' as flash_upcharge_name			-- 	u.name as flash_upcharge_name
 	,	'' as flash_upcharge_type			-- 	u.type as flash_upcharge_type
 	, c.custom_product_id as custom_image_id
 	,		c.custom_image as custom_image
 	, 	ft.name as layout
 	,	products.expiration as expiration_date
 	,	products.default_subtitle as subtitle
 	,	skus.small_image as product_image
	,	products.product_number as product_number
 	,	products.default_product_name as nickname
	,	skus.active as active
	,	skus.shipping_weight as weight
	,	skus.weight as true_weight
	,	skus.dedicated_package_count as number_pkgs
	,	skus.shipping_weight as dim_weight_ups
	,	skus.ups_shipping_surcharge as dim_charges_ups
	,	skus.shipping_weight as dim_weight_fedex
	,	skus.fedex_shipping_surcharge as dim_charges_fedex
	,	skus.requires_freight as freight_shipping
	,	skus.inventory as inventory
	,	skus.limited_inventory as limited_inventory
	,	skus.`name` as sku_code
	,	sizes.`name` as size
	,	materials.`name` as material_description
	,	skus.id as sku_id
	,	subcategories.`name` as subcategory_name

from

		bs_cart_skus as p

		left outer join bs_product_skus as product_skus
		on
		p.sku_id = product_skus.sku_id
		and
		p.product_id = product_skus.product_id

		left outer join bs_skus as skus
		on
		product_skus.sku_id = skus.id
		and
		skus.active = 1

		left outer join bs_materials as materials
		on
		skus.material_id = materials.id
		and
		materials.active = 1

		left outer join bs_sizes as sizes
		on
		skus.size_id = sizes.id
		and
		sizes.active = 1

		left outer join bs_tool_types as tool_types
		on
		p.tool_type_id = tool_types.id

		left outer join bs_products as products
		on
		p.product_id = products.id
		and
		products.active = 1

		left outer join bs_subcategory_products as subcategory_products
		on
		products.id = subcategory_products.product_id

		left outer join bs_subcategories as subcategories
		on
		subcategory_products.subcategory_id = subcategories.id
		and
		subcategories.active = 1

		left outer join bs_pricing as pricing
		on
		skus.pricing_id = pricing.id

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
		skus.pricing_id = pricing_tiers.pricing_id
		and
		pricing_tiers.active = 1

	 	and quantity >= pricing_tiers.minimum_quantity
 		and quantity <= pricing_tiers.maximum_quantity


		left outer join bs_cart_sku_data as a
		on
		p.id = a.cart_sku_id

	   left join bs_product_custom c on (c.custom_product_id = p.custom_image_id)
		 left join bs_flash_tools ft on (p.flash_tool_id = ft.id)
where
		p.id = ?
");

			$sql->execute(array((int) $this->id));

			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

				$data[] = $row;
			}

		}



		if (!empty($data)) {

			// Loop through and decipher all the data
			foreach($data as $row) {

				// Set properties from bs_cart_products
				$this->eps             = $row['eps'];
				$this->pdf             = $row['pdf'];
				$this->designService   = $row['design_service'];
				$this->comments        = $row['comments'];
				$this->proofRequested  = (mb_strpos(mb_strtolower($this->comments), 'proof') !== FALSE ? TRUE : FALSE); //Determines whether any items in the cart have proofs requested

				// Upcharges >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

					// Decide whether or not to save the upcharge based on whether we already have it saved or not
					$saveUpcharge = true;
					foreach($this->upcharges as $upcharge) {
						if ($row['flash_upcharge_id'] == $upcharge['id']) {
							$saveUpcharge = false;
						}
					}

					// Save the upcharge if there is one and we do not already have it saved
					if (!empty($row['flash_upcharge_id']) && $saveUpcharge) {

						// Set properties from bs_cart_product_attributes
						$this->upcharges[] = array('id'             => $row['flash_upcharge_id'],
												   'upcharge_price' => $row['upcharge_price'],
												   'name'           => $row['flash_upcharge_name'],
												   'type'           => $row['flash_upcharge_type']);

					}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Custom Images >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

					if (!empty($row['custom_image'])) {

						// Set properties from bs_products_custom
						$this->customImage = array('customImageId'   => $row['custom_image_id'],
												   'designId'        => $row['design_id'],
												   'customImage'     => $row['custom_image'],
												   'customXml'       => $row['custom_xml'],
												   'pdfFile'         => $row['pdf_file'],
												   'ip'              => $row['ip'],
												   'createdDate'     => $row['created_date'],
												   'comments'        => $row['comments'],
												   'lastModified'    => $row['last_modified']);

					}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Set custom variables
				$this->designId = $row['design_id'];
				$this->layout = $row['layout'];
				$this->editUrl = URL_PREFIX_HTTP . '/flashedit/' . (int) $this->id;
				$this->adminEditUrl = URL_PREFIX_HTTPS . 'ssctl/orders/tool.php?status=edit&layout=' . $this->layout . '&product_id=' . $this->productId . '&design_id=' . $this->designId;

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

				// Instantiate a product so we can get the URL
				$p = new Page('product', $this->productId);
				$this->productLink = $p->getUrl();

				// Everything was successful
				return true;

			}

		// Otherwise, we have no data array
		} else {

			// Something went wrong
			return false;

		}

	}



	// Calls the parent remove() and then removes its own attributes on success
	public function remove() {

		$sql = Connection::getHandle()->prepare("DELETE FROM bs_product_custom WHERE custom_product_id = ?");

		if ($sql->execute(array($this->customImage['customProductId']))) {

			return ($parentSuccess = parent::remove());

		} else {
			return false;
		}

	}




	/**
	 * This function checks design & product id pair for flash items
	 * It returns true if the design/product pair is safe to edit;
	 * returns false if there are any rows in the shopping cart where
	 * the design ID is paired up with a different product ID.
	 *
	 * @param string  $design_id  	design id for product
	 * @param string  $product_id  	product id
	 * @return true or false based on count
	 */
	static function checkDesignProductPair($design_id, $product_id) {

	 	// Selects a count of shopping cart rows (temporary and permanent) where the provided design ID is paired up with a product ID other than the one provided.
		$stmt=Connection::getHandle()->prepare("SELECT COUNT(t.product_id) AS `count`
									FROM (
											(SELECT d.design_id AS design_id, c.products_id AS product_id FROM bs_product_custom d INNER JOIN bs_cart_skus c ON (c.custom_image_id = d.custom_product_id))
										) t
									WHERE t.design_id = :design_id AND t.product_id != :product_id
								");
		$stmt->execute(array(":design_id"=>$design_id,":product_id"=>$product_id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		// Convert the count result to an integer for safer comparisons.
		$count = (int) $row['count'];

		return $count === 0 ? true : false;

	}


}