<?php

class CartProductStock extends CartProduct {

	// Hard-coded tool type so we can differentiate between this and other product types
	public $type = 'stock';
    public $isCustom;
    public $cartId;
    public $productId;
    public $active;
    public $materialCode;
    public $creationTime;
    public $modificationTime;
    public $savingsPercentage;
    public $productNumber;
    public $nickname;
    public $designService;
    public $eps;
    public $pdf;
    public $createdDate;
    public $comments;
    public $totalPrice;
    public $unitPrice;
    public $quantity;
    public $expirationDate;
    public $skuCode;
    public $inventory;
    public $limitedInventory;
    public $subtitle;
    public $productImage;
    public $size;
    public $materialDescription;
    public $weight;
    public $trueWeight;
    public $numberOfPackages;
    public $dimWeightUps;
    public $dimChargesUps;
    public $dimWeightFedex;
    public $dimChargesFedex;
    public $freightShipping;
    public $skuId;
    public $subcategoryName;


    /**
	 * Constructor
	 *
	 * If a data array is passed in, our class properties will be set for the data.
	 * Otherwise we will run a query to get all of the info about this product.
	 *
	 * @param [int]    $id       [ID of the product from bs_cart_products]
	 * @param [object] $objCart  [Handle for the cart object that this object is linked to]
	 * @param [array]  $data     [Optional array of class properties so we can avoid querying for them]
	 */
	public function __construct($id, $objCart, $data = array()) {

		//Establish a database connection
		$this->dbh = Connection::getHandle();

		// Call our parent constructor
		parent::__construct($id, $objCart);

		// If we have properties, set them. Otherwise we will need to get them with a query
		if (!empty($data) && $id > 0) {

			$this->setProperties($data);

		} else if ($id > 0) {

			$this->setProperties();
		}

	}



	/**
	 * Sets the class properties from a data array passed in
	 * @param  [array] $data [array of class properties]
	 * @return [bool]  [true / false on success or failure]
	 */
	public function setProperties($data = null) {

		// If there was no data supplied, query the DB for it
		if ($data == null) {


			$sql = $this->dbh->prepare("SELECT
			p.id AS cart_product_id,
			p.cart_id AS cart_id,
			p.product_id AS product_id,
			p.source_product_id,
			p.source_product_recommendation_id,
			p.source_accessory_family_product_id,
			p.source_installation_accessory_id,
			p.source_landing_product_id,
			p.source_subcategory_product_id,
			p.tool_type_id AS tool_type,
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
			pr.expiration AS expiration_date,
			pr.page_subtitle AS subtitle,
			pr.default_product_name AS nickname,
			pr.search_thumbnail AS product_image,
			pr.product_number AS product_number,
			pr.active AS active,
			sd.weight AS true_weight,
			sd.shipping_weight as weight,
			sd.dedicated_package_count AS number_pkgs,

			sd.ups_shipping_surcharge AS dim_charges_ups,

			sd.fedex_shipping_surcharge AS dim_charges_fedex,
			sd.requires_freight AS freight_shipping,
			sd.inventory AS inventory,
			sd.limited_inventory AS limited_inventory,
			sd.`name` AS sku_code,
			sz.`name` AS size,
			mat.`name` AS material_description,
			sd.id AS sku_id,
			s.name AS subcategory_name

		FROM bs_cart_skus p

		INNER JOIN bs_products pr ON (pr.id = p.product_id AND pr.active = 1)
		LEFT JOIN bs_subcategory_products sub ON (pr.id = sub.product_id 	AND p.subcategory_id = sub.id)
		INNER JOIN bs_product_skus ps ON (p.sku_id = ps.sku_id AND p.product_id = ps.product_id)
		INNER JOIN bs_skus sd ON (sd.id = ps.sku_id AND sd.active = 1)
		INNER JOIN bs_tool_types tt ON (tt.id = p.tool_type_id)
		LEFT JOIN bs_sizes sz ON(sd.size_id = sz.id)
		LEFT JOIN bs_subcategories s ON (s.id = sub.subcategory_id AND s.active = 1)
		LEFT JOIN bs_packagings pkg ON(pkg.id = sd.package_id)
		LEFT JOIN bs_materials mat ON(mat.id = sd.material_id)
		LEFT JOIN bs_pricing prr ON (sd.pricing_id = pr.id)
		LEFT JOIN bs_pricing_tiers prt ON(prr.id = prt.pricing_id)
		left outer join bs_pricing as pricing
		on sd.pricing_id = pricing.id

		left outer join
		(select min_pricing.id,	min_pricing.pricing_id,	min_pricing.minimum_quantity
		,	coalesce (max_pricing.minimum_quantity - 1, min_pricing.minimum_quantity * 1000) as maximum_quantity
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

				WHERE p.id = ?");

            if( $sql->execute(array((int) $this->id)) ) {

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                    $data[] = $row;
                }
            }
		}


		if (!empty($data)) {

			foreach($data as $row) {

				// Set parent properties
				// (This class has no properties of its own to set, so we will only be setting parent properties)
				$this->isCustom			   = 0;
				$this->cartId              = isset($row['cart_id']) ? (int) $row['cart_id'] : NULL;
				$this->productId 		   = isset($row['product_id']) ? (int) $row['product_id'] : NULL;
				$this->active              = isset($row['active']) ? $row['active'] : NULL;
				$this->materialCode        = isset($row['material_code']) ? $row['material_code'] : NULL;
				$this->creationTime        = isset($row['creation_time']) ? $row['creation_time'] : NULL;
				$this->modificationTime    = isset($row['modification_time']) ? $row['modification_time'] : NULL;
				$this->savingsPercentage   = isset($row['savings_percentage']) ? $row['savings_percentage'] : NULL;
				$this->productNumber	   = isset($row['product_number']) ? $row['product_number'] : NULL;
				$this->nickname            = isset($row['nickname']) ? $row['nickname'] : NULL;
				$this->designService       = isset($row['design_service']) ? $row['design_service'] : NULL;
				$this->eps                 = isset($row['eps']) ? $row['eps'] : NULL;
				$this->pdf                 = isset($row['pdf']) ? $row['pdf'] : NULL;
				$this->createdDate         = isset($row['created_date']) ? $row['created_date'] : NULL;
				$this->comments            = isset($row['comments']) ? $row['comments'] : NULL;
				$this->totalPrice          = isset($row['total_price']) ? $row['total_price'] : NULL;
				$this->unitPrice           = isset($row['unit_price']) ? $row['unit_price'] : NULL;
				$this->quantity            = (isset($row['quantity']) ? intval($row['quantity']) : NULL );
				$this->expirationDate      = isset($row['expiration_date']) ? $row['expiration_date'] : NULL;
				$this->skuCode 		       = isset($row['sku_code']) ? $row['sku_code'] : NULL;
				$this->inventory           = isset($row['inventory']) ? $row['inventory'] : NULL;
				$this->limitedInventory    = isset($row['limited_inventory']) ? $row['limited_inventory'] : NULL;
				$this->subtitle            = isset($row['subtitle']) ? $row['subtitle'] : NULL;
				$this->productImage        = isset($row['product_image']) ? $row['product_image'] : NULL;
				$this->size                = isset($row['size_name']) ? $row['size_name'] : NULL;
				$this->materialDescription = isset($row['material_description']) ? $row['material_description'] : NULL;
				$this->weight              = isset($row['weight']) ? $row['weight'] : NULL;
				$this->trueWeight          = isset($row['true_weight']) ? $row['true_weight'] : NULL;
				$this->numberOfPackages    = isset($row['number_pkgs']) ? $row['number_pkgs'] : NULL;
				$this->dimWeightUps        = isset($row['dim_weight_ups']) ? $row['dim_weight_ups'] : NULL;
				$this->dimChargesUps       = isset($row['dim_charges_ups']) ? $row['dim_charges_ups'] : NULL;
				$this->dimWeightFedex      = isset($row['dim_weight_fedex']) ? $row['dim_weight_fedex'] : NULL;
				$this->dimChargesFedex     = isset($row['dim_charges_fedex']) ? $row['dim_charges_fedex'] : NULL;
				$this->freightShipping     = ($row['freight_shipping'] == 'Y' ? true : false);
				$this->skuId               = isset($row['sku_id']) ? (int) $row['sku_id'] : NULL;
				$this->subcategoryName     = isset($row['subcategory_name']) ? $row['subcategory_name'] : NULL;
                $this->stateParameters = array("sourceProduct"  => ( isset($row['source_product_id'])? $row['source_product_id'] : NULL),
                    "sourceProductRecommendation"  => (isset($row['source_product_recommendation_id'])? $row['source_product_recommendation_id'] : NULL),
                    "sourceAccessoryFamilyProduct" => (isset($row['source_accessory_family_product_id'])? $row['source_accessory_family_product_id'] : NULL),
                    "sourceInstallationAccessory"  => (isset($row['source_installation_accessory_id'])? $row['source_installation_accessory_id'] : NULL),
                    "sourceLandingProduct"         => (isset($row['source_landing_product_id'])? $row['source_landing_product_id']:NULL),
                    "sourceSubcategoryProduct"     => (isset($row['source_subcategory_product_id'])? $row['source_subcategory_product_id']:NULL));
			}

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
}