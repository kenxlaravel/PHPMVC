<?php
/**
 * Class Laminate
 */
class Laminate extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id AS id, name AS name, position, active AS active FROM bs_laminates WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique laminate id
	* DB column: bs_laminates.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Name of the laminate
	* DB column: bs_laminates.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* Whether or not the laminate is active.
	* DB column: bs_laminates.active.
	*
	* @var int|bool $active
	*/
	private $active;

	/**
	 * Position of laminate to display
	 * DB column: bs_laminates.position
	 *
	 * @var int position
	 */
	private $position;


    /**
     * @param null $id
     */
    public function __construct($id = NULL) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->id);

            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }
			}


            $this->setName($data['name'])
				 ->setPosition($data['position'])
                 ->setActive($data['active']);

        } else {
             // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load laminate properties: \'' . $this->getId() . '\' is not a valid ID number.');
        }
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return Laminate Return current class object
	*/
	private function setId($id = NULL) {
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	* Set privately the $string and return Laminate()
	*
	* @param string $name
	* @return Laminate() Return current class object
	*/
	public function setName($name = '') {
		$this->name = ( isset($name) ? trim($name) : NULL );
		return $this;
	}

	/**
	 * Set the position
	 *
	 * @param int $position
	 * @return Laminate()
	 */
	public function setPosition($position) {
		$this->position = $position;
		return $this;
	}

	/**
	* Set privately the $active and return Laminate()
	*
	* @param int|bool $active
	* @return Laminate() Return current class object
	*/
	public function setActive($active = FALSE) {
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
	 * @return int
	 */
	public function getPosition() { return $this->position; }

    /**
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return Laminate
     */
    public static function create($id = NULL) { return new self($id); }

}