<?php

class ProductAccessory extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT afp.id, afp.product_name as title, afp.product_subtitle as subtitle, p.active,
									p.id as product_id, pa.id as accessory_product_id, afp.position
								FROM
									bs_products as p
									INNER JOIN bs_accessory_families as af ON p.accessory_family_id = af.id
									INNER JOIN bs_accessory_family_products as afp ON af.id = afp.accessory_family_id
									INNER JOIN bs_products AS pa ON afp.product_id = pa.id
									WHERE
										p.active = 1
										and
										pa.active = 1";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

	/**
	* Unique id of the product accessory relation
	* DB column: bs_accessory_family_products.id.
	* @var int $id
	*/
	private $id;

	/**
	* Name of accessory relation
	* DB column: bs_accessory_family_products.product_name as title.
	* @var string $title
	*/
	private $title;

	/**
	* subtitle of product accessory relation
	* DB column: bs_accessory_family_products.product_subtitle as subtitle.
	* @var string $subtitle
	*/
	private $subtitle;

	/**
	* Whether or not the product accessory relation is active
	* DB column: bs_products.active.
	* @var int|bool $active
	*/
	private $active;

	/**
	* Product object
	* DB column: ALIASING A SELF-JOIN bs_products as bs_product_accessories bs_products.id as accessory_product_id.
	* @var Product $AccessoryProduct
	*/
	private $AccessoryProduct;

	/**
	* Position of the accessory on product page
	* DB column: bs_accessory_family_products.position.
	* @var int $position
	*/
	private $position;

	/**
	 * [$accessoryIds description]
	 * @var int
	 */
	private $accessoryIds;

	/**
	 * @var int $product_id
	 */
	private $productId;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 * 
	 * @param int $id Id used to query records from bs_installation_question_answer
	 * @param int $product_id
	 */
	public function __construct($id, $product_id = 4567) {

		$this->setId($id)->checkProductId($product_id);

		if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id ");

				if( !is_null($this->productId)) {
					$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id AND product_id = :product_id ");
					$query->bindParam(':product_id', $this->productId, PDO::PARAM_INT);
				}

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {
					$data = $query->fetch(PDO::FETCH_ASSOC);
					$this->storeCache($data);
				}
			}

			$this->setTitle($data['title'])->setSubtitle($data['subtitle'])
				 ->setProductId($data['product_id'])->setAccessoryProductId($data['accessory_product_id'])
				 ->setActive($data['active']);
		} else {
	            
	            //Trigger a notice if an invalid ID was supplied.
	            trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
		}
	}


	// Setters
    /**
     * @param null $id
     * @return ProductAccessory()
     */
    private function setId($id = NULL) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Check the products id
	 *
	 * @param int $product_id
	 * @return $this
	 */
    private function checkProductId($product_id = NULL) {

		$this->productId = isset($product_id) && is_numeric($product_id) && $product_id > 0 ? (int) $product_id : NULL;
		return $this;
	}

    /**
     * @param $title
     * @return ProductAccessory()
     */
    public function setTitle($title) {

		$this->title = !empty($title) ? trim($title) : NULL;
		return $this;
	}

    /**
     * @param $subtitle
     * @return ProductAccessory()
     */
    public function setSubtitle($subtitle) {
		$this->subtitle = !empty($subtitle) ? trim($subtitle) : NULL;
		return $this;
	}

    /**
     * @param $productid
     * @return ProductAccessory()
     */
    public function setProductId($productid) {

		$this->productId = isset($productid) && is_numeric($productid) && $productid > 0 ? (int) $productid : NULL;
		return $this;
	}

    /**
     * @param $accessoryProductIds
     * @return ProductAccessory()
     */
    public function setAccessoryProductId($accessoryProductIds) {

		$this->accessoryIds = isset($accessoryProductIds) && is_numeric($accessoryProductIds) && 
									$accessoryProductIds > 0 ? (int) $accessoryProductIds : NULL;
		return $this;

	}

    /**
     * @param $position
     * @return ProductAccessory()
     */
    public function setPosition($position) {

		$this->position = isset($position) && is_numeric($position) && $position > 0 ? (int) $position : NULL;
		return $this;
	}

    /**
     * @param $active
     * @return ProductAccessory()
     */
    public function setActive($active) {

		$this->active = (bool) $active;
		return $this;
	}

	// Getters
    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return string
     */
    public function getTitle() {  return $this->title; }

    /**
     * @return string
     */
    public function getSubtitle() { return $this->subtitle; }

    /**
     * @return array
     */
    public function getAccessoryProduct() {

		$this->AccessoryProductId[$this->accessoryIds] = Product::create($this->accessoryIds);
		return $this->AccessoryProductId; 
	}

    /**
     * @return int
     */
    public function getPosition() { return $this->position; }

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

	// create a static self pricing class
    /**
     * @param null $id
     * @return ProductAccessory
     */
    public static function create($id = NULL) { return new self($id); }
}

