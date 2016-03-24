<?php

/**
 * Class PageTypes
 */
class PageType extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, active FROM bs_headers WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	 * Unique Id of language
	 * DB Column: bs_pagetypes.id.
	 * 
	 * @var int $id
	 * */
	private $id;

	/**
	 * This is really just the Name/Department of the page
	 * ex: Grouping, Subcategory, Landing, Category
	 * DB Column: bs_pagetypes.pagetype
	 *
	 * @var string $pagetype;
	 */
	private $pagetype;

	/**
	 * Boolean to show if current object is being rendered or not
	 * DB Column: bs_pagetypes.templated
	 * 
	 * @var int|bool $rendered
	 */
	private $rendered;

	/**
	 * Boolean to show if current object is rendered or not
	 * DB Column: bs_pagetypes.template_secure
	 * 
	 * @var int|bool returns true or false
	 */
	private $templateSecure;

	/**
	 * The name of the files used as a template
	 * DB Column: bs_pagetypes.template_filename
	 * 
	 * @var string Call our php class/file template
	 */
	private $templateFilename;

	/**
	 * Bit that represents a boolean this bit will let us if the current record can be targeted
	 * DB Column: bs_pagetypes.allow_target
	 * 
	 * @var int|bool Returns 1 or 0 (True, False)
	 */
	private $allowTarget;

	/**
	 * Do we need the current user to be logged in in order to access this record
	 * DB Column: bs_pagetypes.requires_login
	 * 
	 * @var int|bool Returns 1 or 0 (True, False)
	 */
	private $requiresLogin;

	/**
	 * Are guest allowed to view this information?
	 * DB Column: bs_pagetypes.disallow_guests
	 * 
	 * @var int|bool Returns 1 or 0 (True, False)
	 */
	private $disallowGuests;

    /**
     * Get the name of the Page Type
     * DB Column: bs_pagetypes.name
     *
     * @var string $name
     */
    private $name;

    /**
     * Check if this type is accessible
     * DB Column: bs_pagetypes.active
     *
     * @var int|bool
     */
    private $active;

    /**
     * Construct
     * @param $id
     */
	public function __construct($id) {

		$this->setId($id);

		if ( !is_null($this->getId()) ) {

            $data = $this->getCache();

            if (empty($data)) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }

            }else{

                // Trigger a notice if an invalid ID was supplied.
                trigger_error('Cannot load Blue Print properties: \'' . $this->getId() . '\' is not a valid ID number.');
            }

            $this->setName($data['name'])
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
	* @return PageTypes()
	*/	
	public function setId($id) {
		$this->id = filter_var(abs($id), FILTER_VALIDATE_INT) ? $id : NULL;
		return $this;
	}

	/**
	* Set the pageType
	* 
	* @param string $pagetype
	* @return PageTypes()
	*/	
	public function setPagetype($pagetype) {
		$this->pagetype = !empty($pagetype) ? trim($pagetype) : NULL;
		return $this;
	}

    /**
     * @param $active
     */
    public function setActive($active) {
        $this->active = (bool) $active;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name) {
        $this->name = !empty($name) ? trim($name) : NULL;
        return $this;
    }

	/**
	* Get and set the value for templated
	* 
	* @param int|boolean $rendered
	* @return PageTypes()
	*/	
	public function setTemplated($rendered) {
		$this->rendered = (bool) $rendered;
		return $this;
	}

	/**
	* Get and set the value for templateSecure
	* 
	* @param int|boolean $templateSecure
	* @return PageTypes()
	*/	
	public function setTemplateSecure($templateSecure) {
		$this->templateSecure = (bool) $templateSecure;
		return $this;
	}

	/**
	* Set the string for templateFilename
	* 
	* @param string $templateFilename
	* @return PageTypes()
	*/	
	public function setTemplateFilename($templateFilename) {
		$this->templateFilename = !empty($templateFilename) ? trim($templateFilename) : NULL;
		return $this;
	}

	/**
	* Set the bit for allowTarget 
	* bit will evaluate to true or false
	* 
	* @param int|bool $allowTarget
	* @return PageTypes()
	*/	
	public function setAllowTarget($allowTarget) {
		$this->allowTarget = (bool) $allowTarget;
		return $this;
	}

	/**
	* Set the bit for requiresLogin 
	* bit will evaluate to true or false
	* 
	* @param int|bool $requiresLogin
	* @return PageTypes()
	*/	
	public function setRequiresLogin($requiresLogin) {
		$this->requiresLogin = (bool) $requiresLogin;
		return $this;
	}

	/**
	* Set the bit for disallowGuests 
	* bit will evaluate to true or false
	* 
	* @param bool $disallowGuests
	* @return PageTypes()
	*/	
	public function setDisallowGuests($disallowGuests) {
		$this->disallowGuests = (bool) $disallowGuests;
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
    public function getPagetype() { return $this->pagetype; }

    /**
     * @return bool|int
     */
    public function getTemplated() { return $this->rendered; }

    /**
     * @return bool|int
     */
    public function getTemplateSecure() { return $this->templateSecure; }

    /**
     * @return string
     */
    public function getTemplateFilename() { return $this->templateFilename; }

    /**
     * @return bool|int
     */
    public function getAllowTarget() { return $this->allowTarget; }

    /**
     * @return bool|int
     */
    public function getRequiresLogin() { return $this->requiresLogin; }

    /**
     * @return bool|int
     */
    public function getDisallowGuests() { return $this->disallowGuests; }

    /**
     * @param null $id
     * @return PageTypes
     */
    public function create($id = NULL) { return new self($id); }
}