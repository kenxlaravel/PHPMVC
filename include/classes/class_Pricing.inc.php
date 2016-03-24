<?php

/**
 * Class Pricing
 */
class Pricing extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT p.id AS id, p.material_code AS material_code, p.price_rank AS price_rank,
	                            p.made_to_order AS made_to_order, GROUP_CONCAT(pt.id) AS array_pricing_tier_ids

                             FROM bs_pricing p

                             INNER JOIN bs_pricing_tiers pt ON ( p.id = pt.pricing_id ) WHERE pt.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = " GROUP BY p.id ";

	/**
	* Unique current pricing id in pricing
	* DB column: bs_pricing.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	 * Unique code used for the pricing material code
	 * DB column: bs_pricing.material_code
	 *
	 * @var string $materialCode
	 */
	private $materialCode;

	/**
	 * Materials pricerank.
	 * DB column: bs_pricing.priceRank
	 *
	 * @var int $priceRank;
	 */
	private $priceRank;

	/**
	 * Whether or not product exists in warehouse or product needs to be manufactured
	 * DB Column: bs_pricing.made_to_order
	 *
	 * @var int|bool $madeToOrder
	 */
	private $madeToOder;

	/**
	 * Array of pricing tier ids
	 * DB TABLE: bs_pricing_tiers
	 *
	 * @var array $pricingTierIds
	 */
	private $pricingTierIds;

	/**
	* Array of PricingTier objects
	* DB table: bs_pricing_tiers
	*
	* @var array $priceTiers
	*/
	private $priceTiers;

	/**
	 * @var array
	 */
	private $maxQuantityArray;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material
	 * @throws Error if $id is not set
	 */
	public function __construct($id = NULL) {

		$this->setId($id);

		if ( !is_null($this->getId()) ) {

			// Set cache object
			CacheableEntity::__construct(get_class($this), $this->getId());

			// Attempt to get data from cache
			$data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND p.id = :id " . self::ADDITIONAL_CLAUSES);

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    //$this->storeCache($data);
                }
			}

			// Set the class properties
			$this->setMaterialCode($data['material_code'])
				 ->setPriceRank($data['price_rank'])
				 ->setMadeToOrder($data['made_to_order'])
				 ->setPricingTierIds($data['array_pricing_tier_ids']);

		} else {

			// Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load MountingHoleArrangement properties: \'' . $id . '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set the $id and return InstallationStepList
	*
	* @param int $id
	* @return InstallationStepList()
	*/
	public function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set the material code and trim() it clearing out any spaces.
	 *
	 * @param string $materialCode
	 * @return Pricing()
	 */
	public function setMaterialCode($materialCode) {
	 	$this->materialCode = !empty($materialCode) ? trim($materialCode) : NULL;
	 	return $this;
	}

    /**
     * @param $pricingTierIds
     * @return Pricing()
     */
    public function setPricingTierIds($pricingTierIds) {
        $this->pricingTierIds = !empty($pricingTierIds) ? explode(",", $pricingTierIds) : NULL;;
        return $this;
    }

	/**
	 * Set the $priceRank integer
	 *
	 * @param int $priceRank assign and pass the priceRank
	 * @return Pricing()
	 */
	public function setPriceRank($priceRank) {

		$this->priceRank = (int) $priceRank;
		return $this;
	}

	/**
	 * Set $madeToOrder bool
	 *
	 * @param int|bool
	 * @return Pricing()
	 */
	public function setMadeToOrder($madeToOrder) {

		$this->madeToOder = (bool) $madeToOrder;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * @return int $id
     */
	public function getId() { return $this->id; }

    /**
     * @return int $priceRank
     */
    public function getPriceRank() { return $this->priceRank; }

    /**
     * @return string $materialCode
     */
    public function getMaterialCode() { return $this->materialCode; }

    /**
     * @return mixed
     */
    public function isMadeToOrder() { return $this->madeToOrder; }

    /**
     * @return array $priceTiers;
     */
    public function getPriceTiers() {

		foreach ( $this->pricingTierIds AS $id ) {

			$this->priceTiers[$this->getId()][] = PricingTier::create((int)$id);
		}

		return $this->priceTiers;
	}

	/**
	 * This method takes care of calculating the minimum
	 * quantity for the tiers used for the current sku or product.
	 * NOTE: Temp functionality subject to change (concept)
	 *
	 * @param array $maxQuantityArray
	 * @return PricingTier() return our current class (self)
	 */
	public static function setMaxQuantityArray($maxQuantityArray) {

		$maxQuantityTiers = array();

		$total = count($maxQuantityArray) - 1;

		foreach (array_keys($maxQuantityArray) as $index => $k) {

			$curIndex  = $maxQuantityArray[$k];
			$lastIndex = end($maxQuantityArray[$k]);

			if( ($k + 1) <= $total ) {

				$nextIndex = $maxQuantityArray[$k + 1];
			}

			$sequence = ($k >= $total) ? $lastIndex['minimumQuantity']."+" : " - ".($nextIndex ['minimumQuantity'] - 1);
			$maxQuantityArray[$index]["minimumQuantity"] = $curIndex['minimumQuantity'].$sequence;
		}

		$maxQuantityTiers = $maxQuantityArray;

		return $maxQuantityTiers;
	}

    /**
	* Returns correct pricing tier based on quantity added to cart
	 *
	* @param  $quantity
	* @return mixed
	*/
	public function getPriceTier($quantity) {

        $priceTier = "";

		if( $quantity > 0 ) {

			foreach( $this->priceTiers AS $tier ) {

				if( $quantity >= $tier->getMinimumQuantity() ) {

					$priceTier = $tier;
					continue;
				}
				return $priceTier;
			}
		}

        return false;
	}

    /**
     * @param null $id
     * @return Pricing
     */
    public static function create($id = NULL) { return new self($id); }

}