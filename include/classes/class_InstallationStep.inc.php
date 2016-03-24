<?php

/**
 * Class InstallationStep
 */
class InstallationStep extends CacheableEntity {

	const IMAGE_PATH = '';

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     *
     */
	const FULL_TABLE_DUMP = "SELECT id, installation_method_step_list_id, image, description, position, active
						     FROM bs_installation_method_steps WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique identifier for installation step
	* Db column: bs_installation_method_steps.id
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Image associated with step
	* Db column: bs_installation_method_steps.image
	* 
	* @var string $image
	*/
	private $image;

	/**
	* Text describing step
	* Db column: bs_installation_method_steps.description
	* 
	* @var string $description
	*/
	private $description;

	/**
	* Position determining order for steps
	* Db column: bs_installation_method_steps.position
	* 
	* @var int $position
	*/
	private $position;

	/**
	* Whether or not the stepis active
	* Db column: bs_installation_method_steps.active
	* 
	* @var bool $active
	*/
	private $active;

	/**
	 * @var int $installationMethodStepListId
	 */
	private $installationMethodStepListId;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id
	 */
	public function __construct($id) {

		$this->setId($id);

		if ( !is_null($this->getId()) ) {

			CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND installation_method_step_list_id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					while ($data = $query->fetch(PDO::FETCH_ASSOC)) {

						$this->setInstallationMethodStepListId($data['installation_method_steps_list_id'])
							 ->setDescription($data['description'])
							 ->setPosition($data['position'])
							 ->setImage($data['image'])
							 ->setActive($data['active']);
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
	* Set the $id and return $this
	* DB column: installation_method_steps.id
	* 
	* @param int $id
	* @return InstallationStep() Return current class object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	* Set the $image and return InstallationStep
	* DB column: installation_method_steps.image
	* 
	* @param string $image
	* @return InstallationStep() Return current class object
	*/
	public function setImage($image) {

		$this->image = !empty($image) ? trim(self::IMAGE_PATH.$image) : NULL;
		return $this;
	}

	/**
	* Set the $description and return InstallationStep
	* DB column: installation_method_steps.description
	* 
	* @param string $description
	* @return InstallationStep() Return current class object
	*/
	public function setDescription($description) {

		$this->description = !empty($description) ? trim($description) : NULL;
		return $this;
	}

	/**
	* Set the $position and return InstallationStep
	* DB column: installation_method_steps.position
	* 
	* @param int $position
	* @return InstallationStep() Return current class object
	*/
	public function setPosition($position) {

		$this->position = (int) $position;
		return $this;
	}

    /**
     * @param $methodStepListId
     * @return InstallationStep()
     */
    public function setInstallationMethodStepListId($methodStepListId) {

		$this->installationMethodStepListId = $methodStepListId;
		return $this;
	}

	/**
	* Set the $active and return InstallationStep
	* DB column: installation_method_steps.active
	* 
	* @param string $active
	* @return InstallationStep()
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
    public function getImage() { return $this->image; }

    /**
     * @return string
     */
    public function getDescription() { return $this->description; }

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
     * @return InstallationStep
     */
    public static function create($id = NULL) { return new self($id); }

}