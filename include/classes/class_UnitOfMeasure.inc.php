<?php


require_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

class UnitOfMeasure
{
    /**
     * The canonical name for this unit of measure.
     *
     * Typically this is the official way the unit is abbreviated.
     *
     * @var string
     */
    protected $name;

    /**
     * A collection of alias names that map to this unit of measure
     *
     * @var string[]
     */
    protected $aliases;

    /**
     * A value in this quantity's
     * native unit to this unit of measure.
     *
     * @var float
     */
    protected $fromNativeUnit;

    /**
     * A value in the native unit of the physical quantity.
     *
     * @var float
     */
    protected $toNativeUnit;

    /**
     * For the special case of units that have a linear conversion factor, this factory
     * method simplifies the construction of the unit of measure.
     *
     * For example the relationship between meters and feet is a simple multiplicative factor of
     * 0.3048 meters in a foot.  Converting back and forth between these two units is a matter of
     * multiplication or division by this scaling factor.
     *
     * To help in getting the multiplication and division right, toNativeUnitFactor
     * is the number you'd multiply this unit by to get to the native unit of measure.  In
     * other words:
     * 'Value in the native unit of measure' = 'Value in this unit of measure' * toNativeUnitFactor
     *
     * @param string $name               This unit of measure's canonical name
     * @param float  $toNativeUnitFactor The factor to scale the unit by where factor * base unit = this unit
     *
     * @return self
     */
    public static function linearUnitFactory($name, $toNativeUnitFactor) {
    	return new self($name, $toNativeUnitFactor);
    }

    /**
     * Configure this object's mandatory properties.
     *
     * @param string   $name           This unit of measure's canonical name
     * @param float    $toNativeUnit   Native unit Factor
     *
     */
    public function __construct($name, $toNativeUnit) {
        if (!is_string($name)) {
            trigger_error("Alias ($name) must be a string value.");
        }

        $this->name           = $name;
        $this->fromNativeUnit = 1 / $toNativeUnit;
        $this->toNativeUnit   = $toNativeUnit;
    }

    /**
     * Gets name of unit
     *
     * @return string $this->name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Adds alias to the unit of measure
     *
     * @param string $alias
     *
     */
    public function addAlias($alias) {
        if (!is_string($alias)) {
            trigger_error("Alias ($alias) must be a string value.");
        }

        $this->aliases[] = $alias;
    }

    /**
     * Retrieves all names of unit fo measure
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * TRUE if $unit is within list of aliases, FALSE if not.
     *
     * @param string $unit
     * @return bool
     *
     */
    public function isAliasOf($unit) {
        if (!is_string($unit)) {
            trigger_error("Alias ($unit) must be a string value.");
            exit;
        }

        return in_array($unit, $this->aliases);
    }

    /**
     * Convert from native unit to desired unit
     *
     * @param float $value
     * @return float $value
     *
     */
    public function convertValueFromNativeUnitOfMeasure($value) {
        if (!is_numeric($value)) {
           trigger_error("Alias ($value) must be a numeric value.");
        }

        return $value / $this->fromNativeUnit;
    }

    /**
     * Convert original unit to native unit
     *
     * @param float $value
     * @return float $value
     *
     */
    public function convertValueToNativeUnitOfMeasure($value) {
        if (!is_numeric($value)) {
            trigger_error("Value ($value) must be numeric.");
        }

        return $value / $this->toNativeUnit;
    }
}
