<?php
/**
 * Class used to Accessory Families
 *
 **/


/**
 * Class AccessoryFamilyProduct
 */
class AccessoryFamilyProduct extends CacheableEntity {

    /**
     * Constant used for two purposes
     * <ol>
     *  <li>Getting the record from the database</li>
     *  <li>FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run</li>
     * </ol>
     */
    const FULL_TABLE_DUMP = "SELECT afp.id AS id, afp.accessory_family_id AS accessory_family_id,
                                 afp.product_id AS product_id, afp.product_name AS product_name,
                                 afp.product_subtitle AS product_subtitle, afp.product_image AS product_image,
                                 afp.position AS position, GROUP_CONCAT(at.name) AS accesory_type_name
                             FROM bs_accessory_families af

                             INNER JOIN bs_accessory_family_products afp ON (afp.accessory_family_id = af.id)
                             LEFT JOIN bs_product_accessory_types pat ON (afp.product_id = pat.product_id)
                             LEFT JOIN bs_accessory_types at ON (at.id = pat.accessory_type_id) ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var int $accessoryFamilyId
     */
    private $accessoryFamilyId;

    /**
     * @var int $productId
     */
    private $productId;

    /**
     * @var string $productName
     */
    private $productName;

    /**
     * @var string $productSubtitle
     */
    private $productSubtitle;

    /**
     * @var string $productImage
     */
    private $productImage;

    /**
     * @var int $position
     */
    private $position;

    /**
     * @var string $accessoryTypeName
     */
    private $accessoryTypeName;

    /**
     * Our heart of the class.
     *
     * @param null|int $id
     * @param string   $type
     * @param null|int $productId
     */
    public function __construct($id, $productId, $type = NULL) {

        $this->setId($id)->setProductId($productId);

        if( !is_null($this->getId()) && !is_null($this->getProductId()) ) {

            //Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            //Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

                $parameter = ($type == "sku") ? " AND s.id = :id " : "AND p.id = :id ";

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP. " WHERE  afp.id = :afid ");

                $query->bindParam(':afid', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }
            }

            $this->setId($data['id'])
                 ->setAccessoryFamilyId($data['accessory_family_id'])
                 ->setProductId($data['product_id'])
                 ->setProductName($data['product_name'])
                 ->setProductImage($data['product_image'])
                 ->setProductSubtitle($data['product_subtitle'])
                 ->setAccessoryTypeName($data['accesory_type_name'])
                 ->setPosition($data['position']);
        }
    }

    /**
     * @param int $accessoryFamilyId
     * @return AccessoryFamilyProduct()
     */
    public function setAccessoryFamilyId($accessoryFamilyId) {

        $this->accessoryFamilyId = isset($accessoryFamilyId) && is_numeric($accessoryFamilyId) &&
                                            $accessoryFamilyId > 0 ? (int) $accessoryFamilyId : NULL;
        return $this;
    }

    /**
     * @param int $id
     * @return AccessoryFamilyProduct()
     */
    public function setId($id) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
        return $this;
    }

    /**
     * @param int $position
     * @return AccessoryFamilyProduct()
     */
    public function setPosition($position) {

        $this->position = (int) $position;
        return $this;
    }

    /**
     * Get the product Id from the accessory family table
     *
     * @param int $productId
     * @return AccessoryFamilyProduct()
     */
    public function setProductId($productId) {

        $this->productId = isset($productId) && is_numeric($productId) && $productId > 0 ? (int) $productId : NULL;
        return $this;
    }

    /**
     * @param int $productImage
     * @return AccessoryFamilyProduct()
     */
    public function setProductImage($productImage) {

        $this->productImage = isset($productImage) ?
            IMAGE_URL_PREFIX.'/images/catlog/product/small/'.trim($productImage) : NULL;

        return $this;
    }

    /**
     * @param string $productName
     * @return AccessoryFamilyProduct()
     */
    public function setProductName($productName) {

        $this->productName = !empty($productName) ? trim($productName) : NULL;
        return $this;
    }

    public function setAccessoryTypeName($accessoryTypeName) {

        $this->accessoryTypeName = isset($accessoryTypeName) ? trim ($accessoryTypeName) : NULL;
        return $this;
    }

    /**
     * @param string $productSubtitle
     * @return AccessoryFamilyProduct()
     */
    public function setProductSubtitle($productSubtitle) {
        $this->productSubtitle = !empty($productSubtitle) ? trim($productSubtitle) : NULL;
        return $this;
    }

    /***************************************/
    /*** Start Getters                  ****/
    /***************************************/
    /**
     * @return int
     */
    public function getAccessoryFamilyId() {

        return $this->accessoryFamilyId;
    }

    /**
     * @return int
     */
    public function getId() {

        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition() {

        return $this->position;
    }

    /**
     * @return int
     */
    public function getProductId() {

        return $this->productId;
    }

    /**
     * @return string
     */
    public function getProductImage() {

        return $this->productImage;
    }

    /**
     * @return string
     */
    public function getProductName() {

        return $this->productName;
    }

    public function getProductUrl() {

        return Page::create($this->getProductId())->getUrl();
    }

    /**
     * @return string
     */
    public function getProductSubtitle() {

        return $this->productSubtitle;
    }

    /**
     * @return string
     */
    public function getAccessoryTypeName() {

        return $this->accessoryTypeName;
    }

    public static function create($id, $productId) {

        return new self($id, $productId);
    }
}
