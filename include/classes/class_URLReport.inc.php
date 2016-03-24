<?php

/**
 * Generates a URL Report for: Categories, Groupings, Sub-Categories, and Landing Pages.
 *
 */

class URLReport
{
    public $subcat_file;
    public $landingpage_file;
 
    public function __construct() {

        //Depending on which report you want to create, these file names will be used for the CSV to be downloaded.
        $this->subcat_file = Date("Y-m-d")."_Categories_URLReport.csv"; //For Categories, Groupings and Sub-Categories
        $this->landingpage_file = Date("Y-m-d")."_LandingPages_URLReport.csv"; //For Landing Pages

    }

    /**
     * Generate Categories, Groupings and SubCategories Report
     * @return array $results_array the generated report in array format
     */

    public function generateSubCategoriesReport() {

        $results_array = array();

        //Get Categories, Groupings and SubCategories
        $query = Connection::getHandle()->prepare("SELECT *
                                                    FROM (
                                                        SELECT 'category' AS pageType, c.id AS pageId, c.name AS categoryName, '' AS groupingName, '' AS subCategoryName FROM bs_categories c
                                                        WHERE c.active=true

                                                        UNION ALL

                                                        SELECT 'grouping' AS pageType, g.id AS pageId, bs_categories.name AS categoryName, g.name AS groupingName, '' AS subCategoryName FROM bs_groupings g
                                                        INNER JOIN bs_categories ON bs_categories.id=g.category_id
                                                        WHERE g.active=true

                                                        UNION ALL

                                                        SELECT 'subcategory' AS pageType, s.id AS pageId, bs_categories.name AS categoryName, bs_groupings.name AS groupingName, s.name AS subCategoryName FROM bs_subcategories s
                                                        INNER JOIN bs_groupings ON bs_groupings.id=s.grouping_id
                                                        INNER JOIN bs_categories ON bs_categories.id=bs_groupings.category_id
                                                        WHERE s.active=true

                                                    ) q
                                                    ORDER BY categoryName, groupingName, subCategoryName;");

        if ( $query->execute() ) {

            while ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                //Get the Page URL
                $results_array[] = array(
                    'pageId'            => (string)$row['pageId'],
                    'pageType'          => (string)$row['pageType'],
                    'categoryName'      => (string)$row['categoryName'],
                    'groupingName'      => (string)$row['groupingName'],
                    'subCategoryName'   => (string)$row['subCategoryName'],
                    'pageUrl'           => (string)Page::getPageUrlFromTypeAndId($row['pageType'], (int)$row['pageId']),
                );

            }

        }

        return $results_array;

    }

    /**
     * Generate Landing Pages Report
     * @return array $results_array the generated report in array format
     */

    public function generateLandingPagesReport() {

        //Get all Sub Categories
        $query = Connection::getHandle()->prepare( "SELECT id, name FROM bs_landings WHERE active=true" );

        if ( $query->execute() ) {
            while ( $row = $query->fetch(PDO::FETCH_ASSOC) ) {

                //Get the Canonical Page URL for this Landing Page
                $results_array[] = array(
                    'pageId'            => (string)$row['id'],
                    'landingPageName'   => (string)$row['name'],
                    'pageUrl'           => (string)Page::getPageUrlFromTypeAndId("landing", (int)$row['id']),
                );

            }

        }

        return $results_array;
    }

    /**
     * Export the results to an Excel Spreadsheet
     *
     * @param array $array the array that was produced after generating the report
     * @param string $filename the file name to save the CSV file as
     * @param string $report_type the type of report. i.e. Category Pages or Landing Pages
     */

    public function exportToSpreadSheet( $array, $filename, $report_type ) {

        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="'.$filename.'";');

        //Open the "output" stream
        //See http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
        $handle = fopen('php://output', 'w');

        if ( $report_type == 'subcats' ) {
            $headers = array('Page ID', 'Page Type', 'Category Page Name', 'Grouping Page Name', 'Sub-Category Page Name', 'Page URL');
        }
        else if ( $report_type == 'landingpages' ) {
            $headers = array('Page ID', 'Landing Page Name', 'Landing Page URL');
        }

        //Write Headers (Column Names) to file
        fputcsv( $handle, $headers );

        //Loop through array and write each row to file
        foreach ( $array as $line ) {
            fputcsv( $handle, $line );
        }

        //Close the Output Stream
        fclose( $handle );

    }

}
