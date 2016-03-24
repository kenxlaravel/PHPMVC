<?php


class Menu {

    private $dbh;
    private $cacheFile;
    private $navigationCache;

    const CACHE_DIR = 'cache';
    const CACHE_FILENAME = 'navigation.json';

    /**
     * Constructor
     */
    public function __construct() {

        //Establish a database connection
		$this->dbh = Connection::getHandle();

        // Define the cache file path.
         $this->cacheFile = APP_ROOT.'/'.self::CACHE_DIR.'/'.self::CACHE_FILENAME;

         // Import the cache data.
         $this->importCache();

    }

     public function getNavigation() {
         return $this->navigationCache;
     }

     private function importCache() {

        // Read the cache and parse the JSON.
        if ( file_exists($this->cacheFile) ) {
            $this->navigationCache = json_decode(file_get_contents($this->cacheFile), true);
        } else {
            $this->updateCache();
        }

        return $this;

     }

     public function updateCache() {

         // Prepare the navigationCache array
         $sqldata = $this->MenuList();

        foreach($sqldata as $key => $cvalue){

            $main_id = $cvalue['id'];
            $link = new Page($cvalue['primary_link_pagetype'], $cvalue['primary_link_pageid']);
            $clink = $link->getUrl();
            $validate_link = $link->getValidity();

            $navigation_array[$key] = array(
                'main_category_id' => $cvalue['primary_link_pageid'],
                'main_menu_name' => $cvalue['name'],
                'main_menu_css_class' => $cvalue['css_class'],
                'main_category_image' => $cvalue['image'],
                'column_breakpoint' => $cvalue['column_breakpoint'],
                'clink' => $clink,
                'validate_link' => $validate_link,
                'sub_menu' => array());

            $sub_menu = $this->SubMenuList($main_id);

                foreach ($sub_menu as $svalue) {
                    $link = new Page($svalue['pagetype'], $svalue['pageid']);
                    if ( $link->getValidity() ) {
                        $slink = $link->getUrl();
                        $navigation_array[$key]['sub_menu'][] = array(
                            'sub_name' => $svalue['name'],
                            'sub_menu_pagetype' => $svalue['pagetype'],
                            'sub_menu_id' => $svalue['pageid'],
                            'slink' => $slink);
                    }
                }

             $this->navigationCache = $navigation_array;

        }

         // Cache the status array as JSON.
         file_put_contents($this->cacheFile, json_encode($this->navigationCache));

         return $this;

     }


    public function MenuList()
    {

        $result = array();

        $sql = Connection::getHandle()->prepare(
            "SELECT id AS id, name AS name, primary_link_pagetype AS primary_link_pagetype,
                         primary_link_pageid AS primary_link_pageid, column_breakpoint AS column_breakpoint, css_class AS css_class, image AS image
                         FROM bs_navigation_menu WHERE active = 1 ORDER BY position");
        if ($sql->execute()) {

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }

        }

        return $result;
    }


    function SubMenuList($main_category_id) {

        $sql = $this->dbh->prepare("SELECT pagetype AS pagetype,
                                           name AS name,
                                           pageid AS pageid
                                    FROM bs_navigation_submenu
                                    WHERE  navigation_menu_id = ?
                                    AND active = 1
                                    ORDER BY position");
        $sql->execute(array($main_category_id));
        while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $sub_menu[] = $row;
        }

        return $sub_menu;

    }

}
