<?php
// by product


/**
 * Class Header
 */
class Header extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, active FROM bs_headers WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

    /**
	 * Unique ID of Header
	 * DB column: bs_headers.id.
	 * 
	 * @var int $id
	 */
	private $id;

	/**
	 * Name of header
	 * DB column: bs_headers.name.
	 * 
	 * @var string $name
	 */
	private $name;

	/**
	 * Translation object retrieved from translation table
	 * DB column: bs_headers_translation.*
	 * 
	 * @var Header() HeaderTransl()
	 */
	private $translation;

	/**
	 * Whether or not header is active
	 * DB column: bs_headers.active.
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

        $data = $this->getCache($this->getId());

        if( empty($data) ) {

			if( !is_null($this->getId()) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(":id", $this->getId(), PDO::PARAM_INT);

			    if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($id, $data);
                }

			} else {

			 	// Trigger a notice if an invalid ID was supplied.
		        trigger_error('Cannot load Header properties: \'' . $this->getId() . '\' is not a valid ID number.');

			}
		}

		$this->setName($data['name'])
			 ->setActive($data['active']);

	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set privately the $id and return $this
	* 
	* @param int $id
	* @return Header() Return current class
	*/
	private function setId($id) {
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	* Set the $name and return current Header()
	* 
	* @param string $name
	* @return Header() Return current class
	*/	
	public function setName($name = '') {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set the $active and return current object
	* 
	* @param int|bool $active
	* @return Header() Return current class
	*/	
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters 
	**************************************************/
    /**
     * @return int $id;
     */
	public function getId() { return $this->id; }

    /**
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * @return int|bool $active
     */
    public function isActive() { return $this->active; }

    /**
     * @param int $id
     * @return Header() instance of self
     */
    public static function create($id = NULL) { return new self($id); }

}