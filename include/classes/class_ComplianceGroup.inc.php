<?php


// by sku
class ComplianceGroup extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name FROM bs_compliance_groups ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

    /**
	* Unique compliance group identifier
	* DB column: bs_compliance_groups.id
	*
	* @var int $id
	*/
	private $id;

	/**
	* Name of compliance group
	* DB column: bs_compliance_groups.name
	*
	* @var string $name
	*/
	public $name;

	/**
	* Whether or not the compliance group is active
	* DB column: bs_compliance_groups.active
	*
	* @var int|bool $active
	*/
	public $active;

	/**
	 * Constructor will handle all the setters
	 * and the query needed for this class to run
	 *
	 * @param int $id
	 */
	public function __construct($id) {

		 // Set the ID.
        $this->setId($id);

		// Attempt to get data from cache
        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." WHERE id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    //This record was not cached, lets create a cache file for it
                    $this->storeCache($id, $data);
                }
            }

            $this->setName($data['name']);

        }else{

             // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load ComplianceGroup properties: \'' . $id . '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param  int $id Unique id used to search for record
	* @return ComplianceGroup() Return current class object
	*/
	private function setId($id = NULL) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	* Set the $name and return $this
	*
	* @param string $name
	* @return ComplianceGroup() Return current class object
	*/
	public function setName($name = '') {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}


	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the Compliance Group id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the Compliance Group name
     *
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * Create an instance of ComplianceGroup in this realm..
     *
     * @param null $id
     * @return ComplianceGroup
     */
    public static function create($id = NULL) { return new self($id); }

}