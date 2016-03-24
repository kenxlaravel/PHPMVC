<?php
//Set the memory limit high enough that we won't time out even with relativevly large uploads
ini_set('memory_limit', '512M');
require("../include/config.php");
require ("global-controller.php");

// Set the correct MIME type. IE < 9 needs text/html. Everything else that accepts JSON should get application/json.
if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
	header('Content-type: application/json');
} else {
	header('Content-type: text/html; charset=utf-8');
}

//Get our variables/arrays ready
$general_err = array();
$sys_errors = array();
$errors = array();
$json_array = array();
$row_id = 0;
$visitor_ip = 0;

//Get visitors information
$visitor_ip = Uploader::get_ip();
$session_id = session_id();

//Grab the customer ID if they have one
if(isset($_SESSION[CID])){
	$customer_id = $_SESSION[CID];
}

//Date and time for error log
$today = date("F j, Y")." - ".date("g:i:sa");

//Throw an error if there is nothing in the $_FILES array
if (empty($_FILES)) {
	$sys_errors[] = "Unknown Error";
	echo json_encode($json_array);

//If the array wasn't empty, proceed
} else {

	//Make sure we have an actual file
	if(isset($_FILES['files'])){

		//Define paths to files
		$active_path = '/uploads/active/'. date("Y-m") . '/';
		$orig_path = '/uploads/orig/'. date("Y-m") . '/';

		//Maximum allowed files per upload
		if(!Uploader::max_file_upload($_FILES['files']['tmp_name'])){
			$general_err[] = "Please upload your images in groups of ". Uploader::$max_file_upload ." or fewer.";
			$sys_errors[] = "Too many files in upload";
		}

		//If the directory does not exist and couldn't be created, throw an error
		if(!Uploader::directories()){
			$sys_errors[] = "Could not create directory";
		}



		//Get last uploaded row for spam check
		$sql = Connection::getHandle()->prepare("SELECT id, UNIX_TIMESTAMP(time_stamp)
							  FROM bs_builder_uploads
							  WHERE ip_address = ?
							  ORDER BY time_stamp DESC
							  LIMIT 1");
		$sql->execute(array($visitor_ip));
		$results = $sql->fetch(PDO::FETCH_ASSOC);

		//If there was an upload from this ip address, we will compare the last upload time to the current time
		//to check if the user is spamming
		if(!empty($results)){

			//The time of the previous upload
			$old_upload = $results['UNIX_TIMESTAMP(time_stamp)'];

			//Check for excessive uploading
			if(Uploader::spam_check($old_upload , time())){
				$general_err[]= "Bandwidth limit exceeded. Please try again in one minute.";
				$sys_errors[] = "Excessive post frequency detected.";
			}
		}

		//If we haven't had an error thus far
		if(empty($general_err)){

			//Go through every file in the array
			foreach($_FILES['files']['tmp_name'] as $key => $temp){

				//Collect errors
				$errors = array();
				$sys_errors = array();

				//Zero out the row id
				$row_id = 0;

				//If we know everything about the file
				if(isset($_FILES['files']['name'][$key]) && isset($_FILES['files']['type'][$key]) &&
				isset($_FILES['files']['size'][$key]) && isset($_FILES['files']['error'][$key])){

					//Collect file properties
					$image_name = $_FILES['files']['name'][$key];
					$image_type = $_FILES['files']['type'][$key];
					$image_size = $_FILES['files']['size'][$key];
					$image_error = $_FILES['files']['error'][$key];
				}

				//Instantiating Class
				$Uploader = new Uploader( $image_name, $image_type, $image_size, $image_error);

				//Check filesize
				if(!$Uploader->file_size_limit()){
					 $errors[]= 'File "'. $image_name.'" exceeds the 2 MB maximum file size.';
					 $sys_errors[]= "File size too large ($Uploader->image_size)";
				}

				//Output any errors that have occurred
				if($Uploader->upload_errors()){
					$sys_errors[] = $Uploader->upload_errors();
				}

				//Checking image validity via mime type
				if(!empty($temp)){

					if(!$Uploader->mime_check($temp)){
						$errors[]= 'File "'. $image_name. '" is not a supported image file.';
						$sys_errors[]= "Unknown file type ($Uploader->image_type)";
					}

					//Define original file extention
					switch (exif_imagetype($temp)){
						case 1:
							$ext = ".gif";
						break;

						case 2:
							$ext = ".jpg";
						break;

						case 3:
							$ext = ".png";
						break;

						default:
							$sys_errors[] = "Problem with file - ". $image_name." - Unknown file type ($image_name)";
					}

				}

				if(empty($sys_errors) && empty($errors) && $image_error == 0 ) {

					//Insert new record
					try {
						$sql = Connection::getHandle()->prepare("INSERT INTO bs_builder_uploads (time_stamp, name, ip_address)
											  VALUES(NOW(), ?, ?)");
						$sql->execute(array($image_name, $visitor_ip));
					} catch(PDOException $e) {
						$sys_errors[]= "Failed to insert record for $image_name";
					}

					//Save the row id
					$row_id = Connection::getHandle()->lastInsertId();

					//If we haven't had any errors so far, generate a hash and move the file
					if (empty($sys_errors)) {

						//Get filename hash
						$filename_hash = $Uploader->get_filename_hash();

						//Move file to new destination and rename it
						if (!move_uploaded_file($temp, $_SERVER["DOCUMENT_ROOT"].$orig_path.$filename_hash.$ext)){

							//If file failed to move, clean up db
							$sql = Connection::getHandle()->prepare("DELETE FROM bs_builder_uploads WHERE hash = ?");
							$sql->execute(array($filename_hash));

							$sys_errors[]="Failed to move $temp from ". sys_get_temp_dir(). " to ". $_SERVER["DOCUMENT_ROOT"].$orig_path.$filename_hash.$ext;

						} else {
							$orig_file = $_SERVER["DOCUMENT_ROOT"].$orig_path.$filename_hash.$ext;
						}

					}

					//Make image into a resource object for later conversion
					if(empty($sys_errors)) {

						if ($Uploader->image_to_object($orig_file)) {
							$image = $Uploader->image_object;
						}else{
							$sys_errors[]= "Failed convert $orig_file to object";
						}

						if(empty($sys_errors)){
							//Get image dimensions
							$origWidth=imagesx($image);
							$origHeight=imagesy($image);

							$targetWidth = $Uploader->targetWidth;
							$targetHeight = $Uploader->targetHeight;

							if ($targetWidth / $origWidth > $targetHeight / $origHeight) {
								$ratio = $targetHeight / $origHeight;
							} else {
								$ratio = $targetWidth / $origWidth;
							}

							$resizeHeight = round($origHeight * $ratio);
							$resizeWidth = round($origWidth * $ratio);

							$dest_img = imagecreatetruecolor($resizeWidth, $resizeHeight);

							imagealphablending($dest_img, false);
							imagesavealpha($dest_img,true);
							$transparent = imagecolorallocatealpha($dest_img, 255, 255, 255, 127);
							imagefilledrectangle($dest_img, 0, 0, $nWidth, $nHeight, $transparent);
							imagecopyresized($dest_img,$image,0,0,0,0,$resizeWidth, $resizeHeight, $origWidth, $origHeight);
							imagepng($dest_img, $_SERVER["DOCUMENT_ROOT"].$active_path.$filename_hash.'.png');

							//Update new record
							try {
								$sql = Connection::getHandle()->prepare("UPDATE bs_builder_uploads
													  SET hash = :hash,
														  original_filename = :original_filename,
														  original_directory = :original_directory,
														  converted_filename = :converted_filename,
														  converted_directory = :converted_directory,
														  session_id = :session_id
													  WHERE id = :id");
								$sql->execute(array(":hash" => $filename_hash,
													":original_filename" => $filename_hash . $ext,
													":original_directory" => $orig_path,
													":converted_filename" => $filename_hash.'.png',
													":converted_directory" => $active_path,
													":session_id" => $session_id,
													":id" => $row_id));
							} catch(PDOException $e) {
								$sys_errors[]= "Failed to update query";
							}

							if(isset($customer_id)) {

								try {

									$sql = Connection::getHandle()->prepare("UPDATE bs_builder_uploads
														  SET customer_id = ?
														  WHERE id = ?");
									$sql->execute(array($row_id));

								} catch (PDOException $e) {

									$sys_errors[]= "Failed to associate customer_id with image id $filename_hash";

									//Delete the row we are were working with
									$sql = Connection::getHandle()->prepare("DELETE FROM bs_builder_uploads
														  WHERE hash = ?");
									$sql->execute(array($filename_hash));

									if(!$execute){
										$sys_errors[]= "Failed to remove upload image id: $filename_hash";
									}

									//Delete the png that was already created
									$origenal = $_SERVER["DOCUMENT_ROOT"].$orig_path.$filename_hash.$ext;
									$converted = $_SERVER["DOCUMENT_ROOT"].$active_path.$filename_hash.'.png';

									unlink($origenal);
									unlink($converted);
								}
							}
						}
					}
				}

				if(empty($errors) || $row_id !== 0) {

					// Get the filename.
					$filename = Uploader::$dir_for_active . date("Y-m") . "/" . $filename_hash . '.png';

					// Get the image dimensions.
					list($width, $height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $filename);

					// Update the output array.
					$json_array['uploaded'][$filename_hash] = array(
						'name' => $image_name,
						'src' => $filename,
						'uploadtime' => time(),
						'w' => (int) $width,
						'h' => (int) $height
					);

				}

				foreach($errors as $file_key => $file_error) {
					$json_array['errors'][] = $file_error;
				}

			}

		if($image_error !==4){
			echo json_encode($json_array);
		}

		}else{
			foreach($general_err as $file_key => $file_error){
				$json_array['errors'][] = $file_error;
			}
			echo json_encode($json_array);
		}
	}

	if(!empty($sys_errors)){
		foreach($sys_errors as $value){
			error_log("$visitor_ip - $today - $value \r\n", 3, APP_ROOT . "/logs/builder_upload_errors.log");
		}
	}

}
ini_restore('memory_limit');