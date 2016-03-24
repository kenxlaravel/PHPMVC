<?php

/**
 * Class Language
 */
class Language extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id, name, count, active from bs_languages WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique Id of language
	* DB Column: bs_languages.id.
	* 
	* @var int $id
	*/
	private $id;

	/**
	* What language it is
	* DB Column: bs_languages.name.
	* 
	* @var string $name
	*/
	private $name;

	/**
	* The number of actual languages or dialects present inside of this row in the DB
	* DB Column: bs_languages.count.
	* 
	* @var int $count
	*/
	private $count;

	/**
	* Whether the language is active or not
	* DB Column: bs_languages.active.
	* 
	* @var bool $active
	*/
	private $active;

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

		if( !is_null($this->getID()) ) {

			// Set cache object
			CacheableEntity::__construct(get_class($this), $this->getId());

			// Attempt to get data from cache
			$data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id");

				$query->bindParam(':id', $this->getID(), PDO::PARAM_INT);

				if( $query->execute() ) {
					$data = $query->fetch(PDO::FETCH_ASSOC);
				}
			}

			$this->setName($data['name'])->setCount($data['count'])->setActive($data['active']);

		}else{
			 // Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load Language properties: \'' . $id . '\' is not a valid ID number.');
		}

	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set privately the $id and Language object
	* 
	* @param int $id
	* @return object Language()
	*/
	private function setId($id) {
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	* Set the $name and Language object
	* 
	* @param string $name
	* @return object Language()
	*/	
	public function setName($name = '') {

		$this->name = !empty($name) ? trim($name) : NULL;
		
		return $this;
	}

	/**
	* Set the $count and Language object
	* 
	* @param int $count
	* @return object Language()
	*/	
	public function setCount($count) {
		
		$this->count = isset($count) && is_numeric($count) && $count > 0 ? (int) $count : NULL;
		return $this;
	}

	/**
	* Set the $active and return Language object
	* 
	* @param int|bool $active
	* @return object Language()
	*/	
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters 
	**************************************************/
    /**
     * Get the record id
     *
     * @return int $id
     */
	public function getID() { return $this->id; }

    /**
     * Get the name of the record
     *
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * Get the count for the current record (This is not a total count of records)
     *
     * @return int $count
     */
    public function getCount() { return $this->count; }

    /**
     * If record is active return true or false.
     *
     * @return int|bool $active
     */
    public function isActive() { return $this->active; }

    /**
     * Create an instance of this class statically
     *
     * @param null $id
     * @return Language
     */
    public static function create($id = NULL) { return new self($id); }
}