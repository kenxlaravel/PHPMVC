(function (window, document, undefined) {

'use strict';

var

// Private Variables
instances = {},
baseurl = '',
spellcheckurl = '/process/spellcheck.php',
imageurl = '/builderimages/',
fileuploadurl = '/process/builder_upload.php',
carturl = '/cart',
fontstylesheet = '/styles/builderfonts.css',
producttype = 'custom-builder',
fontsizes = {
	auto: "Autosize",
	smallest: "Smallest",
	small: "Small",
	medium: "Medium",
	large: "Large",
	largest: "Largest"
},
alignments = {
	left: "Left",
	center: "Center",
	right: "Right"
},
images = {
	changealerticon: '/images/builder/change-alert-icon.png',
	informationicon: '/images/builder/information-icon.png',
	successicon: '/images/builder/success-icon.png',
	fatalerroricon: '/images/builder/fatal-error-icon.png',
	customartworkplaceholder: '/images/builder/custom-artwork-placeholder.png',
	loading: '/images/builder/spinner.gif',
	erroricon: '/images/builder/error-icon.png',
	sprites: '/images/builder/builder-sprite.png'
},
edges = [
	{
		edge: 'bottom',
		opposite: 'top',
		orientation: 'vertical',
		direction: 'negative'
	},
	{
		edge: 'top',
		opposite: 'bottom',
		orientation: 'vertical',
		direction: 'positive'
	},
	{
		edge: 'right',
		opposite: 'left',
		orientation: 'horizontal',
		direction: 'negative'
	},
	{
		edge: 'left',
		opposite: 'right',
		orientation: 'horizontal',
		direction: 'positive'
	}
],
uploads = {},
phonenum = '800-274-6271',
emailaddress = 'sales@safetysign.com',
prepared = false,
numeral = window.numeral ? window.numeral : null,
WebFont = window.WebFont ? window.WebFont : null,
ss = window.ss ? window.ss : null,

// Private Functions
builder = function (input) {

	if (prepared === false) {

		// Prepare the basic functionality.
		prepareBasicFunctionality();

		// Prevent prepareBasicFunctionality() from running more than once.
		prepared = true;

	}

	if (!input) {

		// If the input is empty or missing, return all Builders.
		return instances;

	} else if (input.nodeType) {

		// If a DOM Element was supplied, create a new Builder for it.
		return createNewBuilderInstance(input);

	} else if (typeof input === 'string') {

		// If a string was supplied, look up the Builder associated with that string.
		return instances[input];

	}

},
createNewBuilderInstance = function (e) {

	var $e = $(e),

	id = $e.attr('data-builder-id'),
	mode = $e.attr('data-builder-mode'),
	submiturl = $e.attr('data-builder-submiturl'),
	addToCartOverrides = {
		subcategoryid: $e.attr('data-builder-subcategoryid'),
		landingid: $e.attr('data-builder-landingid'),
		sourceproductid: $e.attr('data-builder-sourceproductid'),
		sourceproductrecommendationid: $e.attr('data-builder-sourceproductrecommendationid'),
		sourceaccessoryfamilyproductid: $e.attr('data-builder-sourceaccessoryfamilyproductid'),
		sourceinstallationaccessoryid: $e.attr('data-builder-sourceinstallationaccessoryid'),
		sourcelandingproductid: $e.attr('data-builder-sourcelandingproductid'),
		sourcesubcategoryproductid: $e.attr('data-builder-sourcesubcategoryproductid')
	},
	inputdata = JSON.parse($e.attr('data-builder-inputdata')),
	config = inputdata.configdata,
	designid,
	settings;

	if (mode === 'edit' || mode === 'adminedit') {

		// For edit mode, extract the design ID, and settings from editdata.
		designid = $e.attr('data-builder-designid');
		settings = inputdata.editdata;

	} else if (mode === 'tweak') {

		// For tweak mode, extract settings from the tweakdata.
		settings = inputdata.tweakdata;

	}

	// Create a new Builder if it doesn't already exist and the required information was supplied. Then initialize it.
	if (!instances.hasOwnProperty(id) && defined(config) && (mode === 'new' || mode === 'edit' || mode === 'tweak' || mode === 'adminedit') && (typeof id === 'string' && id !== '')) {
		instances[id] = new Builder(e, mode, submiturl, id, addToCartOverrides, config, settings, designid);
		instances[id].preload();
	}

	// Add the uploads.
	addUploads(inputdata.uploaddata);

	return instances[id];

},
prepareBasicFunctionality = function () {

	var $builder = $('div.builder'), $html = $('html'), $window = $(window);

	// Main functionality.
	$builder.find('fieldset.artwork').on('click', '.prev-artwork, .next-artwork, .page-number', artworkPaginationClick);
	$builder.find('fieldset.control .expanding-textarea').on('keyup', 'textarea', expandingTextareaInput);
	$builder.find('fieldset.material').on('click', '.material-info', materialInfoClick).on('click', '.row-cell-wrap', tableRowWrapClick);
	$builder.children('div.sidebar').find('a.full-pricing-link').on('click', fullPricingClick);
	$builder.find('a.dialog').on('click', dialogLinkClick);
	$builder.find('fieldset.control div.fontfamily-control, fieldset.control div.fontsize-control').on('click', customDropdownClick);
	$html.on('click', htmlClick);
	$window.on('scroll resize', windowScrollResize);

	// IE fixes.
	if ($html.hasClass('lt-ie9')) { $builder.on('click', '.control-option-wrap img', ieImgLabelClick); }

},
determineQuantity = function (value) {
	var qty = parseInt(value, 10);
	if (typeof qty !== 'number' || qty <= 0) {
		qty = 1;
	}
	return qty;
},
getCharacterCountMessage = function (current, charlimit) {
	var messagehtml = '';
	if (charlimit > 0) {
		var remainder = charlimit - current;
		if ( remainder < 0 ) {
			messagehtml += '<span class="char-count-language">Too much text! Reduce to ' + charlimit + ' characters.</span>';
		} else {
			messagehtml += '<span class="chars-avail">' + remainder + '</span> <span class="char-count-language">' + (remainder === 1 ? 'character' : 'characters') + ' left</span>';
		}

	}
	return messagehtml;
},
multiLineHtmlEncode = function (value) {
	var lines = value.split(/\r\n|\r|\n/);
	for (var i = 0; i < lines.length; i++) {
		lines[i] = htmlEncode(lines[i]);
	}
	return lines.join('<br />');
},
htmlEncode = function (value) {
	return $('<div />').text(value).html();
},
scrollSidebar = function () {

	var $window = $(window),
		windowheight = $window.height(),
		paddingTop = window.ss.site.getViewportStart() + 5,
		paddingBottom = 5,
		id,
		$builder,
		$sidebar,
		sidebarheight,
		scrolltop,
		buildertop,
		builderheight;

	for (id in instances) {
		if (instances.hasOwnProperty(id) && instances[id] instanceof Builder) {

			$builder = $(instances[id].e);
			$sidebar = $builder.children('div.sidebar');
			sidebarheight = $sidebar.outerHeight(false);

			if (windowheight >= (sidebarheight + paddingTop + paddingBottom)) {

				scrolltop = $window.scrollTop();
				buildertop = $builder.offset().top;
				builderheight = $builder.outerHeight(false);

				if (builderheight > sidebarheight && scrolltop >= (buildertop - paddingTop)) {

					if (scrolltop <= (buildertop + builderheight - sidebarheight - paddingTop)) {
						$sidebar.removeClass('bottom').addClass('fixed');
					} else {
						$sidebar.removeClass('fixed').addClass('bottom');
					}

				} else {

					$sidebar.removeClass('fixed bottom');

				}

			} else {

				$sidebar.removeClass('fixed bottom');

			}

		}
	}

},
expandTextarea = function (textarea) {
	$(textarea).parent().find('pre span').text($(textarea).val());
},
triggerRowOption = function (row) {
	var input = $(row).find('input[type="radio"]').not(':checked');
	if (input.length > 0) {
		input.trigger('change').prop('checked', true);
	}
},
resizeToFit = function (width, height, maxwidth, maxheight) {

	var dimensions = {
		width: 0,
		height: 0
	};

	if (typeof width === 'number' && width > 0 && typeof height === 'number' && height > 0 && typeof maxwidth === 'number' && maxwidth > 0 && typeof maxheight === 'number' && maxheight > 0) {
		var scalingratio = Math.min(maxwidth/width, maxheight/height);
		dimensions.width = Math.round(width * scalingratio);
		dimensions.height = Math.round(height * scalingratio);
	}

	return dimensions;

},
addUploads = function (data) {

	var upload;

	for (upload in data) {
		if (data.hasOwnProperty(upload)) {
			addUpload(upload, data[upload].src, data[upload].name, data[upload].uploadtime, data[upload].w, data[upload].h);
		}
	}

},
addUpload = function (id, src, name, time, w, h) {

	if (!uploads.hasOwnProperty(id)) {
		uploads[id] = {
			src: src,
			name: name,
			time: time,
			w: w,
			h: h
		};
	}

},
getSortedUploads = function () {
	var alluploads;
	alluploads = [];
	for (var upload in uploads) {
		if (uploads.hasOwnProperty(upload)) {
			alluploads[alluploads.length] = {
				id: upload,
				src: uploads[upload].src,
				name: uploads[upload].name,
				time: uploads[upload].time,
				w: uploads[upload].w,
				h: uploads[upload].h
			};
		}
	}
	alluploads.sort(sortUploads);
	return alluploads;
},
sortUploads = function (a, b) {

	var a_time = ((typeof a.time === 'number' && a.time > 0) ? a.time : 0),
		b_time = ((typeof b.time === 'number' && b.time > 0) ? b.time : 0),
		a_name = (typeof a.name === 'string' ? a.name.toLowerCase() : ''),
		b_name = (typeof b.name === 'string' ? b.name.toLowerCase() : ''),
		a_id = (typeof a.id === 'string' ? a.id.toLowerCase() : ''),
		b_id = (typeof b.id === 'string' ? b.id.toLowerCase() : '');

	if (a_time !== b_time) {
		return b_time - a_time;
	} else if (a_name !== b_name) {
		return (a_name < b_name ? -1 : 1);
	} else if (a_id !== b_id) {
		return (a_id < b_id ? -1 : 1);
	} else {
		return 0;
	}

},
showDialog = function (options) {

	// Prepare Fancybox's options
	var fancyboxoptions = {};
	fancyboxoptions.wrapCSS = 'builderstyle';
	fancyboxoptions.modal = (options.modal === true);
	if (typeof options.width === 'number' && typeof options.height === 'number') {
		fancyboxoptions.autoSize = false;
		fancyboxoptions.width = options.width;
		fancyboxoptions.height = options.height;
	} else {
		fancyboxoptions.autoSize = true;
		if (typeof options.width === 'number') {
			fancyboxoptions.minWidth = options.width;
			fancyboxoptions.maxWidth = options.width;
		}
		if (typeof options.height === 'number') {
			fancyboxoptions.minHeight = options.height;
			fancyboxoptions.maxHeight = options.height;
		}
	}
	if (options.ajax === true) {
		fancyboxoptions.type = 'ajax';
	}

	// Generate the dialog using Fancybox
	if (defined(options.content)) {
		$.fancybox.open(options.content, fancyboxoptions);
	} else {
		$.fancybox.open(fancyboxoptions);
	}

},
closeDialog = function (remove) {

	$.fancybox.close();

	// If requested, wait a second and then remove the content from the DOM.
	if (remove === true) { setTimeout(removeDialogContent, 1000); }

},
removeDialogContent = function () {
	$(document.getElementById('builder-dialog-wrapper')).remove();
},
updateDialog = function () {
	$.fancybox.update();
},
defined = function (v) {
	return (typeof v !== 'undefined' && v !== null);
},
formatPrice = function (price, currencySymbol) {

    var formattedPrice;

    if ( numeral ) {

        // Default to $ as the currency symbol.
        if ( !defined(currencySymbol) ) {
            currencySymbol = "$";
        }

        formattedPrice = numeral(price).format(currencySymbol + "0,0.00");

    } else {

        formattedPrice = "";

    }

    return formattedPrice;

},
between = function (num, a, b, inclusive) {
	if (inclusive === true) {
		return (a < b ? num >= a && num <= b : num <= a && num >= b);
	} else {
		return (a < b ? num > a && num < b : num < a && num > b);
	}
},
getEdgeData = function (edge) {

	for (var i = 0; i < edges.length; i++) {
		if (edges[i].edge === edge) {
			return edges[i];
		}
	}

},
generatePriceDiffText = function (currentprice, price) {

	var pricedifference = price - currentprice,
		text = '';

	if (pricedifference > 0) {
		text = '(Add ' + formatPrice(pricedifference, '$') + ')';
	} else if (pricedifference < 0) {
		text = '(Subtract ' + formatPrice((0 - pricedifference), '$') + ')';
	}

	return text;

},
truncateCaption = function (caption, limit) {

	var truncated = '';

	if (typeof caption === 'string') {

		if (caption.length > limit) {
			truncated = caption.substr(0, (limit - 1)) + '…';
		} else {
			truncated = caption;
		}

	}

	return truncated;

},
updateArtworkPagination = function ($picker, pagenum, animate) {

	var pagecount = $picker.find('.page-number').length,
		currentpagenumber = parseInt($picker.find('.page-number.current').text(), 10),
		$container = $picker.children('.artwork-pages-wrap-outer'),
		i, newpagenumber, scrollposition, $imgs;

	// Figure out what the newly-requested page number is.
	switch (pagenum) {
		case 'prev': newpagenumber = currentpagenumber - 1; break;
		case 'next': newpagenumber = currentpagenumber + 1; break;
		default: newpagenumber = parseInt(pagenum > 0 ? pagenum : 1, 10); break;
	}
	if (newpagenumber < 1) { newpagenumber = 1; }
	if (newpagenumber > pagecount) { newpagenumber = pagecount; }

	// Update the pagination display if the newly-requested page number is different from the current page number.
	if (newpagenumber !== currentpagenumber) {
		$picker.find('.page-number').eq(newpagenumber-1).addClass('current').siblings('.page-number').removeClass('current');
	}

	// Calculate where the container will need to scroll to.
	scrollposition = $container.outerWidth(false) * (newpagenumber - 1);

	// Scroll or jump to the correct page, depending on whether or not animation was requested.
	if (animate) {
		$container.stop().animate({scrollLeft: scrollposition}, 250);
	} else {
		$container.scrollLeft(scrollposition);
	}

	// Load the necessary artwork images.
	$imgs = $container.children('.artwork-pages-wrap-inner').children('.artwork-page').eq(newpagenumber-1).find('img');
	for (i = 0; i < $imgs.length; i++) {
		loadImage($imgs[i], $($imgs[i]).attr('data-builder-img-src'));
	}

},
updateUploadControls = function () {

	for (var id in instances) {
		if (instances.hasOwnProperty(id) && instances[id] instanceof Builder) {
			instances[id].rebuildControls('upload', true);
		}
	}

},
createBoundFunction = function (f, c) {

	return function () {
		return f.apply(c, arguments);
	};

},
downloadImage = function (src, data, callback) {

	if (typeof src === 'string' && src.length > 0) {

		// Create a DOM element, prepare the load event, then update the DOM element's source.
		$(document.createElement('img')).one('load error', data, callback).attr('src', src);

	} else {

		callback({data: data});

	}

},
loadImage = function (img, src) {

	var $img = $(img);

	// If the image source is changing...
	if ($img.attr('src') !== src) {

		// Update the source and placeholder source.
		$img.attr('src', baseurl + images.loading).attr('data-builder-img-src', src);

		// Download the image.
		downloadImage(src, {img:img, src:src}, imageLoaded);

	}

},
prepareFileUploader = function (e) {

	var upload = new Upload(e);
	upload.prepare();

},

// Private Events
submit = function (e) {

	e.preventDefault();
	e.data.builder.addToCart();

},

sizeChange = function (e) {

	var size;

	// Capture the new size.
	size = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(size, 'size');

	// Update the interface.
	e.data.builder.checkControls(['material','scheme','layout','option','color','artwork','text','textarea','textselect']);
	e.data.builder.rebuildControls(['material','scheme','layout','option','color','artwork','text','fontsize']);
	e.data.builder.updateSelections(['size','material','scheme','layout','option','color','artwork','text','textarea','textselect','fontfamily','fontsize','fontalignment']);
	e.data.builder.updatePricing(true);
	e.data.builder.updateProductDetails();
	e.data.builder.updateOptionDisclaimer();

	// Render the image.
	e.data.builder.render();

},
materialChange = function (e) {

	var material;

	// Capture the new material.
	material = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(material, 'material');

	// Update the interface.
	e.data.builder.checkControls(['scheme','layout','option','color','artwork','text','textarea','textselect']);
	e.data.builder.rebuildControls(['scheme','layout','option','color','artwork','text','fontsize']);
	e.data.builder.updateSelections(['material','scheme','layout','option','color','artwork','text','textarea','textselect','fontfamily','fontsize','fontalignment']);
	e.data.builder.updatePricing(true);
	e.data.builder.updateProductDetails();
	e.data.builder.updateOptionDisclaimer();

	// Render the image.
	e.data.builder.render();

},
schemeChange = function (e) {

	var scheme;

	// Capture the new scheme.
	scheme = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(scheme, 'scheme');

	// Update the interface.
	e.data.builder.checkControls(['layout','option','color','artwork','text','textarea','textselect']);
	e.data.builder.rebuildControls(['layout','option','color','artwork','text','fontsize']);
	e.data.builder.updateSelections(['scheme','layout','option','color','artwork','text','textarea','textselect','fontfamily','fontsize','fontalignment']);
	e.data.builder.updatePricing(true);
	e.data.builder.updateProductDetails();
	e.data.builder.updateOptionDisclaimer();

	// Render the image.
	e.data.builder.render();

},
layoutChange = function (e) {

	var layout;

	// Capture the new layout.
	layout = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(layout, 'layout');

	// Update the interface.
	e.data.builder.checkControls(['color','artwork','text','textarea','textselect']);
	e.data.builder.rebuildControls(['artwork','text','fontsize']);
	e.data.builder.updateSelections(['layout','artwork','text','textarea','textselect','fontfamily','fontsize','fontalignment']);

	// Render the image.
	e.data.builder.render();

},
optionChange = function (e) {

	var option, optionval;

	// Determine which option is being changed.
	option = $(this).closest('fieldset.option').attr('data-controltarget');

	// Capture the new option value.
	optionval = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(optionval, 'option', option);

	// Update the interface.
	e.data.builder.updateSelection('option', option);
	e.data.builder.updateOptionPricing(option);
	e.data.builder.updatePriceGrid();
	e.data.builder.updateOptionDisclaimer();
	e.data.builder.updateProductDetails();

	// Render this option within the image.
	e.data.builder.render('option', option);

},
colorChange = function (e) {

	var schemecolor, color, elementdata;

	// Determine which element is being changed.
	schemecolor = $(this).closest('fieldset.color').attr('data-controltarget');

	// Capture the new color.
	color = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(color, 'color', schemecolor);

	// Update the interface.
	e.data.builder.updateSelection('color', schemecolor);

	// Render every element within the image that uses this schemecolor within the image.
	for (var element in e.data.builder.config.elements) {
		if (e.data.builder.config.elements.hasOwnProperty(element)) {
			elementdata = e.data.builder.getElementAppearance(e.data.builder.settings.layout.value, e.data.builder.settings.size.value, e.data.builder.settings.scheme.value, element);
			if (elementdata && elementdata.schemecolor === schemecolor) {
				e.data.builder.render('element', element);
			}
		}
	}

},
artworkChange = function (e) {

	var control, element, artwork, custom, uploadindicator;

	// Determine which element is being changed, and what it is being changed to.
	control = $(this).closest('fieldset.artwork');
	element = $(control).attr('data-controltarget');
	artwork = $(this).val();

	// Determine whether the image is a custom upload and parse the artwork value if it is.
	uploadindicator = 'upload-';
	if (artwork.indexOf(uploadindicator) === 0) {
		custom = true;
		artwork = artwork.substring(uploadindicator.length);
	} else {
		custom = false;
	}

	// Update the settings.
	e.data.builder.updateSettings(artwork, 'artwork', element, custom);

	// Update the interface.
	e.data.builder.updateSelection('artwork', element);

	// Render this element within the image.
	e.data.builder.render('element', element);

	// Force scroll to the correct page (to address a Chrome bug).
	updateArtworkPagination($(this).closest("div.artwork-picker"), $(this).closest("div.artwork-page").index() + 1, false);

	// Scroll the sidebar.
	scrollSidebar();

},
textInput = function (e) {

	var element, text;

	// Determine which element is being changed.
	element = $(this).attr('data-controltarget');

	// Capture the new text.
	text = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(text, 'text', element);

	// Update the interface.
	e.data.builder.rebuildControl('text', element);
	e.data.builder.updateControlValueDisplay('text', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

	// Scroll the sidebar.
	scrollSidebar();

},
textareaInput = function (e) {

	var element, text;

	// Determine which element is being changed.
	element = $(this).attr('data-controltarget');

	// Capture the new text.
	text = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(text, 'text', element);

	// Update the interface.
	e.data.builder.updateControlValueDisplay('textarea', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

},
textselectChange = function (e) {

	var element, text;

	// Determine which element is being changed.
	element = $(this).closest('fieldset.textselect').attr('data-controltarget');

	// Capture the new text.
	text = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(e.data.builder.config.text[text].fulltext, 'text', element);

	// Update the interface.
	e.data.builder.updateSelection('textselect', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

},
instructionsInput = function (e) {

	var instructions;

	// Capture the new instructions.
	instructions = $(this).val();

	// Update the settings.
	e.data.builder.updateSettings(instructions, 'instructions');

	// Update the interface.
	e.data.builder.updateControlValueDisplay('instructions');

},
designserviceChange = function (e) {

	var designservice;

	// Capture the new design service choice.
	designservice = ($(this).val() === 'true');

	// Update the settings.
	e.data.builder.updateSettings(designservice, 'designservice');

	// Update the interface.
	e.data.builder.updateSelection('designservice');

},
fontfamilyChange = function (e) {

	var element;

	// Determine which element is being changed.
	element = $(this).closest('fieldset.fontfamily').attr('data-controltarget');

	// Capture the new font family and update the settings.
	e.data.builder.updateSettings($(this).val(), 'fontfamily', element);

	// Update the interface.
	e.data.builder.updateSelection('fontfamily', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

},
fontsizeChange = function (e) {

	var element;

	// Determine which element is being changed.
	element = $(this).closest('fieldset.fontsize').attr('data-controltarget');

	// Capture the new font size and update the settings.
	e.data.builder.updateSettings($(this).val(), 'fontsize', element);

	// Update the interface.
	e.data.builder.updateSelection('fontsize', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

},
fontalignmentChange = function (e) {

	var element;

	// Determine which element is being changed.
	element = $(this).closest('fieldset.fontalignment').attr('data-controltarget');

	// Capture the new font size and update the settings.
	e.data.builder.updateSettings($(this).val(), 'alignment', element);

	// Update the interface.
	e.data.builder.updateSelection('fontalignment', element);

	// Render this element within the image.
	e.data.builder.render('element', element, false);

},
quantityInput = function (e) {

	var qty, tier, prevqty, prevtier;

	// Get the old and new quantity.
	qty = determineQuantity($(this).val());
	prevqty = e.data.builder.settings.qty;

	// Get the old and new tier.
	tier = e.data.builder.getPriceTier(e.data.builder.settings.product, qty);
	prevtier = e.data.builder.getPriceTier(e.data.builder.settings.product, prevqty);

	// Update the settings.
	e.data.builder.updateSettings(qty, 'qty');

	// If the tier changed, update the price grid and size/material/scheme controls.
	if (tier !== prevtier) {
		e.data.builder.updatePriceTier();
	}

},
undoChangesClick = function (e) {

	e.data.builder.undoChanges();

},
acceptChangesClick = function (e) {

	e.data.builder.acceptChanges();

},
artworkCategoryClick = function (e) {

	var i, j, len, len2, $this, category, element, custom, controlData, artwork, currentArtwork, uploads;

	if (!$(e.target).is("div.artwork-picker, div.artwork-picker *, div.custom-artwork-controls-wrap, div.custom-artwork-controls-wrap *")) {

		$this = $(this);

		if ( !$this.hasClass("expanded") ) {

			element = $this.closest("fieldset.artwork").attr("data-controltarget");
			category = $this.attr("data-builder-artworkcategory");
			custom = category === "custom";

			currentArtwork = e.data.builder.settings.elements[element].value;
			controlData = e.data.builder.getControlData("element", element);

			// For non-custom category clicks, determine which artwork should be selected.
			if ( custom ) {

				uploads = getSortedUploads();
				artwork = ( uploads && uploads.length > 0 ) ? uploads[0].id : "uploadplaceholder";

			} else if ( controlData && controlData.categories && controlData.categories.length > 0 ) {
				categoryLoop: for ( i = 0, len = controlData.categories.length; i < len; i++ ) {
					if ( controlData.categories[i].ref === category && controlData.categories[i].artwork && controlData.categories[i].artwork.length > 0) {
						if ( $.inArray(currentArtwork, controlData.categories[i].artwork) !== -1 ) {
							artwork = currentArtwork;
							break categoryLoop;
						} else {
							for ( j = 0, len2 = controlData.categories[i].artwork.length; j < len2; j++ ) {
								if ( e.data.builder.config.artwork.hasOwnProperty(controlData.categories[i].artwork[j]) ) {
									artwork = controlData.categories[i].artwork[j];
									break categoryLoop;
								}
							}
						}
					}
				}
			}

			if ( artwork !== currentArtwork ) {

				// Update the settings.
				e.data.builder.updateSettings(artwork, "artwork", element, custom);

				// Update the interface.
				e.data.builder.updateSelection("artwork", element);

				// Render this element within the image.
				e.data.builder.render("element", element);

			} else {

				// Rebuild the artwork control.
				e.data.builder.rebuildArtworkControl(element);

			}

			// Scroll the sidebar.
			scrollSidebar();

		}

	}

},
expandingTextareaInput = function (e) {

	expandTextarea(this);
	scrollSidebar();

},
artworkPaginationClick = function (e) {

	var page;

	if ($(this).hasClass('prev-artwork')) {
		page = 'prev';
	} else if ($(this).hasClass('next-artwork')) {
		page = 'next';
	} else if ($(this).hasClass('page-number')) {
		page = $(this).text();
	}

	updateArtworkPagination($(this).closest('div.artwork-picker, div.recent-uploads'), page, true);

},
tableRowWrapClick = function (e) {

	triggerRowOption($(this).closest('tr'));

},
materialInfoClick = function (e) {

	e.stopPropagation();
	e.preventDefault();
	showDialog({
		content: $(this).attr('href'),
		ajax: true,
		width: 812,
		height: 400
	});

},
fullPricingClick = function (e) {

	e.preventDefault();

	// Extract the hash from the link. Necessary to get Fancybox and IE7 to cooperate.
	var link = $(this).attr('href');
	link = link.substring(link.lastIndexOf('#'));

	showDialog({
		content: link,
		width: 812,
		height: 600
	});

},
dialogLinkClick = function (e) {

	e.preventDefault();
	showDialog({
		width: 812,
		ajax: true,
		content: $(this).attr('href')
	});

},
windowScrollResize = function (e) {

	scrollSidebar();

},
customDropdownClick = function (e) {

	var id;

	e.stopPropagation();

	// Make sure this isn't a click on the input.
	if (!$(e.target).is('input')) {

		$(this).toggleClass('expanded');
		for (id in instances) {
			if (instances.hasOwnProperty(id) && instances[id] instanceof Builder) {
				$(instances[id].e).find('fieldset.control > div.font-controls > div.fontfamily-control, fieldset.control > div.font-controls > div.fontsize-control').not(this).removeClass('expanded');
			}
		}

	}

},
ieImgLabelClick = function (e) {

	$(this).closest('label').siblings('input').trigger('change').prop('checked', true);

},
htmlClick = function (e) {

	var id;

	for (id in instances) {
		if (instances.hasOwnProperty(id) && instances[id] instanceof Builder) {
			$(instances[id].e).find('fieldset.control > div.font-controls > div.fontfamily-control, fieldset.control > div.font-controls > div.fontsize-control').removeClass('expanded');
		}
	}

},
imageLoaded = function (e) {

	$(e.data.img).attr('src', e.data.src);

},
renderImageLoaded = function(e) {

	$(e.data.img).attr('src', e.data.src);
	e.data.builder.updateRenderProgress();

},

// Classes
Builder = function (e, mode, submiturl, id, addToCartOverrides, config, settings, designid) {

	this.e = e;
	this.mode = mode;
	this.submiturl = submiturl;
	this.id = id;
	this.addToCartOverrides = addToCartOverrides;
	this.config = config;
	this.settings = this.getSettings(settings);
	this.designid = designid;
	this.errors = {};
	this.rendering = {
		active: false,
		assets: [],
		countdown: 0
	};

},
Element = function (id, data, type, visible) {
	this.id = id;
	var inputdata = this.interpretData(data, type, visible);
	if (defined(inputdata)) {
		this.input = inputdata;
		this.edges = this.calculateEdges(inputdata);
	}
},
Upload = function (element) {

	this.domelement = element;
	this.errors = [];

};

// Public Variables
builder.version = '1.2.0';

// Builder Class
Builder.prototype.initialize = function () {

	// Clear the loading message and display the interface. Note: This must be done before preparing the interface or rendering the image.
	this.displayInterface();

	// Prepare the interface.
	this.prepareInterface();

	// Render the image.
	this.render();

};
Builder.prototype.prepareInterface = function () {

	this.prepareControls();
	this.updateProductDetails();
	this.updatePricing(true);
	this.updateOptionDisclaimer();

};
Builder.prototype.prepareControls = function () {

	this.prepareFileUploaders();
	this.checkControls();
	this.rebuildControls();
	this.updateSelections();
	this.updateQuantity();
	this.prepareControlEvents();

};
Builder.prototype.updateControls = function () {

	this.checkControls();
	this.rebuildControls();
	this.updateSelections();

};
Builder.prototype.prepareControlEvents = function () {

	var eventdata = {builder:this}, $builder = $(this.e);

	$builder.find('form.builder-submit, form.savechanges').on('submit', eventdata, submit);
	$builder.find('fieldset.size').on('change', 'input', eventdata, sizeChange);
	$builder.find('fieldset.material').on('change', 'input', eventdata, materialChange);
	$builder.find('fieldset.scheme').on('change', 'input', eventdata, schemeChange);
	$builder.find('fieldset.layout').on('change', 'input', eventdata, layoutChange);
	$builder.find('fieldset.option').on('change', 'input', eventdata, optionChange);
	$builder.find('fieldset.artwork').on('click', 'div.artwork-category, div.custom-artwork-category', eventdata, artworkCategoryClick);
	$builder.find('fieldset.artwork').on('change', 'input[type="radio"]', eventdata, artworkChange);
	$builder.find('input[type="text"].text').on('keyup', eventdata, textInput);
	$builder.find('textarea.textarea').on('keyup', eventdata, textareaInput);
	$builder.find('fieldset.textselect').on('change', 'input', eventdata, textselectChange);
	$builder.find('fieldset.color').on('change', 'input', eventdata, colorChange);
	$builder.find('textarea.instructions').on('keyup', eventdata, instructionsInput);
	$builder.find('fieldset.designservice').on('change', 'input', eventdata, designserviceChange);
	$builder.find('fieldset.fontfamily').on('change', 'input', eventdata, fontfamilyChange);
	$builder.find('fieldset.fontsize').on('change', 'input', eventdata, fontsizeChange);
	$builder.find('fieldset.fontalignment').on('change', 'input', eventdata, fontalignmentChange);
	$builder.find('input.quantity').on('keyup click', eventdata, quantityInput);
	$builder.find('fieldset.control').on('click', 'div.change-alert div.undo', eventdata, undoChangesClick);
	$builder.find('fieldset.control').on('click', 'div.change-alert div.accept', eventdata, acceptChangesClick);

};
Builder.prototype.displayInterface = function () {

	$(this.e).removeClass('loading').addClass('loaded');

};
Builder.prototype.getSettings = function (savedlayout) {

	var oldsettings, newsettings, defaultproduct, oldsize, oldsize_setbyuser, oldmaterial, oldmaterial_setbyuser, oldscheme, oldscheme_setbyuser, oldlayout, oldlayout_setbyuser,
		oldoptiondata, schemecolors, oldcolordata, oldelementdata, oldqty, oldinstructions, olddesignservice, option, element, i;

	// Prepare the new settings object.
	newsettings = {};

	// Try to pull the old settings from the supplied saved layout or from the current settings.
	if (savedlayout) {
		oldsettings = savedlayout;
	} else if (this.settings) {
		oldsettings = this.settings;
	} else {
		oldsettings = {};
	}

	// Get the default product.
	defaultproduct = this.getProduct();

	// Extract size information from old settings, or grab it from the default product.
	if (defined(oldsettings.size)) {
		oldsize = oldsettings.size.value;
		oldsize_setbyuser = oldsettings.size.setbyuser;
	} else {
		oldsize = this.config.products[defaultproduct].size;
		oldsize_setbyuser = false;
	}

	// Extract material information from old settings, or grab it from the default product.
	if (defined(oldsettings.material)) {
		oldmaterial = oldsettings.material.value;
		oldmaterial_setbyuser = oldsettings.material.setbyuser;
	} else {
		oldmaterial = this.config.products[defaultproduct].material;
		oldmaterial_setbyuser = false;
	}

	// Extract scheme information from old settings, or grab it from the default product.
	if (defined(oldsettings.scheme)) {
		oldscheme = oldsettings.scheme.value;
		oldscheme_setbyuser = oldsettings.scheme.setbyuser;
	} else {
		oldscheme = this.config.products[defaultproduct].scheme;
		oldscheme_setbyuser = false;
	}

	// Extract layout, quantity, instructions, and design service information from old settings (if available).
	if (defined(oldsettings.layout)) { oldlayout = oldsettings.layout.value; oldlayout_setbyuser = oldsettings.layout.setbyuser; }
	if (defined(oldsettings.qty)) { oldqty = oldsettings.qty; }
	if (defined(oldsettings.instructions)) { oldinstructions = oldsettings.instructions; }
	if (defined(oldsettings.designservice)) { olddesignservice = oldsettings.designservice; }

	// Determine the new size, material, scheme, layout, product, quantity, instructions, and design service selection.
	newsettings.size = this.getSizeSettings(oldsize, oldsize_setbyuser);
	newsettings.material = this.getMaterialSettings(newsettings.size.value, oldmaterial, oldscheme, oldmaterial_setbyuser);
	newsettings.scheme = this.getSchemeSettings(newsettings.size.value, newsettings.material.value, oldscheme, oldscheme_setbyuser);
	newsettings.layout = this.getLayoutSettings(oldlayout, newsettings.size.value, newsettings.scheme.value, oldlayout_setbyuser);
	newsettings.product = this.getProduct(newsettings.size.value, newsettings.material.value, newsettings.scheme.value);
	newsettings.qty = (defined(oldqty) ? oldqty : 1);
	newsettings.instructions = (defined(oldinstructions) ? oldinstructions : '');
	newsettings.designservice = (olddesignservice === false ? false : true);

	// Determine the new options.
	newsettings.options = {};
	for (option in this.config.options) {
		if (this.config.options.hasOwnProperty(option)) {
			if (oldsettings && oldsettings.options && oldsettings.options[option]) { oldoptiondata = oldsettings.options[option]; }
			newsettings.options[option] = this.getOptionSettings(newsettings.product, oldoptiondata, option);
		}
	}

	// Determine the new colors.
	newsettings.colors = {};
	schemecolors = this.getAllSchemecolors();
	for (i = 0; i < schemecolors.length; i++) {
		if (oldsettings && oldsettings.colors && oldsettings.colors[schemecolors[i]]) { oldcolordata = oldsettings.colors[schemecolors[i]]; }
		newsettings.colors[schemecolors[i]] = this.getColorSettings(newsettings.size.value, newsettings.layout.value, newsettings.scheme.value, oldcolordata, schemecolors[i]);
	}

	// Determine the new elements.
	newsettings.elements = {};
	for (element in this.config.elements) {
		if (this.config.elements.hasOwnProperty(element)) {
			if (oldsettings && oldsettings.elements && oldsettings.elements[element]) { oldelementdata = oldsettings.elements[element]; }
			newsettings.elements[element] = this.getElementSettings(newsettings.layout.value, newsettings.size.value, newsettings.scheme.value, oldelementdata, element);
		}
	}

	return newsettings;

};

Builder.prototype.getAvailableSettings = function (options) {

	var controldata, available = [], i;

	switch (options.type) {

		case 'size':
			controldata = this.getControlData('size');
			for (i = 0; i < controldata.sizes.length; i++) {
				var thissize = controldata.sizes[i];
				if (this.config.sizes.hasOwnProperty(thissize) && this.getProduct(thissize)) {
					available[available.length] = thissize;
				}
			}
			break;

		case 'material':
			controldata = this.getControlData('material');
			for (i = 0; i < controldata.materials.length; i++) {
				var thismaterial = controldata.materials[i];
				if (this.config.materials.hasOwnProperty(thismaterial)) {
					if (typeof options.size === 'string' && !this.getProduct(options.size, thismaterial)) {
						continue;
					} else {
						available[available.length] = thismaterial;
					}
				}
			}
			break;

		case 'scheme':
			controldata = this.getControlData('scheme');
			for (i = 0; i < controldata.schemes.length; i++) {
				var thisscheme = controldata.schemes[i];
				if (this.config.schemes.hasOwnProperty(thisscheme)) {
					if ((typeof options.size === 'string' && typeof options.material === 'string' && !this.getProduct(options.size, options.material, thisscheme)) || (typeof options.size === 'string' && !this.getProduct(options.size, null, thisscheme)) || (typeof options.material === 'string' && !this.getProduct(null, options.material, thisscheme))) {
						continue;
					} else {
						available[available.length] = thisscheme;
					}
				}
			}
			break;

		case 'layout':
			controldata = this.getControlData('layout');
			for (i = 0; i < controldata.layouts.length; i++) {
				var thislayout = controldata.layouts[i];
				if (this.config.layouts.hasOwnProperty(thislayout)) {
					for (var j = 0; j < this.config.layouts[thislayout].length; j++) {
						if ((typeof options.size === 'string' && $.inArray(options.size, this.config.layouts[thislayout][j].sizes) === -1) || (typeof options.scheme === 'string' && $.inArray(options.scheme, this.config.layouts[thislayout][j].schemes) === -1)) {
							continue;
						} else {
							available[available.length] = thislayout;
							break;
						}
					}
				}
			}
			break;

		case 'option':
			if (typeof options.id === 'string' && typeof options.product === 'string' && defined(this.config.products[options.product].options) && defined(this.config.products[options.product].options[options.id])) {
				for (i in this.config.products[options.product].options[options.id]) {
					if (this.config.products[options.product].options[options.id].hasOwnProperty(i)) {
						var optionvalue = this.config.products[options.product].options[options.id][i].optionvalue;
						if (this.config.options[options.id].hasOwnProperty(optionvalue)) {
							available[available.length] = optionvalue;
						}
					}
				}
			}
			break;

		case 'color':
			if (typeof options.id === 'string' && typeof options.scheme === 'string' && defined(this.config.schemes[options.scheme].schemecolors[options.id])) {
				for (i = 0; i < this.config.schemes[options.scheme].schemecolors[options.id].colors.length; i++) {
					var colorname = this.config.schemes[options.scheme].schemecolors[options.id].colors[i];
					if (this.config.colors.hasOwnProperty(colorname)) {
						available[available.length] = colorname;
					}
				}
			}
			break;

		case 'text':
			if (typeof options.id === 'string') {
				controldata = this.getControlData('element', options.id);
				if (controldata && controldata.text) {
					for (i = 0; i < controldata.text.length; i++) {
						var textid = controldata.text[i];
						if (this.config.text.hasOwnProperty(textid)) {
							available[available.length] = textid;
						}
					}
				}
			}
			break;

		case 'font':
			if (typeof options.id === 'string') {
				controldata = this.getControlData('element', options.id);
				if (controldata && controldata.fonts) {
					for (i = 0; i < controldata.fonts.length; i++) {
						var fontname = controldata.fonts[i];
						if (this.config.fonts.hasOwnProperty(fontname)) {
							available[available.length] = fontname;
						}
					}
				}
			}
			break;

		default:
			break;

	}

	return available;

};
Builder.prototype.getSizeSettings = function (value, setbyuser) {

	var newsize, availablesizes;

	newsize = {};
	availablesizes = this.getAvailableSettings({type: 'size'});

	newsize.controlenabled = (availablesizes.length > 1);

	if ($.inArray(value, availablesizes) !== -1) {

		newsize.value = value;
		newsize.setbyuser = setbyuser;

	} else {

		newsize.value = availablesizes[0];
		newsize.setbyuser = false;

	}

	return newsize;

};
Builder.prototype.getMaterialSettings = function (size, material, scheme, setbyuser) {

	var newmaterial, availablematerials;

	newmaterial = {};
	availablematerials = this.getAvailableSettings({type: 'material', size: size});

	newmaterial.controlenabled = (availablematerials.length > 1);

	if ($.inArray(material, availablematerials) !== -1) {

		newmaterial.value = material;
		newmaterial.setbyuser = setbyuser;

	} else {

		newmaterial.value = availablematerials[0];
		newmaterial.setbyuser = false;

	}

	return newmaterial;

};
Builder.prototype.getSchemeSettings = function (size, material, scheme, setbyuser) {

	var newscheme, availableschemes;

	newscheme = {};
	availableschemes = this.getAvailableSettings({type: 'scheme', size: size, material: material});

	newscheme.controlenabled = (availableschemes.length > 1);

	if ($.inArray(scheme, availableschemes) !== -1) {

		newscheme.value = scheme;
		newscheme.setbyuser = setbyuser;

	} else {

		newscheme.value = availableschemes[0];
		newscheme.setbyuser = false;

	}

	return newscheme;

};
Builder.prototype.getLayoutSettings = function (layout, size, scheme, setbyuser) {

	var newlayout, availablelayouts;

	newlayout = {};
	availablelayouts = this.getAvailableSettings({type: 'layout', size: size, scheme: scheme});

	newlayout.controlenabled = (availablelayouts.length > 1);

	if (setbyuser && $.inArray(layout, availablelayouts) !== -1) {

		newlayout.value = layout;
		newlayout.setbyuser = setbyuser;

	} else {

		newlayout.value = (defined(size) && $.inArray(this.config.sizes[size].defaultlayout, availablelayouts) !== -1 ? this.config.sizes[size].defaultlayout : availablelayouts[0]);
		newlayout.setbyuser = false;

	}

	return newlayout;

};
Builder.prototype.getOptionSettings = function (product, oldoption, optionname) {

	var i, newoption = {}, availableoptioncontent, defaultoptioncontent, optiondata;

	// Get a list of the options available for the current product.
	availableoptioncontent = this.getAvailableSettings({type:'option',id:optionname,product:product});

	// Check the supplied product for default option content, or default to the first option available.
	if (this.config.products[product] && this.config.products[product].options && this.config.products[product].options[optionname]) {
		optiondata = this.config.products[product].options[optionname];
		for (i in optiondata) {
			if (optiondata.hasOwnProperty(i)) {
				if (optiondata[i].defaultselection === true) {
					defaultoptioncontent = i;
					break;
				}
			}
		}
	}
	defaultoptioncontent = (defaultoptioncontent && $.inArray(defaultoptioncontent, availableoptioncontent) !== -1 ? defaultoptioncontent : availableoptioncontent[0]);

	newoption.controlenabled = (availableoptioncontent.length > 1);

	if (oldoption && oldoption.setbyuser && $.inArray(oldoption.value, availableoptioncontent) !== -1) {

		newoption.value = oldoption.value;
		newoption.setbyuser = oldoption.setbyuser;

	} else {

		newoption.value = defaultoptioncontent;
		newoption.setbyuser = false;

	}

	return newoption;

};
Builder.prototype.getColorSettings = function (size, layout, scheme, oldcolor, colorname) {

	var newcolor = {},
		availablecolors;

	// Determine if the color control should be enabled, based on whether anything in the current layout will use this color.
	newcolor.controlenabled = this.colorInUse(colorname, layout, size, scheme);

	// If the control is disabled...
	if (newcolor.controlenabled === false) {

		// Leave the color content alone.
		newcolor.value = (oldcolor ? oldcolor.value : undefined);
		newcolor.setbyuser = (oldcolor ? oldcolor.setbyuser : false);

	// If the control is enabled...
	} else {

		availablecolors = this.getAvailableSettings({type:'color',id:colorname,scheme:scheme});

		newcolor.controlenabled = (availablecolors.length > 1);

		if (oldcolor && oldcolor.setbyuser && $.inArray(oldcolor.value, availablecolors) !== -1) {

			newcolor.value = oldcolor.value;
			newcolor.setbyuser = oldcolor.setbyuser;

		} else {

			newcolor.value = (defined(scheme) && defined(colorname) && defined(this.config.schemes[scheme].schemecolors[colorname]) && $.inArray(this.config.schemes[scheme].schemecolors[colorname].defaultcolor, availablecolors) !== -1 ? this.config.schemes[scheme].schemecolors[colorname].defaultcolor : availablecolors[0]);
			newcolor.setbyuser = false;

		}

	}

	return newcolor;

};
Builder.prototype.getAllSchemecolors = function () {

	var schemecolors = [];

	for (var scheme in this.config.schemes) {
		if (this.config.schemes.hasOwnProperty(scheme)) {
			for (var schemecolor in this.config.schemes[scheme].schemecolors) {
				if (this.config.schemes[scheme].schemecolors.hasOwnProperty(schemecolor)) {
					if ($.inArray(schemecolor, schemecolors) === -1) {
						schemecolors[schemecolors.length] = schemecolor;
					}
				}
			}
		}
	}

	return schemecolors;

};
Builder.prototype.getElementSettings = function (layout, size, scheme, _element, elementname) {

	var element, elementdata, elementtype, controldata, configdefault, firstartwork, allownone, allowcustom, allowedartwork, defaultartwork, defaultcustom, availablefonts, fontsize, defaultsize, minsize, spread;

	element = {};

	elementdata = this.getElementAppearance(layout, size, scheme, elementname);
	elementtype = this.config.elements[elementname].type;
	controldata = this.getControlData('element', elementname);

	// Determine if the control for this element is enabled.
	element.controlenabled = !!elementdata;

	// If the element is of the artwork type...
	if (elementtype === 'artwork') {

		// Element content.
		if (_element && _element.setbyuser) {

			element.value = _element.value;
			element.setbyuser = _element.setbyuser;
			element.custom = (_element && _element.custom === true);

		} else {

			configdefault = (elementdata && elementdata.defaultartwork ? elementdata.defaultartwork : undefined);
			firstartwork = (controldata && controldata.categories && controldata.categories[0] && controldata.categories[0].artwork && controldata.categories[0].artwork[0] ? controldata.categories[0].artwork[0] : undefined);
			allownone = (controldata.allownone === true);
			allowcustom = (controldata.allowcustom === true);
			allowedartwork = [];
			for (var i = 0; i < controldata.categories.length; i++) {
				allowedartwork = allowedartwork.concat(controldata.categories[i].artwork);
			}

			defaultcustom = false;

			if (defined(configdefault) && ($.inArray(configdefault, allowedartwork) !== -1)) {
				defaultartwork = configdefault;
			} else if (allownone) {
				defaultartwork = 'none';
			} else if (defined(firstartwork)) {
				defaultartwork = firstartwork;
			} else if (allowcustom) {
				defaultartwork = 'uploadplaceholder';
				defaultcustom = true;
			}

			element.value = defaultartwork;
			element.setbyuser = false;
			element.custom = defaultcustom;

		}

	// If the element is of the text type...
	} else {

		// Element content.
		if (_element && _element.setbyuser) {

			element.value = _element.value;
			element.setbyuser = _element.setbyuser;

		} else {

			element.value = (elementdata && elementdata.defaulttext ? elementdata.defaulttext : '');
			element.setbyuser = false;

		}

		// Element font.
		availablefonts = this.getAvailableSettings({type:'font',id:elementname});
		if (_element && _element.font && _element.font.setbyuser && $.inArray(_element.font.value, availablefonts) !== -1) {
			element.font = {
				value: _element.font.value,
				setbyuser: _element.font.setbyuser
			};
		} else {
			element.font = {
				value: (elementdata && $.inArray(elementdata.font, availablefonts) !== -1 ? elementdata.font : availablefonts[0]),
				setbyuser: false
			};
		}

		// Element font size.
		// TODO: Consolidate repetitious font sizing logic.
		if (_element && _element.fontsize && _element.fontsize.setbyuser) {
			fontsize = _element.fontsize.value;
			switch (fontsize) {
				case 'small':
					defaultsize = elementdata && elementdata.fontsize > 0 ? elementdata.fontsize : 0;
					minsize = elementdata && elementdata.minfontsize > 0 ? elementdata.minfontsize : Math.ceil(defaultsize/4);
					spread = defaultsize - minsize;
					fontsize = spread > 2 ? 'small' : 'smallest';
					break;
				case 'large':
					defaultsize = elementdata && elementdata.fontsize > 0 ? elementdata.fontsize : 0;
					minsize = elementdata && elementdata.minfontsize > 0 ? elementdata.minfontsize : Math.ceil(defaultsize/4);
					spread = defaultsize - minsize;
					fontsize = spread > 2 ? 'large' : 'largest';
					break;
				default: break;
			}
			element.fontsize = {
				value: fontsize,
				setbyuser: _element.fontsize.setbyuser
			};
		} else {
			element.fontsize = {
				value: (elementdata && elementdata.fontsize > elementdata.minfontsize ? 'auto' : undefined),
				setbyuser: false
			};
		}

		// Element alignment.
		if (_element && _element.alignment && _element.alignment.setbyuser) {
			element.alignment = {
				value: _element.alignment.value,
				setbyuser: _element.alignment.setbyuser
			};
		} else {
			element.alignment = {
				value: (elementdata && elementdata.halign ? elementdata.halign : undefined),
				setbyuser: false
			};
		}

	}

	return element;

};
Builder.prototype.getControlData = function (type, target) {
	var sections = this.config.ui;
	for (var i = 0; i < sections.length; i++) {
		var controls = sections[i].controls;
		for (var j = 0; j < controls.length; j++) {
			if ( ((type === 'size'|| type === 'layout'|| type === 'scheme'|| type === 'material' || type === 'instructions' || type === 'designservice') && controls[j].type === type ) || ( controls[j].target === target && ( controls[j].type === type || ( type === 'element' && (controls[j].type === 'artwork' || controls[j].type === 'text' || controls[j].type === 'textarea' || controls[j].type === 'textselect') ) ) ) ) {
				return controls[j];
			}
		}
	}
};

Builder.prototype.getSettingName = function (type, target) {
	var settingname;
	var controldata = this.getControlData(type, target);
	if (defined(controldata)) {
		settingname = controldata.name;
	}
	if (typeof settingname !== 'string' || settingname.length <= 0) {
		switch (type) {
			case 'size': settingname = 'Size'; break;
			case 'material': settingname = 'Material'; break;
			case 'scheme': settingname = 'Scheme'; break;
			case 'layout': settingname = 'Layout'; break;
			case 'instructions': settingname = 'Instructions'; break;
			case 'designservice': settingname = 'Design Service'; break;
			default: settingname = type; break;
		}
	}
	return settingname;
};
Builder.prototype.getProduct = function (size, material, scheme) {

	var product, p;

	// If no parameters were supplied, use the default product.
	if (!defined(size) && !defined(material) && !defined(scheme)) {

		// If the default product is defined and it exists, use that.
		if (defined(this.config.info.defaultproduct) && defined(this.config.products[this.config.info.defaultproduct])) {

			product = this.config.info.defaultproduct;

		// If the default product is not defined or doesn't exist, grab the first product.
		} else {

			for (p in this.config.products) {
				if (this.config.products.hasOwnProperty(p)) {
					product = p;
					break;
				}
			}

		}

	// If parameters were supplied, find the matching product.
	} else {

		for (p in this.config.products) {
			if (this.config.products.hasOwnProperty(p)) {
				var thisproduct = this.config.products[p];
				if ((thisproduct.size === size || !defined(size)) && (thisproduct.material === material || !defined(material)) && (thisproduct.scheme === scheme || !defined(scheme))) {
					product = p;
					break;
				}
			}
		}

	}

	return product;

};

Builder.prototype.getClosestProduct = function (size, material, scheme) {

	var product, newMaterial, newScheme;

	// Start by trying to get the exact product.
	product = this.getProduct(size, material, scheme);

	// If the exact product wasn't found...
	if (!defined(product)) {

		// ... figure out what the new scheme will be.
		newScheme = this.getSchemeSettings(size, material, this.settings.scheme.value, this.settings.scheme.setbyuser).value;

		// Then try getting the product with the new scheme.
		product = this.getProduct(size, material, newScheme);

		// If the product still wasn't found...
		if (!defined(product)) {

			// ... figure out what the new material and scheme will be.
			newMaterial = this.getMaterialSettings(size, this.settings.material.value, this.settings.scheme.value, this.settings.material.setbyuser).value;
			newScheme = this.getSchemeSettings(size, newMaterial, this.settings.scheme.value, this.settings.scheme.setbyuser).value;

			// Then try getting the product with the new material and scheme.
			product = this.getProduct(size, newMaterial, newScheme);

			// If the new product still wasn't found...
			if (!defined(product)) {

				// ... then just get the default product.
				product = this.getProduct();

			}

		}

	}

	return product;

};
Builder.prototype.getProductOptionData = function (product, optionid, optionvalue) {
	if (this.config.products[product].options && this.config.products[product].options[optionid]) {
		for (var i = 0; i < this.config.products[product].options[optionid].length; i++) {
			if (this.config.products[product].options[optionid][i].optionvalue === optionvalue) {
				return this.config.products[product].options[optionid][i];
			}
		}
	}
};
Builder.prototype.getPriceTier = function (product, qty) {

	var pricetier, pricetiers;

	if (!defined(product)) { product = this.settings.product; }

	qty = (defined(qty) ? qty : this.settings.qty);
	pricetier = 0;
	pricetiers = this.config.products[product].pricing;

	for (var i = 0; i < pricetiers.length; i++) {
		if (pricetiers[i].minqty <= qty) {
			pricetier = i;
		} else {
			break; // Stop the loop if the minqty becomes too high
		}
	}

	return pricetier;

};
Builder.prototype.getLayoutVariationNumber = function (layout, size, scheme) {

	for (var i = 0; i < this.config.layouts[layout].length; i++) {
		if ($.inArray(size, this.config.layouts[layout][i].sizes) !== -1 && $.inArray(scheme, this.config.layouts[layout][i].schemes) !== -1) {
			return i;
		}
	}

};
Builder.prototype.updateSettings = function (value, property, id, custom) {

	var recalculate = false;

	// Store the old settings in the history
	this.history = $.extend(true, {}, this.settings); // Using jQuery to do a deep copy of the settings object. It's pretty fast, though (~1ms).

	// Apply the changes requested
	switch (property) {

		case 'qty':
			this.settings.qty = value;
			break;

		case 'size':
			this.settings.size.value = value;
			this.settings.size.setbyuser = true;
			recalculate = true;
			break;

		case 'material':
			this.settings.material.value = value;
			this.settings.material.setbyuser = true;
			recalculate = true;
			break;

		case 'scheme':
			this.settings.scheme.value = value;
			this.settings.scheme.setbyuser = true;
			recalculate = true;
			break;

		case 'layout':
			this.settings.layout.value = value;
			this.settings.layout.setbyuser = true;
			recalculate = true;
			break;

		case 'color':
			this.settings.colors[id].value = value;
			this.settings.colors[id].setbyuser = true;
			break;

		case 'artwork':
			this.settings.elements[id].value = value;
			this.settings.elements[id].setbyuser = true;
			this.settings.elements[id].custom = (custom === true ? true : false);
			break;

		case 'text':
			this.settings.elements[id].value = value;
			this.settings.elements[id].setbyuser = true;
			break;

		case 'fontfamily':
			this.settings.elements[id].font.value = value;
			this.settings.elements[id].font.setbyuser = true;
			break;

		case 'fontsize':
			this.settings.elements[id].fontsize.value = value;
			this.settings.elements[id].fontsize.setbyuser = true;
			break;

		case 'alignment':
			this.settings.elements[id].alignment.value = value;
			this.settings.elements[id].alignment.setbyuser = true;
			break;

		case 'option':
			this.settings.options[id].value = value;
			this.settings.options[id].setbyuser = true;
			break;

		case 'instructions':
			this.settings.instructions = value;
			break;

		case 'designservice':
			this.settings.designservice = value;
			break;

		default: break;

	}

	// Update all settings if necessary
	if (recalculate) {
		this.settings = this.getSettings();
		this.changes = this.determineChanges(property);
	} else {
		this.changes = {};
	}
	this.updateChangeAlerts();

};

Builder.prototype.getElementAppearance = function (layout, size, scheme, element) {
	var elementappearance;
	var layoutelements = this.config.layouts[layout][this.getLayoutVariationNumber(layout, size, scheme)].appearance;
	if (defined(layoutelements)) {
		elementappearance = layoutelements[element];
	}
	return elementappearance;
};
Builder.prototype.getElementColor = function (elementid) {

	var element_appearance = this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, elementid),
		element_color;

	if (defined(element_appearance) && defined(element_appearance.schemecolor) && defined(this.settings.colors[element_appearance.schemecolor])) {
		element_color = this.settings.colors[element_appearance.schemecolor].value;
	}

	return element_color;

};
Builder.prototype.getRenderedElementText = function (elementid) {

	var content = [],
		renderedtext;

	$(this.e).find('#' + this.id + '-' + elementid + '-preview .element-content span').each(function () {
		content[content.length] = $(this).text();
	});

	renderedtext = content.join('\r');

	return renderedtext;

};
Builder.prototype.getArtworkCategory = function (element, artwork, preferredCategory) {

	var i, len, category, controlData = this.getControlData("element", element);

	if ( ( typeof artwork === "undefined" || artwork === null ) && this.settings.elements[element] ) {
		artwork = this.settings.elements[element].value;
	}

	if ( controlData && controlData.categories && controlData.categories.length > 0 ) {
		for ( i = 0, len = controlData.categories.length; i < len; i++ ) {
			if ( $.inArray(artwork, controlData.categories[i].artwork) !== -1 ) {
				if ( typeof preferredCategory !== "undefined" && preferredCategory !== null && controlData.categories[i].ref === preferredCategory ) {
					category = controlData.categories[i].ref;
					break;
				} else if ( typeof category === "undefined" || category === null ) {
					category = controlData.categories[i].ref;
				}
			}
		}
	}

	return category;

};
Builder.prototype.getColorValue = function (schemecolor) {

	var colorvalue;
	if (defined(schemecolor) && defined(this.settings.colors[schemecolor])) {
		var colorid = this.settings.colors[schemecolor].value;
		if (defined(colorid) && defined(this.config.colors[colorid])) {
			var configvalue = this.config.colors[colorid].colorvalue;
			if (typeof configvalue === 'string' && configvalue.match(/^[0-9a-fA-F]{6}$/)) {
				colorvalue = configvalue;
			}
		}
	}
	return colorvalue;

};
Builder.prototype.getColorId = function (schemecolor) {

	var colorid;
	if (defined(schemecolor) && defined(this.settings.colors[schemecolor])) {
		colorid = this.settings.colors[schemecolor].value;
	}
	return colorid;

};
Builder.prototype.colorInUse = function (schemecolor, layout, size, scheme) {

	var colorinuse = false, layoutelements, i;

	layout = defined(layout) ? layout : this.settings.layout.value;
	size = defined(size) ? size : this.settings.size.value;
	scheme = defined(scheme) ? scheme : this.settings.scheme.value;
	layoutelements = this.config.layouts[layout][this.getLayoutVariationNumber(layout, size, scheme)].appearance;

	for (i in layoutelements) {
		if (layoutelements.hasOwnProperty(i) && layoutelements[i].schemecolor === schemecolor) {
			colorinuse = true;
			break;
		}
	}

	return colorinuse;

};
Builder.prototype.getBuilderElement = function (id) {
	for (var i = 0; i < this.elements.length; i++) {
		if (this.elements[i].id === id) {
			return this.elements[i];
		}
	}
};

Builder.prototype.getBaselineOffset = function (font, fontsize, leading) {

	// Adapted from: http://bl.ocks.org/3157389, http://stackoverflow.com/a/11615439

	var $container, $small, $large, position = 0, test = 1000;

	if (defined(font) && defined(fontsize) && defined(leading)) {

		$container = $('<div class="font-' + font + '" />').css({'visibility': 'hidden', 'position': 'absolute', 'line-height': leading});
		$small = $('<span>A</span>').css({'font-size': 1, 'vertical-align': 'baseline'});
		$large = $('<span>A</span>').css({'font-size': test, 'vertical-align': 'baseline'});
		$container.append($small).append($large).appendTo(this.e);

		position = Math.round(fontsize * $small.position().top / test);

		$container.remove();

	}

	return position;

};
Builder.prototype.checkControls = function (types) {

	var allcontrols;

	allcontrols = !defined(types);

	if (typeof types === 'string') { types = [types]; }

	for (var i = 0; i < this.config.ui.length; i++) {

		for (var j = 0; j < this.config.ui[i].controls.length; j++) {

			if (allcontrols || $.inArray(this.config.ui[i].controls[j].type, types) !== -1) {
				this.checkControl(this.config.ui[i].controls[j].type, this.config.ui[i].controls[j].target);
			}

		}

	}

};
Builder.prototype.checkControl = function (type, target) {

	var enabled, control;

	enabled = false;

	switch (type) {

		case 'size':
			target = 'size';
			enabled = (this.settings.size.controlenabled === true);
		break;

		case 'material':
			target = 'material';
			enabled = (this.settings.material.controlenabled === true);
		break;

		case 'scheme':
			target = 'scheme';
			enabled = (this.settings.scheme.controlenabled === true);
		break;

		case 'layout':
			target = 'layout';
			enabled = (this.settings.layout.controlenabled === true);
		break;

		case 'option':
			enabled = (this.settings.options[target] && this.settings.options[target].controlenabled === true);
		break;

		case 'color':
			enabled = (this.settings.colors[target] && this.settings.colors[target].controlenabled === true);
		break;

		case 'element': case 'artwork': case 'text': case 'textarea': case 'textselect':
			type = 'element';
			enabled = (this.settings.elements[target] && this.settings.elements[target].controlenabled === true);
		break;

	}

	control = document.getElementById(this.id + '-' + target + '-' + type + 'control');

	if (enabled) {
		$(control).closest('fieldset.control').prop('disabled', false);
	} else {
		$(control).closest('fieldset.control').prop('disabled', true);
	}

};
Builder.prototype.rebuildControls = function (types, force) {

	var allcontrols;

	allcontrols = !defined(types);

	if (typeof types === 'string') { types = [types]; }

	for (var i = 0; i < this.config.ui.length; i++) {

		for (var j = 0; j < this.config.ui[i].controls.length; j++) {

			// Update the main control (if applicable).
			if (allcontrols || $.inArray(this.config.ui[i].controls[j].type, types) !== -1) {
				this.rebuildControl(this.config.ui[i].controls[j].type, this.config.ui[i].controls[j].target, force);
			}

			// Update the upload control (if applicable). Note that upload controls are only rebuild when explicitly called, not when updating "all controls".
			if ($.inArray('upload', types) !== -1 && this.config.ui[i].controls[j].allowcustom === true) {
				this.rebuildControl('upload', this.config.ui[i].controls[j].target, force);
			}

			// Update the font family control (if applicable).
			if ((allcontrols || $.inArray('fontfamily', types) !== -1) && (this.config.ui[i].controls[j].fonts && this.config.ui[i].controls[j].fonts.length > 0)) {
				this.rebuildControl('fontfamily', this.config.ui[i].controls[j].target, force);
			}

			// Update the font size control (if applicable).
			if ((allcontrols || $.inArray('fontsize', types) !== -1) && this.config.ui[i].controls[j].sizecontrols === true) {
				this.rebuildControl('fontsize', this.config.ui[i].controls[j].target, force);
			}

			// Update the font alignment control (if applicable).
			if ((allcontrols || $.inArray('fontalignment', types) !== -1) && this.config.ui[i].controls[j].aligncontrols === true) {
				this.rebuildControl('fontalignment', this.config.ui[i].controls[j].target, force);
			}

		}

	}

};
Builder.prototype.rebuildControl = function (type, target, force) {

	switch (type) {

		case 'material': this.rebuildMaterialControl(force); break;
		case 'scheme': this.rebuildSchemeControl(force); break;
		case 'layout': this.rebuildLayoutControl(force); break;
		case 'option': this.rebuildOptionControl(target, force); break;
		case 'color': this.rebuildColorControl(target, force); break;
		case 'upload': case 'artwork': this.rebuildArtworkControl(target, force); break;
		case 'text': this.rebuildTextControl(target, force); break;
		case 'fontsize': this.rebuildFontsizeControl(target, force); break;

	}

};
Builder.prototype.rebuildMaterialControl = function (force) {

	var inputs, materials, value;

	inputs = $('#' + this.id + '-material-materialcontrol input');

	if (inputs.length > 0) {

		materials = this.getAvailableSettings({type: 'material', size: this.settings.size.value});

		for (var i = 0; i < inputs.length; i++) {

			value = $(inputs[i]).val();

			if ($.inArray(value, materials) !== -1) {

				// Enable and show the input.
				$(inputs[i]).prop('disabled', false).closest('tr').removeClass('hidden');

			} else {

				// Disable and hide the input.
				$(inputs[i]).prop('disabled', true).closest('tr').addClass('hidden');

			}

		}

	}

};
Builder.prototype.rebuildSchemeControl = function (force) {

	var inputs, schemes, value, $input, img, filename;

	inputs = $('#' + this.id + '-scheme-schemecontrol input');

	if (inputs.length > 0) {

		schemes = this.getAvailableSettings({type: 'scheme', size: this.settings.size.value, material: this.settings.material.value});

		for (var i = 0; i < inputs.length; i++) {

			value = $(inputs[i]).val();
			$input = $(inputs[i]);
			img = $input.siblings('label').find('img')[0];

			if ($.inArray(value, schemes) !== -1) {

				// Enable and show the input.
				$input.prop('disabled', false).parent().removeClass('hidden');

				// Update the image.
				filename = this.getBackgroundFilename(this.settings.size.value, value);
				loadImage(img, filename);

			} else {

				// Disable and hide the input.
				$input.prop('disabled', true).parent().addClass('hidden');

			}

		}

	}

};
Builder.prototype.rebuildLayoutControl = function (force) {

	var inputs, layouts, value;

	inputs = $('#' + this.id + '-layout-layoutcontrol input');

	if (inputs.length > 0) {

		layouts = this.getAvailableSettings({type: 'layout', size: this.settings.size.value, scheme: this.settings.scheme.value});

		for (var i = 0; i < inputs.length; i++) {

			value = $(inputs[i]).val();

			if ($.inArray(value, layouts) !== -1) {

				// Enable and show the input.
				$(inputs[i]).prop('disabled', false).parent().removeClass('hidden');

				// Insert layout name.
				$(inputs[i]).siblings('label').find('.option-text').text(this.config.layouts[value][this.getLayoutVariationNumber(value, this.settings.size.value, this.settings.scheme.value)].name);

			} else {

				// Disable and hide the input.
				$(inputs[i]).prop('disabled', true).parent().addClass('hidden');

				// Remove layout name.
				$(inputs[i]).siblings('label').find('.option-text').text('');

			}

		}

	}

};
Builder.prototype.rebuildOptionControl = function (option, force) {

	var inputs, optionvals, value;

	inputs = $('#' + this.id + '-' + option + '-layoutcontrol input');

	if (inputs.length > 0) {

		optionvals = this.getAvailableSettings({type: 'option', id: option, product: this.settings.product});

		for (var i = 0; i < inputs.length; i++) {

			value = $(inputs[i]).val();

			if ($.inArray(value, optionvals) !== -1) {

				// Enable and show the input.
				$(inputs[i]).prop('disabled', false).parent().removeClass('hidden');

			} else {

				// Disable and hide the input.
				$(inputs[i]).prop('disabled', true).parent().addClass('hidden');

			}

		}

	}

};
Builder.prototype.rebuildColorControl = function (color, force) {

	var controlid, colors, control, controlhtml, inputid, colordata;

	controlid = this.id + '-' + color + '-colorcontrol';
	control = document.getElementById(controlid);

	if (control) {

		colors = this.getAvailableSettings({type: 'color', id: color, scheme: this.settings.scheme.value});

		if (colors.length > 0) {

			controlhtml = '';

			for (var i = 0; i < colors.length; i++) {

				inputid = controlid + '-' + colors[i];
				colordata = this.config.colors[colors[i]];

				controlhtml += '<div class="control-option-wrap"><input type="radio" name="' + htmlEncode(controlid) + '" id="' + htmlEncode(inputid) + '" value="' + htmlEncode(colors[i]) + '" /><label for="' + htmlEncode(inputid) + '"><span class="swatch" style="background: #' + htmlEncode(colordata.colorvalue) + ';" /><span class="image-caption">' + htmlEncode(truncateCaption(colordata.name, 15)) + '</span></label></div>';

			}

			$(control).html(controlhtml);

		}

	}

};
Builder.prototype.rebuildArtworkControl = function (element, force) {

	var
	i,
	j,
	len,
	len2,
	artworkCount = 0,
	pageCount = 1,
	selectedPage,
	selectedArtwork,
	options = [],
	custom,
	uploads,
	oldCategory,
	newCategory,
	pageLimit = 30,
	controlName = this.id + "-" + element + "-elementcontrol",
	controlData = this.getControlData("element", element),
	$control = $(document.getElementById(controlName)),
	$allCategories = $control.find("div.artwork-category, div.custom-artwork-category"),
	$oldCategory,
	$newCategory,
	$picker,
	$oldPages,
	$newPages,
	$page,
	$option,
	$input,
	$label,
	$pagination,
	createNewPage = function () { return $(document.createElement("div")).addClass("artwork-page"); };

	// Determine the currently-selected artwork.
	if ( this.settings.elements[element] ) {

		selectedArtwork = this.settings.elements[element].value;
		custom = this.settings.elements[element].custom;

		// Determine the old and new artwork categories.
		oldCategory = $control.find("div.artwork-category.expanded, div.custom-artwork-category.expanded").attr("data-builder-artworkcategory");
		newCategory = this.settings.elements[element].custom === true ? "custom" : this.getArtworkCategory(element, selectedArtwork, oldCategory);
		$oldCategory = $(document.getElementById(controlName + "-artworkcategory-" + oldCategory));
		$newCategory = $(document.getElementById(controlName + "-artworkcategory-" + newCategory));


		if ( oldCategory !== newCategory || force ) {

			$picker = $newCategory.children("fieldset").find("div.artwork-picker, div.recent-uploads");
			$oldPages = $oldCategory.find("div.artwork-pages-wrap-inner");
			$newPages = $newCategory.find("div.artwork-pages-wrap-inner");

			// Remove everything except the new category.
			$oldPages.empty();
			$oldCategory.find("div.artwork-pagination").remove();

			// Construct the new category inputs.
			if ( selectedArtwork !== "none" && controlData && controlData.categories ) {

				for ( i = 0, len = controlData.categories.length; i < len; i++ ) {

					if ( controlData.categories[i].ref === newCategory || ( custom && newCategory === "custom" ) ) {

						if ( custom ) {

							uploads = getSortedUploads();

							for ( j = 0, len2 = uploads.length; j < len2; j++ ) {

								options.push({
									id: uploads[j].id,
									controlid: controlName + "-upload-" + uploads[j].id,
									val: "upload-" + uploads[j].id,
									src: uploads[j].src,
									name: uploads[j].name
								});

							}

							if ( uploads.length > 0 ) {

								// Add pages to the picker if necessary.
								if ( $newPages.length < 1 ) {
									$newPages = $(document.createElement("div")).addClass("artwork-pages-wrap-inner");
									$picker.append($(document.createElement("div")).addClass("artwork-pages-wrap-outer").append($newPages));
								}

								// Show the picker.
								$picker.removeClass("empty");

							} else {

								// Hide the picker.
								$picker.addClass("empty");

							}


						} else {

							for ( j = 0, len2 = controlData.categories[i].artwork.length; j < len2; j++ ) {

								if ( this.config.artwork.hasOwnProperty(controlData.categories[i].artwork[j]) ) {

									options.push({
										id: controlData.categories[i].artwork[j],
										controlid: controlName + "-" + controlData.categories[i].artwork[j],
										val: controlData.categories[i].artwork[j],
										src: this.getArtworkFilename(controlData.categories[i].artwork[j]),
										name: this.config.artwork[controlData.categories[i].artwork[j]].name
									});

								}

							}

						}

						$page = createNewPage();
						$newPages.append($page);

						for ( j = 0, len2 = options.length; j < len2; j++ ) {

							// Create a new page if necessary.
							if ( artworkCount > 0 && ( artworkCount % pageLimit ) === 0 ) {
								$page = createNewPage();
								$newPages.append($page);
								pageCount++;
							}

							// Create the option.
							$option = $(document.createElement("div")).addClass("control-option-wrap");

							// Add the input.
							$input = $(document.createElement("input")).attr("type", "radio").attr("name", controlName).attr("id", options[j].controlid).attr("value", options[j].val);
							$option.append($input);

							// Note the selected page for pagination controls.
							if ( options[j].id === selectedArtwork ) {
								selectedPage = pageCount;
							}

							// Add the label
							$label = $(document.createElement("label")).attr("for", options[j].controlid).append(
								$(document.createElement("span")).addClass("image-wrap-outer").append(
									$(document.createElement("span")).addClass("image-wrap-inner").append(
										$(document.createElement("img")).attr("data-builder-img-src", options[j].src).attr("alt", options[j].name)
									)
								)
							).append(
								$(document.createElement("span")).addClass("image-caption").text(truncateCaption(options[j].name, 15))
							);
							$option.append($label);

							// Append the option to the page.
							$page.append($option);

							// Increment the counter.
							artworkCount++;

						}

						if ( pageCount > 1 ) {

							// Build the pagination.
							$pagination = $(document.createElement("div")).addClass("artwork-pagination");
							$pagination.append($(document.createElement("span")).addClass("prev-artwork").text("Prev"));
							for ( j = 1; j <= pageCount; j++ ) {
								$pagination.append($(document.createElement("span")).addClass("page-number").text(j));
							}
							$pagination.append($(document.createElement("span")).addClass("next-artwork").text("Next"));

							// Attach the pagination to the DOM.
							$picker.append($pagination);

						}

						break;

					}

				}

			}

			// Set only the new category to expanded.
			$newCategory.addClass("expanded");
			$allCategories.not($newCategory).removeClass("expanded");

			updateArtworkPagination($picker, selectedPage, false);

		}

	}

};
Builder.prototype.rebuildTextControl = function (element, force) {

	var
	elementData,
	valueLength,
	charCountMessage,
	$charCountDisplay,
	$input = $(document.getElementById(this.id + '-' + element + '-elementcontrol'));

	if ( $input.length > 0 ) {

		elementData = this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, element);
		$charCountDisplay = $input.siblings(".char-count");

		if ( elementData && defined(elementData.charlimit) ) {

			$input.attr("maxlength", elementData.charlimit);

			valueLength = this.settings.elements[element] ? this.settings.elements[element].value.length : 0;
			charCountMessage = getCharacterCountMessage(valueLength, elementData.charlimit);

			if ( $charCountDisplay.length > 0 ) {
				$charCountDisplay.html(charCountMessage);
			} else {
				$input.before("<p class=\"char-count\">" + charCountMessage + "</p>");
			}

			if ( valueLength > elementData.charlimit ) {
				this.setError(element, "charLimit", true);
			} else {
				this.clearError(element, "charLimit", true);
			}

		} else {

			$input.removeAttr("maxlength");
			$charCountDisplay.remove();

		}

	}

};

Builder.prototype.rebuildFontsizeControl = function (element, force) {

	var inputs, appearance, size, min, precision, value;

	inputs = $('#' + this.id + '-' + element + '-elementcontrol-fontsize input');

	if (inputs.length > 0) {

		appearance = this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, element);
		size = (appearance && appearance.fontsize > 0 ? appearance.fontsize : 0);
		min = (appearance && appearance.minfontsize > 0 ? appearance.minfontsize : 0);
		precision = ((size - min) >= 2);

		for (var i = 0; i < inputs.length; i++) {

			value = $(inputs[i]).val();

			if (precision || (value !== 'small' && value !== 'large')) {

				// Enable and show the input.
				$(inputs[i]).prop('disabled', false).parent().removeClass('hidden');

			} else {

				// Disable and hide the input.
				$(inputs[i]).prop('disabled', true).parent().addClass('hidden');

			}

		}

	}

};

Builder.prototype.updatePricing = function (options) {

	// Update the product pricing.
	this.updatePriceGrid();
	this.updateSizePricing();
	this.updateMaterialPricing();
	this.updateSchemePricing();

	// If requested, update the option pricing, too.
	if (options === true) { this.updateOptionPricing(); }

};
Builder.prototype.updatePriceTier = function () {

	// Update the product pricing.
	this.updateSizePricing();
	this.updateMaterialPricing();
	this.updateSchemePricing();

	// Highlight the active price tier.
	this.highlightPriceTier();

};

Builder.prototype.calculateOptionPricing = function () {

	var
	optionpricing = 0,
	options = this.config.products[this.settings.product].options,
	option,
	i;

	if (defined(options)) {
		for (option in options) {
			if (options.hasOwnProperty(option) && defined(this.settings.options[option]) && this.settings.options[option].controlenabled) {
				for (i = 0; i < options[option].length; i++) {
					if (options[option][i].optionvalue === this.settings.options[option].value) {
						optionpricing += options[option][i].price;
						break;
					}
				}
			}
		}
	}

	return optionpricing;

};
Builder.prototype.updatePriceGrid = function () {

	var table, pricetiers, optionpricing, baseprice, tierprice, savings, i;

	// Get the pricing information for the current product.
	pricetiers = this.config.products[this.settings.product].pricing;

	// Calculate the total of all the selected options.
	optionpricing = this.calculateOptionPricing();

	// Add the option pricing to the first tier price to get the base price.
	baseprice = pricetiers[0].price + optionpricing;

	table = '<thead><th class="qty"><abbr title="Quantity">Qty</abbr></th><th class="price">Each</th><th class="savings">Savings</th></thead><tbody>';

	for (i = 0; i < pricetiers.length; i++) {

		// Calculate the price and savings.
		tierprice = pricetiers[i].price + optionpricing;
		savings = Math.round((1 - (tierprice / baseprice)) * 100);

		table += '<tr><td class="qty">' + htmlEncode(pricetiers[i].minqty) + '</td><td class="price">' + formatPrice(tierprice, '$') + '</td><td class="savings">' + (savings > 0 ? (savings + '%') : '—') + '</td></tr>';

	}

	table += '</tbody>';

	// Update the pricing table.
	$(this.e).find('.pricing-table table').html(table);

	// Highlight the active price tier.
	this.highlightPriceTier();

};

Builder.prototype.updateSizePricing = function () {

	var inputs, baseprice, size, product, price;

	inputs = $('#' + this.id + '-size-sizecontrol input');
	baseprice = this.config.products[this.settings.product].pricing[this.getPriceTier()].price;

	for (var i = 0; i < inputs.length; i++) {

		size = $(inputs[i]).val();
		product = this.getClosestProduct(size, this.settings.material.value, this.settings.scheme.value);
		price = this.config.products[product].pricing[this.getPriceTier(product)].price;

		$(inputs[i]).siblings('label').find('.price-difference').text(generatePriceDiffText(baseprice, price));

	}

};
Builder.prototype.updateMaterialPricing = function () {

	var inputs, baseprice, material, product, price;

	inputs = $('#' + this.id + '-material-materialcontrol input');
	baseprice = this.config.products[this.settings.product].pricing[this.getPriceTier()].price;

	for (var i = 0; i < inputs.length; i++) {

		material = $(inputs[i]).val();
		product = this.getClosestProduct(this.settings.size.value, material, this.settings.scheme.value);
		price = this.config.products[product].pricing[this.getPriceTier(product)].price;

		$(inputs[i]).siblings('label').find('.price-difference').text(generatePriceDiffText(baseprice, price));

	}

};
Builder.prototype.updateSchemePricing = function () {

	var inputs, baseprice, scheme, product, price;

	inputs = $('#' + this.id + '-scheme-schemecontrol input');
	baseprice = this.config.products[this.settings.product].pricing[this.getPriceTier()].price;

	for (var i = 0; i < inputs.length; i++) {

		scheme = $(inputs[i]).val();
		product = this.getClosestProduct(this.settings.size.value, this.settings.material.value, scheme);
		price = this.config.products[product].pricing[this.getPriceTier(product)].price;

		$(inputs[i]).siblings('label').find('.price-difference').text(generatePriceDiffText(baseprice, price));

	}

};
Builder.prototype.updateOptionPricing = function (option) {

	var options, inputs, basedata, baseprice, optionval, data, price;

	options = [];

	if (option) {

		// If an argument was supplied, just update that option.
		options[options.length] = option;

	} else {

		// If an argument wasn't supplied, update all options that have controls.
		for (var id in this.config.options) {
			if (this.config.options.hasOwnProperty(id)) {
				if (this.getControlData('option', id)) { options[options.length] = id; }
			}
		}

	}

	for (var i = 0; i < options.length; i++) {

		inputs = $('#' + this.id + '-' + options[i] + '-optioncontrol input');
		basedata = this.getProductOptionData(this.settings.product, option, this.settings.options[options[i]].value);
		baseprice = (basedata && defined(basedata.price) ? basedata.price : 0);

		for (var j = 0; j < inputs.length; j++) {

			optionval = $(inputs[j]).val();
			data = this.getProductOptionData(this.settings.product, options[i], optionval);
			price = (data && defined(data.price) ? data.price : 0);

			$(inputs[j]).siblings('label').find('.price-difference').text(generatePriceDiffText(baseprice, price));

		}

	}

};
Builder.prototype.updateSelections = function (types) {

	var allcontrols;

	allcontrols = !defined(types);

	if (typeof types === 'string') { types = [types]; }

	for (var i = 0; i < this.config.ui.length; i++) {

		for (var j = 0; j < this.config.ui[i].controls.length; j++) {

			// Update the main control (if applicable).
			if (allcontrols || $.inArray(this.config.ui[i].controls[j].type, types) !== -1) {
				this.updateSelection(this.config.ui[i].controls[j].type, this.config.ui[i].controls[j].target);
			}

			// Update the font family control (if applicable).
			if ((allcontrols || $.inArray('fontfamily', types) !== -1) && (this.config.ui[i].controls[j].fonts && this.config.ui[i].controls[j].fonts.length > 0)) {
				this.updateSelection('fontfamily', this.config.ui[i].controls[j].target);
			}

			// Update the font size control (if applicable).
			if ((allcontrols || $.inArray('fontsize', types) !== -1) && this.config.ui[i].controls[j].sizecontrols === true) {
				this.updateSelection('fontsize', this.config.ui[i].controls[j].target);
			}

			// Update the font alignment control (if applicable).
			if ((allcontrols || $.inArray('fontalignment', types) !== -1) && this.config.ui[i].controls[j].aligncontrols === true) {
				this.updateSelection('fontalignment', this.config.ui[i].controls[j].target);
			}

		}

	}

};
Builder.prototype.updateSelection = function (type, target) {

	var targettype = type, controlsuffix = '', valuetype, value = '', tabled = false, artworkcategories = false, expandingtextarea = false, display = false, controlid, $control, $input;

	switch (type) {

		case 'size':
			target = 'size';
			valuetype = 'check';
			value = this.settings.size.value;
			display = true;
		break;

		case 'material':
			target = 'material';
			valuetype = 'check';
			value = this.settings.material.value;
			display = true;
			tabled = true;
		break;

		case 'scheme':
			target = 'scheme';
			valuetype = 'check';
			value = this.settings.scheme.value;
			display = true;
		break;

		case 'layout':
			target = 'layout';
			valuetype = 'check';
			value = this.settings.layout.value;
			display = true;
		break;

		case 'option':
			valuetype = 'check';
			if (this.settings.options[target]) { value = this.settings.options[target].value; }
			display = true;
		break;

		case 'color':
			targettype = 'color';
			valuetype = 'check';
			if (this.settings.colors[target]) { value = this.settings.colors[target].value; }
			display = true;
		break;

		case 'artwork':
			targettype = 'element';
			valuetype = 'check';
			if (this.settings.elements[target]) { value = (this.settings.elements[target].custom === true ? 'upload-' : '') + this.settings.elements[target].value; }
			display = true;
			artworkcategories = true;
		break;

		case 'textselect':
			targettype = 'element';
			valuetype = 'check';
			if (this.settings.elements[target]) { value = this.settings.elements[target].value; }
			display = true;
		break;

		case 'text':
			targettype = 'element';
			valuetype = 'text';
			if (this.settings.elements[target]) { value = this.settings.elements[target].value; }
			display = true;
		break;

		case 'textarea':
			targettype = 'element';
			valuetype = 'text';
			if (this.settings.elements[target]) { value = this.settings.elements[target].value; }
			display = true;
			expandingtextarea = true;
		break;

		case 'instructions':
			target = 'instructions';
			valuetype = 'text';
			value = this.settings.instructions;
			display = true;
			expandingtextarea = true;
		break;

		case 'designservice':
			target = 'designservice';
			valuetype = 'check';
			value = (this.settings.designservice === true ? 'true' : 'false');
			display = true;
		break;

		case 'fontfamily':
			targettype = 'element';
			controlsuffix = 'fontfamily';
			valuetype = 'check';
			if (this.settings.elements[target] && this.settings.elements[target].font) { value = this.settings.elements[target].font.value; }
		break;

		case 'fontsize':
			targettype = 'element';
			controlsuffix = 'fontsize';
			valuetype = 'check';
			if (this.settings.elements[target] && this.settings.elements[target].fontsize) { value = this.settings.elements[target].fontsize.value; }
		break;

		case 'fontalignment':
			targettype = 'element';
			controlsuffix = 'fontalignment';
			valuetype = 'check';
			if (this.settings.elements[target] && this.settings.elements[target].alignment) { value = this.settings.elements[target].alignment.value; }
		break;

	}

	controlid = this.id + '-' + target + '-' + targettype + 'control' + (controlsuffix.length > 0 ? '-' + controlsuffix : '');
	$control = $('#' + controlid);

	if (valuetype === 'check') {

		// For artwork controls, expand the correct category.
		if (artworkcategories) {
			this.rebuildArtworkControl(target);
		}

		// Check the correct input and uncheck the rest.
		$input = $('#' + controlid + '-' + value);
		$input.prop('checked', true).addClass('current-selection');
		$control.find('input').not($input).removeClass('current-selection');

		// For tabled controls, add a selected class to the correct table row.
		if (tabled) {
			$control.find('tr').removeClass('selected');
			$input.closest('tr').addClass('selected');
		}

	} else if (valuetype === 'text') {

		// Update the text value.
		$control.val(value);

		// For expanding textarea controls, also update the value of the styling <pre>.
		if (expandingtextarea) {
			$control.siblings('pre').find('span').text(value);
		}

	}

	// Update the value display, if applicable.
	if (display) { this.updateControlValueDisplay(type, target); }

};
Builder.prototype.updateControlValueDisplays = function (types) {

	var allcontrols;

	allcontrols = !defined(types);

	if (typeof types === 'string') { types = [types]; }

	for (var i = 0; i < this.config.ui.length; i++) {

		for (var j = 0; j < this.config.ui[i].controls.length; j++) {

			// Update the main control value display (if applicable).
			if (allcontrols || $.inArray(this.config.ui[i].controls[j].type, types) !== -1) {
				this.updateControlValueDisplay(this.config.ui[i].controls[j].type, this.config.ui[i].controls[j].target);
			}

		}

	}

};
Builder.prototype.updateControlValueDisplay = function (type, target) {

	var targettype, value, control, layoutvar;

	switch (type) {

		case 'size':
			targettype = 'size';
			target = 'size';
			value = this.config.sizes[this.settings.size.value].name ? this.config.sizes[this.settings.size.value].name : '';
		break;

		case 'material':
			targettype = 'material';
			target = 'material';
			value = this.config.materials[this.settings.material.value].name ? this.config.materials[this.settings.material.value].name : '';
		break;

		case 'scheme':
			targettype = 'scheme';
			target = 'scheme';
			value = this.config.schemes[this.settings.scheme.value].name ? this.config.schemes[this.settings.scheme.value].name : '';
		break;

		case 'layout':
			targettype = 'layout';
			target = 'layout';
			layoutvar = this.getLayoutVariationNumber(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value);
			value = this.config.layouts[this.settings.layout.value][layoutvar].name ? this.config.layouts[this.settings.layout.value][layoutvar].name : '';
		break;

		case 'option':
			targettype = 'option';
			if (this.config.products[this.settings.product].options && this.config.products[this.settings.product].options[target] && this.config.options[target] && this.settings.options[target] && this.config.options[target][this.settings.options[target].value]) {
				value = this.config.options[target][this.settings.options[target].value].name ? this.config.options[target][this.settings.options[target].value].name : '';
			}
		break;

		case 'color':
			targettype = 'color';
			if (this.colorInUse(target) && this.settings.colors[target] && this.config.colors[this.settings.colors[target].value]) {
				value = this.config.colors[this.settings.colors[target].value].name ? this.config.colors[this.settings.colors[target].value].name : '';
			}
		break;

		case 'artwork':
			targettype = 'element';
			if (this.settings.elements[target] && this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, target)) {
				if (this.settings.elements[target].custom === true && uploads[this.settings.elements[target].value]) {
					value = uploads[this.settings.elements[target].value].name ? uploads[this.settings.elements[target].value].name : '';
				} else if (this.settings.elements[target].custom !== true && this.config.artwork[this.settings.elements[target].value]) {
					value = this.config.artwork[this.settings.elements[target].value].name ? this.config.artwork[this.settings.elements[target].value].name : '';
				}
			}
		break;

		case 'textselect':
			targettype = 'element';
			if (this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, target)) {
				for (var text in this.config.text) {
					if (this.config.text.hasOwnProperty(text) && this.config.text[text].fulltext === (this.settings.elements[target] ? this.settings.elements[target].value : '')) {
						value = (this.config.text[text].name ? this.config.text[text].name : '');
						break;
					}
				}
			}
		break;

		case 'text':
			targettype = 'element';
			if (this.settings.elements[target] && this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, target)) {
				value = (this.settings.elements[target].value && this.settings.elements[target].value.length > 0 ? 'Custom text' : 'Blank');
			}
		break;

		case 'textarea':
			targettype = 'element';
			if (this.settings.elements[target] && this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, target)) {
				value = (this.settings.elements[target].value && this.settings.elements[target].value.length > 0 ? 'Custom text' : 'Blank');
			}
		break;

		case 'instructions':
			targettype = 'instructions';
			target = 'instructions';
			value = (this.settings.instructions && this.settings.instructions.length > 0 ? 'Instructions' : 'None');
		break;

		case 'designservice':
			targettype = 'designservice';
			target = 'designservice';
			value = (this.settings.designservice ? 'Yes' : 'No');
		break;

	}

	// Find the control.
	control = document.getElementById(this.id + '-' + target + '-' + targettype + 'control');

	// Update the value display.
	$(control).closest('.control').find('legend .control-value').text(defined(value) ? value : 'N/A');

};
Builder.prototype.updateOverflowStatus = function (target) {

	var control_el, container_el, render_el;

	// Get the necessary DOM elements.
	control_el = $(document.getElementById(this.id + '-' + target + '-elementcontrol')).closest('.control');
	container_el = document.getElementById(this.id + '-' + target + '-preview');
	render_el = $(container_el).find('.element-content div div');

	if ($(render_el).outerHeight(false) > $(container_el).innerHeight()) {

		// If the text is overflowing, set the error.
		this.setError(target, 'overflow', true, control_el);

	} else {

		// If the text is not overflowing, clear the error.
		this.clearError(target, 'overflow', true, control_el);

	}

};
Builder.prototype.highlightPriceTier = function () {

	var tier, rows;

	// Get the current tier and the pricing table rows to update.
	tier = this.getPriceTier();
	rows = $(this.e).find('.pricing-table tbody tr');

	// Update the pointer position.
	for (var i = 0; i < rows.length; i++) {
		if (i < tier) {
			$(rows[i]).addClass('lower-tier').removeClass('active-tier');
		} else if (i === tier) {
			$(rows[i]).addClass('active-tier').removeClass('lower-tier');
		} else {
			$(rows[i]).removeClass('lower-tier active-tier');
		}
	}

};
Builder.prototype.prepareFileUploaders = function () {

	var $uploaders = $(this.e).find('.fileupload'),
		i;

	for (i = 0; i < $uploaders.length; i++) {
		prepareFileUploader($uploaders[i]);
	}

};
Builder.prototype.updateOptionDisclaimer = function () {

	var options, list, disclaimer;

	// Prepare the options array.
	options = [];

	// Gather all of the option names.
	for (var option in this.settings.options) {
		if (this.settings.options.hasOwnProperty(option)) {
			options[options.length] = this.getSettingName('option', option);
		}
	}

	// If there are any options set...
	if (options.length > 0) {

		// Merge all of the option names into a string.
		switch (options.length) {

			case 1: list = options[0]; break;

			case 2: list = options[0] + ' or ' + options[1]; break;

			default:
				list = '';
				for (var i = 0; i <= (options.length - 2); i++) {
					list += options[i] + ', ';
				}
				list += 'or ' + options[options.length-1];
			break;

		}

		disclaimer = 'Pricing does not include optional ' + htmlEncode(list) + '. For total pricing of the product you’ve configured, see the grid above the Add to Cart button.';

	} else {

		disclaimer = '';

	}

	// Update the option disclaimer.
	$('#' + this.id + '-full-pricing .full-pricing-option-disclaimer').html(disclaimer);

};
Builder.prototype.updateProductDetails = function () {

	var productdetails;

	// Get the current size and material name.
	productdetails = this.config.sizes[this.settings.size.value].name + '\n' + this.config.materials[this.settings.material.value].name;

	// Add information about the different options available.
	for (var option in this.settings.options) {
		if (this.settings.options.hasOwnProperty(option) && defined(this.settings.options[option].value)) {
			productdetails += '\n' + this.config.options[option][this.settings.options[option].value].name;
		}
	}

	// Update the product details display.
	$(this.e).find('.product-info .options').html(multiLineHtmlEncode(productdetails));

};
Builder.prototype.updateQuantity = function () {

	$(document.getElementById(this.id + '-qty')).val(this.settings.qty);

};
Builder.prototype.render = function (type, target, load) {

	var i;

	// If the image isn't already being rendered...
	if (!this.rendering.active && load !== false) {

		// Add a class to show users that the rendered image is still being loaded.
		$(this.e).children('.sidebar').children('.preview-sign').addClass('rendering');

		// Set a property explaining that the image is being rendered.
		this.rendering.active = true;

	}

	// Prepare an array for the required assets.
	this.rendering.assets = [];

	switch (type) {

		case 'element': this.renderElement(target); break;
		case 'option': this.renderOption(target); break;
		default: this.renderBackground(); this.renderElements(); this.renderOptions(); break;

	}

	if (this.rendering.assets.length > 0) {

		// Prepare a countdown for the assets that must be loaded (in case another render was requested before the previous one finished, add the existing countdown value if it exists).
		this.rendering.countdown += this.rendering.assets.length;

		for (i = 0; i < this.rendering.assets.length; i++) {

			// Download the asset.
			downloadImage(this.rendering.assets[i].src, {builder: this, img: this.rendering.assets[i].img, src: this.rendering.assets[i].src}, renderImageLoaded);

		}

	} else if (this.rendering.countdown === 0) {

		this.displayRenderedImage();

	}

	// Empty the assets array to prevent double-loading.
	this.rendering.assets = [];

};
Builder.prototype.updateRenderProgress = function () {

	// Decrease the render countdown by one.
	this.rendering.countdown--;

	// If every render asset has been loaded, display the rendered image.
	if (this.rendering.countdown === 0) {

		this.displayRenderedImage();

	}

};
Builder.prototype.displayRenderedImage = function () {

	// Remove the loading class so the image is properly displayed.
	$(this.e).children('.sidebar').children('.preview-sign').removeClass('rendering');

	// Reset the property explaining that the image is done rendering.
	this.rendering.active = false;

};
Builder.prototype.renderBackground = function () {

	var $display = $(this.e).children('.sidebar').children('.preview-sign'),
		$img = $display.find('img.background-preview'),
		src = this.getBackgroundFilename(this.settings.size.value, this.settings.scheme.value);

	// Update the dimensions of the preview image.
	$display.css({
		width: this.config.sizes[this.settings.size.value].w,
		height: this.config.sizes[this.settings.size.value].h
	});

	// Prepare the background image element.
	$img.attr({
		'data-builder-img-src': src,
		alt: this.config.info.productname
	});

	// Load the background image.
	this.rendering.assets[this.rendering.assets.length] = {img: $img[0], src: src};

};
Builder.prototype.renderOptions = function () {
	for (var i in this.config.options) {
		if (this.config.options.hasOwnProperty(i)) {
			this.renderOption(i);
		}
	}
};
Builder.prototype.renderOption = function (option) {

	var optionvalue = this.settings.options[option].value,
		$optionpreview = $('#' + this.id + '-' + option + '-optionpreview'),
		$img = $optionpreview.find('img'),
		filename,
		name;

	// Check if the option is available.
	if (this.config.options[option][optionvalue] && this.config.options[option][optionvalue].display === true) {
		filename = this.getOptionFilename(option, optionvalue, this.settings.size.value);
		name = this.getSettingName('option', option);
	}

	// Display or hide the option.
	$optionpreview.css({
		display: (defined(filename) ? 'block' : 'none')
	});

	// Prepare the option image element.
	$img.attr({
		'data-builder-img-src': (defined(filename) ? filename : ''),
		alt: name
	});

	// Load the option image (if there is one).
	if (defined(filename)) { this.rendering.assets[this.rendering.assets.length] = {img: $img[0], src: filename}; }

};
Builder.prototype.renderElements = function () {

	// Prepare the elements array.
	this.prepareElements();

	// Render each element.
	for (var i in this.config.elements) {
		if (this.config.elements.hasOwnProperty(i)) {
			this.renderElement(i);
		}
	}

};
Builder.prototype.renderElement = function (id) {

	var element = this.getBuilderElement(id),
		settings = this.settings.elements[element.id],
		type = this.config.elements[element.id].type,
		content = settings.value,
		contentvisible = ((settings.controlenabled === true) && this.checkIfContentIsVisible(type, content)),
		previewelement, appearance = {}, doublepadding, newwidth, newheight, allalignmentclasses,
		newalignmentclasses, artworkid, custom, artworkfilename, artworkalttext, artworkdimensions, thisartwork,
		artworkcolor, elementsettings, fontids, i, allfontclasses, newfontclass, $img;

	if (contentvisible !== element.input.inlayout) {

		this.renderElements();

	} else {

		// Retrieve the preview DOM Element.
		previewelement = document.getElementById(this.id + '-' + element.id + '-preview');

		// Determine the element appearance.
		appearance.display = (element.input.inlayout ? 'block' : 'none');
		appearance.top = element.edges.top.value;
		appearance.left = element.edges.left.value;
		appearance.width = (element.edges.right.value - element.edges.left.value);
		appearance.height = (element.edges.bottom.value - element.edges.top.value);
		appearance.padding = element.input.padding;
		appearance.halign = (defined(settings.alignment) ? settings.alignment.value : element.input.halign);
		appearance.valign = element.input.valign;
		appearance.color = this.getColorValue(element.input.schemecolor);

		// If there is padding, adjust the width and height as necessary.
		if (appearance.padding > 0) {

			doublepadding = appearance.padding * 2;

			newwidth = appearance.width - doublepadding;
			appearance.width = (newwidth >= 0 ? newwidth : 0);

			newheight = appearance.height - doublepadding;
			appearance.height = (newheight >= 0 ? newheight : 0);

		}

		// If the width or height is zero, don't bother rendering the element at all.
		if (appearance.width <= 0 || appearance.height <= 0) {
			appearance.display = 'none';
			appearance.top = 0;
			appearance.left = 0;
			appearance.width = 0;
			appearance.height = 0;
			appearance.padding = 0;
		}

		if (type === 'artwork') {

			appearance.defaultartwork = ((element.input.defaultartwork === 'none' || defined(this.config.artwork[element.input.defaultartwork])) ? element.input.defaultartwork : 'none');

		} else if (type === 'text') {

			appearance.font = (defined(this.config.fonts[element.input.font]) ? element.input.font : undefined);
			appearance.fontsize = element.input.fontsize;
			appearance.minfontsize = element.input.minfontsize;
			appearance.leading = element.input.leading;
			appearance.charlimit = element.input.charlimit;
			appearance.defaulttext = element.input.defaulttext;

		}

		// Prepare the alignment class names.
		allalignmentclasses = 'halign-center halign-left halign-right valign-middle valign-top valign-bottom';
		newalignmentclasses = 'halign-' + appearance.halign + ' ' + 'valign-' + appearance.valign;

		// Render the element's appearance.
		$(previewelement).css({
			display: appearance.display,
			top: appearance.top,
			left: appearance.left,
			width: appearance.width,
			height: appearance.height,
			padding: appearance.padding,
			color: (defined(appearance.color) ? '#' + appearance.color : undefined)
		}).removeClass(allalignmentclasses).addClass(newalignmentclasses);

		// If the element is in the layout, render the element's content.
		if (element.input.inlayout) {

			if (type === 'artwork') {

				artworkid = this.settings.elements[element.id].value;
				custom = this.settings.elements[element.id].custom;

				artworkfilename = '';
				artworkalttext = '';
				artworkdimensions = {
					width: 0,
					height: 0
				};

				if (artworkid !== 'none') {

					if (custom === true) {

						// If the artwork is custom, but nothing has yet been uploaded...
						if (artworkid === 'uploadplaceholder') {

							// Set the filename to the placeholder.
							artworkfilename = baseurl + images.customartworkplaceholder;

							// Set the alt text to the placeholder.
							artworkalttext = 'Your Custom Image Here';

							// Calculate the dimensions of the placeholder.
							artworkdimensions = resizeToFit(260, 260, appearance.width, appearance.height);

						} else {

							// Find the data.
							if (uploads.hasOwnProperty(artworkid)) {

								// Get the filename.
								artworkfilename = uploads[artworkid].src;

								// Get the alt text.
								artworkalttext = uploads[artworkid].name;

								// Calculate the dimensions.
								artworkdimensions = resizeToFit(uploads[artworkid].w, uploads[artworkid].h, appearance.width, appearance.height);

							}

						}

					} else if (defined(this.config.artwork[artworkid])) {

						// Find the data.
						thisartwork = this.config.artwork[artworkid];

						// Get the filename.
						artworkcolor = this.getColorId(element.input.schemecolor);
						artworkfilename = this.getArtworkFilename(artworkid, artworkcolor);

						// Get the alt text.
						artworkalttext = thisartwork.name;

						// Calculate the dimensions.
						artworkdimensions = resizeToFit(thisartwork.w, thisartwork.h, appearance.width, appearance.height);

					}

				}

				$img = $(previewelement).find('.element-content img');

				// Prepare the artwork image.
				$img.attr({
					'data-builder-img-src': artworkfilename,
					alt: artworkalttext
				}).css({
					width: artworkdimensions.width,
					height: artworkdimensions.height
				});

				// Load the artwork image.
				this.rendering.assets[this.rendering.assets.length] = {img: $img[0], src: artworkfilename};

			} else if (type === 'text') {

				// Get the element settings.
				elementsettings = this.settings.elements[element.id];

				// Set the font
				fontids = [];
				for (i in this.config.fonts) {
					if (this.config.fonts.hasOwnProperty(i)) {
						fontids[fontids.length] = 'font-' + i;
					}
				}
				allfontclasses = fontids.join(' ');
				newfontclass = ((elementsettings.font && elementsettings.font.value) ? 'font-' + elementsettings.font.value : '');
				$(previewelement).removeClass(allfontclasses).addClass(newfontclass);

				// Compose the text
				if (appearance.fontsize > 0) {
					this.renderElementText(element, appearance, elementsettings, previewelement);
				} else {
					$(previewelement).css({
						fontSize: appearance.fontsize,
						lineHeight: (Math.round((appearance.leading / 100) * appearance.fontsize) + 'px')
					}).find('.element-content div div').html('');
				}

			}

		}

	}

};
Builder.prototype.renderElementText = function (element, appearance, elementsettings, previewelement) {

	var textsettings = {};

	// Find the necessary DOM elements
	textsettings.renderedelement = previewelement;
	textsettings.contentelement = $(previewelement).find('.element-content div div');
	textsettings.proxyelement = $(previewelement).find('.element-content-proxy span');

	// Determine the maximum width and height
	textsettings.maxheight = appearance.height;
	textsettings.maxwidth = Math.round(appearance.width * 0.9); // Using 90% of actual width to allow for different text rendering engines

	// Determine the text content to render
	var textcontent = elementsettings.value;
	if (appearance.charlimit > 0) {
		textcontent = textcontent.substr(0, appearance.charlimit);
	}
	textsettings.text = textcontent.split(/\r\n|\r|\n/);
	for (var i = 0; i < textsettings.text.length; i++) {
		var words;
		if (textsettings.text[i] === '') {
			words = ['\u00A0'];
		} else {
			words = textsettings.text[i].replace(/ /g,'\u00A0').replace(/\u00A0([^\u00A0])/g,' $1').split(' '); // Convert 2+ spaces to non-breaking, then split the line into an array of words
		}
		textsettings.text[i] = words;
	}

	// Determine the default, minimum, and maximum font sizes
	textsettings.oldfontsize = parseInt($(textsettings.renderedelement).css('font-size'), 10);
	textsettings.defaultfontsize = appearance.fontsize;
	textsettings.minfontsize = appearance.minfontsize > 0 ? appearance.minfontsize : Math.ceil(textsettings.defaultfontsize/4);
	textsettings.maxfontsize = textsettings.defaultfontsize + textsettings.defaultfontsize - textsettings.minfontsize;

	// Determine the leading
	textsettings.leading = (appearance.leading / 100);

	// If this element needs to be autosized...
	if (elementsettings.fontsize.value === 'auto') {

		// Set the autosize flag to true
		textsettings.autosize = true;

		// Set the current font size to the default, or the closest allowed value to the old font size (if available).
		if (typeof textsettings.oldfontsize === 'number') {
			if (textsettings.oldfontsize > textsettings.maxfontsize) {
				textsettings.currentfontsize = textsettings.maxfontsize;
			} else if (textsettings.oldfontsize < textsettings.minfontsize) {
				textsettings.currentfontsize = textsettings.minfontsize;
			} else {
				textsettings.currentfontsize = textsettings.oldfontsize;
			}
		} else {
			textsettings.currentfontsize = textsettings.defaultfontsize;
		}

	// If this element is manually sized...
	} else {

		switch (elementsettings.fontsize.value) {
			case 'smallest': textsettings.currentfontsize = textsettings.minfontsize; break;
			case 'small': textsettings.currentfontsize = Math.round((textsettings.defaultfontsize + textsettings.minfontsize)/2); break;
			case 'medium': textsettings.currentfontsize = textsettings.defaultfontsize; break;
			case 'large': textsettings.currentfontsize = Math.round((textsettings.defaultfontsize + textsettings.maxfontsize)/2); break;
			case 'largest': textsettings.currentfontsize = textsettings.maxfontsize; break;
			default: textsettings.currentfontsize = textsettings.defaultfontsize; break;
		}

	}

	var composedtext = this.composeText(textsettings);

	$(textsettings.contentelement).html(composedtext.html);
	$(textsettings.renderedelement).css({
		fontSize: composedtext.fontsize,
		lineHeight: (Math.round((appearance.leading / 100) * composedtext.fontsize) + 'px')
	});

	// Set or clear error as necessary
	this.updateOverflowStatus(element.id);

};
Builder.prototype.composeText = function (textsettings) {

	// Set the font size for the proxy.
	$(textsettings.proxyelement).css({
		'font-size': textsettings.currentfontsize
	});

	// Start with an empty line set.
	var renderedlines = [];

	// Set a word-break variable so font size can be decreased if a single word doesn't fit on a line.
	var wordbreak = false;

	// For each user-defined line...
	for (var i = 0; i < textsettings.text.length; i++) {

		// Start a new line.
		renderedlines[renderedlines.length] = {
				words: [],
				width: 0
		};

		// For each word in this user-defined line...
		for (var j = 0; j < textsettings.text[i].length; j++) {

			// Calculate the word text and width.
			var word = {
				text: textsettings.text[i][j],
				width: $(textsettings.proxyelement).text(textsettings.text[i][j] + '\u00A0').width()
			};

			// If the word will fit on the line...
			if (renderedlines[renderedlines.length-1].width + word.width <= textsettings.maxwidth) {

				// Add the word to the line.
				renderedlines[renderedlines.length-1].words[renderedlines[renderedlines.length-1].words.length] = word.text;
				renderedlines[renderedlines.length-1].width += word.width;

			// If the word will not fit on the line...
			} else {

				// If the word will fit on its own line...
				if (word.width <= textsettings.maxwidth) {

					// Start a new line and add the word there.
					renderedlines[renderedlines.length] = {
						words: [
							word.text
						],
						width: word.width
					};

				// If the word will not fit on its own line...
				} else {

					// Update the word-break variable.
					wordbreak = true;

					// If the font size can't be reduced...
					if (textsettings.autosize !== false || textsettings.currentfontsize === textsettings.minfontsize) {

						// Split the word into characters to allow breaking into chunks across multiple lines.
						var characters = word.text.split('');

						// Calculate how much space is left on the current line.
						var availablewidth = textsettings.maxwidth - renderedlines[renderedlines.length-1].width;

						// Define the current chunk holder variable.
						var currentchunk = {
							characters: '',
							width: 0
						};

						// For each character in the word...
						for (var k = 0; k < characters.length; k++) {

							// Create a temporary chunk variable by cloning the current chunk and adding this character.
							var tempchunk = {
								characters: currentchunk.characters + characters[k],
								width: $(textsettings.proxyelement).text(currentchunk.characters + characters[k] + '\u00A0').width()
							};

							// If there is only one character in the temporary chunk or the temporary chunk will fit on the current line...
							if (tempchunk.characters.length === 1 || tempchunk.width <= availablewidth) {

								// Update the current chunk to match the temporary chunk.
								currentchunk = {
									characters: tempchunk.characters,
									width: tempchunk.width
								};

							// If the temporary chunk will not fit on the current line...
							} else {

								// Add the current chunk to the current line.
								renderedlines[renderedlines.length-1].words[renderedlines[renderedlines.length-1].words.length] = currentchunk.characters;
								renderedlines[renderedlines.length-1].width += currentchunk.width;

								// Reset the chunk and add the character to it.
								currentchunk = {
									characters: characters[k],
									width: $(textsettings.proxyelement).text(characters[k] + '\u00A0').width()
								};

								// Increment the current line.
								renderedlines[renderedlines.length] = {
									words: [],
									width: 0
								};

								// Reset the available width.
								availablewidth = textsettings.maxwidth;

							}

							// If this character is the last in the word...
							if (k === (characters.length - 1)) {

								// Add the character to the newly-created line.
								renderedlines[renderedlines.length-1].words[renderedlines[renderedlines.length-1].words.length] = currentchunk.characters;
								renderedlines[renderedlines.length-1].width += currentchunk.width;

							}

						}

					}

				}

			}

		}

	}

	// Clear the proxy and get rid previously-set font size.
	$(textsettings.proxyelement).html('').removeAttr('style');

	var results;

	// If this is an autosizing element...
	if (textsettings.autosize === true) {

		// Calculate how tall the rendered lines will be.
		var height = Math.round(textsettings.currentfontsize * textsettings.leading * renderedlines.length);

		// If the rendered lines will fit inside of the element and no words are broken...
		if (height <= textsettings.maxheight && wordbreak === false) {

			// If there was a previous attempt and that attempt failed, or if the current font size is at the maximum...
			if (textsettings.previous === false || textsettings.currentfontsize === textsettings.maxfontsize) {

				// Return the final HTML and font size
				results = {
					html: this.convertComposedTextToHtml(renderedlines),
					fontsize: textsettings.currentfontsize
				};
				return results;

			// If there was not a previous attempt or the previous attempt did not fail, and the font size is not at the maximum...
			} else {

				// Set the 'previous' flag to show that this attempt worked.
				textsettings.previous = true;

				// Increase the font size and repeat the function.
				textsettings.currentfontsize++;
				return this.composeText(textsettings);

			}

		// If the rendered lines will not fit inside of the element...
		} else {

			// If the font size is already at the minimum...
			if (textsettings.currentfontsize === textsettings.minfontsize) {

				// Return the final HTML and font size
				results = {
					html: this.convertComposedTextToHtml(renderedlines),
					fontsize: textsettings.currentfontsize
				};
				return results;

			// If the font size is not at the minimum...
			} else {

				// Set the 'previous flag to show that this attempt did not work.
				textsettings.previous = false;

				// Reduce the font size and repeat the function.
				textsettings.currentfontsize--;
				return this.composeText(textsettings);

			}

		}

	// If this is not an autosizing element...
	} else {

		// Return the final HTML and font size
		results = {
			html: this.convertComposedTextToHtml(renderedlines),
			fontsize: textsettings.currentfontsize
		};
		return results;

	}

};
Builder.prototype.convertComposedTextToHtml = function (lines) {
	var html = '';
	for (var i = 0; i < lines.length; i++) {
		var encodedtext = htmlEncode(lines[i].words.join(' '));
		html += '<span>' + encodedtext + '</span>';
	}
	return html;
};
Builder.prototype.rebuildLayout = function (data) {

	var edge, opposite, i, j, k;

	// Loop through each element
	for (i = 0; i < data.length; i++) {

		var currentelement = data[i];

		if (currentelement.input.inlayout) {

			// Loop through all of the other elements
			for (j = i + 1; j < data.length; j++) {

				var testelement = data[j];

				if (testelement.input.inlayout) {

					// If there is an overlap...
					if (currentelement.edges.top.value < testelement.edges.bottom.value && currentelement.edges.bottom.value > testelement.edges.top.value && currentelement.edges.left.value < testelement.edges.right.value && currentelement.edges.right.value > testelement.edges.left.value) {

						// Determine the order of edges to attempt changing to fix the overlap.
						var edges_highpriority = [],
							edges_lowpriority = [];

						var overlap = {};

						// For each edge...
						for (k = 0; k < edges.length; k++) {

							edge = edges[k].edge;
							opposite = edges[k].opposite;

							// If the this edge on the current element and the opposite edge of the other element are both inside of the other element...
							if (between(currentelement.edges[edge].value, testelement.edges[edge].value, testelement.edges[opposite].value) && between(testelement.edges[opposite].value, currentelement.edges[edge].value, currentelement.edges[opposite].value)) {

								// Set this edge as a higher priority.
								edges_highpriority[edges_highpriority.length] = k;

							// If the this edge on the current element and the opposite edge of the other element are not both inside of the other element...
							} else {

								// Set this edge as a lower priority.
								edges_lowpriority[edges_lowpriority.length] = k;

							}

							// Determine this edge of the overlap.
							// TODO: Optimize this code.
							var original_currentedge = currentelement.edges[edge].original;
							var original_testedge = testelement.edges[edge].original;
							var original_testopposite = testelement.edges[opposite].original;
							var new_currentedge = currentelement.edges[edge].value;
							var new_testedge = testelement.edges[edge].value;
							var new_testopposite = testelement.edges[opposite].value;

							var currentedge = (typeof original_currentedge === 'number' ? original_currentedge : new_currentedge);
							var testedge = (typeof original_testedge === 'number' ? original_testedge : new_testedge);
							var testopposite = (typeof original_testopposite === 'number' ? original_testopposite : new_testopposite);
							if (between(currentedge, testedge, testopposite)) {
								overlap[edge] = currentedge;
							} else {
								overlap[edge] = testedge;
							}

						}

						var overlaps_prioritized = edges_highpriority.concat(edges_lowpriority);

						for (k = 0; k < overlaps_prioritized.length; k++) {

							edge = edges[overlaps_prioritized[k]].edge;
							opposite = edges[overlaps_prioritized[k]].opposite;

							if (currentelement.edges[edge].collapse && testelement.edges[opposite].collapse) {

								// Both elements can collapse.
								var mid = Math.round((overlap[edge] + overlap[opposite]) / 2), // Middle of overlap
									check_current = currentelement.testAdjustment(edge, mid),  // Test the mid point in the current element
									check_test = testelement.testAdjustment(opposite, mid);    // Test the mid point in the test element

								// If the middle will work for both elements...
								if (check_current && check_test) {

									// If the middle isn't a problem for either element, set both elements to the middle.
									currentelement.collapseEdge(edge, mid);
									testelement.collapseEdge(opposite, mid);
									break;

								// If the middle is only a problem for the current element...
								} else if (!check_current && check_test) {

									// Determine the minimum value for the current element.
									var c_min = currentelement.getMinimumValue(edge);

									// Check if the current minimum will work for the test element, and use that for both if it does.
									if (testelement.testAdjustment(opposite, c_min)) {
										currentelement.collapseEdge(edge, c_min);
										testelement.collapseEdge(opposite, c_min);
										break;
									}

								// If the middle is only a problem for the test element...
								} else if (check_current && !check_test) {

									// Determine the minimum value for the current element.
									var t_min = testelement.getMinimumValue(opposite);

									// Check if the test minimum will work for the current element, and use that for both if it does.
									if (currentelement.testAdjustment(edge, t_min)) {
										currentelement.collapseEdge(edge, t_min);
										testelement.collapseEdge(opposite, t_min);
										break;
									}

								}

							} else if (currentelement.edges[edge].collapse && !testelement.edges[opposite].collapse) {

								// Only the current element collapses. Reduce it to fit (if possible).
								if (currentelement.testAdjustment(edge, testelement.edges[opposite].value)) {
									currentelement.collapseEdge(edge, testelement.edges[opposite].value);
									break;
								}

							} else if (!currentelement.edges[edge].collapse && testelement.edges[opposite].collapse) {

								// Only the test element collapses. Reduce it to fit (if possible).
								if (testelement.testAdjustment(opposite, currentelement.edges[edge].value)) {
									testelement.collapseEdge(opposite, currentelement.edges[edge].value);
									break;
								}

							}

						}

					}

				}

			}

		}

	}

	return data;

};
Builder.prototype.sortByCollapsibility = function (a, b) {

	var a_collapsew = (a.input.minw < a.input.w),
		a_collapseh = (a.input.minh < a.input.h),
		a_collapseboth = (a_collapsew && a_collapseh),
		a_collapse = (a_collapsew || a_collapseh),
		a_collapsewonly = (a_collapsew && !a_collapseh),
		a_collapsehonly = (a_collapseh && !a_collapsew),
		a_originx = a.input.x,
		a_originy = a.input.y,

		b_collapsew = (b.input.minw < b.input.w),
		b_collapseh = (b.input.minh < b.input.h),
		b_collapseboth = (b_collapsew && b_collapseh),
		b_collapse = (b_collapsew || b_collapseh),
		b_collapsewonly = (b_collapsew && !b_collapseh),
		b_collapsehonly = (b_collapseh && !b_collapsew),
		b_originx = b.input.x,
		b_originy = b.input.y;

	if (!a_collapse && b_collapse) { // A doesn't collapse at all. B does.
		return -1; // A first.
	} else if (!b_collapse && a_collapse) { // B doesn't collapse at all. A does.
		return 1; // B first.
	} else if (a_collapsewonly && b_collapseh) { // A only collapses horizontally. B collapses vertically.
		return -1; // A first.
	} else if (b_collapsewonly && a_collapseh) { // B only collapses horizontally. A collapses vertically.
		return 1;
	} else if (a_collapsehonly && b_collapseboth) { // A only collapses vertically. B collapses both vertically and horizontally.
		return -1;
	} else if (b_collapsehonly && a_collapseboth) { // B only collapses vertically. A collapses both vertically and horizontally.
		return 1;
	} else if (a_originy < b_originy) { // A's origin is closer to the top than B's.
		return -1;
	} else if (b_originy < a_originy) { // B's origin is closer to the top than A's.
		return 1;
	} else if (a_originx < b_originx) { // A's origin is closer to the left than B's.
		return -1;
	} else if (b_originx < a_originx) { // B's origin is closer to the left than A's.
		return 1;
	} else {
		return 0; // Don't sort.
	}

};
Builder.prototype.checkIfContentIsVisible = function (type, content) {

	var contentvisible = false;
	if ((typeof type === 'string' && typeof content === 'string') && ((type === 'artwork' && content !== 'none') || (type === 'text' && content.length > 0))) {
		contentvisible = true;
	}
	return contentvisible;

};
Builder.prototype.prepareElements = function () {

	var i, data, type, visible;

	this.elements = [];

	// Find all of the elements, create them, and add them to the elements array.
	for (i in this.config.elements) {

		if (this.config.elements.hasOwnProperty(i)) {

			data = this.getElementAppearance(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value, i);
			type = this.config.elements[i].type;
			visible = ((this.settings.elements[i].controlenabled === true) && this.checkIfContentIsVisible(type, this.settings.elements[i].value));

			this.elements[this.elements.length] = new Element(i, data, type, visible);

		}

	}

	// Sort the elements by collapsbility.
	this.elements.sort(this.sortByCollapsibility);

	// Rebuild the layout.
	this.elements = this.rebuildLayout(this.elements);

};
Builder.prototype.addToCart = function () {

	this.showLoadingMessage('Adding to cart');
	this.performSpellcheck();

};
Builder.prototype.prepareOutput = function () {

	// Check for errors.
	var errors = false;
	for (var i in this.errors) {
		if (this.errors.hasOwnProperty(i)) {
			errors = true;
			break;
		}
	}

	// If there are errors, show the error dialog. Otherwise, add the item to the cart.
	if (errors) {
		this.showSubmitErrorDialog();
	} else {
		this.showLoadingMessage('Adding to cart', true);
		this.submitCartData();
	}

};
Builder.prototype.submitCartData = function () {

	var ajax = {};

	// Basic Setup
	ajax.url = baseurl + this.submiturl;
	ajax.dataType = 'json';
	ajax.type = 'POST';
	ajax.error = createBoundFunction(this.showCartError, this);
	ajax.success = createBoundFunction(this.showCartUpdate, this);
	ajax.complete = createBoundFunction(this.closeAjaxRequest, this);

	// Data Output
	ajax.data = {};
	ajax.data.qty = this.settings.qty;
	ajax.data.builderref = this.id;
	ajax.data.subcategoryid = this.addToCartOverrides.subcategoryid;
	ajax.data.landingid = this.addToCartOverrides.landingid;
	ajax.data.sourceproductid = this.addToCartOverrides.sourceproductid;
	ajax.data.sourceproductrecommendationid = this.addToCartOverrides.sourceproductrecommendationid;
	ajax.data.sourceaccessoryfamilyproductid = this.addToCartOverrides.sourceaccessoryfamilyproductid;
	ajax.data.sourceinstallationaccessoryid = this.addToCartOverrides.sourceinstallationaccessoryid;
	ajax.data.sourcelandingproductid = this.addToCartOverrides.sourcelandingproductid;
	ajax.data.sourcesubcategoryproductid = this.addToCartOverrides.sourcesubcategoryproductid;
	ajax.data.designid = this.designid;
	ajax.data.producttype = producttype;
	ajax.data.editdata = JSON.stringify(this.generateEditData());
	ajax.data.renderdata = JSON.stringify(this.generateRenderData());

	switch (this.mode) {

		case 'edit': ajax.data.action = 'edit'; break;
		case 'adminedit': ajax.data.action = 'adminedit'; break;
		default: ajax.data.action = 'add'; break;
	}

	// Send the data to the cart
	this.submitAjaxRequest(ajax);

};
Builder.prototype.submitAjaxRequest = function (settings) {

	this.xhr = $.ajax(settings);

};
Builder.prototype.closeAjaxRequest = function () {

	if (defined(this.xhr)) {

		if (this.xhr.readyState < 4) {
			this.xhr.abort();
		}

		delete this.xhr;

	}

};
Builder.prototype.generateEditData = function () {

	var i, data = {};

	// Instructions
	data.instructions = {
		value: this.settings.instructions,
		display: !!this.getControlData('instructions')
	};

	// Design service
	data.designservice = {
		value: this.settings.designservice,
		display: !!this.getControlData('designservice')
	};

	// Size
	data.size = {
		value: this.settings.size.value,
		display: this.settings.size.controlenabled,
		setbyuser: this.settings.size.setbyuser
	};

	// Material
	data.material = {
		value: this.settings.material.value,
		display: this.settings.material.controlenabled,
		setbyuser: this.settings.material.setbyuser
	};

	// Scheme
	data.scheme = {
		value: this.settings.scheme.value,
		display: this.settings.scheme.controlenabled,
		setbyuser: this.settings.scheme.setbyuser
	};

	// Layout
	data.layout = {
		value: this.settings.layout.value,
		display: this.settings.layout.controlenabled,
		setbyuser: this.settings.layout.setbyuser
	};

	// Elements
	data.elements = {};
	for (i in this.settings.elements) {

		if (this.config.elements.hasOwnProperty(i)) {

			if (this.config.elements[i].type === 'artwork' && this.settings.elements[i].custom) {

				// Uploaded artwork
				data.elements[i] = {
					type: 'upload',
					value: (this.settings.elements[i].value !== 'uploadplaceholder' ? this.settings.elements[i].value : null),
					display: this.settings.elements[i].controlenabled,
					setbyuser: this.settings.elements[i].setbyuser
				};

			} else if (this.config.elements[i].type === 'artwork' && !this.settings.elements[i].custom) {

				// Stock artwork
				data.elements[i] = {
					type: 'artwork',
					value: this.settings.elements[i].value,
					color: this.getElementColor(i),
					display: this.settings.elements[i].controlenabled,
					setbyuser: this.settings.elements[i].setbyuser
				};

			} else if (this.config.elements[i].type === 'text') {

				// Text
				data.elements[i] = {
					type: 'text',
					value: this.settings.elements[i].value,
					color: this.getElementColor(i),
					display: this.settings.elements[i].controlenabled,
					setbyuser: this.settings.elements[i].setbyuser,
					font: this.settings.elements[i].font.value,
					font_setbyuser: this.settings.elements[i].font.setbyuser,
					fontsize: this.settings.elements[i].fontsize.value,
					fontsize_setbyuser: this.settings.elements[i].fontsize.setbyuser,
					textalign: this.settings.elements[i].alignment.value,
					textalign_setbyuser: this.settings.elements[i].alignment.setbyuser
				};

			}

		}

	}

	// Schemecolors
	data.schemecolors = {};
	for (i in this.settings.colors) {

		if (this.settings.colors.hasOwnProperty(i)) {

			data.schemecolors[i] = {
				value: this.settings.colors[i].value,
				display: this.settings.colors[i].controlenabled,
				setbyuser: this.settings.colors[i].setbyuser
			};

		}

	}

	// Options
	data.options = {};
	for (i in this.settings.options) {

		if (this.settings.options.hasOwnProperty(i)) {

			data.options[i] = {
				value: this.settings.options[i].value,
				display: this.settings.options[i].controlenabled,
				setbyuser: this.settings.options[i].setbyuser
			};

		}

	}

	return data;

};
Builder.prototype.generateRenderData = function () {

	var i, j, data, element, option, e, position, x, y, content, fontsize, leading, baselineoffset;

	data = {};

	// Generate the background data.
	data.background = {
		size: this.settings.size.value,
		scheme: this.settings.scheme.value,
		w: this.config.sizes[this.settings.size.value].w,
		h: this.config.sizes[this.settings.size.value].h
	};

	// Generate the element data.
	data.elements = [];
	for (i = 0; i < this.elements.length; i++) {

		element = this.elements[i].id;

		// Check that the element exists and is visible in the layout.
		if (this.config.elements[element] && this.settings.elements[element] && this.elements[i].input.inlayout === true) {

			// Artwork and uploads.
			if (this.config.elements[element].type === 'artwork') {

				// Find the rendered image.
				e = $('#' + this.id + '-' + element + '-preview .element-content img');

				// Get the position of the rendered image.
				position = $(e).position();
				x = position.left + this.elements[i].edges.left.value;
				y = position.top + this.elements[i].edges.top.value;

				// Add the image to the data.
				if (this.settings.elements[element].custom === true) {

					// Upload.
					data.elements[data.elements.length] = {
						type: 'upload',
						id: (this.settings.elements[element].value !== 'uploadplaceholder' ? this.settings.elements[element].value : undefined),
						x: x,
						y: y,
						w: $(e).width(),
						h: $(e).height()
					};

				} else {

					// Artwork.
					data.elements[data.elements.length] = {
						type: 'artwork',
						id: this.settings.elements[element].value,
						color: this.getColorId(this.elements[i].input.schemecolor),
						x: x,
						y: y,
						w: $(e).width(),
						h: $(e).height()
					};

				}

			// Text.
			} else if (this.config.elements[element].type === 'text') {

				// Find the rendered text.
				e = $('#' + this.id + '-' + element + '-preview .element-content span');

				// Get the position of the first rendered line of text.
				position = $(e).position();
				y = position.top + this.elements[i].edges.top.value;
				switch (this.settings.elements[element].alignment.value) {
					case 'left': x = position.left + this.elements[i].edges.left.value; break;
					case 'center': x = Math.round(position.left + ($(e).width() / 2) + this.elements[i].edges.left.value); break;
					case 'right': x = (position.left + $(e).width() + this.elements[i].edges.left.value); break;
				}

				// Get the the baseline offset.
				fontsize = parseInt($(e).css('font-size'), 10);
				leading = this.elements[i].input.leading / 100;
				baselineoffset = this.getBaselineOffset(this.settings.elements[element].font.value, fontsize, leading);

				// Join the rendered lines of text into a string.
				content = [];
				for (j = 0; j < e.length; j++) { content[content.length] = $(e[j]).text(); }
				content = content.join('\n');

				// Add the text to the data.
				data.elements[data.elements.length] = {
					type: 'text',
					content: content,
					color: this.getColorId(this.elements[i].input.schemecolor),
					alignment: this.settings.elements[element].alignment.value,
					font: this.settings.elements[element].font.value,
					fontsize: fontsize,
					leading: Math.round(leading * fontsize),
					x: x,
					y: y,
					baselineoffset: baselineoffset
				};

			}

		}

	}

	// Generate the option data.
	data.options = [];
	for (option in this.config.options) {

		// Add the option to the data if it exists and is visible.
		if (this.config.options.hasOwnProperty(option) && this.settings.options[option] && this.config.options[option][this.settings.options[option].value] && this.config.options[option][this.settings.options[option].value].display === true) {

			data.options[data.options.length] = {
				id: option,
				value: this.settings.options[option].value
			};

		}

	}

	return data;

};
Builder.prototype.updateChangeAlerts = function () {

	var control;

	// Clear existing change alerts.
	this.clearAlert();

	// Look for the trigger, then apply the change alert to that control.
	if (this.checkForChanges()) {

		control = document.getElementById(this.id + '-' + this.changes.trigger + '-' + this.changes.trigger + 'control');

		if (control) {

			$(control).closest('.control').addClass('alert').find('.control-description').after(this.generateChangeAlert());

		}

	}

};
Builder.prototype.acceptChanges = function () {

	this.clearAlert();

};
Builder.prototype.undoChanges = function () {

	// Get rid of the change alert.
	this.clearAlert();

	// Perform the undo.
	this.settings = $.extend(true, {}, this.history); // Using jQuery to do a deep copy of the settings object. It's pretty fast, though (~1ms).

	// Update the interface.
	this.checkControls(['material','scheme','layout','option','color','artwork','text','textarea','textselect']);
	this.rebuildControls(['material','scheme','layout','option','color','text','fontsize']);
	this.updateSelections(['size','material','scheme','layout','option','color','artwork','text','textarea','textselect','fontfamily','fontsize','fontalignment']);
	this.updatePricing(true);
	this.updateProductDetails();
	this.updateOptionDisclaimer();

	// Render the image.
	this.render();

};
Builder.prototype.determineChanges = function (trigger) {

	var changes = {}, color, element, option, change;
	if (trigger !== 'layout' && this.settings.layout.value !== this.history.layout.value) {
		changes.layout = {
			before: this.config.layouts[this.history.layout.value][this.getLayoutVariationNumber(this.history.layout.value, this.history.size.value, this.history.scheme.value)].name,
			after: this.config.layouts[this.settings.layout.value][this.getLayoutVariationNumber(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value)].name
		};
	}
	if (trigger !== 'material' && this.settings.material.value !== this.history.material.value) {
		changes.material = {
			before: this.config.materials[this.history.material.value].name,
			after: this.config.materials[this.settings.material.value].name
		};
	}
	if (trigger !== 'scheme' && this.settings.scheme.value !== this.history.scheme.value) {
		changes.scheme = {
			before: this.config.schemes[this.history.scheme.value].name,
			after: this.config.schemes[this.settings.scheme.value].name
		};
	}

	for (color in this.settings.colors) {
		if (this.settings.colors.hasOwnProperty(color)) {

			if (this.getControlData('color', color)) {

				// Enabled/Disabled
				if (this.settings.colors[color].controlenabled !== this.history.colors[color].controlenabled) {
					if (!defined(changes.color)) { changes.color = {}; }
					if (!defined(changes.color[color])) { changes.color[color] = {}; }
					changes.color[color].controlenabled = {
						before: this.history.colors[color].controlenabled,
						after: this.settings.colors[color].controlenabled
					};
				}

				if (this.settings.colors[color].controlenabled === true) {

					if (this.settings.colors[color].value !== this.history.colors[color].value && defined(this.settings.colors[color].value) && defined(this.history.colors[color].value)) { // So that change alerts don't show for any other color that isn't user-adjustable

						if (!defined(changes.color)) { changes.color = {}; }
						if (!defined(changes.color[color])) { changes.color[color] = {}; }
						changes.color[color].content = {
							before: this.config.colors[this.history.colors[color].value].name,
							after: this.config.colors[this.settings.colors[color].value].name
						};

					}

				}

			}

		}
	}

	// Elements
	for (element in this.settings.elements) {
		if (this.settings.elements.hasOwnProperty(element)) {

			var controldata = this.getControlData('element', element);
			if (defined(controldata)) {

				// Enabled/Disabled
				if (this.settings.elements[element].controlenabled !== this.history.elements[element].controlenabled) {
					if (!defined(changes.element)) { changes.element = {}; }
					if (!defined(changes.element[element])) { changes.element[element] = {}; }
					changes.element[element].controlenabled = {
						before: this.history.elements[element].controlenabled,
						after: this.settings.elements[element].controlenabled
					};
				}

				if (this.settings.elements[element].controlenabled === true && this.history.elements[element].controlenabled !== false) {

					// Content
					if (this.settings.elements[element].value !== this.history.elements[element].value && defined(this.settings.elements[element].value) && defined(this.history.elements[element].value)) {
						if (!defined(changes.element)) { changes.element = {}; }
						if (!defined(changes.element[element])) { changes.element[element] = {}; }
						if (controldata.type === 'artwork') {
							var oldvalue;
							var newvalue;
							switch (this.history.elements[element].value) {
								case 'none': oldvalue = 'None'; break;
								case 'custom': oldvalue = 'Custom'; break;
								default: oldvalue = this.config.artwork[this.history.elements[element].value].name; break;
							}
							switch (this.settings.elements[element].value) {
								case 'none': newvalue = 'None'; break;
								case 'custom': newvalue = 'Custom'; break;
								default: newvalue = this.config.artwork[this.settings.elements[element].value].name; break;
							}
							changes.element[element].content = {
								before: oldvalue,
								after: newvalue
							};
						} else {
							changes.element[element].content = true;
						}
					}

					// Font
					if (this.settings.elements[element].font && this.settings.elements[element].font.value !== this.history.elements[element].font.value && defined(this.settings.elements[element].font.value) && defined(this.history.elements[element].font.value)) {
						if (!defined(changes.element)) { changes.element = {}; }
						if (!defined(changes.element[element])) { changes.element[element] = {}; }
						changes.element[element].font = {
							before: this.config.fonts[this.history.elements[element].font.value].name,
							after: this.config.fonts[this.settings.elements[element].font.value].name
						};
					}

					// Fontsize
					if (this.settings.elements[element].fontsize && this.settings.elements[element].fontsize.value !== this.history.elements[element].fontsize.value && defined(this.settings.elements[element].fontsize.value) && defined(this.history.elements[element].fontsize.value)) {
						if (!defined(changes.element)) { changes.element = {}; }
						if (!defined(changes.element[element])) { changes.element[element] = {}; }
						changes.element[element].fontsize = {
							before: fontsizes[this.history.elements[element].fontsize.value],
							after: fontsizes[this.settings.elements[element].fontsize.value]
						};
					}

					// Alignment
					if (this.settings.elements[element].alignment && this.settings.elements[element].alignment.value !== this.history.elements[element].alignment.value && defined(this.settings.elements[element].alignment.value) && defined(this.history.elements[element].alignment.value)) {
						if (!defined(changes.element)) { changes.element = {}; }
						if (!defined(changes.element[element])) { changes.element[element] = {}; }
						changes.element[element].alignment = {
							before: alignments[this.history.elements[element].alignment.value],
							after: alignments[this.settings.elements[element].alignment.value]
						};
					}

				}

			}

		}
	}

	// Options
	for (option in this.settings.options) {
		if (this.settings.options.hasOwnProperty(option)) {

			if (this.getControlData('option', option)) {

				// Enabled/Disabled
				if (this.settings.options[option].controlenabled !== this.history.options[option].controlenabled) {
					if (!defined(changes.option)) { changes.option = {}; }
					if (!defined(changes.option[option])) { changes.option[option] = {}; }
					changes.option[option].controlenabled = {
						before: this.history.options[option].controlenabled,
						after: this.settings.options[option].controlenabled
					};
				}

				if (this.settings.options[option].controlenabled === true) {

					// Content
					if (this.settings.options[option].value !== this.history.options[option].value && defined(this.settings.options[option].value) && defined(this.history.options[option].value)) {
						if (!defined(changes.option)) { changes.option = {}; }
						if (!defined(changes.option[option])) { changes.option[option] = {}; }
						changes.option[option].content = {
							before: this.history.options[option].value,
							after: this.settings.options[option].value
						};
					}

				}

			}

		}
	}

	for (change in changes) { // Check if there were any changes...
		if (changes.hasOwnProperty(change)) {
			changes.trigger = trigger; // ...if so, define the trigger.
			break; // And don't bother redefining the trigger once a change has been found.
		}
	}

	return changes;

};
Builder.prototype.checkForChanges = function () {

	var changes;

	changes = false;

	if (this.changes) {
		for (var i in this.changes) {
			if (this.changes.hasOwnProperty(i)) {
				changes = true;
			}
		}
	}

	return changes;

};
Builder.prototype.generateChangeAlert = function () {

	var message = '';

	for (var setting in this.changes) {
		if (this.changes.hasOwnProperty(setting)) {

			if (setting === 'trigger') { continue; }

			var messagetype = 'default';
			var name = '';
			var before = '';
			var after = '';
			var label = '';

			if (defined(this.changes[setting].before)) {
				name = this.getSettingName(setting);
				before = this.changes[setting].before;
				after = this.changes[setting].after;
				message += this.generateChangeMessage(messagetype, name, before, after, label);
			} else {
				for (var i in this.changes[setting]) {
					if (this.changes[setting].hasOwnProperty(i)) {
						if (defined(this.changes[setting][i].before)) {
							name = this.getSettingName(setting, i);
							before = this.changes[setting][i].before;
							after = this.changes[setting][i].after;
							message += this.generateChangeMessage(messagetype, name, before, after, label);
						} else {
							for (var j in this.changes[setting][i]) {
								if (this.changes[setting][i].hasOwnProperty(j)) {
									name = this.getSettingName(setting, i);
									if (this.changes[setting][i][j] === true) {
										messagetype = 'short';
										message += this.generateChangeMessage(messagetype, name, before, after, label);
									} else {
										before = this.changes[setting][i][j].before;
										after = this.changes[setting][i][j].after;
										switch (j) {
											case 'controlenabled': messagetype = 'toggle'; break;
											case 'font': messagetype = 'default'; label = 'font'; break;
											case 'fontsize': messagetype = 'default'; label = 'font size'; break;
											case 'alignment': messagetype = 'default'; label = 'alignment'; break;
											default: break;
										}
										message += this.generateChangeMessage(messagetype, name, before, after, label);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	var triggername = this.getSettingName(this.changes.trigger);
	var triggervalue;
	switch (this.changes.trigger) {
		case 'size':
			triggervalue = this.config.sizes[this.settings.size.value].name;
			break;
		case 'layout':
			triggervalue = this.config.layouts[this.settings.layout.value][this.getLayoutVariationNumber(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value)].name;
			break;
		case 'scheme':
			triggervalue = this.config.schemes[this.settings.scheme.value].name;
			break;
		case 'material':
			triggervalue = this.config.materials[this.settings.material.value].name;
			break;
		default:
			break;
	}
	return '<div id="' + htmlEncode(this.id) + '-change-alert" class="change-alert-wrap"><img class="change-alert-icon" src="' + htmlEncode(baseurl + images.changealerticon) + '" alt="Change Alert" /><div class="change-alert"><p>Changing <strong>' + htmlEncode(triggername) + '</strong> to <strong>' + htmlEncode(triggervalue) + '</strong> also changed the following:</p><ul>' + message + '</ul><div class="change-controls"><div class="undo">Undo</div><div class="accept">Accept</div></div></div></div>';
};
Builder.prototype.generateChangeMessage = function (messagetype, name, before, after, label) {

	var message;

	switch (messagetype) {

		case 'short':
			message = '<li><strong>' + htmlEncode(name) + '</strong> changed.</li>';
		break;

		case 'toggle':
			message = '<li><strong>' + htmlEncode(name) + '</strong> is ' + (after === true ? 'now available' : 'no longer available') + '.</li>';
		break;

		case 'default':
			message = '<li><strong>' + htmlEncode(name) + '</strong> ';
			if (label !== '') {
				message += label + ' ';
			}
			message += 'changed ';
			if (defined(before)) {
				message += 'from <strong>' + htmlEncode(before) + '</strong> ';
			}
			message += 'to <strong>' + htmlEncode(after) + '</strong>.</li>';
		break;

	}

	return message;

};
Builder.prototype.clearAlert = function () {

	var $alert = $(document.getElementById(this.id + '-change-alert'));

	$alert.closest('fieldset.control').removeClass('alert');
	$alert.remove();

};
Builder.prototype.preload = function () {

	var i, updatePreloadStatus = createBoundFunction(this.updatePreloadStatus, this);

	// Set up the preload object to monitor progress
	this.assets = {
		fonts: [],
		images: []
	};

	// Images
	for (i in images) {
		if (images.hasOwnProperty(i)) {
			this.assets.images[this.assets.images.length] = baseurl + images[i];
		}
	}

	// Fonts
	if (WebFont) {
		for (i in this.config.fonts) {
			if (this.config.fonts.hasOwnProperty(i)) {
				this.assets.fonts[this.assets.fonts.length] = i;
			}
		}
	}

	// Update the count of assets that need to be preloaded.
	this.assets.remaining = this.assets.fonts.length + this.assets.images.length;

	// Preload the fonts using Google's WebFont Loader.
	if (WebFont) {
		WebFont.load({
			custom: {
				families: this.assets.fonts,
				urls: [baseurl + fontstylesheet]
			},
			fontactive: updatePreloadStatus,
			fontinactive: updatePreloadStatus
		});
	}

	// Preload the images.
	for (i = 0; i < this.assets.images.length; i++) {

		downloadImage(this.assets.images[i], null, updatePreloadStatus);

	}

};
Builder.prototype.updatePreloadStatus = function () {

	// Decrease the completion counter.
	this.assets.remaining--;

	// If everything is done loading, initialize the Builder (but wait .25s extra to allow for font resource interpretation).
	if (this.assets.remaining === 0) {
		setTimeout(createBoundFunction(this.initialize, this), 250);
	}

};
Builder.prototype.getArtworkFilename = function (artworkid, color) {

	var artworkfilename;

	if (defined(this.config.artwork[artworkid])) {

		artworkfilename = baseurl + imageurl + 'artwork/' + (color ? color : 'default') + '/' + artworkid + '.png';

	}

	return artworkfilename;

};
Builder.prototype.getOptionFilename = function (optionid, optionvalue, sizeid) {

	return baseurl + imageurl + 'options/' + sizeid + '/' + optionid + '/' + optionvalue + '.png';

};
Builder.prototype.getBackgroundFilename = function (size, scheme) {
	return baseurl + imageurl + 'backgrounds/' + size + '/' + (scheme ? scheme : 'default') + '.png';
};
Builder.prototype.performSpellcheck = function () {

	var text = [], element;

	if (this.settings.elements) {

		// Clear old spelling errors
		this.clearAllSpellingErrors();

		// Gather the text from each element.
		for (element in this.settings.elements) {
			if (this.settings.elements.hasOwnProperty(element)) {
				if (this.config.elements[element].type === 'text') {
					text[text.length] = {
						target: element,
						content: this.settings.elements[element].value
					};
				}
			}
		}

	}

	if (text.length > 0) {

		this.submitAjaxRequest({
			url: baseurl + spellcheckurl,
			dataType: 'json',
			type: 'POST',
			data: {content: text},
			error: createBoundFunction(this.spellcheckError, this),
			success: createBoundFunction(this.parseSpellcheckResults, this)
		});

	} else {

		this.prepareOutput();

	}

};
Builder.prototype.spellcheckError = function () {

	var aborted = (this.xhr && this.xhr.statusText === 'abort');

	// Close the XHR request.
	this.closeAjaxRequest();

	// Proceed if the user didn't manually abort.
	if (!aborted) { this.prepareOutput(); }

};
Builder.prototype.parseSpellcheckResults = function (results) {

	var target;

	// Close the XHR.
	this.closeAjaxRequest();

	for (target in results) {
		if (results.hasOwnProperty(target) && results[target].length > 0) {
			this.setError(target, 'spelling', results[target]);
		}
	}

	this.prepareOutput();

};
Builder.prototype.clearAllSpellingErrors = function () {
	for (var i in this.errors) {
		if (this.errors.hasOwnProperty(i)) {
			this.clearError(i, 'spelling');
		}
	}
};
Builder.prototype.setError = function (target, errortype, errorvalue, controlelement) {

	if (!defined(this.errors[target])) {
		this.errors[target] = {};
	}

	if (!defined(this.errors[target][errortype])) {

		// Add the error.
		this.errors[target][errortype] = errorvalue;

		// If the error type is an overflow, display the overflow error notification on the control.
		if (errortype === 'overflow') {
			$(controlelement).addClass('error').find('.expanding-textarea, input.text').before('<p class="error-notification">Too much text</p>');
		}
	}

};
Builder.prototype.clearError = function (target, errortype, errorvalue, controlelement) {

	if (defined(this.errors[target]) && typeof this.errors[target][errortype] !== true) {

		// If an error value was supplied, remove that error value

		// Delete the error.
		delete this.errors[target][errortype];

		// Check if the target has any other errors.
		var othererrors = false;
		for (var i in this.errors[target]) {
			if (this.errors[target].hasOwnProperty(i) && this.errors[target][i] === true) {
				othererrors = true;
			}
		}

		// If the target has no other errors, delete the target from the error list entirely.
		if (!othererrors) {
			delete this.errors[target];
		}

		// If the error type was an overflow, remove the overflow error notification on the control.
		if (errortype === 'overflow') {
			$(controlelement).removeClass('error').find('p.error-notification').remove();
		}

	}

};
Builder.prototype.showLoadingMessage = function (message, update) {

	var loadingmsg = '', $msg;

	loadingmsg += '<div id="builder-dialog-wrapper"><div class="adding-to-cart"><p class="adding-to-cart-message">' + htmlEncode(message) + '…</p><div class="progress-bar indeterminate"><div class="progress" /></div><div class="cancel builder-button">Cancel</div></div></div>';

	if (update) {

		$msg = $(document.getElementById('builder-dialog-wrapper')).html(loadingmsg);
		$msg.find('.cancel').on('click', createBoundFunction(this.finishLoad, this));

		updateDialog();

	} else {

		$msg = $(document.createElement('div')).html(loadingmsg);
		$msg.find('.cancel').on('click', createBoundFunction(this.finishLoad, this));

		showDialog({
			content: $msg,
			modal: true,
			width: 690
		});

	}

};
Builder.prototype.finishLoad = function () {

	this.closeAjaxRequest();
	closeDialog(true);

};
Builder.prototype.showSubmitErrorDialog = function () {

	var $clone, signpreview, html, setting, $content;

	$clone = $(this.e).find('.preview-sign').clone(false);
	$clone.find('*').removeAttr('id');

	signpreview = $clone.wrap(document.createElement('div')).parent().html();

	html = '<div class="submit-error"><div class="submit-error-preview">' + signpreview + '</div><div class="submit-error-details"><p class="error-headline">Problems Detected</p><p>Review the issues below and double-check the preview of your design to confirm that it will print as you intended.</p><ul>';

	for (var i in this.errors) {

		if (this.errors.hasOwnProperty(i)) {

			setting = this.getSettingName('element', i);

			for (var j in this.errors[i]) {

				if (this.errors[i].hasOwnProperty(j)) {

					html += '<li><strong>' + setting + ':</strong> ';

					switch (j) {

						case 'overflow': case 'charLimit':
							html += 'Too much text.';
							break;

						case 'spelling':
							if (this.errors[i][j].length === 1) {
								html += 'Potential misspelling: “' + this.errors[i][j][0] + '”.';
							} else {
								html += 'Potential misspellings: “' + this.errors[i][j].join('”, “') + '”.';
							}

							break;

						default:
							break;

					}

				}

			}

		}

	}

	html += '</ul><div class="cancel builder-button">Cancel</div><div class="proceed builder-button-primary">Proceed Anyway</div></div>';

	$content = $(document.getElementById('builder-dialog-wrapper')).html(html);

	$content.find('.proceed').on('click', {builder: this}, function (e) {
		e.data.builder.showLoadingMessage('Adding to cart', true);
		e.data.builder.submitCartData();
	});

	$content.find('.cancel').on('click', function () {
		closeDialog(true);
	});

	updateDialog();

};
Builder.prototype.showCartUpdate = function (data) {

	var html = '', option, price = this.config.products[this.settings.product].pricing[this.getPriceTier()].price + this.calculateOptionPricing(), $content;

	if (data.success !== true) {

		this.showCartError(data);

	} else {

		this.designid = data.designid;

		html += '<div class="addtocart-added' + ((this.mode === 'edit' || this.mode === 'adminedit') ? ' editmode' : '') + '">';

			html += '<div class="addtocart-message"><img src="' + htmlEncode(baseurl + images.successicon) + '" alt="Success" /> ' + ( (this.mode === 'edit' || this.mode === 'adminedit') ? 'Changes Successfully Applied' : 'Item Added to Cart' ) + ': ' + htmlEncode(this.config.info.productname) + '</div>';

			html += '<div class="addtocart-message-summary">';

				html += '<div class="product-image"><img src="' + htmlEncode(data.image) + '" alt="Your ' + htmlEncode(this.config.info.productname) + '" /></div>';

				html += '<div class="product-data">';

					html += '<dl>';

						// Size
						if (this.settings.size.controlenabled) {
							html += '<dt>' + htmlEncode(this.getSettingName('size')) + '</dt><dd>' + htmlEncode(this.config.sizes[this.settings.size.value].name) + '</dd>';
						}

						// Material
						if (this.settings.material.controlenabled) {
							html += '<dt>' + htmlEncode(this.getSettingName('material')) + '</dt><dd>' + htmlEncode(this.config.materials[this.settings.material.value].name) + '</dd>';
						}

						// Options
						for (option in this.settings.options) {
							if (this.settings.options.hasOwnProperty(option) && this.settings.options[option].controlenabled) {
								html += '<dt>' + htmlEncode(this.getSettingName('option', option)) + '</dt><dd>' + htmlEncode(this.config.options[option][this.settings.options[option].value].name) + '</dd>';
							}
						}

						// Design Service (Check if control exists and is enabled)
						if (this.getControlData('designservice')) {
							html += '<dt>' + htmlEncode(this.getSettingName('designservice')) + '</dt><dd>' + ( this.settings.designservice ? 'Yes, improve design for free.' : 'No, print design as closely as possible to image shown.' ) + '</dd>';
						}

						// Instructions (Check if control exists and is enabled)
						if (this.settings.instructions.length > 0 && this.getControlData('designservice')) {
							html += '<dt>' + htmlEncode(this.getSettingName('instructions')) + '</dt><dd>' + htmlEncode(this.settings.instructions) + '</dd>';
						}

					html += '</dl>';

					html += '<table>';
						html += '<thead>';
							html += '<tr>';
								html += '<th>Quantity</th>';
								html += '<th>Each</th>';
								html += '<th>Product Total</th>';
							html += '</tr>';
						html += '</thead>';
						html += '<tbody>';
							html += '<tr>';
								html += '<td>' + this.settings.qty + '</td>';
								html += '<td>' + formatPrice(price, '$') + '</td>';
								html += '<td>' + formatPrice(price * this.settings.qty, '$') + '</td>';
							html += '</tr>';
						html += '</tbody>';
					html += '</table>';

					if (data.notices && data.notices.length > 0) {
						html += '<p class="addtocart-notice">Note: ' + htmlEncode(data.notices.join(' ')) + '</p>';
					}

				html += '</div>';

			html += '</div>';

			html += '<div>';

				html += ((this.mode === 'edit' || this.mode === 'adminedit') ? '<div class="cancel builder-button">Continue Editing</div>' : '<div class="cancel builder-button">Close Window</div><div class="revert builder-button">Remove Item</div>');

				if (this.mode === 'adminedit') {
					html += '<a class="proceed builder-button-primary" href="javascript:window.close();">Done</a>';
				} else {
					html += '<a class="proceed builder-button-primary" href="' + htmlEncode(baseurl + carturl) + '">Go to Shopping Cart</a>';
				}

			html += '</div>';

		html += '</div>';

		// Generate the content.
		$content = $(document.getElementById('builder-dialog-wrapper')).html(html);
		$content.find('.revert').on('click', createBoundFunction(this.undoSubmit, this));
		$content.find('.cancel').on('click', function (e) { closeDialog(true); });

		// Update the dialog.
		updateDialog();

		// Update the cart display in the header.
		if ( ss ) { ss.site.updateMiniCart(data.count, data.subtotal); }

	}

};
Builder.prototype.showCartRemove = function (data) {

	if (data.success !== true) {

		this.showCartError(data);

	} else {

		// Close the dialog.
		closeDialog(true);

		// Remove the design ID.
		this.designid = undefined;

		// Update the cart display in the header.
		if ( ss ) { ss.site.updateMiniCart(data.count, data.subtotal); }

	}

};
Builder.prototype.showCartError = function (data) {

	var html = '',
		errors = (data && data.errors && data.errors.length > 0) ? data.errors.join(' ') : 'A networking error occurred.',
		subject = 'Help me with my ' + this.config.info.productname,
		body = '',
		option,
		color,
		element,
		elementname,
		$content;

	body += '\r\rBelow are the details of my item:\r\r';

		// Size
		body += this.getSettingName('size') + ': ' + this.config.sizes[this.settings.size.value].name + '\r';

		// Material
		body += this.getSettingName('material') + ': ' + this.config.materials[this.settings.material.value].name + '\r';

		// Scheme
		body += this.getSettingName('scheme') + ': ' + this.config.schemes[this.settings.scheme.value].name + '\r';

		// Layout
		body += this.getSettingName('layout') + ': ' + this.config.layouts[this.settings.layout.value][this.getLayoutVariationNumber(this.settings.layout.value, this.settings.size.value, this.settings.scheme.value)].name + '\r';

		// Options
		for (option in this.settings.options) {
			if (this.settings.options.hasOwnProperty(option) && this.config.options[option][this.settings.options[option].value]) {
				body += this.getSettingName('option', option) + ': ' + this.config.options[option][this.settings.options[option].value].name + '\r';
			}
		}

		// Colors
		for (color in this.settings.colors) {
			if (this.settings.colors.hasOwnProperty(color) && this.config.colors[this.settings.colors[color].value]) {
				body += this.getSettingName('color', color) + ': ' + this.config.colors[this.settings.colors[color].value].name + '\r';
			}
		}

		// Elements
		for (element in this.settings.elements) {
			if (this.settings.elements.hasOwnProperty(element)) {

				elementname = this.getSettingName('element', element);

				if (this.config.elements[element].type === 'artwork') {

					if (this.config.elements[element].custom) {

						// Uploads
						body += elementname + ': Upload ID ' + this.settings.elements[element].value + '\r';

					} else if (this.config.artwork[this.settings.elements[element].value]) {

						// Artwork
						body += elementname + ': ' + this.config.artwork[this.settings.elements[element].value].name + '\r';

					}

				} else {

					// Text
					body += elementname + ' (Content): ' + this.settings.elements[element].value + '\r\r';

					if (this.settings.elements[element].font && this.config.fonts[this.settings.elements[element].font.value]) {
						body += elementname + ' (Font): ' + this.config.fonts[this.settings.elements[element].font.value].name + '\r';
					}

					if (this.settings.elements[element].fontsize && fontsizes[this.settings.elements[element].fontsize.value]) {
						body += elementname + ' (Font Size): ' + fontsizes[this.settings.elements[element].fontsize.value] + '\r';
					}

					if (this.settings.elements[element].alignment && alignments[this.settings.elements[element].alignment.value]) {
						body += elementname + ' (Text Alignment): ' + alignments[this.settings.elements[element].alignment.value] + '\r';
					}

				}

			}
		}

		// Quantity
		body += 'Quantity: ' + this.settings.qty + '\r';

		// Cart ID
		if (defined(this.designid)) { body += 'Design ID: ' + this.designid; }

	body += '\r\r\rThe following errors were encountered:\r' + errors + '\r\r';

	html += '<div class="addtocart-error">';

		html += '<div class="addtocart-errormessage"><img src="' + htmlEncode(baseurl + images.fatalerroricon) + '" alt="Error" /> Error</div>';

		html += '<div class="error-info">';

			html += '<p>' + htmlEncode(errors) + '</p>';

			html += '<p>Don’t worry — we’re here to help; just <a href="' + htmlEncode('mailto:' + emailaddress + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body)) + '">send us an email</a> or give us a call at ' + htmlEncode(phonenum) + '.</p>';

		html += '</div>';

		html += '<div><div class="cancel builder-button">Close Window</div></div>';

	html += '</div>';

	// Generate the content.
	$content = $(document.getElementById('builder-dialog-wrapper')).html(html);
	$content.find('.cancel').on('click', function (e) { closeDialog(true); });

	// Display the 'Added to Cart' dialog.
	updateDialog();

};
Builder.prototype.undoSubmit = function () {

	var ajax = {};

	this.showLoadingMessage('Removing from cart', true);

	// Basic Setup
	ajax.url = baseurl + this.submiturl;
	ajax.dataType = 'json';
	ajax.type = 'POST';
	ajax.error = createBoundFunction(this.showCartError, this);
	ajax.success = createBoundFunction(this.showCartRemove, this);
	ajax.complete = createBoundFunction(this.closeAjaxRequest, this);

	// Data Output
	ajax.data = {};
	ajax.data.designid = this.designid;
	ajax.data.producttype = producttype;
	ajax.data.action = 'remove';

	// Send the data to the cart
	this.submitAjaxRequest(ajax);

};

// Element Class
Element.prototype.interpretData = function (data, type, visible) {

	var newdata = {};

	// Set the defaults.
	var datadefaults = {
		origin: 'tl',
		x: 0,
		y: 0,
		w: 0,
		h: 0,
		padding: 0,
		halign: 'center',
		valign: 'middle',
		schemecolor: undefined,
		defaultartwork: 'none',
		font: undefined,
		fontsize: 0,
		leading: 120,
		charlimit: 0,
		defaulttext: ''
	};

	var dataavailable = (defined(data) && visible);

	// Inlayout
	newdata.inlayout = dataavailable;

	// Origin
	var tmporigin = ((dataavailable && typeof data.origin === 'string') ? data.origin.toLowerCase() : datadefaults.origin);
	newdata.origin = ((tmporigin === 'tl' || tmporigin === 't' || tmporigin === 'tr' || tmporigin === 'r' || tmporigin === 'br' || tmporigin === 'b' || tmporigin === 'bl' || tmporigin === 'l' || tmporigin === 'c') ? tmporigin : datadefaults.origin);

	// X
	newdata.x = ((dataavailable && typeof data.x === 'number' && data.x >= 0) ? Math.round(data.x) : datadefaults.x);

	// Y
	newdata.y = ((dataavailable && typeof data.y === 'number' && data.y >= 0) ? Math.round(data.y) : datadefaults.y);

	// Width
	newdata.w = ((dataavailable && typeof data.w === 'number' && data.w >= 0) ? Math.round(data.w) : datadefaults.w);

	// Height
	newdata.h = ((dataavailable && typeof data.h === 'number' && data.h >= 0) ? Math.round(data.h) : datadefaults.h);

	// Minimum Width
	newdata.minw = ((dataavailable && typeof data.minw === 'number' && data.minw >= 0 && data.minw <= newdata.w) ? Math.round(data.minw) : newdata.w);

	// Minimum Height
	newdata.minh = ((dataavailable && typeof data.minh === 'number' && data.minh >= 0 && data.minh <= newdata.h) ? Math.round(data.minh) : newdata.h);

	// Padding
	newdata.padding = ((dataavailable && typeof data.padding === 'number' && data.padding >= 0) ? Math.round(data.padding) : datadefaults.padding);

	// Horizontal Alignment
	var tmphalign = ((dataavailable && typeof data.halign === 'string') ? data.halign.toLowerCase() : datadefaults.halign);
	newdata.halign = ((tmphalign === 'center' || tmphalign === 'left' || tmphalign === 'right') ? tmphalign : datadefaults.halign);

	// Vertical Alignment
	var tmpvalign = ((dataavailable && typeof data.valign === 'string') ? data.valign.toLowerCase() : datadefaults.valign);
	newdata.valign = ((tmpvalign === 'middle' || tmpvalign === 'top' || tmpvalign === 'bottom') ? tmpvalign : datadefaults.valign);

	// Scheme Color
	newdata.schemecolor = ((dataavailable && typeof data.schemecolor === 'string') ? data.schemecolor : datadefaults.schemecolor);

	if (type === 'artwork') {

		// Default Artwork
		newdata.defaultartwork = ((dataavailable && typeof data.defaultartwork === 'string') ? data.defaultartwork : datadefaults.defaultartwork);

	} else if (type === 'text') {

		// Font
		newdata.font = ((dataavailable && typeof data.font === 'string') ? data.font : datadefaults.font);

		// Font Size
		newdata.fontsize = ((dataavailable && typeof data.fontsize === 'number' && data.fontsize >= 0) ? Math.round(data.fontsize) : datadefaults.fontsize);

		// Minimum Font Size
		newdata.minfontsize = ((dataavailable && typeof data.minfontsize === 'number' && data.minfontsize >= 0 && data.minfontsize <= newdata.fontsize) ? Math.round(data.minfontsize) : newdata.fontsize);

		// Leading
		newdata.leading = ((dataavailable && typeof data.leading === 'number' && data.leading >= 0) ? Math.round(data.leading) : datadefaults.leading);

		// Character Limit
		newdata.charlimit = ((dataavailable && typeof data.charlimit === 'number' && data.charlimit >= 0) ? Math.round(data.charlimit) : datadefaults.charlimit);

		// Default Text
		newdata.defaulttext = ((dataavailable && typeof data.defaulttext === 'string') ? data.defaulttext : datadefaults.defaulttext);

	}

	return newdata;

};
Element.prototype.calculateEdges = function (data) {

	var edgevalues;

	switch (data.origin) {

		case 'tl':
			edgevalues = {
				top: {
					value: data.y,
					collapse: false
				},
				right: {
					value: data.x + data.w,
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y + data.h,
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x,
					collapse: false
				}
			};
			break;

		case 't':
			edgevalues = {
				top: {
					value: data.y,
					collapse: false
				},
				right: {
					value: data.x + Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y + data.h,
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x - Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				}
			};
			break;

		case 'tr':
			edgevalues = {
				top: {
					value: data.y,
					collapse: false
				},
				right: {
					value: data.x,
					collapse: false
				},
				bottom: {
					value: data.y + data.h,
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x - data.w,
					collapse: (data.minw < data.w)
				}
			};
			break;

		case 'r':
			edgevalues = {
				top: {
					value: data.y - Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x,
					collapse: false
				},
				bottom: {
					value: data.y + Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x - data.w,
					collapse: (data.minw < data.w)
				}
			};
			break;

		case 'br':
			edgevalues = {
				top: {
					value: data.y - data.h,
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x,
					collapse: false
				},
				bottom: {
					value: data.y,
					collapse: false
				},
				left: {
					value: data.x - data.w,
					collapse: (data.minw < data.w)
				}
			};
			break;

		case 'b':
			edgevalues = {
				top: {
					value: data.y - data.h,
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x + Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y,
					collapse: false
				},
				left: {
					value: data.x - Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				}
			};
			break;

		case 'bl':
			edgevalues = {
				top: {
					value: data.y - data.h,
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x + data.w,
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y,
					collapse: false
				},
				left: {
					value: data.x,
					collapse: false
				}
			};
			break;

		case 'l':
			edgevalues = {
				top: {
					value: data.y - Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x + data.w,
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y + Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x,
					collapse: false
				}
			};
			break;

		case 'c':
			edgevalues = {
				top: {
					value: data.y - Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				right: {
					value: data.x + Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				},
				bottom: {
					value: data.y + Math.round(data.h / 2),
					collapse: (data.minh < data.h)
				},
				left: {
					value: data.x - Math.round(data.w / 2),
					collapse: (data.minw < data.w)
				}
			};
			break;

		default: break;

	}

	return edgevalues;

};
Element.prototype.collapseEdge = function (edge, value) {

	if (typeof this.edges[edge].original !== 'number') {
		this.edges[edge].original = this.edges[edge].value;
	}

	this.edges[edge].value = value;

};
Element.prototype.testAdjustment = function (edge, value) {

	var successful = false,
		oldvalue = this.edges[edge].value,
		edgedata = getEdgeData(edge),
		oppositevalue = this.edges[edgedata.opposite].value,
		min = 0;

	switch (edgedata.orientation) {
		case 'vertical': min = this.input.minh; break;
		case 'horizontal': min = this.input.minw; break;
	}

	if (between(value, oldvalue, oppositevalue) && (Math.abs(value - oppositevalue) >= min)) {
		successful = true;
	}

	return successful;

};
Element.prototype.getMinimumValue = function (edge) {

	var minvalue = this.edges[edge].value,
		edgedata = getEdgeData(edge),
		oppositevalue = this.edges[edgedata.opposite].value,
		min = 0;

	switch (edgedata.orientation) {
		case 'vertical': min = this.input.minh; break;
		case 'horizontal': min = this.input.minw; break;
	}

	switch (edgedata.direction) {
		case 'positive': minvalue = oppositevalue - min; break;
		case 'negative': minvalue = oppositevalue + min; break;
	}

	return minvalue;

};

// Upload Class
Upload.prototype.prepare = function () {

	$(this.domelement).fileupload({

		dataType: 'json',
		url: (baseurl + fileuploadurl),
		dropZone: null,
		pasteZone: null,
		singleFileUploads: false,
		add: createBoundFunction(this.add, this),
		done: createBoundFunction(this.done, this),
		fail: createBoundFunction(this.fail, this)

	});

};
Upload.prototype.showDialog = function () {

	var numberofimages = this.userfiles.length,
		dialoghtml = '<div id="builder-dialog-wrapper"><div id="upload-dialog" class="files-uploading"><p class="loading-message">Uploading ' + (numberofimages > 1 ? numberofimages + ' images' : ' image') + '…</p><div class="progress-bar indeterminate"><div class="progress" /></div><div class="cancel-upload builder-button">Cancel Upload</div></div></div></div>',
		$content = $(document.createElement('div')).html(dialoghtml);

	$content.find('.cancel-upload').on('click', createBoundFunction(this.cancel, this));

	showDialog({
		modal: true,
		content: $content,
		width: 500
	});

};
Upload.prototype.closeDialog = function () {

	// Hide the progress bar modal dialog.
	closeDialog(true);

	// Determine how many images were uploaded.
	var uploadcount = 0;
	for (var key in this.uploaded) {
		if (this.uploaded.hasOwnProperty(key)) {
			uploadcount++;
		}
	}

	// If the upload was successful...
	if (uploadcount	> 0) {

		// Rebuild the every file uploader's upload listing.
		updateUploadControls();

		// Select the first input in the file uploader that was just used, and then update it's pagination.
		$(this.domelement).parent().find('div.recent-uploads input').first().prop('checked', true).trigger('change');
		updateArtworkPagination($(this.domelement).parent().find('.recent-uploads'), 1, false);

	}

	// Reset errors.
	this.errors = [];

};
Upload.prototype.logError = function (error) {

	this.errors[this.errors.length] = error;

};
Upload.prototype.finish = function () {

	var $dialog = $('#upload-dialog'), errorhtml = '', i, upload;

	for (upload in this.uploaded) {
		if (this.uploaded.hasOwnProperty(upload)) {
			addUpload(upload, this.uploaded[upload].src, this.uploaded[upload].name, this.uploaded[upload].uploadtime, this.uploaded[upload].w, this.uploaded[upload].h);
		}
	}

	if (this.aborted === true) {

		this.closeDialog();

	} else {

		// Close the dialog or show the errors.
		if (this.errors.length === 0) {

			this.closeDialog(true);

		} else {

			$dialog.removeClass('files-uploading').addClass('upload-error-report');

			errorhtml += '<p class="upload-error-headline">The following errors were encountered:</p><ul>';

			for (i = 0; i < this.errors.length; i++) {
				errorhtml += '<li>' + htmlEncode(this.errors[i]) + '</li>';
			}

			errorhtml += '</ul><p>For assistance, you can email your files to <a href="mailto:' + emailaddress + '">' + emailaddress + '</a>.</p><div class="close-dialog builder-button-primary">Close</div>';

			$dialog.html(errorhtml);
			$dialog.find('.close-dialog').on('click', createBoundFunction(this.dismissErrors, this));

			updateDialog();

		}

	}
};
Upload.prototype.add = function (e, data) {

	this.aborted = false;
	this.userfiles = data.files;
	this.jqXHR = data.submit();
	this.showDialog();

};
Upload.prototype.done = function (e, data) {

	var i;

	// Log any errors.
	if (data.result.errors) {
		for (i = 0; i < data.result.errors.length; i++) {
			this.logError(data.result.errors[i]);
		}
	}

	// Store the successful uploads.
	this.uploaded = data.result.uploaded;

	// Finish the upload.
	this.finish();

};
Upload.prototype.fail = function (e, data) {

	// Unless the user aborted the updated, log an unknown error.
	if (data.errorThrown !== 'abort') {
		this.logError('An unknown error was encountered.');
	}

	// Finish the upload.
	this.finish();

};
Upload.prototype.cancel = function (e) {

	if (this.jqXHR.readyState < 4) {
		this.aborted = true;
		this.jqXHR.abort();
	} else {
		this.finish();
	}

};
Upload.prototype.dismissErrors = function (e) {
	this.closeDialog();
};

// Attach to the global scope.
window.builder = builder;

})(this, document);

// Create Builders when the DOM is ready.
$(function () {

	$('div.builder').each(function () {

		if (window.builder) { window.builder(this); }

	});

});
