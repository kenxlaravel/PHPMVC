<?php

// by SkuId

/**
 * Class Sku
 */
class Sku extends CacheableEntity {

    /**
     * Path to all catlog images
     */
	const IMAGEPATH = "/images/catlog/product/";

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT sku.id AS id, sku.name, sku.active, sku.small_image, sku.medium_image,
                                sku.shipping_weight_display_unit_id, sku.weight_display_unit_id, sku_types.name AS sku_type_name,
                                sku.accessory_family_id AS accessory_family_id,
                                sku.large_image, sku.artwork_production_file, sku.requires_freight, sku.package_id,
						        sku.inventory, sku.limited_inventory, sku.max_chars_upper, sku.absolute_maximum,
						        sku.shipping_weight, sku.weight, sku.dedicated_package_count, sku.ups_shipping_surcharge,
						        sku.fedex_shipping_surcharge, sku.accessory_material_header, sku.accessory_material_description,
						        sku.accessory_size_description, sku.streetsign_accessory_display, sku.package_inclusion_note,
						        sku.made_to_order, sku.size_id, sku.corner_radius_id, sku.material_id, sku.package_id, sku.inner_units,
						        sku.pricing_id, sku.advertising_category_id, sku.sku_type_id,
						        mh.id AS mounting_hole_arrangement_id,
						        sku.laminate_id, sku.lead_time, sku_compliances.compliances_id,

						         GROUP_CONCAT(sku_compliances.compliances_id) AS sku_compliance_array

					         FROM `bs_skus` AS sku

						     LEFT JOIN `bs_mounting_hole_arrangements` mh ON (sku.mounting_hole_arrangement_id = mh.id AND mh.active = 1)
							 LEFT JOIN `bs_sku_types` AS sku_types ON sku.sku_type_id = sku_types.id
					         LEFT JOIN `bs_sku_compliances` AS sku_compliances ON sku_compliances.sku_id = sku.id
							 LEFT JOIN `bs_pricing` pricing ON (pricing.id = sku.pricing_id)
					         WHERE sku.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	 * ID of the sku.
	 * DB column : bs_skus.id.
     *
	 * @var int $id
	 */
	private $id;

	/**
	 * Sku code.
	 * DB column: bs_skus.name.
	 *
	 * @var string $name
	 */
	private $name;

	/**
	 * A boolean representing whether the sku is active or not
	 * DB column: bs_skus.active.
	 *
	 * @var boolean $active
	 */
	private $active;

	/**
	 * Sku $smallImage
	 * DB column: bs_sku.small_image
	 *
	 * @var string $smallImage
	 */
	private $smallImage;

	/**
	 * Sku $smallImage
	 * DB column: bs_sku.medium_image
	 *
	 * @var string $mediumImage
	 */
	private $mediumImage;

	/**
	 * Sku $smallImage
	 * DB column: bs_sku.large_image
	 *
	 * @var string $largeImage
	 */
	private $largeImage;

	/**
	 * Specific file name or file path for a product
	 * DB column: bs_skus.artwork_production_file.
	 *
	 * @var string $artworkProductionFile;
	 */
	private $artworkProductionFile;

	/**
	 * Whether the sku requires freight shipment or not
	 * DB column: bs_skus.requires_freight.
	 *
	 * @var boolean $requiresFreight;
	 */
	private $requiresFreight;

	/**
	 * Contains an integer if bsProducts.limitedInventory is TRUE or INF if FALSE.
	 * DB column: bs_skus.inventory.
	 *
	 * @var int $inventory
	 */
	private $inventory;

    /**
     * @var int $shippingWeightDisplayUnitId
     */
    private $shippingWeightDisplayUnitId;

    /**
     * @var int $weightDisplayUnitId
     */
    private $weightDisplayUnitId;

	/**
	 * Whether or not inventory on SKU is limited
	 * DB column: bs_skus.limited_inventory
	 *
	 * @var bool $limitedInventory
	 */
	private $limitedInventory;

	/**
	 * Define the initial max character limit for streetname products
	 * DB column: bs_skus.max_chars_upper.
	 *
	 * @var int $maxCharsUpper
	 */
	private $maxCharsUpper;

	/**
	 * Define the absolute character maximum for streetname products
	 * DB column: bs_skus.absolute_maximum.
	 *
	 * @var int $absoluteMaximum
	 */
	private $absoluteMaximum;

	/**
	 * Shipping weight
	 * DB column: bs_skus.shipping_weight.
	 *
	 * @var float $shippingWeight
	 */
	private $shippingWeight;

	/**
	 * True weight of the sku.
	 * DB column: bs_skus.weight.
	 *
	 * @var float $weight
	 */
	private $weight;

	/**
	 * Applies to products that aren't packaged with other products.
	 * This is the number of packages that are shipped out
	 * DB column: bs_skus.dedicated_package_count.
	 *
	 * @var int $dedicatedPackageCount
	 */
	private $dedicatedPackageCount;

	/**
	 * Extra UPS charge based on dimensional properties of the shipment
	 * DB column: bs_skus.ups_shipping_surcharge.
	 *
	 * @var float $upsShippingSurcharge
	 */
	private $upsShippingSurcharge;

	/**
	 * Extra FedEx charge based on dimensional properties of the shipment
	 * DB column: bs_skus.fedex_shipping_surcharge.
	 *
	 * @var float $fedexShippingSurcharge
	 */
	private $fedexShippingSurcharge;

	/**
	 * This material header used in builder accessory loop within a product to determine when to start new quanity row
	 * DB column: bs_skus.accessory_material_header.
	 *
	 * @var string $accessoryMaterialHeader
	 */
	private $accessoryMaterialHeader;

	/**
	 * Description for sku accessorry display in builder product detail page
	 * DB column: bs_skus.accessory_material_description.
	 *
	 * @var string $accessoryMaterialDescription;
	 */
	private $accessoryMaterialDescription;

	/**
	 * Size description of a sku accessory display in builder product detail page
	 * DB column: bs_skus.accessory_size_description.
	 *
	 * @var string $accessorySizeDescription;
	 */
	private $accessorySizeDescription;

	/**
	 * This is a control to determine whether the sku displays or not in streetsign page accessories section
	 * DB column: bs_skus.streetsign_accessory_display.
	 *
	 * @var bool $streetsignAccessoryDisplay;
	 */
	private $streetsignAccessoryDisplay;

	/**
	 * A note about what is included or not included for a given package
	 * DB column: bs_skus.package_inclusion_note.
	 *
	 * @var string $packageInclusionNote;
	 */
	private $packageInclusionNote;

	/**
	 * This field is only use inside Brimar for define whether this sku
	 * needs to custom build or in-stock
	 * DB column: bs_skus.made_to_order
	 *
	 * @var int defines a bool (True or False)
	 */
	private $madeToOrder;

	/**
	 * Size Object constaining information about the size such as width, height, diameter, etc.
	 * DB column: bs_skus.size_id.
	 *
	 * @var Size() $Size;
	 */
	private $Size;

	/**
	 * Holds the Sku's $cornerRadius that we will get from
	 * a different table
	 * DB column: bs_corner_radius.corner_radius
	 *
	 * @var int $cornerRadisu
	 */
	private $cornerRadiusId;

    /**
     * @var int $CornerRadius holds the value from the CornerRadius table
     */
	private $CornerRadius;

	/**
	 * Holds an object of Material()
	 * DB column: None, pre defined property
	 *
	 * @var Material object $Material;
	 */
	private $Material;

	/**
	 * Innerunit of a packaging type of a specific sku
	 * DB column: bs_skus.inner_units
	 *
	 * @var int $innerunits;
	 */
	private $innerunits;

	/**
	 *DB column: bs_skus.pricing_id
	 *
	 * @var int $pricingId
	 */
	private $pricingId;

	/**
	 * Pricing object
	 * DB column: bs_pricing.id
	 *
	 * @var Pricing() $Pricing;
	 */
	private $Pricing;

	/**
	 * Category that a product belongs to, used for advertising
	 * DB column: bs_advertising_categories.id.
	 *
	 * @var int $advertisingCategory;
	 */
	private $advertisingCategory;

	/**
	 * Gets all skutypes by its id
	 * DB column: bs_skus.sku_type.id
	 *
	 * @var SkuType() $SkuType
	 */
	private $SkuType;

	/**
	 * A MountingHoleArrangement object.
	 * DB column: bs_mounting_hole_arrangements.id
	 *
	 * @var MountingHoleArrangement() $MountingHoleArrangement
	 */
	private $MountingHoleArrangement;

	/**
	 * TextHeight object
	 * DB column: bsTextHeights.id
	 *
	 * @var Textheight() $TextHeight;
	 */
	private $TextHeight;

	/**
	 * An array of compliance objects
	 * DB column: bs_sku_compliances.id
	 *
	 * @var Compliance array $compliances;
	 */
	private $compliances;

    /**
     * @var array $compliancesId
     */
	private $compliancesId = array();

	/**
	 * This contain the value of lead time required to process
	 * the product before ship out to customer
	 * DB column: bs_skus.lead_time.
	 *
	 * @var int $leadTime;
	 */
	private $leadTime;

	/**
	 * MountingHoleArrangment id
	 * DB column: bs_skus.mounting_hole_arrangement_id
	 *
	 * @var int $mountingHoleArrangementId
	 */
	private $mountingHoleArrangementId;

    /**
     * @var int $sizeId
     */
    private $sizeId;

    /**
     * @var int $laminatedId
     */
	private $laminateId;

	/**
	 * @var int $packageId
	 */
	private $packagingId;

	/**
	 * @var Packaging()
	 */
	private $packagings;

	/**
	 * @var int $materialId
	 */
	private $materialId;

	/**
	 * Laminate object
	 * DB column: bs_laminates.id
	 *
	 * @var Laminate $Laminate
	 */
	private $Laminate;

	/**
	 * DB column: bs_sku_types.name
	 *
	 * @var string $skuTypeName
	 */
	private $skuTypeName;

	/**
	 * DB column: bs_sku_set_accessory_family_id
	 * @var int $setAccessoryFamilyId
	 */
	private $setAccessoryFamilyId;

	/**
	 * @var array $AccessoryFamilyId
	 */
	private $AccessoryFamilyId = array();

    private $shipDate;


    /**
     * Constructor
     *
     * @param $id
     */
	public function __construct($id) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

			$data = array();

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND sku.id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
					$this->storeCache($data);
                }
            }

            $this->setMaterialId($data['material_id'])
                 ->setCornerRadiusId($data['corner_radius_id'])
                 ->setInnerunits($data['inner_units'])
                 ->setMountingHoleArrangementId($data['mounting_hole_arrangement_id'])
                 ->setWeight($data['weight'])
                 ->setPackageInclusionNote($data['package_inclusion_note'])
                 ->setPackageId($data['package_id'])
                 ->setComplianceId($data['sku_compliance_array'])
                 ->setSizeId($data['size_id'])
                 ->setName($data['name'])
				 ->setRequiresFreight($data['requires_freight'])
				 ->setAccessoryFamilyId($data['accessory_family_id'])
				 ->setSmallImage($data['small_image'])
				 ->setSkuTypeName($data['sku_type_name'])
				 ->setLimitedInventory($data['limited_inventory'])
				 ->setDedicatedPackageCount($data['dedicated_package_count'])
				 ->setInventory($data['inventory'])
                 ->setMadeToOrder($data['made_to_order'])
				 ->setPricingId($data['pricing_id'])
				 ->setSmallImage($data['small_image'])
				 ->setMediumImage($data['medium_image'])
                 ->setLaminateId($data['laminate_id'])
				 ->isActive($data['active']);

	        } else {
	            // Trigger a notice if an invalid ID was supplied.
	            trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
	        }

	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return current object
	*
	* @param int $id
	* @return Sku() Return current class object
	*/
	public function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set cornerRadius properties
	 *
	 * @param int $cornerRadiusId
	 * @return Sku() Return current class object
	 */
	public function setCornerRadiusId($cornerRadiusId) {
		$this->cornerRadiusId = isset($cornerRadiusId) && is_numeric($cornerRadiusId) && $cornerRadiusId > 0 ? (int) $cornerRadiusId : NULL;
		return $this;
	}

	/**
	 * Sets inventory for the sku if one exists, otherwise set it to infinite
	 *
	 * @param int $inventory
	 * @return int Amount left of product
	 */
	public function setInventory($inventory) {
		$this->inventory = isset($inventory) && is_numeric($inventory) && $inventory > 0 ? (int) $inventory : NULL;
		return $this;
	}

    public function setMadeToOrder($madeToOrder = FALSE) {
        $this->madeToOrder = (bool) $madeToOrder;
        return $this;
    }

	/**
	 * Sets the name of the sku to our $name propertie
	 *
	 * @param string $name The name of the sku
	 * @return Sku() Return current class object
	 */
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	 * Set our $active properties
	 *
	 * @param  bool  $active returns 0 or 1
	 * @return Sku() Return current class object
	 */
	public function isActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

    /**
     * The skus small iamge
     *
     * @param $smallImage
     * @return Sku();
     */
	public function setSmallImage($smallImage) {
		$this->smallImage = isset($smallImage) ? IMAGE_URL_PREFIX . trim( self::IMAGEPATH . $smallImage) : NULL;
		return $this;
	}

    /**
     *
     * @param int $weightDisplayUnitId
     * @return Skuc()
     */
    public function setWeightDisplayUnitId($weightDisplayUnitId) {
        $this->weightDisplayUnitId = isset($weightDisplayUnitId) && is_numeric($weightDisplayUnitId) &&
												$weightDisplayUnitId > 0 ? (int) $weightDisplayUnitId : NULL;
        return $this;
    }

    /**
     *
     * @param int $shippingWeightDisplayUnitId
     * @return Sku()
     */
    public function setShippingWeightDisplayUnitId($shippingWeightDisplayUnitId) {

        $this->shippingWeightDisplayUnitId = isset($shippingWeightDisplayUnitId) && is_numeric($shippingWeightDisplayUnitId) &&
													$shippingWeightDisplayUnitId > 0 ? (int) $shippingWeightDisplayUnitId : NULL;
        return $this;
    }

    /**
     * The skus medium image
     *
     * @param $mediumImage
     * @return Sku()
     */
	public function setMediumImage($mediumImage) {
		$this->images['medium'] = !empty($mediumImage) ? trim(self::IMAGEPATH . $mediumImage) : NULL;
		return $this;
	}

    /**
     * The skus large image
     *
     * @param $largeImage
     * @return Sku()
     */
	public function setLargeImage($largeImage) {
		$this->images['large'] = !empty($largeImage) ? trim(self::IMAGEPATH . $largeImage) : NULL;
		return $this;
	}

    /**
     * The skus art work file (pdf, psd, ai, etc...)
     *
     * @param $artworkProductionFile
     * @return Sku()
     */
    public function setArtworkProductionFile($artworkProductionFile) {

		$this->artworkProductionFile = !empty($artworkProductionFile) ? trim($artworkProductionFile) : NULL;
		return $this;
	}

    /**
     * Set the sku freight
     *
     * @param $requiresFreight
     * @return Sku()
     */
    public function setRequiresFreight($requiresFreight = FALSE) {
		$this->requiresFreight = isset($requiresFreight) ? (bool) $requiresFreight : FALSE;
		return $this;
	}

    /**
     * Set the limited inventory
     *
     * @param $limitedInventory
     * @return Sku()
     */
    public function setLimitedInventory($limitedInventory) {
		$this->limitedInventory = (int) $limitedInventory;
		return $this;
	}

    /**
     * Set the max char
     *
     * @param $maxCharsUpper
     * @return Sku()
     */
    public function setMaxCharsUpper($maxCharsUpper) {
		$this->maxCharsUpper = $maxCharsUpper;
		return $this;
	}

    /**
     * Set the absolute max char
     *
     * @param $absoluteMaximum
     * @return Sku()
     */
    public function setAbsoluteMaximum($absoluteMaximum) {
		$this->absoluteMaximum = $absoluteMaximum;
		return $this;
	}

    /**
     * Set the shipping weight for the sku
     *
     * @param $shippingWeight
     * @return Sku()
     */
    public function setShippingWeight($shippingWeight) {
		$this->shippingWeight = $shippingWeight;
		return $this;
	}

    /**
     * Set the skus weight
     *
     * @param $weight
     * @return Sku()
     */
    public function setWeight($weight) {
		$this->weight = $weight;
		return $this;
	}

    /**
     * Set the compliance id
     *
     * @param array $compliancesId
     * @return Sku()
     */
    public function setComplianceId($compliancesId) {
		$this->compliancesId = isset($compliancesId) ? explode(",", $compliancesId) : NULL;
		return $this;
	}

    /**
     * Set the dedicated package count
     *
     * @param $dedicatedPackageCount
     * @return Sku()
     */
    public function setDedicatedPackageCount($dedicatedPackageCount) {
		$this->dedicatedPackageCount = isset($dedicatedPackageCount) && is_numeric ($dedicatedPackageCount) &&
											 $dedicatedPackageCount > 0 ? (int) $dedicatedPackageCount : NULL;
		return $this;
	}

    /**
     * Set the up shipping surcharge
     *
     * @param $upsShippingSurcharge
     * @return Sku()
     */
    public function setUpsShippingSurcharge($upsShippingSurcharge) {
		$this->upsShippingSurcharge = $upsShippingSurcharge;
		return $this;
	}

    /**
     * Set the fedex shipping charge
     *
     * @param $fedexShippingSurcharge
     * @return Sku()
     */
    public function setFedexShippingSurcharge($fedexShippingSurcharge) {
		$this->fedexShippingSurcharge = $fedexShippingSurcharge;
		return $this;
	}

    /**
     * Set accessory material header
     *
     * @param $accessoryMaterialHeader
     * @return Sku()
     */
    public function setAccessoryMaterialHeader($accessoryMaterialHeader) {
		$this->accessoryMaterialHeader = $accessoryMaterialHeader;
		return $this;
	}

    /**
     * Set accessory material description
     *
     * @param $accessoryMaterialDescription
     * @return Sku()
     */
    public function setAccessoryMaterialDescription($accessoryMaterialDescription) {
		$this->accessoryMaterialDescription = $accessoryMaterialDescription;
		return $this;
	}

    /**
     * Set accessory size description
     *
     * @param $accessorySizeDescription
     * @return Sku()
     */
    public function setAccessorySizeDescription($accessorySizeDescription) {
		$this->accessorySizeDescription = $accessorySizeDescription;
		return $this;
	}

    /**
     * Set street sign accessory display
     *
     * @param $streetsignAccessoryDisplay
     * @return Sku()
     */
    public function setStreetsignAccessoryDisplay($streetsignAccessoryDisplay) {
		$this->streetsignAccessoryDisplay = $streetsignAccessoryDisplay;
		return $this;
	}

    /**
     * Logic: Use this in Conjunction with packaging method ($this->getPackaging())
	 *
     * @param $packageInclusionNote
     * @return Sku()
     */
    public function setPackageInclusionNote($packageInclusionNote = NULL) {
		$this->packageInclusionNote = !is_null($packageInclusionNote) ? trim($packageInclusionNote) : NULL;
		return $this;
	}

    /**
     * Set the size id
     *
     * @param $sizeId
     * @return Sku()
     */
    public function setSizeId($sizeId) {

		$this->sizeId = isset($sizeId) && is_numeric($sizeId) && $sizeId > 0 ? (int) $sizeId : NULL;
		return $this;
	}

    /**
     * Set the material id
     *
     * @param $materialId
     * @return Sku()
     */
    public function setMaterialId($materialId) {

		$this->materialId = isset($materialId) && is_numeric($materialId) && $materialId > 0 ? (int) $materialId : NULL;
		return $this;
	}

    /**
     * Set the package id
     *
     * @param null|int $packageId
     * @return Sku()
     */
    public function setPackageId($packageId) {

		$this->packagingId = isset($packageId) && is_numeric($packageId) && $packageId > 0 ? (int) $packageId : NULL;

		return $this;
	}

    /**
     * Set the innerunits for this sku
     *
     * @param null $innerunits
     * @return Sku()
     */
    public function setInnerunits($innerunits = NULL) {

		$this->innerunits = isset($innerunits) && is_numeric($innerunits) && $innerunits > 0 ? (int) $innerunits : NULL;

		return $this;
	}

    /**
     * Set the skus packaging id
     *
     * @param null $packaging_id
     * @return Sku()
     */
    public function setPackagings($packaging_id = NULL) {

		$this->packagingId = !is_null($packaging_id) ? $packaging_id : NULL;

		return $this;
	}

    /**
     * Set the skus pricing id
     *
     * @param $pricingId
     * @return Sku()
     */
    public function setPricingId($pricingId) {
		$this->pricingId = isset($pricingId) && is_numeric($pricingId) && $pricingId > 0 ? (int) $pricingId : NULL;
		return $this;
	}

    /**
     * Set the advertising category id
     *
     * @param $advertisingCategoryId
     * @return Sku()
     */
    public function setAdvertisingCategoryId($advertisingCategoryId) {

		$this->advertisingCategoryId = $advertisingCategoryId;
		return $this;
	}

	/**
	 * @param $accessoryFamId
	 * @return Sku()
	 */
	public function setAccessoryFamilyId($accessoryFamId) {

		$this->setAccessoryFamilyId = isset($accessoryFamId) && is_numeric($accessoryFamId) && $accessoryFamId > 0 ? (int) $accessoryFamId : NULL;
		return $this;
	}

    /**
     * Set the sku type
     *
     * @param $skuTypeId
     * @return Sku()
     */
    public function setSkuType($skuTypeId) {
		$this->skuTypeId = $skuTypeId;
		return $this;
	}

	public function setSkuTypeName($skuTypeName) {
		$this->skuTypeName = isset($skuTypeName) ? trim($skuTypeName) : NULL;
		return $this;
	}
    /**
     * Set the sku type
     *
     * @param $bsSkuTypes
     * @return Sku()
     */
    public function setBsSkuTypes($bsSkuTypes) {
		$this->bsSkuTypes = !empty($bsSkuTypes) ? trim($bsSkuTypes) : NULL;
		return $this;
	}

    /**
     * Set the sku lead time
     *
     * @param $leadTime
     * @return Sku()
     */
    public function setLeadTime($leadTime) {
		$this->leadTime = $leadTime;
		return $this;
	}

    /**
     * Set the sku laminated id
     *
     * @param $laminateId
     * @return Sku()
     */
    public function setLaminateId($laminateId) {
		$this->laminateId = isset($laminateId) && is_numeric($laminateId) && $laminateId > 0 ? (int) $laminateId : NULL;;;
		return $this;
	}

    /**
     * Set the mounting hole ids
     *
     * @param $mountingHoleArrangementId
     * @return Sku()
     */
    public function setMountingHoleArrangementId($mountingHoleArrangementId) {
		$this->mountingHoleArrangementId = isset($mountingHoleArrangementId) && is_numeric($mountingHoleArrangementId)
											&& $mountingHoleArrangementId > 0 ? (int) $mountingHoleArrangementId : NULL;
		return $this;
	}

    /**
     * Set the stock value for this sku
     *
     * @return bool
     */
    public function isInStock() {
		if( $this->getInventory() <= 0 && $this->getLimitedInventory() ) {
			return FALSE;
		} else {
            return TRUE;
        }
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the sku id
     *
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * Get the sku name
     *
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * Set the corner radius for the current sku
     *
     * @return int
     */
    public function getCornerRadiusId() { return $this->cornerRadiusId; }

	/**
	* Set the $cornerRadius and return a class object
	*
	* @return self Return current class object
	*/
	public function getCornerRadius() {

		if ( is_null($this->CornerRadius) && !empty($this->cornerRadiusId) ) {
			$this->CornerRadius = CornerRadius::create($this->cornerRadiusId);
		}

		return ( !empty($this->CornerRadius) ? $this->CornerRadius : NULL );
	}

	/**
	 * @todo: Finish logic when the time is right - currently this logic is being produced in the product page using js
	 * Packaging logic according to the flowchart
	 *
	 * @see http://192.168.12.10/documentation/packaging-name-flowchart.pdf
	 * @return string $packageInclusionNote
	 */
	public function getFullPackagings() {

		if( is_null ($this->getPackagingId ()) ) {

			//$dedicatedPackageCount is 0 and packageInclusionNote is NULL
			if( $this->getDedicatedPackageCount () <= 0 && is_null ($this->getPackageInclusionNote ()) ) {

				$packageInclusionNote = "Sold individually";
			}

			//$dedicatedPackageCount is 0 but packageInclusionNote is not NULL
			if( $this->getDedicatedPackageCount() <= 0 && !is_null ($this->getPackageInclusionNote ()) ) {

				$packageInclusionNote = "Sold individually - " . $this->getPackageInclusionNote();
			}

			//$dedicatedPackageCount is 0 but packageInclusionNote is not NULL
			if( $this->getDedicatedPackageCount () == 1 && is_null ($this->getPackageInclusionNote ()) ) {

				$packageInclusionNote = "Sold individually - Ships in ".$this->getDedicatedPackageCount () .
										" separate packages.";
			}
		}
	}

    /**
     * Get the skus small image
     *
     * @return string
     */
    public function getSmallImage() { return $this->smallImage; }

    /**
     * Get the skus medium image
     *
     * @return string
     */
    public function getMediumImage() { return $this->mediumImage; }

    /**
     * Get the skus large image
     *
     * @return string
     */
    public function getLargeImage() { return $this->largeImage; }

    /**
     * Get the skus materials
     *
     * @return array|Material
     */
    public function getMaterial() {

		if ( empty($this->Material) ){

			$this->Material = Material::create($this->getMaterialId());
		}

		return !empty($this->Material) ? $this->Material : array();
	}

    /**
     * Get the skus mounting holes
     *
     * @return array|MountingHoleArrangement
     */
    public function getMountingHoleArrangement() {

		if( empty($this->MountingHoleArrangement) && !empty($this->mountingHoleArrangementId) ) {

			$this->MountingHoleArrangement = MountingHoleArrangement::create($this->mountingHoleArrangementId);
		}

		return !empty($this->MountingHoleArrangement) ? $this->MountingHoleArrangement : array();
	}

	/**
	 * @return array $compliancesId
	 */
	public function getComplianceIds() {

		return $this->compliancesId;
	}

    /**
     * Get the sku compliance
     *
     * @return Compliance
     */
    public function getCompliances() {

		if( !is_null($this->compliancesId) ) {

			foreach($this->compliancesId AS $id) {

				$this->compliances[(int)$id] = Compliance::create((int)$id);
			}
		}

		return $this->compliances;
	}

    /**
     * Get the current sku laminate information
     *
     * @return Laminate|null
     */
    public function getLaminate() {

		if( empty($this->Laminate) && !empty($this->laminateId) ) {

			$this->Laminate = Laminate::create($this->laminateId);
		}

		return !empty($this->Laminate) ? $this->Laminate : NULL;
	}

    /**
     * Get the current sku sizes
     *
     * @return Size
     */
    public function getSize() {

		if ( !is_null($this->getSizeId()) ){

			$this->Size = Size::create($this->getSizeId());
		}

		return $this->Size;
	}

	public function getLaminateId() { return $this->laminateId; }
    /**
     * Get the sku artwork production file
     *
     * @return string
     */
    public function getArtworkProductionFile() { return $this->artworkProductionFile; }

    /**
     * Get the current sku required freight
     *
     * @return bool
     */
    public function getRequiresFreight() { return $this->requiresFreight; }

    /**
     * Get the current skus inventory
     *
     * @return int
     */
    public function getInventory() { return $this->inventory; }

    public function getMadeToOrder() { return $this->madeToOrder; }

    /**
     * Get the current skus limited inventory
     *
     * @return bool
     */
    public function getLimitedInventory() { return $this->limitedInventory; }

    /**
     * Get the current skus max chars
     *
     * @return int
     */
    public function getMaxCharsUpper() { return $this->maxCharsUpper; }

    /**
     * Get the current sku absolute maximum value
     *
     * @return int
     */
    public function getAbsoluteMaximum() { return $this->absoluteMaximum; }

    /**
     * Get the current skus shipping weight
     *
     * @return float
     */
    public function getShippingWeight() { return $this->shippingWeight; }

    /**
     * Get the current skus weight
     *
     * @return float
     */
    public function getWeight() { return $this->weight; }

    /**
     * Get the current skus dedicated package count
     *
     * @return int
     */
    public function getDedicatedPackageCount() { return $this->dedicatedPackageCount; }

    /**
     * Get the current skus shipping surcharge
     *
     * @return float
     */
    public function getUpsShippingSurcharge() { return $this->upsShippingSurcharge; }

    /**
     * Get the current skus fedex shipping surcharge
     *
     * @return float
     */
    public function getFedexShippingSurcharge() { return $this->fedexShippingSurcharge; }

    /**
     * Get the sku accessory material headers
     *
     * @return string
     */
    public function getAccessoryMaterialHeader() { return $this->accessoryMaterialHeader; }

    /**
     * Get the accessory material description
     *
     * @return string
     */
    public function getAccessoryMaterialDescription() { return $this->accessoryMaterialDescription; }

    /**
     * Get the Accessory size description
     *
     * @return string
     */
    public function getAccessorySizeDescription() { return $this->accessorySizeDescription; }

    /**
     * Get the street sign accessory display
     *
     * @return bool
     */
    public function getStreetsignAccessoryDisplay() { return $this->streetsignAccessoryDisplay; }

    /**
     * Get the package inclusion note
     *
     * @return string
     */
    public function getPackageInclusionNote() { return $this->packageInclusionNote; }

    /**
     * Get the sku size ids
     *
     * @return array
     */
    public function getSizeId() { return $this->sizeId; }


    /**
     * Get the skus material ids
     *
     * @return int
     */
    public function getMaterialId() { return $this->materialId; }

	/**
	 * @return int $packagingId;
	 */
	public function getPackagingId() { return $this->packagingId; }

    /**
     * Get the skus packaging
     *
     * @return int
     */
    public function getPackagings() {

		if( !is_null($this->getPackagingId()) && $this->getInnerunits() > 1 ) {

			//$packagingObj = Packaging::create($this->getPackagingId());
			$this->packagings = Packaging::create($this->getPackagingId());

			//if( $this->getInnerunits() > 1) $this->packagings = $packagingObj->getShortPluralName();
			//if( $this->getInnerunits() <= 1) $this->packagings = $packagingObj->getShortName();
		}

		return $this->packagings;
	}

    /**
     * Get the sku innerunits
     *
     * @return int
     */
    public function getInnerunits() { return $this->innerunits; }

	/**
	 * @return int $mountingHoleArrangementId
	 */
	public function getMountingHoleArrangementId() { return $this->mountingHoleArrangementId; }

    /**
     * Get the skus pricing ids
     *
     * @return int
     */
    public function getPricingId() { return $this->pricingId; }

    /**
     * Get the sku pricing
     *
     * @return int
     */
    public function getPricing() {

		if( !is_null($this->getPricingId()) ) {

			$this->Pricing = Pricing::create($this->getPricingId());
		}

		return $this->Pricing;
	}

    /**
     * Get the advertising category id
     *
     * @return int
     */
    public function getAdvertisingCategoryId() { return $this->advertisingCategoryId; }

    /**
     * Get the advertising category
     *
     * @return string
     */
    public function getBsAdvertisingCategories() { return $this->bsAdvertisingCategories; }

    /**
     * Get the skus type
     *
     * @return string
     */
    public function getSkuType() { return $this->skuTypeId; }

	/**
	 * Get the sku type name from sku_type table
	 *
	 * @return string $skuTypeName
	 */
	public function getSkuTypeName() { return $this->skuTypeName; }

    /**
     * @return int
     */
    public function getWeightDisplayUnitId() {
        return $this->weightDisplayUnitId;
    }

    /**
     * @return int
     */
    public function getShippingWeightDisplayUnitId() {

        return $this->shippingWeightDisplayUnitId;
    }

	public function getAccessoryFamilyId() {
		return $this->setacessoryFamilyId;
	}
	/**
	 * @return array
	 */
	public function getAccessoryFamily() {

		if( !is_null($this->getAccessoryFamilyId()) ) {

			//$this->AccessoryFamily = Acces
		}

		return $this->AccessoryFamily;
	}

    /**
     * Get the skus lead time
     *
     * @return int
     */
    public function getLeadTime() { return $this->leadTime; }

	function getFromSkusPriceWithQuantity($qty) {


		$sql = Connection::getHandle()->prepare("SELECT pt.price, pt.minimum_quantity FROM bs_pricing_tiers pt
									WHERE pt.active = TRUE AND pricing_id = ? AND pt.minimum_quantity <= ? ORDER BY minimum_quantity DESC limit 1");
		if ($sql->execute(array($this->getPricingId(), $qty)) ) {

			$row = $sql->fetch(PDO::FETCH_ASSOC);

		}

		return $row;

	}

	/**
	 * Takes a material code and an optional list of upcharge id's, and returns an array of upcharge rows
	 * @param  string    $material     material code
	 * @param  array     $upcharges    array of upcharge IDs (optional)
	 * @return array                   array of upcharge results
	 */
	function getStreetnameUpcharges($upcharges=NULL) {
		//If we were passed a list of upcharges, we will only return those upcharge rows.
		//If we are passed a layout and not upcharges, we will return the default.
		foreach ($upcharges as $value) {

			if (!is_null($upcharges)) {

				$sql = Connection::getHandle()->prepare("SELECT stmha.id, stmha.note, mha.id as mounting_hole_arrangement_id, mha.`name` FROM bs_streetsign_tool_mounting_hole_arrangements stmha
										 INNER JOIN bs_mounting_hole_arrangements mha ON (mha.id = stmha.mounting_hole_arrangement_id
										 AND mha.active = TRUE) WHERE stmha.id = ?");
				$sql->execute(array($value));

				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$row['type'] = 'Mounting';
					$results[] = $row;
				}
			}

			return $results;

		}

	}

    /**
     * Update the Inventory amount and Price Rank of the SKU
     *
     * @param int $inventory
     * @param int $priceRank
     *
     * @return string
     */
    public function updateSku($inventory, $priceRank) {

        $pricing = $this->getPricing();
        $newPricingId = '';

        //echo "<pre>".print_r($pricing, 1)."</pre>";

        //Get the Pricing ID based on this price rank
        $query = Connection::getHandle()->prepare("SELECT id FROM bs_pricing WHERE material_code = :mc AND price_rank = :pr ");

        $query->bindParam(':mc', $pricing->getMaterialCode(), PDO::PARAM_INT);
        $query->bindParam(':pr', $priceRank, PDO::PARAM_INT);

        if ( $query->execute() ) {

            $data = $query->fetch(PDO::FETCH_ASSOC);

            $newPricingId = (int)$data['id'];

        }

        if ( $newPricingId == 0 ) {

            return false;

        } else {

            $query = Connection::getHandle()->prepare("UPDATE bs_skus SET inventory = :inv, pricing_id = :pi WHERE id = :id");

            $query->bindParam(':inv', $inventory, PDO::PARAM_INT);
            $query->bindParam(':pi', $newPricingId, PDO::PARAM_INT);
            $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

            if ( $query->execute() ) {

                return true;

            }

        }

    }

    /**
     * Create a static instance of Sku()
     *
     * @param null $id
     * @return Sku
     */
    public static function create($id = NULL) { return new self($id); }
}
