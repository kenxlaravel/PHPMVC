<?php

	class FlashDesign {


		public function extract_canvas_data($file, $design_id, $cpi) {

			// First we need to clean out any old canvas data that may already exist for this design ID
			$sql = Connection::getHandle()->prepare("DELETE FROM bs_flash_design_textareas WHERE design_id = ?");
			$sql->execute(array($design_id));

			$sql = Connection::getHandle()->prepare("DELETE FROM bs_flash_design_clipart WHERE design_id = ?");
			$sql->execute(array($design_id));

			$sql = Connection::getHandle()->prepare("DELETE FROM bs_flash_design_uploads WHERE design_id = ?");
			$sql->execute(array($design_id));

			// Grab the file path for the zip
			$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

			// extract the zip to grab the xml canvas file contents
			$zip = new ZipArchive;
			$res = $zip->open($file);

			// Make sure we have the zip archive to extract
			if ($res === TRUE) {

				// extract it to the path we determined above
				$zip->extractTo($path . "/" . $design_id);
				$zip->close();

				// Build a path to the canvas xml file
				$canvas_file = $path . "/" . $design_id . "/canvas.xml";

				// Make sure that the canvas file exists before proceeding
				if (file_exists($canvas_file)) {

					$xml = simplexml_load_file($canvas_file);

					// Get the background.
					$background = $xml->page->BackgroundImageItem;

					// Look in the database to get an ID for this background
					$sql = Connection::getHandle()->prepare("SELECT COUNT(*) AS count, id FROM bs_flash_backgrounds WHERE overlay = ?");
					$sql->execute(array($background['filename']));

					$row = $sql->fetch(PDO::FETCH_ASSOC);

					// If we have one background row in the DB, use that
					if ($row['count'] == 1) {

						// Update the background for this custom product
						$sql = Connection::getHandle()->prepare("UPDATE bs_product_custom SET background_id = ? WHERE custom_product_id = ?");
						$sql->execute(array($row['id'], $cpi));

					}

					// Grab the textarea items
					$items = $xml->page->TextAreaItem;

					// Grab the image uploads
					$uploads = $xml->page->ImageItem;

					// Grab the clipart
					$cliparts = $xml->page->SWFImageItem;

					// Check if we have items
					foreach ($items as $item) {

						$flow = $item->children('http://ns.adobe.com/textLayout/2008');
						$textflow = $flow->children('http://ns.adobe.com/textLayout/2008');
						$flow_attributes = $flow->attributes();


						// Insert the textarea
						$sql = Connection::getHandle()->prepare("INSERT INTO bs_flash_design_textareas (design_id, font_family) VALUES(?, ?)");
						$sql->execute(array($design_id, $flow_attributes['fontFamily']));
						$textarea_id = Connection::getHandle()->lastInsertId();

						// Grab the paragraphs within the textarea
						$ps = $textflow->p;

						// Loop through each textFlow
						foreach ($ps as $p) {

							$p_attributes = $p->attributes();

							$sql = Connection::getHandle()->prepare("INSERT INTO bs_flash_design_textlines(textarea_id, alignment) VALUES(?, ?)");
							$sql->execute(array($textarea_id, $p_attributes['textAlign']));
							$textline_id = Connection::getHandle()->lastInsertId();

							$spans = $p->children('http://ns.adobe.com/textLayout/2008');
							foreach($spans as $span) {

								$span_attributes = $p->span->attributes();

								// Insert the paragraph
								$sql = Connection::getHandle()->prepare("INSERT INTO bs_flash_design_text (textline_id, contents, alignment, color, font_size, font_family)
																  VALUES(:textline_id, :contents, :alignment, :color, :font_size, :font_family)");
								$sql->execute(array(":textline_id" => $textline_id,
													":contents"    => $span,
													":alignment"   => $p_attributes['textAlign'],
													":color"       => mb_substr($span_attributes['color'], -6),
													":font_size"   => $span_attributes['fontSize'],
													":font_family" => $span_attributes['fontFamily']));


							}

						}

					}


					foreach ($uploads as $upload) {

						$sql = Connection::getHandle()->prepare("INSERT INTO bs_flash_design_uploads (design_id, original_filename, new_filename, new_file_location)
														  VALUES (:design_id, :original_filename, :new_filename, :new_file_location)");
						$sql->execute(array(":design_id"         => $design_id,
											":original_filename" => $upload['filename'],
											":new_filename"      => $upload['internalFilename'],
											":new_file_location" => 'design/save/zips/' . $design_id . '/' . $upload['internalFilename']));
					}

					foreach ($cliparts as $clipart) {

						// We'll have to find the clipart id
						$sql = Connection::getHandle()->prepare("SELECT id FROM bs_flash_clipart WHERE image_name = ? AND active = TRUE");
						$sql->execute(array($clipart['filename']));

						$row = $sql->fetch(PDO::FETCH_ASSOC);

						// Make sure we have something, and insert
						if ($row['id'] > 0) {
							$sql = Connection::getHandle()->prepare("INSERT INTO bs_flash_design_clipart (design_id, clipart_id) VALUES(?, ?)");
							$sql->execute(array($design_id, $row['id']));
						}

					}

					return true;

				// The canvas file did not exist. Return false.
				} else {

					return false;

				}

			// The zip did not exist. Return false.
			} else {

				return false;

			}

		}



		function GetCustomProductFontLayoutByID ($layoutId) {

			$results = array();

			$sql_font = Connection::getHandle()->prepare(
				"SELECT id AS layout_id, name AS layout, font AS font, xml AS xml FROM bs_flash_tools WHERE id = :layoutId"
			);

			$sql_font->bindParam(":layoutId", $layoutId, PDO::PARAM_INT);

			if( $sql_font->execute() ) {

				while ($row = $sql_font->fetch(PDO::FETCH_ASSOC)) {

					$results['layout'] = $row['layout_id'];
					$results['font']   = $row['font'];
					$results['xml']    = $row['xml'];
				}
			}

			return $results;
		}
	}