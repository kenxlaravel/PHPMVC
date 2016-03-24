<?php

class Encoder {

    /**
     * Define the character set.
     */
    const CHARACTER_SET = 'UTF-8';

    /**
     * @var string $rawValue The raw value of the string as it was supplied.
     */
    private $rawValue = '';

    /**
     * Create a new Encoder object using the supplied string.
     *
     * @param string $string The raw input string.
     */
    public function __construct($string = '') {
        $this->setRawValue($string);
    }

    /**
     * Set the raw value of the input string.
     *
     * @param string $string The raw input string.
     *
     * @return self
     */
    public function setRawValue($string = '') {
        $this->rawValue = (string) $string;
        return $this;
    }

    /**
     * Get the raw value of the input string.
     *
     * @return string The raw input string.
     */
    public function getRawValue() {
        return $this->rawValue;
    }

    /**
     * Get the input string, encoded for use in HTML.
     *
     * @return string The HTML-encoded string.
     */
    public function getHtml() {
        return htmlspecialchars($this->rawValue, ENT_QUOTES, self::CHARACTER_SET);
    }

    /**
     * Create a new Encoder object using the supplied string.
     *
     * @param string $string The raw input string.
     *
     * @return Encoder
     */
    public static function create($string = '') {
        return new self($string);
    }

    /**
     * Encode and return the supplied string as HTML.
     *
     * @return string $string The raw, unencoded input string.
     *
     * @return string The HTML-encoded string.
     */
    public static function html($string = '') {
        return self::create($string)->getHtml();
    }

}
