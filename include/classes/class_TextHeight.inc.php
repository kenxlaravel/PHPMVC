<?php

    /**
     * Class TextHeight
     *
     */
    class TextHeight extends CacheableEntity {

        /**
         * Constant used for two purposes
         *
         * - Getting the record from the database
         * - $FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
         */
        const FULL_TABLE_DUMP = "SELECT id, height, size_id, product_id, active FROM bs_text_heights WHERE active = 1 ";

        /**
         * Extra query parameter used with $FULL_TABLE_DUMP
         */
        const ADDITIONAL_CLAUSES = " GROUP BY id ";

        /**
         * ID of text height
         * DB column: bs_skus.id.
         *
         * @var [int] $id
         */
        private $id;

        /**
         * Height of text
         *  DB column: bs_skus.height.
         *
         * @var [int] $height
         */
        private $height;

        /**
         * Size id
         * DB column: bs_skus.size_id.
         *
         * @var [int] $sizeId
         */
        private $sizeId;

        /**
         * Size Object
         * DB Table: bs_sizes
         *
         * @var object $Size
         */
        private $Size;

        /**
         * Whether text height is active or not
         *
         * DB column: bs_skus.active.
         *
         * @var [bool] $active
         */
        private $active;

        /**
         * @var int $maxViewingDistance
         */
        private $maxViewingDistance;

        /**
         * @var string $formula
         */
        private $formula;

        /**
         * The heart of the realm
         *
         * @param null|int $id
         * @param string   $formulaType
         */
        public function __construct ($id = NULL, $formulaType = NULL) {

            // Set the ID.
            $this->setId ($id);

            if( !is_null ($this->getId ()) ) {

                $data = $this->getCache ($this->getId ());

                if( empty($data) ) {

                    $query = Connection::getHandle ()->prepare (self::FULL_TABLE_DUMP." AND id = :id ");

                    $query->bindParam (':id', $this->getId (), PDO::PARAM_INT);

                    if( $query->execute () ) {

                        $data = $query->fetch (PDO::FETCH_ASSOC);
                        $this->storeCache ($this->getId (), $data);
                    }
                }

                $this->setHeight ($data['height'])->setSizeID ($data['size_id'])
                     ->setFormula ($formulaType)
                     ->setActive ($data['active']);
            }else{

                // Trigger a notice if an invalid ID was supplied.
                trigger_error ('Cannot load TextHeight properties: \''.$id.'\' is not a valid ID number.');
            }
        }

        /*************************************************
         * Start Setters
         **************************************************/
        /**
         * Set privately the $id and return $this
         *
         * @param int $id
         * @return TextHeight()
         */
        private function setId ($id = NULL) {

            $this->id = isset($id) && is_numeric ($id) && $id > 0 ? (int) $id : NULL;
            return $this;
        }

        /**
         * Set the $height and return $this
         *
         * @param [int] $height
         * @return TextHeight()
         */
        public function setHeight ($height = NULL) {

            $this->height = !is_null($height) ? (float) $height : NULL;

            return $this;
        }

        public function setFormula ($formulaType = NULL) {

            $this->formula = !empty($formulaType) ? trim ($formulaType) : NULL;

            return $this;
        }

        /**
         * Set privately the $sizeId and return $this
         *
         * @param int $sizeId
         * @return TextHeight()
         */
        public function setSizeID ($sizeId) {

            $this->sizeId = isset($sizeId) && is_numeric ($sizeId) && $sizeId > 0 ? (int) $sizeId : NULL;

            return $this;
        }

        /**
         * Set privately the $active and return $this
         *
         * @param  bool|int $active
         * @return TextHeight()
         */
        public function setActive ($active) {

            $this->active = (bool) $active;

            return $this;
        }

        /*************************************************
         * Start Getters
         **************************************************/
        //Get the Id
        private function getId () { return $this->id; }

        //Get the Height
        public function getHeight () { return !is_null ($this->height) ? $this->height : NULL; }

        /**
         * Return the size of this TextHeight
         *
         * @return Size()
         */
        public function getSize () {

            if( empty($this->Size) && isset($this->sizeId) ) {

                $this->Size = SIze::create ($this->sizeId);
            }

            return $this->Size;
        }


        /**
         *
         * ANSI / OSHA
         * 25 feet of viewing distance per inch of text height. Thus, if ?v? is the viewing distance and ?t?
         * is the text height, a formula of ?v = 300t? is used.
         *
         * NFPA
         *   1 inch of text height is suitable for viewing distances of 0–50 feet.
         *   2 inches of text height is suitable for viewing distances of 50–75 feet.
         *   3 inches of text height is suitable for viewing distances of 75–100 feet.
         *   4 inches of text height is suitable for viewing distances of 100–200 feet.
         *   6 inches of text height is suitable for viewing distances of 200–300 feet.
         *   Recommendations for viewing distances of over 300 feet are not available.
         *
         * @return float|int
         */
        public function calculateMaxViewingDistance () {

            switch ($this->getFormula ()) {

                case "ANSI_OSHA":

                    if( !is_null ($this->getHeight ()) ) {
                        $this->maxViewingDistance = ($this->getHeight () * 300);
                    }

                    break;

                case "NFPA":

                    //@todo: change logic
                    if( !is_null ($this->getHeight ()) && $this->getHeight () <= 6 ) {
                        $this->maxViewingDistance = ($this->getHeight () * 300) / 12;
                    }

                    break;

                default:
                    $this->maxViewingDistance = $this->getHeight () * 300;

                    break;
            }

            return $this->maxViewingDistance;
        }

        /**
         * @return null|int $height
         */
        public function getMaximumFavorableViewingDistance () {

            return !is_null ($this->getHeight ()) ? ($this->getHeight () * 300) : NULL;
        }

        /**
         * @return null|int $height
         */
        public function getMaximumUnfavorableViewingDistance () {

            return !is_null ($this->getHeight ()) ? ($this->getHeight () * 144) : NULL;
        }

        /**
         *
         * @param $productId
         * @param $sizeId
         * @param $formulaType
         * @return null|TextHeight
         */
        public static function createFromProductAndSizeIds ($productId, $sizeId, $formulaType = NULL) {

            $formula = !is_null ($formulaType) ? $formulaType : NULL;

            if( !empty($sizeId) && !empty($productId) ) {

                if( is_numeric ($sizeId) && is_numeric ($productId) ) {

                    $query = Connection::getHandle ()->prepare (
                        "
						SELECT id FROM bs_text_heights WHERE size_id = :sid AND product_id = :pid"
                    );

                    $query->bindParam (':sid', $sizeId, PDO::PARAM_INT);
                    $query->bindParam (':pid', $productId, PDO::PARAM_INT);

                    if( $query->execute () ) {

                        $data = $query->fetch (PDO::FETCH_ASSOC);

                        return !empty($data) ? self::create ($data['id'], $formula) : NULL;
                    }
                }
            }
        }

        public function getFormula () {

            return $this->formula;
        }

        //Get Activation value
        public function isActive () { return $this->active; }

        /**
         * Create an instance in this realm.
         *
         * @param null|int $id
         * @param string   $formulaType
         * @return TextHeight()
         */
        public static function create ($id = NULL, $formulaType = NULL) { return new self ($id, $formulaType); }
    }