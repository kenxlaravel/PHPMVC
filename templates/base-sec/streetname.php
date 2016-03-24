<?php

	$options_control = $ObjStreetsign->ControlCustomOptions();
	$secondstep_c = 0;
	$product_no = $Product->getProductNumber();

	$productNote = $Product->getNote();
	$productsize = $Product->getCustomProductSizeFilterList();

	$color = $ObjStreetsign->CustomProductColor();

	$mountingoption= $ObjStreetsign->getStreetnameUpchargeDefaults($Product->getStreetsignToolId());
	$defaultStreetsignToolMountingHoleArrangementId = (int) $ObjStreetsign->getStreetsignDefaultMountingHoleArrangementId();

	$editdata = json_decode($_REQUEST['editdata'], true);



	if (isset($editdata['textupper']) && $editdata['textupper']=="Y") {
		$textupperclass = " textupper";
	} else {
		$textupperclass = " ";
	}
	if($options_control['font_active']==TRUE)
		$secondstep_c ++;
	if($options_control['background_active']==TRUE)
		$secondstep_c ++;

	if($options_control['logo_active'] == TRUE)
		$logo_class = "upload";
	else
		$logo_class="noupload";

	if(isset($editdata['line_1']) && $editdata['line_1'] != "" && isset($editdata['line_2']) && $editdata['line_2'] != "") {
		$lineclass=" twoline";
		$lineclassv="twoline";
	} else {
		$lineclass=" oneline";
		$lineclassv="oneline";
	}

	if($options_control['prefix_active'] == 1)
		$prefix = $ObjStreetsign->CustomProductPrefix($Product->getStreetsignToolId());
	if($options_control['leftarrow_active'] == 1) {
		$leftarrow = $ObjStreetsign->CustomProductLeftArrow($Product->getStreetsignToolId());
	} else {
		$leftarrow = array();
	}
	if($options_control['suffix_active'] == 1)
		$suffix = $ObjStreetsign->CustomProductSuffix($Product->getStreetsignToolId());
	if($options_control['rightarrow_active'] == 1) {
		$rightarrow = $ObjStreetsign->CustomProductRightArrow($ObjProduct->getStreetsignToolId());
	} else {
		$rightarrow = array();
	}
	if($options_control['position_active'] == 1) {
		$positionsign = $ObjStreetsign->CustomProductPositionSign($Product->getStreetsignToolId());
		if (isset($editdata['position']))
			$position_0 = " ".substr($editdata['position'],0,1);
		else
			$position_0 = " ".substr($positionsign[0],0,1);
	}


	if ($options_control['background_active'] == 1) {
		$background = $ObjStreetsign->CustomProductBackground($Product->getStreetsignToolId());

		if( isset($editdata['sign_background']) ){
			$background_0 = " ".$editdata['sign_background'];
		} else {
			reset($background);
			$background_0 = " ".key($background);
		}
	}

	if ($options_control['font_active'] == 1) {
		$font = $ObjStreetsign->CustomProductFont($Product->getStreetsignToolId());
		if(isset($editdata['sign_font']))
			$font_0 = " ".str_replace(" ","_",$editdata['sign_font']);
		else
			$font_0 = " ".str_replace(" ","_",$font[0]);
	} else {
		$font_0=" Highway";
	}


	if(isset($editdata['sign_color']))
		$color_0_sepa = explode('/',$editdata['sign_color']);
	else
		$color_0_sepa = explode('/',$color[0]);



	if(isset($_REQUEST['currentsize']))
		$size_strip=explode("'",$_REQUEST['currentsize']);
	else
		$size_strip=str_replace("'","",$productsize[0]['size']);

	$color_0 = $color_0_sepa[0];

	$product_material_by_size = $ObjStreetsign->GetCustomMaterialList($product_no,$size_strip);

	$size_0 = str_replace(" ","",$size_strip);
	$size_0 = 's'.$size_0;
	$size_0 = str_replace('×', 'x', $size_0);
	$new_sizeclass_sepa = explode('x',$size_0);
	$new_sizeclass = $new_sizeclass_sepa[0]." x".$new_sizeclass_sepa[1];
	$prefix_0 = "";
	$suffix_0 = "";

	if ($prefix[0] == 'NONE' && !isset($editdata['prefix']) && $options_control['street_num_active'] != 1 || isset($editdata['prefix']) && $editdata['prefix'] == "NONE" && $options_control['street_num_active']!=TRUE)
		$prefix_0=" None-p";
	if ($suffix[0] == 'NONE' && !isset($editdata['suffix']) || isset($editdata['suffix']) && $editdata['suffix']=="NONE")
		$suffix_0 = " None-s";
	if ($options_control['street_num_active'] == 1 && (($prefix[0] == 'NONE' || $prefix[0] == '') && !isset($editdata['prefix']) || isset($editdata['prefix']) && ($editdata['prefix'] == "NONE" || $editdata['prefix'] == ''))) {
		$street_num_class = " None-p street-num";
	} else if($options_control['street_num_active']==TRUE) {
		$street_num_class = " street-num";
	} else {
		$street_num_class = " ";
	}

	$leftarrow_0 = "";
	$rightarrow_0 = "";
	$prefixarrow_label = "";
	$suffixarrow_label = "";

	if ($options_control['leftarrow_active'] == TRUE && $options_control['prefix_active'] == FALSE) {
		if ($leftarrow[0] == 'NONE' && !isset($editdata['prefix']) || isset($editdata['prefix']) && $editdata['prefix'] == "NONE") {
			$leftarrow_0 = ' None-p';
		} else {
			if(isset($editdata['prefix']))
				$arrow_sepa_l=explode(" ",$editdata['prefix']);
			else
				$arrow_sepa_l=explode(" ",$leftarrow[0]);

			$leftarrow_0=" ".$arrow_sepa_l[1]."-".substr($arrow_sepa_l[0],0,1)."-p";
		}

		$prefixarrow_label="Left Arrow";
	} else if ($options_control['leftarrow_active']==FALSE && $options_control['prefix_active']==TRUE)
		$prefixarrow_label="Prefix";
	else if ($options_control['leftarrow_active']==TRUE && $options_control['prefix_active']==TRUE)
		$prefixarrow_label="Prefix/Left Arrow";
	if ($options_control['rightarrow_active']==TRUE && $options_control['suffix_active']==FALSE) {
		if ($rightarrow[0]=='NONE' && !isset($editdata['suffix']) || isset($editdata['suffix']) && $editdata['suffix']=="NONE") {
			$rightarrow_0=' None-s';
		} else {
			if(isset($editada['suffix']))
				$arrow_sepa_r=explode(' ',$editdata['suffix']);
			else
				$arrow_sepa_r=explode(' ' ,$rightarrow[0]);
			$rightarrow_0=" ".$arrow_sepa_r[1]."-".substr($arrow_sepa_r[0],0,1)."-s";
		}

		$suffixarrow_label="Right Arrow";
	} else if ($options_control['rightarrow_active'] == FALSE && $options_control['suffix_active']==TRUE)
		$suffixarrow_label="Suffix";
	else if ($options_control['rightarrow_active'] == TRUE && $options_control['suffix_active']==TRUE)
		$suffixarrow_label="Suffix/Right Arrow";

	$file_name = "";
	$file_id = "";

	//above for initial value only
	if (isset($_REQUEST['btupload']) && $_REQUEST['btupload'] == "Upload File") {
		$file_array = $ObjStreetsign->uploadcustomfile($_FILES['importcustomfile']);


		$printmsg = $file_array[0];
		$file_id = $file_array[1];
		$file_name = $file_array[2];
		$file_size = $file_array[3];

		$editdata['uploadfileid'] = $file_id;


	} else if (isset($_REQUEST['delete_file']) && $_REQUEST['delete_file'] == "Delete File") {
		$deleted = $ObjStreetsign->deleteCustomFile($_REQUEST['uploadfileid'], $_REQUEST['uploadfilename'], $_REQUEST['uploadfilesize']);
		if ($deleted) { $deletemessage = "Your file has been deleted"; } else { $deletemessage = "This file could not be deleted"; }
	}

	$customproduct=" ".strtolower(str_replace(" ","_",$_REQUEST['subcategory']));

?>

<div id="product-configuration" class="hidden-data">
	<input name="logoclass" type="hidden" id="logoclass"  value="<? echo htmlspecialchars($logo_class); ?>">
	<input name="stid" type="hidden" value="<?php echo htmlspecialchars($stid, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="best_seller" type="hidden" value="<?php echo htmlspecialchars($best_seller, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="product_name" type="hidden" value="<?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="subtitle" type="hidden" value="<?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="translation_family_id" type="hidden" value="<?php echo htmlspecialchars($translation_family_id, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="landing_id" type="hidden" value="<?php echo htmlspecialchars($landing_id, ENT_QUOTES, 'UTF-8'); ?>">
	<input name="subcategory_id" type="hidden" value="<?php echo htmlspecialchars($subcategory_id, ENT_QUOTES, 'UTF-8'); ?>">
<?
	if ($options_control['street_num_active']==TRUE) {
?>
	<input name="numclass" type="hidden" id="numclass" value="street-num" />
<?
	}
?>
	<input name="lineclass" type="hidden" id="lineclass" value="<? if(isset($editdata['line_1'])) print $lineclassv;else print 'oneline'; ?>" />
	<input name="loadpath" type="hidden" id="loadpath"  value="<? print URL_PREFIX_HTTP."/templates/base-sec/streetname_materials.php"; ?>" />
</div>

<section class="clearfix span-24">
	<div id="custom-tool-options" class="product-customization span-7">
		<p class="h3" id="start-here">Start Here To Design Your Sign</p>
		<div id="customer-copy" class="option_container">
			<p class="h3 section-heading">
				<span class="step_counter">1</span>
				Choose A Sign Style
			</p>
<?
			if (count($productsize) > 1) {
?>
				<p class="bold prepend-top no-margin-bottom">Select A Size</p>
				<select id="sign_size" name="sign_size">
<?
					for ($i = 0; $i < count($productsize); $i++) {

						if($i == 0) {
							if (isset($_POST['currentsize'])) {
								$defaultsize = $_POST['currentsize'];
							} else {
								$defaultsize= $productsize[$i]['size'];
							}
						}

						$search_special_characters = array('×', '″');
						$replace = array("x", "");
						$size_value = str_replace($search_special_characters,$replace,$productsize[$i]['size']);
						$size_value = str_replace(" ","",$size_value);
						$size_value = 's' . $size_value;
						$size_value_new_sepa=explode('x',$size_value);
						$size_value_new=$size_value_new_sepa[0]." x".$size_value_new_sepa[1];

?>
						<option value="<? print $size_value_new.'|'.$productsize[$i]['max_chars_upper'].'|'.$productsize[$i]['absolute_maximum'];?>" <? if($productsize[$i]['size'] == $size_strip[0]) print ' selected="selected"';?>><? print $productsize[$i]['size']." Max Char (".$productsize[$i]['max_chars_upper'].') with ';if($size_value_new_sepa[0]==6) print '4';else if($size_value_new_sepa[0]==4) print '2';else print '6';print '"'.' text'; ?></option>
<?
					}
?>
				</select>
<?
			}

			if (count($color) > 1) {
?>
			<div class=" prepend-top">
				<hr class="append-bottom" />
				<label for="sign_color">Color Style</label>
				<select id="sign_color" name="sign_color" class="customoption">
<?
				for ($i = 0; $i < count($color); $i++) {

			   		if($i == 0) {
						if(isset($editdata['sign_color'])) {
							$defaultcolor_sepa=explode('/',$editdata['sign_color']);
							$defaultcolor=$defaultcolor_sepa[0];
						} else {
							$defaultcolor=$color[$i];
						}
					}
?>
					<option value="<? print $color[$i]; ?>"<? if(isset($editdata['sign_color']) && $color[$i]==$editdata['sign_color']) print ' selected="selected"'; ?>><? print $color[$i];?></option>
<?
				}
?>
				</select>
			</div>
<?
			}
?>

<?
		if ($options_control['background_active']==TRUE) {
			if (count($background) > 1){
?>
			<div>
				<label for="sign_background">Background</label>
				<select id="sign_background" name="sign_background" class="customoption">
<?
					$i = 0;
					foreach($background AS $name => $row) {

						// Check whether or not customer is required to upload a file.
						$requiresFileUpload = ( $row['upload_required'] ? TRUE : FALSE );

						if (!($options_control['logo_active'] != 1 && $name == 'Logo')) {

							if ($i == 0) {
								if ( isset($editdata['sign_background']) ) {
									$defaultbackground = $editdata['sign-backgrounds'];
								} else {
									$defaultbackground = $name;
								}
							}
							?>
							<option value="<? print $name; ?>"<? if(isset($editdata['sign_background']) && $editdata['sign_background'] == $name) print ' selected="selected"';?> data-streetsign-upload-required="<?=$requiresFileUpload?>"><? print $name;?></option>
						<?
						}

					$i++;
					}
?>
				</select>
			</div>
<?
			} elseif (count($background) === 1) {
				reset($background);
				$defaultbackground = key($background);
?>
				<input type="hidden" id="sign_background" name="sign_background" class="customoption" value="<?php echo htmlspecialchars($defaultbackground, ENT_QUOTES, 'UTF-8'); ?>">
<?

			}
		}
?>
		</div>
		<div id="enter-copy-sections" class=" clearfix">
			<div id="line-by-line-editing" class="clearfix prepend-top">
				<p class="h3 section-heading prepend-top">
					<span class="step_counter">2</span>
					Enter Text Options
				</p>
				<div id="label-container" class="prepend-top">
				<p id="overmaximum" for="line_1" class="error append-bottom" style="display:none"><span id="lineovermax"></span> over the maximum allowed characters (<span id="maximumallow"></span>) for this sign size, please delete characters or change to a wider sign size.</p>
					<div class="labels span-5 last">
						<p class="bold">Street Name</p>
					</div>
					<div class="labels span-2 last">
						<p class="bold">Max Chars</p>
					</div>

					<div id="line-1-container" class="line-containers custom-selection clear" name="line-1">

						<input type="text" id="line_1" name="line_1" class="text-line span-5" maxlength="<? if(isset($editdata['line_1'])) print $product_material_by_size[0]['absolute_maximum'];else print $productsize[0]['absolute_maximum']; ?>" value="<? if(isset($editdata['line_1'])) print htmlspecialchars($editdata['line_1'],ENT_QUOTES,'UTF-8'); ?>" />
						<input type="hidden" id="line_1_maxrecom" value="<? if(isset($_REQUEST['currentsize'])) print $product_material_by_size[0]['max_chars_upper'];else print $productsize[0]['max_chars_upper']; ?>">
						<input type="hidden" id="line_1_maxabs" value="<? if(isset($_REQUEST['currentsize'])) print $product_material_by_size[0]['absolute_maximum'];else print $productsize[0]['absolute_maximum']; ?>">
						<span class="current-characters<? if(isset($_REQUEST['currentsize'])&&strlen($editdata['line_1'])>$product_material_by_size[0]['max_chars_upper']) print ' over-max';?>">
						<? if(isset($editdata['line_1'])) print strlen($editdata['line_1']);else print '0';?>
						</span> <span class="max-characters">
						<? if(isset($_REQUEST['currentsize'])&&strlen($editdata['line_1'])>$product_material_by_size[0]['max_chars_upper']) print $product_material_by_size[0]['absolute_maximum'];else if(isset($_REQUEST['currentsize'])) print $product_material_by_size[0]['max_chars_upper'];else print $productsize[0]['max_chars_upper']; ?>
						</span>
					</div>
				</div>
<?
				if ($options_control['street_num_active']!= TRUE) {
					if($options_control['secondline_active']== TRUE) {
?>
				<div class="add-delete"><a href="#line-1" id="addline">[+] Add Line</a></div>
				<div id="line-2-container" class="line-containers  custom-selection">
					<input type="text" id="line_2" name="line_2" class="text-line span-5" maxlength="<? if(isset($editdata['line_1'])) print $product_material_by_size[0]['absolute_maximum'];else print $productsize[0][absolute_maximum]; ?>" value="<? if(isset($editdata['line_2'])) print htmlspecialchars($editdata['line_2'],ENT_QUOTES,'UTF-8'); ?>" />
					<span class="current-characters"<? if(isset($_REQUEST['currentsize'])&&strlen($editdata['line_2'])>$product_material_by_size[0]['max_chars_upper']) print ' over-max';?>>
					<? if(isset($editdata['line_2'])) print strlen($editdata['line_2']);else print '0';?>
					</span><span class="max-characters">
					<? if(isset($_REQUEST['currentsize'])&&strlen($editdata['line_2'])>$product_material_by_size[0]['max_chars_upper']) print $product_material_by_size[0]['absolute_maximum'];else if(isset($_REQUEST['currentsize'])) print $product_material_by_size[0]['max_chars_upper'];else print $productsize[0]['max_chars_upper']; ?>
					</span>
					<div class="add-delete"> <a href="#line-1" id="deleteline">[-] Delete Line</a></div>
				</div>
<?
					}
				} else {
?>
				<div class="labels clearfix span-7 last">
					<p class="bold">Street Number (Max 5 characters)</p>
				</div>
				<div id="line-2-container" class="line-containers" style="display:block" >
					<div id="line2outer">
						<input type="hidden" id="line_2" name="line_2" class="text-line span-5" maxlength="5" value=""/>
						<input type="text" id="sidetext" name="sidetext" class="text-line span-5" maxlength="5" value="<? if(isset($editdata['sidetext'])) print htmlspecialchars($editdata['sidetext'],ENT_QUOTES,'UTF-8');?>"/>
						<span class="current-characters">
						<? if(isset($_REQUEST['currentsidetext'])) print strlen($_REQUEST['currentsidetext']);else print '0';?>
						</span> <span class="max-characters"><? print "5" ?></span> </div>
				</div>
<?
				}
?>
				<div id="over-max" <? if(isset($editdata['line_2']) && strlen($editdata['line_2']) > $product_material_by_size[0]['max_chars_upper'] || isset($editdata['line_1']) && strlen($editdata['line_1']) > $product_material_by_size[0]['max_chars_upper']) print 'style="display:block"';else print 'style="display:none"';?>>
					<p class="h6">You have exceeded Brimar's recommended maximum of <span id="recommended-warning"></span> characters for legibility. You may continue typing to the absolute maximum.</p>
				</div>
			</div>

			<div class=" append-bottom">
				<input type="checkbox" value="<? if(isset($editdata['textupper'])) print htmlspecialchars($editdata['textupper'],ENT_QUOTES,'UTF-8');else print 'N'; ?>" name="text_upper" id="text_upper" <? if (isset($editdata['textupper']) && $editdata['textupper']=="Y") print 'checked="checked"';?>>
				<label for="text_upper">All text upper case</label>
			</div>
		</div>
<?

		if ($options_control['font_active'] == TRUE) { ?>
		<div>
			<p class="bold no-margin-bottom">Font</p>
<?

			if (count($font) > 1) {
?>
			<select id="sign_font" name="sign_font" class="customoption">
<?
				for($i=0; $i<count($font); $i++) {
						if ($i == 0) {
							if(isset($editdata['sign_font']))
								$defaultfont=$editdata['sign_font'];
							else
								$defaultfont=$font[$i];
						}
				?>

				<option value="<? print $font[$i]; ?>"<? if(isset($editdata['sign_font']) && $editdata['font'] == $font[$i]) print ' selected="selected"';?>><? print $font[$i]; ?></option>
<?
				}
?>
				</select>

<?
			}
?>
		</div>
<?
		}
?>

		<div id="sign_font_container" class=" prepend-top">
			<p class="h3 section-heading prepend-top append-bottom"><span class="step_counter">3</span> Choose Other Sign Options</p>
<?
			if ($options_control['prefix_active'] == TRUE || $options_control['leftarrow_active'] == TRUE) {
?>
			<div id="prefix_selector_container" class="grid_1 alpha text-option">
				<label for="prefix_selector"><? print $prefixarrow_label;?></label>
<?
				if(count($prefix) || count($leftarrow) > 1) {
?>
				<select id="prefix_selector" name="prefix">
<?
					if (count($prefix)>0) {
						for($i=0;$i<count($prefix);$i++) {
							if($i==0) {
								if(isset($editdata['prefix']))
									$defaultprefix=$editdata['prefix'];
								else
									$defaultprefix=$prefix[$i];
							}

							if(!(count($leftarrow) > 0 && $prefix[$i] == 'NONE')) {
?>
							<option value="<? print  $prefix[$i];?>"<? if(isset($editdata['prefix'])&&$editdata['prefix']==$prefix[$i]) print ' selected="selected"';?>><? print  $prefix[$i]; ?></option>
					<?
									}
								}
							}
							if(count($leftarrow) > 0) {
								for($i=0; $i<count($leftarrow); $i++) {
									if($i == 0&&count($prefix) == 0) {
										if(isset($editdata['prefix']))
											$defaultprefix = $editdata['prefix'];
										else
											$defaultprefix = $leftarrow[$i];
									} if (!(count($prefix) > 0 && $leftarrow[$i] == 'NONE')) {

?>
										<option value="<? print  $leftarrow[$i];?>"<? if(isset($editdata['prefix'])&&$editdata['prefix']==$prefix[$i]) print ' selected="selected"';?>><? print  $leftarrow[$i]; ?></option>
<?
									}
								} if (count($prefix) > 0) {
?>
									<option value="NONE"<? if(isset($editdata['prefix'])&&$editdata['prefix']=="NONE") print ' selected="selected"';?>>NONE</option>
<?
								}
							}
?>
				</select>
			</div>
<?
				}
			}

			if ($options_control['suffix_active'] == TRUE || $options_control ['rightarrow_active'] == TRUE) {
?>
			<div id="suffix_selector_container" class="grid_1 text-option">
				<label for="suffix_selector"><? print $suffixarrow_label;?></label>
<?
				if (count($suffix) > 1 || count(isset($rightarrow)?$rightarrow:NULL) > 1) {
?>
				<select id="suffix_selector" name="suffix">
<?
					if(count($suffix) || count($rightarrow) > 0) {
						for ($i = 0;$i < count($suffix); $i++) {
							if ($i == 0) {
								if (isset($editdata['suffix'])) {
									$defaultsuffix=$editdata['suffix'];
								} else {
									$defaultsuffix=$suffix[$i];
								}
							}

							if(!(count($rightarrow)>0&&$suffix[$i]=='NONE')) {
						?>
					<option value="<? print $suffix[$i];?>"<? if(isset($editdata['suffix'])&&$editdata['suffix']==$suffix[$i]) print ' selected="selected"';?>><? print $suffix[$i];?></option>
					<?
							}
						}
					}

					if (count($rightarrow) > 0) {
						for($i=0;$i<count($rightarrow);$i++) {
								if ($i == 0 && count($suffix) == 0) {
									if (isset($editdata['suffix']))
										$defaultsuffix = $editdata['suffix'];
									else
										$defaultsuffix = $rightarrow[$i];
								}

								if (!(count($suffix)>0 && $rightarrow[$i]=='NONE')) {
?>
								<option value="<? print $rightarrow[$i];?>"<? if(isset($editdata['suffix']) && $editdata['suffix'] == $suffix[$i]) print ' selected="selected"';?>><? print $rightarrow[$i]; ?></option>
<?
								}
						}

						if (count($suffix) > 0) {
?>
						<option value="NONE"<? if(isset($editdata['suffix']) && $editdata['suffix'] == "NONE") print ' selected="selected"';?>>NONE</option>
<?
						}
					}
?>
				</select>
			</div>
<?
				}
			} if ($options_control['position_active'] == TRUE && ($options_control['prefix_active'] == TRUE && count($prefix) > 0 || $options_control['suffix_active'] == TRUE && count($suffix) > 0)) {
?>
			<div id="position_selector_container" class="grid_2 omega text-option">
				<label for="position_selector">Prefix/Suffix Position</label>
<?
				if (count($positionsign) > 0) {
?>
				<select id="position_selector" name="position">
<?
					for ($i = 0; $i < count($positionsign); $i++) {
						if ($i == 0) {
							if (isset($editdata['position']))
								$defaultposition = $editdata['position'];
							else
								$defaultposition = $positionsign[$i];
						}
?>
					<option value="<? print $positionsign[$i];?>"<? if (isset($editdata['position']) && $editdata['position'] == $positionsign[$i]) print ' selected="selected"';?>><? print $positionsign[$i];?></option>
<?
					}
?>
				</select>
			</div>
<?
			}
		}
?>
		</div>
		<div class="  prepend-top">
			<hr class="append-bottom" />
			<p class="relative-position" id="mounting-description"><span class="left-side">Mounting Option</span><i class="sprite sprite-question"></i> <span class="question-popup small-text">Mounting holes are not required for typical street sign post installation.</span></p>
<?

			if (count($mountingoption) > 0) {
?>
			<select id="mounting_option" name="mounting_option">
<?
			$i = 0;
			foreach ($mountingoption as $mount) {
				if($i == 0) {
					if (isset($_REQUEST['currentmountingoption']))
						$defaultmounting = (int) $_REQUEST['currentmountingoption'];
					else
						$defaultmounting = (int) $mount['id'];
				}

?>
				<option value="<? print (int)$mount['id']; ?>"<? if(isset($_REQUEST['currentmountingoption'])&&$_REQUEST['currentmountingoption']==$mount['id']) print ' selected="selected"';?>><?php if ((int) $mount['id'] == $defaultStreetsignToolMountingHoleArrangementId ) { print $mount['note']; } else { print $mount['name']. ' -'.$mount['note']; }?></option>
<?
				$i++;
			}
?>
			</select>
<?
			}



			# Build the editdata array
			$default_editdata = array(
				'line_1' => (string) $editdata['line_1'],
				'line_2' => (string) $editdata['line_2'],
				'position' => (string) isset($defaultposition)? $defaultposition:NULL,
				'prefix' => (string) isset($defaultprefix)? $defaultprefix : NULL,
				'sign_background' => (string) $defaultbackground,
				'sign_color' => (string) $defaultcolor,
				'sign_font' => (string) (isset($defaultfont)?$defaultfont : ""),
				'special_comment' => (string) $editdata['special_comment'],
				'suffix' => (string) isset($defaultsuffix)?$defaultsuffix:NULL,
				'textupper' => (string) (isset($editdata['textupper']) ? $editdata['textupper'] : 'N'),
				'uploadfileid' => (string) $file_id,
				'sidetext' => (string) isset($_REQUEST['currentsidetext'])? $_REQUEST['currentsidetext'] : NULL
			);

?>
</div>
<?

			if ($options_control['logo_active'] == TRUE) {
?>
		<div id="uploadfile_container" class="option_container prepend-top">
			<p class="h3 section-heading prepend-top append-bottom"><span class="step_counter">4</span> Upload A Logo/Clipart</p>

			<div class="clearfix append-bottom" id="upload-div">
				<form accept-charset="utf-8" id="uploadcustom" name="uploadcustom" class="uploadform" action="<? echo $page->getUrl().'?s='.ProductStateParameter::encode($productStateParameters); ?>" method="post" enctype="multipart/form-data">

					<div class="append-bottom" id="uploadshow"<?
																 //If the sign has a logo available, is a novelty sign, and the logo is NOT selected, OR a file has been uploaded already. display none
																 if (($logo_class == 'upload' && $options_control['font_active'] == TRUE && $editdata['sign_background'] != 'Logo') || isset($_REQUEST['btupload'])) { echo " style='display: none;'"; }
															  ?>>
						<input id="importcustomfile" name="importcustomfile" type="file" />
						<input type="submit" value="Upload File" name="btupload" class="button green small-text submit span-3">
					</div>

<?
					if ($deletemessage != "") {
?>
						<div id="uploadnessage" class="<?php if (strpos($deletemessage, 'has been deleted') !== false) print 'success'; else print 'error'; ?> span-6"><? print $deletemessage; ?></div>
<?
					}
?>



					<div id="buttonmessage">
						<?
							if (!isset($_REQUEST['btupload']) && $options_control['font_active'] == TRUE && $logo_class == 'upload' && $editdata['sign_background'] != 'Logo' ) {
								print 'To upload custom artwork, please select Logo as your background option above.';
							}
						?>
					</div>
					<? if(isset($_REQUEST['btupload'])) {?>
					<div id="uploadnessage" class="<? if(strpos($printmsg,'has been uploaded') !== false) print 'success'; else print 'error';?> span-6">
						<? print $printmsg;?>
					</div>
					<? }
					$product_namebr = str_replace(" ","",$_REQUEST['productno']);

					$requiresFileUpload = ( $background[$defaultbackground]['upload_required'] ? TRUE : FALSE );

					?>
					<input type="hidden" id="streetsign-upload-required" value="<?=$requiresFileUpload?>">
					<input name="uploadfilename" id="uploadfilename" value="<? print $file_name; ?>" type="hidden"/>
					<input name="uploadfileid" id="uploadfileid" value="<? print $file_id; ?>" type="hidden"/>
					<input name="uploadfilesize" id="uploadfilesize" value="<? print $file_size; ?>" type="hidden"/>
					<input name="delete_file" id="delete_file1" type="submit" value="Delete File" <? if($file_name=="") print 'style="display:none"'; ?> />
					<input name="currentsize" id="currentsize" class="vsize" value="<?php print $defaultsize; ?>" type="hidden"/>
					<input name="currentcolor" id="currentcolor" class="vcolor" value="<? print htmlspecialchars($defaultcolor,ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input name="currentbackground" id="currentbackground" class="vbackground" value="<? print htmlspecialchars($defaultbackground,ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input name="currentline1" id="currentline1" class="vline_1" value="<? if(isset($editdata['line_1'])) print htmlspecialchars($editdata['line_1'],ENT_QUOTES,'UTF-8');?>" type="hidden"/>
					<input name="currentline2" id="currentline2" class="vline_2" value="<? if(isset($editdata['line_2'])) print htmlspecialchars($editdata['line_2'],ENT_QUOTES,'UTF-8');?>" type="hidden"/>
                    <input type="hidden" class="compress_1" name="currentcompress_1"value="<? if(isset($_REQUEST['currentcompress_1'])) print htmlspecialchars($_REQUEST['currentcompress_1'],ENT_QUOTES,'UTF-8');else print '1'; ?>" />
                    <input type="hidden" class="compress_2" name="currentcompress_2"value="<? if(isset($_REQUEST['currentcompress_2'])) print htmlspecialchars($_REQUEST['currentcompress_2'],ENT_QUOTES,'UTF-8');else print '1'; ?>" />
                    <input type="hidden" class="compress_suffix" name="currentcompress_suffix"value="<? if(isset($_REQUEST['currentcompress_suffix'])) print htmlspecialchars($_REQUEST['currentcompress_suffix'],ENT_QUOTES,'UTF-8');else print '1'; ?>" />
                    <? if($options_control[street_num_active]==TRUE) {?>
                    <input type="hidden" class="compress_streetnum" name="currentcompress_streetnum"value="<? if(isset($_REQUEST['currentcompress_streetnum'])) print htmlspecialchars($_REQUEST['currentcompress_streetnum'],ENT_QUOTES,'UTF-8');else print '1'; ?>" />
                    <? }?>
					<input name="currentsidetext" id="currentsidetext" class="vsidetext" value="<? if(isset($editdata['sidetext'])) print htmlspecialchars($editdata['sidetext'],ENT_QUOTES,'UTF-8');?>" type="hidden"/>
					<textarea name="currentline1preview" id="currentline1preview" class="vline_1preview" style="display:none"><? if(isset($_REQUEST['currentline1preview'])) print htmlspecialchars($_REQUEST['currentline1preview'],ENT_QUOTES,'UTF-8');?>
</textarea>
					<textarea name="currentline2preview" id="currentline2preview" class="vline_2preview" style="display:none"><? if(isset($_REQUEST['currentline2preview'])) print htmlspecialchars($_REQUEST['currentline2preview'],ENT_QUOTES,'UTF-8');?>
</textarea>
					<input name="currenttextsize1" id="currenttextsize1" class="vfontsize" value="<? print htmlspecialchars($defaultfontsize,ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input name="currenttextsize2" id="currenttextsize2" class="vfontsize2" value="<? print htmlspecialchars($defaultfontsize2,ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input name="currentfont" id="currentfont" class="vfont" value="<? print htmlspecialchars($defaultfont,ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input type="hidden" name="currentposition" class="vposition" value="<? print htmlspecialchars($defaultposition,ENT_QUOTES,'UTF-8');?>" />
					<input type="hidden" name="currentprefix" class="vprefix" value="<? print htmlspecialchars($defaultprefix,ENT_QUOTES,'UTF-8');?>" />
					<input type="hidden" name="currentsuffix" class="vsuffix" value="<? print htmlspecialchars($defaultsuffix,ENT_QUOTES,'UTF-8');?>" />
					<input type="hidden" name="currenttextupper" class="vtextupper" value="<? if(isset($editdata['textupper'])) print htmlspecialchars($editdata['textupper'],ENT_QUOTES,'UTF-8'); else print 'N';?>" />
					<input name="currentmountingoption" id="currentmountingoption" class="vmounting" value="<? print htmlspecialchars((!empty($_REQUEST['mounting']) ? $_REQUEST['mounting'] : $_REQUEST['currentmountingoption']),ENT_QUOTES,'UTF-8'); ?>" type="hidden"/>
					<input name="currentspecialcomment" id="currentspecialcomment" class="vspecial_comment" value="<? if(isset($editdata['special_comment'])) { print htmlspecialchars($editdata['special_comment'], ENT_QUOTES, 'UTF-8'); }?>" type="hidden"/>
					<input name="uploadinitial" id="uploadinitial" value="<? if(isset($_REQUEST['uploadinitial']))  print 'Y';else print 'N';?>" type="hidden"/>

					<input name="editdata" type="hidden" value="<?php if (isset($editdata)) { echo htmlspecialchars(json_encode($editdata), ENT_QUOTES, 'UTF-8'); } else { echo htmlspecialchars(json_encode($default_editdata), ENT_QUOTES, 'UTF-8'); } ?>">


					<div id="deleteresult"></div>
					<p id="show-inst"><a href="#show-inst">View Instructions</a></p>
					<div id="upload-instructions">
						<p id="hide-inst"><a href="#enter-copy-sections">Hide Instructions</a></p>
						<p>To upload a file, click the left button above, and then browse for the file on your computer. Once the file is selected, click the "Upload File" button. A message will indicate if the upload was successful or not.</p>
						<ul class="with-bullets">
							<li>Acceptable file formats: JPG, JPEG, GIF, PNG, TIFF, AI, CDR, PDF, EPS and PSD. For any other format, please email the file to <a href="mailto:<? print EMAIL_SALES; ?>?subject=%20Custom%20Product%20Upload%20File"><? print EMAIL_SALES; ?></a></li>

							<li>There is a 2MB size limit for all image files.</li>
						</ul>
					</div>
				</form>
			</div>

		</div>


		<?
			}
			?>
	</div>


	<div class="product-customization span-16 last">

		<div id="product-options">
			<div id="custom-product-images" class="<? echo $logo_class." ".htmlspecialchars(str_replace("″", "", $new_sizeclass),ENT_QUOTES,'UTF-8')." ".htmlspecialchars($color_0,ENT_QUOTES,'UTF-8').htmlspecialchars($position_0,ENT_QUOTES,'UTF-8').htmlspecialchars($font_0,ENT_QUOTES,'UTF-8').$prefix_0.$suffix_0.htmlspecialchars($leftarrow_0,ENT_QUOTES,'UTF-8').htmlspecialchars($rightarrow_0,ENT_QUOTES,'UTF-8').htmlspecialchars($background_0,ENT_QUOTES,'UTF-8').$street_num_class.$lineclass.$textupperclass;?>">
				<div id="preview-image">
					<div class="thumbnail">
						<div id="prefix-wrap"><p><? if(isset($editdata['prefix'])&&$editdata['prefix']!="NONE"&&strpos($editdata['prefix'],"Arrow")===false) print htmlspecialchars($editdata['prefix'],ENT_QUOTES,'UTF-8');else if(!isset($editdata['prefix'])&&$prefix[0]!="NONE") print $prefix[0]; ?></p></div>
						<div class="product_text">
							<div class="vertically-aligned-copy1">
								<p class="line_1"><span class="copy"><? if(isset($editdata['line_1'])) print htmlspecialchars($editdata['line_1'],ENT_QUOTES,'UTF-8');?></span></p></div>
							<div class="vertically-aligned-copy2">
								<p class="line_2"><span class="copy"><? if(isset($editdata['line_2'])) print htmlspecialchars($editdata['line_2'],ENT_QUOTES,'UTF-8');?></span></p>
							</div>
						</div>
						<?
                    if($options_control['street_num_active']== 1)
                    {
                ?>
						<div id="streetnum"><p><? if(isset($editdata['sidetext'])) print htmlspecialchars($editdata['sidetext'],ENT_QUOTES,'UTF-8');?></p></div>
						<?
                    }
                ?>
						<div id="suffix-wrap"><p><? if(isset($editdata['suffix'])&&$editdata['suffix']!="NONE"&&strpos($editdata['suffix'],"Arrow")===false) print htmlspecialchars($editdata['suffix'],ENT_QUOTES,'UTF-8');else if(!isset($editdata['suffix'])&&$suffix[0]!="NONE") print $suffix[0]; ?></p></div>
					</div>
				</div>
			</div>

		</div>
		<p class="small-text"><span class="bold">Note:</span> This image is for preview purposes only.  <a href="<? echo "/images/help-elements/street-name-popups/" . $options_control['accuracy_image'] ?>" class="zoom underline no-wrap">Click here</a> to see an example of our print quality.</p>

		<? if ( $productNote || $options_control['product_links'] ) { ?>
			<hr class="prepend-top append-bottom" />
		<? } ?>

		<? // Display product note if exists
		 if ( $productNote ) { ?>
			<div class="small-text" ><?=$productNote?></div>
		<? } ?>

		<?if ($options_control['product_links']){ print '<div id="product-links"><span>More Info:</span> ' . $options_control['product_links'] . '</div>';}

        ?>

		<div id="pricing"  class="span-16 last prepend-top">
			<p class="h3 section-heading append-bottom"><span class="step_counter">
				<? if($options_control['logo_active']==TRUE) print '5';else print '4';?>
				</span> View Materials / Enter Quantity &amp; Add to Cart</p>
<?php

	//Include the material table/add to cart buttons
echo Template::generate(
	"base-sec/streetname_materials",
	array (
		'page'           => $page,
		"ObjPageProduct" => $ObjPageProduct,
        'ObjProduct' => $Product,
		'ObjStreetsign' => $ObjStreetsign,
		'product_material_by_size' => $product_material_by_size,
		'defaultposition' => $defaultposition,
		'defaultprefix' => $defaultprefix,
		'defaultbackground' => $defaultbackground,
		'defaultcolor' => $defaultcolor,
		'defaultfont' => $defaultfont,
		'defaultsuffix' => $defaultsuffix,
		'file_id' => $file_id,
		'defaultmounting' => $defaultmounting,
		'productno' => $productno,
		'pid' => $ObjPageProduct->getId(),
		'stid' => $stid,
		'best_seller' => $best_seller,
		'product_name' => $product_name,
		'subtitle' => $subtitle,
		'translation_family_id' => $translation_family_id,
		'landing_id' => $landing_id,
		'subcategory_id' => $subcategory_id,
        'productStateParameters' => $productStateParameters
	)
);

?>
			<p class="h3 section-heading pad-left-15 append-bottom">Special Instructions/Comments</p>
			<div class="grid_10 clearfix<? if($options_control[logo_active]==TRUE) print 'uploadcomment';?>">
				<textarea id="special-instructions-text" name="special_comment" wrap="logical" cols="6" rows="3"><? if(isset($editdata['special_comment'])) print htmlspecialchars($editdata['special_comment'],ENT_QUOTES,'UTF-8');?>
</textarea>
			</div>
			<? $instruction=$ObjStreetsign->GetInstruction($product_no);
	if($instruction)
	{
 ?>
			<div class="prepend-top">
				<div class="instruction"><? print $instruction; ?></div>
			</div>
			<? } ?>
		</div>
	</div>

</section>
<section class="span-24 clearfix prepend-top custom">
	<?php 	//Include the material table/add to cart buttons
	echo Template::generate(
		"base-sec/streetname_accessories",
		array (
			'page'           => $page,
			'productno'		=> $productno,
			"ObjPageProduct" => $ObjPageProduct,
			'ObjStreetsign' => $ObjStreetsign,
			'ObjProductAttributes' => $ObjProductAttributes,
			'ObjProductSubAttributes' => $ObjProductSubAttributes,
			'Path_Img_Small_product' => $pathImgSmallProduct,
            'productStateParameters' => $productStateParameters
		)
	); ?>
</section>

	<?php if($ObjPageProduct->getDetailsTabContent()) { ?>
		<section class='span-24 clearfix prepend-top bottom-rounded' id='sign_details_regulations'>
			<?php include $pathContentDetailTab.$ObjPageProduct->getDetailsTabContent(); ?>
		</section>
	<?php } ?>

<div id="fontlist-wrapper"></div>
