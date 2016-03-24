<?php


/**
 * Class Size
 */
class Size extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, position, name, width, width_display_unit_id, height, height_display_unit_id, diameter,
                             diameter_display_unit_id, depth, depth_display_unit_id, length, length_display_unit_id,
							 volume, volume_display_unit_id, active, shape_id, minimum_pipe_diameter, minimum_pipe_diameter_display_unit_id,
							 maximum_pipe_diameter, maximum_pipe_diameter_display_unit_id
				             FROM `bs_sizes` WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique Size id
	* DB column: bs_skus.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Name of Size
	* DB column: bs_skus.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* Width property
	* DB column: bs_skus.width.
	*
	* @var float $width
	*/
	private $width;

	/**
	 * New column added on Feb 10, 2015
	 * DB column: bs_sizes.width_display_unit_id
	 *
	 * @var int $widthDisplayUnitId
	 */
	private $widthDisplayUnitId;

	/**
	* Height property
	*
	* DB column: bs_skus.height.
	* @var float $height
	*/
	private $height;

	/**
	 * New column added on Feb 10, 2015
	 * DB column: bs_sizes.height_diplay_unit_id
	 *
	 * @var int $heighDisplayUnitId
	 */
	private $heightDisplayUnitId;

	/**
	* Used for liquid type products (I.E: Paints)
	* DB Column: bs_skus.volume
	*
	* @var float $volume
	*/
	private $volume;

	/**
	 * New column added on Feb 10, 2014
	 * DB column: bs_sizes.volume_display_unit_id
	 *
	 * @var int $volumeDisplayUnitId
	 */
	private $volumeDisplayUnitId;

	/**
	* Diameter
	* DB column: bs_skus.diameter.
	*
	* @var float $diameter
	*/
	private $diameter;

	/**
	 * New column added on Feb 10, 2014
	 * DB column: bs_sizes.diameter_display_unit_id
	 *
	 * @var int diameterDisplayUnitId
	 */
	private $diameterDisplayUnitId;

	/**
	* Depth property
	* DB column: bs_sizes.depth
	*
	* @var float $depth
	*/
	private $depth;

	/**
	 * New column added on Feb 10, 2015
	 * DB column: bs_sizes.depth_display_unit_id
	 *
	 * @var float $depthDisplayUnitId
	 */
	private $depthDisplayUnitId;

	/**
	 * @var array $mountingHoleImageIds
	 */
	private $mountingHoleImageIds;

	/**
	* Length property
	* DB column: bs_sizes.length
	*
	* @var float $length
	*/
	private $length;

	/**
	 * New column added on Feb 10, 2014
	 * DB column: bs_sizes.length_display_unit_id
	 *
	 * @var int $lengthDisplayUnitId
	 */
	private $lengthDisplayUnitId;

	/**
	* Whether size is active or not
	* DB column: bs_skus.active.
	*
	* @var int|bool $active
	*/
	private $active;

	/**
	* Shape object
	* DB column: bs_shapes.
	*
	* @var Shape $Shape
	*/
	private $Shape;

	/**
	 * Holds the shape ids
	 * DB column: bs_shape.shape_id
	 *
	 * @var int $shapeId
	 */
	private $shapeId;

	/**
	 * New column added on Oct 23, 2014
	 * DB column: bs_sizes.max_pipe_diameter.maximum_pipe_diameter
	 *
	 * @var float $maxPipeDiameter
	 */
	private $maxPipeDiameter;

	/**
	 * New column added on Feb 10, 2015
	 * DB column: bs_sizes.max_pipe_diameter_display_unit_id
	 *
	 * @var int $maxPipeDiameterDisplayUnitId
	 */
	private $maximumPipeDiameterDisplayUnitId;

	/**
	 * New column added on Oct 23, 2014
	 * DB column: bs_sizes.max_pipe_diameter.minimum_pipe_diameter
	 *
	 * @var float $minPipeDiameter
	 */
	private $minPipeDiameter;

	/**
	 * New column added on Feb 10, 2015
	 * DB column: bs_sizes.minimum_pipe_diameter_display_unit_id
	 *
	 * @var int $minimumPipeDiameterDisplayUnitId
	 */
	private $minimumPipeDiameterDisplayUnitId;

	/**
	 * @var int $position
	 */
	private $position;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Id used to query records from bs_skus
	 */
	public function __construct($id) {

		$this->setId($id);


        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP .  " AND id = :id  ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

					$data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }
            }

            $this->setName($data['name'])->setWidth($data['width'])->setWidthDisplayUnitId($data['width_display_unit_id'])
				 ->setPosition($data['position'])
				 ->setHeight($data['height'])->setHeightDisplayUnitId($data['height_display_unit_id'])->setDiameter($data['diameter'])
				 ->setDiameterDisplayUnitId($data['diameter_display_unit_id'])->setDepth($data['depth'])
				 ->setDepthDisplayUnitId($data['depth_display_unit_id'])->setLength($data['length'])
				 ->setlengthDisplayUnitId($data['length_display_unit_id'])->setVolume($data['volume'])
				 ->setVolumeDisplayUnitId($data['volume_display_unit_id'])->setMaxPipeDiameter($data['maximum_pipe_diameter'])
				 ->setMaximumPipeDiameterDisplayUnitId($data['maximum_pipe_diameter_display_unit_id'])
				 ->setMinPipeDiameter($data['minimum_pipe_diameter'])
				 ->setMinimumPipeDiameterDisplayUnitId($data['minimum_pipe_diameter_display_unit_id']);

        } else {

			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return Size() Return current class object
	*/
	public function setId($id) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;

        return $this;
	}

	public function setPosition ($position) {
		$this->position = $position;
		return $this;
	}

	/**
	* Set the $name and return class object
	*
	* @param string $name
	* @return self Return current class object
	*/
	public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	 * @param int $heightDisplayUnitId
     * @return Size()
	 */
	public function setHeightDisplayUnitId($heightDisplayUnitId)
	{
		$this->heightDisplayUnitId = isset($heightDisplayUnitId) && is_numeric ($heightDisplayUnitId) &&
												$heightDisplayUnitId > 0 ? (int) $heightDisplayUnitId : NULL;
        return $this;
	}

	/**
	 * @param int $volumeDisplayUnitId
     * @return Size()
	 */
	public function setVolumeDisplayUnitId($volumeDisplayUnitId)
	{
		$this->volumeDisplayUnitId = isset($volumeDisplayUnitId) && is_numeric ($volumeDisplayUnitId) &&
												$volumeDisplayUnitId > 0 ? (int) $volumeDisplayUnitId : NULL;
        return $this;
	}

	/**
	 * @param int $diameterDisplayUnitId
     * @return Size()
	 */
	public function setDiameterDisplayUnitId($diameterDisplayUnitId)
	{
		$this->diameterDisplayUnitId = isset($diameterDisplayUnitId) && is_numeric ($diameterDisplayUnitId) &&
												$diameterDisplayUnitId > 0 ? (int) $diameterDisplayUnitId : NULL;
        return $this;
	}

	/**
	 * @param float $depthDisplayUnitId
     * @return Size()
	 */
	public function setDepthDisplayUnitId($depthDisplayUnitId) {

		$this->depthDisplayUnitId = isset($depthDisplayUnitId) && is_numeric ($depthDisplayUnitId) &&
												$depthDisplayUnitId > 0 ? (int) $depthDisplayUnitId : NULL;
        return $this;
	}

	/**
	 * @param int $lengthDisplayUnitId
     * @return Size()
	 */
	public function setLengthDisplayUnitId($lengthDisplayUnitId) {

		$this->lengthDisplayUnitId = isset($lengthDisplayUnitId) && is_numeric ($lengthDisplayUnitId) &&
													$lengthDisplayUnitId > 0 ? (int) $lengthDisplayUnitId : NULL;
		return $this;
	}

	/**
	 * @param Shape $Shape
     * @return Size()
	 */
	public function setShape($Shape)
	{
		$this->Shape = $Shape;
		return $this;
	}

	/**
	 *
	 * @param int $maximumPipeDiameterDisplayUnitId
	 * @return Size()
	 */
	public function setMaximumPipeDiameterDisplayUnitId($maximumPipeDiameterDisplayUnitId) {

		$this->maximumPipeDiameterDisplayUnitId =
					isset($maximumPipeDiameterDisplayUnitId) &&  is_numeric ($maximumPipeDiameterDisplayUnitId)
						&& $maximumPipeDiameterDisplayUnitId > 0 ? (int) $maximumPipeDiameterDisplayUnitId : NULL;
		return $this;
	}

	/**
	 * @param int $minimumPipeDiameterDisplayUnitId
     * @return Size()
	 */
	public function setMinimumPipeDiameterDisplayUnitId($minimumPipeDiameterDisplayUnitId)
	{
		$this->minimumPipeDiameterDisplayUnitId = isset($minimumPipeDiameterDisplayUnitId) &&
					is_numeric ($minimumPipeDiameterDisplayUnitId) && $minimumPipeDiameterDisplayUnitId > 0 ?
																	(int) $minimumPipeDiameterDisplayUnitId : NULL;
		return $this;
	}


	/**
	* Set the $width and return a class object
	*
	* @param float $width
	* @return self Return current class object
	*/
	public function setWidth($width) {

		$this->width = !is_null($width) ? (float) $width : NULL;
		return $this;
	}

	/**
	 * @param int $widthDisplayUnitId
	 * @return Size()
	 */
	public function setWidthDisplayUnitId($widthDisplayUnitId)  {

		$this->widthDisplayUnitId = isset($widthDisplayUnitId) && is_numeric ($widthDisplayUnitId) &&
													$widthDisplayUnitId > 0 ? (int) $widthDisplayUnitId : NULL;
		return $this;
	}

	/**
	 * Set the $width and return a class object
	 *
	 * @param float $height
	 * @return self Return current class object
	 */
	public function setHeight ($height) {

		$this->height = !is_null($height) ? (float) $height : NULL;
		return $this;
	}

	/**
	 * Set the $volume and return a class object
	 *
	 * @param float $volume
	 * @return self Return current class object
	 */
	public function setVolume ($volume) {

		$this->volume = !is_null($volume) ? (float) $volume : NULL;
		return $this;
	}

	/**
	 * Set the $diameter and return a class object
	 *
	 * @param float $diameter
	 * @return self Return current class object
	 */
	public function setDiameter ($diameter) {

		$this->diameter = !is_null($diameter) ? (float) $diameter : NULL;

		return $this;
	}

	/**
	 * Set the $shapeId and return a class object
	 *
	 * @param int $shapeId
	 * @return Size() Return current class object
	 */
	public function setShapeId ($shapeId) {

		$this->shapeId = isset($shapeId) && is_numeric ($shapeId) && $shapeId > 0 ? (int) $shapeId : NULL;
		return $this;
	}

	/**
	 * Set the $depth and return a class object
	 *
	 * @param float $depth
	 * @return Size() Return current class object
	 */
	public function setDepth ($depth) {

		$this->depth = !is_null($depth) ? (float) $depth : NULL;
		return $this;
	}

	/**
	 * Set the $length and return a class object
	 *
	 * @param float $length
	 * @return self Return current class object
	 */
	public function setLength ($length) {

		$this->length = !is_null($length) ? (float) $length : NULL;
		return $this;
	}

	/**
	 * Daniel
	 *
	 * @param: array $mountingHoleImageIds
	 * @return Size()
	 */
	public function setMountingHoleImages ($mountingHoleImageIds) {

		$this->mountingHoleImageIds = $mountingHoleImageIds;
		return $this;
	}

	/**
	 * Set the $maxpipeDiameter and return current class object
	 *
	 * @param float $maxpipeDiameter
	 * @return Size()
	 */
	public function setMaxPipeDiameter ($maxpipeDiameter = NULL) {

		$this->maxPipeDiameter = !is_null ($maxpipeDiameter) ? $maxpipeDiameter : NULL;

		return $this;
	}

	/**
	 * Set the $minpipeDiameter and return current class object
	 *
	 * @param float $minpipeDiameter
	 * @return Size()
	 */
	public function setMinPipeDiameter ($minpipeDiameter = NULL) {

		$this->minPipeDiameter = !is_null ($minpipeDiameter) ? $minpipeDiameter : NULL;

		return $this;
	}

	/**
	 * Set the $active and return current class object
	 *
	 * @param int|bool $active
	 * @return object Return current class object
	 */
	public function setActive ($active) {

		$this->active = (bool) $active;

		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * Get the Size id
     *
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * Get the Size name
     *
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * Get the Size width
     *
     * @return float
     */
    public function getWidth() { return $this->width; }

    /**
     * Get the Size height
     *
     * @return float
     */
    public function getHeight() { return $this->height; }

    /**
     * Get the Size volume
     *
     * @return float
     */
    public function getVolume() { return $this->volume; }

    /**
     * Get the Size Diameter
     *
     * @return float
     */
    public function getDiameter() { return $this->diameter; }

    /**
     * Get the Size shape
     *
     * @return Shape
     */
    public function getShape() { return $this->Shape; }

    /**
     * Get the Size length
     *
     * @return float
     */
    public function getLength() { return $this->length; }

    /**
     * Get the Size depth
     *
     * @return float
     */
    public function getDepth() { return $this->depth; }

    /**
     * If this Size is active, return TRUE, FALSE otherwise
     *
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * Get the Size MaxPipeDiameter
     *
     * @return float
     */
    public function getMaxPipeDiameter() { return $this->maxPipeDiameter; }


	/**
	 * @return int
	 */
	public function getHeightDisplayUnitId () { return $this->heightDisplayUnitId; }

    /**
     * @return int
     */
    public function getWidthDisplayUnitId() { return $this->widthDisplayUnitId; }


	//@todo: Finish logic
	public function getMountingHoleImages() {

/*		foreach ($mountingHoleImageIds AS $id) {

			$this->mountingHoleImages[$id] = SizeMountingHoleImage::create ($id);
		}
*/
	}

	/**
	 * @return int
	 */
	public function getVolumeDisplayUnitId () {

		return $this->volumeDisplayUnitId;
	}

	/**
	 * @return int
	 */
	public function getDiameterDisplayUnitId () {

		return $this->diameterDisplayUnitId;
	}

	/**
	 * @return float
	 */
	public function getDepthDisplayUnitId () {

		return $this->depthDisplayUnitId;
	}

	/**
	 * @return int
	 */
	public function getLengthDisplayUnitId () {

		return $this->lengthDisplayUnitId;
	}

	public function getPosition() {
		return $this->position;
	}

	/**
	 * @return int
	 */
	public function getShapeId () {

		return $this->shapeId;
	}

	/**
	 * @return int
	 */
	public function getMaximumPipeDiameterDisplayUnitId () {

		return $this->maximumPipeDiameterDisplayUnitId;
	}

	/**
	 * @return int
	 */
	public function getMinimumPipeDiameterDisplayUnitId () {

		return $this->minimumPipeDiameterDisplayUnitId;
	}

	/**
     * Get the Size minPipeiameter
     *
     * @return float
     */
    public function getMinPipeDiameter() { return $this->minPipeDiameter; }


    /**
     * Convert decimal to fraction
     *
     * @param $decimalValue
     * @return mixed
     */
    public function converDecToFrac($decimalValue) {

		$precision = 0.01;

		return Converter::dec2frac($decimalValue, $precision);
	}

    /**
     * Create a static instance of Size()
     *
     * @param null $id
     * @return Size
     */
    public static function create($id = NULL) { return new self($id); }

}


