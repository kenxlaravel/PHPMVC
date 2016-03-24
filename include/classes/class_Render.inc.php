<?php



class Render {


	// Define private variables.
	private $basedir;
	private $cachedir = '/cache/renders';
	private $location;
	private $id;


	/**
	 * The constructor can take a basedir argument when run from the terminal. Otherwise basedir
	 * will default to the server document root.
	 *
	 * @param    string    $basedir    An optional base directory to run from
	 */
	public function __construct($basedir = NULL) {

		// Determine the base directory and store it in a class variable.
		$this->basedir = isset($basedir) ? $basedir : APP_ROOT;

		// Get the cache directory within the base directory and store it in the class variable.
		$this->cachedir = $this->basedir . $this->cachedir;

	}



	/**
	 * Checks if a cache file exists, and generates a file if not
	 *
	 * @param     string    the file hash
	 * @param     string    png or pdf
	 * @return    string    filename
	 */
	public function getFile($hash, $filetype, $overwrite=FALSE) {

		//Concatinate the filename
		$filename = $hash . "." . $filetype;

		// Get the cache filename.
		$cachefile = $this->encodeCacheName($filename);

		// If the cache file exists, serve it.
		if (file_exists($cachefile) && $overwrite == FALSE) {

			return $cachefile;

		// If the cache file does not exist, generate it.
		} else {

			//Pull all the renderata from the db for the given hash
			$renderdata = $this->getImageData($hash);

			//If we were able to find renderdata for the requested image, attempt to generate the file
			if($renderdata) {

				//If we generated the file, return the cache filename. Else return false.
				if ($this->generateFile($renderdata, $hash)) {
					return $cachefile;
				} else {
					return false;
				}

			} else {
				return false;
			}

		}

	}


	/**
	 * This function takes a php renderdata array as an argument, generates a random id and creates
	 * a png and a pdf.
	 *
	 * @return [type] [description]
	 */
	public function createFile($renderdata) {

		//Gets a unique ID for the image
		$hash = $this->getHash();

		//Returns the hash as long as the images were created successfully
		if ($this->generateFile($renderdata, $hash)) {

			return $hash;
		} else {
			return false;
		}

	}
	public function getDesignIDFromHash($hash) {

		$stmt=Connection::getHandle()->prepare("SELECT id FROM bs_designs WHERE hash=:hash LIMIT 1");
		$stmt->execute(array(":hash"=>$hash));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}
	public function getHashFromDesignID($design_id) {

		$stmt = Connection::getHandle()->prepare("SELECT hash FROM bs_designs WHERE id=:design_id LIMIT 1");
		$stmt->execute(array(":design_id"=>$design_id));
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$hash=$row['hash'];
		}

		return $hash;

	}
	public function getPIDFromHash($hash) {

		$stmt=Connection::getHandle()->prepare("SELECT s.product_id FROM bs_designs d LEFT JOIN bs_cart_skus s ON (d.id = s.design_id) WHERE hash=:hash LIMIT 1");

		$stmt->execute(array(":hash"=>$hash));
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$products_id=$row['product_id'];
		}

		return $products_id;
	}


	/**
	 * Returns the url for a png with the given hash
	 * @param     string    $hash    The image hash
	 * @return    string             the full image path
	 */
	public function imageURL($hash) {
		$time = $this->getTimestamp($hash);
		return '/images/designs/' . $hash . '.' . $time . '.png';
	}


	/**
	 * Returns the url for a pdf with the given hash
	 * @param     string    $hash    The pdf hash
	 * @return    string             the full pdf path
	 */
	public function PDFURL($hash) {
		$time = $this->getTimestamp($hash);
		return '/images/designs/' . $hash . '.' . $time . '.pdf';
	}


	/**
	 * Returns the url for a png with the given hash
	 * @param     string    $hash    The image hash
	 * @return    string             the full image path
	 */
	public function imageURLFromDesignID($design_id) {

		$hash = $this->getHashFromDesignID($design_id);
		$time = $this->getTimestamp($hash);
		return '/images/designs/' . $hash . '.' . $time . '.png';
	}


	/**
	 * Returns the url for a pdf with the given hash
	 * @param     string    $hash    The pdf hash
	 * @return    string             the full pdf path
	 */
	public function PDFURLFromDesignID($design_id) {
		$hash = $this->getHashFromDesignID($design_id);
		$time = $this->getTimestamp($hash);
		return '/images/designs/' . $hash . '.' . $time . '.pdf';
	}

	private function getTimestamp($hash) {

		$stmt=Connection::getHandle()->prepare("SELECT time FROM bs_designs WHERE hash=:hash LIMIT 1");
		$stmt->execute(array(":hash"=>$hash));
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$time = strtotime(preg_replace("/[^0-9]/", "", $result['time']));
		}
		return $time;
	}


	/**
	 * Gets renderdata for a given hash, duplicates the data, and returns the new hash
	 * @param     string    $hash    A given hash
	 * @return    string       		 The hash of the duplicated data
	 */
	public function duplicateDesign($design_id) {
		$hash = $this->getHashFromDesignID($design_id);
		$renderdata = $this->getImageData($hash);
		$new_hash = $this->getHash();
		$new_design_id  = $this->renderBuilder($renderdata, $new_hash);
		$image = $this->createFile($renderdata);

		if ($image) {
			return $new_design_id;
		} else {
			return false;
		}
	}


	/**
	 * Takes renderdata and a hash, and inserts everything into the renderdata tables in the database
	 * Then returns the id from the renderdata table of the insertion
	 *
	 * @param  array     $myData    a renderdata array
	 * @param  string    $hash      the hash
	 * @return int                  the id from the renderdata table
	 */
	public function renderBuilder($myData, $hash) {

		//Check if we have a database connection

		$stmt=Connection::getHandle()->prepare("SELECT id FROM bs_designs WHERE hash=:hash");
		$stmt->execute(array(":hash"=>$hash));

		while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
				$id=$result['id'];
		}

		//If there was not already a row with that hash
		if (!$id) {
			$stmt_insert=Connection::getHandle()->prepare("INSERT INTO bs_designs (hash) VALUES (:hash)");
			$stmt_insert->execute(array(":hash"=>$hash));

			//Use the id from this new row as design id
			$design_id = Connection::getHandle()->lastInsertId();

		} else {

			//Update the timestamp in bs_designs
			$time = date("Y-m-d H:i:s");
			$stmt_update=Connection::getHandle()->prepare("UPDATE bs_designs SET time =:time WHERE id=:id ");
			$stmt_update->execute(array(":time"=>$time,":id"=>$id));

			//Use the id from the existing row as design id
			$design_id = $id;

			//Delete old renderdata
			$stmt_delete=Connection::getHandle()->prepare("DELETE FROM bs_builder_renderdata WHERE design_id=:design_id");
			$stmt_delete->execute(array(":design_id"=>$id));
		}



		//Background ===============================================================================

		if($myData["background"]) {
			$stmt_insert=Connection::getHandle()->prepare("INSERT INTO bs_builder_renderdata (design_id, size_ref,scheme_ref,width,height)
											  VALUES (:design_id,:size_ref,:scheme_ref,:width,:height)");
			$stmt_insert->execute(array(
									":design_id"=>$design_id,":size_ref"=>$myData['background']['size'],":scheme_ref"=>$myData['background']['scheme'],
									":width"=>$myData['background']['w'],":height"=>$myData['background']['h']
								 ));

			$render_id =Connection::getHandle()->lastInsertId();



		}

		//Elements =================================================================================
		if($myData['elements']) {

			$position=0;

			foreach($myData['elements'] as $value) {

				$artwork_id=null;
				$upload_id=null;
				$text=null;
				$font=null;
				$fontsize=null;
				$baseline=null;
				$leading=null;
				$color=null;
				$alignment=null;
				$x=0;
				$y=0;
				$width=0;
				$height=0;
				$position++;
				$type=$value['type'];

				switch($type) {
					case 'artwork': $artwork_id=$value['id']; break;
					case 'upload': $upload_id=$value['id']; break;
					case 'text': $text=$value['content']; break;
				}

				if($artwork_id==NULL)
					$artwork_id='';
				if($upload_id==NULL)
					$upload_id='';
				if($text==NULL)
					$text='';
				if($value['font']!=NULL)
					$font=$value['font']; else	$font='';
				if($value['fontsize']!=NULL)
					$fontsize=$value['fontsize'];else $fontsize=0;
				if($value['leading']!=NULL)
					$leading=$value['leading'];else	$leading='';
				if($value['baselineoffset']!=NULL)
					$baseline=$value['baselineoffset'];else $baseline=0.00;
				if($value['color']!=NULL)
					$color=$value['color'];else $color='';
				if($value['alignment']!=NULL)
					$align=$value['alignment'];else $align='';
				if($value['x']!=NULL)
					$x=$value['x'];else $x=0.00;
				if($value['y']!=NULL)
					$y=$value['y'];else $y=0.00;
				if($value['w']!=NULL)
					$w=$value['w'];else $w=0;
				if($value['h']!=NULL)
					$h=$value['h'];else $h=0;
				$stmt_insert_element=Connection::getHandle()->prepare("INSERT INTO bs_builder_renderdata_elements
							  								(render_id,position,type,artwork_ref,upload_id,`text`,font,fontsize,`leading`,baseline_offset,color,alignment,x,y,width,height)
							  							  VALUES
							  							  (:render_id,:position,:type,:artwork,:upload_id,:text,:font,:fontsize,:leading,
							  							  	:baseline_offset,:color,:alignment,:x,:y,:width,:height)");
				$stmt_insert_element->execute(array(
													":render_id"=>$render_id,":position"=>$position,":type"=>$type,":artwork"=>$artwork_id,":upload_id"=>$upload_id,
													":text"=>$text,":font"=>$font,":fontsize"=>$fontsize,":leading"=>$leading,":baseline_offset"=>$baseline,":color"=>$color,
													":alignment"=>$align,":x"=>$x,":y"=>$y,":width"=>$w,":height"=>$h
											 ));
			}
		}

		//Options ==================================================================================
		if($myData['options']) {

			$position=0;
			foreach($myData['options'] as $value) {
				$position++;
				$ref=$value['id'];
				$value_ref=$value['value'];
				$stmt_insert_option=Connection::getHandle()->prepare("INSERT INTO bs_builder_renderdata_options (render_id,position,option_ref,optionvalue_ref)
														 VALUES(:render_id,:position,:option_ref,:optionvalue_ref)
														");
				$stmt_insert_option->execute(array(":render_id"=>$render_id,":position"=>$position,":option_ref"=>$ref,":optionvalue_ref"=>$value_ref));
			}
		}

		return ($design_id);
	}


	/**
	 * Checks for a duplicate hash in the database
	 *
	 * @param     string    $hash        A unique ID
	 * @return    bool 					 True if the ID is unique, false if it was not
	 */


	private function checkHash($hash) {

		$stmt=Connection::getHandle()->prepare("SELECT hash FROM bs_designs WHERE hash=:hash LIMIT 1");
		$stmt->execute(array(":hash"=>$hash));
		while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
			$hash1=$result['hash'];
		}
		if ($hash1 != NULL && $hash1 != '') {
				return false;
			} else {
				return true;
			}

	}


	/**
	 * Generates a random unique
	 * @return [type] [description]
	 */
	private function getHash() {

		//Seed the random generator
		mt_srand($this->make_seed());

		//Alphanumeric upper/lower array
		$alfa = "1234567890qwrtypsdfghjklzxcvbnm";
		$hash = "";

		//Loop through and generate the random hash
		for($i = 0; $i < 32; $i ++) {
		  $hash .= $alfa[mt_rand(0, strlen($alfa)-1)];
		}

		//If there is a duplicate, run this function recursively
		if(!$this->checkHash($hash)) {
			$hash = $this->getHash();
		}

		//Return the hash
		return $hash;
	}


	//Random generator seed function
	private function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}


	/**
	 * Checks if we could verify the hash, and if so generates a file.
	 *
	 * @return    string    The generated filename
	 */
	public function generateFile($renderdata, $hash) {

		//If we did receive an id back
		if ($renderdata && $hash) {

			//Create the PNG
			$RenderVector = new RenderVectorSetup();

			$png = $RenderVector->getRasterImage(260, 260, $hash, $renderdata, true);

			//Create the PDF
			$RenderPDF = new RenderPDFSetup();
			$pdf = $RenderPDF->getRenderPDF($hash, $renderdata, true);

			//Return true or false depending on whether both images were created successfully
			if ($png && $pdf) {
				return true;
			} else {
				return false;
			}

		} else {
			return true;
		}

	}


	/**
	 * Gets all image data from bs_builder_render_data and constructs an array that is returned as $data
	 * @return    array    An array of all the render data needed to recreate the builder image
	 */

	public function getImageData($hash) {


		//Create the data array
		$data = array();

		//Get the background data ===================================================================

			$stmt_background = Connection::getHandle()->prepare(
						"SELECT bs_builder_renderdata.id AS id, size_ref AS size_ref, scheme_ref AS scheme_ref,
						width AS width, height AS height FROM bs_builder_renderdata

						INNER JOIN bs_designs ON bs_builder_renderdata.design_id = bs_designs.id
						WHERE hash=:hash"
			);

			if( $stmt_background->execute(array(":hash"=>$hash)) ) {

				while ($result = $stmt_background->fetch(PDO::FETCH_ASSOC)) {

					$renderdata_id = $result['id'];

					if ($result['size_ref'] != NULL && $result['size_ref'] != '') {
						$data['background']['size'] = $result['size_ref'];
					}

					if ($result['scheme_ref'] != NULL && $result['scheme_ref'] != '') {
						$data['background']['scheme'] = $result['scheme_ref'];
					}

					if ($result['width'] != NULL && $result['width'] != '') {
						$data['background']['w'] = (int)$result['width'];
					}

					if ($result['height'] != NULL && $result['height'] != '') {
						$data['background']['h'] = (int)$result['height'];
					}

				}
			}



		//Get the elements ==========================================================================
			//Build the query
			$stmt_element=Connection::getHandle()->prepare("SELECT * FROM bs_builder_renderdata_elements WHERE render_id=:renderdata_id ORDER BY position  ASC");
			$stmt_element->execute(array(":renderdata_id"=>$renderdata_id));
			$i=0;
			while($result=$stmt_element->fetch(PDO::FETCH_ASSOC)){
					if ($result['type'] != NULL && $result['type'] != '')                               { $data['elements'][$i]['type'] = $result['type']; }
					if ($result['artwork_ref'] != NULL && $result['artwork_ref'] != '')                 { $data['elements'][$i]['id'] = $result['artwork_ref']; }
					if ($result['upload_id'] != NULL && $result['upload_id'] != '')                     { $data['elements'][$i]['id'] = $result['upload_id']; }
					if ($result['text'] != NULL && $result['text'] != '')                               { $data['elements'][$i]['content'] = $result['text']; }
					if ($result['color'] != NULL && $result['color'] != '')                             { $data['elements'][$i]['color'] = $result['color']; }
					if ($result['alignment'] != NULL && $result['alignment'] != '')                     { $data['elements'][$i]['alignment'] = $result['alignment']; }
					if ($result['font'] != NULL && $result['font'] != '')                               { $data['elements'][$i]['font'] = $result['font']; }
					if ($result['fontsize'] != NULL && $result['fontsize'] != '')                       { $data['elements'][$i]['fontsize'] = (int) $result['fontsize']; }
					if ($result['leading'] != NULL && $result['leading'] != '')                         { $data['elements'][$i]['leading'] = (int) $result['leading']; }
					if ($result['x'] != NULL && $result['x'] != '')                                     { $data['elements'][$i]['x'] = (float) $result['x']; }
					if ($result['y'] != NULL && $result['y'] != '')                                     { $data['elements'][$i]['y'] = (float) $result['y']; }
					if ($result['width'] != NULL && $result['width'] != '')                             { $data['elements'][$i]['w'] = (float) $result['width']; }
					if ($result['height'] != NULL && $result['height'] != '')                           { $data['elements'][$i]['h'] = (float) $result['height']; }
					if ($result['baseline_offset'] != NULL && $result['baseline_offset'] != '')         { $data['elements'][$i]['baselineoffset'] = (float) $result['baseline_offset']; }

					$i++;
			}

		//Get the options ===========================================================================
			$stmt_option=Connection::getHandle()->prepare("SELECT * FROM bs_builder_renderdata_options WHERE render_id = :renderdata_id  ORDER BY position ASC ");
			$stmt_option->execute(array(":renderdata_id"=>$renderdata_id));
			$i=0;
			while($result=$stmt_option->fetch(PDO::FETCH_ASSOC)){
				if ($result['option_ref'] != NULL && $result['option_ref'] != '')                   { $data['options'][$i]['id'] = $result['option_ref']; }
				if ($result['optionvalue_ref'] != NULL && $result['optionvalue_ref'] != '')		    { $data['options'][$i]['value'] = $result['optionvalue_ref']; }
				$i++;
			}


		if ($data) {
			return $data;
		} else {
			return false;
		}

	}


	/**
	 * Takes a filename as input, and returns the full path to the image cache dir
	 *
	 * @param     string    $filename    The input file
	 * @return    string                 Output file, with full cache path
	 */
	private function encodeCacheName($filename) {

		// Determine the directory for this cache file, based on the first two characters of the filename.
		$directory = $this->cachedir . '/' . substr($filename, 0, 2);

		// Return the full file path of the cache file.
		return $directory . '/' . $filename;

	}


}