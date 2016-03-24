<?php

/*
 * Builder
 *
 */

class Builder {

	private $builderid;
	private $designid;
	private $tweakid;
	private $sessionid;
	private $customerid;
	private $config;
	private $tweakconfig;
	private $edits;
	private $uploads;
	private $mode;
	private $admin;
	private $basedir;
	private $cachedir = '/cache/builder/data';
	private $loadingimg = '/images/builder/spinner.gif';
	private $submiturl = '/add-to-cart';
	private $adminsubmiturl = '/ssctl/process/buildersubmit.php';

	public function __construct($builderid, $productname, $designid, $tweakid, $admin = false, $basedir = NULL, $buildercacheflag = false, $productStateParameters = NULL) {

		// Determine the base directory and adjust the cache directory as necessary.
		$this->basedir = (isset($basedir) ? $basedir : $_SERVER['DOCUMENT_ROOT']);
		$this->cachedir = $this->basedir . $this->cachedir;

		// Set the Builder ID and original product ID.
		$this->builderid = $builderid;
		$this->productname = $productname;
        $this->productStateParameters = $productStateParameters;

		//Set the whether we are an admin
		$this->admin = $admin;

		// Get the config data.
		$this->config = $this->getConfigData($buildercacheflag);

		// Get the edit data or tweak data, depending on which was set.
		if (isset($designid)) {

			$this->designid = $designid;
			$this->edits = $this->getEditData();

		} elseif (isset($tweakid)) {

			$this->tweakid = $tweakid;
			$this->tweakconfig = $this->getTweakData();

		}

		// If session data is available, store it for easy access.
		if (isset($_SESSION)) {

			$this->sessionid = session_id();
			$this->customerid = isset($_SESSION['CID'])? $_SESSION['CID'] : NULL;

		}

		// Get the upload data.
		$this->uploads = $this->getUploadData();

		// Determine the mode based on the data gathered.
		if (isset($this->edits) && !empty($this->edits)) {
			$this->mode = $this->admin ? 'adminedit' : 'edit';
		} elseif (isset($this->tweakconfig) && !empty($this->tweakconfig)) {
			$this->mode = 'tweak';
		} else {
			$this->mode = 'new';
		}

	}


	// Generate and return front-end HTML.
	public function getHtml($description='') {

		$html = '<div class="builder builderstyle" '; //take 'loaded' out
		$html .= 'data-builder-mode="' . htmlspecialchars($this->mode, ENT_QUOTES, 'UTF-8') . '" ';
		$html .= 'data-builder-submiturl="' . htmlspecialchars(( $this->admin ? $this->adminsubmiturl : $this->submiturl ), ENT_QUOTES, 'UTF-8') . '" ';
		$html .= 'data-builder-id="' . htmlspecialchars($this->builderid, ENT_QUOTES, 'UTF-8') . '" ';

        if ( $this->productStateParameters['sourceProduct'] ) { $html .= 'data-builder-sourceproductid="' . htmlspecialchars($this->productStateParameters['sourceProduct'], ENT_QUOTES, 'UTF-8') . '" '; }
		if ( $this->productStateParameters['sourceProductRecommendation'] ) { $html .= 'data-builder-sourceproductrecommendationid="' . htmlspecialchars($this->productStateParameters['sourceProductRecommendation'], ENT_QUOTES, 'UTF-8') . '" '; }
		if ( $this->productStateParameters['sourceAccessoryFamilyProduct'] ) { $html .= 'data-builder-sourceaccessoryfamilyproductid="' . htmlspecialchars($this->productStateParameters['sourceAccessoryFamilyProduct'], ENT_QUOTES, 'UTF-8') . '" '; }
        if ( $this->productStateParameters['sourceInstallationAccessory'] ) { $html .= 'data-builder-sourceinstallationaccessoryid="' . htmlspecialchars($this->productStateParameters['sourceInstallationAccessory'], ENT_QUOTES, 'UTF-8') . '" '; }
        if ( $this->productStateParameters['sourceLandingProduct'] ) { $html .= 'data-builder-sourcelandingproductid="' . htmlspecialchars($this->productStateParameters['sourceLandingProduct'], ENT_QUOTES, 'UTF-8') . '" '; }
        if ( $this->productStateParameters['sourceSubcategoryProduct'] ) { $html .= 'data-builder-sourcesubcategoryproductid="' . htmlspecialchars($this->productStateParameters['sourceSubcategoryProduct'], ENT_QUOTES, 'UTF-8') . '" '; }

		if (isset($this->designid)) { $html .= 'data-builder-designid="' . htmlspecialchars($this->designid, ENT_QUOTES, 'UTF-8') . '" '; }
		$html .= 'data-builder-inputdata="' . htmlspecialchars($this->getInputJson(), ENT_QUOTES, 'UTF-8') .'">';

			$html .= '<div class="primary-content">';
				$html .= '<div class="product-description"><p class="product-name">' . htmlspecialchars($this->config['info']['productname'], ENT_QUOTES, 'UTF-8') . '</p><p class="image-disclaimer">' . $description . '</p></div>';
				$html .= '<div class="builder-loading"><span>Loading…</span></div>';
				$html .= '<div class="ui">' . $this->getUiHtml() . '</div>';
			$html .= '</div>';
			$html .= '<div class="sidebar">' . $this->getSidebarHtml() . '</div>';

		$html .= '</div>';

		return $html;

	}


	private function getUiHtml() {
		$html = '';

		foreach ($this->config['ui'] as $section) {

			$html .= '<fieldset class="section">' . $this->getSectionHtml($section) . '</fieldset>';

		}

		return $html;

	}



	private function getSectionHtml($section) {

		$html = '<legend>' . $section['name'] . '</legend>';

		foreach ($section['controls'] as $control) {

			$html .= $this->getControlHtml($control);

		}

		return $html;

	}



	private function getControlHtml($control) {

		switch (strtolower($control['type'])) {

			case 'size':
				$controlhtml = $this->getSizeControlHtml($control);
				$hidden = (count($control['sizes']) < 2);
			break;

			case 'material':
				$controlhtml = $this->getMaterialControlHtml($control);
				$hidden = (count($control['materials']) < 2);
			break;

			case 'scheme':
				$controlhtml = $this->getSchemeControlHtml($control);
				$hidden = (count($control['schemes']) < 2);
			break;

			case 'layout':
				$controlhtml = $this->getLayoutControlHtml($control);
				$hidden = (count($control['layouts']) < 2);
			break;

			case 'option':
				$controlhtml = $this->getOptionControlHtml($control);
				$hidden = false;
			break;

			case 'artwork':
				$controlhtml = $this->getArtworkControlHtml($control);
				$hidden = false;
			break;

			case 'color':
				$controlhtml = $this->getColorControlHtml($control);
				$hidden = false;
			break;

			case 'textarea':
				$controlhtml = $this->getTextareaControlHtml($control);
				$hidden = false;
			break;

			case 'text':
				$controlhtml = $this->getTextControlHtml($control);
				$hidden = false;
			break;

			case 'textselect':
				$controlhtml = $this->getTextselectControlHtml($control);
				$hidden = false;
			break;

			case 'instructions':
				$controlhtml = $this->getInstructionsControlHtml($control);
				$hidden = false;
			break;

			case 'designservice':
				$controlhtml = $this->getDesignserviceControlHtml($control);
				$hidden = false;
			break;

		}

		$html = '<fieldset class="control' . ($hidden === true ? ' hidden' : '') . '">';

		if (isset($control['name']) && !empty($control['name'])) {
			$html .= '<legend><span class="control-name">' . htmlspecialchars($control['name'], ENT_QUOTES, 'UTF-8') . '</span><span class="control-value"></span></legend>';
		}

		if (isset($control['description']) && !empty($control['description'])) {
			$html .= '<p class="control-description">' . $control['description'] . '</p>';
		}

		$html .= $controlhtml;

		$html .= '</fieldset>';

		return $html;

	}



	private function getSizeControlHtml($control) {

		$controltype = 'size';
		$controltarget = 'size';
		$controlname = $this->builderid . '-size-sizecontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		foreach ($control['sizes'] as $size) {

			$optionname = $controlname . '-' . $size;
			$sizedata = $this->config['sizes'][$size];


			$html .= '<div class="control-option-wrap">';
			$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($size, ENT_QUOTES, 'UTF-8') . '" />';
			$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($sizedata['name'], ENT_QUOTES, 'UTF-8') . '</span> <span class="price-difference"></span></label>';
			$html .= '</div>';

		}

		$html .= '</fieldset>';

		return $html;

	}



	private function getMaterialControlHtml($control) {

		$controltype = 'material';
		$controltarget = 'material';
		$controlname = $this->builderid . '-material-materialcontrol';

		// Table headings to display for the different material properties.
		$materialproperties = array(
			'visibility' => "Visibility",
			'chemres' => "Resistance",
			'durability' => "Outdoor Durability",
			'svctemp' => "Service Temperature"
		);

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		// Determine whether any materials have information popups.
		$materialinfoavailable = false;
		$materialinfobasedir = '/info/materials/';
		foreach ($control['materials'] as $material) {
			$materialinfo = $this->config['materials'][$material]['materialinfo'];
			if (isset($materialinfo) && !empty($materialinfo)) {
				$materialinfoavailable = true;
				break;
			}
		}

		// Create the table and start creating the headings.
		$html .= '<table><thead><tr><th class="material-overview">Material</th>';

		// Create a heading for each comparison defined for this control.
		foreach ($control['comparisons'] as $comparison) {
			$html .= '<th class="' . htmlspecialchars($comparison, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($materialproperties[$comparison], ENT_QUOTES, 'UTF-8') . '</th>';
		}

		// If any materials have information popups, create a heading for them.
		if ($materialinfoavailable) {
			$html .= '<th class="info">Info</th>';
		}

		// Finish the headings and start the table body.
		$html .= '</tr></thead><tbody>';

		foreach ($control['materials'] as $material) {

			$optionname = $controlname . '-' . $material;
			$materialdata = $this->config['materials'][$material];

			$html .= '<tr>';

			// Primary information and input.
			$html .= '<td class="material-overview"><div class="row-cell-wrap"><div class="control-option-wrap">';
				$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($material, ENT_QUOTES, 'UTF-8') . '" />';
				$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars($materialdata['name'], ENT_QUOTES, 'UTF-8') . '</strong><span class="description">' . htmlspecialchars($materialdata['description'], ENT_QUOTES, 'UTF-8') . '</span> <span class="price-difference"></span></label>';
			$html .= '</div></div></td>';

			// Properties.
			foreach ($control['comparisons'] as $comparison) {
				$html .= '<td class="' . htmlspecialchars($comparison, ENT_QUOTES, 'UTF-8') . '"><div class="row-cell-wrap">';
					$html .= ((isset($materialdata[$comparison]) && !empty($materialdata[$comparison])) ? htmlspecialchars($materialdata[$comparison], ENT_QUOTES, 'UTF-8') : 'N/A');
				$html .= '</div></td>';
			}

			// Material info
			if ($materialinfoavailable) {
				$html .= '<td class="info"><div class="row-cell-wrap">';
				if (isset($materialdata['materialinfo']) && !empty($materialdata['materialinfo'])) {
					$html .= '<a class="material-info" href="' . htmlspecialchars($materialinfobasedir . $materialdata['materialinfo'], ENT_QUOTES, 'UTF-8') . '"><img src="/images/information-icon.png" alt="More Information" /></a>';
				}
				$html .= '</div></td>';
			}

			$html .= '</tr>';

		}

		$html .= '</tbody></table></fieldset>';

		return $html;

	}



	private function getSchemeControlHtml($control) {

		$controltype = 'scheme';
		$controltarget = 'scheme';
		$controlname = $this->builderid . '-scheme-schemecontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		foreach ($control['schemes'] as $scheme) {

			$optionname = $controlname . '-' . $scheme;
			$schemedata = $this->config['schemes'][$scheme];

			$html .= '<div class="control-option-wrap">';
				$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($scheme, ENT_QUOTES, 'UTF-8') . '" />';
				$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '">';
					$html .= '<span class="image-wrap-outer"><span class="image-wrap-inner"><img src="' . htmlspecialchars($this->loadingimg,ENT_QUOTES, 'UTF-8') . '" data-builder-img-src="" alt="' . htmlspecialchars($schemedata['name'], ENT_QUOTES, 'UTF-8') . '" /></span></span>';
					$html .= '<span class="image-caption">' . htmlspecialchars($this->limitCaption($schemedata['name'], 32), ENT_QUOTES, 'UTF-8') . ' <span class="price-difference"></span></span>';
				$html .= '</label>';
			$html .= '</div>';

		}

		$html .= '</fieldset>';

		return $html;

	}



	private function getLayoutControlHtml($control) {

		$controltype = 'layout';
		$controltarget = 'layout';
		$controlname = $this->builderid . '-layout-layoutcontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		foreach ($control['layouts'] as $layout) {

			$optionname = $controlname . '-' . $layout;

			$html .= '<div class="control-option-wrap">';
				$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($layout, ENT_QUOTES, 'UTF-8') . '" />';
				$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text"></span></label>';
			$html .= '</div>';

		}

		$html .= '</fieldset>';

		return $html;

	}



	private function getOptionControlHtml($control) {

		$controltype = 'option';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-optioncontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		foreach ($this->config['options'][$controltarget] as $optionval => $optionvaldata) {

			$optionname = $controlname . '-' . $optionval;

			$html .= '<div class="control-option-wrap">';
				$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($optionval, ENT_QUOTES, 'UTF-8') . '" />';
				$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($optionvaldata['name'], ENT_QUOTES, 'UTF-8') . '</span> <span class="price-difference"></span></label>';
			$html .= '</div>';

		}

		$html .= '</fieldset>';

		return $html;

	}



	private function getArtworkControlHtml($control) {

		$controltype = 'artwork';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

		// 'None' option.
		if ($control['allownone']) {

			$none_option = 'none';
			$none_optionname = $controlname . '-none';
			$none_caption = 'None';

			$html .= '<div class="control-option-wrap">';
				$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($none_optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($none_option, ENT_QUOTES, 'UTF-8') . '" />';
				$html .= '<label for="' . htmlspecialchars($none_optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($none_caption, ENT_QUOTES, 'UTF-8') . '</span></label>';
			$html .= '</div>';

		}

		// 'Upload' option.
		if ($control['allowcustom']) {

			$custom_caption = 'Upload custom image…';
			$email_address = 'sales@safetysign.com';
			$email_subject = 'Artwork for my ' . ((isset($this->config['info']['productname']) && !empty($this->config['info']['productname'])) ? $this->config['info']['productname'] : 'custom product');

			$html .= '<div id="' . htmlspecialchars($controlname . '-artworkcategory-custom', ENT_QUOTES, 'UTF-8') . '" class="custom-artwork-category" data-builder-artworkcategory="custom"><fieldset>';
				$html .= '<legend><span class="option-text">' . htmlspecialchars($custom_caption, ENT_QUOTES, 'UTF-8') . '</span></legend>';
				$html .= '<div class="custom-artwork-controls-wrap">';
					$html .= '<div class="fileupload">';
						$html .= '<p class="upload-instructions"><strong>Acceptable files:</strong> JPEG, GIF, and PNG images only. 2 <abbr title="megabytes">MB</abbr> size limit. For any other format, please email the file to <a href="mailto:' . htmlspecialchars($email_address, ENT_QUOTES, 'UTF-8') . '?subject=' . htmlspecialchars(rawurlencode($email_subject), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($email_address, ENT_QUOTES, 'UTF-8') . '</a>.</p>';
						$html .= '<div class="upload-button builder-button-primary"><span>Choose File</span><input type="file" multiple="multiple" accept="image/gif,image/png,image/jpg,image/jpeg" /></div>';
					$html .= '</div>';
					$html .= '<div class="recent-uploads' . ( count($this->uploads) > 0 ? '' : ' empty' ) . '"></div>';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname.'-upload-uploadplaceholder', ENT_QUOTES, 'UTF-8') . '" class="upload-placeholder" value="upload-uploadplaceholder" />';
				$html .= '</div>';
			$html .= '</fieldset></div>';

		}

		// Artwork categories.
		foreach ($control['categories'] as $category) {

			$categoryname = (isset($category['name']) && !empty($category['name'])) ? $category['name'] : 'Clipart';

			$html .= '<div id="' . htmlspecialchars($controlname . '-artworkcategory-' . $category['ref'], ENT_QUOTES, 'UTF-8') . '" class="artwork-category" data-builder-artworkcategory="' . htmlspecialchars($category['ref'], ENT_QUOTES, 'UTF-8') . '"><fieldset>';
				$html .= '<legend><span class="option-text">' . htmlspecialchars($categoryname, ENT_QUOTES, 'UTF-8') . '</span></legend>';
				$html .= '<div class="artwork-picker">';
					$html .= '<div class="artwork-pages-wrap-outer">';
						$html .= '<div class="artwork-pages-wrap-inner">';
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</fieldset></div>';

		}

		$html .= '</fieldset>';

		return $html;

	}



	private function getColorControlHtml($control) {

		$controltype = 'color';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-colorcontrol';

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '"></fieldset>';

		return $html;

	}



	private function getTextareaControlHtml($control) {

		$controltype = 'textarea';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol';

		// Font controls.
		$html = '<div class="font-controls">';

			// Font family.
			if (count($control['fonts']) > 1) {

				$fontfamilycontroldata = array(
					'type' => 'fontfamily',
					'target' => $controltarget,
					'fonts' => $control['fonts']
				);

				$html .= '<div class="fontfamily-control">' . $this->getFontfamilyControlHtml($fontfamilycontroldata) . '</div>';

			}

			// Font size.
			if ($control['sizecontrols']) {

				$fontsizecontroldata = array(
					'type' => 'fontsize',
					'target' => $controltarget
				);

				$html .= '<div class="fontsize-control">' . $this->getFontsizeControlHtml($fontsizecontroldata) . '</div>';

			}

			// Font alignment.
			if ($control['aligncontrols']) {

				$fontalignmentcontroldata = array(
					'type' => 'fontalignment',
					'target' => $controltarget
				);

				$html .= '<div class="fontalignment-control">' . $this->getFontalignmentControlHtml($fontalignmentcontroldata) . '</div>';

			}

		$html .= '</div>';

		$html .= '<div class="expanding-textarea">';
			$html .= '<pre><span></span><br /></pre>';
			$html .= '<textarea id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" spellcheck="true"></textarea>';
		$html .= '</div>';

		return $html;

	}



	private function getTextControlHtml($control) {

		$controltype = 'text';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol';

		// Font controls.
		$html = '<div class="font-controls">';

			// Font family.
			if (count($control['fonts']) > 1) {

				$fontfamilycontroldata = array(
					'type' => 'fontfamily',
					'target' => $controltarget,
					'fonts' => $control['fonts']
				);

				$html .= '<div class="fontfamily-control">' . $this->getFontfamilyControlHtml($fontfamilycontroldata) . '</div>';

			}

		$html .= '</div>';

		$html .= '<input type="text" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" value="" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '"';
		if ($control['charlimit'] > 0) { $html .= ' maxlength="' . htmlspecialchars($control['charlimit'], ENT_QUOTES, 'UTF-8') . '"'; }
		$html .= ' spellcheck="true" />';

		return $html;

	}



	private function getTextselectControlHtml($control) {

		$controltype = 'textselect';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol';

		// Font controls.
		$html = '<div class="font-controls">';

			// Font family.
			if (count($control['fonts']) > 1) {

				$fontfamilycontroldata = array(
					'type' => 'fontfamily',
					'target' => $controltarget,
					'fonts' => $control['fonts']
				);

				$html .= '<div class="fontfamily-control">' . $this->getFontfamilyControlHtml($fontfamilycontroldata) . '</div>';

			}

		$html .= '</div>';

		$html .= '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

			foreach ($control['text'] as $text) {

				$optionname = $controlname . '-' . $text;
				$colordata = $this->config['text'][$text];

				$html .= '<div class="control-option-wrap">';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '" />';
					$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($colordata['name'], ENT_QUOTES, 'UTF-8') . '</span></label>';
				$html .= '</div>';

			}

		$html .= '</fieldset>';

		return $html;

	}



	private function getFontsizeControlHtml($control) {

		$controltype = 'fontsize';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol-' . $controltype;

		$fontsizes = array(
			'auto' => 'Autosize',
			'smallest' => 'Smallest',
			'small' => 'Small',
			'medium' => 'Medium',
			'large' => 'Large',
			'largest' => "Largest"
		);

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

			foreach ($fontsizes as $fontsize => $fontsizename) {

				$optionname = $controlname . '-' . $fontsize;

				$html .= '<div class="control-option-wrap">';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($fontsize, ENT_QUOTES, 'UTF-8') . '" />';
					$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($fontsizename, ENT_QUOTES, 'UTF-8') . '</span></label>';
				$html .= '</div>';

			}

		$html .= '</fieldset>';

		return $html;

	}



	private function getFontfamilyControlHtml($control) {

		$controltype = 'fontfamily';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol-' . $controltype;

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

			foreach ($control['fonts'] as $font) {

				$optionname = $controlname . '-' . $font;

				$html .= '<div class="control-option-wrap">';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($font, ENT_QUOTES, 'UTF-8') . '" />';
					$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text"><span class="font-' . htmlspecialchars($font, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->config['fonts'][$font]['name'], ENT_QUOTES, 'UTF-8') . '</span></span></label>';
				$html .= '</div>';

			}

		$html .= '</fieldset>';

		return $html;

	}



	private function getFontalignmentControlHtml($control) {

		$controltype = 'fontalignment';
		$controltarget = $control['target'];
		$controlname = $this->builderid . '-' . $controltarget . '-elementcontrol-' . $controltype;

		$alignments = array(
			'left' => 'Left',
			'center' => 'Center',
			'right' => 'Right'
		);

		$html = '<fieldset class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . $controltarget . '">';

			foreach ($alignments as $alignment => $alignmentname) {

				$optionname = $controlname . '-' . $alignment;

				$html .= '<div class="' . htmlspecialchars($alignment, ENT_QUOTES, 'UTF-8') . '">';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($alignment, ENT_QUOTES, 'UTF-8') . '" />';
					$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($alignmentname, ENT_QUOTES, 'UTF-8') . '</label>';
				$html .= '</div>';

			}

		$html .= '</fieldset>';

		return $html;

	}



	private function getInstructionsControlHtml($control) {

		$controltype = 'instructions';
		$controltarget = 'instructions';
		$controlname = $this->builderid . '-' . $controltarget . '-instructionscontrol';

		$html = '<div class="expanding-textarea">';
			$html .= '<pre><span></span><br /></pre>';
			$html .= '<textarea id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" spellcheck="true"></textarea>';
		$html .= '</div>';

		return $html;

	}



	private function getDesignserviceControlHtml($control) {

		$controltype = 'designservice';
		$controltarget = 'designservice';
		$controlname = $this->builderid . '-' . $controltarget . '-designservicecontrol';

		$dschoices = array(
			'true' => 'Yes, improve my design for free.',
			'false' => 'No, print my design as closely as possible to what I see on this page.'
		);

		$html = '<fieldset id="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($controltype, ENT_QUOTES, 'UTF-8') . '" data-controltarget="' . htmlspecialchars($controltarget, ENT_QUOTES, 'UTF-8') . '">';

			foreach ($dschoices as $dschoice => $dschoicename) {

				$optionname = $controlname . '-' . $dschoice;

				$html .= '<div class="control-option-wrap">';
					$html .= '<input type="radio" name="' . htmlspecialchars($controlname, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($dschoice, ENT_QUOTES, 'UTF-8') . '" />';
					$html .= '<label for="' . htmlspecialchars($optionname, ENT_QUOTES, 'UTF-8') . '"><span class="option-text">' . htmlspecialchars($dschoicename, ENT_QUOTES, 'UTF-8') . '</span></label>';
				$html .= '</div>';

			}

		$html .= '</fieldset>';

		return $html;

	}



	private function getSidebarHtml() {

		$html = $this->getPreviewHtml();
		$html .= $this->getCurrentPricingHtml();
		$html .= '<div class="full-pricing" id="' . htmlspecialchars($this->builderid, ENT_QUOTES, 'UTF-8') . '-full-pricing">' . $this->getFullPricingHtml() . '</div>';

		return $html;

	}



	private function getPreviewHtml() {

		$html = '<div class="preview-sign">';



		// Elements.
		foreach ($this->config['elements'] as $elementref => $element) {

			$previewid = $this->builderid . '-' . $elementref . '-preview';

			switch ($element['type']) {
				case 'artwork': $html .= '<div id="' . htmlspecialchars($previewid, ENT_QUOTES, 'UTF-8') . '" class="element-preview artwork"><div class="element-content"><div><div><img /></div></div></div></div>'; break;
				case 'text': $html .= '<div id="' . htmlspecialchars($previewid, ENT_QUOTES, 'UTF-8') . '" class="element-preview text"><div class="element-content"><div><div></div></div></div><div class="element-content-proxy"><span></span></div></div>'; break;
			}

		}

		// Options.
		foreach ($this->config['options'] as $optionref => $option) {

			$previewid = $this->builderid . '-' . $optionref . '-optionpreview';

			$html .= '<div id="' . htmlspecialchars($previewid, ENT_QUOTES, 'UTF-8') . '" class="option-preview"><img /></div>';

		}

		// Background image.
		$html .= '<img class="background-preview" />';

		$html .= '</div>';

		return $html;

	}



	private function getCurrentPricingHtml() {

		$html = '<div id="" class="pricing">';

			// Product info.
			$html .= '<div class="product-info">';
				$html .= '<p class="name">' . $this->config['info']['productname'] . '</p>';
				$html .= '<p class="options"></p>';
			$html .= '</div>';

			// Pricing.
			$html .= '<div class="pricing-table"><table></table><p class="pricing-note">Prices are for your custom sign. <a class="full-pricing-link" href="#' . htmlspecialchars($this->builderid, ENT_QUOTES, 'UTF-8') . '-full-pricing">See all pricing.</a></p></div>';

			// Purchase/save controls.
			$qtycontrolid = $this->builderid . '-qty';
			if ($this->mode === 'edit' || $this->mode === 'adminedit') {
				$formclass = 'savechanges';
				$buttonclass = 'save-changes';
				$buttontext = 'Save Changes';
			} else {
				$formclass = 'builder-submit';
				$buttonclass = 'add-to-cart';
				$buttontext = 'Add to Cart';
			}
			$html .= '<form accept-charset="utf-8" class="' . htmlspecialchars($formclass, ENT_QUOTES, 'UTF-8') . '">';
				$html .= '<div class="qty-control"><label for="'. htmlspecialchars($qtycontrolid, ENT_QUOTES, 'UTF-8') . '"><abbr title="Quantity">Qty</abbr></label><input type="number" id="'. htmlspecialchars($qtycontrolid, ENT_QUOTES, 'UTF-8') . '" name="'. htmlspecialchars($qtycontrolid, ENT_QUOTES, 'UTF-8') . '" class="quantity" min="1" /></div>';
				$html .= '<div class="'. htmlspecialchars($buttonclass, ENT_QUOTES, 'UTF-8') . '"><input class="builder-button-primary" type="submit" value="'. htmlspecialchars($buttontext, ENT_QUOTES, 'UTF-8') . '" /></div>';
			$html .= '</form>';

		$html .= '</div>';

		return $html;

	}



	private function getFullPricingHtml() {

		$html = '<div class="full-pricing-inner"><div class="full-pricing-title">' . htmlspecialchars($this->config['info']['productname'], ENT_QUOTES, 'UTF-8') . '</div><div class="full-pricing-disclaimer"><strong>Note:</strong> All pricing is per-item. <span class="full-pricing-option-disclaimer"></span></div>';

		$sizecontroltext = $this->getSettingName('size');
		$materialcontroltext = $this->getSettingName('material');
		$schemecontroltext = $this->getSettingName('scheme');

		foreach ($this->config['sizes'] as $sizeref => $size) {
			$html .= '<div class="table-wrap">';
				$html .= '<table>';
					$html .= '<caption><span class="table-size">' . htmlspecialchars($sizecontroltext, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($size['name'], ENT_QUOTES, 'UTF-8') . '</span></caption>';
					$html .= '<thead>';
						$html .= '<tr>';
							$html .= '<th class="sku">SKU</th>';
							$html .= '<th class="material">' . htmlspecialchars($materialcontroltext, ENT_QUOTES, 'UTF-8') . '</th>';
							$html .= '<th class="scheme">' . htmlspecialchars($schemecontroltext, ENT_QUOTES, 'UTF-8') . '</th>';
							$tmp_product = $this->getProduct();
							foreach ($this->config['products'][$tmp_product]['pricing'] as $pricing) {
								$html .= '<th class="price">Qty ' . number_format($pricing['minqty']) . '</th>';
							}
						$html .= '</tr>';
					$html .= '</thead>';
					$html .= '<tbody>';

						$materials = $this->getAvailableMaterials($sizeref);

						foreach ($materials as $materialref) {

							$schemes = $this->getAvailableSchemes($sizeref, $materialref);

							foreach ($schemes as $schemeref) {

								$product = $this->getProduct($sizeref, $materialref, $schemeref);

								$html .= '<tr>';

									$html .= '<td class="sku">' . htmlspecialchars($this->config['products'][$product]['sku'], ENT_QUOTES, 'UTF-8') . '</td>';
									$html .= '<td class="material">' . htmlspecialchars($this->config['materials'][$materialref]['name'], ENT_QUOTES, 'UTF-8') . '</td>';
									$html .= '<td class="scheme">' . htmlspecialchars($this->config['schemes'][$schemeref]['name'], ENT_QUOTES, 'UTF-8') . '</td>';

									foreach ($this->config['products'][$product]['pricing'] as $pricing) {
										$html .= '<td class="price">$' . number_format($pricing['price'], 2) . '</td>';
									}

								$html .= '</tr>';

							}

						}

					$html .= '</tbody>';
				$html .= '</table>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;

	}



	// Generate and return input JSON.
	private function getInputJson() {

		// Prepare the input data array.
		$input = array();

		// Add sections to the input data array depending on what's available.
		if (isset($this->config) && !empty($this->config)) { $input['configdata'] = $this->config; }
		if (isset($this->tweakconfig) && !empty($this->tweakconfig)) { $input['tweakdata'] = $this->tweakconfig; }
		if (isset($this->edits) && !empty($this->edits)) { $input['editdata'] = $this->edits; }
		if (isset($this->uploads) && !empty($this->uploads)) { $input['uploaddata'] = $this->uploads; }

		// Encode the input data as JSON and return it.
		return json_encode($input);

	}



	// Helper functions for HTML generation. Note that mirrored versions of these functions exist in JavaScript. Don't change one without changing the other to match.
	private function limitCaption($text='', $limit=32) {

		// Ensure that the text is a string.
		$limited = (string) $text;

		// Truncate and add an ellipse (unless the limit was zero, in which case an empty string should be returned).
		if ($limit == 0) {
			$limited = '';
		} elseif (!empty($text) && strlen($text) > $limit) {
			$limited = substr($text, 0, ($limit-1)) . '…';
		}

		return $limited;

	}



	private function getSettingName($type = NULL, $target = NULL) {

		$controldata = $this->getControlData($type, $target);

		$settingname = $controldata['name'];

		if (!isset($settingname) || empty($settingname)) {

			switch ($type) {

				case 'size': $settingname = 'Size'; break;
				case 'material': $settingname = 'Material'; break;
				case 'scheme': $settingname = 'Scheme'; break;
				case 'layout': $settingname = 'Layout'; break;
				case 'instructions': $settingname = 'Instructions'; break;
				case 'designservice': $settingname = 'Design Service'; break;
				default: $settingname = $type; break;

			}

		}

		return $settingname;

	}



	private function getControlData($type, $target = NULL) {

		$generictypes = array('size','layout','scheme','material','instructions','designservice');
		$elementtypes = array('artwork','text','textarea','textselect');

		foreach ($this->config['ui'] as $section) {

			foreach ($section['controls'] as $control) {

				$typematch = ($control['type'] === $type);
				// $targetmatch = ($control['target'] === $type);
				$generic = in_array($type, $generictypes);
				$elementmatch = ($type === 'element' && in_array($control['type'], $elementtypes));

				if ( ($generic && $typematch) ||  ($typematch || $elementmatch) )  {

					return $control;

				}

			}

		}

	}



	private function getProduct($size = NULL, $material = NULL, $scheme = NULL) {
		$productref = array();
		// If no arguments were passed to the function, grab the default product.
		if (!isset($size) && !isset($material) && !isset($scheme)) {

			// If a default product is defined and exists in the products object within the config, use that.
			if (isset($this->config['products'][$this->config['info']['defaultproduct']])) {

				$productref = $this->config['info']['defaultproduct'];

			// Otherwise, just grab the first item from the products object within the config.
			} else {

				foreach ($this->config['products'] as $tmp_productref => $tmp_product) {
					$productref = $tmp_productref;
					break;
				}

			}

		// Otherwise, find the product with the settings that match the arguments.
		} else {

			foreach ($this->config['products'] as $tmp_productref => $tmp_product) {
				if ( ($tmp_product['size'] === $size || !isset($size)) && ($tmp_product['material'] === $material || !isset($material)) && ($tmp_product['scheme'] === $scheme || !isset($scheme)) ) {
					$productref = $tmp_productref;
					break;
				}
			}

		}

		return $productref;

	}



	private function getAvailableMaterials($size) {

		// Prepare the output array.
		$available = array();

		// Get the material control data.
		$controldata = $this->getControlData('material');

		// Check each material to see if a product exists for it, and add it to the output array if it does.
		foreach ($controldata['materials'] as $material) {
			if ($this->getProduct($size, $material)) {
				$available[] = $material;
			}
		}

		// Return the available materials.
		return $available;

	}



	private function getAvailableSchemes($size, $material) {

		// Prepare the output array.
		$available = array();

		// Get the scheme control data.
		$controldata = $this->getControlData('scheme');

		// Check each scheme to see if a product exists for it, and add it to the output array if it does.
		foreach ($controldata['schemes'] as $scheme) {
			if ($this->getProduct($size, $material, $scheme)) {
				$available[] = $scheme;
			}
		}

		// Return the available schemes.
		return $available;

	}



	// Retrieve configuration data from the database.
	private function getConfigData($buildercacheflag) {

		// Create sections cache filename array.
		$sections = array('info', 'sizes', 'materials', 'schemes', 'layouts', 'options', 'elements', 'artwork', 'fonts', 'colors', 'text', 'ui', 'products');

		// Loop through the sections array
		foreach ( $sections as $section ) {

			$sectionCachefile = $this->getDataCacheName($section);

			// If recache wasn't requested and the section file already exists, get the file.
			if ($buildercacheflag && !file_exists($sectionCachefile)) {

				$config[$section] = $this->getConfigDataCache($sectionCachefile);

			// Else we need to regenerate all data cache file
			} else {

				// Generate the data cache files.
				switch($section){

					case 'info': $config['info'] = $this->getConfigInfoData(); break;
					case 'sizes': $config['sizes'] = $this->getConfigSizeData(); break;
					case 'materials': $config['materials'] = $this->getConfigMaterialData(); break;
					case 'schemes': $config['schemes'] = $this->getConfigSchemeData(); break;
					case 'layouts': $config['layouts'] = $this->getConfigLayoutData(); break;
					case 'options': $config['options'] = $this->getConfigOptionData(); break;
					case 'elements': $config['elements'] = $this->getConfigElementData(); break;
					case 'artwork': $config['artwork'] = $this->getConfigArtworkData(); break;
					case 'fonts': $config['fonts'] = $this->getConfigFontData(); break;
					case 'colors': $config['colors'] = $this->getConfigColorData(); break;
					case 'text': $config['text'] = $this->getConfigTextData(); break;
					case 'ui': $config['ui'] = $this->getConfigUiData(); break;
					case 'products': $config['products'] = $this->getConfigProductData(); break;

				}

			}

		}

		return $config;

	}



	private function getConfigInfoData() {

		// Get basic information about this builder from the database.
		$sql = Connection::getHandle()->prepare("SELECT b.description AS productname,
										    bs.product_ref AS defaultproduct
											FROM bs_builder_skus bs
											LEFT JOIN bs_builders b ON (b.builder_ref = bs.builder_ref)
		        					WHERE bs.builder_ref = ?
		        					AND bs.active=TRUE
		        					AND b.active=TRUE LIMIT 1");
		$sql->execute(array($this->builderid));
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		// Set up info object.
		$info = array(
			'productname' => (string) $row['productname'],
			'defaultproduct' => (string) $row['defaultproduct']
		);


		$filename = $this->getDataCacheName('info');

		$this->createConfigDataCache($info, $filename);

		return $info;

	}


	private function getConfigSizeData() {

		// Prepare the output array.
		$sizes = array();

		$sql = Connection::getHandle()->prepare(
                            "SELECT s.size_ref ,s.`name`,s.width,s.height,d.layout_ref as defaultlayout
		             				FROM bs_builders b
		             				LEFT JOIN bs_builder_size_groups sg ON (sg.size_group=b.size_group AND sg.active=TRUE)
		             				LEFT JOIN bs_builder_default_layout d ON (d.size_ref=sg.size_ref AND d.builder_ref=b.builder_ref AND d.active=TRUE)
		             				LEFT JOIN bs_builder_sizes s ON (s.size_ref=sg.size_ref AND s.active=TRUE)
		             				WHERE b.builder_ref = ? AND b.active = TRUE
		             				GROUP BY s.size_ref
		             				ORDER BY sg.order");

		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row_size = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the size reference.
			$size_ref = (string) $row_size['size_ref'];

			// Add data about each size into the output array.
			$sizes[$size_ref]= array(
				'name' => (string) $row_size['name'],
				'w' => (int) $row_size['width'],
				'h' => (int) $row_size['height'],
				'defaultlayout' => (string) $row_size['defaultlayout']
			);

		}

		$filename = $this->getDataCacheName('sizes');

		$this->createConfigDataCache($sizes, $filename);

		return $sizes;

	}


	private function getConfigMaterialData() {

		// Prepare the output array.
		$materials = array();

		// Query the database for material data.
		$sql = Connection::getHandle()
                    ->prepare("SELECT m.material_ref, m.name AS `name`, m.`description` AS description, IF(mm.chemical_resistance = TRUE, 'Yes', 'No') AS chemres, mm.durability AS durability,
                                IF(mm.luminous = TRUE, 'Yes', 'No') AS visibility, m.material_info_file AS materialinfo, mm.service_temperature_range AS svctemp
								FROM bs_builders b
                                JOIN bs_builder_material_groups mg ON (mg.material_group = b.material_group AND mg.active = 1)
                                LEFT JOIN bs_builder_materials m ON (m.material_ref = mg.material_ref AND m.active = 1)
                                INNER JOIN bs_materials mm ON (m.material_id = mm.id)
                                WHERE b.builder_ref = ?  AND b.active = 1
                                GROUP BY m.material_ref
                                ORDER BY mg.order, m.material_ref");

		$sql->execute(array($this->builderid));

		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the material ref.
			$material_ref = (string) $row['material_ref'];

			// Add data about each size into the output array.
			$materials[$material_ref] = array(
				'name' => (string) $row['name'],
				'description' => (string) $row['description'],
				'visibility' => (string) $row['visibility'],
				'chemres' => (string) $row['chemres'],
				'durability' => (string) $row['durability'],
				'svctemp' => (string) $row['svctemp'],
				'materialinfo' => (string) $row['materialinfo']
			);

		}

		$filename = $this->getDataCacheName('materials');

		$this->createConfigDataCache($materials, $filename);

		return $materials;

	}


	private function getConfigSchemeData() {

		// Prepare the output array;
		$schemes = array();

		// Query the database to get scheme data.
		$scheme_sql = '';

		$sql = Connection::getHandle()->prepare(
                       "SELECT GROUP_CONCAT(DISTINCT(cg.color_ref) ORDER BY cg.color_group_order) AS color_ref,
                        s.scheme_ref, s.`name`, sc.scheme_color_ref, sc.default_color_ref
                        FROM bs_builders b
                        JOIN bs_builder_scheme_groups sg ON (sg.scheme_group = b.scheme_group)
                        LEFT JOIN bs_builder_schemes s ON (s.scheme_ref = sg.scheme_ref AND s.active = 1)
                        LEFT JOIN bs_builder_scheme_colors sc ON (s.scheme_ref = sc.scheme_ref AND sc.active = 1 AND sc.color_group != '')
                        LEFT JOIN bs_builder_color_groups cg ON (cg.color_group=sc.color_group AND cg.active = 1)
                        WHERE b.builder_ref = ?
                        GROUP BY s.scheme_ref, sc.scheme_color_ref
                        ORDER BY s.scheme_ref, sc.scheme_color_ref");

		$sql->execute(array($this->builderid));

		while ( $scheme_row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the scheme ref.
			$scheme_ref = (string) $scheme_row['scheme_ref'];

			// If an array for this scheme ref hasn't already been created, do so now.
			if (!isset($schemes[$scheme_ref])) {
				$schemes[$scheme_ref] = array(
					'name' => (string) $scheme_row['name'],
					'schemecolors' => array()
				);
			}

			// Get the scheme color ref.
			$scheme_color = (string) $scheme_row['scheme_color_ref'];

			// Create an array for the scheme color if doesn't exist.
			if (!isset($schemes[$scheme_ref]['schemecolors'][$scheme_color])) {
				$schemes[$scheme_ref]['schemecolors'][$scheme_color] = array();
			}

			// Process scheme color's information to output array.
			$schemes[$scheme_ref]['schemecolors'][$scheme_color] = array(
				'colors' => explode(',', $scheme_row['color_ref']),
				'defaultcolor' => (string) $scheme_row['default_color_ref']
			);

		}

		$filename = $this->getDataCacheName('schemes');

		$this->createConfigDataCache($schemes, $filename);

		return $schemes;
	}


	private function getConfigLayoutData() {

		// Prepare the output array.
		$layouts = array();

		// Query the database for layout data.
		$sql = Connection::getHandle()->prepare("
		            SELECT GROUP_CONCAT(DISTINCT(sg.size_ref) ORDER BY sg.size_group ASC) AS `sizes`,
                        GROUP_CONCAT(DISTINCT(scg.scheme_ref) ORDER BY scg.scheme_group ASC) AS `schemes`,
                        l.layout_ref, l.`name`, la.element_ref AS elementref, la.origin, la.x, la.y, la.w, la.h, la.minh, la.minw, la.schemecolor, la.padding,
                        la.charlimit, la.default_artwork_ref AS defaultartwork, la.`leading`, la.default_text AS defaulttext, la.font, la.fontsize, la.minfontsize,
                        la.valign, la.halign, l.layout_id AS layoutvarid, l.layout_ref AS layoutref, sg.size_ref
                        FROM bs_builders b
                        LEFT JOIN bs_builder_layout_groups lg ON (lg.layout_group = b.layout_group AND lg.active = 1)
                        LEFT JOIN bs_builder_layouts l ON ( l.layout_ref = lg.layout_ref AND l.active = 1)
                        LEFT JOIN bs_builder_layout_appearances la ON (la.layout_id = l.layout_id AND la.active = 1)
                        LEFT JOIN bs_builder_size_groups sg ON (sg.size_group = l.size_group AND sg.active = 1)
                        LEFT JOIN bs_builder_scheme_groups scg ON (scg.scheme_group = l.scheme_group AND scg.active = 1)
                        WHERE b.builder_ref = ? AND b.active = 1
                        GROUP BY sg.size_group, scg.scheme_group, la.layout_appearance_id
                        ORDER BY lg.layout_group_order, la.layout_id, la.layout_appearance_id");

		$sql->execute(array($this->builderid));

		$lastlayoutvar = "";

		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the layout reference and variation ID.
			$layoutref = (string) $row['layoutref'];
			$layoutvarid = (string) $row['layoutvarid'];

			// Set up the layout if it doesn't already exist.
			if (!isset($layouts[$layoutref])) {
				$layouts[$layoutref] = array();
			}

			// Set up the layout variation if it doesn't already exist.
			if ($lastlayoutvar !== $layoutvarid) {
				$lastlayoutvar = $layoutvarid;
				$layouts[$layoutref][] = array(
					'name' => (string) $row['name'],
					'sizes' => explode(',', $row['sizes']),
					'schemes' => explode(',', $row['schemes']),
					'appearance' => array()
				);
			}

			// Get the layout variation number.
			$layoutvar = count($layouts[$layoutref]) - 1;

			// Get the element reference and create an array for it in the appearance property.
			$elementref = (string) $row['elementref'];
			$layouts[$layoutref][$layoutvar]['appearance'][$elementref] = array();

			// If a value was entered in the database, add that property to the element's appearance.
			if ($row['origin'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['origin'] = (string) $row['origin']; }
			if ($row['x'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['x'] = (int) $row['x']; }
			if ($row['y'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['y'] = (int) $row['y']; }
			if ($row['w'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['w'] = (int) $row['w']; }
			if ($row['h'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['h'] = (int) $row['h']; }
			if ($row['minw'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['minw'] = (int) $row['minw']; }
			if ($row['minh'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['minh'] = (int) $row['minh']; }
			if ($row['padding'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['padding'] = (int) $row['padding']; }
			if ($row['halign'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['halign'] = (string) $row['halign']; }
			if ($row['valign'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['valign'] = (string) $row['valign']; }
			if ($row['schemecolor'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['schemecolor'] = (string) $row['schemecolor']; }
			if ($row['font'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['font'] = (string) $row['font']; }
			if ($row['fontsize'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['fontsize'] = (int) $row['fontsize']; }
			if ($row['minfontsize'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['minfontsize'] = (int) $row['minfontsize']; }
			if ($row['leading'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['leading'] = (int) $row['leading']; }
			if ($row['charlimit'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['charlimit'] = (int) $row['charlimit']; }
			if ($row['defaulttext'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['defaulttext'] = (string) $row['defaulttext']; }
			if ($row['defaultartwork'] != NULL) { $layouts[$layoutref][$layoutvar]['appearance'][$elementref]['defaultartwork'] = (string) $row['defaultartwork']; }

		}

		$filename = $this->getDataCacheName('layouts');

		$this->createConfigDataCache($layouts, $filename);
		return $layouts;

	}



	private function getConfigProductData() {

		// Prepare the output array.
		$products = array();

		// Query the database for product data.
		$sql = Connection::getHandle()->prepare (
                        "SELECT bsku.product_ref AS productref, sku.name AS sku, bsku.size_ref AS size, bsku.material_ref AS material, bsku.scheme_ref AS scheme, pt.minimum_quantity AS minqty , pt.price AS price
						 FROM bs_builder_skus bsku
						 INNER JOIN bs_product_builders pb ON (pb.builder_ref = bsku.builder_ref)
						 INNER JOIN bs_product_skus ps ON (ps.product_id = pb.product_id)
						 INNER JOIN bs_products p ON (p.id = ps.product_id AND p.active = TRUE)
						 INNER JOIN bs_skus sku ON (sku.id = bsku.sku_id AND sku.active = TRUE)
						 LEFT JOIN bs_pricing pri ON (pri.id = sku.pricing_id )
						 LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = pri.id)
						 WHERE bsku.builder_ref = ? AND bsku.active = TRUE
						 GROUP BY bsku.product_ref, bsku.sku_id, bsku.size_ref, bsku.material_ref, bsku.scheme_ref,  pt.minimum_quantity, pt.price");

		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $products_row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the product ref.
			$productref = (string) $products_row['productref'];

			// If an array for this product ref hasn't already been created, do so now.
			if (!isset($products[$productref])) {
				$products[$productref] = array(
					'sku' => (string) $products_row['sku'],
					'size' => (string) $products_row['size'],
					'material' => (string) $products_row['material'],
					'scheme' => (string) $products_row['scheme'],
					'pricing' => array(),
					'options' => array()
				);
			}

			// Add this row of data to the output.
			$products[$productref]['pricing'][] = array(
				'minqty' => (int) $products_row['minqty'],
				'price' => (float) $products_row['price']
			);

			if (empty($products[$productref]['options'])) {
				// Query the database for product option data.
				$query = Connection::getHandle()->prepare(
					"SELECT bsku.sku_id, bsku.product_ref, blam.builder_preview_display AS lam_builder_preview_display, blam.name AS laminate_name, pt.price,
					mha.short_name AS short_mounting_hole_name, bmha.name AS mounting_hole_name, bmha.builder_preview_display AS mount_builder_preview_display, blam.option_value AS lam_option_value,
					bmha.option_value AS mha_option_value
						 FROM bs_builder_skus bsku
						 LEFT JOIN bs_skus sku ON (bsku.sku_id = sku.id AND sku.active = TRUE)
						 LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = sku.pricing_id)
						 LEFT JOIN bs_builder_laminates blam ON (blam.laminate_ref = bsku.laminate_ref AND blam.builder_ref = bsku.builder_ref)
						 LEFT JOIN bs_laminates lam ON (lam.id = sku.laminate_id)
						 LEFT JOIN bs_builder_mounting_hole_arrangements bmha ON (bmha.mounting_hole_arrangement_ref = bsku.mounting_hole_arrangement_ref AND bmha.builder_ref = bsku.builder_ref)
						 LEFT JOIN bs_mounting_hole_arrangements mha ON (mha.id = sku.mounting_hole_arrangement_id)
						 WHERE bsku.active = TRUE AND bsku.builder_ref = ?  AND bsku.product_ref = ?
						 GROUP BY bsku.product_ref, bsku.sku_id, blam.option_value, bmha.option_value order by bsku.position");

				$query->execute(array($this->builderid, $productref));

				// Loop through the results.
				while ($options_row = $query->fetch(PDO::FETCH_ASSOC)) {

					// Get the product ref and the option ref.

					$productref = (string)$products_row['productref'];

					if (!is_null($options_row['laminate_name'])) {
						$loptionref = (string)'antigraffiti';
					}
					if (!is_null($options_row['mounting_hole_name'])) {
						$moptionref = (string)'mountingoptions';
					}

					// If an options array hasn't already been created for this product, do so now.
					if (!isset($products[$productref]['options'])) {
						$products[$productref]['options'] = array();
					}


					// If an array hasn't already been created for this option, do so now.
					if (!isset($products[$productref]['options'][$loptionref])) {
						$products[$productref]['options'][$loptionref] = array();
					}

					if (!isset($products[$productref]['options'][$moptionref])) {
						$products[$productref]['options'][$moptionref] = array();
					}


					// Add this row of data to the output.
					if ($loptionref == "antigraffiti") {
						$loptionname = (bool)$options_row['lam_builder_preview_display'];
						$loption_price = (float)$options_row['laminate_price'];
						$loptionvalue = trim($options_row['lam_option_value']);
					}

					if ($moptionref == "mountingoptions") {
						$moptionname = (bool)$options_row['mount_builder_preview_display'];
						$moption_price = (float)$options_row['mounting_hole_price'];
						$moptionvalue = trim($options_row['mha_option_value']);
					}

					$products[$productref]['options'][$loptionref][] = array(
						'defaultoptionvalue' => $loptionname,
						'optionvalue' => $loptionvalue,
						'price' => $loption_price
					);

					$products[$productref]['options'][$moptionref][] = array(
						'defaultoptionvalue' => $moptionname,
						'optionvalue' => $moptionvalue,
						'price' => $moption_price
					);


				}
			}



		}





		$filename = $this->getDataCacheName('products');

		$this->createConfigDataCache($products, $filename);

		return $products;

	}



	private function getConfigOptionData() {

		// Prepare the output array.
		$options = array();

		// Query the database for option data.
		$sql = Connection::getHandle()->prepare("SELECT bsku.product_ref AS productref, blam.name AS laminate_name,
					 blam.builder_preview_display as lam_default_value, blam.option_value AS lam_builder_option_value, mha.name AS mounting_hole_name, mha.short_name AS short_mounting_hole_name,
					bmha.builder_preview_display AS mounting_hole_default_value, bmha.option_value AS mount_builder_option_value
		 			 FROM bs_builder_skus bsku
					 LEFT JOIN bs_skus sku ON (bsku.sku_id = sku.id AND sku.active = TRUE)
					 LEFT JOIN bs_builder_laminates blam ON (blam.laminate_ref = bsku.laminate_ref AND blam.builder_ref = bsku.builder_ref)
					 LEFT JOIN bs_laminates lam ON (lam.id = blam.laminate_id)
					 LEFT JOIN bs_builder_mounting_hole_arrangements bmha ON (bmha.mounting_hole_arrangement_ref = bsku.mounting_hole_arrangement_ref AND bmha.builder_ref = bsku.builder_ref)
					 LEFT JOIN bs_mounting_hole_arrangements mha ON (mha.id = bmha.mounting_hole_arrangement_id)
					 WHERE bsku.active = TRUE AND bsku.builder_ref = ?
					 GROUP BY bsku.sku_id, bsku.product_ref, blam.option_value, bmha.option_value order by bsku.position");
		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the option ref.

			if (!is_null($row['laminate_name'])) {
				$loptionref = (string)'antigraffiti';
			}
			if (!is_null($row['mounting_hole_name'])) {
				$moptionref = (string) 'mountingoptions';
			}

			// If an array for this option ref hasn't already been created, do so now.
			if (!isset($options[$loptionref])) {
				$options[$loptionref] = array();
			}
			if (!isset($options[$moptionref])) {
				$options[$moptionref] = array();
			}

			// Add the optionvalue's data to the output array.
			if ($loptionref == 'antigraffiti') {
				$optionvalue = $row['lam_builder_option_value'];
                if (!is_null($optionvalue) || !empty($optionvalue)) {
                    $options[$loptionref][$optionvalue] = array(
                        'name' => (string)$row['laminate_name'],
                        'display' => (bool)$row['lam_default_value']
                    );
                }

			}
			if ($moptionref == 'mountingoptions') {
				$optionvalue = $row['mount_builder_option_value'];
                if (!is_null($optionvalue) || !empty($optionvalue)) {
                    $options[$moptionref][$optionvalue] = array(
                        'name' => (string)$row['mounting_hole_name'],
                        'display' => (bool)$row['mounting_hole_default_value']
                    );
                }
			}


		}

		$filename = $this->getDataCacheName('options');

		$this->createConfigDataCache($options, $filename);

		return $options;

	}



	private function getConfigElementData() {

		// Prepare the output array.
		$elements = array();

		// Query the database for element data.
		$sql = Connection::getHandle()->prepare(
                    "SELECT elements_ref, type FROM bs_builder_elements WHERE builder_ref = ? AND active = 1 ");

		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the element ref.
			$ref = (string) $row['elements_ref'];

			// Add the interpreted results to the output array.
			$elements[$ref] = array(
				'type' => (string) $row['type']
			);

		}

		$filename = $this->getDataCacheName('elements');

		$this->createConfigDataCache($elements, $filename);

		return $elements;

	}



	private function getConfigArtworkData() {

		// Prepare the output array.
		$artwork = array();

		// Query the database for artwork data.
		$sql = Connection::getHandle()->prepare("SELECT DISTINCT(aw.artwork_ref) AS artwork_ref, aw.`name` AS `name`, i.raster_url AS url
		                			FROM bs_builder_ui u
		                			JOIN bs_builder_artwork_category_groups acg ON (acg.category_artwork_group=u.control_artwork_category_group AND acg.active = 1)
		                			LEFT JOIN bs_builder_artwork_categories ac ON (ac.categories_ref=acg.category_ref AND ac.active = 1)
		                			LEFT JOIN bs_builder_artwork_groups art ON (ac.artwork_group=art.artwork_group  AND art.active = 1)
		                			LEFT JOIN bs_builder_artwork aw ON (aw.artwork_ref=art.artwork_ref AND aw.active = 1)
		                			LEFT JOIN bs_builder_images_artwork i ON (i.artwork_ref=aw.artwork_ref AND i.color_ref='default' AND i.active = 1)
		               				WHERE u.builder_ref = ? AND u.control_artwork_category_group !='' ");
		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row_artwork = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the artwork ref.
			$ref = (string) $row_artwork['artwork_ref'];

			// Get the image dimensions
			list($width, $height) = getimagesize($this->basedir . $row_artwork['url']);

			// Insert this artwork's data into the output array.
			$artwork[$ref] = array(
				'name' => (string) $row_artwork['name'],
				'w' => (int) $width,
				'h' => (int) $height
			);

		}

		$filename = $this->getDataCacheName('artwork');

		$this->createConfigDataCache($artwork, $filename);

		return $artwork;

	}



	private function getConfigFontData() {


		// Prepare the output array.
		$fonts = array();
		// Query the database for font data.

		$sql_fonts = Connection::getHandle()->prepare(
                         "SELECT DISTINCT(fg.font_ref) AS fonts_ref, f.`name`

                            FROM bs_builder_ui u

                            JOIN bs_builder_font_groups fg ON(fg.font_group = u.control_font_group AND fg.active = 1)
                            LEFT JOIN bs_fonts f ON(f.font_ref = fg.font_ref AND f.active = 1)

                         WHERE builder_ref = :builder AND u.control_font_group != '' AND u.active = 1
                         ORDER BY fg.font_group_order, fg.font_ref"
        );

		$sql_fonts->execute(array(":builder"=>$this->builderid));

		// Loop through the results.
		while( $row_font=$sql_fonts->fetch(PDO::FETCH_ASSOC) ){

				// Get the font ref.
			$ref = (string) $row_font['fonts_ref'];

			// Add this font's data to the output array.
			$fonts[$ref] = array(
				'name' => (string) $row_font['name']
			);
		}
		$filename = $this->getDataCacheName('fonts');

		$this->createConfigDataCache($fonts, $filename);

		return $fonts;

	}



	private function getConfigColorData() {

		// Prepare the output array.
		$colors = array();

		// Query the database for color data.
		$sql = Connection::getHandle()->prepare(
                        "SELECT DISTINCT(c.colors_ref ), c.`name`, c.`value`

                        FROM bs_builders b

                        JOIN bs_builder_layout_groups lg ON (lg.layout_group = b.layout_group AND lg.active = 1)

                        LEFT JOIN bs_builder_layouts l ON (l.layout_ref = lg.layout_ref AND l.active = 1)
                        LEFT JOIN bs_builder_layout_appearances la ON (la.layout_id = l.layout_id AND la.active = 1)
                        LEFT JOIN bs_builder_scheme_colors sc ON(sc.scheme_color_ref = la.schemecolor AND sc.active = 1)
                        LEFT JOIN bs_builder_color_groups cg ON (cg.color_group = sc.color_group AND cg.active = 1)
                        LEFT JOIN bs_builder_colors c ON (c.colors_ref = cg.color_ref AND c.active = 1)

                        WHERE b.builder_ref = ? AND b.active = 1
                        ORDER BY cg.color_group_order, c.colors_ref");

		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the color ref.
			$ref = (string) $row['colors_ref'];

			// Add this color's data to the output array.
			$colors[$ref] = array(

				'name' => (string) $row['name'],
				'colorvalue' => (string) $row['value']
			);

		}

		$filename = $this->getDataCacheName('colors');

		$this->createConfigDataCache($colors, $filename);

		return $colors;

	}



	private function getConfigTextData() {

		// Prepare the output array.
		$text = array();

		// Query the database for text data.
		$sql = Connection::getHandle()->prepare("
		                    SELECT DISTINCT(tg.text_ref), t.`name`, t.`fulltext`
							        FROM bs_builder_ui u
							        JOIN bs_builder_text_groups tg ON (tg.text_group=u.control_text_group AND tg.active = 1)
							        LEFT JOIN bs_builder_text t ON (t.text_ref=tg.text_ref AND t.active = 1)
							        WHERE u.builder_ref = ? AND u.control_text_group!='' AND u.active = 1
							        ORDER BY tg.text_group_order");

		$sql->execute(array($this->builderid));

		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Get the text ref.
			$ref = (string) $row['text_ref'];

			$text[$ref] = array(
				'name' => (string) $row['name'],
				'fulltext' => (string) $row['fulltext']
			);

		}

		$filename = $this->getDataCacheName('text');

		$this->createConfigDataCache($text, $filename);

		return $text;
	}


	private function getConfigUiData() {

		// Prepare the output array.
		$ui = array();

		// Query the database for the UI data.
		$sql = Connection::getHandle()->prepare('
                     SELECT GROUP_CONCAT(DISTINCT(ag.artwork_ref) ORDER BY ag.artwork_group_order ASC) AS `control_categories_artwork`,
                            GROUP_CONCAT(DISTINCT(sg.size_ref) ORDER BY sg.order ASC) AS `control_sizes`,
                            GROUP_CONCAT(DISTINCT(mg.material_ref) ORDER BY mg.order ASC) AS `control_materials`,
                            GROUP_CONCAT(DISTINCT(cg.scheme_ref) ORDER BY cg.scheme_group_order ASC) AS `control_schemes`,
                            GROUP_CONCAT(DISTINCT(tg.text_ref) ORDER BY tg.text_group_order ASC) AS `control_text`,
                            GROUP_CONCAT(DISTINCT(fg.font_ref) ORDER BY fg.font_group_order ASC) AS `control_fonts`,
                            GROUP_CONCAT(DISTINCT(lg.layout_ref) ORDER BY lg.layout_group_order ASC) AS `control_layouts`,
                            GROUP_CONCAT(DISTINCT(mcg.comparison) ORDER BY mcg.comparison_group_order ASC) AS `control_comparisons`,
                            ui.ui_id AS `id`, sec.section_ref AS `section_ref`, sec.section_name AS `section_name`,
                            ui.control_type AS `control_type`, ui.control_name AS `control_name`, ui.control_description AS `control_description`,
                            ui.control_target AS `control_target`, ui.control_allownone AS `control_allownone`,
                            ui.control_allowcustom AS `control_allowcustom`, acg.category_ref AS `control_categories_ref`,
                            ac.categories_name AS `control_categories_name`, ui.control_aligncontrols AS `control_aligncontrols`,
                            ui.control_sizecontrols AS `control_sizecontrols`

                            FROM bs_builders b

                            LEFT JOIN bs_builder_ui ui ON (ui.active = 1 AND ui.builder_ref=b.builder_ref)
                            LEFT JOIN bs_builder_ui_sections sec ON (sec.active = 1 AND sec.section_ref=ui.section_ref)
                            LEFT JOIN bs_builder_size_groups sg ON (ui.control_type="size" AND sg.active = 1 AND sg.size_group=b.size_group)
                            LEFT JOIN bs_builder_material_groups mg ON (ui.control_type="material" AND mg.active = 1 AND mg.material_group=b.material_group)
                            LEFT JOIN bs_builder_material_comparison_groups mcg ON (ui.control_type="material" AND mcg.active = 1 AND mcg.comparison_group=ui.control_comparison_group)
                            LEFT JOIN bs_builder_scheme_groups cg ON (ui.control_type="scheme" AND cg.active = 1 AND cg.scheme_group=b.scheme_group)
                            LEFT JOIN bs_builder_layout_groups lg ON (ui.control_type="layout" AND lg.active = 1 AND lg.layout_group=b.layout_group)
                            LEFT JOIN bs_builder_artwork_category_groups acg ON (ui.control_type="artwork" AND acg.active = 1 AND acg.category_artwork_group=ui.control_artwork_category_group)
                            LEFT JOIN bs_builder_artwork_categories ac ON (ac.active = 1 AND ac.categories_ref=acg.category_ref)
                            LEFT JOIN bs_builder_artwork_groups ag ON (ag.active = 1 AND ag.artwork_group=ac.artwork_group)
                            LEFT JOIN bs_builder_font_groups fg ON (ui.control_type IN ("text","textarea","textselect") AND fg.active = 1 AND fg.font_group=ui.control_font_group)
                            LEFT JOIN bs_builder_text_groups tg ON (ui.control_type="textselect" AND tg.active = 1 AND tg.text_group=ui.control_text_group)
                            WHERE b.active = 1 AND b.builder_ref = ? GROUP BY control_target, control_type, acg.category_ref
                            ORDER BY ui.section_order ASC, ui.control_order ASC, acg.category_group_order ASC');

		$sql->execute(array($this->builderid));


		// Loop through the results.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Set up the section if it doesn't already exist.
			if ($last_section_ref != $row['section_ref']) {

				$last_section_ref = (string) $row['section_ref'];

                $ui[] = array(
					'name' => (string) $row['section_name'],
					'controls' => array()
				);
			}

			// Get the section number.
			$section_number = count($ui) - 1;

			// If this is a new control, add data to the output array.
			if ($lasttarget != $row['id']) {

				// Store this control ID to check against in the next iteration.
				$lasttarget = $row['id'];

				// Prepare the control array.
				$ui[$section_number]['controls'][] = array();

				// Get the control number.
				$control_number = count($ui[$section_number]['controls']) - 1;

				// Add data to the control array.
				$ui[$section_number]['controls'][$control_number]['type'] = (string) $row['control_type'];
				$ui[$section_number]['controls'][$control_number]['name'] = (string) $row['control_name'];
				if ($row['control_sizes'] != NULL) { $ui[$section_number]['controls'][$control_number]['sizes'] = explode(',', $row['control_sizes']); }
				if ($row['control_materials'] != NULL) { $ui[$section_number]['controls'][$control_number]['materials'] = explode(',', $row['control_materials']); }
				if ($row['control_schemes'] != NULL) { $ui[$section_number]['controls'][$control_number]['schemes'] = explode(',', $row['control_schemes']); }
				if ($row['control_layouts'] != NULL) { $ui[$section_number]['controls'][$control_number]['layouts'] = explode(',',$row['control_layouts']); }
				if ($row['control_description'] != NULL) { $ui[$section_number]['controls'][$control_number]['description'] = (string) $row['control_description']; }
				if ($row['control_comparisons'] != NULL) { $ui[$section_number]['controls'][$control_number]['comparisons'] = explode(',', $row['control_comparisons']); }
				if ($row['control_allownone'] != NULL || $row['control_allownone'] != '') { $ui[$section_number]['controls'][$control_number]['allownone'] = ($row['control_allownone'] == TRUE);																				 }
				if ($row['control_allowcustom'] != NULL || $row['control_allowcustom'] != '') { $ui[$section_number]['controls'][$control_number]['allowcustom'] = ($row['control_allowcustom'] == TRUE);																				 }
				if ($row['control_target'] != NULL || $row['control_target'] != '') { $ui[$section_number]['controls'][$control_number]['target'] = (string) $row['control_target']; }
				if ($row['control_fonts'] != NULL) { $ui[$section_number]['controls'][$control_number]['fonts'] = explode(',', $row['control_fonts']); }
				if ($row['control_text'] != NULL) { $ui[$section_number]['controls'][$control_number]['text'] = explode(',', $row['control_text']); }
				if ($row['control_aligncontrols'] != NULL) { $ui[$section_number]['controls'][$control_number]['aligncontrols'] = ($row['control_aligncontrols']) == TRUE; }
				if ($row['control_sizecontrols'] !=  NULL) { $ui[$section_number]['controls'][$control_number]['sizecontrols'] = ($row['control_sizecontrols']) == TRUE; }

			}

			// Add the artwork category information to the output array (if applicable).
			if ($row['control_categories_artwork'] != NULL) {
				$ui[$section_number]['controls'][$control_number]['categories'][] = array(
					'ref' => (string) $row['control_categories_ref'],
					'name' => (string) $row['control_categories_name'],
					'artwork' => explode(',', $row['control_categories_artwork'])
				);
			}

		}

		$filename = $this->getDataCacheName('ui');

		$this->createConfigDataCache($ui, $filename);

		return $ui;

	}


	private function getDataCacheName($section) {

		// If section isset, create cache files name
		if (isset($section)) {

				$filename = (string) $section;


			$directory = $this->cachedir. '/' . $this->builderid;

			$dataCacheName = $directory . '/' . $filename . '.json';

		}

		return $dataCacheName;

	}


	private function getConfigDataCache($cachefile) {
		return json_decode(file_get_contents($cachefile), true);

	}



	private function createConfigDataCache($data, $cachefile) {

		// Get the main cache directory.
		$cachedirectory = $this->cachedir;

		// Get the cache file's subdirectory.
		$subdirectory = pathinfo($cachefile, PATHINFO_DIRNAME);

		// Check and fix the permissions on the directory and subdirectory if necessary.
		foreach ( array($cachedirectory, $subdirectory) as $directory ) {

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

		// Try creating the cache file (return false on failure).
		if (!file_put_contents($cachefile, json_encode($data))) {
			return false;
		}

		// Try to fix the cache file permissions, but don't return false on failure; just proceed.
		chmod($cachefile, 0777);

		// If the function got here it means the cache file was successfully created, so return true.
		return true;

	}

	// Retrieve user-edited data from the database.
	private function getTweakData() {

		// Prepare output array.
		$tweakconfig = array();

		//Run the query
		$sql = Connection::getHandle()->prepare(
                        "SELECT setting AS setting, subsetting AS subsetting, value AS value, setting_display AS display,
                        setbyuser AS setbyuser, color AS color, font AS font, font_setbyuser AS font_setbyuser, fontsize AS fontsize,
                        fontsize_setbyuser AS fontsize_setbyuser, alignment AS alignment, alignment_setbyuser AS alignment_setbyuser
                        FROM bs_builder_tweak_tool_data WHERE tool_id = ? ORDER BY id");

		if ($sql->execute(array($this->tweakid)) ){

			// Loop through all records
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

				// Update the tweak config array with this row of data.
				$this->updateSettings($tweakconfig, $row);

			}
	}

		return $tweakconfig;

	}



	// Retrieve tweak-mode data from the database.
	private function getEditData() {

		// Prepare output array.
		$edits = array();

		// Prepare the query.
			$sql = Connection::getHandle()->prepare("
                        SELECT sca.builder_setting AS setting, sca.builder_subsetting AS subsetting,
                        sca.builder_value AS value, sca.builder_setting_display AS display,
                        sca.builder_setbyuser AS setbyuser, sca.builder_color AS color, sca.builder_font AS font,
                        sca.builder_font_setbyuser AS font_setbyuser, sca.builder_fontsize AS fontsize,
                        sca.builder_fontsize_setbyuser AS fontsize_setbyuser, sca.builder_alignment AS alignment,
                        sca.builder_alignment_setbyuser AS alignment_setbyuser, sc.quantity AS quantity,
                        sc.comments AS instructions, sc.design_service AS designservice, d.hash AS designid

                        FROM bs_cart_skus sc

                        LEFT JOIN bs_cart_sku_data sca ON ( sca.cart_sku_id = sc.id )
                        LEFT JOIN bs_designs d ON ( sc.design_id = d.id )
                        LEFT JOIN bs_tool_types t ON (sc.tool_type_id = t.id)

                        WHERE t.`name` = 'builder' AND d.hash = ? ");

			$sql->execute(array($this->designid));

		// Loop through all records.
		while ( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

			// Update the edit data with this row of data.
			$this->updateSettings($edits, $row);

			// Quantity, instructions, and design service.
			if (!isset($edits['qty'])) { $edits['qty'] = (int) $row['quantity']; }
			if (!isset($edits['instructions'])) { $edits['instructions'] = (string) $row['instructions']; }
			if (!isset($edits['designservice'])) { $edits['designservice'] = ($row['designservice'] || $row['designservice'] == "1" ? 'adjust' : FALSE); }

		}

		return $edits;

	}



	// Retrieve user-uploaded file data from the database.
	private function getUploadData() {


		// Prepare the uploads array.
		$uploads = array();

		// If there is any data available that can be associated with an upload...
		if (isset($this->designid) || isset($this->customerid) || isset($this->sessionid)) {

			$queries = array();

			// Prepare a query to get all of the uploads used in the current edit (if applicable).
			if (isset($this->designid)) {
				if($this->admin) {
					$queries[] = '( SELECT u.`hash` AS id, UNIX_TIMESTAMP(u.time_stamp) AS uploadtime, u.name AS name, u.converted_directory AS src_dir, u.converted_filename AS src_file
									FROM bs_cart_sku_data csd
									LEFT JOIN bs_builder_uploads u ON (csd.builder_value = u.`hash`)
									LEFT JOIN bs_cart_skus cs ON (csd.cart_sku_id = cs.id)
									LEFT JOIN bs_designs d ON (cs.design_id = d.id)
									WHERE d.`hash` = ? AND csd.builder_setting = "upload" )';
				} else {

					$queries[] = '( SELECT u.`hash` AS id, UNIX_TIMESTAMP(u.time_stamp) AS uploadtime, u.name AS name, u.converted_directory AS src_dir, u.converted_filename AS src_file
									FROM bs_cart_sku_data csd
									LEFT JOIN bs_builder_uploads u ON (csd.builder_value = u.`hash`)
									LEFT JOIN bs_cart_skus cs ON (csd.cart_sku_id = cs.id)
									LEFT JOIN bs_designs d ON (cs.design_id = d.id)
									WHERE d.`hash` = ? AND csd.builder_setting = "upload" )';
				}
			}

			// Prepare a query to get all of the uploads associated with the customer ID (if applicable).
			if (isset($this->customerid)) {
				$queries[] = '( SELECT u.hash AS id, UNIX_TIMESTAMP(u.time_stamp) AS uploadtime, u.name AS name, u.converted_directory AS src_dir, u.converted_filename AS src_file FROM bs_builder_uploads u WHERE customer_id=? )';
			}

			// Prepare a query to get all of the uploads associated with the session ID (if applicable).
			if (isset($this->sessionid)) {
				$queries[] = '( SELECT u.hash AS id, UNIX_TIMESTAMP(u.time_stamp) AS uploadtime, u.name AS name, u.converted_directory AS src_dir, u.converted_filename AS src_file FROM bs_builder_uploads u WHERE session_id=? )';
			}

			// In case there was more than one query, combine them with UNION DISTINCT. Either way, also set the ordering.
			$query = implode(' UNION DISTINCT ', $queries) . ' ORDER BY uploadtime DESC, name ASC, id ASC';
			$sql = Connection::getHandle()->prepare($query);


			//Keep track of the number of paramaters we've bound
			$param_counter = 1;

			if (isset($this->designid)) {
				$sql->bindParam($param_counter, $this->designid);
				$param_counter++;
			}

			if (isset($this->customerid)) {
				$sql->bindParam($param_counter, $this->customerid);
				$param_counter++;
			}

			if (isset($this->sessionid)) {
				$sql->bindParam($param_counter, $this->sessionid);
				$param_counter++;
			}

			$sql->execute();

			// Extract the results to populate the uploads array.
			while( $row = $sql->fetch(PDO::FETCH_ASSOC) ) {

				// Get the upload ID.
				$id = (string) $row['id'];

				// Get the image dimensions
				list($width, $height) = getimagesize($this->basedir . $row['src_dir'] . $row['src_file']);

				// Add the data to the output array.
				$uploads[$id] = array(
					'name' => (string) $row['name'],
					'src' => ($row['src_dir'] . $row['src_file']),
					'uploadtime' => (int) $row['uploadtime'],
					'w' => (int) $width,
					'h' => (int) $height
				);

			}

			return $uploads;

		}

	}



	// Helper functions to translate database values into settings arrays for edit and tweak data.
	private function updateSettings(&$settings, $data) {

		switch ($data['setting']) {

			case 'size':
				$settings['size'] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'material':
				$settings['material'] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'scheme':
				$settings['scheme'] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'layout':
				$settings['layout'] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'option':
				$settings['options'][$data['subsetting']] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'schemecolor':
				$settings['colors'][$data['subsetting']] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
			break;

			case 'text':
				$settings['elements'][$data['subsetting']] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
				$settings['elements'][$data['subsetting']]['font'] = $this->formatSubSetting($data['font'], $data['font_setbyuser']);
				$settings['elements'][$data['subsetting']]['fontsize'] = $this->formatSubSetting($data['fontsize'], $data['fontsize_setbyuser']);
				$settings['elements'][$data['subsetting']]['alignment'] = $this->formatSubSetting($data['alignment'], $data['alignment_setbyuser']);
			break;

			case 'artwork':
				$settings['elements'][$data['subsetting']] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
				$settings['elements'][$data['subsetting']]['custom'] = false;
			break;

			case 'upload':
				$settings['elements'][$data['subsetting']] = $this->formatSetting($data['value'], $data['setbyuser'], $data['display']);
				$settings['elements'][$data['subsetting']]['custom'] = true;
			break;

		}

	}



	private function formatSetting($value, $setbyuser, $controlenabled) {

		$setting = array(
			'value' => (string) $value,
			'setbyuser' => ($setbyuser || $setbyuser == "1" ? TRUE : FALSE),
			'controlenabled' => ($controlenabled || $controlenabled == "1" ? TRUE : FALSE)
		);

		return $setting;

	}



	private function formatSubsetting($value, $setbyuser) {

		$subsetting = array(
			'value' => (string) $value,
			'setbyuser' => ($setbyuser || $setbyuser == "1" ? TRUE : FALSE)
		);

		return $subsetting;

	}


}
