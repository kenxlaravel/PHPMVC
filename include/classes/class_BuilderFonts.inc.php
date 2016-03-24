<?php


class BuilderFonts {

	private $cachefile = '/cache/builder/fonts/styles.css';

	public function __construct($basedir) {

		if (!isset($basedir)) { $basedir = $_SERVER['DOCUMENT_ROOT']; }

		$this->cachefile = $basedir . $this->cachefile;

	}

	public function getCss() {

		// If the cache file exists, serve it.
		if (file_exists($this->cachefile)) {

			$css = file_get_contents($this->cachefile);

		// If the cache file does not exist, build it from the database.
		} else {

			$css = $this->buildCss();
			$this->createCacheFile($css);

		}

		return $css;

	}

	private function buildCss() {

		// Prepare output variables.
		$imports = '';
		$declarations = '';

		// Prepare the database query.
		$sql = Connection::getHandle()->query('SELECT font_ref AS ref,
		                           fallback AS fallback,
		                           filename_ttf AS ttf,
		                           filename_eot AS eot,
		                           filename_woff AS woff
		                    FROM bs_fonts
		                    WHERE active = TRUE AND font_ref != ""
		                    ORDER BY font_ref ASC');

		// Execute the query and loop through the results.
        while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			$imports .= '@font-face { ';
			$imports .= 'font-family: \'' . $row['ref'] . '\'; ';
			$imports .= 'src: url(\'' . $row['eot'] . '\'); ';
			$imports .= 'src: local(\'☺\'), ';
			$imports .= 'url(\'' . $row['eot'] . '#iefix\') format(\'embedded-opentype\'), ';
			$imports .= 'url(\'' . $row['woff'] . '\') format(\'woff\'), ';
			$imports .= 'url(\'' . $row['ttf'] . '\') format(\'truetype\'); ';
			$imports .= 'font-weight: normal; font-style: normal; ';
			$imports .= '} ';

			$declarations .= '.builderstyle .font-' . $row['ref'] . ' { font-family: \'' . $row['ref'] . '\', ' . $row['fallback'] . '; } ';

		}

		// Combine the imports and declarations to create the full CSS.
		$css = $imports . $declarations;

		// Output the CSS.
		return $css;

	}

	private function createCacheFile($data) {

		// Get the cache file's directory.
		$directory = pathinfo($this->cachefile, PATHINFO_DIRNAME);

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

		// Try creating the cache file (return false on failure).
		if (!file_put_contents($this->cachefile, $data)) {
			return false;
		}

		// Try to fix the cache file permissions, but don't return false on failure; just proceed.
		chmod($this->cachefile, 0777);

		// If the function got here it means the cache file was successfully created, so return true.
		return true;

	}

	public function rebuildCss() {

		// Delete the old CSS file.
		if (file_exists($this->cachefile)) { unlink($this->cachefile); }

		// Create the new CSS file.
		$css = $this->buildCss();
		$this->createCacheFile($css);

	}

}