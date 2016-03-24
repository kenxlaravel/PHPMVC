<?php


/**
 * Class PageCategory
 */
class PageCategory {

    /**
     * @var array $imagePath
     */
    public $imagePath = array();

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $shortName
     */
    public $shortName;

    /**
     * @var string $slug
     */
    public $slug;

    /**
     * @var int $position
     */
    public $position;

    /**
     * @var string $image
     */
    public $image;

    /**
     * @var string $pageTitle
     */
    public $pageTitle;

    /**
     * @var string $pageHeading
     */
    public $pageHeading;

    /**
     * @var string $metaKeywords
     */
    public $metaKeywords;

    /**
     * @var string $metaDescription
     */
    public $metaDescription;

    /**
     * @var string $descriptionTextHtml
     */
    public $descriptionTextHtml;

    /**
     * @var string $descriptionImage
     */
    public $descriptionImage;

    /**
     * @var string $descriptionMoreInfoHtml
     */
    public $descriptionMoreInfoHtml;

    /**
     * @var int $navWidth
     */
    public $navWidth;

    /**
     * @var string $imageTemplate
     */
    public $imageTemplate;

    /**
     * @var string $specialHeaderClassName
     */
    public $specialHeaderClassName;

    /**
     * @var string $introSupplementHtml
     */
    public $introSupplementHtml;

    /**
     * @var string $popupHtml
     */
    public $popupHtml;


    /**
     * Constructor
     *
     * @param null|int $pageid
     */
    public function __construct($pageid = NULL) {

        //Set the image paths
        $this->imagePath['grid'] = IMAGE_URL_PREFIX . '';
        $this->imagePath['description'] = IMAGE_URL_PREFIX . '';

        //Some functions can be used without a page id, but a number of core properties will not exist
        if( $pageid ) {

            $this->id = $pageid;
            $this->getProperties();
        }
    }

    public function getId() {

        return $this->id;
    }

    /**
     * Main properties function to get most of what we need about the page on instantiation
     * and set as class variables.
     *
     * Get Properties
     */
    private function getProperties() {

        //If we do not already have this info for the current page, grab it
        if( empty($this->name) ) {

            $stmt = Connection::getHandle()->prepare("SELECT * FROM bs_categories WHERE id=? LIMIT 1");

            if ($stmt->execute(array($this->id))) {

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                //Set class properties
                $this->name = $row['name'];
                $this->shortName = $row['short_name'];
                $this->slug = $row['slug'];
                $this->position = $row['position'];
                $this->image = $this->imagePath['grid'] . $row['image'];
                $this->pageTitle = $row['page_title'];
                $this->pageHeading = $row['page_heading'];
                $this->metaKeywords = $row['meta_keywords'];
                $this->metaDescription = $row['meta_description'];
                $this->descriptionTextHtml = $row['description_text_html'];
                $this->descriptionImage = $row['description_image'];
                $this->imageTemplate = $row['image_template'] ? trim($row['image_template']) . '-banner' : 'basic-banner';
                $this->descriptionMoreInfoHtml = $row['description_more_info_html'];
                $this->navWidth = isset($row['nav_width']) ? $row['nav_width'] : NULL;
                $this->specialHeaderClassName = $row['special_header_class_name'];
                $this->introSupplementHtml = $row['intro_supplement_html'];
                $this->popupHtml = $row['popup_html'];
            }
        }
    }

    /**
     * Used to get listings based off of a location on the page. Example: 'grid' will return all
     * categories that appear on the category page grouping grid
     *
     * @param     string $location The location on the page that we need listings for
     * @return    array     $refid       An array of all the listings
     */
    public function getListings($location, $refid = NULL) {

        $stmt = NULL;
        $row  = NULL;

        switch ($location) {

            case 'grid':

                $sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                         c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                         c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                         c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
                         c.xml_show AS visibility, c.sitemap_page_priority AS priority, c.sitemap_page_change_frequency AS change_frequency, c.*
                        FROM bs_groupings c JOIN bs_pagetypes t
                        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
                        LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
                        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
                        WHERE cat.active = TRUE AND c.active = TRUE AND cat.id = ? AND t.pagetype = 'grouping'
                        GROUP BY c.id ORDER BY c.position ASC, c.name ASC";

                $stmt = Connection::getHandle()->prepare($sql);

                break;

            case 'sidebar':

                $sql = "SELECT
                         t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                         c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                         c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                         c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
                         c.xml_show AS visibility, c.xml_page_priority AS priority,c.sitemap_page_change_frequency AS change_frequency, c.accessory AS accessory
                        FROM bs_groupings c JOIN bs_pagetypes t
                         LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
                         LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
                         LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
                         WHERE cat.active = TRUE AND c.active = TRUE AND cat.id = ? AND t.pagetype = 'grouping' GROUP BY c.id ORDER BY c.accessory ASC,
                         c.name ASC";

                $stmt = Connection::getHandle()->prepare($sql);

                break;
        }

        if( $stmt->execute(array($this->id)) ) {

            while( $results = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                $row[] = $results;
            }
        }

        return $row;
    }

    /**
     * Instantiate class statically
     *
     * @param null $id
     * @return PageCategory
     */
    public static function create($id = NULL) { return new self($id); }
}