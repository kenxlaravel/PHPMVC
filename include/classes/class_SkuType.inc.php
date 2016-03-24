<?php

/**
 * Class SkuType
 */
class SkuType extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, title, subtitle, active, product_id, recommend_product_id, position
						  	 FROM bs_product_types WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
     const ADDITIONAL_CLAUSES = "GROUP BY id;";

	/**
	* Unique product type id
	* DB column: bs_product_types.id.
    *
	* @var int $id
	*/
	private $id;

	/**
	* Name of the product type
	* DB column: bs_product_types.name.
    *
	* @var string $name
	*/
	private $name;

	/**
     * Name of the product type
     * DB column: bs_product_types.name.
     *
     * @var string $namePlural
     */
	private $namePlural;

	/**
     * Whether product type is active or not
     * DB column: bs_product_types.active.
     *
     * @var int|bool $active
     */
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material
	 * @throws [trigger] Error if $id is not set
	 */
	public function __construct($id){

		$this->setId($id);

        if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

		    if( empty($data) ) {

                $query = Connection::getHandle()
                             ->prepare(self::FULL_TABLE_DUMP . " AND id = :id LIMIT 10");

                $query->bindParam(':id', $this->id, PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }
            }

            $this->setName($data['name'])->setActive($data['active']);
        }
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
     * Set the $name and return  class object Shape()
     *
     * @param string $id
     * @return Shape() Return current class object
     */
	public function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
     * Set the $name and return class object ProductType()
     *
     * @param string $name
     * @return Shape() Current class object
     */
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

    /**
     * @param string $namePlural
     * @return ProductType()
     */
    public function setNamePlural($namePlural) {
        $this->namePlural = !empty($namePlural) ? trim($namePlural) : NULL;
        return $this;
    }

	/**
     * Set the $active and return class object ProductType()
     *
     * @param bool $active
     * @return Shape() Current class object
     */
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the SkuType id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the SkuType name
     *
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * If the SkuType is active return its records, else return false
     *
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

    /**
     * Create a static instance of SkuType()
     *
     * @param null $id
     * @return SkuType
     */
    public static function create($id = NULL) { return new self($id); }
}
