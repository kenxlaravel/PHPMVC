<?php

/**
 * Class CanonicalPageUrl
 */
class CanonicalPageUrl extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, url, pagetype, pageid, active FROM bs_page_urls WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
	* Unique current canonical page url id in product
	* DB column: bs_page_urls.id.
	* 
	* @var int the id of bs_page_urls.id
	*/
	private $id;

	/**
	 *  Unique canonical page url in the product
	 * 	DB column: bs_page_urls.url
	 * 
	 * @var string Page url i.e: http://...
	 */
	public $url;

	/**
	 * page type of the url
	 * DB column: bs_page_urls.pagetype
	 * 
	 * @var string CanonicalPageUrl() of pageTypes
	 * @see class_pageTypes.inc.php for class declaration
	 */
	public $pageType;

	/**
	* Url page unsigned id
	* DB column: bs_page_urls.pageid
	* 
	* @var int $pageid
	*/
	public $pageid;

	/**
	* Page url activation property
	* DB column bs_page_urls.active
	*
	* @var int|bool Verify if url is active or not
	*/
	private $active;

	/**
	 * Construct will handle setting and calling
	 * the setters methods
	 * 
	 * @param int $id Id used to query records from bs_page_urls
	 */
	public function __construct($id) {

		$this->setId($id);

		// Get cache dir
  		$this->setCacheDir($this->cacheDirectory);

		// Attempt to get data from cache
		$data = $this->getCache($this->id);

		if( empty($data) ) {

			if( !is_null($this->getId()) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                }

			} else {

				// Trigger a notice if an invalid ID was supplied.
		        trigger_error('Cannot load page url properties: \'' . $this->getId() . '\' is not a valid ID number.');
			}
		}

		$this->setUrl($data['url'])
			 ->setPagetype($data['pagetype'])
			 ->setPageid($data['pageid'])
			 ->setActive($data['active']);

		return false;
	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	 * Set privately the $id and return $this
	 * 
	 * @param int $id
	 * @return CanonicalPageUrl() $this
	 */	
	private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
	}

	/**
	 * Set the url for the page 
	 * trim() the url from spaces
	 * 
	 * @param string $url
	 * @return CanonicalPageUrl() $this
	 */	
	public function setUrl($url = '') {
		$this->url = !empty($url) ? trim($url) : NULL;
		return $this;
	}

	/**
	 * Set the $pagetype for the page
	 * trim() $pagetype removing any spaces
	 * 
	 * @param string $pagetype
	 * @return CanonicalPageUrl() $this
	 */	
	public function setPagetype($pagetype = '') {
		$this->pagetype = !empty($pagetype) ? trim($pagetype) : NULL;
		return $this;
	}

	/**
	 * Set the $pageid
	 * isset() make sure its not empty and larger than 0
	 * 
	 * @param int $pageid
	 * @return CanonicalPageUrl() $this
	 */	
	public function setPageid($pageid ) {
		$this->pageid = isset($pageid) && is_numeric($pageid) && $pageid > 0 ? (int) $pageid : NULL;
		return $this;
	}

	/**
	 * @param int|bool $active
	 * @return CanonicalPageUrl() $this
	 */	
	public function setActive($active){
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
    public function getUrl() { return $this->url; }

    /**
     * @return mixed
     */
    public function getPagetype() { return $this->pagetype; }

    /**
     * @return int
     */
    public function getPageid() { return $this->pageid; }

    /**
     * @return bool|int
     */
    public function isActive() { return $this->active; }

    /**
     * @param $id
     * @return CanonicalPageUrl
     */
    public static function create($id = NULL) { return new self($id); }

}