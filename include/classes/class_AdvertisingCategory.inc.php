<?php


/**
 * Class AdvertisingCategory
 */
 class AdvertisingCategory extends CacheableEntity {

     /**
      * Constant used for two purposes
      *
      * - Getting the record from the database
      * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
      */
     const FULL_TABLE_DUMP = "SELECT id, name, active FROM bs_advertising_categories WHERE active = 1 ";

     /**
      * Extra query parameter used with $FULL_TABLE_DUMP
      */
     const ADDITIONAL_CLAUSES = "GROUP BY id;";

     /**
 	 * The id of the Add
 	 * DB column: bs_advertising_categories.id
 	 *
 	 * @var int $id
 	 */
	private $id;

	/**
	 * The name of the Add
	 * DB column: bs_advertising_categories.name
     *
	 * @var string $name
	 */
	private $name;

	/**
	 * Check if current Add is active
	 * DB column: bs_advertising_categories.active
	 *
     * @var int|bool $active
	 */
	private $active;

	/**
	 * Construct will handle setting calling the setters methods
     *
     * @param int $id Id used to query records from ?? table
	 * @throws Error if $id is not set
	 */
 	public function __construct($id) {
	 	// Set the ID.
        $this->setId($id);
		// Get cache dir
  		$this->setCacheDir($this->cacheDirectory);
		// Attempt to get data from cache
		$data = $this->getCache($this->getId());

		if( empty($data) ) {

			if ( !is_null($this->getId()) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");
				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    //This record was not cached, lets create a cache file for it
                    $this->storeCache($id, $data);
                }
			} else {

				 // Trigger a notice if an invalid ID was supplied.
		        trigger_error('Cannot load AdvertisingCategory properties: \'' . $id . '\' is not a valid ID number.');
			}
		}
 	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
     * Set privately the $id and return current class object
     *
     * @param int $id
     *
     * @return AdvertisingCategory
     */
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
     * Set the $name and return current class object
     *
     * @param $name
     * @return AdvertisingCategory() Return class object
     */
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
     * Set the $active value to true or false, and return current class object
     *
     * @param int|bool $active
     * @return AdvertisingCategory() Return current class object
     */
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
     /**
      * Get the id of the current record
      *
      * @method getId()
      * @return int $id
      */
     public function getId() { return $this->id; }

     /**
      * Get the name of the category
      *
      * @method getName
      * @return string $name
      */
     public function getName() { return $this->name; }

     /**
      * Get the active value from $active
      *
      * @method isActive
      * @return bool|int $active
      */
     public function isActive() { return $this->active; }

     /**
      * Create AdvertisingCategory with out an instance.
      *
      * @param null $id
      * @return AdvertisingCategory
      */
     public function create($id = NULL) { return new self($id); }
}
