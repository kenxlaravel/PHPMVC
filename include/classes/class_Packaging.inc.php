<?php

/**
 * Class Packaging
 */
class Packaging extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, plural_name, short_name, short_plural_name, active
                             FROM `bs_packagings` WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique packaging id
	* DB column: bs_packagings.id.
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Packaging name
	* DB column: bs_packagings.name.
	* 
	* @var string $name
	*/
	private $name;

	/**
	* Plural name of a packaging
	* DB column: bs_packagings.plural_name.
	* 
	* @var string $pluralName
	*/
	private $pluralName;

	/**
	* Short name of a packaging
	* DB column: bs_packagings.short_name.
	* 
	* @var string $shortName
	*/
	private $shortName;

	/**
	* Short plural name of a packaging
	* DB column: bs_packagings.short_plural_name.
	* 
	* @var string $shortPluralName
	*/
	private $shortPluralName;

	/**
	* Whether or not the packaging is active
	* DB column: bs_packagings.active.
	* 
	* @var int|bool
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_units
	 * @throws Error if $id is not set
	 */
	public function __construct($id){

		$this->setId($id);

		if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }
            }

			$this->setName($data['name'])
				 ->setPluralName($data['plural_name'])
				 ->setShortName($data['short_name'])
				 ->setShortPluralName($data['short_plural_name'])
				 ->setActive($data['active']);
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return current object
	*
	* @param int $id
	* @return Packaging() Return current object
	*/
	public function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	* Set $name for the bs_packaging table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $name bs_packaging.name
	* @return Packaging() Return current class object
	*/
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set $pluralName for the bs_packaging table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $pluralName bs_packaging.plural_name
	* @return Packaging() Return current class object
	*/
	public function setPluralName($pluralName) {
		$this->pluralName = !empty($pluralName) ? trim($pluralName) : NULL;
		return $this;
	}

	/**
	* Set $shortName for the bs_packaging table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $shortName bs_packaging.short_name
	* @return Packaging() Return current class object
	*/
	public function setShortName($shortName) {

		$this->shortName = !empty($shortName) ? trim($shortName) : NULL;
		return $this;
	}

	/**
	* Set $shortPluralName for the bs_packaging table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $shortPluralName bs_packaging.short_plural_name
	* @return Packaging() Return current class object
	*/
	public function setShortPluralName($shortPluralName) {

		$this->shortPluralName = !empty($shortPluralName) ? trim($shortPluralName) : NULL;
		return $this;
	}

    /**
     * @param $active
     * @return Packaging();
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
     * @return string
     */
    public function getPluralName() { return $this->pluralName; }

    /**
     * @return string
     */
    public function getShortName() { return $this->shortName; }

    /**
     * @return string
     */
    public function getShortPluralName() { return $this->shortPluralName; }

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return Packaging
     */
    public static function create($id = NULL) { return new self($id);}
}

