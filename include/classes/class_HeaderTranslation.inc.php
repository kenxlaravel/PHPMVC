<?php

/**
 * Class HeaderTransl
 */
class HeaderTranslation extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, language_id FROM bs_header_translations WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	 * DB column: bs_header_translations.id
	 * 
	 * @var int $id
	 */
	private $id;

	/**
	 * DB column: bs_header_translations.language_id
	 * 
	 * @var int $languageId
	 */
	private $languageId;

	/**
	 * DB column: bs_header_translations.header_id
	 * 
	 * @var int $headerId
	 */
	private $headerId;

	/**
	 * DB column: bs_header_translations.active
	 * 
	 * @var int|bool $active
	 */
	private $active;

	/**
	 * Our constructor will handle all of our properties
	 * settings and validations
	 * 
	 * @param int $id
	 */
	public function __construct($id) {

        $this->setId($id);

        $data = $this->getCache();

        if (empty($data)) {

            if (!is_null($this->getId())) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }
            } else {

                // Trigger a notice if an invalid ID was supplied.
                trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');
            }

            $this->setHeaderId($data['header_id'])
                 ->setLanguage($data['language_id'])
                 ->setActive($data['active']);
        }
    }

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set privately the $id and return $this
	* 
	* @param int $id
	* @return HeaderTransl()
	*/
    private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

	/**
	* Set the $languageId and return $this
	* 
	* @param int $languageId
	* @return HeaderTransl()
	*/	
	public function setLanguage($languageId) {
		$this->languageId = (int) $languageId;
		return $this;
	}

	/**
	* Set privately the $headerId and return $this
	* 
	* @param int $headerId
	* @return HeaderTransl()
	*/	
	public function setHeaderId($headerId) {
		$this->headerId = $headerId;
		return $this;
	}

	/**
	* Set the $active and return $this
	* 
	* @param int|bool $active
	* @return HeaderTransl()
	*/	
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters 
	**************************************************/
    /**
     * @return int $id
     */
	public function getId() { return $this->id; }

    /**
     * @return int $languageId
     */
    public function getLanguage() { return $this->languageId; }

    /**
     * @return int $headerId
     */
    public function getHeaderId() { return $this->headerId; }

    /**
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return HeaderTransl
     */
    public function create($id = NULL) { return new self($id); }

}