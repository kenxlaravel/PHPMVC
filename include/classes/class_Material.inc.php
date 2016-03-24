<?php


class Material extends CacheableEntity {

    /**
     * Constant used for two purposes
     * <ol>
     *  <li>Getting the record from the database</li>
     *  <li>FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run</li>
     * </ol>
     */
	const FULL_TABLE_DUMP = "SELECT m.id AS id, m.name AS name, m.thickness AS thickness, m.thickness_display_unit_id,
								m.durability AS durability, m.material_group_id AS material_group_id, m.luminous AS luminous,
								m.service_temperature_range AS service_temperature_range, m.chemical_resistance AS chemical_resistance,
								m.illustration AS illustration, m.active AS active,  mg.reflectivity_id, mg.position as group_position,
								mc.position as category_position
								FROM bs_materials m
								LEFT JOIN bs_material_groups mg ON (mg.id = m.material_group_id)
								LEFT JOIN bs_material_categories mc ON (mg.material_category_id = mc.id)
							 WHERE m.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	* Unique Material id
	* DB column: bs_materials.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Material name
	* DB column: bs_materials.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* Material thickness
	* DB column: bs_materials.thickness.
	*
	* @var float $thickness
	*/
	private $thickness;

	/**
	* Unit object
	* DB column: bs_materials.thickness_display_unit_id.
	*
	* @var Unit $Unit
	*/
	private $ThicknessUnit;

	/**
	* Unique thickness identifier for Unit.
	* DB column: bs_materials.thickness_display_unit_id.
	*
	* @var int $thicknessUnitId.
	*/
	private $thicknessUnitId;

	/**
	* Material deruability
	* DB column: bs_materials.durability.
	*
	* @var string $durability
	*/
	private $durability;

	/**
	* The sevice temperature range of the material
	* DB column: bs_materials.service_temperature_range.
	*
	* @var string $serviceTemperatureRange
	*/
	private $serviceTemperatureRange;

	/**
	* Unique reflectivity identifier
	* DB column: bs_materials.reflectivity_id.
	*
	* @var int $reflectivityId
	*/
	private $reflectivityId;

	/**
	* Reflectivity object
	* DB column: bs_materials.reflectivity_id.
	*
	* @var Reflectivity $Reflectivity
	*/
	private $Reflectivity;

	/**
	* Luminous of the materialc
	* DB column: bs_materials.luminous.
	*
	* @var bool $luminous
	*/
	private $luminous;

	/**
	* Chemical resistance of the material
	* DB column: bs_materials.chemical_resistance.
	*
	* @var bool $chemicalResistance
	*/
	private $chemicalResistance;

	/**
	* DB column: bs_materials.illustration.
	*
	* @var string $illustration
	*/
	private $illustration;

	/**
	* Unique material group identifier
	* DB column: bs_materials.material_group_id.
	*
	* @var int $materialGroupId;
	*/
	private $materialGroupId;

	/**
	* Material Group object
	* DB column: bs_materials.material_group_id.
	*
	* @var MaterialGroup $MaterialGroup
	*/
	private $MaterialGroup;

	/**
	* Whether or not the material is active
	* DB column: bs_materials.active.
	*
	* @var bool $active
	*/
	private $active;

    /**
     * @var int $thicknessDisplayUnitId
     */
    private $thicknessDisplayUnitId;

	/**
	 * @var int $groupPosition
	 */
	private $groupPosition;

	/**
	 * @var int $categoryPosition
	 */
	private $categoryPosition;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material
	 * @throws trigger Error if $id is not set
	 */
	public function __construct($id = NULL) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

			// Set cache object
	  		CacheableEntity::__construct(get_class($this), $this->getId());

			// Attempt to get data from cache
			$data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND m.id = :id  ORDER BY mc.position, mg.position ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }
			}

//			uasort($data, array($this, "sortByPosition"));

			//Set class properties from $data
			$this->setLuminous($data['luminous'])
				 ->setIllustration($data['illustration'])
				 ->setThickness($data['thickness'])
				 ->setThicknessUnitId($data['thickness_display_unit_id'])
				 ->setChemicalResistance($data['chemical_resistance'])
				 ->setMaterialGroupId($data['material_group_id'])
				 ->setName($data['name'])
				 ->setGroupPosition($data['group_position'])
				 ->setCategoryPosition($data['category_position'])
				 ->setReflectivityId($data['reflectivity_id'])
				 ->setActive($data['active'])
				 ->setDurability($data['durability'])
				 ->setServiceTemperatureRange($data['service_temperature_range']);

		} else {

			 // Trigger a notice if an invalid ID was supplied.
	        trigger_error('Cannot load Material properties: \'' . $this->getId() . '\' is not a valid ID number.');

		}

	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return object Return current object
	*/
	private function setId($id) {
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set $luminous value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $luminous bs_installation_methods.luminous
	 * @return object Return current object
	 */
	public function setLuminous($luminous = FALSE) {
		$this->luminous = (bool) $luminous;
		return $this;
	}

	public function setGroupPosition($groupPosition) {
		$this->groupPosition = (int) $groupPosition;
		return $this;
	}

	public function setCategoryPosition($categoryPosition) {
		$this->categoryPosition = (int) $categoryPosition;
		return $this;
	}

	/**
	 * Set $illustration of for the bs_materials table
	 * Trim() any spaces at the start / end of the string
	 *
	 * @param string $illustration bs_materials.illustration
	 * @return Material() Return current object clss
	 */
	public function setIllustration($illustration = '') {
		$this->illustration = !empty($illustration) ? trim($illustration) : NULL;
		return $this;
	}

	/**
	* Set the $thickness and return $this
	*
	* @param float $thickness
	* @return object Return current class object
	*/
	public function setThickness($thickness = NULL) {

		$this->thickness = !is_null($thickness) ? (float) $thickness : NULL;
		return $this;
	}

    /**
     * Set Material thickness display unit id
     *
     * @param int $thicknessDisplayUnitId
     * @return Material()
     */
    public function setThicknessDisplayUnitId($thicknessDisplayUnitId) {

        $this->thicknessDisplayUnitId = isset($thicknessDisplayUnitId) && is_numeric ($thicknessDisplayUnitId) &&
												$thicknessDisplayUnitId > 0 ? (int) $thicknessDisplayUnitId : NULL;
        return $this;
    }

	/**
	 * Create $Unit object
	 * Hold instance of Unit()
	 *
	 * @see class_Unit.inc.php
	 * @param int thicknessUnitId
	 * @return Material()
	 */
	public function setThicknessUnitId($thicknessUnitId) {

		$this->thicknessUnitId = isset($thicknessUnitId) && is_numeric($thicknessUnitId) &&
                                        $thicknessUnitId > 0 ? (int) $thicknessUnitId : NULL;
		return $this;
	}

	/**
	 * Set $chemicalResistance value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $chemicalResistance bs_installation_methods.chemical_resistance
	 * @return object Return current class object
	 */
	public function setChemicalResistance($chemicalResistance = FALSE) {

		$this->chemicalResistance = (bool) $chemicalResistance;
		return $this;
	}

	/**
	 * Create $MaterialGroup object
	 * Hold instance of MaterialGroup()
	 *
	 * @param  int $materialGroupId
	 * @return Material()
	 */
	public function setMaterialGroupId($materialGroupId) {

		$this->materialGroupId = isset($materialGroupId) && is_numeric($materialGroupId) && $materialGroupId > 0 ? (int) $materialGroupId : NULL;
		return $this;
	}

	/**
	 * Set $name of for the bs_materials table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $name bs_materials.name
	 * @return object Return current class object
	 */
	public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	 * Set $reflectivity value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param int $reflectivityId
	 * @return self Return current object
	 */
	public function setReflectivityId($reflectivityId = NULL) {

		$this->reflectivityId = isset($reflectivityId) && is_numeric($reflectivityId) && $reflectivityId > 0 ? (int) $reflectivityId : NULL;
		return $this;
	}

	/**
	 * Set $durability of for the bs_materials table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $durability bs_materials.durability
	 * @return object Return current class object
	 */
	public function setDurability($durability = '') {

		$this->durability = !empty($durability) ? trim($durability) : NULL;
		return $this;
	}

	/**
	 * Set $serviceTemperatureRange of for the bs_materials table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $serviceTemperatureRange bs_materials.service_emperature_range
	 * @return object Return current class object
	 */
	public function setServiceTemperatureRange($serviceTemperatureRange) {

		$this->serviceTemperatureRange = !empty($serviceTemperatureRange) ? trim($serviceTemperatureRange) : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $active bs_installation_methods.active
	 * @return object Return current class object
	 */
	public function setActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
	//Get the Id
	public function getId() { return $this->id; }

	//Get the $name
	public function getName() { return $this->name; }

	//Get the Durability
	public function getDurability() { 

		return !empty($this->durability) ? $this->durability : NULL; }

	//Get the Service Temp Range
	public function getServiceTemperatureRange() { return $this->serviceTemperatureRange; }

	//Get Reflectivity id
	public function getReflectivityId() { return $this->reflectivityId; }

	//Get the Reflectivity
	public function getReflectivity() {

		if ( empty($this->Reflectivity) && !is_null($this->getReflectivityId()) ) {

			$this->Reflectivity = Reflectivity::create($this->getReflectivityId());
		}

		return $this->Reflectivity;
	}

	//Get the Luminous
	public function isLuminous() { return $this->luminous; }

	//Get the Illustration
	public function getIllustration() { return $this->illustration; }

	//Get the Thickness
	public function getThickness() { return $this->thickness; }

	//Get the Chemeicalresistance
	public function getChemicalResistance() { return $this->chemicalResistance; }

	//Get Material Group Id
	public function getMaterialGroupId() { return $this->materialGroupId; }

	//Get the Material Group
	public function getMaterialGroup() {

		if ( !is_null($this->getMaterialGroupId()) ) {

			$this->MaterialGroup = MaterialGroup::create($this->getMaterialGroupId());
		}

		return $this->MaterialGroup;
	}

	//Get Thickness Unit id
	public function getThicknessUnitId(){ return $this->thicknessUnitId; }

	// Get the ThicknessUnit
	public function getThicknessUnit() {

		if ( empty($this->ThicknessUnit) ){

			$this->ThicknessUnit = Unit::create($this->getThicknessUnitId());
		}

		return $this->ThicknessUnit;
	}

    /**
     * @return int $thicknessDisplayUnitId
     */
    public function getThicknessDisplayUnitId() {

        return $this->thicknessDisplayUnitId;
    }

	/**
	 * Sort by material group position
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function sortByGroupPosition ($a, $b) {

		foreach ($a as $index => $s) {

			return $a[$index]->getGroupPosition () > $b[$index]->getGroupPosition ();
		}
	}

	/**
	 * Sort by material category position
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function sortByCategoryPosition ($a, $b) {

		foreach($a as $index => $s) {

			return $a[$index]->getCategoryPosition() > $b[$index]->getCategoryPosition();
		}
	}

	/**
	 * @return int
	 */
	public function getGroupPosition() {

		return $this->groupPosition;
	}

	/**
	 * @return int
	 */
	public function getCategoryPosition() {

		return $this->categoryPosition;
	}

    //Get the Active results
	public function isActive() { return $this->active; }


	//Create an instance of it self (Material()).
	public static function create ($id = NULL) { return new self($id); }

}