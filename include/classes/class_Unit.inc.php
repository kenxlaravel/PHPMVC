<?php

// TODO: Finish this class.

// by Material

class Unit extends CacheableEntity {

	const FULL_TABLE_DUMP = "SELECT id, name, plural_name, active FROM bs_units WHERE active = 1 ";


	/**
	 * Extra query parameter used with $FULL_TABLE_DUMP
	 */
	const ADDITIONAL_CLAUSES = " GROUP BY id ";


	/**
	* Unique Unit id
	* DB column: bs_units.id.
	*
	* @var [int] $id
	*/
	private $id;

	/**
	* Material unit name
	* DB column: bs_units.name.
	*
	* @var [string] $name
	*/
	private $name;

	/**
	* Material unit plural name
	* DB column: bs_units.plural_name.
	*
	* @var [string] $pluralName
	*/
	private $pluralName;

	/**
	* Material unit inch ratio
	* DB column: bs_materials.inch_ratio.
	*
	* @var [float] $inchRatio
	*/
	private $ratio;

	/**
	* Whether or not the material unit is active
	* DB column: bs_units.active.
	*
	* @var [bool] $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param [int] $id Id used to query records from bs_units
	 * @throws [trigger] Error if $id is not set
	 */
	public function __construct($id = NULL) {

		 // Set the ID.
        $this->setId($id);

			if ( empty($data) ) {

				if (!is_null($this->getId())) {

					$query = Connection::getHandle()
						->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

					$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

					if( $query->execute() ) {

						$data = $query->fetch(PDO::FETCH_ASSOC);

						$this->storeCache($data); //Cache data so we don't have to retrieve from database again
					}

					$this->setName($data['name'])
						->setPluralName($data['plural_name'])
						->setActive($data['active']);

				} else {

					// Trigger a notice if an invalid ID was supplied.
					trigger_error('Cannot load Unit properties: \'' . $id . '\' is not a valid ID number.');

				}
			}

		}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param [int] $id
	* @return [object] Return current object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}


	/**
	 * Set $name of for the bs_units table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param [string] $name bs_units.name
	 * @return [object] Return current class object
	 */
	public function setName($name = '') {
		$this->name = trim($name);
		return $this;
	}

	/**
	 * Set the $plural name value of the bs_units table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param [string] $pluralName bs_units.plural_name
	 * @return [self] Return current object
	 */
	public function setPluralName($pluralName = '') {
		$this->pluralName = trim ($pluralName);
		return $this;
	}

	/**
	* Set the $inch ratio of the bs_units table
	*
	* @param [float] $inchRatio
	* @return [object] Return current class object
	*/
	public function setRatio($ratio = NULL) {
		$this->ratio = isset($ratio) && is_numeric($ratio) &&
							 	 $ratio > 0 ? (float) $ratio : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param [bool] $active bs_installation_methods.active
	 * @return [object] Return current class object
	 */
	public function setActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
	//Get the Id
	private function getID() { return $this->id; }

	//Get the Name
	public function getName() { return $this->name; }

	//Get the Plural Name
	public function getPluralName() { return $this->pluralName; }

	//Get the Inch Ratio
	public function getRatio() { return $this->ratio; }

	//Get the Active results
	public function isActive() { return $this->active; }

	//Create an instance of it self (Material()).
	public static function create ($id = NULL) { return new self($id); }

}
