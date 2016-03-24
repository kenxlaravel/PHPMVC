<?php


// by sku | ComplianceGroup

/**
 * Class Compliance
 */
class Compliance extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, description, product_property, active, compliance_group_id, link, product_grid_display
                             FROM bs_compliances WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	* Unique compliance id
	* DB column: bs_compliances.id.
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Compliance name
	* DB column: bs_compliances.name.
	* 
	* @var string $name
	*/
	private $name;

	/**
	* Compliance description
	* DB column: bs_compliances.description.
	* 
	* @var string $description
	*/
	private $description;

	/**
	* Caption control of compliance on product image
	* DB column: bs_compliances.product_property.
	* 
	* @var bool $productProperty
	*/
	private $productProperty;

	/**
	* URL for compliances
	* DB column: bs_compliances.link
	* 
	* @var string $link
	*/
	private $link; 

	/**
	* $Tooltip will hold an object of Tooltip
	* 
	* @see class_ToolTip.inc.php
	* @var Tooltip $Tooltip
	*/
	private $Tooltip;

	/**
	* Whether or not compliance is active
	* DB column: bs_compliances.active.
	* 
	* @var bool $active
	*/
	private $active;

    /**
     * Whether or not to display this compliance on the product grid
     * DB column: bs_compliances.product_grid_display.
     *
     * @var bool $product_grid_display
     */
    private $productGridDisplay;

   /**
	* ComplianceGroup object
	* DB column: bs_compliances.compliance_group_id.
	* 
	* @var object $ComplianceGroup
	* @see class_ComplianceGroup.inc.php
	**/
	private $ComplianceGroup;


	/**
	 *
	 * @var int $complianceGroupId
	 */
	private $complianceGroupId;


	/**
	 * Construct will handle setting calling
	 * the setters methods
	 * 
	 * @param int $id Id used to query records
	 * @throws trigger Error if $id is not set
	 */
	public function __construct($id = NULL) {

		$data = array();

        $this->setId($id);

		if( !is_null($this->getId()) ) {

			CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id");
				$query->bindParam(':id', $this->id, PDO::PARAM_INT);

				if( $query->execute() ) {

					$data = $query->fetch(PDO::FETCH_ASSOC);
				}

				$this->setName($data['name'])
					 ->setLink($data['link'])
					 ->setActive($data['active'])
					 ->setDescription($data['description'])
					 ->setproductProperty($data['product_property'])
                     ->setProductGridDisplay($data['product_grid_display'])
					 ->setComplianceGroupIds($data['compliance_group_id']);
			}

		}else{

			//Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load Compliance properties: \'' . $id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters 
	**************************************************/
    /**
     * Check and set the id for the record
     *
     * @param int $id
     * @return $this
     */
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

	/**
	* Set privately the $ComplianceGroupID and return Compliance()
    *
	* @param int $complianceGroupid
	* @return Compliance()
	*/	
	public function setComplianceGroupIds($complianceGroupid = NULL) {

		$this->complianceGroupId = isset($complianceGroupid) && is_numeric($complianceGroupid)
											&& $complianceGroupid > 0 ? (int) $complianceGroupid : NULL;
		return $this;
	}

	/**
	* Set the $productProperty and return Compliance()
	* 
	* @param int $productProperty
	* @return Compliance Return current class object
	*/	
	public function setProductProperty($productProperty) {

		$this->productProperty = isset($productProperty) && is_numeric($productProperty) &&
												$productProperty > 0 ? (int) $productProperty : NULL;

		return $this;
	}

	/**
	* Set the $tooltip and return $this
	* 
	* @param int $id
	* @see class_Tooltip.inc.php
	* @return object Return current class object
	*/	
	public function setTooltip($id) {
	//	$this->Tooltip = new Tooltip($id, $tableName);
		return $this;
	}

	/**
	* Set the $description and return $this
	* 
	* @param string $description
	* @return Compliance() Return current class object
	*/	
	public function setDescription($description = '') {

		$this->description = !empty($description) ? trim($description) : NULL;
		return $this;
	}

	/**
	* Set the $link and return current object
	* 
	* @param string $link
	* @return Compliance() Return current class object
	*/	
	public function setLink($link) {

		$this->link = !empty($link) ? trim($link) : NULL;
		return $this;
	}

	/**
	* Set the $name and return Compliance()
	* 
	* @param string $name
	* @return Compliance() Return current class object
	*/	
	public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set the $active and return Compliance()
	* 
	* @param int|bool $active
	* @return Compliance() Return current class object
	*/	
	public function setActive($active) {

		$this->active = (bool) $active;
		return $this;
	}

    /**
     * Set the $productGridDisplay and return Compliance()
     *
     * @param int|bool $productGridDisplay
     * @return Compliance() Return current class object
     */
    public function setProductGridDisplay($productGridDisplay) {

        $this->productGridDisplay = (bool) $productGridDisplay;
        return $this;
    }

	/*************************************************
	* Start Getters                                  *
	**************************************************/
    /**
     * Get the records id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the records compliance group object
     *
     * @return object $ComplianceGroup
     */
    public function getComplianceGroup() {

		if( !is_null($this->getComplianceGroupId()) ) {

			$this->ComplianceGroup = ComplianceGroup::create($this->getComplianceGroupId());
		}

		return $this->ComplianceGroup;
	}


	public function getComplianceGroupId() {

		return $this->complianceGroupId;
	}

    /**
     * Get the record productProperty
     *
     * @return int|bool $productProperty
     */
    public function isProductProperty() { return $this->productProperty; }

    /**
     * Get the record productProperty
     *
     * @return int|bool $productProperty
     */
    public function getProductGridDisplay() { return $this->productGridDisplay; }

    /**
     * Get the record Tooltip object
     *
     * @return object $Tooltip
     */
    public function getTooltip() { return $this->Tooltip; }

    /**
     * Show true or false if the record is active
     *
     * @return int|bool $active
     */
    public function isActive() { return $this->active; }

    /**
     * Get the record description
     *
     * @return string $description
     */
    public function getDescription() { return $this->description; }

    /**
     * Get the record link
     *
     * @return string $link
     */
    public function getLink() { return $this->link; }

    /**
     * Get the record name
     *
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * Create an instance of this class statically
     *
     * @param null $id
     * @return Compliance
     */
    public static function create($id = NULL) { return new self($id); }

}