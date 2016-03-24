<?php

/**
 * Class BluePrint
 */
class Blueprint extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, size_id AS size_id, corner_radius_id as corner_radius,
                             mounting_hole_arrangement_id as mounting_hole_arrangement_id, image_file as image_file, active AS active
                             FROM bs_blueprints WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

	/**
	 * Unique id
	 * DB column: bs_blueprints.id
	 *
	 * @var int $id
	 */
	private $id;

	/**
	 * Relational size id from the size table
	 * DB column: bs_blueprints.size_id
	 *
	 * @var int $sizeId;
	 */
	private $sizeId;

    /**
     * @var int $size
     */
	private $Size;

	/**
	 * Relational $cornerRadiusId from bs_corner_radius table
	 * DB column: bs_blueprints.corner_radius_id
	 *
	 * @var int $cornerRadiusId
	 */
	private $cornerRadiusId;

    /**
     * @var int $cornerRadius
     */
	private $CornerRadius;

    /**
     * @var int $MountingHoleArrangement
     */
	private $MountingHoleArrangement;

	/**
	 * The blueprint image file
	 * DB column: bs_blueprints.image_file
	 *
	 * @var string $imageFile
	 */
	private $imageFile;

    /**
     * @var int $mountingHoleId
     */
    private $mountingHoleId;

	/**
	 * Is the blueprint active?
	 * DB column: bs_blueprints.active
	 *
	 * @var int|bool $active
	 */
	private $active;

	/**
	 * Hold tooltip for the blueprint
	 *
	 * @var string
	 */
	private $toolTip = NULL;

	/**
	 *
	 * @param null|int $id
	 * @param null|int $sizeId
	 * @param null|int $cornerRadiusId
	 * @param null|int $mountingHoleArrangementId
	 */
	public function __construct($id = NULL, $sizeId = NULL, $cornerRadiusId = NULL, $mountingHoleArrangementId = NULL) {

		$this->setId($id)
			 ->setSizeId ($sizeId)
		 	 ->setCornerRadiusId ($cornerRadiusId)
			 ->setMountingHoleArrangmentId ($mountingHoleArrangementId);

		if( !is_null($this->getId()) || !is_null($this->getSizeId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

				if( !is_null ($this->getMountingHoleId ()) && !is_null ($this->getCornerRadiusId ())) {

					$query = Connection::getHandle ()->prepare (
						self::FULL_TABLE_DUMP." AND size_id = :size_id AND mounting_hole_arrangement_id = :mountingHole_id AND corner_radius_id = :radius_id "
					);

					$query->bindParam (':size_id', $this->getSizeId (), PDO::PARAM_INT);
					$query->bindParam (':mountingHole_id', $this->getMountingHoleId (), PDO::PARAM_INT);
					$query->bindParam (':radius_id', $this->getCornerRadiusId (), PDO::PARAM_INT);

				}else{

					$query = Connection::getHandle ()->prepare (self::FULL_TABLE_DUMP." AND id = :id ");
					$query->bindParam (':id', $this->getId (), PDO::PARAM_INT);
				}

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->getId(), $data);
                }
			}

			if( is_null($this->getId()) && !empty($data) ) $this->setId($data['id']);

			$this->setImageFile($data['image_file'])
				 ->setActive($data['active']);

		} else {
			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load Blue Print properties: \'' . $id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	 * @param $id
	 * @return Blueprint() Return current class object
	 */
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set the $sizeId
     *
	 * @param $sizeId
	 * @return Blueprint() Return current class object
	 */
	public function setSizeId($sizeId) {

		$this->sizeId = isset($sizeId) && is_numeric($sizeId) && $sizeId > 0 ? (int) $sizeId : NULL;
		return $this;
	}

	/**
	 * Set the $cornerRadiusId property
	 *
	 * @param $cornerRadiusId CornerRadius unique ids
     * @return Blueprint()
	 */
	public function setCornerRadiusId($cornerRadiusId) {

		$this->cornerRadiusId = isset($cornerRadiusId) && is_numeric($cornerRadiusId) && $cornerRadiusId > 0 ? (int) $cornerRadiusId : NULL;
		return $this;
	}

	/**
	 * Set the $MountingHoleId
	 *
	 * @param $mountingHoleId
	 * @return Blueprint() Return current class object
	 */
	public function setMountingHoleArrangmentId($mountingHoleId) {
		$this->mountingHoleId = isset($mountingHoleId) && is_numeric($mountingHoleId) && $mountingHoleId > 0 ? (int) $mountingHoleId : NULL;
		return $this;
	}

	/**
	 * Set the $imageFile
	 *
	 * @param $imageFile
	 * @return Blueprint() Return current class object
	 */
	public function setImageFile($imageFile) {
		$this->imageFile = !empty($imageFile) ? trim($imageFile) : NULL;
		return $this;
	}

	/**
	 * Set the $active int 0 / 1 representing
	 * bool values
	 *
	 * @param int|bool $active
	 * @return Blueprint() Return current class object
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
     * @return int|Size
     */
    public function getSize() {

		$this->Size = Size::create($this->sizeId);
		return $this->Size;
	}

	public function getBluePrintToolTips($data = NULL) {

		if( !is_null ($this->getImageFile ()) ) {

			$this->toolTip = "The diagram above is a representation of a typical layout. All measurements are approximate.";

		}else if( isset($data) ) {

			$this->toolTip = "The diagram above is for physical dimensions only. For information on how far away the product can be read from, see the size table.";

		}else if( !is_null($this->getCornerRadiusId()) && !is_null($this->getMountingHoleId()) ) {

			$this->toolTip = "In some cases hole locations may be moved closer to the edges in order to improve sign legibility.";
		}

		return $this->toolTip;

	}
	/**
	 * @return int
	 */
	public function getSizeId() {
		return $this->sizeId;
	}

    /**
     * @return object
     */
    public function getCornerRadius() {
		$this->CornerRadius = CornerRadius::create($this->cornerRadiusId);
		return $this->CornerRadius;
	}

	/**
	 * @return int
	 */
	public function getCornerRadiusId() {
		return $this->cornerRadiusId;
	}


    /**
     * @return object
     */
    public function getMountingHoleArrangement() {
		$this->MountingHoleArrangement = MountingHoleArrangement::create($this->mountingHoleArrangementId);
		return $this->MountingHoleArrangement;
	}

	/**
	 * @return int
	 */
	public function getMountingHoleId() {
		return $this->mountingHoleId;
	}

    /**
     * @return string $imageFile
     */
    public function getImageFile() { return $this->imageFile; }

    /**
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

	/**
	 *
	 * @param null|int $id
	 * @param null|int $sizeId
	 * @param null|int $cornerRadiusId
	 * @param null|int $mountingHoleArrangementId
	 * @return Blueprint
	 */
    public static function create($id = NULL, $sizeId = NULL, $cornerRadiusId = NULL, $mountingHoleArrangementId = NULL) {
		return new self($id, $sizeId, $cornerRadiusId, $mountingHoleArrangementId); }
}
