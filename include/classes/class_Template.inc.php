<?php

/**
 * The Template class is used to generate output in a way that helps separate logic from templating
 * and guarantees that all expected variables will be available within the template while no
 * unexpected variables will be.
 */
class Template {

    /**
     * Extension
     */
    const EXTENSION = '.php';

    /**
     * Define the path where template files are stored relative to the app root.
     */
    const TEMPLATE_PATH = 'templates';

    /**
     * @var string|null $file The template filename.
     */
    private $file = NULL;

    /**
     * @var mixed[] $variables An associative array of variables to be given to the template. The
     * keys will be used as the variable names and the values will be used as the variable values.
     * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
     * `3` for use within the template.
     */
    private $variables = array();

    /**
     * Create a new Template object using the supplied file and variables.
     *
     * @param string $file The template filename relative to the site-wide template path; no leading slash.
     *
     * @param mixed[] $variables An associative array of variables to be given to the template. The
     * keys will be used as the variable names and the values will be used as the variable values.
     * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
     * `3` for use within the template.
     */
    public function __construct($file = NULL, $variables = NULL) {
        $this->setFile($file)->addVariables($variables);
    }

    /**
     * Set the template filename.
     *
     * @param string $file The template filename relative to the site-wide template path; no leading slash.
     *
     * @return self
     */
    public function setFile($file = NULL) {

        $filePath = NULL;

        if ( !empty($file) ) {

            // Assemble the full path to the file.
            $filePath = APP_ROOT . DIRECTORY_SEPARATOR . self::TEMPLATE_PATH . DIRECTORY_SEPARATOR . $file . self::EXTENSION;

            // If the file does not exist, trigger a warning and reset the file path to null.
            if ( !file_exists($filePath) ) {
                trigger_error('Cannot set Template file: \'' . $filePath . '\' cannot be found.');
                $filePath = NULL;
            }

        }

        $this->file = $filePath;

        return $this;

    }

    /**
     * Add a variable to be available within the template.
     *
     * @param string $name The name that will be given to the variable within the template. For instance,
     * `'myVar'` will make a variable named `$myVar` available within the template.
     *
     * @param mixed $value The value of the variable.
     *
     * @return self
     */
    public function addVariable($name, $value = NULL) {

        if ( !empty($name) ) {
            $this->variables[(string) $name] = $value;
        }

        return $this;

    }

    /**
     * Add multiple variables to be available within the template.
     *
     * @param mixed[] $variables An associative array of variables to be given to the template. The
     * keys will be used as the variable names and the values will be used as the variable values.
     * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
     * `3` for use within the template.
     *
     * @return self
     */
    public function addVariables($variables = NULL) {

        if ( !empty($variables) ) {
            foreach ( $variables as $name => $value ) {
                $this->addVariable($name, $value);
            }
        }

        return $this;

    }

    /**
     * Remove a previously-supplied variable.
     *
     * @param string $name The name of the variable to remove. For instance, `'myVar'` will prevent
     * the `$myVar` variable from being available within the template if it was previously defined
     * using `addVariable`, `addVariables`, or one of the constructor functions.
     *
     * @return self
     */
    public function removeVariable($name) {

        if ( !empty($name) ) {
            unset($this->variables[(string) $name]);
        }

        return $this;

    }

    /**
     * Remove multiple previously-supplied variables.
     *
     * @param string[] $names The names of the variables to remove. For instance,
     * `array( 'myVar', 'anotherVar' )` will prevent the `$myVar` and `$anotherVar` variables
     * from being available within the template if they were previously defined using
     * `addVariable`, `addVariables`, or one of the constructor functions. If an empty array is
     * supplied or this parameter is omitted, all previously-supplied variables will be removed.
     *
     * @return self
     */
    public function removeVariables($names = NULL) {

        if ( empty($names) ) {

            $this->resetVariables();

        } else {

            foreach ( $names as $name ) {
                $this->removeVariable($name);
            }

        }

        return $this;

    }

    /**
     * Remove all previously-supplied variables.
     *
     * @return self
     */
    public function resetVariables() {
        $this->variables = array();
        return $this;
    }

    /**
     * Generate the template using the supplied file and variables and return the content.
     *
     * @return string The generated template content. Note that an empty string will be returned
     * if this function is called before supplying a template file.
     */
    public function getContent() {

        ob_start();

        if ( isset($this->file) ) {

            // Extract the variables from the array.
            extract($this->variables);

            // Generate the Controller.
            include $this->file;

        }

        return ob_get_clean();

    }

    /**
     * Create a new Template object using the supplied file and variables.
     *
     * @param string $file The template filename relative to the site-wide template path; no leading slash.
     *
     * @param mixed[] $variables An associative array of variables to be given to the template. The
     * keys will be used as the variable names and the values will be used as the variable values.
     * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
     * `3` for use within the template.
     *
     * @return Template
     */
    public static function create($file = NULL, $variables = NULL) {
        return new self($file, $variables);
    }

    /**
     * Generate a template using the supplied file and variables and return the content.
     *
     * @param string $file The template filename relative to the site-wide template path; no leading slash.
     *
     * @param mixed[] $variables An associative array of variables to be given to the template. The
     * keys will be used as the variable names and the values will be used as the variable values.
     * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
     * `3` for use within the template.
     *
     * @return string The generated template content. Note that an empty string will be returned
     * if this function is called before supplying a template file.
     */
    public static function generate($file = NULL, $variables = NULL) {
        return self::create($file, $variables)->getContent();
    }

}
