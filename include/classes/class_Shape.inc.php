<?php

// by size

/**
 * Class Shape
 */
class Shape extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - $FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, active FROM bs_shapes WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

	/**
	* Unique Shape ID
	* DB Column: bs_shapes.id (children: bs_sizes)
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Name of the shape
	* DB Column: bs_shapes.name
	* 
	* @var string $name
	*/
	private $name;

	/**
	* Whether shape is active or not
	* DB Column: bs_shapes.active
	* 
	* @var boolean $active
	*/
	private $active;

	/**
	* Path to where cache files are located
	* 
	* @var string $cacheDirectory
	*/
	private $cacheDirectory = 'products/Shapes/';

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 * 
	 * @param int $id Id used to query records from bs_material
	 * @throws Error if $id is not set
	 */
	public function __construct($id = NULL) {
		
		 // Set the ID.
        $this->setId($id);

 	 	$this->setCacheDir($this->cacheDirectory);

        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if (empty($data)) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }

            } else {

                // Trigger a notice if an invalid ID was supplied.
                trigger_error('Cannot load ProductCollection properties: \'' . $id . '\' is not a valid ID number.');
            }

            $this->setName($data['name'])->setActive($data['active']);
        }
	}

    /*************************************************
     * Start Setters
     **************************************************/
    /**
     * Set the $id and return class object Shape()
     *
     * @param int $id
     * @return Shape() Return current class object
     */
    private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

    /**
     * Set the $name and return  class object Shape()
     *
     * @param string $name
     * @return Shape() Return current class object
     */
    public function setName($name = '') {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

    /**
	* Set the $active and return class object Shape()
	* 
	* @param int|bool $active
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
     * Get the Shape Id
     *
     * @return int $id
     */
    private function getId() { return $this->id; }

    /**
     * Get the Shape name
     *
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * If Shape is enabled, return its data, else return false
     *
     * @return bool $active
     */
    public function isActive() { return $this->active; }

    /**
     * Create a static instance of Shape()
     *
     * @param null $id
     * @return Shape
     */
    public static function create ($id = NULL) { return new self($id); }

}