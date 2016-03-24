<?php


class PropertySort {

    /**
     * @var string $sortByKey
     */
    private $sortByKey;

    /**
     * @var string $sortByValue
     */
    private $sortByValue;

    /**
     * Our constructor
     *
     * @param string $sort
     */
    public function __construct ($sort) {

        $this->sortByValue = !empty($sort) ? trim($sort) : NULL;
    }

    /**
     * Sort by material regular array position
     *
     * @param $a
     * @param $b
     * @return int
     */
    public function sortByValueArr ($a, $b) {

        return $a[$this->getSortByValue ()] > $b[$this->getSortByValue()];
    }

    /**
     * @todo: Change me, method should be dynamic
     *
     * This should be dynamic
     *
     * @param array()
     * @param array()
     * @return bool
     */
    public function sortByValueObj($a, $b) {

        return $a->getPosition () > $b->getPosition ();
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public function sortMaterialCategoryTableByValueObj ($a, $b) {

        if( isset($a['materialCategory'], $b['materialCategory']) ) {

            return $a['materialCategory']->getPosition () > $b['materialCategory']->getPosition ();
        }

        return FALSE;
    }

    public function sortMaterialGroupTableByValue ($a, $b) {

        return $a['materialGroup']->getPosition() > $b['materialGroup']->getPosition();
    }

    /**
     * Sort by multiple columns in the array
     *
     * @param array $multiArray
     * @return array
     */
    public function multiSortByValues($multiArray) {

        foreach ($multiArray as $key => $row) {

            $firstKey[$key]  = $row[$this->getSortByValue ()];
            $secondKey[$key] = $row['gposition'];
        }

        array_multisort($firstKey, SORT_REGULAR, $secondKey, SORT_REGULAR, $multiArray);

        return $multiArray;
    }

    /**
     * @param array $objArray
     * @return array
     */
    public function multiSortByValuesObj ($objArray) {

        $firstKey = array ();
        $secondKey = array ();

        //we will need a recursive function
        foreach ($objArray as $groupId => $groupMaterials) {

            foreach ($groupMaterials as $materialIndex => $material) {

                foreach ($material as $index => $values) {

                    if( !empty($values) ) {

                        $firstKey[$groupId][$materialIndex][$index]  = $values->getCategoryPosition ();
                        $secondKey[$groupId][$materialIndex][$index] = $values->getGroupPosition ();
                    }
                }
            }
            array_multisort (
                $firstKey[$groupId],  SORT_REGULAR,
                $secondKey[$groupId], SORT_REGULAR, $objArray[$groupId]
            );
        }

        return $objArray;
    }

    public function multiSortMaterialTable($objArray) {

       foreach ($objArray as $index => $data) {

            if( isset($objArray['materialCategory']) ) {

                $firstKey['materialCategory'] = $objArray['materialCategory']->getPosition();
             }
        }

        array_multisort ($firstKey, SORT_REGULAR, $secondKey, SORT_REGULAR, $objArray);

        return $objArray;
    }

    /*******************************************
     *  Start Getters
     *******************************************
     * @return string
     */
    public function getSortByKey () {

        return $this->sortByKey;
    }

    /**
     * @return string
     */
    public function getSortByValue () {

        return $this->sortByValue;
    }

    public function create($sort = NULL) {

        return new self($sort);
    }

}