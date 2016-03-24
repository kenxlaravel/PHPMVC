<?php

/**
 * Class GroupingPage
 */
class GroupingPage extends Page {

    /**
     * Current page type
     */
    const TYPE = 'grouping';

    /**
     * Constant used to identify the parent's current page that is being executed
     */
    const PARENT_TYPE = 'category';

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP =

            "SELECT g.id AS id, g.name, g.slug, g.active, g.position, g.accessory, g.category_id, g.category_name, g.page_title, g.page_heading, g.meta_keywords, g.meta_description,
              g.image, g.snippet, g.description_text_html, g.intro_supplement_html, g.popup_html, g.description_image, g.image_template, g.description_more_info_html,
              g.special_header_class_name, g.canonical_page_url, g.ppc_campaign, g.ppc_adgroup, g.created_by, g.created_date, g.modified_by, g.modified_date, g.sitemap_page_change_frequency,
              g.sitemap_page_priority, g.sitemap_show, s.id AS subcategory_id
             FROM bs_groupings g
             LEFT JOIN bs_subcategories s ON (s.grouping_id = g.id)
             WHERE g.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = " GROUP BY g.id ";

    /**
     * @var string $popHtml
     */
    private $popHtml;

    /**
     * @var string $descriptionTextHtml
     */
    private $descriptionTextHtml;

    /**
     * @var string $introSupplementHtml
     */
    private $introSupplementHtml;

    /**
     * Get the subcategor id associated with the current Grouping
     *
     * @var int $subcategoryId
     */
    private $subcategoryId;

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var int $categoryId
     */
    private $categoryId;

    /**
     * @var int $accessory
     */
    private $accessory;

    /**
     * @var string $imageTemplate
     */
    private $imageTemplate;

    /**
     * @var string $descriptionImage
     */
    private $descriptionImage;

    /**
     * Our class constructor
     *
     * @param int $id
     */
    public function __construct($id) {

        $this->setId($id);

        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND g.id = :id ");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    $this->storeCache($data);
                }

            }

            /* SET CLASS PROPERTIES FROM $data */
            $this->setCategoryId($data['category_id'])->setAccessory($data['accessory'])->setDescriptionImage($data['description_image'])
                 ->setImageTemplate($data['image_template'])->setDescriptionTextHtml($data['description_text_html'])
                 ->setDescriptionMoreInfoHtml($data['description_more_info_html'])->setSubcategoryId($data['subcategory_id'])
                 ->setSpecialHeaderClassName($data['special_header_class_name'])
                 ->setIntroSupplementHtml($data['intro_supplement_html'])->setPopupHtml($data['popup_html']);

        }else{
            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \''.$this->getId().'\' is not a valid ID number.');
        }
        // Pass the info up to the parent page
        parent::__construct(self::TYPE, $this->getId());

    }

    /*************************************************
     * Start Setters
     **************************************************/
    /**
     * Set/check the Grouping page id
     *
     * @param $id
     * @return GroupingPage()
     */
    private function setId($id = NULL) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

        return $this;
    }


    public function setSubcategoryId($subcategoryId) {
        $this->subcategoryId = isset($subcategoryId) && is_numeric($subcategoryId) && $subcategoryId > 0 ? (int) $subcategoryId : NULL;
        return $this;
    }

    /**
     * Set/check the Grouping page category id
     *
     * @param $categoryId
     * @return GroupingPage();
     */
    public function setCategoryId($categoryId = NULL) {

        $this->categoryId = isset($categoryId) && is_numeric($categoryId) && $categoryId > 0 ? (int) $categoryId : NULL;

        return $this;
    }

    /**
     * Set the Grouping page accesory id
     *
     * @param $accessory
     * @return GroupingPage()
     */
    public function setAccessory($accessory = NULL) {

        $this->accessory = (int) $accessory;

        return $this;
    }

    /**
     * Set the Grouping page image description
     *
     * @param $descriptionImage
     * @return GroupingPage()
     */
    public function setDescriptionImage($descriptionImage = "") {

        $this->descriptionImage = !empty($descriptionImage) ? trim($descriptionImage) : NULL;

        return $this;
    }

    /**
     * Set the Grouping page image template
     *
     * @param $imageTemplate
     * @return GroupingPage()
     */
    public function setImageTemplate($imageTemplate = "") {

        $this->imageTemplate = isset($imageTemplate) ? trim($imageTemplate).'-banner' : 'basic-banner';

        return $this;
    }

    /**
     * Set the Grouping page description text html
     *
     * @param $descriptionTextHtml
     * @return GroupingPage()
     */
    public function setDescriptionTextHtml($descriptionTextHtml = "") {

        $this->descriptionTextHtml = !empty($descriptionTextHtml) ? trim($descriptionTextHtml) : NULL;

        return $this;
    }

    /**
     * Set the Page Grouping descriptionMoreInfoHtml
     *
     * @param string $descriptionMoreInfoHtml
     *
     * @return $this
     */
    public function setDescriptionMoreInfoHtml($descriptionMoreInfoHtml = "") {

        $this->descriptionMoreInfoHtml = !empty($descriptionMoreInfoHtml) ? trim($descriptionMoreInfoHtml) : NULL;

        return $this;
    }

    /**
     * Set the Page Grouping specialHeaderClassName
     *
     * @param string $specialHeaderClassName
     * @return $this
     */
    public function setSpecialHeaderClassName($specialHeaderClassName = "") {

        $this->specialHeaderClassName = !empty($specialHeaderClassName) ? trim($specialHeaderClassName) : NULL;

        return $this;
    }

    /**
     * Set the Page Grouping interSupplementHtml
     *
     * @param string $interSupplementHtml
     * @return $this
     */
    public function setIntroSupplementHtml($interSupplementHtml = "") {

        $this->interSupplementHtml = !empty($interSupplementHtml) ? trim($interSupplementHtml) : NULL;

        return $this;
    }

    /**
     * Set the Page Grouping popuphtml
     *
     * @param $popupHtml
     * @return $this
     */
    public function setPopupHtml($popupHtml) {

        $this->popupHtml = !empty($popupHtml) ? trim($popupHtml) : NULL;

        return $this;
    }

    /*************************************************
     * Start Getters
     **************************************************/
    /**
     * Get the Grouping page category id
     *
     * @return int $id
     */
    public function getId() { return $this->id; }

    /**
     * Get the Grouping page category id
     *
     * @return int $categoryId
     */
    public function getCategoryId() { return $this->categoryId; }

    /**
     * Get the Grouping page Accessory
     *
     * @return int $accessory
     */
    public function getAccessory() { return $this->accessory; }

    /**
     * Get the Grouping page image description
     *
     * @return int $descriptionImage
     */
    public function getDescriptionImage() { return $this->descriptionImage; }

    /**
     * Get the Grouping page image template
     *
     * @return int $imageTemplate
     */
    public function getImageTemplate() { return $this->imageTemplate; }

    /**
     * Get the Grouping page description html
     *
     * @return int $descriptionTextHtml
     */
    public function getDescriptionTextHtml() { return $this->descriptionTextHtml; }

    /**
     * Get the Grouping page description of more info html
     *
     * @return int $descriptionMoreInfoHtml
     */
    public function getDescriptionMoreInfoHtml() { return $this->descriptionMoreInfoHtml; }

    /**
     * Get the Grouping page special header class name
     *
     * @return int $specialHeaderClassName
     */
    public function getSpecialHeaderClassName() { return $this->specialHeaderClassName; }

    /**
     * Get the Grouping page intro supplement html
     *
     * @return int $introSupplementHtml
     */
    public function getIntroSupplementHtml() { return $this->introSupplementHtml; }

    /**
     * Get the Grouping page popup html
     *
     * @return int $popHtml
     */
    public function getPopupHtml() { return $this->popHtml; }


    /**
     * Used to get listings based off of a location on the page. Example: 'grid' will return all
     * listings on the grouping page grid (aka subcategories)
     *
     * @param     string $location The location on the page that we need listings for
     * @return    array                  And array of all the listings
     */
    public function getListings($location) {

        $stmt = NULL;
        $results = NULL;

        switch ($location) {

            case 'grid':

                $sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname,
                             IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity, c.name AS name, u.url AS short_url,
                             t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename, c.id AS canonical,
                             c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                             c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,
                             t.disallow_guests AS disallow_guests, c.sitemap_show AS visibility, c.sitemap_page_priority AS priority,
                             c.sitemap_page_change_frequency AS change_frequency, c.* FROM bs_subcategories c JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id) LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND c.active = TRUE AND grp.id = ? AND t.pagetype = 'subcategory'
					        GROUP BY c.id ORDER BY c.position ASC, c.name ASC";
                break;

            case 'sidebar':

                $sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                             c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                             c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                             c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login,
                             t.disallow_guests AS disallow_guests, c.sitemap_show AS visibility, c.sitemap_page_priority AS priority,
                             c.sitemap_page_change_frequency AS change_frequency, grp.accessory
					        FROM bs_subcategories c JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id) LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND c.active = TRUE AND grp.id = ? AND t.pagetype = 'subcategory'
					        GROUP BY c.id ORDER BY c.name ASC";
                break;
        }

        $stmt = Connection::getHandle()->prepare($sql);

        if( $stmt->execute(array ($this->getId())) ) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $results[] = $row;
            }
        }

        return $results;
    }

    public function getType() {
        return self::TYPE;
    }
    /**
     * Return the Grouping subcategory id
     *
     * @return int $subcategoryId
     */
    public function getSubcategoryId() {
        return $this->subcategoryId;
    }

    /**
     * Create an instantiation of GroupingPage()
     *
     * @param int|null $id
     * @return GroupingPage
     */
    public static function create($id = NULL) { return new self($id); }
}