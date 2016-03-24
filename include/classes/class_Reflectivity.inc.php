<?php


/**
 * Class Reflectivity
 */
class Reflectivity extends CacheableEntity {

    /**
     * Constant: Hold image path
     */
    const IMAGEPATH = "";

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, astm_d4956_type, image, name, brightness, night_time_visibility, rating, reflectivity, active
                             FROM `bs_reflectivities` WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

    /**
     * Unique reflectivity id
     * DB column: bs_reflectivities.id.
     *
     * @var int $id
     */
	private $id;

	/**
     * File path for reflectivity image
     * DB Column: bs_reflectivties.image
     *
     * @var string $image
     */
	private $image;

	/**
     * Material reflectivity name
     * DB column: bs_reflectivities.name.
     *
     * @var string $name
     */
    private $name;

    /**
     * Material reflectivity brightness
     * DB column: bs_reflectivities.brightness.
     *
     * @var string $brightness
     */
	private $brightness;

    /**
     * @var string $astmD4956Type
     */
    private $astmD4956Type;

    /**
     * Material reflectivity night time visibility
     * DB column: bs_reflectivities.night_time_visibility.
     *
     * @var string $nightTimeVisibility
     */
	private $nightTimeVisibility;

	/**
     * Material reflectivity rating
     * DB column: bs_reflectivities.rating.
     *
     * @var int $rating
     */
    private $rating;

    /**
     * Material reflectivity
     * DB column: bs_reflectivities.reflectivity.
     *
     * @var int $reflectivity
     */
    private $reflectivity;

    /**
     * Whether or not the material unit is active
     * DB column: bs_reflectivities.active.
     *
     * @var int|bool $active
     */
    private $active;

    /**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_units
	 * @throws Error if $id is not set
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

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }

                //Set up our properties with their respected values
                $this->setImage($data['image'])
                     ->setName($data['name'])
                     ->setBrightness($data['brightness'])
                     ->setNightTimeVisibility($data['night_time_visibility'])
                     ->setRating($data['rating'])
                     ->setAstmD4956Type($data['astm_d4956_type'])
                     ->setReflectivity($data['reflectivity'])
                     ->setActive($data['active']);

            } else {

				 // Trigger a notice if an invalid ID was supplied.
		        trigger_error('Cannot load reflectivity properties: \'' . $id . '\' is not a valid ID number.');
			}
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
    /**
     * Set privately the $id and return $this
     *
     * @param int $id
     * @return Reflectivity()
     */
    private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
    }

    /**
     * Set $image for the bs_reflectivities table
     * Trim() any spaces at the start/end of the string
     *
     * @param string $image
     * @return object Return current class object
     */
	public function setImage($image = '') {
		$this->image = !empty($image) ? trim(self::IMAGEPATH . $image) : NULL;
		return $this;
	}

    /**
     * @param $astmD4956Type
     * @return Reflectivity()
     */
    public function setAstmD4956Type($astmD4956Type) {

        $this->astmD4956Type = !empty($astmD4956Type) ? trim($astmD4956Type) : NULL;
        return $this;
    }

	/**
	* Set $name for the bs_reflectivities table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $name bs_reflectivities.name
	* @return $this Return current class object
	*/
	public function setName($name = '') {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set $brightness for the bs_reflectivities table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $brightness bs_reflectivities.brightness
	* @return $this Return current class object
	*/
	public function setBrightness($brightness = '') {
		$this->brightness = !empty($brightness) ? trim($brightness) : NULL;
		return $this;
	}

	/**
	* Set $nightTimeVisibility for the bs_reflectivities table
	* Trim() any spaces at the start/end of the string
	*
	* @param string $nightTimeVisibility  bs_reflectivities.night_time_visibility
	* @return object Return current class object
	*/
	public function setNightTimeVisibility($nightTimeVisibility = '') {
		$this->nightTimeVisibility = !empty($nightTimeVisibility) ? trim($nightTimeVisibility) : NULL;
		return $this;
	}

	/**
	* Set $rating for the bs_reflectivities table
	* Trim() any spaces at the start/end of the string
	*
	* @param int $rating
	* @return object Return current class object
	*/
	public function setRating($rating = NULL) {

		$this->rating = !is_null($rating) ? (int) $rating : NULL;
		return $this;
	}

	/**
	* Set $reflectivity for the bs_reflectivities table
	* Trim() any spaces at the start/end of the string
	*
	* @param int $reflectivity
	* @return Reflectivity
	*/
	public function setReflectivity($reflectivity = NULL) {

		$this->reflectivity = !is_null($reflectivity) ? (int) $reflectivity : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false
	 * table value is set as `bit`
	 *
	 * @param bool $active bs_installation_methods.active
	 * @return Reflectivity
	 */
	public function setActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the Reflectivity id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the Reflectivity image
     *
     * @return string $image
     */
    public function getImage () { return $this->image; }

    /**
     * Get the Reflectivity name
     *
     * @return string $name
     */
    public function getName () { return $this->name; }

    /**
     * Get the Reflectivity brightness
     *
     * @return string $brightness
     */
    public function getBrightness () { return $this->brightness; }

    /**
     * @return string $astmD4956Type
     */
    public function getAstmD4956Type() { return $this->astmD4956Type; }

    /**
     * Get the Reflectivity nightTimeVisibility
     *
     * @return string $nightTimeVisibility;
     */
    public function getnightTimeVisibility () { return $this->nightTimeVisibility; }

    /**
     * Get the Reflectivity rating
     *
     * @return int $rating
     */
    public function getRating () { return $this->rating; }

    /**
     * Get the Reflectivity information
     *
     * @return int $reflectivity
     */
    public function getReflectivity () { return $this->reflectivity; }

    /**
     * If Reflectivity is active, return data, else return false
     *
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

    /**
     * Create a static instance of Reflectivity
     *
     * @param null $id
     * @return Reflectivity
     */
    public static function create ($id = NULL) { return new self($id); }

}
