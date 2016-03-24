<?

// TODO: Finish this class.

class Length extends PhysicalQuantity {

    /**
     * @var $value - The given value to convert
     */
    private $value;

    /**
     * @var - The unit of measure of the give value ($value)
     */
    private $unit;

    private $footUnit;

    private $inchUnit;

    private $meterUnit;

    private $mileUnit;

    private $yardUnit;

    private $pointUnit;

    private $mmUnit;

    private $milsUnit;

    /**
     * Configure all the standard units of measure
     * to which this quantity can be converted.
     * Base unit is Meters
     *
     * @param float $value
     * @param string unit
     *
     */
	public function __construct($value, $unit) {

        $this->unit = $unit;
        $this->value = $value;

        parent::__construct($value, $unit);

	    // Foot
        $this->footUnit = UnitOfMeasure::linearUnitFactory('ft', 0.08333);
        $this->footUnit->addAlias('foot');
        $this->footUnit->addAlias('feet');
        $this->registerUnitOfMeasure($this->footUnit);

        // Inch (native)
        $this->inchUnit = UnitOfMeasure::linearUnitFactory('in', 1);
        $this->inchUnit->addAlias('inch');
        $this->inchUnit->addAlias('inches');
        $this->registerUnitOfMeasure($this->inchUnit);

        // Meter
        $this->meterUnit = UnitOfMeasure::linearUnitFactory('m', 0.0254);
        $this->meterUnit->addAlias('meter');
        $this->meterUnit->addAlias('meters');
        $this->registerUnitOfMeasure($this->meterUnit);

        // Mile
        $this->mileUnit = UnitOfMeasure::linearUnitFactory('mil', 1000);
        $this->mileUnit->addAlias('mile');
        $this->mileUnit->addAlias('miles');
        $this->registerUnitOfMeasure($this->mileUnit);

        // Yard
        $this->yardUnit = UnitOfMeasure::linearUnitFactory('yd', 0.02777);
        $this->yardUnit->addAlias('yard');
        $this->yardUnit->addAlias('yards');
        $this->registerUnitOfMeasure($this->yardUnit);

        // Point
        $this->pointUnit = UnitOfMeasure::linearUnitFactory('point', 72.57);
        $this->pointUnit->addAlias('point');
        $this->pointUnit->addAlias('points');
        $this->registerUnitOfMeasure($this->pointUnit);

        // mm
        $this->mmUnit = UnitOfMeasure::linearUnitFactory('mm', 25.4);
        $this->mmUnit->addAlias('mm');
        $this->registerUnitOfMeasure($this->mmUnit);

        // mils
        $this->milsUnit = UnitOfMeasure::linearUnitFactory('mils', 1000);
        $this->milsUnit->addAlias('mils');
        $this->milsUnit->addAlias('mils');
        $this->registerUnitOfMeasure($this->milsUnit);

	}

    /**
     * Gets the current value
     *
     * @return float $value
     *
     */
    public function getValue() {

        return $this->value;

    }

    /**
     * Gets the display value
     *
     * @param bool $showAsFraction - Whether or not to display decimal values as fractions
     * @param string $toUnit - the unit to convert to
     * @return string $value
     *
     */
    public function getDisplayValue($toUnit, $showAsFraction = TRUE) {

        $displayValue = $this->value;

        $unit = ' ' . $toUnit;

        if ($toUnit == 'feet') {
            $unit = '′';
        }

        if ($toUnit == 'inches') {
            $unit = '″';
        }

        if ( $displayValue != 0 ) {

            if (!$showAsFraction) {

                $displayValue = $this->toUnit($toUnit);

            } else {

                $displayValue = Fraction::create($this->toUnit($toUnit))->getAsText();

            }

        }

        return $displayValue.$unit;

    }

	public static function create($value = NULL, $unit = NULL) {
		return new self($value, $unit);
	}

}