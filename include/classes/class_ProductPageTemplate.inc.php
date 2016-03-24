<?php


/**
 * Class ProductPageTemplate
 */
class ProductPageTemplate extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id, name
							 FROM bs_product_page_templates ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique identifier for product page template
	* db column : bs_product_page_templates.id
	* @var int $id
	*/
	private $id;

	/**
     * Name of product page template
     * db column : bs_product_page_templates.name
     *
     * @var string $name
     */
	private $name;

	/**
	 * Class constructor
	 * Set $id and Connect to the database
	 *
     * @param
	 * @param int $id
	 * @throws Error if $id is not set
	 */
	public function __construct($id = NULL) {

		 // Set the ID.
        $this->setId($id);

		if ( isset($id) ) {

            $data = $this->getCache();

            if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " WHERE id = :id");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					$data = $query->fetch(PDO::FETCH_ASSOC);
				}
			}

			$this->setName($data['name']);

		} else {

			 // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load ProductPageTemplate properties: \'' . $id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set the $id and return current class
	* Before assigning $id, first check to make sure that
	* we are actually getting an integer.
	*
	* @param int $id
	* @return ProductPageTemplate() Return current class object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

	/**
	* Set the $name and return current class
	*
	* @param string $name
	* @return ProductPageTemplate() Return current class object
	*/
	public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;

		return $this;
	}

	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * @return string $name
     */
    public function getName() { return $this->name; }

    /**
     * @param null $id
     * @return ProductPageTemplate
     */
    public static function create($id = NULL) { return new self($id); }

}