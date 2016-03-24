<?php


 class FrontEndTemplateIncluder {

    private $handlebarsTemplates  = array();

    const FRONTEND_INCLUDE_PATH   = '/templates/frontend/';
    const HANDLEBARS_EXTENSION    = '.handlebars';

   /**
    * Adds a Handlebars template.
    *
    * @param mixed $templateName The basename of the Handlebars template to include. The file path
    * and extension are automatically added. The basename is also used as the JavaScript ID of the
    * template, so make sure the basename does not include any special characters. If the argument
    * is an array rather than a string, the function is called recursively added all templates
    * specified.
    *
    * @param boolean $brimarkup Whether or not to parse the file using Brimarkup after retrieving
    * the contents. Defaults to true.
    *
    * @return self
    */
    public function addHandlebarsTemplate($templateName, $brimarkup = TRUE) {

        if ( is_array($templateName) ) {

            foreach ( $templateName as $individualTemplateName ) {
                $this->addHandlebarsTemplate($individualTemplateName);
            }

        } elseif ( is_string($templateName) ) {

            $filename = APP_ROOT . self::FRONTEND_INCLUDE_PATH . $templateName . self::HANDLEBARS_EXTENSION;

            if ( file_exists($filename) ) {

                $fileContents = file_get_contents($filename);
                $this->handlebarsTemplates[$templateName] = $brimarkup ? Brimarkup::parse($fileContents) : $fileContents;

            }

        }

        return $this;

    }

   /**
    * Get the HTML string for all of the front-end templates added to the object.
    *
    * The HTML is pre-escaped so it can simply be echoed wherever necessary without requiring
    * `htmlspecialchars()` or similar.
    *
    * @return string
    */
    public function getHtml() {

        $html = '';

        foreach ( $this->handlebarsTemplates as $handlebarsTemplateName => $handlebarsTemplate ) {
            $html .= '<script data-handlebars-template-id="' . htmlspecialchars($handlebarsTemplateName, ENT_QUOTES, 'UTF-8') . '" type="text/x-handlebars-template">' . $handlebarsTemplate . '</script>';
        }

        return $html;
    }

     public static function create() {
         return new self();
     }
 }
