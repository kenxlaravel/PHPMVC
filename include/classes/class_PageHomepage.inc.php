<?php
/**
 * class_PageHomepage.inc.php
 *
 * Homepage class file.
 */


/**
 * Class PageHomepage
 */
class PageHomepage {

    /**
     * Relative paths to the home page images
     *
     * @var array $imagePath
     */
    public $imagePath = array();

    /**
     * Our constructor
     */
    public function __construct() {

        $this->imagePath['grid'] = IMAGE_URL_PREFIX . '';
    }

    /**
     * Used to get listings based off of a location on the page. Example: 'category_grid' will return all
     * categories on the homepage grid
     * @param  string $location The location on the page that we need listings for
     * @return array  $refid    And array of all the listings
     */
    public function getListings($location, $refid = null) {

        $row  = NULL;
        $stmt = NULL;

        switch ($location) {

            case 'category_grid':

                $sql = "SELECT id AS id FROM bs_categories WHERE active = 1  ORDER BY position";
                $stmt = Connection::getHandle()->prepare($sql);
                $stmt->execute();

                break;

            case 'category_gridsub':

                $sql = "SELECT type AS type, ref_id AS ref_id FROM bs_homepage_grid WHERE base_id = ? AND active = 1 ORDER BY position limit 0,5";
                $stmt = Connection::getHandle()->prepare($sql);
                $stmt->execute(array($refid));

                break;
        }


        while ($results = $stmt->fetch()) {
            $row[] = $results;
        }

        return $row;
    }
}
