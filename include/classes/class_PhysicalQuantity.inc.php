<?php


// Load configuration.
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

/**
 * This class is the parent of all the physical quantity classes, and
 * provides the infrastructure necessary for storing quantities and converting
 * between different units of measure.
 */
class PhysicalQuantity
{
    /**
     * The scalar value, in the original unit of measure.
     *
     * @var float
     */
    protected $originalValue;

    /**
     * The original unit of measure's string representation.
     *
     * @var string
     */
    protected $originalUnit;

    /**
     * The collection of units of measure in which this value can
     * be represented.
     *
     * @var array
     */
    protected $unitDefinitions = array();

    /**
     * Store the value and its original unit.
     *
     * @param float  $value The scalar value of the measurement
     * @param string $unit  The unit of measure in which this value is provided
     *
     */
    public function __construct($value, $unit) {
        if (!is_numeric($value) && !is_null($value)) {
            trigger_error("Alias ($value) must be a numeric value.");
            exit;
        }

        if (!is_string($unit) && !is_null($unit)) {
            trigger_error("Alias ($value) must be a string value.");
            exit;
        }

        $this->originalValue = $value;
        $this->originalUnit = $unit;
    }


    /**
     * Register a new Unit of Measure with this quantity.
     *
     * The intended use is to register a new unit of measure to which measurements
     * of this physical quantity can be converted.
     *
     * @param string $unit The new unit of measure
     *
     * @return void
     */
    public function registerUnitOfMeasure($unit) {

        $currentUnits = $this->getSupportedUnits(true);

        $newUnitName = $unit->getName();

        if (in_array($newUnitName, $currentUnits)) {
            trigger_error('The unit name ('.$newUnitName.') is already a registered unit for this quantity');
        }

        $newAliases = $unit->getAliases();

        foreach ($newAliases as $newUnitAlias) {
            if (in_array($newUnitAlias, $currentUnits)) {
                trigger_error('The unit alias ('.$newUnitAlias.') is already a registered unit for this quantity');
            }
        }

        $this->unitDefinitions[] = $unit;

    }

    /**
     * Fetch the measurement, in the given unit of measure
     *
     * @param  string $units The desired unit of measure
     *
     * @return string        The measurement cast in the requested units
     */
    public function toUnit($units) {
        $originalUnit    = $this->findUnitOfMeasureByNameOrAlias($this->originalUnit);
        $nativeUnitValue = $originalUnit->convertValueToNativeUnitOfMeasure($this->originalValue);

        $toUnit      = $this->findUnitOfMeasureByNameOrAlias($units);
        $toUnitValue = $toUnit->convertValueFromNativeUnitOfMeasure($nativeUnitValue);

        if ( $units == 'mm' ) {

            $roundedValue = round($toUnitValue, 1);

        } else if ( $units == 'feet' ) {

            $roundedValue = round($toUnitValue, 2);

        }
        else {

            $roundedValue = round($toUnitValue, 3);

        }

        return $roundedValue;
    }

    /**
     * Get the list of all supported unit names, with the option
     * to include the units' aliases as well.
     *
     * @param boolean $withAliases Include all the unit alias names in the list
     *
     * @return array the collection of unit names
     */
    public function getSupportedUnits($withAliases = false) {
        $supportedUnits = array();
        foreach ($this->unitDefinitions as $unitOfMeasure) {
            $supportedUnits[] = $unitOfMeasure->getName();
            if ($withAliases) {
                foreach ($unitOfMeasure->getAliases() as $alias) {
                    $supportedUnits[] = $alias;
                }
            }
        }

        return $supportedUnits;
    }

    /**
     * Add a given quantity to this quantity, and return a new quantity object.
     *
     * Note that the new quantity's original unit will be the same as this object's.
     *
     * @param PhysicalQuantity $quantity The quantity to add to this one
     *
     * @return PhysicalQuantity the new quantity
     */
    public function add($quantity) {
        $this->originalValue = $this->originalValue + $quantity;

        return $this->originalValue;
    }

    /**
     * Subtract a given quantity from this quantity, and return a new quantity object.
     *
     * Note that the new quantity's original unit will be the same as this object's.
     *
     * @param int $quantity The quantity to subtract from this one
     *
     * @return int $quantity the new quantity
     */
    public function subtract($quantity) {
        return $this->originalValue = $this->originalValue - $quantity;
    }

    /**
     * Get the unit definition that matches the given unit of measure name.
     *
     * Note that this can match either the index or the aliases.
     *
     * @param  string $unit The starting unit of measure
     *
     * @throws string error when an unknown unit of measure is given
     *
     * @return UnitOfMeasure
     */
    protected function findUnitOfMeasureByNameOrAlias($unit) {
        $unit = strtolower($unit);
        foreach ($this->unitDefinitions as $unitOfMeasure) {
            if ($unit === $unitOfMeasure->getName() || $unitOfMeasure->isAliasOf($unit)) {
                return $unitOfMeasure;
            }
        }

        trigger_error("Unknown unit of measure ($unit)");
    }

    public static function create($value = NULL, $unit = NULL) {
        return new self($value, $unit);
    }

}
