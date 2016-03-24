<?php

// TODO: Finish this class.

// Load configuration.
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

class Mass extends PhysicalQuantity
{

    /**
     * Configure all the standard units of measure
     * to which this quantity can be converted.
     *
     * @return void
     */
    public function __construct($value, $unit) {

        parent::__construct($value, $unit);

        // Ton (metric)
        $newUnit = UnitOfMeasure::linearUnitFactory('t', 0.00045);
        $newUnit->addAlias('ton');
        $newUnit->addAlias('tons');
        $newUnit->addAlias('tonne');
        $newUnit->addAlias('tonnes');
        $this->registerUnitOfMeasure($newUnit);

        // Pound (Native)
        $newUnit = UnitOfMeasure::linearUnitFactory('lb', 1);
        $newUnit->addAlias('lbs');
        $newUnit->addAlias('pound');
        $newUnit->addAlias('pounds');
        $this->registerUnitOfMeasure($newUnit);

        // Ounce
        $newUnit = UnitOfMeasure::linearUnitFactory('oz', 16);
        $newUnit->addAlias('ounce');
        $newUnit->addAlias('ounces');
        $this->registerUnitOfMeasure($newUnit);

        // Kilogram
        $newUnit = UnitOfMeasure::linearUnitFactory('kg', 0.45359);
        $newUnit->addAlias('kilogram');
        $newUnit->addAlias('kilograms');
        $this->registerUnitOfMeasure($newUnit);

    }

    public static function create($value = NULL, $unit = NULL) {
        return new self($value, $unit);
    }
}