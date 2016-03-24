<?php

/**
 * @class CornerRadius
 **/
 class CornerRadius extends CacheableEntity {

     /**
      * Constant used for two purposes
      *
      * - Getting the record from the database
      * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
      */
	 const FULL_TABLE_DUMP = "SELECT id, corner_radius as corner_radius, active AS active, corner_radius_display_unit_id
                              FROM bs_corner_radiuses WHERE active = 1 ";

     /**
      * Extra query parameter used with $FULL_TABLE_DUMP
      */
	 const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	 * Unique id
	 * DB column: bs_corner_radius.id
	 *
	 * @var int $id
	 */
	private $id;

	/**
	 * Corner Radius that will later get converted to Fraction
	 * DB column: bs_corner_radius.corner_radius
	 *
	 * @var int $cornerRadius
	 */
	private $cornerRadius;

	/**
	 * Is the record active?
	 * DB column: bs_corner_radius.active
	 *
	 * @var int|bool $active
	 */
	private $active;

     /**
      * @var int $cornerRadiusDisplayUnitId
      */
    private $cornerRadiusDisplayUnitId;

	/**
	 * Construct will handle setting and calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_corner_radius
	 */
	public function __construct($id) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->id);

            $data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($id, $data);
                }
			}

            $this->setCornerRadius($data['corner_radius'])
                ->setCornerRadiusDisplayUnitId($data['corner_radius_display_unit_id'])
                 ->isActive($data['active']);

		} else {
			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load Corner Radius properties: \'' . $this->id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	 * Set privately the $id and return class object
	 *
	 * @param int $id
	 * @return CornerRadius Return current class object
	 */
	public function setId($id) {
		
		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set the $cornerRadius properties
	 *
	 * @param int $cornerRadius
	 * @return CornerRadius Return current class object
	 */
	public function setCornerRadius($cornerRadius) {

		$this->cornerRadius = !is_null($cornerRadius) ? (float) $cornerRadius : NULL;
		return $this;
	}

     /**
      *
      * @param int $cornerRadiusDisplayUnitId
      * @return CornerRadius()
      */
     public function setCornerRadiusDisplayUnitId($cornerRadiusDisplayUnitId) {

         $this->cornerRadiusDisplayUnitId = isset($cornerRadiusDisplayUnitId) && is_numeric ($cornerRadiusDisplayUnitId)
		 									&& $cornerRadiusDisplayUnitId > 0 ? (int) $cornerRadiusDisplayUnitId : NULL;
         return $this;
     }


	/**
	 * Set the $active properties
	 *
	 * @param bool $active
	 * @return CornerRadius Return current class object
	 */
	public function isActive($active) {
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
     public function getCornerRadius() { return $this->cornerRadius; }

     /**
      * @return bool|int
      */
     public function getActive() { return $this->active; }

     /**
      * @return int
      */
     public function getCornerRadiusDisplayUnitId() {

         return $this->cornerRadiusDisplayUnitId;
     }

     /**
      * @param null $id
      * @return CornerRadius
      */
     public static function create($id = NULL) { return new self($id); }
 }
