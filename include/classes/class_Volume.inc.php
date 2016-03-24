<?php

// TODO: Finish this class.

// Load configuration.
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

class Volume extends PhysicalQuantity {

    /**
     * Configure all the standard units of measure
     * to which this quantity can be converted.
     *
     * @return void
     */
    public function __construct($value, $unit) {

        parent::__construct($value, $unit);

        // Cubic meter
        $newUnit = UnitOfMeasure::linearUnitFactory('m^3', 1);
        $newUnit->addAlias('m³');
        $newUnit->addAlias('cubic meter');
        $newUnit->addAlias('cubic meters');
        $newUnit->addAlias('cubic metre');
        $newUnit->addAlias('cubic metres');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic millimeter
        $newUnit = UnitOfMeasure::linearUnitFactory('mm^3', 1000000000);
        $newUnit->addAlias('mm³');
        $newUnit->addAlias('cubic millimeter');
        $newUnit->addAlias('cubic millimeters');
        $newUnit->addAlias('cubic millimetre');
        $newUnit->addAlias('cubic millimetres');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic centimeter
        $newUnit = UnitOfMeasure::linearUnitFactory('cm^3', 1000000);
        $newUnit->addAlias('cm³');
        $newUnit->addAlias('cubic centimeter');
        $newUnit->addAlias('cubic centimeters');
        $newUnit->addAlias('cubic centimetre');
        $newUnit->addAlias('cubic centimetres');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic decimeter
        $newUnit = UnitOfMeasure::linearUnitFactory('dm^3', 1000);
        $newUnit->addAlias('dm³');
        $newUnit->addAlias('cubic decimeter');
        $newUnit->addAlias('cubic decimeters');
        $newUnit->addAlias('cubic decimetre');
        $newUnit->addAlias('cubic decimetres');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic kilometer
        $newUnit = UnitOfMeasure::linearUnitFactory('km^3', 0.000000009);
        $newUnit->addAlias('km³');
        $newUnit->addAlias('cubic kilometer');
        $newUnit->addAlias('cubic kilometers');
        $newUnit->addAlias('cubic kilometre');
        $newUnit->addAlias('cubic kilometres');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic foot
        $newUnit = UnitOfMeasure::linearUnitFactory('ft^3', 35.3147);
        $newUnit->addAlias('ft³');
        $newUnit->addAlias('cubic foot');
        $newUnit->addAlias('cubic feet');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic inch
        $newUnit = UnitOfMeasure::linearUnitFactory('in^3', 61023.7);
        $newUnit->addAlias('in³');
        $newUnit->addAlias('cubic inch');
        $newUnit->addAlias('cubic inches');
        $this->registerUnitOfMeasure($newUnit);
        // Cubic yard
        $newUnit = UnitOfMeasure::linearUnitFactory('yd^3', 1.3079);
        $newUnit->addAlias('yd³');
        $newUnit->addAlias('cubic yard');
        $newUnit->addAlias('cubic yards');
        $this->registerUnitOfMeasure($newUnit);
        // Milliliters
        $newUnit = UnitOfMeasure::linearUnitFactory('ml', 1000000);
        $newUnit->addAlias('milliliter');
        $newUnit->addAlias('milliliters');
        $newUnit->addAlias('millilitre');
        $newUnit->addAlias('millilitres');
        $this->registerUnitOfMeasure($newUnit);
        // Centiliters
        $newUnit = UnitOfMeasure::linearUnitFactory('cl', 100000);
        $newUnit->addAlias('centiliter');
        $newUnit->addAlias('centiliters');
        $newUnit->addAlias('centilitre');
        $newUnit->addAlias('centilitres');
        $this->registerUnitOfMeasure($newUnit);
        // Deciliter
        $newUnit = UnitOfMeasure::linearUnitFactory('dl', 10000);
        $newUnit->addAlias('deciliter');
        $newUnit->addAlias('deciliters');
        $newUnit->addAlias('decilitre');
        $newUnit->addAlias('decilitres');
        $this->registerUnitOfMeasure($newUnit);
        // Liter
        $newUnit = UnitOfMeasure::linearUnitFactory('l', 1000);
        $newUnit->addAlias('liter');
        $newUnit->addAlias('liters');
        $newUnit->addAlias('litre');
        $newUnit->addAlias('litres');
        $this->registerUnitOfMeasure($newUnit);
        // Decaliter
        $newUnit = UnitOfMeasure::linearUnitFactory('dal', 100);
        $newUnit->addAlias('decaliter');
        $newUnit->addAlias('decaliters');
        $newUnit->addAlias('decalitre');
        $newUnit->addAlias('decalitres');
        $this->registerUnitOfMeasure($newUnit);
        // Hectoliter
        $newUnit = UnitOfMeasure::linearUnitFactory('hl', 10);
        $newUnit->addAlias('hectoliter');
        $newUnit->addAlias('hectoliters');
        $newUnit->addAlias('hectolitre');
        $newUnit->addAlias('hectolitres');
        $this->registerUnitOfMeasure($newUnit);
        // Cup
        $newUnit = UnitOfMeasure::linearUnitFactory('cup', 4226.75);
        $newUnit->addAlias('cup');
        $newUnit->addAlias('cups');
        $this->registerUnitOfMeasure($newUnit);
        //Fluid Oz
        $newUnit = UnitOfMeasure::linearUnitFactory('oz', 33814);
        $newUnit->addAlias('fluid oz');
        $newUnit->addAlias('us oz');
        $newUnit->addAlias('fluid ounce');
        $newUnit->addAlias('fluid ounces');
        $newUnit->addAlias('us ounce');
        $newUnit->addAlias('us ounces');
        $this->registerUnitOfMeasure($newUnit);

    }

    public static function create($value = NULL, $unit = NULL) {
        return new self($value, $unit);
    }
}


