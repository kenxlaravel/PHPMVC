<?php


// by product


/**
 * Class TranslationFamily
 */
class TranslationFamily extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     **/
    const FULL_TABLE_DUMP = "SELECT GROUP_CONCAT(DISTINCT tp.product_id) as product_id, GROUP_CONCAT(DISTINCT tp.id) AS id, tf.name as name
    						 FROM bs_translation_family_products tp
							 INNER JOIN bs_translation_families tf ON(tp.translation_family_id = tf.id)
							 WHERE tf.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     **/
    const ADDITIONAL_CLAUSES = " GROUP BY tf.id ";

    /**
     * Unique ID of translation family
     * DB column: bs_translation_families.id
     *
     * @var int $id
     */
	private $id;

	/**
	 * @var array
	 */
	private $translationFamilyProductsIds;

	/**
     * Name of translation family
     * DB column: bs_translation_families.name
     *
     * @var string $name
     */
	private $name;

	/**
	 * @var array $translationFamilyIds
	 */
	private $translationFamilyIds;

	/**
     * DB table: bs_translation_family.comment
     *
     * @var string $comment
     */
	private $comment;

	/**
     * Whether or not the translation family is active or not
     * DB column: bs_translation_families.active
     *
     * @var int|bool $active
     */
	private $active;

    /**
     * Our constructor
     *
     * @param $id
     */
	public function __construct($id) {

		$this->setId($id);


		if( empty($data) ) {

			if ( !is_null($this->getId()) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND tp.translation_family_id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                }

				$this->setName($data['name'])
					 ->setActive($data['active'])
					 ->setTranslationFamilyProductIds($data['product_id']);

	        } else {

	            trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');
	        }
	    }
	}

    /*************************************************
     * Start Setters
     **************************************************/
    /**
     * Set the Translation Family id
     *
     * @param int $id
     * @return TranslationFamily()
     */
    private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

    /**
     * Set the TranslationFamily name
     *
     * @param string $name
     * @return TranslationFamily()
     */
	public function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;
		return $this;
	}

	/**
	 * Set the product translation family ids
	 *
	 * @param $translationFamilyIds
	 * @return Product()
	 */
	public function setTranslationFamilies ($translationFamilyIds) {

		$this->translationFamilyIds = !empty($translationFamilyIds) ? explode (",", $translationFamilyIds) : NULL;

		return $this;
	}

    /**
     * Set the TranslationFamily product
     *
     * @param $translationFamilyProductId
     * @return TranslationFamily()
     */
	public function setTranslationFamilyProductIds($translationFamilyProductId) {

		$this->translationFamilyProductsIds = !empty($translationFamilyProductId) ?
														explode(",",$translationFamilyProductId) : NULL;
		return $this;
	}

    /**
     * Set the TranslationFamily to enabled/disabled
     *
     * @param int|bool $active
     * @return TranslationFamily
     */
	public function setActive($active) {

		$this->active = (bool) $active;
		return $this;
	}

    /**
     * Get the TranslationFamily id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the TranslationFamily name
     *
     * @return string $name
     */
    public function getName() {

		return $this->name;
	}

	public function getTranslationFamilyProductIds() {

		return $this->translationFamilyProductsIds;
	}

    /**
     * Get the TranslationFamilyProducts
     *
     * @return mixed
     */
    public function getTranslationFamilyProducts() {

		if( is_array($this->getTranslationFamilyProductIds()) ) {

			foreach ($this->getTranslationFamilyProductIds() as $id) {

				$this->translationFamilyProducts[$id] = Product::create($id);
			}
		}

		return $this->translationFamilyProducts;
	}

    /**
     * Get TranslationFamily status
     *
     * @return bool|int $active
     */
    public function isActive() { return $this->active; }

    /**
     * Get the available languages
     *
     * @return Language
     */
    public function getAvailableLanguages() {

        $Language = NULL;

		if( empty($this->getTranslationFamilyProducts) ) {

			$this->getTranslationFamilyProducts();
		}

		foreach($this->translationFamilyProducts AS $product) {

			$languages[$product->getId()] = array(
						"language" 		=> $product->getLanguage(),
						"product_url" 	=>ProductPage::getPageUrl("product",$product->getId())
			);
		}

		return $languages;
	}

	/**
	 * @return array $translationFamilyIds
	 */
	public function getTranslationFamilieId () {

		return $this->translationFamilyIds;
	}



	/**
     * Create an object of TranslationFamily
     *
     * @param null $id
     * @return TranslationFamily
     */
    public static function create($id = NULL) { return new self($id); }
}
