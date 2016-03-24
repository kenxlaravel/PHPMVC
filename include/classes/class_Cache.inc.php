<?php


class Cache {

	private $cachedir = '/cache';
	public $errors;

	public function __construct($subdirectory, $basedir) {

		// Determine the base directory and store it in a class variable.
		if (!isset($basedir)) { $basedir = $_SERVER['DOCUMENT_ROOT']; }

		// Get the cache directory within the base directory and store it in the class variable.
		$this->cachedir = $basedir . $this->cachedir;

		// If a subdirectory was supplied, add that to the class variable.
		if (isset($subdirectory)) { $this->cachedir .= '/' . $subdirectory; }

	}

	private function cleanDirectory($directory, $cachelife) {

		// Check if the directory an be opened.
		if (!$dirhandle = opendir($directory)) {

			$this->errors[] = 'Could not open ' . $directory . '.';

		} else {

			// Scan through the directory
			while (($filename = readdir($dirhandle)) !== false) {

				// Make sure not to clear Linux current/parent entries.
				if ($filename == '.' || $filename == '..') { continue; }

				$filename = $directory . '/' . $filename;

				if (is_dir($filename)) {

					// If the current file is actually a directory, look through that directory for old files.
					$this->cleanDirectory($filename, $cachelife);

				} else {

					// If the current file is not a directory, check its age.
					if (filemtime($filename) < (time() - $cachelife)) {

						// If the file is too old, delete it.
						if (!unlink($filename)) { $this->errors[] = 'Could not delete ' . $filename . '.'; }

					}

				}

			}

		}

	}

	public function cleanCache($cachelife=604800) {

		$this->errors = array();
		$this->cleanDirectory($this->cachedir, $cachelife);

	}

	public function emptyCache() {

		$this->cleanCache(0);

	}

}