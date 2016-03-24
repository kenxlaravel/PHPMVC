<?php

class Brimarkup {



    const PARSER_REGEX = '/\\[\\[pageUrl ([a-zA-Z0-9_-]+)(?: ([0-9]+))?(?: ([a-zA-Z]+))?\\]\\]/';



    private $input;
    private $parsed;



   /**
    * Instantiate the object and parse the input argument.
    *
    * @param string $input The unparsed input in Brimarkup format.
    * @return self The Brimarkup object.
    */
    public function __construct($input) {
        $this->input = (string) $input;
        $this->parsed = preg_replace_callback(self::PARSER_REGEX, array('self', 'parserCallback'), $input);
    }



   /**
    * Get the parsed result.
    *
    * @return string The parsed result.
    */
    public function getParsed() {
        return $this->parsed;
    }



   /**
    * Get the page URL based on the the supplied nickname or page type and ID
    * combination. Used as a callback on main preg_replace_callback in the
    * constructor.
    *
    * @param string[] $matches The matches found the preg search.
    * @return self The dynamically-generated page URL.
    */
    private static function parserCallback($matches) {
        $parsed = empty($matches[2]) ? Page::getPageUrlFromNickname($matches[1]) : Page::getPageUrlFromTypeAndId($matches[1], $matches[2]);
        return mb_strtolower($matches[3]) === 'html' ? htmlspecialchars($parsed, ENT_QUOTES, 'UTF-8') : $parsed;
    }



   /**
    * Convenience function to create a new Brimarkup object and get the parsed
    * result back in a single function call.
    *
    * @param string $input The unparsed input in Brimarkup format.
    * @return string The parsed result.
    */
    public static function parse($input) {
        return self::create($input)->getParsed();
    }



   /**
    * Convenience function to create and return a new Brimarkup object.
    *
    * @param string $input The unparsed input in Brimarkup format.
    * @return Brimarkup The Brimarkup object.
    */
    public static function create($input) {
        return new self($input);
    }



}
