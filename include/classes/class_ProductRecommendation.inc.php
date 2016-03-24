<?php

// by product

/**
 * Class ProductRecommendation
 */
class ProductRecommendation extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, title, subtitle, active, product_id, recommend_product_id, position
						  	 FROM bs_product_recommendations WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

    /**
     * Unique recommendation id
     * DB column: bs_product_recommendations.id.
     *
     * @var int $id
     */
    protected $id;

    /**
     * Title of recommendation
     * DB column: bs_product_recommendations.title.
     *
     * @var string $title
     */
    private $title;

    /**
     * Subtitle of recommendation
     * DB column: bs_product_recommendations.subtitle.
     *
     * @var string $subtitle
     */
    private $subtitle;

    /**
     * Recommended product
     * DB column: bs_product_recommendations.recommend_product_id.
     *
     * @var Product $recommendProductId
     */
    private $RecommendedProduct;

    /**
     * [$recommendProductId description]
     * @var int
     */
    private $recommendProductId;

    /**
     * Position of the recommendation on product page
     * DB column: bs_product_recommendations.position.
     *
     * @var int $position
     */
    private $position;

    /**
     * Whether the recommendation is active or not
     * DB column: bs_product_recommendations.active.
     *
     * @var bool $active
     */
    private $active;

    /**
     * Construct will handle setting calling
     * the setters methods
     *
     * @param int $id Id used to query records from bs_installation_question_answer
     */
    public function __construct($id) {

        $this->setId($id);

        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    while ($data = $query->fetch (PDO::FETCH_ASSOC)) {

                        // Parse class properties with results
                        $this->setTitle ($data['title'])->setSubtitle ($data['subtitle'])
                             ->setPosition ($data['position'])->setRecommendProduct ($data['recommend_product_id'])
                             ->setActive ($data['active']);
                    }

                    $this->storeCache ($data);
                }
            } else {

                //Trigger a notice if an invalid ID was supplied.
                trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
            }
        }
    }

    /*************************************************
     * Start Setters
     **************************************************/
    /**
     * Set the ProductRecommendation id
     *
     * @param int|null $id
     * @return $this
     */
    private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

    /**
     * Set the ProductRecommendation title
     *
     * @param string $title
     * @return ProductRecommendation() Return current class object
     */
    public function setTitle($title)
    {
        $this->title = !empty($title) ? trim($title) : NULL;
        return $this;
    }

    /**
     * Set teh ProductRecommendation subtitle
     *
     * @param string $subtitle
     * @return ProductRecommendation() Return current class object
     */
    public function setSubtitle($subtitle) {
        $this->subtitle = !empty($subtitle) ? trim($subtitle) : NULL;

        return $this;
    }

    /**
     * Create instance of Product('int').
     * Set $recommendProductId to an instance of Product
     * Instance of Product('int') takes a parameter of type `int`
     *
     * @param int $recommendProductId
     * @return ProductRecommendation() Return current class object
     */
    public function setRecommendProduct($recommendProductId)
    {
        $this->recommendProductId = isset($recommendProductId) && is_numeric($recommendProductId) &&
        $recommendProductId > 0 ? (int)$recommendProductId : NULL;

        return $this;
    }

    /**
     * Set the $position of the results returned in
     * the order of $position assigned
     *
     * @param int $position
     * @return ProductRecommendation() Return current class object
     */
    public function setPosition($position)
    {
        $this->position = isset($position) && is_numeric($position) && $position > 0 ? (int)$position : NULL;

        return $this;
    }

    /**
     * Check if product is $active, if product is active
     * set to TRUE, else set to False.
     *
     * @param bool $active
     * @return ProductRecommendation() Return current class object
     */
    public function setActive($active)
    {

        $this->active = (bool)$active;
        return $this;
    }

    /*************************************************
     * Start Getters
     **************************************************/
    /**
     * Get the ProductRecommendation id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the ProductRecommendation title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the ProductRecommendation subtitle
     *
     * @return string $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Get the ProductRecommendation products
     *
     * @return Product
     */
    public function getRecommendProduct()
    {

        $this->RecommendedProduct[$this->recommendProductId] = Product::create($this->recommendProductId);

        return $this->RecommendedProduct;
    }

    /**
     * Get the ProductRecommendation position
     *
     * @return int $position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * If ProductRecommendation is active, return its data, else false
     *
     * @return bool $active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Create a static instance of ProductRecommendation
     *
     * @param null $id
     * @return ProductRecommendation
     */
    public static function create($id = NULL)
    {
        return new self($id);
    }
}