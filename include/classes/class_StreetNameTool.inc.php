<?php

class StreetNameTool extends CacheableEntity {

    /**
     * Image path
     */
    const IMAGEPATH = "";

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     **/
    const FULL_TABLE_DUMP = "SELECT st.id AS id, st.name AS streetsign_name, st.active AS streetsignActive, st.default_streetsign_tool_mounting_hole_arrangement_id AS streetsignDefaultMountingHoleArrangementId,
		   					 sp.name AS streetsign_prefix_name, ss.name AS streetsign_suffix_name, sf.name AS streetsign_font_name,
							 sla.`name` AS streetsign_leftarrow_name, sra.name AS streetsign_rightarrow_name, sps.name AS streetsign_position_name,
							 sc.color AS streetsign_color, sbg.name AS streetsign_background_name, sbg.upload_required AS streetsign_background_upload_required,
							 sbg.position AS streetsign_background_position, stmha.mounting_hole_arrangement_id
							 FROM bs_streetsign_tools st
							 LEFT JOIN bs_streetsign_prefix sp ON (sp.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_suffix ss ON (ss.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_font sf ON (sf.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_leftarrow sla ON (sla.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_rightarrow sra ON (sra.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_position_sign sps ON (sps.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_color sc ON (sc.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_background sbg ON (sbg.streetsign_tool_id = st.id)
							 LEFT JOIN bs_streetsign_tool_mounting_hole_arrangements stmha ON (stmha.streetsign_tool_id = st.id) WHERE st.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     **/
    const ADDITIONAL_CLAUSES = " GROUP BY st.id ";

	/**
	* Unique streetname tool id
	* DB column: bs_streetsign_tools.id.
	* @var int $id
	*/
	private $id;

	/**
	 * streetsign name
	 * DB column: bs_streetsign_tools.name
	 * @var string $streetsignName
	 */
	private $streetsignName;

	/**
	 * Unique streetname active
	 * DB column: bs_streetsign_tools.active.
	 * @var bool $streetsignActive
	 */
	private $streetsignActive;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.default_mounting_hole_arrangementId.
	 * @var int $streetsignDefaultMountingHoleArrangementId
	 */
	private $streetsignDefaultMountingHoleArrangementId;
	/**
	 * Unique streetname prefix name
	 * DB column: bs_streetsign_suffix.streetsign_prefix_name
	 * @var string $streetsignPrefixName
	 */
	private $streetsignPrefixName;

	/**
	 * streetname suffix name
	 * DB column: bs_streetsign_suffix.streetsign_suffix_name
	 * @var string $$streetsignSuffixName
	 */
	private $streetsignSuffixName;

	/**
	 * streetname font name
	 * DB column: bs_streetsign_tools.streetsign_font_name.
	 * @var int $streetsignFontName
	 */
	private $streetsignFontName;

	/**
	 * streetsign leftarrow name
	 * DB column: bs_streetsign_tools.streetsignLeftarrowName.
	 * @var string $streetsignLeftarrowName
	 */
	private $streetsignLeftarrowName;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.streetsignRightarrowName.
	 * @var string $streetsignRightarrowName
	 */
	private $streetsignRightarrowName;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.$streetsignColor.
	 * @var int $id
	 */
	private $streetsignPositionName;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.$streetsignBackgroundName.
	 * @var int $id
	 */
	private $streetsignColor;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.$streetsignBackgroundUploadRequired.
	 * @var int $id
	 */
	private $streetsignBackgroundName;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.$streetsignBackgroundPosition.
	 * @var int $id
	 */
	private $streetsignBackgroundUploadRequired;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools.$mountingHoleArrangementId.
	 * @var int $id
	 */
	private $streetsignBackgroundPosition;

	/**
	 * Unique streetname tool id
	 * DB column: bs_streetsign_tools..
	 * @var int $id
	 */
	private $mountingHoleArrangementId;




	public function __construct($id = NULL) {

		$this->setId($id);

        if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

            if( empty($data) ) {

                $sql = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND st.id = :id ");
                $sql->bindParam(":id", $this->getId(), PDO::PARAM_INT);

                if( $sql->execute() ) {

                    $data = $sql->fetch(PDO::FETCH_ASSOC);
                    //Query here

					// Cache data so we don't have to retrieve from database again
					$this->storeCache($data);
                }
            }

			$this->setStreetName(isset($data['streetsign_name'])? isset($data['streetsign_name']) : NULL)
				 ->setStreetsignActive($data['streetsignActive'])
				 ->setStreetsignDefaultMountingHoleArrangementId($data['streetsignDefaultMountingHoleArrangementId'])
				 ->setStreetsignprefixname($data['streetsign_prefix_name'])
				 ->setStreetsignsuffixname($data['streetsign_suffix_name'])
				 ->setStreetsignFontName($data['streetsign_font_name'])
				 ->setStreetsignLeftarrowName($data['streetsign_leftarrow_name'])
				 ->setStreetsignRightarrowName($data['streetsign_rightarrow_name'])
				 ->setStreetsignPositionName($data['streetsign_position_name'])
				 ->setStreetsignColor($data['streetsign_color'])
				 ->setStreetsignBackgroundName($data['streetsign_background_name'])
				 ->setStreetsignBackgroundUploadRequired($data['streetsign_background_upload_required'])
				 ->setStreetsignBackgroundPosition($data['streetsign_background_position'])
				 ->setMountingHoleArrangementId($data['mounting_hole_arrangement_id']);

		} else {

	 		// Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load StreetNameTool properties: \'' . $id . '\' is not a valid ID number.');

		}
	}

	// Setters
	private function setId($id = NULL) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
        return $this;
	}

	/**
	 * @param string $streetsignName
	 */
	public function setStreetName($streetsignName)
	{
		$this->streetsignName = $streetsignName;
		return $this;
	}

	/**
	 * @param boolean $streetsignActive
	 */
	public function setStreetsignActive($streetsignActive)
	{
		$this->streetsignActive = $streetsignActive;
		return $this;
	}

	/**
	 * @param int $streetsignDefaultMountingHoleArrangementId
	 */
	public function setStreetsignDefaultMountingHoleArrangementId($streetsignDefaultMountingHoleArrangementId)
	{
		$this->streetsignDefaultMountingHoleArrangementId = $streetsignDefaultMountingHoleArrangementId;
		return $this;
	}

	/**
	 * @param string $streetsignPrefixName
	 */
	public function setStreetsignPrefixName($streetsignPrefixName)
	{
		$this->streetsignPrefixName = $streetsignPrefixName;
		return $this;
	}

	/**
	 * @param string $streetsignSuffixName
	 */
	public function setStreetsignSuffixName($streetsignSuffixName)
	{
		$this->streetsignSuffixName = $streetsignSuffixName;
		return $this;
	}

	/**
	 * @param int $streetsignFontName
	 */
	public function setStreetsignFontName($streetsignFontName)
	{
		$this->streetsignFontName = $streetsignFontName;
		return $this;
	}

	/**
	 * @param string $streetsignLeftarrowName
	 */
	public function setStreetsignLeftarrowName($streetsignLeftarrowName)
	{
		$this->streetsignLeftarrowName = $streetsignLeftarrowName;
		return $this;
	}

	/**
	 * @param string $streetsignRightarrowName
	 */
	public function setStreetsignRightarrowName($streetsignRightarrowName)
	{
		$this->streetsignRightarrowName = $streetsignRightarrowName;
		return $this;
	}

	/**
	 * @param int $streetsignPositionName
	 */
	public function setStreetsignPositionName($streetsignPositionName)
	{
		$this->streetsignPositionName = $streetsignPositionName;
		return $this;
	}

	/**
	 * @param int $streetsignColor
	 */
	public function setStreetsignColor($streetsignColor)
	{
		$this->streetsignColor = $streetsignColor;
		return $this;
	}

	/**
	 * @param int $streetsignBackgroundName
	 */
	public function setStreetsignBackgroundName($streetsignBackgroundName)
	{
		$this->streetsignBackgroundName = $streetsignBackgroundName;
		return $this;
	}

	/**
	 * @param int $streetsignBackgroundUploadRequired
	 */
	public function setStreetsignBackgroundUploadRequired($streetsignBackgroundUploadRequired)
	{
		$this->streetsignBackgroundUploadRequired = $streetsignBackgroundUploadRequired;
		return $this;
	}

	/**
	 * @param int $streetsignBackgroundPosition
	 */
	public function setStreetsignBackgroundPosition($streetsignBackgroundPosition)
	{
		$this->streetsignBackgroundPosition = $streetsignBackgroundPosition;
		return $this;
	}

	/**
	 * @param int $mountingHoleArrangementId
	 */
	public function setMountingHoleArrangementId($mountingHoleArrangementId)
	{
		$this->mountingHoleArrangementId = $mountingHoleArrangementId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getStreetsignName()
	{
		return $this->streetsignName;
	}

	/**
	 * @return boolean
	 */
	public function isStreetsignActive()
	{
		return $this->streetsignActive;
	}

	/**
	 * @return int
	 */
	public function getStreetsignDefaultMountingHoleArrangementId()
	{
		return $this->streetsignDefaultMountingHoleArrangementId;
	}

	/**
	 * @return string
	 */
	public function getStreetsignPrefixName()
	{
		return $this->streetsignPrefixName;
	}

	/**
	 * @return string
	 */
	public function getStreetsignSuffixName()
	{
		return $this->streetsignSuffixName;
	}

	/**
	 * @return int
	 */
	public function getStreetsignFontName()
	{
		return $this->streetsignFontName;
	}

	/**
	 * @return string
	 */
	public function getStreetsignLeftarrowName()
	{
		return $this->streetsignLeftarrowName;
	}

	/**
	 * @return string
	 */
	public function getStreetsignRightarrowName()
	{
		return $this->streetsignRightarrowName;
	}

	/**
	 * @return int
	 */
	public function getStreetsignPositionName()
	{
		return $this->streetsignPositionName;
	}

	/**
	 * @return int
	 */
	public function getStreetsignColor()
	{
		return $this->streetsignColor;
	}

	/**
	 * @return int
	 */
	public function getStreetsignBackgroundName()
	{
		return $this->streetsignBackgroundName;
	}

	/**
	 * @return int
	 */
	public function getStreetsignBackgroundUploadRequired()
	{
		return $this->streetsignBackgroundUploadRequired;
	}

	/**
	 * @return int
	 */
	public function getStreetsignBackgroundPosition()
	{
		return $this->streetsignBackgroundPosition;
	}

	/**
	 * @return int
	 */
	public function getMountingHoleArrangementId()
	{
		return $this->mountingHoleArrangementId;
	}

    /**
     * This function gets a client's IP address
     * @return [string]    the client's IP address
     */
    function getClientIp() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * Handles user file upload for the streetname sign tool
     * @return [array] an array of information about the upload and its success/failure
     */
    public function uploadcustomfile($file) {

        //Set the file paths
        $uploadFilePath ='upload/temp/';

        //Allowed extensions/MIME types
        $allowed_mimes = array('image/jpeg', 'image/gif', 'image/png', 'image/tiff', 'application/postscript', 'application/postscript', 'image/vnd.adobe.photoshop', 'application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr');
        $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'ai', 'cdr', 'pdf', 'eps', 'psd');

        //If success stays 1 through all our checks, the function will return true and the file upload is a success
        $success = 1;

        do {

            //Check if the file is empty
            if (empty($_FILES['importcustomfile'])) { $message = array('Please select a file to upload.'); break; }

            //Check if the image returned an error
            if ($_FILES['importcustomfile']['error'] !== UPLOAD_ERR_OK) { $message = array('An unknown error has occurred.'); break; }

            //Make sure the extension is allowed
            $extension = end(explode(".",strtolower($file['name'])));
            if (!in_array($extension, $allowed_extensions)) { $message = array('This file type is not allowed.'); break; }

            //Make sure the file is within our 2MB size bound
            if ($file['size'] >= 2000000) { $message = array('Your file must be smaller than 2MB in size. If you would like to upload a larger image, please contact customer support'); break; }

            //Check the mime type
            $filetype_check = getimagesize($file['tmp_name']);
            $mime = $filetype_check['mime'];

            //Make sure the MIME type is allowed
            if (!in_array($mime, $allowed_mimes)) { $message = array('This file type is not allowed.'); break; }

            //Rename the file to remove anything that could modify the destination path
            $name = preg_replace("/[^A-Z0-9._-]/iu", "_", $file['name']);

            //don't overwrite an existing file
            $i = 0;
            $parts = pathinfo($name);
            while (file_exists($uploadFilePath . $name)) {
                $i++;
                $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
            }

            //Move the uploaded file or return an error
            $success = move_uploaded_file($file['tmp_name'], $uploadFilePath . $name);
            if (!$success) { $message = array('Your file could not be saved.'); }

            //Grab the username and IP so we can affiliate the upload with this user
            $username=$this->GetCustomerUsername();
            $ip = $this->getClientIp();

            //Insert into the database
            $sql = Connection::getHandle()->prepare("INSERT INTO bs_streetsign_uploads
											(file_name, file_size, file_type,
											 creation_time)
										VALUES
											(:filename,:filesize, :filetype, :creation_time) ");
            $sql->execute(array(":filename" => $name,
                ":filesize" => $file['size'],
                ":filetype" => $file['type'],
                ":creation_time" => date("Y-m-d H:i:s")));


            $ID = Connection::getHandle()->lastInsertId();
            $message = array('Your file has been uploaded.', $ID, $name, $file['size']);


        } while (empty($message));

        //Return an array with status message, and optionally ID, name, width, and height
        return $message;
    }

    /**
     * Deletes a user-uploaded image file as part of the streetname tool
     */
    public function deleteCustomFile($fileid, $filename, $filesize) {

        $fileatpath = 'upload/temp/'.$filename;

            //Delete from the database if the id, filename, AND filesize match. This is to prevent hackers
            $sql = Connection::getHandle()->prepare("DELETE FROM bs_streetsign_uploads
										WHERE id = ? AND file_name = ? AND file_size = ?");
            $sql->execute(array($fileid, $filename, $filesize));

            //Unlink the file
            unlink($fileatpath);
            return true;


    }

    /**
     * Gets a username from the session
     */
    private function GetCustomerUsername() {

        $sql = Connection::getHandle()->prepare("SELECT username AS username FROM bs_customers WHERE customers_id = ?");
        $sql->execute(array($_SESSION['CID']));
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        return $row['username'];
    }

	/**
	 * Gets custom control options based on a given layout
	 * @param [string]    $layout    layout
	 */
	public function ControlCustomOptions() {

		$sql = Connection::getHandle()->prepare("SELECT prefix_active, leftarrow_active, suffix_active,
									 rightarrow_active, position_active, font_active,
									 background_active, street_num_active, logo_active,
									 secondline_active, textcolor_active, arrow_active,
									 textdefaultcolor_active, arrowcolor_active, product_links, accuracy_image
							  FROM bs_streetsign_control
							  WHERE streetsign_tool_id = ?");
		$sql->execute(array($this->getId()));

		$row = $sql->fetch(PDO::FETCH_ASSOC);

		return $row;
	}

	/**
	 * Returns the color for a given layout
	 * @param    [array]    $layout    color array
	 */
	public function CustomProductColor() {

		$sql = Connection::getHandle()->prepare("SELECT color FROM bs_streetsign_color WHERE streetsign_tool_id = ? AND active = TRUE GROUP BY color ORDER BY position");
		$sql->execute(array($this->getId()));

		while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row['color'];
		}

		return $results;
	}

    /**
     * Gets font names for a given layout
     * @param [string]    $layout    layout
     */
    function CustomProductFont($layout) {

        $sql =Connection::getHandle()->prepare("SELECT name FROM bs_streetsign_font WHERE streetsign_tool_id = ? AND active= TRUE ORDER BY position");
        $sql->execute(array($layout));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $fonts[] = $row['name'];
        }

        return $fonts;

    }

	/**
	 * Gets sku description info for a given product/size
	 * @param [string]    $productno    product number
	 * @param [string]    $size         product size
	 */
	public function getCustomMaterialList($productno, $size) {

		$sql = Connection::getHandle()->prepare("SELECT s.name AS size, sku.id AS sku_id, sku.name AS sku_code, mg.description AS material_description, m.name AS material, sku.requires_freight, sku.max_chars_upper, sku.absolute_maximum, pr.material_code
							  FROM bs_products p
							  INNER JOIN bs_product_skus ps ON(ps.product_id = p.id)
								INNER JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = TRUE)
								LEFT JOIN bs_materials m ON (m.id = sku.material_id AND m.active = TRUE)
								LEFT JOIN bs_material_groups mg ON (mg.id = m.material_group_id AND mg.active = TRUE)
								LEFT JOIN bs_sizes s ON (s.id = sku.size_id AND s.active = TRUE)
								LEFT JOIN bs_pricing pr ON (pr.id = sku.pricing_id)
							  WHERE p.product_number = ? AND s.name like ?
							  AND p.active = TRUE
							  ORDER BY 	ps.position;");
		$sql->execute(array($productno, '%'.$size. '%'));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}

		return $results;

	}

	/**
	 * Returns custom backgrounds for a given layout
	 * @param [string]    $layout    layout
	 */
	public function CustomProductBackground($layout) {

		$sql = Connection::getHandle()->prepare("SELECT * FROM bs_streetsign_background WHERE streetsign_tool_id = ? AND active = TRUE ORDER BY position");
		$sql->execute(array($layout));

		while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[ $row['name']] = $row;
		}
		return $results;
	}

	/**
	 * Returns special sign instructions from sku_description based on the product number
	 * @param [string]    $productno    product number
	 */
	public function GetInstruction($productno) {

		$sql = Connection::getHandle()->prepare("SELECT streetsign_note AS product_description
									FROM bs_products
									WHERE product_number = ?
									AND active = TRUE
									LIMIT 1");
		$sql->execute(array($productno));

		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$instruction = $row['product_description'];

		return $instruction;
	}

	/**
	 * Gets a size for a given sku
	 * @param [string]    $skucode    sku
	 */
	private function getSizeFromSku($skucode) {
		$sql = Connection::getHandle()->prepare("SELECT s.name AS size from bs_sizes s
		 							INNER JOIN bs_skus sku ON (sku.size_id = s.id AND sku.active = TRUE)
		 							WHERE sku.name = ? LIMIT 1");
		$sql->execute(array($skucode));
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		return $row['size'];
	}

	/**
	 * Gets prefixes for a given layout
	 * @param [string]    $layout    layout
	 */
	function CustomProductPrefix($layout) {

		$sql = Connection::getHandle()->prepare("SELECT name AS name FROM bs_streetsign_prefix WHERE streetsign_tool_id = ? AND active = TRUE ORDER BY position");
		$sql->execute(array($layout));
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row['name'];
		}
		return $results;

	}


	/**
	 * Gets suffixes for a given layout
	 * @param [string]    $layout    layout
	 */
	function CustomProductSuffix($layout) {
		$sql = Connection::getHandle()->prepare("SELECT name AS name FROM bs_streetsign_suffix WHERE streetsign_tool_id = ? AND active = TRUE ORDER BY position");
		$sql->execute(array((int) $layout));
		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row['name'];
		}
		return $results;

	}

	/**
	 * Gets product positions for a given layout
	 * @param [string]    $layout    layout
	 */
	public function CustomProductPositionSign($layout) {

		$sql = Connection::getHandle()->prepare("SELECT name FROM bs_streetsign_position_sign WHERE streetsign_tool_id = ? AND active = TRUE ORDER BY position");
		$sql->execute(array($layout));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row['name'];
		}

		return $results;
	}

	/**
	 * Returns an RGB color array for a given color name
	 * @param [string]    $color    color name
	 */
	private function getColorCode($color) {
		switch($color) {
			case 'Orange': $colorcode = array(248, 152, 29);  break;
			case 'White':  $colorcode = array(255, 255, 255); break;
			case 'Red':    $colorcode = array(197, 18, 48);   break;
			case 'Brown':  $colorcode = array(127, 54, 32);   break;
			case 'Blue':   $colorcode = array(42, 80, 163);   break;
			case 'Green':  $colorcode = array(0, 134, 83);    break;
			case 'Black':  $colorcode = array(0, 0, 0);       break;
		}

		return $colorcode;
	}

	/**
	 * Takes a material code and an optional list of upcharge id's, and returns an array of upcharge rows
	 * @param  string    $material     material code
	 * @param  array     $upcharges    array of upcharge IDs (optional)
	 * @return array                   array of upcharge results
	 */
	function getStreetnameUpchargeDefaults($layout) {

		//If we were passed a list of upcharges, we will only return those upcharge rows.
		//If we are passed a layout and not upcharges, we will return every upcharge
		//applicable to that layout
		$sql =Connection::getHandle()->prepare("SELECT stmha.*, mha.name FROM bs_streetsign_tool_mounting_hole_arrangements stmha
												INNER JOIN bs_mounting_hole_arrangements mha ON (mha.id = stmha.mounting_hole_arrangement_id AND mha.active = TRUE)
								  				WHERE stmha.streetsign_tool_id = ? ORDER BY position");
		$sql->execute(array($layout));


		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$row['type'] = 'Mounting';
			$results[] = $row;
		}

		return $results;

	}

	public function createimg($skucode, $layout, $editdata, $design_id) {

		$prefix_x      = "";
		$prefix_y      = "";
		$suffix_x      = "";
		$suffix_y      = "";
		$prefix        = "";
		$suffix        = "";
		$streetnum     = "";
		$streetnum_x   = "";
		$streetnum_y   = "";
		$subfont       = "";
		$position      = "";
		$gap           = 5;
		$uppercheckbox = "";
		$shiftlogo     = 0;
		$shiftsuffix   = 0;
		$customoptions = $this->ControlCustomOptions($layout);

		if (isset($editdata['textupper'])) {
			$uppercheckbox = $editdata['textupper'];
		}

		if ($uppercheckbox == "Y")
			$text = strtoupper($editdata['line_1']);
		else
			$text = $editdata['line_1'];

		$text2 = "";

		if (isset($editdata['line_2']) && $editdata['line_2']!='') {
			if ($uppercheckbox == "Y")
				$text2 = strtoupper($editdata['line_2']);
			else
				$text2 = $editdata['line_2'];
		}

		$size = $this->getSizeFromSku($skucode);

		$size = str_replace(array("′", "″"),"",$size);
		$size = str_replace(" ","",$size);
		$size_sepa = explode("×",$size);
		$color = $editdata['sign_color'];
		$color_Sep = explode("/",$color);

		if ($size_sepa[1] == "4")
			$size_sepa[1] = 6;
		$backgroundimage = $color_Sep[0] . "_" . $size_sepa[1];


		if($customoptions['logo_active'] == TRUE && $editdata['sign_background'] != "") {

			$imgname="../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);


			if($size_sepa[1]==9)
				$shiftlogo=24;
			else if($size_sepa[1]==6)
				$shiftlogo=18;

		} else if($editdata['sign_background']=="" || $editdata['sign_background']=="Extruded")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Extruded with Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Extruded with Outside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade No Border")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade No Border and  Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade No Border and Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade No Border and Outside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade With Border")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade With Border and Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Flat Blade With Border and Outside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes and Border")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes and No Border")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes-Border-Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes-Border-Outside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes-No Border-Inside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Sign with Holes-No Border-Outside Image")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else if($editdata['sign_background']=="" || $editdata['sign_background']=="Plain")
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".str_replace(" ","_",$editdata['sign_background']);
		else
			$imgname = "../images/catlog/product/small/".$backgroundimage."_".$editdata['sign_background'];


		if($size_sepa[1]==9) {
			if($editdata['sign_background']=="Logo")
				$shiftlogo=24;
			else if($editdata['sign_background']=="Left-Pointer")
				$shiftlogo=20;
			else if($editdata['sign_background']=="Right-Pointer")
				$shiftsuffix=20;
			else if($editdata['sign_background']=="Round")
			{
				$shiftlogo=2;
				$shiftsuffix=2;
			}
			$fontsize=12;
			$subfont=7;
			if($text2!="")
				$font2=9;
		} else if($size_sepa[1]==6) {

			if($editdata['sign_background']=="Logo")
				$shiftlogo=18;
			/*start new code*/
			else if($editdata['sign_background']=="Flat Blade With Border and Outside Image")
				$shiftlogo=25;
			else if($editdata['sign_background']=="Flat Blade No Border and Outside Image")
				$shiftlogo=25;
			else if($editdata['sign_background']=="Flat Blade With Border and Inside Image")
				$shiftlogo=25;
			else if($editdata['sign_background']=="Flat Blade No Border and Inside Image")
				$shiftlogo=25;


			/*end new code*/
			else if($editdata['sign_background']=="Left-Pointer")
				$shiftlogo=20;
			else if($editdata['sign_background']=="Right-Pointer")
				$shiftsuffix=20;
			else if($editdata['sign_background']=="Extruded with Outside Image")
				$shiftlogo=28;
			else if($editdata['sign_background']=="Round")
			{
				$shiftlogo=2;
				$shiftsuffix=2;
			}
			$fontsize=9;
			$subfont=7;
			if($text2!="")
				$font2=7;
		}

		if (isset($editdata['prefix']) && strpos($editdata['prefix'],"Arrow") && $editdata['prefix']!="NONE")
		{
			$prefix_Sep = explode(" ",$editdata['prefix']);
			$imgname = $imgname."_".$prefix_Sep[0]."_p";
			$shiftlogo = $shiftlogo+20;
		}

		if (isset($editdata['suffix']) && strpos($editdata['suffix'],"Arrow") && $editdata['suffix']!="NONE")
		{
			$suffix_Sep = explode(" ",$editdata['suffix']);
			$imgname = $imgname."_".$suffix_Sep[0]."_s";
			$shiftsuffix = $shiftsuffix+20;
		}

		if($editdata['prefix'] != "NONE"&&strpos($editdata['prefix'],'Arrow') === false)
		{
			if($uppercheckbox == "Y")
				$prefix = strtoupper($editdata['prefix']);
			else
				$prefix = $editdata['prefix'];
			$position = $editdata['position'];
		}

		if($editdata['suffix'] != "NONE" && strpos($editdata['suffix'],'Arrow') === false)
		{
			if($uppercheckbox == "Y")
				$suffix = strtoupper($editdata['suffix']);
			else
				$suffix = $editdata['suffix'];
			$position = $editdata['position'];
		}

		$imgname = $imgname.".jpg";

		$im = imagecreatefromjpeg($imgname);
		$width = imagesx($im);
		$height = imagesy($im);
		$filename = $design_id.".jpg";

		if($color_Sep[0] == "White")
			$textcolorcode = $this->getColorCode("Black");
		else
			$textcolorcode = $this->getColorCode("White");
		$textcolor = imagecolorallocate($im, $textcolorcode[0], $textcolorcode[1], $textcolorcode[2]);
		$colorcode = $this->getColorCode($color_Sep[0]);

        // This causing the function imagettftext that can not accept array
		if( !empty($editdata['sign_font']) ) {
            $font = '../content/' . $editdata['sign_font'] . '.ttf';
        } else {
            $font = '../content/Highway.ttf';
        }
        
		if(isset($editdata['sign_font']) && $editdata['sign_font'] == "Algerian")
		{
			$text = strtoupper($text);
			$text2 = strtoupper($text2);
		}

		$prefixbox = imagettfbbox($subfont, 0, $font, $prefix);
		$suffixbox = imagettfbbox($subfont, 0, $font, $suffix);
		$mainbox = imagettfbbox($fontsize, 0,$font , $text);
		$prefix_width = $prefixbox[2]-$prefixbox[0];
		$prefix_height = $prefixbox[3]-$prefixbox[5];
		$suffix_width = $suffixbox[2]-$suffixbox[0];
		$suffix_height = $suffixbox[3]-$suffixbox[5];
		$suffixtakewidth = $suffix_width;
		$textgap = 2;
		$vertextgap = 2;
		$addtextgap = 0;
		$text2addgap = 0;
		$text1addgap = 0;
		$bothupper_addgap = 0;
		$bothmixed_reduce = 0;
		$lowertext2 = 0;
		$uppertext1 = 0;
		$addy = 0;

		if($position == "Top") {
			if($prefix !="")
				$prefix_y = ($height-2*$gap)/3+$gap;
			if($suffix != "")
				$suffix_y = ($height-2*$gap)/3+$gap;
		} else if($position == "Middle") {
			if($prefix != "")
				$prefix_y = ($height-2*$gap)*2/3+$gap;
			if($suffix != "")
				$suffix_y = ($height-2*$gap)*2/3+$gap;
		} else if($position == "Bottom") {
			if($prefix != "")
				$prefix_y = $height-$gap;
			if($suffix != "")
				$suffix_y = $height-$gap;
		} else {
			if($prefix != "")
				$prefix_y = ($height-2*$gap)*2/3+$gap;
			if($suffix != "")
				$suffix_y = ($height-2*$gap)*2/3+$gap;
		}


		$textwidth=$mainbox[2]-$mainbox[0];
		$textheight=$mainbox[3]-$mainbox[5];
		$heightboundry=$height-$textheight;
		$suffix_x=$width-$gap-$suffix_width-$shiftsuffix;
		$prefix_x=$gap+$shiftlogo;

		if (!empty($editdata['sidetext'])) {
			if ($uppercheckbox=="Y")
				$streetnum=strtoupper($editdata['sidetext']);
			else
				$streetnum=$editdata['sidetext'];

			$streetnumbox=imagettfbbox($subfont, 0, $font, $streetnum);
			$streetnum_width=$streetnumbox[2]-$streetnumbox[0];
			$streetnum_height=$streetnumbox[3]-$streetnumbox[5];
			$streetnum_y=($height-2*$gap-$vertextgap-$streetnum_height*2)/3+$gap+$streetnum_height;
			$suffix_y=$height-$gap-($height-2*$gap-$vertextgap-$streetnum_height*2)/3;

			if ($streetnum_width>$suffix_width) {
				$boundary=$width-$prefix_width-$streetnum_width-2*$gap-2*$textgap-$shiftlogo-$shiftsuffix;
				$streetnum_x=$width-$gap-$streetnum_width-$shiftsuffix;
				$suffix_x=$width-($streetnum_width-$suffix_width)/2-$gap-$suffix_width;
				$suffixtakewidth=$streetnum_width;
			} else {
				$boundary=$width-$prefix_width-$suffix_width-2*$gap-2*$textgap-$shiftlogo-$shiftsuffix;
				$streetnum_x=$width-($suffix_width-$streetnum_width)/2-$gap-$streetnum_width;
				$suffixtakewidth=$suffix_width;
			}
		} else {
			$boundary=$width-$prefix_width-$suffix_width-2*$gap-2*$textgap-$shiftlogo-$shiftsuffix;
		}

		if($text2!="") {
			if (strtoupper($text2)==$text2) {
				$shrinkp=1;
				if (strtoupper($text)!=$text)
					$text2addgap=1;
				$bothupper_addgap=1;
			} else if (strtolower($text2)==$text2) {
				$shrinkp=0.8;
				if (strtoupper($text)!=$text)
					$bothmixed_reduce=1;
				else
					$lowertext2=2;
			} else {
				$shrinkp=0.8;
				if (strtoupper($text)!=$text)
					$bothmixed_reduce=1;
			}

			$mainbox2 = imagettfbbox($font2, 0,$font , $text2);
			$textwidth2=$mainbox2[2]-$mainbox2[0];
			$textheight2=$mainbox2[3]-$mainbox2[5];
			if ($textwidth2<=$boundary)
			{
				if ($prefix!=""&&($suffix!=""||$streetnum!="")) {
					$adddistance2=($width-$prefix_width-$suffixtakewidth-2*$gap-2*$textgap-$textwidth2-$shiftlogo-$shiftsuffix)/2;
					$x2=$prefix_width+$gap+$textgap+$adddistance2+$shiftlogo;
				} else if ($prefix==""&&($suffix!=""||$streetnum!="")) {
					$adddistance2=($width-$prefix_width-$suffixtakewidth-2*$gap-$textgap-$textwidth2-$shiftlogo-$shiftsuffix)/2;
					$x2=$prefix_width+$gap+$adddistance2+$shiftlogo;
				} else if ($prefix!=""&&($suffix==""&&$streetnum=="")) {
					$adddistance2=($width-$prefix_width-2*$gap-$textgap-$textwidth2-$shiftlogo-$shiftsuffix)/2;
					$x2=$prefix_width+$gap+$textgap+$adddistance2+$shiftlogo;
				} else if ($prefix==""&&($suffix==""&&$streetnum=="")) {
					$adddistance2=($width-$prefix_width-2*$gap-$textwidth2-$shiftlogo-$shiftsuffix)/2;
					$x2=$prefix_width+$gap+$adddistance2+$shiftlogo;
				}

				$y2=($height-$textheight-$textheight2)/2+$textheight+$textheight2+$lowertext2/2;
				$addtextgap=1;
			} else {
				$compresstext2="Y";
				$y2=($height-$textheight-$textheight2)/2+$textheight;
				$textimage2 = imagecreate($textwidth2, $textheight2);
				$backgroundcolor2 = imagecolorallocate($textimage2, $colorcode[0], $colorcode[1], $colorcode[2]);
				$textcolor_org2 = imagecolorallocate($textimage2, $textcolorcode[0], $textcolorcode[1], $textcolorcode[2]);
				imagettftext($textimage2,$font2, 0, 0, $textheight2*$shrinkp, $textcolor_org2, $font, $text2);
				imagecopyresampled($im, $textimage2, $prefix_width+$gap+$textgap+$shiftlogo, $y2+$bothupper_addgap+$text2addgap+$lowertext2, 0, 0, $boundary, $textheight2, $textwidth2, $textheight2);
			}
		} else {
			$textwidth2=0;
			$textheight2=0;
		}

		if($text2=="") {
			if(strtoupper($text)!=$text) {
				if($size_sepa[1]==6) {
					if($textheight==11) {
						$addy=1;
						$shrinkp=1;
						$compressy=$textheight*$shrinkp-2*$addy;
						$addy=1;
					} else {
						$shrinkp=1;
						$compressy=$textheight*$shrinkp;
						$addy=-1;
					}
				} else {
					if ($textheight==16) {
						$addy=1;
						$shrinkp=1;
						$compressy=$textheight*$shrinkp-2*$addy;
					} else {
						$shrinkp=1;
						$compressy=$textheight*$shrinkp;
						$addy=-1;
					}
				}
			} else {
				$shrinkp=1;
				$compressy=$textheight*$shrinkp-$text1addgap;
			}
		} else if(strtoupper($text)==$text) {
			$shrinkp=1;
			$compressy=$textheight*$shrinkp-$text1addgap;
			if (isset($editdata['sign_font'])&&$editdata['sign_font']=="Algerian")
				$compressy=$textheight*$shrinkp;
		} else if(strtolower($text)==$text) {
			$shrinkp=1;
			$text1addgap=2;
			$compressy=$textheight*$shrinkp-$text1addgap;
		} else {
			$shrinkp=1.2;
			$text1addgap=-2;

			if ($size_sepa[1]==6) {
				$uppertext1=0.5;
				if ($textheight==11) {
					$compressy=$textheight*$shrinkp+$mainbox[5]+$vertextgap-$text1addgap;
					$addy=3;
				} else {
					$compressy=$textheight*$shrinkp+$mainbox[5]+$vertextgap+9*$uppertext1-$text1addgap/2;
					$addy=3.0;
				}
			} else if($size_sepa[1]==9) {
				$uppertext1=0.6;
				if ($textheight==16) {
					$compressy=$textheight*$shrinkp+$mainbox[5]+$vertextgap+$uppertext1-2*$text1addgap;
					$addy=0;
				} else {
					$compressy=$textheight*$shrinkp+$mainbox[5]+$vertextgap+12*$uppertext1-$text1addgap;
					$addy=0.4;
				}
			}
		} if ($textwidth<=$boundary) {

			if($prefix!=""&&($suffix!=""||$streetnum!="")) {
				$adddistance=($width-$prefix_width-$suffixtakewidth-2*$gap-2*$textgap-$textwidth-$shiftlogo-$shiftsuffix)/2;
				$x=$prefix_width+$gap+$textgap+$adddistance+$shiftlogo;
			} else if($prefix==""&&($suffix!=""||$streetnum!="")) {
				$adddistance=($width-$prefix_width-$suffixtakewidth-2*$gap-$textgap-$textwidth-$shiftlogo-$shiftsuffix)/2;
				$x=$prefix_width+$gap+$adddistance+$shiftlogo;
			} else if($prefix!=""&&($suffix==""&&$streetnum=="")) {
				$adddistance=($width-$prefix_width-2*$gap-$textgap-$textwidth-$shiftlogo-$shiftsuffix)/2;
				$x=$prefix_width+$gap+$textgap+$adddistance+$shiftlogo;
			} else if($prefix==""&&($suffix==""&&$streetnum=="")) {
				$adddistance=($width-$prefix_width-2*$gap-$textwidth-$shiftlogo-$shiftsuffix)/2;
				$x=$prefix_width+$gap+$adddistance+$shiftlogo;
			}
			$y = ($height-$textheight-$textheight2)/2+$textheight-$vertextgap;

		} else {

			$compresstext="Y";
			$y=($height-$textheight-$textheight2)/2;
			$textimage = imagecreate($textwidth, $textheight);
			$backgroundcolor = imagecolorallocate($textimage, $colorcode[0], $colorcode[1], $colorcode[2]);
			$textcolor_org = imagecolorallocate($textimage, $textcolorcode[0], $textcolorcode[1], $textcolorcode[2]);
			imagettftext($textimage,$fontsize, 0, 0, $compressy, $textcolor_org, $font, $text);
			imagecopyresampled($im, $textimage, $prefix_width+$gap+$textgap+$shiftlogo, $y-$bothmixed_reduce-$text2addgap-$bothupper_addgap+$text1addgap+$addy, 0, 0, $boundary, $textheight, $textwidth, $textheight);

		}

		if($compresstext!="Y") {
          
            imagettftext($im, $fontsize, 0, $x, $y, $textcolor, $font, $text);

        }
		if($text2!=""&&$compresstext2!="Y") {
            imagettftext($im, $font2, 0, $x2, $y2, $textcolor, $font, $text2);
        }
		if($prefix_x!=""&&$prefix_y!="") {}
        {
            imagettftext($im, $subfont, 0, $prefix_x, $prefix_y, $textcolor, $font, $prefix);
        }

		if($suffix_x!=""&&$suffix_y!="") {
            imagettftext($im, $subfont, 0, $suffix_x, $suffix_y, $textcolor, $font, $suffix);
        }
		if($streetnum_x!=""&&$streetnum_y!="") {
            imagettftext($im, $subfont, 0, $streetnum_x, $streetnum_y, $textcolor, $font, $streetnum);
        }

		$imagepath='../design/save/previews/small/'.$filename;
		$imagepathpreview='../design/save/previews/'.$filename;
		imagejpeg($im, $imagepath);
		imagejpeg($im, $imagepathpreview);

		return $filename;

	}

	// Rretrieves accuracy image file name for streetname signs for sample
	public function getAccuracyImage($productId){
		$sql = Connection::getHandle()->prepare("SELECT sc.accuracy_image
									FROM bs_streetsign_control sc
									LEFT JOIN bs_products p ON p.default_streetsign_tool_id = sc.streetsign_tool_id
									WHERE p.id = ?");
		$sql->execute(array($productId));

		return $sql->fetchColumn();
	}


	// create a static self pricing class
	public static function create($id = NULL) {
		return new self($id);
	}
}