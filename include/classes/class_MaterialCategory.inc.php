<?php

/**
 * Class MaterialCategory
 */
class MaterialCategory extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id AS id, name AS name, position as position, active AS active
                             FROM bs_material_categories WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	* Unique MaterialCategory id
	* DB column: bs_material_categories.id.
	* @var int $id
	*/
	private $id;

	/**
	* Name of material category
	* DB column: bs_material_categories.name.
	* @var string $name
	*/
	private $name;

    /**
     * DB column: bs_material_categories.position.
     *
     * @var int $position
     */
    private $position;

	/**
	* Whether or not the bs_material_category is active
	* DB column: bs_material_categories.active.
	*
     * @var int|bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material_category
	 * @throws [Trigger]Error if $id is not set
	 */
	public function __construct($id = NULL) {

		$this->setId($id);

		if( !is_null($this->getId())  ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

			if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id  ");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    //This record was not cached, lets create a cache file for it
                    $this->storeCache($data);
                }
            }

            $this->setName($data['name'])
                 ->setPosition($data['position'])
                 ->setActive($data['active']);

        } else {

             // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load Material Category properties: \'' . $this->getId() . '\' is not a valid ID number.');
        }

	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param [int] $id
	* @return MaterialCategory() Return current class object
	*/
	private function setId($id) {
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

    /**
     * @param $position
     * @return MaterialCategory()
     */
    public function setPosition($position) {
        $this->position = isset($position) && is_numeric($position) && $position > 0 ? (int)$position : NULL;
        return $this;
    }

	/**
	* Set the $name and return $this
	*
	* @param [string] $name
	* @return MaterialCategory() Return current class object
	*/
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set the $active and return $this
	*
	* @param [bool] $active
	* @return MaterialCategory() Return current class object
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
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @return int
     */
    public function getPosition () { return $this->position; }

    /**
     * @param null $id
     * @return MaterialCategory
     */
    public static function create($id = NULL) { return new self($id); }
}
