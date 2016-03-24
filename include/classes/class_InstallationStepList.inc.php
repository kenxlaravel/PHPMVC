<?php

/**
 * Class InstallationStepList
 */

class InstallationStepList extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id, installation_method_id, title, position, active
						     FROM bs_installation_method_step_lists WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique identifier for installation list of steps
	* DB column: bs_installation_method_step_lists.id
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Title of for list
	* DB column: bs_installation_method_step_lists.title
	* 
	* @var string $title
	*/
	private $title;

	/**
	* An array of InstallationStep objects
	* DB table: bs_installation_method_steps
	* 
	* @var array $installationSteps
	*/
	private $installationSteps;

	/**
	 * An array that will hold all the 
	 * Installation Step Ids
	 *
	 * @var array $installationStepIds
	 */
	private $installationStepIds;

	/**
	* Position determineing order
	* DB column: bs_installation_method_step_lists.position
	* 
	* @var int $position
	*/
	private $position;

	/**
	* Whether or not the step is active
	* DB column: bs_installation_method_step_lists.active
	* 
	* @var int|bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_installation_methods
	 */
	public function __construct($id) {

		$this->setId($id);

		if ( !is_null($this->getId()) ) {

//            CacheableEntity::__construct(get_class($this), $this->getId());
//            $data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND installation_method_id = :id ");
				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					while ($data = $query->fetch(PDO::FETCH_ASSOC)) {

						$this->setTitle($data['title'])
							 ->setPosition($data['position'])
							 ->setActive($data['active'])
							 ->setInstallationSteps($data['id']);
					}
				}
			}

		} else {
			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
		}

	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set the $id and return InstallationStepList
	* 
	* @param int $id
	* @return InstallationStepList() Return current class object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	* Set the $title and return InstallationStepList
	* 
	* @param string $title
	* @return InstallationStepList() Return current class object
	*/
	public function setTitle($title) {

		$this->title[] = !empty($title) ? trim($title) : NULL;
		return $this;
	}

	/**
	* Set the $installationStepIds and return InstallationStepList
	* 
	* @param array $installationStepIds
	* @return InstallationStepList() Return current class object
	*/
	public function setInstallationSteps($installationStepIds) {

		$this->installationStepIds[] = !empty($installationStepIds) ? $installationStepIds : NULL;

		return $this;
	}

	/**
	* Set the $position and return InstallationStepList
	* 
	* @param int $position
	* @return InstallationStepList() Return current class object
	*/
	public function setPosition($position) {

		$this->position = isset($position) && is_numeric($position) && $position > 0 ? (int) $position : NULL;
		return $this;
	}

	/**
	* Set the $active and return InstallationStepList
	* 
	* @param bool $active
	* @return InstallationStepList() Return current class object
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
    public function getTitle() { return $this->title; }

    /**
     * @return array
     */
    public function getInstallationSteps() {
		
		foreach( $this->installationStepIds as $id ) {
		
			$this->installationSteps[$id] = InstallationStep::create($id);
			
			if( !isset($this->installationSteps[$id]) ) $this->installationSteps[$id] = NULL;
		}

		return $this->installationSteps; 
	}

    /**
     * @return int
     */
    public function getPosition() { return $this->position; }

    /**
     * @return bool
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return InstallationStepList
     */
    public static function create($id = NULL) { return new self($id); }
}
