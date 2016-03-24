<?php

/**
 * Usage: ClassLoader::init();
 * In some cases you may
 * want to set a constant pointing to the actual directory
 * where all the classes are located.
 *
 * @var $classLocation This is where all the class files are located
 * @var $className The class name we want to load.
 *      This uses the class name to load the class file so the
 *      class file must have the same name as the class itself.
 *      You may also add extentions to the file but make sure to add
 *      this extention to is_readable / require_once.
 */

/**
 * WARNING: It is best not to add this file on the same directory as
 *          the class files it is going to load.
 */

 class ClassLoader {

 	/**
 	 * The autoloader() method will search a given directory and load
 	 * the selected class file
 	 *
 	 * @param string $className The name of the class file we want to load
 	 * @return file Include our file if it is found in its local space
 	 * @return exception Error if class not found
 	 */
 	private static function autoloader($className) {

 		$filePath = APP_ROOT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class_' . $className . '.inc.php';

 		try {

 			//Check if class file exists and is readable.
	 		if ( is_readable($filePath) ) {

	 			//Load the class file
	 			require_once "{$filePath}";

	 		} else {

	 			error_log('Directory or file is not readable!');
	 		}

	 	} catch ( Exception $e ) {

	 		error_log($e->getTraceAsString());
	 	}
 	}

 	/**
 	 * @spl_autoload_register:
 	 * 		Make sure we have nothing registered first or
 	 *   	Register given function as __autoload() implementation
 	 *
 	 * @spl_autoload_extensions:
 	 * 		Register and return default file extensions for spl_autoload
 	 *   	example: .php, .class.php
  	 */
 	public static function init() {
 		spl_autoload_register(null, false);
 		spl_autoload_extensions(".inc.php");
 		spl_autoload_register("self::autoloader");
 	}

 	/**
 	 * Return all registered __autoload() functions
 	 *
 	 * @return array Return an array with class files that are being loaded.
 	 */
 	public function showLoadedComponants() {

		return '<pre>'.print_r(spl_autoload_functions(), true).'</pre>';
	}

 }

