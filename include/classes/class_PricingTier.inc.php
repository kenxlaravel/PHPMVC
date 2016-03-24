<?php

/**
 * Class PricingTier
 */
class PricingTier extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, pricing_id, minimum_quantity, price, streetsign_accessory_display, active
                             FROM bs_pricing_tiers WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	* DB column bs_pricing_tiers.id
	*
	* @var int $id
	*/
	private $id;

	/**
	* DB column bs_pricing_tiers.minimum_quantity
	*
	* @var int $minimumQuantity
	*/
	private $minimumQuantity;

	/**
	* Holds the max quantity -1 depending on the min quantity
	* This is used to add the number next to the number tiers
	* Example: The old school way was 1,2,3,4,etc.. The new
	* and improved way will now be 1-4, 5-10, 11-15, etc.
	*
	* bs_pricing_tiers.minimum_quantity (maxquantity-1)
	*
	* @var int $maxQuantityArray
	*/
	private $maxQuantityArray;

	/**
	* DB column bs_pricing_tiers.price
	*
	* @var float $price
	*/
	private $price;

	/**
	* DB column bs_pricing_tiers.streetsign_accessory_display
	*
	* @var int|bool $streetsignAccessoryDisplay
	*/
	private $streetsignAccessoryDisplay;

	/**
	* DB column bs_pricing_tiers.active
	*
	* @var int|bool $active
	*/
	private $active;

	/**
	 * @var int $pricingId
	 */
	private $pricingId;


	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material
	 * @throws Error if $id is not set
	 */
	public function __construct($id = NULL) {

			$this->setId($id);

			if( !is_null($this->getId()) ) {

                // Set cache object
                CacheableEntity::__construct(get_class($this), $this->getId());

                // Attempt to get data from cache
                $data = $this->getCache();

                if( empty($data) ) {

                    $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                    $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                    if ($query->execute()) {

                        $data = $query->fetch(PDO::FETCH_ASSOC);
                        //$this->storeCache($data);
                    }
                }

				$this->setPricingId($data['pricing_id'])
					 ->setMinimumQuantity($data['minimum_quantity'])
					 ->setPrice($data['price'])
					 ->setStreetsignAccessoryDisplay($data['streetsign_accessory_display'])
					 ->setActive($data['active']);

			} else {

				// Trigger a notice if an invalid ID was supplied.
	            trigger_error('Cannot load PricingTier properties: \'' . $id . '\' is not a valid ID number.');

			}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set the $id and return InstallationStepList
	*
	* @param int $id
	* @return PricingTier() Return current class object
	*/
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	public function setPricingId($pricingId) {
		$this->pricingId = isset($pricingId) && is_numeric($pricingId) && $pricingId > 0 ? (int) $pricingId : NULL;
		return $this;
	}
	/**
	* Set the max quantity for the tiers
	* DB column: bs_pricing_tiers.minimum_quantity
	*
	* @param int $minimumQuantity
	* @return PricingTier()
	*/
	public function setMinimumQuantity($minimumQuantity) {
		$this->minimumQuantity = (int) $minimumQuantity;
		return $this;
	}

	/**
	 * Set the $price for the current product / sku
	 *
	 * @param int $price Product / sku price
	 * @return PricingTier Return current class object
	 */
	public function setPrice($price) {
		$this->price = (double) $price;
		return $this;
	}

	/**
	 * set $streetsignAccessoryDisplay and return current class
	 *
	 * @param bool $streetsignAccessoryDisplay
	 * @return array Return current class object
	 */
	public function setStreetsignAccessoryDisplay($streetsignAccessoryDisplay) {
		$this->streetsignAccessoryDisplay = (bool) $streetsignAccessoryDisplay;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $active bs_installation_methods.active
	 * @return object Return current class object
	 */
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return bool
     */
    public function getStreetsignAccessoryDisplay() { return $this->streetsignAccessoryDisplay; }

    /**
     * @return int
     */
    public function getMinimumQuantity() { return $this->minimumQuantity; }

    /**
     * @return int
     */
    public function getMaxQuantity() { return $this->maxQuantityArray; }

    /**
     * @return float
     */
    public function getPrice() { return $this->price; }

    /**
     * @return bool
     */
    public function isActive() { return $this->active; }

	/**
	* Example : $Sku->getPricing()->getPricingTier()->getPackageUnitPrice( $Sku->getPackaging()->getInnerUnits() )
	* Gets price for unit in a package
	* @param  int 	[How many come in a package]
	* @return float [Price per item in a package]
	*/
	public function getPackageUnitPrice($packagingAmount) {
		return $this->getPrice() / $packagingAmount;
	}

    /**
     * @param null $id
     * @return PricingTier
     */
    public static function create($id = NULL) { return new self($id); }

}