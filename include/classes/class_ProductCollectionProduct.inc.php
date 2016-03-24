<?php

class ProductCollectionProduct extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, product_collection_id, product_id, name, subtitle, product_collection_position,
    							product_position, product_collection_ref
                             FROM bs_product_collection_products ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	* Unique product collection id
	* DB Column: bs_product_collections.id.
	* 
	* @var int $id
	*/
	protected $id;

	/**
	 * Product Id being returned
	 * DB Column: bs_product_collections_products.product_id
	 *
	 * @var int $productId
	 */
	private $collectionProductId;

	/**
	* Name of the product collection
	* DB Column: bs_product_collections.name.
	* 
	* @var string $name
	*/
	private $name;

	/**
	* Holds Product object
	* DB Column: bs_product_collection_products.product_id
	* 
	* @var Product $CollectionProduct
	*/
	private $productCollectionProducts;

	/**
	 * Holds our product collection ref string
	 * DB Column: bs_product_collection_products.product_collection_ref
	 *
	 * @var string $productCollectionRef
	 */
	private $productCollectionRef;

	/**
	* Subtitle of the product collection
	* DB Column: bs_product_collection_products.subtitle
	* 
	* @var string $subtitle
	*/
	private $subtitle;

	/**
	* The position of the product collection product that will be sorted by ASC, 
	* if not by $id DB Column bs_product_collection_products.product_collection_position 
	* 
	* @var int $productCollectionPosition
	*/
	private $productCollectionPosition;

	/**
	* The position of the product collection sorted by ASC
	* This position will sort bs_product_collections.name in order
	* 
	* @var int $productPosition
	*/
	private $productPosition;

	/**
	* Whether or not the product collection is active
	* DB Column: bs_product_collections.active.
	* 
	* @var int|bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 * 
	 * @param int $id Id
	 * @throws Error if $id is not set
	 */
	public function __construct($id = NULL) {

		 // Set the ID.
        $this->setId($id);

		if ( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " WHERE id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }
            }

			$this->setProductPosition($data['product_position'])
				 ->setProductCollectionProducts($data['product_collection_id'])
				 ->setProductCollectionPosition($data['product_collection_position'])
				 ->setProductCollectionRef($data['product_collection_ref'])
				 ->setCollectionProductId($data['product_id'])
				 ->setName($data['name'])
				 ->setSubtitle($data['subtitle']);

		} else {

			 // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load ProductCollectionProduct properties: \'' . $id . '\' is not a valid ID number.');
		
		}
	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set privately the $id and return current object
	* 
	* @param int $id
	* @return ProductCollectionProduct Return current class object
	*/	
	private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set the products id that is being retrived from the collection table
	 *
	 * @param NULL|Int $productId
	 * @return ProductCollectionProduct()
	 */
	private function setCollectionProductId ($productId = NULL) {

		$this->collectionProductId = isset($productId) && is_numeric ($productId) && $productId > 0 ? (int) $productId : NULL;
		return $this;
	}


	/**
	* Set the collection name and return ProductCollectionProduct()
	* DB Column: bs_product_collection_products.name
	* 
	* @param NULL|string $name
	* @return ProductCollectionProduct() Return current class object
	*/	
	public function setName($name = "") {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set the product collection product subtitle
	* DB Column: bs_product_collection_products.subtitle
	* 
	* @param string $subtitle
	* @return ProductCollectionProduct() Return current class object
	*/
	public function setSubtitle($subtitle) {
		$this->subtitle = !empty($subtitle) ? trim($subtitle) : NULL;
		return $this;
	}

	/**
	* Set the  product collection product position 
	* Check and make sure that the value passed is an integer 
	* DB Column: bs_product_collection_products.product_collection_position
	* 
	* @param int $productCollectionPosition
	* @return ProductCollectionProduct() Return current class object
	*/
	public function setProductCollectionPosition($productCollectionPosition) {
		$this->productCollectionPosition = 
			isset($productCollectionPosition) && is_numeric($productCollectionPosition) && 
					$productCollectionPosition > 0 ? (int) $productCollectionPosition : NULL;

		return $this;
	}

	/**
	* Set the product collection position
	* DB Column: bs_product_collection_products.product_position
	*  
	* @param int 	$productPosition
	* @return ProductCollectionProduct() Return current class object
	*/
	public function setProductPosition($productPosition) {

		$this->productPosition = isset($productPosition) && is_numeric($productPosition) &&
										$productPosition > 0 ? (int) $productPosition : NULL;
		return $this;
	}

	/**
	 * Set the product collection ref string
	 * DB Column: bs_product_collection_products.product_collection_ref
	 *
	 * @param $productCollectionRef
	 * @return ProductCollectionProduct()
	 */
	public function setProductCollectionRef($productCollectionRef) {

		$this->productCollectionRef = !empty($productCollectionRef) ? trim($productCollectionRef) : NULL;
		return $this;
	}

	/**
	*  Instantiate an instance of ProductCollection
	*  $productCollectionProducts holds the object of ProductCollection()
	* 
	*  @param int $productCollectionProducts
	*  @return ProductCollectionProduct() Return current class object
	*/
    //@TODO: Get the id's instead of the object...
	public function setProductCollectionProducts($productCollectionProducts) {
		$this->productCollectionProducts = ProductCollection::create($productCollectionProducts);
		return $this;
	}

	/**
	* Check if its active, return true if it is activated
	* false if its not
	* 
	* @param bool $active
	* @return ProductCollectionProduct() Return current class object
	*/	
	public function setActive($active) {
		$this->active = (bool) $active;
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
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * @return string $subtitle
     */
    public function getSubTitle() { return $this->subtitle; }

    /**
     * @return int $productPosition
     */
    public function getProductPosition() { return $this->productPosition; }

    /**
     * @return int $productCollectionPosition
     */
    public function getPosition () { return $this->productCollectionPosition; }

	/**
	 * @return int $collectionProductId
	 */
	public function getCollectionProductId() { return $this->collectionProductId; }

	/**
	 * @return string $productCollectionRef
	 */
	public function getProductCollectionRef() {return $this->productCollectionRef; }

    /**
     * @return int $productCollectionProducts
     */
    public function getProductCollectionProducts() { return $this->productCollectionProducts; }

    /**
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

	/**
	 *
	 * @param $a
	 * @param $b
	 * @return array
	 */
	//@todo: Make key dynamic. Use use() for the extra parameters
	public static function sortCollectionByPositionASC ($a, $b) {

		return $a['position'] - $b['position'];
	}

    /**
     * @param null $id
     * @return ProductCollectionProduct
     */
    public static function create($id = NULL) { return new self($id); }
}