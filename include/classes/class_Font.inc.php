<?php

/**
 * Class Font
 */
class Font extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, fallback, filename_ttf, filename_eot, filename_woff,
                             filename_server, font_ref, active FROM bs_fonts WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
     * Sets the path to the fonts directory
     */
    const FONTS_PATH = '';

	/**
	* Unique font id
	* DB column: bs_fonts.id.
	*
	* @var int $id
	*/
	private $id;

	/**
	* Name of the font
	* DB column: bs_fonts.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* DB column: bs_fonts.fallback.
	*
	* @var string $fallback
	*/
	private $fallback;

	/**
	* DB column: bs_fonts.filename_ttf.
	*
	* @var string $filenameTtf
	*/
	private $filenameTtf;

	/**
	* DB column: bs_fonts.filename_eot.
	*
	* @var string $filenameEot
	*/
	private $filenameEot;

	/**
	* DB column: bs_fonts.filename_woff.
	*
	* @var string $filenameWoff
	*/
	private $filenameWoff;

	/**
	* DB column: bs_fonts.filename_server.
	*
	* @var string $filenameServer
	*/
	private $filenameServer;

	/**
	* DB column: bs_fonts.font_ref.
	*
	* @var string $fontRef
	*/
	private $fontRef;

	/**
	* Whether or not the font is active
	* DB column: bs_fonts.active.
	*
	* @var string $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id Used to query records
	 */
	public function __construct($id = NULL) {

		$this->setId($this->id);

  		$this->setCacheDir($this->cacheDirectory);
		// Attempt to get data from cache
		$data = $this->getCache($this->id);

		if( empty($data) ) {

			if( !is_null($this->getId()) ) {

				$sql = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $sql->bindParam(":id", $this->getId(), PDO::PARAM_INT);

                if( $sql->execute() ) {

                    $data = $sql->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($this->id, $data);
                }

                $this->setName($data['name'])->setFallback($data['fallback'])
                     ->setFilenameTtf($data['filename_ttf'])->setFilenameEot($data['filename_eot'])
                     ->setFilenameWoff($data['filename_woff'])->setFilenameSever($data['filename_server'])
                     ->setFontref($data['font_ref'])->setActive($data['active']);

            } else {
                // Trigger a notice if an invalid ID was supplied.
                trigger_error('Cannot load Font properties: \'' . $id . '\' is not a valid ID number.');
            }
        }
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return current object
	*
	* @param int $id
	* @return Font() Return current class object
	*/
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	* Set the $name and return current object
	*
	* @param string $name
	* @return Font() Return current class object
	*/
	public function setName($name = '') {
		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	* Set the $fallback and return current object
	*
	* @param string $fallback
	* @return Font() Return current class object
	*/
	public function setFallback($fallback = '') {
		$this->fallback = !empty($fallback) ? trim($fallback) : NULL;
		return $this;
	}

	/**
	* Set the $filenameTtf and return current object
	*
	* @param string $filenameTtf
	* @return Font() Return current class object
	*/
	public function setFilenameTtf($filenameTtf = '') {
		$this->filenameTtf = !empty($filenameTtf) ? trim(self::FONTS_PATH. $filenameTtf) : NULL;
		return $this;
	}

	/**
	* Set the $filenameEot and return current object
	*
	* @param string $filenameEot
	* @return Font() Return current class object
	*/
	public function setFilenameEot($filenameEot = '') {
		$this->filenameEot = !empty($filenameEot) ? trim(self::FONTS_PATH.$filenameEot) : NULL;
		return $this;
	}

	/**
	* Set the $filenameWoff and return current object
	*
	* @param string $filenameWoff
	* @return Font() Return current class object
	*/
	public function setFilenameWoff($filenameWoff = '') {
		$this->filenameWoff = !empty($filenameWoff) ? trim(self::FONTS_PATH.$filenameWoff) : NULL;
        return $this;
	}

	/**
	* Set the $filenameServer and return current object
	*
	* @param string $filenameServer
	* @return Font() Return current class object
	*/
	public function setFilenameSever($filenameServer) {
		$this->filenameServer = !empty($filenameServer) ? trim($filenameServer) : NULL;
		return $this;
	}

	/**
	* Set the $fontRef and return current object
	*
	* @param string $fontRef
	* @return Font() Return current class object
	*/
	public function setFontRef($fontRef) {
		$this->fontRef = !empty($fontRef) ? trim($fontRef) : NULL;
		return $this;
	}

	/**
	* Set the $active and return $this
	*
	* @param int|bool $active
	* @return Font() Return current class object
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
    public function getName() { return $this->name; }

    /**
     * @return string
     */
    public function getFilenameTtf() { return $this->filenameTtf; }

    /**
     * @return string
     */
    public function getFilenameEot() { return $this->filenameEot; }

    /**
     * @return string
     */
    public function getFilenameWoff() { return $this->filenameWoff; }

    /**
     * @return string
     */
    public function getFilenameSever() { return $this->filenameServer; }

    /**
     * @return string
     */
    public function getFontRef() { return $this->fontRef; }

    /**
     * @return string
     */
    public function isActive() { return $this->active; }

    /**
     * @param null $id
     * @return Font
     */
    public static function create($id = NULL) { return new self($id); }
}