<?php


/**
 * Class CacheableEntity
 */
class CacheableEntity {

    /**
     * Path to the cache folder
     */
    const DIRECTORY = "/cache";

    /**
     * Path to the directory of specific class instances
     *
     * @var null|string $cacheFile
     */
    private $cacheFile = NULL;

    /**
     * A list of cacheable classes
     *
     * @var array $cacheableClasses
     */
    public static $cacheableClasses = array(
            "AdvertisingCategory", "Blueprint", "CanonicalPageUrl", "CategoryPage", "Compliance", "ComplianceGroup",
            "FlashTool", "Font", "GroupingPage", "Header", "HeaderTranslation", "InstallationAccessory", "InstallationMethod",
            "InstallationQuestion", "InstallationQuestionAnswer", "InstallationStep", "InstallationStepList", "Laminate",
            "Language", "Packaging", "PageType", "LandingPage", "Material", "MaterialCategory", "MaterialGroup",
            "MountingHoleArrangement", "MountingHoleOverlayImage", "Pricing", "PricingTier", "Product", "ProductAccessory",
            "ProductCollection", "ProductCollectionProduct", "ProductPage", "ProductRecommendation", "Reflectivity",
            "Shape", "Size", "Sku",  "StreetNameTool" , "SubcategoryPage", "TextHeight", "ToolType",
            "TranslationFamily", "Unit", "ChangeFrequency", "CornerRadius", "GeotargetPage", "AccessoryFamilyProduct",
            "ProductPageTemplate"
    );

    /**
     * Constructor : Specifies the directory where to store the cache
     *
     * @param string  [$className, used in determining the correct directory]
     * @param int $id
     */
    public function __construct($className, $id) {

        if ( in_array($className, self::$cacheableClasses) ) {

            $this->cacheFile = self::DIRECTORY . "/" . $className . "/" . $id;
            // sprintf("%s/%s/%s", self::DIRECTORY, $className, $id);

        } else {

			// Report that class is not cacheable
			trigger_error('CacheableEntity was instantiated for a class that is not a CacheableEntity.');
		}
    }

    /**
     * Gets cached data that is indexed by corresponding id from DB
     *
     * @return Object [A cached object]
     */
    protected function getCache() {

        // Check if file exists and is readable
        if ( file_exists(APP_ROOT . $this->cacheFile) ) {

            if (!is_readable(APP_ROOT . $this->cacheFile)) {

                chmod(APP_ROOT . $this->cacheFile, 0777);
            }

            if( defined("CACHE_ENABLED") && CACHE_ENABLED === TRUE ) {

                return (filesize(APP_ROOT . $this->cacheFile) > 0 ? unserialize(file_get_contents(APP_ROOT . $this->cacheFile)) : NULL);
            }
        }

        return NULL;
    }

    /**
     * Stores (array) $data specified directory this->cacheFile
     *
     * @param  array Array of data to store, usually raw data from database
     * @return bool  Whether or not the storing was successful
     */
    public function storeCache($data = NULL) {

        if( !is_null($this->cacheFile) ) {

            //If @file_put_contents() fails, then check the directory to make sure they exists
            if( file_put_contents(APP_ROOT .$this->cacheFile, serialize($data)) === false ) {

                $this->checkCacheDir($data);
            }
        }

        return NULL;
    }

    /**
     * Check if cache directories exists, if not create them
     *
     * @param null $data
     */
    public function checkCacheDir($data = NULL) {

        $traversDir = array();
        $splitFiles = explode(DIRECTORY_SEPARATOR, $this->cacheFile);
        $filename   = end($splitFiles);

        foreach($splitFiles as $directories) {

            if( !empty($directories) && $directories != $filename ) $traversDir[] = $directories;
        }

        if( !file_exists(implode(DIRECTORY_SEPARATOR, $traversDir)) ) {

            if (mkdir(APP_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $traversDir), 0777, true)) {

                file_put_contents(APP_ROOT . $this->cacheFile, serialize($data));
            }
        }
    }

    /**
     * Crates an instance of CacheableEntity
     *
     * @param object $className
     * @param int $rowId
     * @param string $data
     * @return bool
     */
    public static function createCache($className, $rowId, $data)  {
     
        $Cache = new self($className, $rowId);

        return $Cache->storeCache($data);
    }
}
