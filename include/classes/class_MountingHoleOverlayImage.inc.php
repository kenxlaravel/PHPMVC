<?php

/**
 * Class MountingHoleOverlayImages
 */
class MountingHoleOverlayImage extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id as id, size_id AS size_id, mounting_hole_arrangement_id as mounting_hole_arrangement_id,
                              overlay_image_file as overlay_image_file, active AS active
                             FROM bs_mounting_hole_overlay_images WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	 * Unique id
	 * DB column: bs_mounting_hole_overlay_image.id
	 *
	 * @var int $id
	 */
	private $id;

	/**
	 * Relational size id from the size table
	 * DB column: bs_mounting_hole_overlay_image.size_id
	 *
	 * @var int $sizeId
	 */
	private $sizeId;

    /**
     * @var array Size()
     */
	private $Size;

	/**
	 * Relational mounting hole id from bs_mounthing_hole_arrangements table
	 * DB column: bs_mounting_hole_overlay_image.mounting_hole_id
	 *
	 * @var int $mountingHoleArrangementId
	 */
	private $mountingHoleArrangementId;

    /**
     * @var array MountingHoleArrangement()
     */
	private $MountingHoleArrangement;

	/**
	 * The blueprint image file
	 * DB column: bs_mounting_hole_overlay_image.image_file
	 *
	 * @var string $overlayImageFile
	 */
	private $overlayImageFile;

	/**
	 * Is the blueprint active?
	 * DB column: bs_mounting_hole_overlay_image.active
	 *
	 * @var int|bool $active
	 */
	private $active;

    /**
     * @var string $imageFile
     */
    private $imageFile;

	/**
	 * Construct will handle setting and calling
	 * the setters methods
	 *
	 * @param int $id Query records from bs_mounting_hole_overlay_image
	 */
	public function __construct($id) {

		$this->setId($id);

		if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->id);

            // Attempt to get data from cache
            $data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    $this->storeCache($this->getId(), $data);
                }
			}

            //@TODO: Change me, this should be just explode()
			foreach ( $data AS $key => $field ) {

				if ( strpos($key, "array_") == 0 ) {

					// Remove 'array_' and unset old unexploded version.
					$data[substr($key, 6)] = explode(",", $field);
					unset($data[$key]);

				}
			}

			$this->setSIzeId($data['size_id'])
				 ->setMountingHoleArrangementId($data['mounting_hole_arrangement_id'])
				 ->setOverlayImageFile($data['overlay_image_file'])
				 ->setActive($data['active']);

		} else {
			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load Blue Print properties: \'' . $this->id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	 * Set privately the $id and return class object
	 *
	 * @param int $id
	 * @return MountingHoleOverlayImages() Return current class object
	 */
	public function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	/**
	 * Set the $sizeId
	 *
	 * @param int $sizeId
	 * @return MountingHoleOverlayImages() Return current class object
	 */
	public function setSizeId($sizeId) {
		$this->sizeId = $sizeId;
		return $this;
	}

	/**
	 * Set the $MountingHoleId
	 *
	 * @param int $mountingHoleArrangementId
	 * @return MountingHoleOverlayImages() Return current class object
	 */
	public function setMountingHoleArrangementId($mountingHoleArrangementId) {
		$this->$mountingHoleArrangementId = $mountingHoleArrangementId;
		return $this;
	}

	/**
	 * Set the $imageFile
	 *
	 * @param string $imageFile
	 * @return MountingHoleOverlayImages() Return current class object
	 */
	public function setOverlayImageFile($imageFile) {
		$this->imageFile = !empty($imageFile) ? trim($imageFile) : NULL;
		return $this;
	}

	/**
	 * Set the $active
	 *
	 * @param int|bool $active
	 * @return MountingHoleOverlayImages() Return current class object
	 */
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the MountingHoleOverlayImages id
     *
     * @return int
     */
	public function getId() { return $this->id; }

    /**
     * Get the MountingHoleOverlayImages sizes
     *
     * @return array|Size
     */
    public function getSizeId() {

		$this->Size = Size::create($this->sizeId);
		return $this->Size;

	}

    /**
     * Get the MountingHoleOverlayImages MountingHoleOverlayArrangement
     *
     * @return array|MountingHoleArrangement
     */
    public function getMountingHoleArrangement() {

		$this->MountingHoleArrangement = MountingHoleArrangement::create($this->mountingHoleArrangementId);
		return $this->MountingHoleArrangement;

	}

    /**
     * Get the MountingHoleOverlayImages overlayImageFile
     *
     * @return string
     */
    public function getOverlayImageFile() { return $this->overlayImageFile; }

    /**
     * Get the MountingHoleOverlayImages activation
     *
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * Get the MountingHoleOverlayImages by size and arrangement id
     *
     * @param $sizeId
     * @param $mountingHoleArrangementId
     */
    public function getBySizeIdAndMountingHoleArrangementId($sizeId, $mountingHoleArrangementId){}

    /**
     * Create a static instance of MountingHoleOverlayImages()
     *
     * @param null $id
     * @return MountingHoleOverlayImages
     */
    public static function create($id = NULL) { return new self($id); }
}