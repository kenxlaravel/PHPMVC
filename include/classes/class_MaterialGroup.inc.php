<?php

// by Material

/**
 * Class MaterialGroup
 */
class MaterialGroup extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id AS id, name AS name, position as position, description AS description,
                             material_category_id AS material_category_id, active AS active
                             FROM bs_material_groups WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique reflectivity id
	* DB column: bs_reflectivities.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Material group name
	* DB column: bs_material_groups.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* Material group description
	* DB column: bs_material_groups.description.
	*
	* @var string $description
	*/
	private $description;

	/**
	* Unique material category identifier for material category
	* DB column: bs_material_groups.material_category_id.
	*
	* @var int $materialCategory
	*/
	private $materialCategoryId;

	/**
	* MaterialCategory object
	* DB column: bs_material_groups.rating.
	*
	* @var MaterialCategory $MaterialCategory
	*/
	private $MaterialCategory;


	/**
	 * Positioning of material groups when displayed together, ie material table
	 * Db column: bs_material_groups.position
	 *
	 * @var [int] $position
	 */
	private $position;

	/**
	* Whether or not the material group is active
	* DB column: bs_material_groups.active.
	*
	* @var int|bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_units
	 * @throws [trigger] Error if $id is not set
	 */
	public function __construct($id = NULL) {

		 // Set the ID.
        $this->setId($id);

		if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

			if ( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id ");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }
            }

            //Set up our properties with their respected values
            $this->setName($data['name'])
                 ->setPosition($data['position'])
                 ->setDescription($data['description'])
                 ->setMaterialCategoryId($data['material_category_id'])
                 ->setActive($data['active']);

        } else {

             // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load Material Group properties: \'' . $this->getId() . '\' is not a valid ID number.');
        }
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return MaterialGroup() Return current object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}


    /**
     * @param $position
     * @return MaterialGroup()
     */
	public function setPosition($position) {
		$this->position = isset($position) && is_numeric($position) && $position > 0 ? (int) $position : NULL;
		return $this;
	}


	/**
	* Set $name for the bs_material_groups table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $name bs_material_groups.name
	* @return MaterialGroup() Return current class object
	*/
	public function setName($name = '') {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set $description for the bs_material_groups table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $description bs_material_groups.description
	* @return MaterialGroup() Return current class object
	*/
	public function setDescription($description = '') {
		$this->description = !empty($description) ? trim($description) : NULL;
		return $this;
	}

	/**
	* Create $MaterialCategory object
	* Hold instance of MaterialGroup()
	*
	* @see class_MaterialCategory.inc.php
	* @param NULL
	* @return MaterialGroup() Return current class object
	*/
	public function setMaterialCategoryId($materialCategoryId = NULL) {
		$this->materialCategoryId = isset($materialCategoryId) && is_numeric($materialCategoryId) &&
                                            $materialCategoryId > 0 ? (int) $materialCategoryId : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $active bs_material_groups.active
	 * @return MaterialGroup() Return current class object
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
    public function getName () { return $this->name; }

    /**
     * @return string
     */
    public function getDescription () { return $this->description; }

    /**
     * @return int
     */
    public function getMaterialCategoryId() { return $this->materialCategoryId; }

    /**
     * @return MaterialCategory()
     */
    public function getMaterialCategory() {

		if ( empty($this->MaterialCategory) ) {

			$this->MaterialCategory = MaterialCategory::create($this->getMaterialCategoryId());
		}

		return $this->MaterialCategory;
	}

	/**
	 * Sort by position
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public function sortByPosition($a, $b) {

		return $a['position'] > $b['position'];
	}

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * Call MaterialGroup with out creating an instance.
     *
     * @param null $id
     * @return MaterialGroup()
     */
    public static function create ($id = NULL) { return new self($id); }
}