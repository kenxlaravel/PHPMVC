<?php
class RenderPDFSetup
{
	private $destination;
	private $myRenderData;
	private $rootdir;
	private $background;
	private $maxwidth;
	private $maxheight;
	private $filepath;
	private $sid;
	private $cachedir = 'cache/renders';


	public function __construct($rootdir = NULL )
	{

		// If the root directory argument wasn't supplied, assume the document root.
		if(!isset($rootdir)) { $this->rootdir = $_SERVER['DOCUMENT_ROOT']; }

		// Prepend the cache directory with the root directory.
		$this->cachedir = $this->rootdir . '/' . $this->cachedir;

	}

	public function getRenderPDF($hash,$data,$overwrite=false)
	{

		//After hash as a new file create subdirectory n use directly, no need of encodeFilename
			$subdirectory = $this->cachedir . '/' . substr($hash, 0, 2);
			$filepath = $subdirectory . '/' . $hash.'.pdf';

		 //Store the render data.
		   $this->myRenderData = $data;



		//$filename = $this->encodeFilename($this->sid, $time,'pdf');
		//need to check with renderdata if already same as previous data then touch file else create new.
		if (!$overwrite && file_exists($filepath)) {
			// If the file already exists and an overwrite was not requested, touch it.
			touch($filepath);
		} else {
		// If the file does not already exist or an overwrite was requested, create it.
		 if (!$this->createPDF( $filepath)) {

			return false;
			}
		}

		return $this->encodePDFUrl($filepath);
	}

	private function encodePDFUrl($filename) {

		return '/images/designs/' . basename($filename);
	}

	private function prepareCacheDir($filepath) {
		$subdirectory = pathinfo($filepath, PATHINFO_DIRNAME);
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
		return true;
	}
	private function determineScalingRatio($backgroundFile,$maxwidth,$maxheight)
	{

		$outfile='';
		$p = new pdflib();
		$p->begin_document($outfile,"");

		//Open background file
		$indoc = $p->open_pdi_document($backgroundFile, "");

		//Get width & height for background
		$pagewidth =$p->pcos_get_number($indoc, "pages[0]/width");
		$pageheight =$p->pcos_get_number($indoc, "pages[0]/height");

		//Calculate scaling ratio
		$new_ratio = min($pagewidth/$maxwidth, $pageheight/$maxheight);

	  return $new_ratio;


	}
	private function createPDF($filename)
	{
		 $this->filepath=$filename;
		 $BuilderImages = new BuilderImages();

		 // Prepare the cache directory, and only proceed if successful.
				if ($this->prepareCacheDir($this->filepath))
				{
					// Create the file, and fix it's permissions
					$file_path=fopen($this->filepath,'w+');
					chmod($this->filepath, 0777);

					// Check that the background data was supplied before proceeding.
					if(isset($this->myRenderData['background'])){

						$this->sizes=$this->myRenderData['background']['size'];
						$scheme=$this->myRenderData['background']['scheme'];

							$pdffile =$BuilderImages->getPDFBackground($this->sizes, $scheme);

							$path_back=pathinfo($pdffile);

							if($path_back['extension']=='pdf' && file_exists($pdffile))
							{

							//Gether width & height from renderdata for background
						 	$width=$this->myRenderData['background']['w'];
							$height=$this->myRenderData['background']['h'];

							//Determine scaling ratio
							$this->ratio= $this->determineScalingRatio($pdffile,$width,$height);

							//Create background pdf as a background for rest of the asset
							$background=$this->pdf_background($pdffile,$this->filepath);

							//Write value from buffer to a new file
								$fh=fopen($this->filepath,'w+');
								chmod($this->filepath,0777);
								fwrite($fh,$background);
								fclose($fh);

								if($this->myRenderData['elements']){
								 foreach($this->myRenderData['elements'] as $key => $value)
								 {
								 //var_dump ($this->myRenderData['elements'] );
									if($value['type']=='artwork')
									{
										$color=$value['color'];
										if(!isset($color))
											$color='default';
										$artwork_pdf=$BuilderImages->getPDFArtwork($value['id'], $color);

										$path=pathinfo($artwork_pdf);
										if($path['extension']=='pdf')
										{
											//Gether x, y , width & height from renderdata for artwork
											$x=$value['x'];
											$y=$value['y'];
											$w=$value['w'];
											$h=$value['h'];
											//create temp directory and sub directory for temporary background, artwork &  upload
											$out_art=$this->pdf_resize($artwork_pdf,$w,$h);

											//Prepare temporary directory for resized artwork
												$artwork=tempnam("/tmp", "temp_artwork.pdf");

												//Write value from buffer to a new file
												$fh=fopen($artwork,'w');
												fwrite($fh,$out_art);
												fclose($fh);

												//Define background pdf for artwork to be placed
												$background=$this->filepath;

												//Get merged buffer	for artwork
												$merged=$this->merge_pdf($background,$artwork,$x,$y,$h);

												//Write to output file
												$fh=fopen($this->filepath,'w+');
												chmod($this->filepath,0777);
												fwrite($fh,$merged);
												fclose($fh);

												unlink($artwork);
										}

									}
									if($value['type']=='upload')
									{

									$directory='';
									$filename='';

									//Query database to get uploaded image
										$sql = Connection::getHandle()->prepare("SELECT original_filename AS original_filename,
																		   original_directory AS original_directory
																	FROM bs_builder_uploads
																	WHERE hash = ?");
										$sql->execute(array($value['id']));
										while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
											$directory=$row['original_directory'];
											$filename=$row['original_filename'];
										}

										$upload=$this->rootdir.$directory.$filename;

										//Gether x, y , width & height from renderdata for uploaded image
										$x=$value['x'];
										$y=$value['y'];
										$w=$value['w'];
										$h=$value['h'];

										//Define background pdf for uploaded image to be placed
										$background=$this->filepath;

										//Get merged buffer for uploded image
										$uploaded=$this->pdf_upload($upload,$w,$h,$background,$x,$y);

										if ($uploaded === false) {
											return false;
										}

										//Write to output file
										$fh=fopen($this->filepath,'w+');
										chmod($this->filepath,0777);
										fwrite($fh,$uploaded);
										fclose($fh);
										//unlink($background);
									}
									if($value['type']=='text')
									{

										$scale=$this->ratio;
										//prepare an array of text breaks with \n/\r
										$lines = preg_split('/\n|\r\n?/', $value['content']);

										//Gether alignment,font,fontsize,leading,color for each text element of rendered data
										$align=$value['alignment'];
										$font=$value['font'];
										if($font=='')
											$font='nimbussansbold';
										$fontSize=($value['fontsize']) * $scale;
										$line_height=($value['leading']) * $scale;
										$color=$value['color'];
										if($color==NULL)
											$color='black';
										$baseline=$value['baselineoffset'];
										//Prepare Query to get color value from database
										$sql = Connection::getHandle()->prepare("SELECT spot_color_name AS spot_color_name,
																		   spot_c AS spot_c,
																		   spot_m AS spot_m,
																		   spot_y AS spot_y,
																		   spot_k AS spot_k,
																		   tint_percent AS tint_percent
																	FROM bs_builder_colors
																	WHERE colors_ref = ?
																	AND active = 1 ");
										$sql->execute(array($color));

										while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
											$spot_color=$row['spot_color_name'];
											$spot_c=($row['spot_c'] )/ 100;
											$spot_m=($row['spot_m'])/100;
											$spot_y=($row['spot_y'])/100;
											$spot_k=($row['spot_k'])/100;
											$tint=($row['tint_percent'])/100;
										}



										//Query database to get font file name

										$sql = Connection::getHandle()->prepare("SELECT filename_server AS filename
													   FROM bs_fonts
													   WHERE
													   		font_ref is not NULL
													   AND 	font_ref <> ''
													   AND 	font_ref = ?
													   AND 	active = 1 ");
										$sql->execute(array($font));

										while ($row_font = $sql->fetch(PDO::FETCH_ASSOC)) {
											$font_file = $row_font['filename'];
										}




										$font=$this->rootdir.$font_file;

										//$y=$value['y'] + ($line_height * $scale)- $line_height;

										$y= $value['y'] ;
										$y=round(($y+$baseline)*$scale);

										//If blank lines exists
										  if($lines!=''){
										  //Loop through each line
											foreach($lines as $key=>$line)
											{

												// $y+=$baseline;
												$x=$value['x'] * $scale;
												//Call function to place text line on output file
												$this->text_merge($this->filepath,$font,$spot_color,$spot_c,$spot_m,$spot_y,$spot_k,$fontSize,$line,$x,$y,$align,$baseline,$line_height);
												//Recalculate y co-ordinate to allow line spacing
												$y=($y+$line_height) ;

											}//Line loop ends here

										  }//Checking lines ends here

										}//Text element ends here

									  }//Loop foreach elements ends here

								   }//elements object ends here
								 }

				  }
				}
				return true;
		}
	private function pdf_background($imagefile,$pdffile)
	{

			$outfile =  "";
			$p = new pdflib();

		 	$p->set_parameter("license",pdflib_license);

			$p->begin_document($outfile, "");

			$scale=$this->ratio;

		   //  Open the first page of the background input PDF
			$indocEN = $p->open_pdi_document($imagefile, "");
			$pageEN = $p->open_pdi_page($indocEN, 1, "");

			$p->begin_page_ext(0,0, "");

			// Place the imported page on the background layer of the output page
			$p->fit_pdi_page($pageEN, 0,0, "adjustpage");
			$p->close_pdi_page($pageEN);

			//Finish a page and document
			$p->end_page_ext("");
			$p->end_document("");

			//Get document buffer
			$buf = $p->get_buffer();

		return $buf;


	}
	private function pdf_upload($imagefile,$w,$h,$pdffile,$x,$y)
{

		$scale=$this->ratio;

		    $outfile =  "";
			$p = new pdflib();
			$p->set_parameter("license",pdflib_license);
			/*  open new PDF file; insert a file name to create the PDF on disk */
			$p->begin_document($outfile, "");

			//Open pdffile & page
			$indocEN = $p->open_pdi_document($pdffile, "");
			$pageEN = $p->open_pdi_page($indocEN, 1, "");

			//Get page width   & height
			$pagewidth =$p->pcos_get_number($indocEN, "pages[0]/width");
			$pageheight =$p->pcos_get_number($indocEN, "pages[0]/height");

			//Begin page with specific width & height
			$p->begin_page_ext(0,0, "");

			$p->fit_pdi_page($pageEN, 0,0, "adjustpage");
			$p->close_pdi_page($pageEN);

			//Turn error reporting on before we load the image
			$p->set_parameter("errorpolicy", "return");

		    //Load image file to pdflib object
			$image = $p->load_image("auto", $imagefile, "");

			//The image will = 0 if an error has occurred
			if ($image == 0) {
				return false;
			}

			//Set the "errorpolicy" parameter to "exception" again
			$p->set_parameter("errorpolicy", "exception");

		   //Get image width & heigth
		    $imagewidth = $p->info_image($image, "imagewidth", "");
			$imageheight = $p->info_image($image, "imageheight", "" );

			$x=round ($x * $scale);
			$y=round($pageheight-($y*$scale)-($h*$scale));

		   $bw=round($w * $scale);
		   $bh=round($h * $scale);


		   $buf =  "boxsize={" . $bw . " " . $bh . "} fitmethod=entire";
		   $p->fit_image($image, $x,$y,$buf);

		   //Fit an image at particular co-ordinates on pdf
		   // $p->fit_image($image, $x,$y,"");

			// Close image
		    $p->close_image($image);
			//Finish a page and document
			$p->end_page_ext("");
		    $p->end_document("");

		//Get output from memory
		$buf = $p->get_buffer();

	 return $buf;

	}
	private function text_merge($pdffile,$font,$color,$spot_c,$spot_m,$spot_y,$spot_k,$fontSize,$line,$x,$y,$align,$baseline,$line_height)
	{

	$p = new PDFlib();

			$p->set_parameter("license",pdflib_license);
			$outfile='';
			$spot;
			/*  open new PDF file; insert a file name to create the PDF on disk */
			$p->begin_document($outfile,"");

			$indoc = $p->open_pdi_document($pdffile, "");
			$text_page = $p->open_pdi_page($indoc, 1, "");

			$pagewidth =$p->pcos_get_number($text_page, "pages[0]/width");
			$pageheight =$p->pcos_get_number($text_page, "pages[0]/height");

		   //Begin page with specific width & height
			$p->begin_page_ext(0,0, "");
			$p->fit_pdi_page($text_page, 0, 0, "adjustpage");

			//set parameter for fonts with .ttf
			$p->set_parameter("FontOutline","font=".$font);
			$font = $p->load_font("font", "unicode", "embedding");


			// $p->set_parameter("FontOutline","font=".$this->rootdir."/fonts/centuryblack/centuryblack.ttf");
			 if($color==NULL)
			 {
			 	$p->setcolor("fill", "cmyk", $spot_c,$spot_m,$spot_y,$spot_k); // define alternate CMYK values
			}
			else
			{
			//NOTE: value from database will be overwritten if it matches original Pantone name for spot color
			$p->setcolor("fill", "cmyk", $spot_c,$spot_m,$spot_y,$spot_k); // define alternate CMYK values
			$spot = $p->makespotcolor($color); // derive a spot color from it
			$p->setcolor("fill", "spot", $spot, 1, 0, 0);
			}
			$scale= $this->ratio;

		   //set parameter supports Format used to interpret the supplied text.
			$p->set_parameter("textformat", "utf8");

			//get width of string to be placed
			$width=$p->stringwidth($line,$font,$fontSize);
			$height=$line_height;

			//Set x co-ordinate according to alignment
			if($align=='center')
			{
				$x= $x  - ($width/2);
			}

			else if($align=='right')
			{
				$x=$x- $width;
			}
			else if($align=='left')
			{
				$x=$x;
			}
			$y=$pageheight-$y;
			 	 $textopts = "font=".$font." fontsize=".$fontSize." encoding=unicode";

			 //Fit text line to the PDF
			$p->fit_textline($line, $x,$y,$textopts);

			// Close page
			$p->close_pdi_page($text_page);
			//Finish a page and document
			$p->end_page_ext("");
			$p->end_document("");

			//buffer the output pdf
			$buf = $p->get_buffer();

			//Write to output pdf
			$fh=fopen($this->filepath,'w+');
			fwrite($fh,$buf);
			fclose($fh);
	}
	private function merge_pdf($background,$artwork,$x,$y,$h)
	{
		    $outfile =  "";
			$p = new pdflib();
			$p->set_parameter("license",pdflib_license);
			$p->begin_document($outfile, "");

			$scale=$this->ratio;

		   //  Open the first page of the background input PDF
			$indocBG = $p->open_pdi_document($background, "");
			$pageBG = $p->open_pdi_page($indocBG, 1, "");

		   //  Open the first page of the artwork input PDF
			$indocAW = $p->open_pdi_document($artwork, "");
			$pageAW = $p->open_pdi_page($indocAW, 1, "");

			//Get page width & height of background
			$pagewidth =$p->pcos_get_number($indocBG, "pages[0]/width");
			$pageheight =$p->pcos_get_number($indocBG, "pages[0]/height");

			//Get page width & height of artwork
			$pagewidthA =$p->pcos_get_number($indocAW, "pages[0]/width");
			$pageheightA =$p->pcos_get_number($indocAW, "pages[0]/height");


			$p->begin_page_ext(0,0, "");

			// Place the imported page on the background layer of the output page
			$p->fit_pdi_page($pageBG, 0,0, "adjustpage");
			$p->close_pdi_page($pageBG);

			//Scale x & y co-ordinate to place artwork on pdf
			// $x= round($x * $scale);
			// $y= round($y * $scale);

			$x=round ($x * $scale);
			$y= round($pageheight-$pageheightA -( $y * $scale));


			// Place the imported page on the artwork layer of the output page
			$p->fit_pdi_page($pageAW,$x,$y ,"fitmethod=clip");
			$p->close_pdi_page($pageAW);

			//Finish a page and document
			$p->end_page_ext("");
			$p->end_document("");

			//Get document buffer
			$buf = $p->get_buffer();

		return $buf;

	}
	private function pdf_resize($pdffile,$w,$h)
	{

		   $outfile='';

		   $p = new pdflib();
		   $p->set_parameter("license",pdflib_license);
		   //Begin new pdf document
		   $p->begin_document($outfile,"");
			// Open the input PDF
			$indoc = $p->open_pdi_document($pdffile, "");
			//Open page of input pdf
			$resize_page = $p->open_pdi_page($indoc, 1, "");
			//Get original width & heigth of input pdf document
			$pagewidth =$p->pcos_get_number($indoc, "pages[0]/width");
			$pageheight =$p->pcos_get_number($indoc, "pages[0]/height");

			$scale=$this->ratio;

			//Begin document with new scaled dimensions
			 $p->begin_page_ext($w*$scale,$h*$scale, "");

			//Fit 	existing page into new scaled dimensions
			 $p->fit_pdi_page($resize_page, 0, 0, "boxsize={" . $w * $scale. " " .$h *$scale . "} fitmethod=meet");

			//End page and close document
			$p->end_page_ext("");
			$p->close_pdi_page($resize_page);
			$p->end_document("");

			//Get value of pdf in buffer
			$buf = $p->get_buffer();

		return $buf;
	}
}