<?php
// by product_ref, builder_ref, size_ref, material_ref, scheme_ref, sku, product

/**
 * Class Language
 */
class MountingHoleArrangement extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id AS id, name AS name, short_name AS short_name, position, count AS count, hole_size_display_unit_id,
                             hole_size AS hole_size, active AS active FROM bs_mounting_hole_arrangements WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	* Unique arrangement id
	* DB column: bs_mounting_hole_arrangements.id.
    *
	* @var int $id
	*/
	private $id;

	/**
	* Name of the mounting hole arrangement
	* DB column: bs_mounting_hole_arrangements.name.
    *
	* @var string $name
	*/
	private $name;

	/**
	* Short name of the mounting hole arrangement
	* DB column: bs_mounting_hole_arrangements.short_name.
    *
	* @var string $shortName
	*/
	private $shortName;

	/**
	* Amount of holes
	* DB column: bs_mounting_hole_arrangements.count.
    *
	* @var int $count
	*/
	private $count;

    /**
     * @var int $holeSizeDisplayUnitId
     */
    private $holeSizeDisplayUnitId;

	/**
	* Size of the hole
	* DB column: bs_mounting_hole_arrangements.hole_size.
    *
	* @var float $holeSize
	*/
	private $holeSize;

    /**
     * Get the mounting_hole_arrangement position
     * DB column: bs_mounting_hole_arrangements.position
     *
     * @var int $position
     */
    private $position;

	/**
	* Whether or not the arrangement is active
	* DB column: bs_mounting_hole_arrangements.active.
    *
	* @var int|bool $active
	*/
	private $active;


    /**
     * Constructor
     *
     * @param null $id
     */
	public function __construct($id = NULL) {

		$this->setId($id);

        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    //$this->storeCache($data);
                }
            }

            $this->setName($data['name'])
                 ->setCount($data['count'])
                 ->setPosition($data['position'])
                 ->setActive($data['active'])
                 ->setShortName($data['short_name'])
                 ->setHoleSize($data['hole_size']);

        } else {
             // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load MountingHoleArrangement properties: \'' . $id . '\' is not a valid ID number.');
        }

	}

    /**
     * Set the id for the MountingHoleArrangement
     *
     * @param $id
     * @return MountingHoleArrangement()
     */
    private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

    /**
     * Set the name for the MountingHoleArrangement
     *
     * @param string $name
     * @return MountingHoleArrangement()
     */
    public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

    /**
     * Set the MountingHoleArrangement shortName
     *
     * @param string $shortName
     * @return MountingHoleArrangement()
     */
    public function setShortName($shortName = '') {

		$this->shortName = !empty($shortName) ? trim($shortName) : NULL;
		return $this;
	}

    /**
     * Set the MountingHoleArrangement holeSize
     *
     * @param null $holeSize
     * @return MountingHoleArrangement()
     */
    public function setHoleSize($holeSize = NULL) {

		$this->holeSize = isset($holeSize) && is_numeric($holeSize) && $holeSize > 0 ? (float) $holeSize : NULL;
		return $this;
	}

    /**
     * Set the MountingHoleArrangement position
     *
     * @param int $position
     * @return MountingHoleArrangement()
     */
    public function setPosition($position) {
		$this->position = $position;
		return $this;
	}

    /**
     * Set the MountingHoleArrangement to active or inactive
     *
     * @param int|bool $active
     * @return MountingHoleArrangement()
     */
    public function setActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

    /**
     * Set the MountingHoleArrangement count
     *
     * @param $count
     * @return MountingHoleArrangement()
     */
    public function setCount($count) {
		$this->count = (int) $count;
		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the MountingHoleArrangement id
     *
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * Get the MountingHoleArrangement name
     *
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * Get the MountingHoleArrangement shortName
     *
     * @return string
     */
    public function getShortName() { return $this->shortName; }

    /**
     * Get the MountingHoleArrangement holeSize
     *
     * @return float
     */
    public function getHoleSize() { return $this->holeSize; }

    /**
     * Get the MountingHoleArrangement position
     *
     * @return int
     */
    public function getPosition () { return $this->position; }

    /**
     * Get the MountingHoleArrangement activation
     *
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @return int $holeSizeDisplayUnitId
     */
    public function getHoleSizeDisplayUnitId() {

        return $this->holeSizeDisplayUnitId;
    }

    /**
     * Create a static instance of MountingHoleArrangement
     *
     * @param null $id
     * @return MountingHoleArrangement
     */
    public static function create($id = NULL) { return new self($id); }
}