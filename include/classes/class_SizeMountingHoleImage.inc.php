<?php


/**
 * Class SizeMountingHoleImage
 */
class SizeMountingHoleImage extends CacheableEntity {

    /**
     * Path to overlay Images
     */
    const OVERLAY_IMAGE_PATH = '';

    /**
     * Path to blueprints
     */
    const BLUEPRINT_PATH = '';

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, size_id, mounting_hole_arrangement_id, overlay_image_file, blueprint
                             FROM bs_size_mounting_hole_images WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

	/**
	* Unique mounting hole image identifier
	* DB Column : bs_size_mounting_hole_images.id
	*
	* @var int $id
	*/
	private $id;

	/**
	 * Unique size id used to get the size in bs_sizes
	 * DB column: bs_size_mounting_hole_images.size_id
	 *
	 * @var int $sizeId
	 */
	private $sizeId;

	/**
	* DB Column: bs_size_mounting_hole_images.mounting_hole_arrangement_id
	* Parent to: bs_mounting_hole_arrangements
	*
	* @var int $mountingHoleArrangementID
	*/
	private $mountingHoleArrId;

	/**
	* File path for mounting hole image
	* DB Column: bs_size_mounting_hole_images.overlay_mage_file
	*
	* @var string $overlayImageFile
	*/
	private $overlayImageFile;

	/**
	* File path for blueprint
	* DB Column: bs_size_mounting_hole_images.blueprint
	*
	* @var string $blueprint
	*/
	private $blueprint;

	/**
	 * The Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_material
	 * @throws Error if $id is not set
	 */
    public function __construct($id = NULL) {

        // Set the ID.
        $this->setId($id);

  		$this->setCacheDir($this->cacheDirectory);

		$data = $this->getCache($this->id);

		if( !is_null($this->getId()) ) {

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");
				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if ($query->execute()) {
					$data = $query->fetch(PDO::FETCH_ASSOC);
	        	}
			}

			$this->setOverlayImageFile($data['overlay_image_file'])
				 ->setBlueprint($data['blueprint'])
				 ->setSizeId($data['size_id'])
				 ->setMountingHoleArrangementId($data['mounting_hole_arrangement_id']);

		} else {

			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load SizeMountingHoleImage properties: \'' . $id . '\' is not a valid ID number.');

		}
    }

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	 * Set the $id and return current class with
	 * $id populated
	 *
	 * @param int $id
	 * @return SizeMountingHoleImage() Current class object
	 */
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set the $imagefile and return current class with
	 * $imagefile populated.
	 *
	 * @param string $overlayImageFile
	 * @return SizeMountingHoleImage() Current class object
	 */
	public function setOverlayImageFile($overlayImageFile = '') {

		$this->overlayImageFile = trim(self::OVERLAY_IMAGE_PATH.$overlayImageFile);
		return $this;
	}

	/**
	 * Set the $blueprint and return current class with
	 * $blueprint image file populated.
	 *
	 * @param string $blueprint
	 * @return SizeMountingHoleImage() Current class object
	 */
	public function setBlueprint($blueprint = '') {

		$this->blueprint = trim(self::BLUEPRINT_PATH.$blueprint);
		return $this;
	}

	/**
	 * Set the $mountingHoleArrId and return current class with
	 * $mountingHoleArrId populated
	 *
	 * @param int $mountingHoleArrId
	 * @return SizeMountngHoleImage() Current class object
	 */
	public function setMountingHoleArrangementId($mountingHoleArrId = NULL) {

		$this->mountingHoleArrId =
				isset($mountingHoleArrId) && is_numeric($mountingHoleArrId) &&
					$mountingHoleArrId > 0 ? (int) $mountingHoleArrId : NULL;

		return $this;
	}

	/**
	* Set the $sizeId and return current class with
	* $sizeId populated in $sizeID
	*
	* @param int $sizeId
	* @return SizeMountingHoleImage() Return current class object
	*/
	public function setSizeId($sizeId = NULL) {

		$this->sizeId = isset($sizeId) && is_numeric($sizeId) && $sizeId > 0 ? (int) $sizeId : NULL;
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
     * @return mixed
     */
    public function getMountingArrangement() { return $this->MountingHoleArrangement; }

    /**
     * @return string
     */
    public function getOverLayImageFile() { return $this->overlayImageFile; }

    /**
     * @return string
     */
    public function getBlueprint() { return $this->blueprint; }

    /**
     * @return int
     */
    public function getSizeID() { return $this->sizeId; }

    /**
     * @param $sizeId
     * @param $mountingHoleArrId
     * @return bool|SizeMountingHoleImage
     */
    public static function createFromSizeAndMountingHoleArrangementIds($sizeId, $mountingHoleArrId) {

		if( !empty($sizeId) && !empty($mountingHoleArrId) ) {

			if( is_numeric($sizeId) && is_numeric($mountingHoleArrId)) {

				$query = Connection::getHandle()
						->prepare("SELECT id
									FROM bs_size_mounting_hole_images WHERE
										size_id = :sid AND mounting_hole_arrangement_id = :mid"
				);

				$query->bindParam(':sid', $sizeId, PDO::PARAM_INT);
				$query->bindParam(':mid', $mountingHoleArrId, PDO::PARAM_INT);

				if($query->execute()) {

					$data = $query->fetch(PDO::FETCH_ASSOC);
				}

				return !empty($data['id']) ? self::create($data['id']) : FALSE;
			}
		}
	}

    /**
     * @param null $id
     * @return SizeMountingHoleImage
     */
    public static function create($id = NULL) { return new self($id); }

}
