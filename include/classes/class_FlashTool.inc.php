<?php


class FlashTool extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     *
     */
	const FULL_TABLE_DUMP = "SELECT id, name, font, xml, color, active FROM bs_flash_tools WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	 * Unique flash tool id
	 * DB column: bs_flash_tools.id.
	 *
	 * @var int $id
	 */
	private $id;

	/**
	 * Name of flash tool
	 * DB column: bs_flash_tools.name.
	 *
	 * @var string name
	 */
	private $name;

	/**
	 * Flash tool font
	 * DB column: bs_flash_tools.font.
	 *
	 * @var string font
	 */
	private $font;

	/**
	 * Relevant information about flash tool via xml
	 * DB column: bs_flash_tools.xml.
	 *
	 * @var string xml
	 */
	private $xml;

	/**
	 * Color
	 * DB column: bs_flash_tools.color.
	 *
	 * @var string color
	 */
	private $color;

	/**
	 * Whether or not the flash tool is active
	 * DB column: bs_flash_tools.active.
	 *
	 * @var int|bool $active
	 */
	private $active;


    /**
     * @param null $id
     */
    public function __construct($id = NULL) {

		$this->setId($id);

        $data = $this->getCache($this->getId());

        if ( !is_null($this->getId()) ) {

			$query = Connection::getHandle()
				        ->prepare(self::FULL_TABLE_DUMP . " AND id = :id");

			$query->bindParam(':id', $this->id, PDO::PARAM_INT);

            if( $query->execute() ) {

                $data = $query->fetch(PDO::FETCH_ASSOC);
                $this->storeCache($data['id'], $data);
            }

			$this->setName($data['name'])->setFont($data['font'])
				 ->setColor($data['color'])->setActive($data['active'])->setXml($data['xml']);

		}else{

			 // Trigger a notice if an invalid ID was supplied.
            trigger_error("Cannot load properties: '{$this->getId()}' is not a valid ID number.");

		}
	}

	/*************************************************
	 * Start Setters
	 **************************************************/
	/**
	 * Set privately the $id and return $this
	 *
	 * @param int $id
	 * @return FlashTool() Return current class object
	 */
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set the flash tool name
	 *
	 * @param string $name
	 * @return FlashTool() Return current class object
	 */
	public function setName($name) {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	 * Set the flash tool font
	 *
	 * @param string $font Font family name
	 * @return FlashTool()
	 */
	public function setFont($font) {
		$this->font = !empty($font) ? trim($font) : NULL;
		return $this;
	}

	/**
	 * Set the flash tool XML data
	 *
	 * @param string $xml XML parsed data
	 * @return FlashTool() Return current class object
	 */
	public function setXml($xml) {
		$this->xml = !empty($xml) ? trim($xml) : NULL;
		return $this;
	}

	/**
	 * Set the color for the flash tool
	 *
	 * @param string $color hex/string color
	 * @return FlashTool() Return current class object
	 */
	public function setColor($color) {
		$this->color = !empty($color) ? trim($color) : NULL;
		return $this;
	}

	/**
	 * Set the $active and return the current class
	 *
	 * @param int|bool $active
	 * @return FlashTool() Return current class object
	 */
	public function setActive($active) {
		$this->active = (bool)$active;
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
    public function getName() { return $this->name; }

    /**
     * @return string
     */
    public function getFont() { return $this->font; }

    /**
     * @return string
     */
    public function getXml() { return $this->xml; }

    /**
     * @return string
     */
    public function getColor() { return $this->color; }

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return FlashTool
     */
    public static function create($id = NULL) { return new self($id); }
}
