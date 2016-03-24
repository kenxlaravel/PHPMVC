<?php

/**
 * Class ChangeFrequency
 */
class ChangeFrequency extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, active FROM bs_change_frequencies WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	* Unique change frequency identifier
	* DB column : bs_change_frequencies.id
	* @var int $id
	*/
	private $id;

	/**
	* Name of change of frequency
	* DB column : bs_change_frequencies.name
	* @var string $name
	*/
	private $name;

	/**
	* Whether or not the change frequency is active
	* DB column : bs_change_frequencies.active
	* @var int|bool $active
	*/
	private $active;

    /**
     * @param $id
     */
	public function __construct($id) {

		$this->setId($id);

		if( empty($data) ) {

			if( !is_null($this->getId()) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                }

			} else {

				// Trigger a notice if an invalid ID was supplied.
	            trigger_error('Cannot load page url properties: \'' . $this->getId() . '\' is not a valid ID number.');
			}
		}

		$this->setName($data['name'])->setActive($data['active']);
	}

	/**
	* Set the $active and return $this
	*
	* @param  int $id
	* @return ChangeFrequency()
	*/
	protected function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set property name and return self class
	 *
	 * @param 	string 	$name
	 * @return 	ChangeFrequency() $this
	 */
	public function setName($name) {
		$this->name = !empty($name) ? trim(mb_strtolower($name)) : NULL;
		return $this;
	}

	/**
	 * Set the $active and return $this
	 *
	 * @param  int|bool $active
	 * @return ChangeFrequency() $this
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
     * @param null $id
     * @return ChangeFrequency
     */
    public static function create($id = NULL) { return new self($id); }
}

