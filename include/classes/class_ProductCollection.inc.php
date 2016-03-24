<?php

// by product

/**
 * Class ProductCollection
 */
class ProductCollection extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT pc.id AS id, pc.name AS name, pc.active AS active,
                                GROUP_CONCAT(pcp.id) AS array_product_collection_product_ids
                             FROM bs_product_collections pc
                             LEFT JOIN bs_product_collection_products pcp ON (pcp.product_collection_id = pc.id) WHERE pc.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = " GROUP BY pc.id ";

	/**
	* Unique product_collection id
	* DB column: bs_product_collections.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Name of the product collection
	* DB column: bs_product_collections.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* An array of ProductCollectionProduct ids
	* DB table: bs_product_collection_products
	*
	* @var array $productCollectionProducts
	* @see class_ProductCollectionProducts.php
	*/
	private $productCollectionProductIds;

	/**
	* An array of ProductCollectionProduct objects
	* DB table: bs_product_collection_products
	*
	* @var array $productCollectionProducts
	* @see class_ProductCollectionProducts.php
	*/
	private $productCollectionProducts;

	/**
	* Whether or not the product collection is active
	* DB column: bs_product_collections.active.
	*
	* @var int|bool $active
	*/
	private $active;

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

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND pc.id = :id " );

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }
			}

			$this->setName($data['name'])
				 ->setProductCollectionProductIds($data['array_product_collection_product_ids'])
				 ->setActive($data['active']);

		} else {

			 // Trigger a notice if an invalid ID was supplied.
	        trigger_error('Cannot load Material properties: \'' . $this->getId(). '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return __CLASS__
	*
	* @param int $id
	* @return ProductCollection() Return current class object
	*/
	private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	* Set the $name and return current class
	*
	* @param string $name
	* @return ProductCollection() Return current class object
	*/
	public function setName($name = "") {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set product collection object
	* $productCollectionProducts will hold instance of ProductCollectionProduct()
	*
	* @param  int $productCollectionProductIds
	* @return ProductCollection()
	*/
	public function setProductCollectionProductIds($productCollectionProductIds) {

		$this->productCollectionProductIds = !empty($productCollectionProductIds) ?
											explode(",", $productCollectionProductIds) : NULL;
		return $this;

	}

	/**
	* Set the $active and return $this
	*
	* @param  int|bool 	$active
	* @return ProductCollection()
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
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * @return array
     */
    public function getProductCollectionProducts() {

		foreach ($this->productCollectionProductIds AS $id){

			$this->productCollectionProducts[] = ProductCollectionProduct::create($id);
		}

		return $this->productCollectionProducts;
	}

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return ProductCollection
     */
    public static function create($id = NULL) { return new self($id); }

}