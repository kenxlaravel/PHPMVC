<?php

class BuilderImages {

	// Define private variables.
	private $basedir;
	private $cachedir = '/cache/builder/images';
	private $defaultimage = '/builderimages/error.png';
	private $imagetype;
	private $imageoptions;

	public function __construct($basedir = NULL) {

		// Determine the base directory and store it in a class variable.
		$this->basedir = isset($basedir) ? $basedir : $_SERVER['DOCUMENT_ROOT'];

		// Get the cache directory within the base directory and store it in the class variable.
		$this->cachedir = $this->basedir . $this->cachedir;

	}


	private function getRaster() {

		// Get the cache filename.
		$cachefile = $this->encodeRasterCacheName();

		// If the cache file exists, serve it.
		if (file_exists($cachefile)) {

			$filename = readlink($cachefile);

		// If the cache file does not exist, get it from the database.
		} else {

			// Build the database query based on the type of image.
			switch ($this->imagetype) {
				case 'artwork': $sql = 'SELECT raster_url FROM bs_builder_images_artwork WHERE artwork_ref = :artwork AND color_ref = :color AND active = 1 LIMIT 1'; break;
				case 'background': $sql = 'SELECT raster_url FROM bs_builder_images_backgrounds WHERE size_ref = :size AND scheme_ref = :scheme AND active = 1 LIMIT 1'; break;
				case 'option': $sql = 'SELECT raster_url FROM bs_builder_images_options WHERE option_ref = :option AND optionvalue_ref = :optionvalue AND size_ref = :size AND active = 1 LIMIT 1'; break;
			}

			// Execute the database query.
			$sth = Connection::getHandle()->prepare($sql);
			$sth->execute($this->imageoptions);

			$result = $sth->fetchColumn();

			// If a result was returned from the database, grab it.
			if ($result) {
				$dbfile = $this->basedir . $result;

			}

			// If the the image from the database exists, use it.
			if (file_exists($dbfile)) {

				// Create a symlink for the cache and serve that (unless creating the symlink failed, then just serve the image from the database).
				$this->createCacheFile($dbfile, $cachefile);
				$filename = $dbfile;

			// If the image from the database does not exist, serve the default image.
			} else {
			
				$filename = $this->basedir . $this->defaultimage;

			}

		}

		return $filename;

	}
	private function getVector() {
			// Connect to the database.

			// Build the database query based on the type of image.
			switch ($this->imagetype) {
				case 'artwork': $sql = 'SELECT vector_url FROM bs_builder_images_artwork WHERE artwork_ref = :artwork AND color_ref = :color AND active = 1 LIMIT 1'; break;
				case 'option': $sql = 'SELECT vector_url FROM bs_builder_images_options WHERE  option_ref = :option AND optionvalue_ref = :optionvalue AND size_ref = :size AND active = 1 LIMIT 1'; break;
				case 'background': $sql = 'SELECT vector_url FROM bs_builder_images_backgrounds WHERE size_ref = :size AND scheme_ref = :scheme AND active = 1 LIMIT 1'; break;
			}

			// Execute the database query.
			$sth = Connection::getHandle()->prepare($sql);
			$sth->execute($this->imageoptions);
			$result = $sth->fetchColumn();

			// If a result was returned from the database, grab it.
			if ($result) {
				$dbfile = $this->basedir . $result;
			}

		return $dbfile;

	}
	private function getPDF()
	{

			// Build the database query based on the type of image.
			switch ($this->imagetype) {
				case 'artwork': $sql = 'SELECT pdf_url FROM bs_builder_images_artwork WHERE artwork_ref = :artwork AND color_ref = :color AND active = 1 LIMIT 1'; break;
				case 'background': $sql = 'SELECT pdf_url FROM bs_builder_images_backgrounds WHERE size_ref = :size AND scheme_ref = :scheme AND active = 1 LIMIT 1'; break;
			}
			// Execute the database query.
			$sth = Connection::getHandle()->prepare($sql);
			$sth->execute($this->imageoptions);
			$result = $sth->fetchColumn();

			// If a result was returned from the database, grab it.
			if ($result) {
				$dbfile = $this->basedir . $result;
			}
		return $dbfile;
	}
	private function encodeRasterCacheName() {

		// MD5 the image's options (id, value, size, scheme, etc.) to create the filename.
		$filename = md5(implode('.', $this->imageoptions));

		// Determine the directory for this cache file, based on the first two characters of the filename.
		$directory = $this->cachedir . '/' . substr($filename, 0, 2);

		// Return the full file path of the cache file.
		return $directory . '/' . $filename;

	}

	private function createCacheFile($dbfile, $cachefile) {

		// Get the cache file's subdirectory.
		$subdirectory = pathinfo($cachefile, PATHINFO_DIRNAME);

		// Check and fix the permissions on the directory and subdirectory if necessary.
		foreach (array($this->cachedir, $subdirectory) as $directory) {

			// If the directory doesn't already exist, try to create it (return false on failure).
			if (!is_dir($directory)) {
				if (!mkdir($directory, 0777, true)) {
					return false;
				}
			}

			// If the directory has the wrong permissions, try fixing them (return false on failure).
			if (!is_writable($directory)) {
				if (!chmod($directory, 0777)) {
					return false;
				}
			}

		}

		// Try creating the symlink (return false on failure).
		if (!symlink($dbfile, $cachefile)) {
			return false;
		}

		// If the function got here it means the cache file was successfully created, so return true.
		return true;

	}

	private function getRelativeFilename($filename) {

		if (strpos($filename, $this->basedir) === 0) {
			return substr($filename, strlen($this->basedir));
		}

	}

	private function updateCacheFile($filename) {

		// Get the filenames.
		$cachefile = $this->encodeRasterCacheName();
		$dbfile = $this->basedir . $filename;

		$cache_exists = is_link($cachefile);
		$cache_correct = ($cacheexists && readlink($cachefile) === $dbfile);

		// If the cache isn't already correct...
		if (!$cache_correct) {

			// If an old cache file already exists, delete it.
			if ($cache_exists) { unlink($cachefile); }

			// If database file exists, .
			if (file_exists($dbfile)) { $this->createCacheFile($dbfile, $cachefile); }

		}

	}

	private function setArtworkImageOption($id,$color)
	{
		$this->imagetype = 'artwork';
		$this->imageoptions=array(
			'artwork' => $id,
			'color' => $color
		);

	}
	private function setOptionImageOption($id, $value, $size)
	{
		$this->imagetype = 'option';
		$this->imageoptions = array(
			'option' => $id,
			'optionvalue' => $value,
			'size' => $size
		);

	}
	private function setBackgroundImageOption($size,$scheme)
	{
		$this->imagetype = 'background';
		$this->imageoptions = array(
			'size' => $size,
			'scheme' => $scheme
		);
	}

	public function getVectorArtwork($id, $color) {

		$this->setArtworkImageOption($id,$color);
		// Return the vector image.
		return $this->getVector();

	}

	public function getVectorOption($id, $value, $size) {

		$this->setOptionImageOption($id, $value, $size);
		// Return the vector image.
		return $this->getVector();

	}

	public function getVectorBackground($size, $scheme) {

		$this->setBackgroundImageOption($size,$scheme);
		// Return the vector image.
		return $this->getVector();

	}

	public function getRasterArtwork($id, $color) {

		$this->setArtworkImageOption($id,$color);
		// Get the full filename.
		$filename = $this->getRaster();

		// Return the relative filename.
		return $this->getRelativeFilename($filename);

	}

	public function getRasterBackground($size, $scheme) {

		$this->setBackgroundImageOption($size,$scheme);

		// Get the full filename.
		$filename = $this->getRaster();

		// Return the relative filename.
		return $this->getRelativeFilename($filename);

	}

	public function getRasterOption($id, $value, $size) {

		$this->setOptionImageOption($id, $value, $size);
		// Get the full filename.
		$filename = $this->getRaster();

		// Return the relative filename.
		return $this->getRelativeFilename($filename);

	}

	public function getPDFBackground($size, $scheme) {
		$this->setBackgroundImageOption($size,$scheme);
		// Return the PDF.
		return $this->getPDF();
	}
	public function getPDFArtwork($id, $color) {

		$this->setArtworkImageOption($id,$color);
		// Return the PDF.
		return $this->getPDF();
	}
	public function rebuildRasterCache() {

		// Artwork
		$sql_artwork = 'SELECT `artwork_ref` AS `artwork`, `color_ref` AS `color`, `raster_url` AS `filename` FROM bs_builder_images_artwork WHERE active = 1';
		foreach (Connection::getHandle()->query($sql_artwork) as $row) {

			$this->imagetype = 'artwork';
			$this->imageoptions = array(
				'artwork' => $row['artwork'],
				'color' => $row['color']
			);
			$this->updateCacheFile($row['filename']);

		}

		// Backgrounds
		$sql_backgrounds = 'SELECT `size_ref` AS `size`, `scheme_ref` AS `scheme`, `raster_url` AS `filename` FROM bs_builder_images_backgrounds WHERE active = 1';
		foreach (Connection::getHandle()->query($sql_backgrounds) as $row) {

			$this->imagetype = 'background';
			$this->imageoptions = array(
				'size' => $row['size'],
				'scheme' => $row['scheme']
			);
			$this->updateCacheFile($row['filename']);

		}

		// Options
		$sql_options = 'SELECT `option_ref` AS `option`, `optionvalue_ref` AS `optionvalue`, `size_ref` AS `size`, `raster_url` AS `filename` FROM bs_builder_images_options WHERE active = 1';
		foreach (Connection::getHandle()->query($sql_options) as $row) {

			$this->imagetype = 'option';
			$this->imageoptions = array(
				'option' => $row['option'],
				'optionvalue' => $row['optionvalue'],
				'size' => $row['size']
			);
			$this->updateCacheFile($row['filename']);

		}

	}

}