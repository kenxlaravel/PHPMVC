<?php


/**
 * Class InstallationAccessory
 */
class InstallationAccessory extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, title, subtitle, installation_method_id, featured, accessory_product_id, position, active
                             FROM bs_installation_accessories WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	* Unique intallation accessory id
	* DB column: bs_installation_accessories.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* installation accessory name
	* DB column: bs_installation_accessories.title.
	*
	* @var string $title
	*/
	private $title;

	/**
	* installation accessory subtitle
	* DB column: bs_installation_accessories.subtitle.
	*
	* @var string $subtitle
	*/
	private $subtitle;

	/**
	* Whether or not the accessory is featured
	* DB column: bs_installation_accessories.featured
	*
	* @var bool $featured
	*/
	private $featured;

	/**
	* Product object
	* DB column: bs_installation_accessories.accessory_product_id.
	*
	* @var Product $AccessoryProduct
	*/
	private $AccessoryProduct;

	/**
	* Position of accessory for display
	* DB column: bs_installation_accessories.position
	*
	* @var int $position
	*/
	private $position;

	/**
	* Whether or not the accessory is active
	* DB column: bs_installation_accessories.active.
	*
	* @var int|bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records
	 */
	public function __construct($id) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

			CacheableEntity::__construct(get_class($this), $this->getId());
			$data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id ");
				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {
					$data = $query->fetch(PDO::FETCH_ASSOC);
				}

				$this->setTitle($data['title'])->setSubtitle($data['subtitle'])
					 ->setFeatured($data['featured'])->setPosition($data['position'])
					 ->setAccessoryProduct($data['accessory_product'])->setActive($data['active']);
			}

		} else {

		 trigger_error('Cannot load InstallationAccessory properties: \'' . $id . '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return InstallationAccessory() Return current class object
	*/
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set the InstallationAccesory title
	 *
	 * @param string $title
	 * @return InstallationAccessory() Return current class object
	 */
	public function setTitle($title) {
		$this->title = !empty($title) ? trim($title) : NULL;
		return $this;
	}

	/**
	 * Set the InstallationAccesory subtitle
	 *
	 * @param string $subtitle
	 * @return InstallationAccessory() Return current class object
	 */
	public function setSubtitle($subtitle) {
		$this->subtitle = !empty($subtitle) ? trim($subtitle) : NULL;
		return $this;
	}

	/**
	 * Set the InstallationAccesory featured
	 *
	 * @param bool $featured
	 * @return InstallationAccessory() Return current class object
	 */
	public function setFeatured($featured) {
		$this->featured = (bool) $featured;
		return $this;
	}

	/**
	 * Set the InstallationAccessory accessoryProductId and return
	 * AccessoryProduct as a Product object
	 *
	 * @param bool $accessoryProductId
	 * @return InstallationAccessory() Return current class object
	 */
	public function setAccessoryProduct($accessoryProductId) {
		$this->AccessoryProduct = Product::create($accessoryProductId);
		return $this;
	}

	/**
	* Set the InstallationAccessory $position and return
	* InstallationAccessory object
	*
	* @param int $position
	* @return InstallationAccessory() Return current class object
	*/
	public function setPosition($position) {
		$this->position = (int) $position;
		return $this;
	}

	/**
	* Set the InstallationAccessory $active and return
	* InstallationAccessory() object
	*
	* @param bool $active
	* @return InstallationAccessory() Return current class object
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
    public function getTitle() { return $this->title; }

    /**
     * @return string
     */
    public function getSubtitle() { return $this->subtitle; }

    /**
     * @return bool
     */
    public function getFeatured() { return $this->featured; }

    /**
     * @return Product
     */
    public function getAccessoryProduct() { return $this->AccessoryProduct; }

    /**
     * @return int
     */
    public function getPosition() { return $this->position; }

    /**
     * @return mixed
     */
    public function isActive() { return $this->active; }

    /**
     * @param $id
     * @return InstallationAccessory
     */
    public static function create($id) { return new self($id); }

}