<?php

class Controller {

	/**
	 * Define the path where template files are stored relative to the app root.
	 */
	const CONTROLLER_PATH = 'process';

	/**
	 * Define file suffix to be concatenated to controller name
	 */
	const FILE_SUFFIX = '-controller.php';

	/**
	 * @var mixed[] $variables An associative array of variables to be given to the template. The
	 * keys will be used as the variable names and the values will be used as the variable values.
	 * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
	 * `3` for use within the template.
	 */
	private $variables;

	/**
	 * @var string|null $file The controller filename.
	 */
	private $file;

	/**
	 * $data      [$pageType, $pageId, urlParameters = array()]
	 * $fileName  [Name of controller file, do not include '.php' ]
	 *
     * @param string $controllerName
     * @param array $variables
	 */
	public function __construct($controllerName = NULL, $variables = NULL){

		$file = $this->getFileName($controllerName);
		$this->setFile($file)->setVariables($variables);
	}

	/**
	 * Concatenate controller file suffix ( FILE_SUFFIX ) to controller name.
	 *
	 * @param null $controllerName
	 *
	 * @return string
	 */
	private function getFileName($controllerName = NULL) {
		return $controllerName . self::FILE_SUFFIX;
	}


	/**
	 * Set the controller filename.
	 *
	 * @param string $file The controller filename relative to the site-wide template path; no leading slash.
	 *
	 * @return self
	 */
	private function setFile($file = NULL){

		if ( !empty($file) ) {

			$filePath = APP_ROOT . DIRECTORY_SEPARATOR . self::CONTROLLER_PATH .
				DIRECTORY_SEPARATOR . $file;

		}

		// If the file does not exist, trigger a warning and reset the file path to null.
		if ( !file_exists($filePath) ) {
			trigger_error('Cannot find Controller file: \'' . $filePath);
			$filePath = NULL;
		}

		$this->file = $filePath;

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
	public function setVariables($variables = NULL) {

		if ( !empty($variables) ) {
			foreach ( $variables as $name => $value ) {
				$this->setVariable($name, $value);
			}
		}

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
	public function setVariable($name, $value = NULL) {

		if ( !empty($name) ) {
			$this->variables[(string) $name] = $value;
		}

		return $this;

	}

	/**
	 * Generate the template using the supplied file and variables and return the content.
	 *
	 * @return string The generated template content. Note that an empty string will be returned
	 * if this function is called before supplying a template file.
	 */
	public function getContent($getGlobalVariables) {

		if ( isset($this->file) ) {

			if ( $getGlobalVariables ) {

				// Get Global-esque variables
				include_once APP_ROOT . DIRECTORY_SEPARATOR . self::CONTROLLER_PATH . DIRECTORY_SEPARATOR . 'global-controller.php';

				// Set variables from global-controller
				$this->setVariables($resultsOfGlobalController);

			}

			// Extract the variables from the array.
			extract($this->variables);

			include_once "$this->file";

		}

	}

	/**
	 * Create a new Template object using the supplied file and variables.
	 *
	 * @param string $controllerName The template filename relative to the site-wide template path; no leading slash.
	 *
	 * @param mixed[] $variables An associative array of variables to be given to the template. The
	 * keys will be used as the variable names and the values will be used as the variable values.
	 * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
	 * `3` for use within the template.
	 *
	 * @return Template
	 */
	public static function create($controllerName = NULL, $variables = NULL) {
		return new self($controllerName, $variables);
	}


	/**
	 * Generate a template using the supplied file and variables and return the content.
	 *
	 * @param string $controllerName The template filename relative to the site-wide template path; no leading slash.
	 *
	 * @param mixed[] $variables An associative array of variables to be given to the template. The
	 * keys will be used as the variable names and the values will be used as the variable values.
	 * For instance, `array( 'myVar' => 3 )` will create a variable named `$myVar` with a value of
	 * `3` for use within the template.
	 *
	 * @return Controller
	 */
	public static function make($controllerName = NULL, $variables = NULL, $getGlobalVariables = TRUE) {

		return self::create($controllerName, $variables)->getContent($getGlobalVariables);

	}

}