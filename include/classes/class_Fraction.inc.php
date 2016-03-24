<?php

class Fraction  {

    /**
     * @var $value - The given value to convert.
     */
    private $value;

    /**
     * @var $fractionalValues - An array containing preset fractional values
     * with their respective glyphs.
     */
    private $fractionalValues = array(
        '1/4' => '¼',
        '1/2' => '½',
        '3/4' => '¾',
        '1/3' => '⅓',
        '2/3' => '⅔',
        '1/5' => '⅕',
        '2/5' => '⅖',
        '3/5' => '⅗',
        '4/5' => '⅘',
        '1/6' => '⅙',
        '5/6' => '⅚',
        '1/8' => '⅛',
        '3/8' => '⅜',
        '5/8' => '⅝',
        '7/8' => '⅞'
    );

	public function __construct($value) {

        $this->value = $value;

	}

    public function getAsText() {

        $fractionValue = '';

        $whole = floor($this->value);
        $decimal = $this->value - $whole;

        $leastCommonDenom = 32;
        $denominators = array(1, 2, 3, 4, 5, 6, 8, 16, 32);

        if ( $whole > 0 || round($decimal * $leastCommonDenom) > 0 ) {

            $roundedDecimal = round($decimal * $leastCommonDenom) / $leastCommonDenom;

            if ( $roundedDecimal == 0 ) {

                $fractionValue = $whole;

            } else {

                if ($roundedDecimal == 1)
                    $whole = $whole + 1;

                foreach ( $denominators as $d ) {

                    if ( $roundedDecimal * $d == floor($roundedDecimal * $d) ) {

                        $denom = $d;

                        break;

                    }

                }

                $numerator = $roundedDecimal * $denom;

                //Simplify
                $gcd = $this->gcd($numerator, $denom);

                $fraction = ($numerator/$gcd) . "/" . $denom/$gcd;

                $found = false;

                foreach ( $this->fractionalValues as $key => $glyph ) {

                    if ( $fraction == $key ) {

                        $fraction = $glyph;

                        $found = true;

                        break;

                    }

                }

                $fractionValue = ($whole == 0 ? '' : $whole) . (!$found ? ' ' : '') . $fraction;
            }

        } else {

            $fractionValue = $this->value;

        }

        return $fractionValue;

    }

    /**
     * Returns the greatest common denominator between two numbers
     *
     * @param int $a - the numerator
     * @param int $b - the denominator
     *
     * @return int $b - greatest common denominator
     *
     */
    function gcd($a,$b) {

        $a = abs($a); $b = abs($b);

        if( $a < $b) list($b,$a) = Array($a,$b);

        if( $b == 0) return $a;

        $r = $a % $b;

        while( $r > 0 ) {

            $a = $b;
            $b = $r;
            $r = $a % $b;

        }

        return $b;

    }

	public static function create($value = NULL) {
		return new self($value);
	}

}