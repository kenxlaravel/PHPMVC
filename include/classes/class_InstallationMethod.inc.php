<?php


class InstallationMethod extends CacheableEntity {

	const IMAGE_PATH  = '';
	const VIDEO_PATH  = '';

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     *
     */
	const FULL_TABLE_DUMP = "SELECT id, title, intro, accessory_recommendations_title, people_required, image, video, active
						     FROM bs_installation_methods WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique intallation method id
	* DB column: bs_installation_methods.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Title of the installation method
	* DB column: bs_installation_methods.title.
	*
	* @var string $title
	*/
	private $title;

	/**
	* Intro for the installation method
	* DB column: bs_installation_methods.intro.
	*
	* @var string $intro
	*/
	private $intro;

	/**
	* Number of people required to install product
	* DB column: bs_installation_methods.people_required.
	*
	* @var string $peopleRequired
	*/
	private $peopleRequired;

	/**
	* An array of all relevant installation accessory objects
	* DB table: bs_installation_accessories
	*
	* @var array $installationAccessories
	*/
	private $installationAccessories;

	/**
	* Array of InstallationStepLists
	* DB table: bs_installation_step_lists
	*
	* @var array $installationStepLists
	*/
	private $installationStepLists;

	/**
	* Array of InstallationRequirement objects
	* DB table: bs_installation_requirements
	*
	* @var array $installationRequirements
	*/
	private $installationRequirements;

	/**
	* Holds a string value
	* DB table: bs_installation_methods.accessory_recommendations_title
	*
	* @var string $accessoryRecommendationsTitle
	*/
	private $accessoryRecommendationsTitle;

	/**
	* Image file name
	* DB column: bs_installation_methods.image
	*
	* @var string $image
	*/
	private $image;

	/**
	* Video file name
	* DB column: bs_installation_methods.video
	*
	* @var string $video
	*/
	private $video;

	/**
	* Whether or not the installation method is active
	* DB column: bs_installation_methods.active.
	*
	* @var bool $active
	*/
	private $active;

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

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					while ($data = $query->fetch(PDO::FETCH_ASSOC)) {

						$this->setTitle($data['title'])
							 ->setIntro($data['intro'])
							 ->setImage($data['image'])
							 ->setVideo($data['video'])
							 ->setInstallationStepLists($data['id'])
							 ->setPeopleRequired($data['people_required'])
							 ->setAccessoryRecommendations($data['accessory_recommendations_title']);
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
	 * Set privately the $id and return InstallationAccessory
	 *
	 * @param int $id
	 * @return InstallationAccessory()
	 */
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set title of for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $title Title from bs_installation_methods.title
	 * @return InstallationAccessory
	 */
	public function setTitle($title) {
		
		$this->title = !empty($title) ? trim($title) : NULL;
		return $this;
	}

	/**
	 * Set Accessory title for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $accessoryRecommendations from bs_installation_methods.accessory_recommendations_title
	 * @return InstallationAccessory
	 */
	public function setAccessoryRecommendations($accessoryRecommendations) {
		
		$this->accessoryRecommendationsTitle = !empty($accessoryRecommendations) ? trim($accessoryRecommendations) : NULL;
		return $this;
	}

	/**
	 * Set intro of for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $intro bs_installation_methods.intro
	 * @return InstallationAccessory
	 */
	public function setIntro($intro) {

		$this->intro = !empty($intro) ? trim($intro) : NULL;
		return $this;
	}

	/**
	 * Set $peopleRequired value
	 * Typecast (int) conversion to int
	 *
	 * @param int $peopleRequired bs_installation_methods.people_required
	 * @return InstallationAccessory
	 */
	public function setPeopleRequired($peopleRequired) {
		
		$this->peopleRequired = isset($peopleRequired) && is_numeric($peopleRequired) && 
									  $peopleRequired > 0 ? (int) $peopleRequired : NULL;
		return $this;
	}

	/**
	 * Create $installation_accessory_ids object
	 *
	 * @see class_InstallationAccessory.inc.php
	 * @param int $installationAccessoryIds Id's returned from Instance IA ()
	 * @return InstallationAccessory
	 */
	public function setInstallationAccessories($installationAccessoryIds = NULL) {

		$this->installationAccessories = NULL;

		if( isset($installationAccessoryIds) ) {

			foreach( $installationAccessoryIds AS $id ) {
			
				$this->installationAccessories[$id] = new InstallationAccessory($id);
			}
		}

		return $this;
	}

	/**
	 * Create $installationStepListIds object
	 * Hold instance of InstallationStepList()
	 *
	 * @see class_InstallationStepList.inc.php
	 * @param int $installationStepListIds
	 * @return InstallationAccessory
	 */
	public function setInstallationStepLists ($installationStepListIds = NULL) {

		$this->installationStepLists = NULL;

		if( isset($installationStepListIds) ) {

			//foreach($installationStepListIds AS $id) {
				
				$this->installationStepLists[$installationStepListIds] = InstallationStepList::create($installationStepListIds);
			//}
		}

		return $this;
	}

	/**
	 * Create $installationRequirementIds array of object
	 * Hold instance of InstallationRequirement()
	 *
	 * @see class_InstallationRequirement.inc.php
     * @param array $installationRequirementIds
     * @return InstallationAccessory
	 */
	public function setInstallationRequirements($installationRequirementIds = array()) {
		
		$this->installationRequirements = NULL;

		if( isset($installationRequirementIds) ) {

			foreach($installationRequirementIds AS $id) {
			
				$this->installationRequirements[$id] = InstallationRequirement::create($id);
			}
		}

		return $this;
	}

	/**
	 * Set $image of for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $image bs_installation_methods.image
	 * @return object
	 */
	public function setImage($image) {
		
		$this->image = !empty($image) ? trim(self::IMAGE_PATH.$image) : NULL;
		
		return $this;
	}

	/**
	 * Set $video of for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 *
	 * @param string $video bs_installation_methods.video
	 * @return InstallationMethod()
	 */
	public function setVideo($video) {

		$this->video = !empty($video) ? trim(self::VIDEO_PATH.$video) : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $active bs_installation_methods.active
	 * @return InstallationMethod()
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
     * @return string
     */
    public function getIntro() { return $this->intro; }

    /**
     * @return string
     */
    public function getPeopleRequired() { return $this->peopleRequired; }

    /**
     * @return array
     */
    public function getInstallationAccessories() { return $this->installationAccessories; }

    /**
     * @return array
     */
    public function getInstallationStepLists() {

		if( isset($installationStepListIds) ) {

			foreach($installationStepListIds as $id) {
				
				$this->installationStepLists[$id] = InstallationStepList::create($id);
			}
		}

		return $this->installationStepLists; 
	}

    /**
     * @return array
     */
    public function getInstallationRequirements() { return $this->installationRequirements; }

    /**
     * @return string
     */
    public function getImage() { return $this->image; }

    /**
     * @return string
     */
    public function getVideo() { return $this->video; }

    /**
     * @return int|bool
     */
    public function isActive() { return $this->active; }

    /**
     * @return null|string
     */
    public function getPreviewMedia() {

		if( $videoPath = $this->getVideo() ){

			return $videoPath;

		} elseif( $imagePath = $this->getImage() ) {

			return $imagePath;

		}

		return NULL;
	}

    /**
     * @param null $id
     * @return InstallationMethod
     */
    public static function create($id = NULL) { return new self($id); }

}