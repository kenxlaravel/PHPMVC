<?php

class Uploader{

	public $image_name;
	public $image_type;
	public $image_size;
	public $image_error;

	//Contains value of image oibject
	public $image_object;

	//Thumbnail size
	public $targetWidth = 260;
	public $targetHeight = 260;

	//Max number of files allowed in a single upload
	static public $max_file_upload = 100;

	//Image destionation dirs
	static public $dir_for_orig = '/uploads/orig/';
	static public $dir_for_active = '/uploads/active/';


	//Wait time between uploads (seconds)
	static protected $spam_interval = 3;

	//Max size per file limit in bytes
	protected $size_limit = 2097152;


	//Constructor
	public function __construct($image_name, $image_type, $image_size, $image_error){
		$this->image_name = $image_name;
		$this->image_type = $image_type;
		$this->image_size = $image_size;
		$this->image_error = $image_error;

	}


	//Grab the user IP address
	static public function get_ip() {
		$ip= 0;

		if (getenv("HTTP_CLIENT_IP")){
			$ip = getenv("HTTP_CLIENT_IP");
		}else if(getenv("HTTP_X_FORWARDED_FOR")){
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}else if(getenv("REMOTE_ADDR")){
			$ip = getenv("REMOTE_ADDR");
		}else{
			$ip = "UNKNOWN";
		}
		return $ip;
	}



	//Maximum allowed files per upload
	static public function max_file_upload($file_array){
		if(!empty($file_array)){
			if(count($file_array) > self::$max_file_upload){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}



	//If directory does not exist, create it
	static public function directories(){


		if(!is_dir($_SERVER["DOCUMENT_ROOT"].self::$dir_for_active. date("Y-m") . '/') || !is_dir($_SERVER["DOCUMENT_ROOT"].self::$dir_for_orig. date("Y-m"))){
			$dir1 = mkdir($_SERVER["DOCUMENT_ROOT"].self::$dir_for_orig. date("Y-m") . '/', 0755 , true);
			$dir2 = mkdir($_SERVER["DOCUMENT_ROOT"].self::$dir_for_active. date("Y-m") . '/', 0755 , true);

			if(!$dir1 || ! $dir2){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}



	//Spam protection
	static public function spam_check($old_upload, $new_upload){
		if($new_upload - $old_upload < self::$spam_interval){

			return true;
		}else{
			return false;
		}
	}



	//Checking image validity via mime type
	public function mime_check($file){

		if(isset($file)){
			$mime_type_filter = array("1" => 'IMAGETYPE_GIF', "2" => 'IMAGETYPE_JPEG', "3" => 'IMAGETYPE_PNG');
			if (!in_array(exif_imagetype($file), array_keys($mime_type_filter))) {
				return false;
			}else{
				return true;
			}

		}else{
			return false;
		}

	}



	public function file_size_limit(){
		if($this->image_size > $this->size_limit || $this->image_error == 1 || $this->image_error == 2 ){
			return false;
		}else{
			return true;
		}
	}



	//Checking possible upload errors
	public function upload_errors(){

		switch($this->image_error){
			case (int)3:
			return  "The uploaded file was only partially uploaded";
			break;

			case (int)4:
			return "No file was uploaded";
			break;

			case (int)6:
			return "Missing a temporary folder";
			break;

			case (int)7:
			return "Failed to write file to disk";
			break;

			default:
			return false;
		}

	}



	public function image_to_object($orig_file){

		if(isset($orig_file)){

			switch (exif_imagetype($orig_file)){
				case 1:
				return	$this->image_object = imagecreatefromgif($orig_file);
				break;

				case 2:
				return	$this->image_object = imagecreatefromjpeg($orig_file);
				break;

				case 3:
				return	$this->image_object = imagecreatefrompng($orig_file);
				break;

				default:
					return false;
			}

		} else {
			return false;
		}
	}



	//Random filename hash function
	public function get_filename_hash() {
		//Seed the random generator
		mt_srand($this->make_seed());

		//Alphanumeric upper/lower array
		$alfa = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$hash = "";

		//Loop through and generate the random hash
		for($i = 0; $i < 10; $i ++) {
		  $hash .= $alfa[mt_rand(0, strlen($alfa)-1)];
		}

		//If there is a duplicate, run this function again
		if($this->filename_hash_exists($hash)) {
			$hash = $this->get_filename_hash();
		}
		//Return the hash
		return $hash;
	}



	//Random generator seed function
	private function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}



	//Duplicate hash checker
	function filename_hash_exists($hash) {

		$sql = Connection::getHandle()
                    ->prepare("SELECT COUNT(*) AS count FROM bs_builder_uploads WHERE hash = ?");
		$sql->execute(array($hash));
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		//Return true if a duplicate is found
		if($row['count'] > 0) {
			return true;
		}
	}
}