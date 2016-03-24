<?php

class RenderVectorSetup
{

	private $myRenderData;
	private $rootdir;
	private $background;
	private $maxwidth;
	private $maxheight;
	private $filepath;
	private $sid;
	private $cachedir = 'cache/renders';

	public function __construct( $rootdir = NULL )
	{


		// If the root directory argument wasn't supplied, assume the document root.
		if(!isset($rootdir)) { $this->rootdir = APP_ROOT; }
		// Prepend the cache directory with the root directory.
		$this->cachedir = $this->rootdir . '/' . $this->cachedir;

	}

	public function getRasterImage($maxwidth, $maxheight, $hash,$data,$overwrite=false) {

		//After hash as a new file create subdirectory n use directly , no need of encodeFilename
		$subdirectory = $this->cachedir . '/' . substr($hash, 0, 2);
		$filepath = $subdirectory . '/' . $hash.'.png';

		$this->myRenderData=$data;

		//need to check with renderdata if already same as previous data then touch file else create new.
		if (!$overwrite && file_exists($filepath)) {
			// If the file already exists and an overwrite was not requested, touch it.
			touch($filepath);

		} else {
			// If the file does not already exist or an overwrite was requested, create it.
			if (!$this->createRasterImage($maxwidth, $maxheight, $filepath)) {
				return false;
			}

		}
		// Return the image URL.
		return $this->encodeImageUrl($filepath);


	}

	 private function encodeImageUrl($filename) {

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
	private function determineScalingRatio($width,$height,$maxwidth,$maxheight)
	{
			$new_ratio = 1;
			// If all variables are greater than 0.
			if ($width > 0 && $height > 0 && $maxwidth > 0 && $maxheight > 0) {
				$new_ratio = min($maxwidth/$width, $maxheight/$height);
			}
		return $new_ratio;
	}
	private function createRasterImage($maxwidth,$maxheight,$filename)
	{

		 $this->filepath=$filename;

		 // Prepare the cache directory, and only proceed if successful.
				if ($this->prepareCacheDir($this->filepath))
				{
					// Create the file, and fix it's permissions
					$file_path=fopen($this->filepath,'w+');
					chmod($this->filepath, 0777);

					// Check that the background data was supplied before proceeding.
					if(isset($this->myRenderData['background'])){
						$this->ratio= $this->determineScalingRatio($this->myRenderData['background']['w'], $this->myRenderData['background']['h'], $maxwidth, $maxheight);
						$this->svg_background();

					}


					//Check if background exists
					if(file_exists($this->background))
					{

					//Loop through all elements
					if($this->myRenderData['elements']){

						foreach($this->myRenderData['elements'] as $key => $value)
						{
							if($value['type']=='artwork')
							{
								$this->svg_artwork($value);
							}

						 if($value['type']=='upload')
							{
								$this->svg_upload($value);
							}
							if($value['type']=='text')
							{
								$lines = preg_split('/\n|\r\n?/', $value['content']);

								$font_ref=$value['font'];
								if($font_ref=='')
									$font_ref='nimbussansbold';

								//Get font filename
								$stmt_font=Connection::getHandle()->prepare("SELECT filename_server AS filename FROM bs_fonts WHERE font_ref is not null and font_ref <> '' and font_ref=:fonts");
								$stmt_font->execute(array(":fonts"=>$font_ref));
								while($row_font=$stmt_font->fetch(PDO::FETCH_ASSOC))
								{
									$font=$this->rootdir.$row_font['filename'];
								}

								$colors_ref=$value['color'];
								if($colors_ref==NULL)
									$colors_ref='black';
								//Get color value
								$stmt_color=Connection::getHandle()->prepare("SELECT value FROM bs_builder_colors WHERE colors_ref=:color");
								$stmt_color->execute(array(":color"=>$colors_ref));
								while($row_color=$stmt_color->fetch(PDO::FETCH_ASSOC)){
									$color_value='#'.$row_color['value'];
								}

								 $scale=$this->ratio;
								$align=$value['alignment'];
								$fontsize=$scale * $value['fontsize'];
								$baseline=$value['baselineoffset'];
								$line_height=$value['leading'];
								//$line_height=round(($scale) * ($value['leading']));

								$y= $value['y'] + $baseline ;

								//$y=$line_height * $baseline;

								$x=$value['x'];
								$add_lineheight=0;

								if($lines!=''){
									foreach($lines as $line)
										{
											//Recalculate y co-ordinate to allow line spacing
											$y=$y+$add_lineheight;
											$this->svg_text($line,$x,$y,$font,$fontsize,$color_value,$align );
											//Assign value for line spacing
											$add_lineheight=$line_height;
										}
									}
							}
						}
					 }

					//Check for available options
					if($this->myRenderData['options']){
							$this->svg_option();
						}
					}
					else
						return false;
				}


				return true;
	}
	private function svg_background()
	{
			$this->BuilderImage = new BuilderImages();
			$this->sizes=$this->myRenderData['background']['size'];
			$scheme=$this->myRenderData['background']['scheme'];
			$scale=$this->ratio;

			//call from builderimage
			$this->background=$this->BuilderImage->getVectorBackground($this->sizes,$scheme);

			if(isset($this->background)){
			//Gether width & height of background
			$width=$scale * ($this->myRenderData['background']['w']); //106.14
			$height=$scale *($this->myRenderData['background']['h']); // 158.6

			$im1 = new Imagick($this->background);

			$im1->setBackgroundColor(new ImagickPixel('transparent'));

			$im1->readImage($this->background);
			//Get Image resolution for artwork
			$res = $im1->getImageResolution();
			//echo $res['x']; 72
			$x_ratio = $res['x'] / $im1->getImageWidth();
			$y_ratio = $res['y'] / $im1->getImageHeight();
			//Remove artwork image
			$im1->removeImage();
			//Set resolution for artwork with new width & height
			$im1->setResolution($width * $x_ratio, $height * $y_ratio);
			//Read image
			$im1->readImage($this->background);
			$im1->setImageFormat("png");
			$im1->writeImage($this->filepath);
		}
		else {
			return false;
		}
	}

	private function svg_artwork($value)
	{
		$this->BuilderImage = new BuilderImages();
		$color=$value['color'];
		if(!isset($color))
			$color='default';
		//Call from builderimage
		$image=$this->BuilderImage->getVectorArtwork($value['id'],$color );
		if(isset($image)){
		//Get scaling ratio for the image
		$scale=$this->ratio;

		//scale width, height ,x & y co-ordinates based on scaling ratio
		$x=$scale * $value['x'];
		$y=$scale * $value['y'];

		$width=$scale*$value['w'];
		$height=$scale*$value['h'];


			//first image as background
			$first=new Imagick($this->filepath);

			//New imagick object for second image to be merge
			$im1 = new Imagick($image);
			$im1->setBackgroundColor(new ImagickPixel('transparent'));
			$im1->readImage($image);

			//Get Image resolution for artwork
			$res = $im1->getImageResolution();
			$x_ratio = $res['x'] / $im1->getImageWidth();
			$y_ratio = $res['y'] / $im1->getImageHeight();

			//Remove artwork image
			$im1->removeImage();
			//Set resolution for artwork with new width & height
			$im1->setResolution($width * $x_ratio, $height * $y_ratio);

			//Read image
			$im1->readImage($image);

			//merge two images
			$first->setImageColorspace($im1->getImageColorspace() );
			$first->compositeImage($im1, $im1->getImageCompose(),$x,$y);
			$first->writeImage($this->filepath);
			}
			else
			{
				return false;
			}
		}
	private function svg_upload($value)
	{

			$directory='';
			$filename='';
			$stmt_upload=Connection::getHandle()->prepare("SELECT original_directory,original_filename FROM bs_builder_uploads WHERE hash=:hash");
			$stmt_upload->execute(array(":hash"=>$value['id']));
			while($row_upload=$stmt_upload->fetch(PDO::FETCH_ASSOC)){
					$directory=$row_upload['original_directory'];
					$filename=$row_upload['original_filename'];
			}


			$image= $this->rootdir.$directory.$filename;

			//Gether x,y co-oridinates & height/width for rendered data of upload
			$scale=$this->ratio;
			$x=$scale*$value['x'];
			$y=$scale*$value['y'];

			$width=$scale*$value['w'];
			$height=$scale*$value['h'];

			//first image as background
			 //Original
			$first=new Imagick($this->filepath);
			//New imagick object for second image to be merge
			$im1 = new Imagick();
			$im1->setBackgroundColor(new ImagickPixel('transparent'));
			$svg1 = file_get_contents($image);
			$im1->readImageBlob($svg1);

			$im1->setImageFormat("png");
			//resize second image (artwork/ upload)
			$im1->ThumbnailImage($width,$height,true);

			//merge two images
			$first->setImageColorspace($im1->getImageColorspace() );
			$first->compositeImage($im1, $im1->getImageCompose(),$x,$y);
			$first->writeImage($this->filepath);
	}
	private function svg_option()
	{

		//Loop through each object
		foreach($this->myRenderData['options'] as $key => $value)
			{
				$image=$this->BuilderImage->getVectorOption($value['id'], $value['value'],$this->sizes);
			}
			if(isset($image)){
			$second=new Imagick($this->filepath);
			$geo=$second->getImageGeometry();

			$im1 = new Imagick($image);
			$im1->setBackgroundColor(new ImagickPixel('transparent'));
			$im1->readImage($this->background);
			//Get Image resolution for artwork
			$res = $im1->getImageResolution();
			$x_ratio = $res['x'] / $im1->getImageWidth();
			$y_ratio = $res['y'] / $im1->getImageHeight();
			//Remove artwork image
			$im1->removeImage();
			//Set resolution for artwork with new width & height
			$im1->setResolution(round($geo['width'] * $x_ratio), round($geo['height'] * $y_ratio));

			//Read image
			$im1->readImage($image);

			$second->setImageColorspace($im1->getImageColorspace() );
			$second->compositeImage($im1, $im1->getImageCompose(),0,0);
			$second->writeImage($this->filepath);

			}else{
				return false;
			}


	}
	private function svg_text($line,$x,$y,$font,$fontsize,$color,$align )
	{
		$scale=$this->ratio;
		//add text line1


		$third=new Imagick($this->filepath);
		$draw = new ImagickDraw();
		$draw->setFillColor($color);
		//Font properties
		$draw->setFont($font);

		$draw->setFontSize( $fontsize );
		$textProperties = $third->queryFontMetrics( $draw, $line );
		if($align=='center'){
			$x =$x - $textProperties['textWidth']/2;
		}
		else if($align=='left')	{
			$x = $x;
		}
		else if($align=='right'){
			$x = $x-$textProperties['textWidth'];
		}
		$x= round($scale * $x);
		$y=round ($scale * $y);

		//Create text
		$third->annotateImage($draw, $x,$y,0, $line);
		//Give image a format
		$third->setImageFormat('png');
		$third->writeImage($this->filepath);
	}
}