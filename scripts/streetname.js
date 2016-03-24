$(document).ready(function () {

var editdata;

function importEditData () {
	editdata = JSON.parse($("input[name=\"editdata\"]").eq(0).val());
}

function getEditDataVal (key) {

	var val;

	key = (""+key).toLowerCase();

	if (editdata && editdata.hasOwnProperty(key)) {
		val = editdata[key];
	}

	return val;

}

function setEditDataVal (key, val) {

	key = (""+key).toLowerCase();

	if (editdata.hasOwnProperty(key)) {
		editdata[key] = ""+val;
	}

	updateEditDataInputs();

}

function getEditDataJSON () {
	return JSON.stringify(editdata);
}

function updateEditDataInputs () {
	$("input[name=\"editdata\"]").val(getEditDataJSON());
}

// Import the edit data on page load.
importEditData();

// Prepare add to cart overrides.
var $productConfiguration = $("#product-configuration");
var addToCartOverrides = {
	'stid':                              $productConfiguration.find("input[name=\"stid\"]").val(),
	'landing_id':                        $productConfiguration.find("input[name=\"landing_id\"]").val(),
	'subcategory_id':                    $productConfiguration.find("input[name=\"subcategory_id\"]").val(),
	'source_product_id':                 $productConfiguration.find("input[name=\"source_product_id\"]").val(),
	'source_product_recommendation_id':  $productConfiguration.find("input[name=\"source_product_recommendation_id\"]").val(),
	'source_accessory_familyProduct_id': $productConfiguration.find("input[name=\"source_accessory_familyProduct_id\"]").val(),
	'source_installation_accessory_id':  $productConfiguration.find("input[name=\"source_installation_accessory_id\"]").val(),
	'source_landing_product_id':         $productConfiguration.find("input[name=\"source_landing_product_id\"]").val(),
	'source_subcategory_product_id':     $productConfiguration.find("input[name=\"source_subcategory_product_id\"]").val()
};

// Prepare the add to cart forms on page load.
prepareAddToCartForms();

// Preload the fonts on page load.
$('#fontlist-wrapper').html('<ul id="fontList-loader"><li id="Highway" class="Highway"><p>Highway</p></li><li id="Algerian" class="Algerian"><p>Algerian</p></li><li id="Arial" class="Arial"><p>Arial</p></li><li id="Brush" class="Brush"><p>Brush</p></li><li id="Century" class="Century"><p>Century</p></li><li id="Clarendon" class="Clarendon"><p>Clarendon</p></li><li id="Futura" class="Futura"><p>Futura</p></li><li id="Swiss" class="Swiss"><p>Swiss</p></li><li id="Times_New_Roman" class="Times_New_Roman"><p>Times New Roman</p></li><li id="Tekton" class="Tekton"><p>Tekton</p></li><li id="Zapf" class="Zapf"><p>Zapf</p></li></ul>');


	function fontTestx6(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 1.5; break;
					case "Arial": var textComp = 1.1; break;
					case "Brush": var textComp = 1.2; break;
					case "Century": var textComp = 1.05; break;
					case "Clarendon": var textComp = 1.05; break;
					case "Futura": var textComp = 1.05; break;
					case "Highway": var textComp = 1.2; break;
					case "Swiss": var textComp = 1; break;
					case "Tekton": var textComp = 1.2; break;
					case "Times_New_Roman": var textComp = 1.2; break;
					case "Zapf": var textComp = 1; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTest2linex6(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){
				   switch (classList[i]) {
					case "Algerian": var textComp = 1.15; break;
					case "Arial": var textComp = .97; break;
					case "Brush": var textComp = 1; break;
					case "Century": var textComp = .9; break;
					case "Clarendon": var textComp = 1.01; break;
					case "Futura": var textComp = .9; break;
					case "Highway": var textComp = 1; break;
					case "Swiss": var textComp = .95; break;
					case "Tekton": var textComp = 1; break;
					case "Times_New_Roman": var textComp = 1; break;
					case "Zapf": var textComp = .9; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestx6Upper(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 1.05; break;
					case "Arial": var textComp = 1; break;
					case "Brush": var textComp = 1.05; break;
					case "Century": var textComp = 1.05; break;
					case "Clarendon": var textComp = 1.05; break;
					case "Futura": var textComp = 1; break;
					case "Highway": var textComp = 1.05; break;
					case "Swiss": var textComp = 1; break;
					case "Tekton": var textComp = 1; break;
					case "Times_New_Roman": var textComp = 1; break;
					case "Zapf": var textComp = 1; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestx9(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 1.5; break;
					case "Arial": var textComp = 1.1; break;
					case "Brush": var textComp = 1.2; break;
					case "Century": var textComp = 1.05; break;
					case "Clarendon": var textComp = 1.05; break;
					case "Futura": var textComp = 1.05; break;
					case "Highway": var textComp = 1.15; break;
					case "Swiss": var textComp = 1; break;
					case "Tekton": var textComp = 1.2; break;
					case "Times_New_Roman": var textComp = 1.2; break;
					case "Zapf": var textComp = 1; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestx9Upper(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 1.05; break;
					case "Arial": var textComp = 1; break;
					case "Brush": var textComp = 1.05; break;
					case "Century": var textComp = 1.05; break;
					case "Clarendon": var textComp = 1.05; break;
					case "Futura": var textComp = 1; break;
					case "Highway": var textComp = 1.1; break;
					case "Swiss": var textComp = 1; break;
					case "Tekton": var textComp = 1; break;
					case "Times_New_Roman": var textComp = 1; break;
					case "Zapf": var textComp = 1; break;
					}
			   }
		 }
		return textComp;
	}

	$('#fontList li').click(
    function(){
        var chosen = $(this).index();
        $('#sign_font option:selected')
            .removeAttr('selected');
        $('#sign_font option')
            .eq(chosen)
            .attr('selected',true);
        $('.selected').removeClass('selected');
        $(this).addClass('selected');
		updateclass();

		var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
		var text1counter = $("#preview-image").find(".line_1 span.copy");
		var text1container = $("#preview-image").find(".line_1");
		var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
		var text2counter = $("#preview-image").find(".line_2 span.copy");
		var text2container = $("#preview-image").find(".line_2");
		compress_text(text1counter, text1boundry, text1container, text1counter.html());
		compress_text(text2counter, text2boundry, text2container, text2counter.html());

		var copyS = $("#suffix_selector").val();
		var textScounter = $("#preview-image #suffix-wrap p");
		var textScontainer = $("#preview-image #suffix-wrap");
		var textSboundry = $("#preview-image #suffix-wrap");
		compress_text_suffix(textScounter, textSboundry, textScontainer, copyS);

		var copyNum = $("#sidetext").val();
		var textNumcounter = $("#preview-image #streetnum p");
		var textNumcontainer = $("#preview-image #streetnum");
		var textNumboundry = $("#preview-image #streetnum");
		compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);
    });


	function compress_text($element_to_compress, $boundry, $textcontainer, text) {

		var a1, a2, a, b, c;

		if ($textcontainer.hasClass("line_1")) {

			a1 = $element_to_compress.width();
			a2 = textWidthLine1($textcontainer, text);

			a = a1 > a2 ? a1 : a2;

			b = $boundry.width();

			c = b / a;

			if (c >= 1) {
				c = 1;
				$textcontainer.css("position", "relative");
				$element_to_compress.css("position", "relative");
			} else {
				var browserversion = $('html').hasClass('lt-ie8');
				c = Math.round(c * 101) / 100;
				if(browserversion == true){
				$textcontainer.css("position", "relative");
				}else{$textcontainer.css("position", "absolute");}
				$element_to_compress.css("position", "absolute");
			}

			if ($(".compress_1").length > 0) {
				$(".compress_1").val(c);
			}

		}

		if ($textcontainer.hasClass("line_2")) {

			a1 = $element_to_compress.width();
			a2 = textWidthLine2($textcontainer, text);

			a = a1 > a2 ? a1 : a2;

			b = $boundry.width();

			c = b / a;

			if (c >= 1) {
				c = 1;
				$textcontainer.css("position", "relative");
				$element_to_compress.css("position", "relative");
			} else {
				c = Math.round(c * 101) / 100;
				$textcontainer.css("position", "absolute");
				$element_to_compress.css("position", "absolute");
			}

			if ($(".compress_2").length > 0) {
				$(".compress_2").val(c);
			}

		}

		$element_to_compress.css({
			scaleX: c,
			origin: [0, 0]
		});

	}



	function textWidthLine1(textcontainer,text){
		var calc = '<span id="tempspan" style="display:none">' + text + '</span>';
		textcontainer.append(calc);

		var widthOriginal = textcontainer.find('span:last').width();
		var x6 = $("#custom-product-images").hasClass("x6");
		var x4 = $("#custom-product-images").hasClass("x4");
		var x9 = $("#custom-product-images").hasClass("x9");
		var line2x6 = $("#custom-product-images").hasClass("twoline");
		var line2content = $("#preview-image .line_2 span").html();
		var textUpper = $("#custom-product-images").hasClass("textupper");
		var test = $("#preview-image").find(".line_1");
		var test2 = $("#preview-image").find(".line_2");

		if ($("html").hasClass("lt-ie10")) {
			if (x6 == true || x4 == true){
				if (textUpper != true){
						if (line2x6 == true){
							var compressRate = fontTest2linex6();
						}else{
							var compressRate = fontTestx6();
						}

				}else {
					var compressRate = fontTestx6Upper();
				}

			}else if (x9 == true){
				if (textUpper != true){
					var compressRate = fontTestx9();
				}else {
					var compressRate = fontTestx9Upper();
				}
			}

		}else {var compressRate = 1;}

		var width = widthOriginal * compressRate;

		textcontainer.find('span:last').remove();
		return width;
	};


	function textWidthLine2(textcontainer,text){
		var calc = '<span id="tempspan" style="display:none">' + text + '</span>';
		textcontainer.append(calc);

		var widthOriginal = textcontainer.find('span:last').width();
		var x6 = $("#custom-product-images").hasClass("x6");
		var x4 = $("#custom-product-images").hasClass("x4");
		var x9 = $("#custom-product-images").hasClass("x9");
		var line2x6 = $("#custom-product-images").hasClass("twoline");
		var line2content = $("#preview-image .line_2 span").html();
		var textUpper = $("#custom-product-images").hasClass("textupper");
		var test = $("#preview-image").find(".line_1");
		var test2 = $("#preview-image").find(".line_2");

		if ($.support.cssFloat != true) {
			if (x6 == true || x4 == true){
				if (textUpper != true){
					var compressRate = fontTestx6();
				}else {
					var compressRate = fontTestx6Upper();
				}

			}else if (x9 == true){
				if (textUpper != true){
					var compressRate = fontTestx9();
				}else {
					var compressRate = fontTestx9Upper();
				}
			}

		}else {var compressRate = 1;}

		var width = widthOriginal * compressRate;

		textcontainer.find('span:last').remove();
		return width;
	};




	$("#sign_color").change(function(){
		var color=$(this).val();
		setEditDataVal("sign_color", color);
		updateclass();
	});
	$("#text_colorddl").change(function(){
		updateclass();
		var textcolor=$(this).val();
		$(".vtextcolor").val(textcolor);
	})
	$("#mounting_option").change(function(){
		var mounting=$(this).val();
		$(".vmounting").val(mounting);
		});
	$("#prefix_selector").change(function(){
		var prefix=$("#prefix_selector").val();
		if(prefix=='NONE')
		{
			if($("#suffix_selector").length>0)
			{
				var suffix=$("#suffix_selector").val();
				if(!(suffix.indexOf("Arrow") != -1))
					$("#position_selector_container").show();
				if(suffix=='NONE')
					$("#suffix-wrap p").html("");
				else if(!(suffix.indexOf("Arrow") != -1))
					$("#suffix-wrap p").html(suffix);
			}
			$("#prefix-wrap p").html("");
		}
		else if(prefix.indexOf("Arrow") != -1)
		{
			if($("#suffix_selector").length>0)
			{
				$("#suffix_selector").val("NONE");
				setEditDataVal("suffix", "NONE");
				$("#suffix-wrap p").html("");
			}
			$("#position_selector_container").hide();
			$("#prefix-wrap p").html("");
		}
		else
		{
			if($("#suffix_selector").length>0)
			{
				var suffix=$("#suffix_selector").val();
				if(suffix.indexOf("Arrow")!=-1)
				{
					$("#suffix_selector").val("NONE");
					$("#suffix-wrap p").html("");
					setEditDataVal("suffix", "NONE");
				}
				else if(suffix=='NONE')
					$("#suffix-wrap p").html("");
				else
					$("#suffix-wrap p").html(suffix);
			}
			$("#position_selector_container").show();
			$("#prefix-wrap p").html(prefix);
		}
		updateclass();
		if($("#line_1").length>0)
		{
			var copy1=$("#line_1").val();
			var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
			var text1counter = $("#preview-image").find(".line_1 span.copy");
			var text1container = $("#preview-image").find(".line_1");
			compress_text(text1counter, text1boundry, text1container, copy1);
		}
		if($("#line_2").length>0)
		{
			var copy2=$("#line_2").val();
			var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
			var text2counter = $("#preview-image").find(".line_2 span.copy");
			var text2container = $("#preview-image").find(".line_2");
			compress_text(text2counter, text2boundry, text2container,copy2);
		}
		setEditDataVal("prefix", prefix);
		});
	$("#suffix_selector").change(function(){
		var suffix=$("#suffix_selector").val();
		if(suffix=='NONE')
		{
			if($("#prefix_selector").length>0)
			{
				var prefix=$("#prefix_selector").val();
				if(!(prefix.indexOf("Arrow") != -1))
					$("#position_selector_container").show();
				if(prefix=='NONE')
					$("#prefix-wrap p").html("");
				else if(!(prefix.indexOf("Arrow") != -1))
					$("#prefix-wrap p").html(prefix);
			}
			$("#suffix-wrap p").html("");
		}
		else if(suffix.indexOf("Arrow") != -1)
		{
			if($("#prefix_selector").length>0)
			{
				$("#prefix_selector").val("NONE");
				setEditDataVal("prefix", "NONE");
				$("#prefix-wrap p").html("");
			}
			$("#position_selector_container").hide();
			$("#suffix-wrap p").html("");
		}
		else
		{
			if($("#prefix_selector").length>0)
			{
				var prefix=$("#prefix_selector").val();
				if(prefix.indexOf("Arrow")!=-1)
				{
					$("#prefix_selector").val("NONE");
					$("#prefix-wrap p").html("");
					setEditDataVal("prefix", "NONE");
				}
				if(prefix=='NONE')
					$("#prefix-wrap p").html("");
				else
					$("#prefix-wrap p").html(prefix);
			}
			$("#position_selector_container").show();
			$("#suffix-wrap p").html(suffix);
		}
		updateclass();
		if($("#line_1").length>0)
		{
			var copy1=$("#line_1").val();
			var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
			var text1counter = $("#preview-image").find(".line_1 span.copy");
			var text1container = $("#preview-image").find(".line_1");

				compress_text(text1counter, text1boundry, text1container, copy1);
		}
		if($("#line_2").length>0)
		{
			var copy2=$("#line_2").val();
			var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
			var text2counter = $("#preview-image").find(".line_2 span.copy");
			var text2container = $("#preview-image").find(".line_2");
			compress_text(text2counter, text2boundry, text2container,copy2);
		}
		var copyS = $("#suffix_selector").val();
		var textScounter = $("#preview-image #suffix-wrap p");
		var textScontainer = $("#preview-image #suffix-wrap");
		var textSboundry = $("#preview-image #suffix-wrap");
		compress_text_suffix(textScounter, textSboundry, textScontainer, copyS);

		setEditDataVal("suffix", suffix);

		});
	$("#position_selector").change(function(){
		updateclass();
		var position=$(this).val();
		setEditDataVal("position", position);
		});
	$("#sign_font").change(function(){
		updateclass();
		var font=$(this).val();
		setEditDataVal("sign_font", font);
		if($("#line_1").length>0)
		{
			var line1=$("#line_1").val();
			var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
			var text1counter = $("#preview-image").find(".line_1 span.copy");
			var text1container = $("#preview-image").find(".line_1");
			compress_text(text1counter, text1boundry, text1container, line1);

		}
		else if($("#textarea_1").length>0)
		{
			var textsizecontainer=$("#line_1_textsize");
			var text1container = $("#preview-image").find(".line_1");
			var counter1 = $("#line-1-container").find("span.current-characters");
			var copy1 = $("#textarea_1").val();
			var text1boundry = $("#preview-image").find(".product_text");
			updatepreview(text1container,text1counter,copy1,text1boundry,textsizecontainer);
		}
		if($("#line_2").length>0)
		{
			var line2=$("#line_2").val();
			var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
			var text2counter = $("#preview-image").find(".line_2 span.copy");
			var text2container = $("#preview-image").find(".line_2");
			compress_text(text2counter, text2boundry, text2container, line2);
		}
		else if($("#textarea_2").length>0)
		{
			var textsizecontainer=$("#line_2_textsize");
			var text2container = $("#preview-image").find(".line_2");
			var counter2 = $("#line-1-container").find("span.current-characters");
			var copy2 = $("#textarea_2").val();
			var text2boundry = $("#preview-image").find(".product_text");
			updatepreview(text2container,text2counter,copy2,text2boundry,textsizecontainer);
		}
		if($("#suffix_selector").length>0)
		{
		var copy1 = $("#suffix_selector").val();
		var text1counter = $("#preview-image #suffix-wrap p");
		var text1container = $("#preview-image #suffix-wrap");
		var text1boundry = $("#preview-image #suffix-wrap");
		compress_text_suffix(text1counter, text1boundry, text1container, copy1);
		}
		if($("#sidetext").length>0)
		{
		var copyNum = $("#sidetext").val();
		var textNumcounter = $("#preview-image #streetnum p");
		var textNumcontainer = $("#preview-image #streetnum");
		var textNumboundry = $("#preview-image #streetnum");
		compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);
		}

		});
	$("#arrow_selector").change(function(){
		updateclass();
		var arrow=$(this).val();
		$(".varrow").val(arrow);
	})
	$("#arrowcolor_selector").change(function(){
		updateclass();
		var arrowcolor=$(this).val();
		$(".varrowcolor").val(arrowcolor);
	})
	$("#sign_background").change(function(){
			var background=$(this).val();
			setEditDataVal("sign_background", background);

			// Update the hidden upload-required input.
			$("#streetsign-upload-required").val($(this).find(":selected").data("streetsignUploadRequired") === "Y" ? "Y" : "N");

			if(background=='Logo' || $('#custom-product-images').hasClass('Image'))
			{
				if($("#uploadshow").length>0)
				{
					if ( getEditDataVal("uploadfileid") != "" ) {
						$("#delete_file1").show();
					}
					$("#uploadshow").show();
					$("#buttonmessage").html("");
				}
				if ($("#prefix_selector").length > 0)
				{
					var prefixoptioncontainer=$("#prefix_selector option");
					prefixoptioncontainer.each(function(index)
					{
						var optionvalue=$(this).val();
						if(optionvalue.indexOf("Arrow") != -1)
							$(this).removeAttr('disabled');
					});
				}
				if ($("#suffix_selector").length > 0)
				{
					var suffixoptioncontainer=$("#suffix_selector option");
					suffixoptioncontainer.each(function(index)
					{
						var optionvalue=$(this).val();
						if(optionvalue.indexOf("Arrow") != -1)
							$(this).removeAttr('disabled');
					});
				}
			}
			else
			{
				if($("#uploadshow").length>0)
				{
					$("#delete_file1").hide();
					$("#uploadshow").hide();
					$("#uploadnessage").html("");
					if($("#uploadnessage").hasClass("success"))
						$("#uploadnessage").removeClass("success");
					else if($("#uploadnessage").hasClass("error"))
						$("#uploadnessage").removeClass("error");
					$("#buttonmessage").html("To upload custom artwork, please select Logo as your background option above.");
				}
				if(background=='Left-Pointer')
				{
					if ($("#prefix_selector").length > 0)
					{
						var prefix=$("#prefix_selector").val();
						if(prefix.indexOf("Arrow") != -1)
						{
							$("#prefix_selector").val("NONE");
							setEditDataVal("prefix", "NONE");
						}
						var prefixoptioncontainer=$("#prefix_selector option");
						prefixoptioncontainer.each(function(index)
						{
							var optionvalue=$(this).val();
							if(optionvalue.indexOf("Arrow") != -1)
								$(this).attr("disabled","disabled");
						});
					}
					if ($("#suffix_selector").length > 0)
					{
						var suffixoptioncontainer=$("#suffix_selector option");
						suffixoptioncontainer.each(function(index)
						{
							var optionvalue=$(this).val();
							if(optionvalue.indexOf("Arrow") != -1)
								$(this).removeAttr('disabled');
						});
					}
				}
				else if(background=='Right-Pointer')
				{
					if ($("#suffix_selector").length > 0)
					{
						var suffix=$("#suffix_selector").val();
						if(suffix.indexOf("Arrow") != -1)
						{
							$("#suffix_selector").val("NONE");
							setEditDataVal("suffix", "NONE");
						}
						var suffixoptioncontainer=$("#suffix_selector option");
						suffixoptioncontainer.each(function(index)
						{
							var optionvalue=$(this).val();
							if(optionvalue.indexOf("Arrow") != -1)
								$(this).attr("disabled","disabled");
						});
					}
					if ($("#prefix_selector").length > 0)
					{
						var prefixoptioncontainer=$("#prefix_selector option");
						prefixoptioncontainer.each(function(index)
						{
							var prefixoptionvalue=$(this).val();
							if(prefixoptionvalue.indexOf("Arrow") != -1)
								$(this).removeAttr('disabled');
						});
					}
				}
				else
				{
					if ($("#prefix_selector").length > 0)
					{
						var prefixoptioncontainer=$("#prefix_selector option");
						prefixoptioncontainer.each(function(index)
						{
							var optionvalue=$(this).val();
							if(optionvalue.indexOf("Arrow") != -1)
								$(this).removeAttr('disabled');
						});
					}
					if ($("#suffix_selector").length > 0)
					{
						var suffixoptioncontainer=$("#suffix_selector option");
						suffixoptioncontainer.each(function(index)
						{
							var optionvalue=$(this).val();
							if(optionvalue.indexOf("Arrow") != -1)
								$(this).removeAttr('disabled');
						});
					}
				}
			}
			updateclass();
			if($("#line_1").length>0)
			{
				var copy1=$("#line_1").val();
				var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
				var text1counter = $("#preview-image").find(".line_1 span.copy");
				var text1container = $("#preview-image").find(".line_1");
				compress_text(text1counter, text1boundry, text1container, copy1);
			}
			if($("#line_2").length>0)
			{
				var copy2=$("#line_2").val();
				var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
				var text2counter = $("#preview-image").find(".line_2 span.copy");
				var text2container = $("#preview-image").find(".line_2");
				compress_text(text2counter, text2boundry, text2container,copy2);
			}
		});
		function updatepreviewchangetextsize(textcontainer,textcounter,text,boundarycontainer,textsizecontainer){
			var orgcopy=textcontainer.html();
			textcontainer.html("");
			if(textsizecontainer.length>0)
				var fontsizev=textsizecontainer.val();
			var boundaryheight=boundarycontainer.height();
			var boundary=boundarycontainer.width();
			//var numberofchars = text.length;
			text=text.replace(/</g,"&lt;").replace(/>/g, "&gt;");
			var textcontainerid=textcontainer.attr("id");
			if(text.indexOf("\n") != -1)
			{
				var copysepa=text.split("\n");
				var arraycount=copysepa.length;
				var i;
				for(i=0;i<=arraycount;i++)
				{
					if(i!=arraycount)
					{
						if(i==0)
							textcontainer.append("<span class='copy' id='"+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+copysepa[i]+"</span>");
						else
							textcontainer.append("<br/><span class='copy' id'="+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+copysepa[i]+"</span>");
						if(textsizecontainer.length>0)
						{
							var textcounter = textcontainer.find("span.copy");
							textcounter.css("font-size",fontsizev+"pt");
							textcounter.css("line-height",fontsizev+"pt");
						}
						var lastline=textcontainer.find("span:last-child");
						if(lastline.width()>boundary)
						{
							fontsizev=parseInt(fontsizev);
							fontsizev=fontsizev-1;
							$("#line_1_textsize").val(fontsizev);
							textcontainer.html(orgcopy);
							break;
						}
					}
					else
					{
						textcontainer.find("span:last-child").html(copysepa[i]);
						if(textsizecontainer.length>0)
						{
							var fontsizev=textsizecontainer.val();
							var textcounter = textcontainer.find("span.copy");
							textcounter.css("font-size",fontsizev+"pt");
							textcounter.css("line-height",fontsizev+"pt");
						}
					}
					var textcounterlastrow = textcontainer.find("span:last-child.copy");
					if(textcontainer.height()>boundaryheight)
					{
						fontsizev=parseInt(fontsizev);
						fontsizev=fontsizev-1;
						textsizecontainer.val(fontsizev);
						textcontainer.html(orgcopy);
						break;
					}
				}
			}
			else
			{
				textcontainer.append("<span class='copy' id='"+textcontainerid+"tx0"+"' name='"+textcontainerid+"tx0"+"'>"+text+"</span>");
				var textcounterlastrow = textcontainer.find("span:last-child.copy");
				if(textsizecontainer.length>0)
				{
					var textcounter = textcontainer.find("span.copy");
					textcounter.css("font-size",fontsizev+"pt");
					textcounter.css("line-height",fontsizev+"pt");
				}
				var textwidth=textcounterlastrow.width();
				var i=0;
				while(textwidth>boundary)
				{
					var c;
					var lastrow=textcounterlastrow.html();
					var numberofchars = lastrow.length;
					for(c=0;c<numberofchars;c++)
					{
						var reducetext=lastrow.substring(0,numberofchars-c);
						textcounterlastrow.html(reducetext);
						var newtextwidth=textcounterlastrow.width();
						if(newtextwidth<=boundary)
						{
							var lastrowtext=lastrow.substring(numberofchars-c);
							textcontainer.append("<br/><span class='copy' id='"+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+lastrowtext+"</span>");
							if(textsizecontainer.length>0)
							{
								var textcounter = textcontainer.find("span.copy");
								textcounter.css("font-size",fontsizev+"pt");
								textcounter.css("line-height",fontsizev+"pt");
							}
							break;
						}
					}
					textcounterlastrow = textcontainer.find("span:last-child.copy");
					textwidth=textcounterlastrow.width();
					if(textcontainer.height()>boundaryheight)
					{
						fontsizev=parseInt(fontsizev);
						fontsizev=fontsizev-1;
						textsizecontainer.val(fontsizev);
						textcounter.css("font-size",fontsizev+"pt");
						textcounter.css("line-height",fontsizev+"pt");
						textcontainer.html(orgcopy);
						break;
					}
				}
			}
			if(textcontainerid=="1")
				$(".vline_1preview").val(textcontainer.html());
			else if(textcontainerid=="2")
				$(".vline_2preview").val(textcontainer.html());

		};
		function updatepreview(textcontainer,textcounter,text,boundarycontainer,textsizecontainer){
			var orgcopy=textcontainer.html();
			textcontainer.html("");
			var orgfontsizev=textsizecontainer.val();
			var fontsizev=orgfontsizev;
			var boundaryheight=boundarycontainer.height();
			var boundary=boundarycontainer.width();
			text=text.replace(/</g,"&lt;").replace(/>/g, "&gt;");
			var textcontainerid=textcontainer.attr("id");
			if(text.indexOf("\n") != -1)//there is enter
			{
				var copysepa=text.split("\n");
				var arraycount=copysepa.length;
				var i;
				var loopbreak=false;
				for(i=0;i<=arraycount;i++)
				{
					if(i!=arraycount)
					{
						if(i==0)
							textcontainer.append("<span class='copy' id='"+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+copysepa[i]+"</span>");
						else
							textcontainer.append("<br/><span class='copy' id='"+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+copysepa[i]+"</span>");
						if(textsizecontainer.length>0)
						{
							var textcounter = textcontainer.find("span.copy");
							textcounter.css("font-size",fontsizev+"pt");
							textcounter.css("line-height",fontsizev+"pt");
						}
						var lastline=textcontainer.find("span:last-child");
						while(lastline.width()>boundary)
						{
							fontsizev=parseInt(fontsizev);
							fontsizev=fontsizev-1;
							if(fontsizev>=6)
							{
								textsizecontainer.val(fontsizev);
								textcounter.css("font-size",fontsizev+"pt");
								textcounter.css("line-height",fontsizev+"pt");
							}
							else
							{
								$(".line_1").html(orgcopy);
								loopbreak=true;
								updatemaxlength(textcontainerid);
								textsizecontainer.val(orgfontsizev);
								break;
							}
						}
						if(loopbreak)
							break;
					}
					else
					{
						textcontainer.find("span:last-child").html(copysepa[i]);
						if(textsizecontainer.length>0)
						{
							var fontsizev=textsizecontainer.val();
							var textcounter = textcontainer.find("span.copy");
							textcounter.css("font-size",fontsizev+"pt");
							textcounter.css("line-height",fontsizev+"pt");
						}
					}
					var textcounterlastrow = textcontainer.find("span:last-child.copy");
					while(textcontainer.height()>boundaryheight)
					{
						fontsizev=parseInt(fontsizev);
						fontsizev=fontsizev-1;
						if(fontsizev>=6)
						{
							textsizecontainer.val(fontsizev);
							textcounter.css("font-size",fontsizev+"pt");
							textcounter.css("line-height",fontsizev+"pt");
						}
						else
						{
							textcontainer.html(orgcopy);
							textsizecontainer.val(orgfontsizev);
							updatemaxlength(textcontainerid);
							break;
						}
					}
				}
			}
			else
			{
				textcontainer.append("<span class='copy' id='"+textcontainerid+"tx0"+"' name='"+textcontainerid+"tx0"+"'>"+text+"</span>");
				var textcounterlastrow = textcontainer.find("span:last-child.copy");
				if(textsizecontainer.length>0)
				{
					var textcounter = textcontainer.find("span.copy");
					textcounter.css("font-size",fontsizev+"pt");
					textcounter.css("line-height",fontsizev+"pt");
				}
				var textwidth=textcounterlastrow.width();
				var i=1;
				while(textwidth>boundary)
				{
					var c;
					var lastrow=textcounterlastrow.html();
					var numberofchars = lastrow.length;
					for(c=0;c<numberofchars;c++)
					{
						var reducetext=lastrow.substring(0,numberofchars-c);
						textcounterlastrow.html(reducetext);
						var newtextwidth=textcounterlastrow.width();
						if(newtextwidth<=boundary)
						{
							var lastrowtext=lastrow.substring(numberofchars-c);
							textcontainer.append("<br/><span class='copy' id='"+textcontainerid+"tx"+i+"' name='"+textcontainerid+"tx"+i+"'>"+lastrowtext+"</span>");
							i++;
							if(textsizecontainer.length>0)
							{
								var textcounter = textcontainer.find("span.copy");
								textcounter.css("font-size",fontsizev+"pt");
								textcounter.css("line-height",fontsizev+"pt");
							}
							break;
						}
					}
					textcounterlastrow = textcontainer.find("span:last-child.copy");
					textwidth=textcounterlastrow.width();
					if(textcontainer.height()>boundaryheight)
					{
						fontsizev=parseInt(fontsizev);
						fontsizev=fontsizev-1;
						if(fontsizev>=6)
						{
							textsizecontainer.val(fontsizev);
							updatepreview(textcontainer,textcounter,text,boundarycontainer,textsizecontainer);
						}
						else
						{
							textcontainer.html(orgcopy);
							textsizecontainer.val(orgfontsizev);
							updatemaxlength(textcontainerid);
							break;
						}
					}
				}
			}
			if(textcontainerid=="1")
				$(".vline_1preview").val(textcontainer.html());
			else if(textcontainerid=="2")
				$(".vline_2preview").val(textcontainer.html());
			return returnv;
		};
	$("#textarea_1").keyup(function() {
		var counter1 = $("#line-1-container").find("span.current-characters");
		var copy1 = $(this).val();
		if(copy1.indexOf("\n") != -1)
		{
			var numberofchars1 = copy1.length;
			var maxleng=$("#textarea_1").attr("maxlength");

			var copysepa=copy1.split("\n");
			var arraycount=copysepa.length;
			var orgtext=copy1.substr(0,numberofchars1-1);
			var maxchar=$("#line_1_maxabs").val();
			maxchar=parseInt(maxchar);
			maxchar=maxchar+arraycount-1;
			$("#textarea_1").attr("maxlength",maxchar);
			numberofchars1=numberofchars1-arraycount+1;
		}
		else
		{
			var numberofchars1 = copy1.length;
			var maxchar=$("#line_1_maxabs").val();
			$("#textarea_1").attr("maxlength",maxchar);
			var orgtext=copy1.substr(0,numberofchars1-1);
		}
		counter1.html(numberofchars1);
		var text1counter = $("#preview-image").find(".line_1 span.copy");
		var text1container = $("#preview-image").find(".line_1");
		var max_abs=$("#line_1_maxabs").val();
		var text1boundry = $("#preview-image").find(".product_text");
		var textsizecontainer=$("#line_1_textsize");
		updatepreview(text1container,text1counter,copy1,text1boundry,textsizecontainer);
		setEditDataVal("line_1", copy1);
	});
	$(".line-containers #textarea_2").keyup(function() {
		var counter2 = $("#line-2-container").find("span.current-characters");
		var copy2 = $(this).val();
		if(copy2.indexOf("\n") != -1)
		{
			var numberofchars2 = copy2.length;
			var maxleng=$("#textarea_2").attr("maxlength");
			var copysepa=copy2.split("\n");
			var arraycount=copysepa.length;
			var orgtext=copy2.substr(0,numberofchars2-1);
			var maxchar=$("#line_2_maxabs").val();
			maxchar=parseInt(maxchar);
			maxchar=maxchar+arraycount-1;
			$("#textarea_2").attr("maxlength",maxchar);
			numberofchars2=numberofchars2-arraycount+1;
		}
		else
		{
			var numberofchars2 = copy2.length;
			var maxchar=$("#line_2_maxabs").val();
			$("#textarea_2").attr("maxlength",maxchar);
			var orgtext=copy2.substr(0,numberofchars2-1);
		}
		counter2.html(numberofchars2);
		var text2counter = $("#preview-image").find(".line_2 span.copy");
		var text2container = $("#preview-image").find(".line_2");
		var max_abs=$("#line_2_maxabs").val();
		var text2boundry = $("#preview-image").find(".product_text");
		var textsizecontainer=$("#line_2_textsize");
		updatepreview(text2container,text2counter,copy2,text2boundry,textsizecontainer);
		setEditDataVal("line_2", copy1);
	});
	function updatemaxlength(id)
	{
		if(id=="1")
		{
			var counter1 = $("#line-1-container").find("span.current-characters");
			var lineid=$(".line_1").find("span:last-child").attr("id");
			var spannum_sepa=lineid.split("x");
			var spannum=parseInt(spannum_sepa[1]);
			var orgtext="";
			for (var i=0;i<=spannum;i++)
			{
				var spanid="1tx"+i;
				if(i!=spannum)
				{
					orgtext=orgtext+$('span#'+spanid).html()+"\n";
				}
				else
				{
					orgtext=orgtext+$('span#'+spanid).html();
				}
			}
			$("#textarea_1").val(orgtext);
			if(orgtext.indexOf("\n") != -1)
			{
				var textlength=orgtext.length;
				var copysepa=orgtext.split("\n");
				var arraycount=copysepa.length;
				textlength=textlength-arraycount+1;
				counter1.html(textlength);
			}
		}
		else if(id=="2")
		{
			var counter2 = $("#line-2-container").find("span.current-characters");
			var lineid=$(".line_2").find("span:last-child").attr("id");
			var spannum_sepa=lineid.split("x");
			var spannum=parseInt(spannum_sepa[1]);
			var orgtext="";
			for (var i=0;i<=spannum;i++)
			{
				var spanid="2tx"+i;
				if(i!=spannum)
				{
					orgtext=orgtext+$('span#'+spanid).html()+"\n";
				}
				else
				{
					orgtext=orgtext+$('span#'+spanid).html();
				}
			}
			if(orgtext.indexOf("\n") != -1)
			{
				$("#textarea_2").val(orgtext);
				var textlength=orgtext.length;
				var copysepa=orgtext.split("\n");
				var arraycount=copysepa.length;
				textlength=textlength-arraycount+1;
				counter2.html(textlength);
			}
		}
	}
	$(".line-containers #line_1").keyup(function() {
		var counter1 = $("#line-1-container").find("span.current-characters");
		var copy1 = $(this).val();
		if($("#line_2").length)
		{
		var copy2 = $(".line-containers #line_2").val();
		var numberofchars2 = copy2.length;
		}
		else
			var numberofchars2=0;
		var numberofchars1 = copy1.length;
		counter1.html(numberofchars1);
		var text1counter = $("#preview-image").find(".line_1 span.copy");
		var text1container = $("#preview-image").find(".line_1");
		//var text1counter = $("#preview-image").find(".line_1");
		var max_char_display=$("#line-1-container").find("span.max-characters");
		text1counter.html(copy1);
		var max_recomm=$("#line_1_maxrecom").val();
		var max_abs=$("#line_1_maxabs").val();
		if(numberofchars1>max_recomm)
		{
			if( !$("#over-max").is(":visible"))
				$("#over-max").show("fast");
			counter1.addClass("over-max");
			max_char_display.html(max_abs);
		}
		else
		{
			if( $("#over-max").is(":visible")&&numberofchars2<=max_recomm)
				$("#over-max").hide("fast");
			counter1.removeClass("over-max");
			max_char_display.html(max_recomm);
		}
		if($("#overmaximum").is(":visible")&&numberofchars1<=max_abs&&numberofchars2<=max_abs)
		{
			$("#overmaximum").hide();
		}
		else if($("#overmaximum").is(":visible")&&numberofchars1<=max_abs&&numberofchars2>max_abs)
		{
			$("#lineovermax").html("Line 2's text is");
		}
		var text1boundry = $("#preview-image").find(".product_text");
		setEditDataVal("line_1", copy1);

		compress_text(text1counter, text1boundry, text1container, copy1);
	});
	$(".line-containers #line_2").keyup(function() {
		var counter2 = $("#line-2-container").find("span.current-characters");
		var text2container = $("#preview-image").find(".line_2");
		var copy2 = $(this).val();
		var copy1 = $(".line-containers #line_1").val();
		var numberofchars2 = copy2.length;
		var numberofchars1 = copy1.length;
		counter2.html(numberofchars2);
		var text2counter = $("#preview-image").find(".line_2 span.copy");
		var max_char_display=$("#line-2-container").find("span.max-characters");
		text2counter.html(copy2);
		var max_recomm=$("#line_1_maxrecom").val();
		var max_abs=$("#line_1_maxabs").val();
		if(numberofchars2>max_recomm)
		{
			if( !$("#over-max").is(":visible"))
				$("#over-max").show("fast");
			counter2.addClass("over-max");
			max_char_display.html(max_abs);
		}
		else
		{
			if( $("#over-max").is(":visible")&&numberofchars1<=max_recomm)
				$("#over-max").hide("fast");
			counter2.removeClass("over-max");
			max_char_display.html(max_recomm);
		}
		if($("#overmaximum").is(":visible")&&numberofchars2<=max_abs&&numberofchars1<=max_abs)
		{
			$("#overmaximum").hide();
		}
		else if($("#overmaximum").is(":visible")&&numberofchars2<=max_abs&&numberofchars1>max_abs)
		{
			$("#lineovermax").html("Line 1's text is");
		}
		var text2boundry = $("#preview-image").find(".product_text");
		var text1boundry = $("#preview-image").find(".product_text");
		var text1counter = $("#preview-image").find(".line_1 span.copy");
		var text1container = $("#preview-image").find(".line_1");

		setEditDataVal("line_2", copy2);

		if(copy2!="")
		{
			$("#lineclass").val("twoline");
			updateclass();
		}
		else
		{
			$("#lineclass").val("oneline");
			updateclass();

		}
		compress_text(text1counter, text1boundry, text1container, copy1);
		compress_text(text2counter, text2boundry, text2container, copy2);
	});
	$(".line-containers #sidetext").keyup(function() {
		var counter2 = $("#line-2-container").find("span.current-characters");
		var copy2 = $(this).val();

		if (typeof copy2 !== "string" || copy2.length <= 0) {
			copy2 = "";
		}

		var numberofchars2 = copy2.length;
		counter2.html(numberofchars2);
		var text2counter = $("#preview-image").find("#streetnum p");
		text2counter.html(copy2);
		setEditDataVal("sidetext", copy2);

		var copyNum = $("#sidetext").val();
		var textNumcounter = $("#preview-image #streetnum p");
		var textNumcontainer = $("#preview-image #streetnum");
		var textNumboundry = $("#preview-image #streetnum");
		compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);


	});

	$("#sign_size").change(function(){

		updateclass();

		if ( $('#text_upper').length > 0 ) {

			var checkboxv = $('#text_upper').val();

			if ( checkboxv == "Y" ) {

				$("#custom-product-images p").addClass("capitalize");

				var x4 = $("#custom-product-images").hasClass("x4");
				var x6 = $("#custom-product-images").hasClass("x6");
				var x9 = $("#custom-product-images").hasClass("x9");

				if ( x6 == true || x4 == true ) {
					$("#custom-product-images p.line_1").addClass("capitalizeSizex6");
					$("#custom-product-images p.line_2").addClass("capitalizeSizex6");
					$("#custom-product-images p.line_1").removeClass("capitalizeSizex9");
					$("#custom-product-images p.line_2").removeClass("capitalizeSizex9");
				} else if ( x9 == true ) {
					$("#custom-product-images p.line_1").addClass("capitalizeSizex9");
					$("#custom-product-images p.line_2").addClass("capitalizeSizex9");
					$("#custom-product-images p.line_1").removeClass("capitalizeSizex6");
					$("#custom-product-images p.line_2").removeClass("capitalizeSizex6");
				}

				if ( $("#line_1").length > 0 ) {
					var copy1=$("#line_1").val();
					var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
					var text1counter = $("#preview-image").find(".line_1 span.copy");
					var text1container = $("#preview-image").find(".line_1");
					compress_text(text1counter, text1boundry, text1container, copy1);
				}

				if ( $("#line_2").length > 0 ) {
					var copy2=$("#line_2").val();
					var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
					var text2counter = $("#preview-image").find(".line_2 span.copy");
					var text2container = $("#preview-image").find(".line_2");
					compress_text(text2counter, text2boundry, text2container,copy2);
				}

				if ( $("#suffix_selector").length > 0 ) {
					var copy1 = $("#suffix_selector").val();
					var text1counter = $("#preview-image #suffix-wrap p");
					var text1container = $("#preview-image #suffix-wrap");
					var text1boundry = $("#preview-image #suffix-wrap");
					compress_text_suffix(text1counter, text1boundry, text1container, copy1);
				}

				if ( $("#sidetext").length > 0 ) {
					var copyNum = $("#sidetext").val();
					var textNumcounter = $("#preview-image #streetnum p");
					var textNumcontainer = $("#preview-image #streetnum");
					var textNumboundry = $("#preview-image #streetnum");
					compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);
				}

			}

		}

		var size = $("#sign_size").val();
		var size_sepa = size.split("|");
		var size_value = size_sepa[0].substring(1);
		var sepavalue = size_value.split("x");
		var size_o = sepavalue[0] + "× " + sepavalue[1] + "″";

		if ( $(".vsize").length > 0 ) {
			$(".vsize").val(size_o);
		}

		var color = $("#sign_color").val();
		var max_char = size_sepa[1];
		var max_absolute = size_sepa[2];

		if ( max_char == "" ) {
			max_char = max_absolute;
		}

		if ( $("#line_1_maxrecom").length > 0 ) {
			$("#line_1_maxrecom").val(max_char);
		}

		$("#line_1_maxabs").val(max_absolute);

		if ( $(".line-containers #line_1").length > 0 ) { // TODO: This was broken on the live site.

			var copy1 = $(".line-containers #line_1").val();
			var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
			var text1counter = $("#preview-image").find(".line_1 span.copy");
			var text1container = $("#preview-image").find(".line_1");
			compress_text(text1counter, text1boundry, text1container, copy1);

		} else if ( $(".line-containers #textarea_1").length > 0 ) { // TODO: This was broken on the live site.

			var copy1 = $(".line-containers #textarea_1").val();

			if ( copy1.indexOf("\n") != -1 ) {
				var numberofchars1 = copy1.length;
				var copysepa = copy1.split("\n");
				var arraycount = copysepa.length;
				max_absolute = parseInt(max_absolute);
				max_absolute = max_absolute + arraycount - 1;
				$("#textarea_1").attr("maxlength", max_absolute);
				$("#line_1_maxabs").val(max_absolute);
			}

		}

		var copy2 = "";

		if ( $(".line-containers #line_2").length > 0 ) { // TODO: This was broken on the live site.

			var copy2 = $(".line-containers #line_2").val();
			var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
			var text2counter = $("#preview-image").find(".line_2 span.copy");
			var text2container = $("#preview-image").find(".line_2");
			compress_text(text2counter, text2boundry, text2container, copy2);

		} else if ( $(".line-containers #textarea_2").length > 0 ) { // TODO: This was broken on the live site.

			var copy2 = $(".line-containers #textarea_2").val();

			if ( copy2.indexOf("\n") != -1 ) {
				var numberofchars2 = copy2.length;
				var copysepa2 = copy2.split("\n");
				var arraycount2 = copysepa.length;
				max_absolute = parseInt(max_absolute);
				max_absolute = max_absolute+arraycount2-1;
				$("#textarea_2").attr("maxlength", max_absolute);
				$("#line_2_maxabs").val(max_absolute);
			}

		}

		var counter1 = $("#line-1-container").find("span.current-characters");
		var counter2 = $("#line-2-container").find("span.current-characters");
		var max_counter1 = $("#line-1-container .max-characters");

		if ( !($("#sidetext").length > 0) ) {
			var max_counter2 = $("#line-2-container .max-characters");
		}

		if ( copy1.length > max_char && !counter1.hasClass('over-max') ) {
			counter1.addClass("over-max");
			max_counter1.html(max_absolute);
		} else if ( copy1.length > max_char ) {
			max_counter1.html(max_absolute);
		} else if (copy1.length <= max_char && counter1.hasClass('over-max') ) {
			counter1.removeClass("over-max");
			max_counter1.html(max_char);
		} else if ( copy1.length <= max_char ) {
			max_counter1.html(max_char);
		}

		if ( !($("#sidetext").length > 0) && $(".line-containers #line_2").length > 0 ) { // TODO: This was broken on the live site.

			if ( copy2.length > max_char && !counter2.hasClass('over-max') ) {
				counter2.addClass("over-max");
				max_counter2.html(max_absolute);
			} else if ( copy2.length > max_char ) {
				max_counter2.html(max_absolute);
			} else if ( copy2.length <= max_char && counter2.hasClass('over-max') ) {
				counter2.removeClass("over-max");
				max_counter2.html(max_char);
			} else if ( copy2.length <= max_char ) {
				max_counter2.html(max_char);
			}

		}

		if ( $("#over-max").is(":visible") && !counter1.hasClass('over-max') && !counter2.hasClass('over-max') ) {
			$("#over-max").hide("fast");
		} else if ( !$("#over-max").is(":visible") && (counter1.hasClass('over-max') || counter2.hasClass('over-max')) ) {
			$("#over-max").show("fast");
		}

		$("#line_1").attr("maxlength", max_absolute);

		if ( $(".line-containers #line_2").length > 0 ) { // TODO: This was broken on the live site.
			$("#line_2").attr("maxlength", max_absolute);
		}

		if ( copy1.length > max_absolute || copy2.length > max_absolute ) {

			if ( copy1.length > max_absolute && copy2.length <= max_absolute ) {
				$("#lineovermax").html("Line 1's text is");
			} else if ( copy2.length > max_absolute && copy1.length <= max_absolute ) {
				$("#lineovermax").html("Line 2's text is");
			} else if ( copy1.length > max_absolute && copy2.length > max_absolute ) {
				$("#lineovermax").html("Line 1 & Line 2's texts are");
			}

			$("#maximumallow").html(max_absolute);
			$("#overmaximum").show();

		} else if ( copy1.length <= max_absolute && copy2.length <= max_absolute ) {

			$("#overmaximum").hide();

		}

		var productno = $("#productno").val();
		var p_id = $("input.p_id").eq(0).val();
		var loadpath = $("#loadpath").val();

		if ( $("#prefix_selector").length > 0 ) {
			var prefix = $("#prefix_selector").val();
		} else {
			var prefix = "";
		}

		if ( $("#suffix_selector").length > 0 ) {
			var suffix = $("#suffix_selector").val();
			var suffixcounter = $("#preview-image #suffix-wrap p");
			var suffixcontainer = $("#preview-image #suffix-wrap");
			var suffixboundry = $("#preview-image #suffix-wrap");
			compress_text_suffix(suffixcounter, suffixboundry, suffixcontainer, suffix);
		} else {
			var suffix = "";
		}

		if ( $("#position_selector").length > 0 ) {
			var position = $("#position_selector").val();
		} else {
			var position = "";
		}

		if ( $("#sign_background").length > 0 ) {
			var background = $("#sign_background").val();
		} else {
			var background = "";
		}

		if ( $("#sign_font").length > 0 ) {
			var font = $("#sign_font").val();
		} else {
			var font = "";
		}

		if ( $("#text_colorddl").length > 0 ) {
			var textcolor = $("#text_colorddl").val();
		} else {
			var textcolor = "";
		}

		if ( $("#arrow_selector").length > 0 ) {
			var arrow = $("#arrow_selector").val();
		} else {
			var arrow = "";
		}

		if ( $("#arrowcolor_selector").length > 0 ) {
			var arrowcolor = $("#arrowcolor_selector").val();
		} else {
			var arrowcolor = "";
		}

		if ( $("#text_upper").length > 0 ) {
			var textupper = $("#text_upper").val();
		} else {
			var textupper = "";
		}

		var mounting = $("#mounting_option").val();
		var fileid = getEditDataVal("uploadfileid");

		if ( $("#line_1_textsize").length > 0 ) {
			var textsize = $("#line_1_textsize").val();
		} else {
			var textsize = "";
		}

		if ( $("#line_2_textsize").length > 0 ) {
			var textsize2 = $("#line_2_textsize").val();
		} else {
			var textsize2 = "";
		}

		var special_comment = $("#special-instructions-text").val();

		if ( $("#sidetext").length > 0 ) {

			var sidetext = $("#sidetext").val();

			$(".viewmaterial").load(
				loadpath,
				{
					'product_no':                        productno,
					'size':                              size_value,
					'pid':                               p_id,
					'line_1':                            copy1,
					'line_2':                            copy2,
					'prefix':                            prefix,
					'suffix':                            suffix,
					'position':                          position,
					'color':                             color,
					'font':                              font,
					'mounting':                          mounting,
					'fileid':                            fileid,
					'textupper':                         textupper,
					'sidetext':                          sidetext,
					'background':                        background,
					'special_comment':                   special_comment,
					'editdata':                          getEditDataJSON(),
					'stid':                              addToCartOverrides.stid,
					'landing_id':                        addToCartOverrides.landing_id,
					'subcategory_id':                    addToCartOverrides.subcategory_id,
					'source_product_id':                 addToCartOverrides.source_product_id,
					'source_product_recommendation_id':  addToCartOverrides.source_product_recommendation_id,
					'source_accessory_familyProduct_id': addToCartOverrides.source_accessory_familyProduct_id,
					'source_installation_accessory_id':  addToCartOverrides.source_installation_accessory_id,
					'source_landing_product_id':         addToCartOverrides.source_landing_product_id,
					'source_subcategory_product_id':     addToCartOverrides.source_subcategory_product_id
				},
				function (value) {
					$('.add-to-cart-confirmation a.continue-shopping').click(function () {
						$('.add-to-cart-confirmation.showme').fadeOut("fast");
						return false;
					});
					prepareAddToCartForms();
				}
			);

			var copyNum = $("#sidetext").val();
			var textNumcounter = $("#preview-image #streetnum p");
			var textNumcontainer = $("#preview-image #streetnum");
			var textNumboundry = $("#preview-image #streetnum");
			compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);

		} else {

			$(".viewmaterial").load(
				loadpath,
				{
					'product_no':                        productno,
					'size':                              size_value,
					'pid':                               p_id,
					'line_1':                            copy1,
					'line_2':                            copy2,
					'prefix':                            prefix,
					'suffix':                            suffix,
					'position':                          position,
					'color':                             color,
					'font':                              font,
					'mounting':                          mounting,
					'fileid':                            fileid,
					'textupper':                         textupper,
					'textcolor':                         textcolor,
					'arrow':                             arrow,
					'arrowcolor':                        arrowcolor,
					'textsize':                          textsize,
					'textsize2':                         textsize2,
					'background':                        background,
					'special_comment':                   special_comment,
					'editdata':                          getEditDataJSON(),
					'stid':                              addToCartOverrides.stid,
					'landing_id':                        addToCartOverrides.landing_id,
					'subcategory_id':                    addToCartOverrides.subcategory_id,
					'source_product_id':                 addToCartOverrides.source_product_id,
					'source_product_recommendation_id':  addToCartOverrides.source_product_recommendation_id,
					'source_accessory_familyProduct_id': addToCartOverrides.source_accessory_familyProduct_id,
					'source_installation_accessory_id':  addToCartOverrides.source_installation_accessory_id,
					'source_landing_product_id':         addToCartOverrides.source_landing_product_id,
					'source_subcategory_product_id':     addToCartOverrides.source_subcategory_product_id
				},
				function (value) {
					$('.add-to-cart-confirmation a.continue-shopping').click(function () {
						$('.add-to-cart-confirmation.showme').fadeOut("fast");
						return false;
					});
					prepareAddToCartForms();
				}
			);

			var copyNum = $("#sidetext").val();
			var textNumcounter = $("#preview-image #streetnum p");
			var textNumcontainer = $("#preview-image #streetnum");
			var textNumboundry = $("#preview-image #streetnum");
			compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);

		}

	});

	$("#special-instructions-text").bind('keyup', function() {
	  		var special_comment=$(this).val();
			setEditDataVal("special_comment", special_comment);
	});


	function updateclass() {

		if ( $("#customproduct").length > 0 ) {
			var custom_v = " " + $("#customproduct").val();
		} else {
			var custom_v = "";
		}

		var logoclass = $("#logoclass").val();

		if ( $("#sign_size").length > 0 ) {
			var size = $("#sign_size").val();
			var size_sepa = size.split("|");
			var size_value = " " + size_sepa[0];
		} else {
			var size_value = "";
		}

		var color_v = $("#sign_color").val();
		var color_sepa = color_v.split("/");
		var color = color_sepa[0];

		if ( $("#text_colorddl").length > 0 ) {
			var textcolor_v = $("#text_colorddl").val();
			var textcolor = " " + textcolor_v + "-t";
		} else {
			var textcolor = "";
		}

		var class_arrow = "";
		var class_prefix = "";
		var class_suffix = "";

		if ( $("#position_selector").length > 0 ) {
			var position_v = $("#position_selector").val();
			var position = " " + position_v.substring(0, 1);
		} else {
			var position = "";
		}

		if ( $("#sign_font").length > 0 ) {
			var font_v = $("#sign_font").val();
			var font = " " + font_v.replace(/ /g, '_');
		} else {
			var font = " Highway";
		}

		if ( $("#numclass").length > 0 ) {
			var numclass = " None-p " + $("#numclass").val();
		} else {
			var numclass = "";
		}

		if ( $("#prefix_selector").length > 0 ) {

			var prefix = $("#prefix_selector").val();

			if ( prefix.indexOf("Arrow") != -1 ) {
				var prefix_sepa = prefix.split(" ");
				var class_arrow = " " + prefix_sepa[1] + "-" + prefix_sepa[0].substring(0, 1) + "-p";
			} else if ( prefix == 'NONE' ) {
				if ( numclass == "" ) {
					class_prefix = " None" + "-p";
				} else {
					class_prefix = "";
				}
			} else {
				if ( $("#numclass").length > 0 ) {
					var numclass = " " + $("#numclass").val();
				} else {
					var numclass = "";
				}
			}

		}

		if ( $("#arrow_selector").length > 0 ) {
			var arrow = $("#arrow_selector").val();
			if ( arrow == "NONE" ) {
				var signarrow_v = " none-a";
			} else {
				var signarrow_v = " " + arrow.replace(/ /g, '_');
			}
		} else {
			var signarrow_v = "";
		}

		if ( $("#arrowcolor_selector").length > 0 ) {
			var arrowcolorclass = " a-" + $("#arrowcolor_selector").val();
		} else {
			var arrowcolorclass = "";
		}

		if ( $("#suffix_selector").length > 0 ) {
			var suffix = $("#suffix_selector").val();
			if ( suffix.indexOf("Arrow") != -1 ) {
				var suffix_sepa = suffix.split(" ");
				var class_arrow = " " + suffix_sepa[1] + "-" + suffix_sepa[0].substring(0, 1) + "-s";
			} else if ( suffix == 'NONE' ) {
				class_suffix = " None" + "-s";
			}
		}

		if ( $("#lineclass").length > 0 ) {
			var lineclass = " " + $("#lineclass").val();
		} else {
			var lineclass = "";
		}

		if ( $("#sign_background").length > 0 ) {
			var class_background = " " + $("#sign_background").val();
		} else {
			var class_background = "";
		}

		if ( $("#text_upper").length > 0 ) {
			var checkbxv = $("#text_upper").val();
			if ( checkbxv == "Y" ) {
				var uppertext = " textupper";
			} else {
				var uppertext = "";
			}
		} else {
			var uppertext = "";
		}

		var array_of_attributes = logoclass + custom_v + size_value + " " + color + textcolor + signarrow_v + position + font + class_prefix + class_suffix + class_arrow + arrowcolorclass + numclass + class_background + lineclass + uppertext;
		$('#custom-product-images').attr("class", array_of_attributes);

	}

	function refillHiddenValues() {

		if ($("#textarea_1").length > 0) {
			setEditDataVal("line_1", $("#textarea_1").val());
		} else if ($(".line-containers #line_1").length > 0) {
			setEditDataVal("line_1", $(".line-containers #line_1").val());
		}

		setEditDataVal("line_2", $(".line-containers #line_2").val());

		setEditDataVal("sidetext", $(".line-containers #sidetext").val());

		setEditDataVal("prefix", $("#prefix_selector").val());

		setEditDataVal("suffix", $("#suffix_selector").val());

		setEditDataVal("position", $("#position_selector").val());

		setEditDataVal("sign_font", $("#sign_font").val());

		setEditDataVal("sign_color", $("#sign_color").val());

		setEditDataVal("sign_background", $("#sign_background").val());

		setEditDataVal("textupper", $('#text_upper').val());

		setEditDataVal("special_comment", $("#special-instructions-text").val());

		$('.vmounting').val($("#mounting_option").val());
	}

	function prepareAddToCartForms () {

		// Turn off global Product class submit handler, then prepare streetsign-specific validation...
		$("div.viewmaterial form.addtocart").off("submit").on("submit", function (event) {

			var errors = [], errorMessage, uploadFileId = getEditDataVal("uploadfileid"), $errordialog;

			// Prevent default form submission.
			event.preventDefault();

			// Validate the sign text length.
			if ( $("#line_1").val().length === 0 ) {
				errors.push("enter a street name.");
			} else if ( $("#overmaximum").length > 0 && $("#overmaximum").is(':visible') ) {
				errors.push("reduce the amount of text.");
			}

			// Validate that an image was uploaded (if required).
			if ( $("#streetsign-upload-required").val() === "Y" && (uploadFileId === 0 || uploadFileId === "0" || uploadFileId === "" || uploadFileId === null || typeof uploadFileId === "undefined") ) {
				errors.push("upload an image");
			}

			// If there is an error...
			if ( errors.length > 0 ) {

				// Compose the error message.
				errorMessage = "Please ";
				if ( errors.length === 1 ) {
					errorMessage += errors[0];
				} else if ( errors.length === 2 ) {
					errorMessage += errors[0] + " and " + errors[1];
				} else {
					errorMessage += errors.slice(0, -1).join(", ") + ", and " + errors[errors.length - 1];
				}
				errorMessage += " before adding this item to your cart.";

				// Find this form's error dialog.
				$errordialog = $(this).parent().find("div.add-to-cart-requiretext");

				// Insert the text and display the dialog.
				$errordialog.text(errorMessage).addClass("showme").fadeIn("fast");

				// Set up an event to hide the error dialog the next time anything is clicked.
				$(document).one("click", function () {
					$errordialog.removeClass("showme").fadeOut("fast");
				});

				// Prevent global.js from adding the product to the cart.
				event.stopImmediatePropagation();

			}

			// Return true if no errors were detected, false if errors were detected.
			return errors.length === 0;

		});

		// Prepare the forms using the standard site-wide Product class (ignoring accessories).
		// This will re-attach the Product class submit handler that was turned off earlier in this function.
		window.ss.prepareProducts(true);

	}

	function focus_events($form_parent) {
			$form_parent.live("focusin",function(){
				$("#line1outer").addClass("focused");
				$("#line2outer").addClass("focused");
			});

			$form_parent.live("focusout",function(){
				$("#line1outer").removeClass("focused");
				$("#line2outer").removeClass("focused");
			});
	}

	$("#show-inst a").click(function(){
		$("#upload-instructions").show();
		$("#show-inst").hide();
		return false;
	});
	$("#hide-inst a").click(function(){
		$("#upload-instructions").hide();
		$("#show-inst").show();
		return false;
	});
	$("a.subgroup_content").fancybox({
	'imageScale': true,
	'overlayShow' : true,
	'zoomSpeedIn' : 500,
	'zoomSpeedOut' : 500,
	'autoDimensions' : false,
	'width' : 330,
	'height' : '30%'
	});
	$("#upload-div .choose-file").fancybox({
	'imageScale': true,
	'overlayShow' : true,
	'zoomSpeedIn' : 500,
	'zoomSpeedOut' : 500,
	'autoDimensions' : false,
	'width' : 400,
	'height' : 200
	});
	$("#textsmaller").click(function(){
		var textsize=$("#line_1_textsize").val();
		textsize=parseInt(textsize);
		if((textsize-1)>=6)
		{
			textsize=textsize-1;
			$("#line_1_textsize").val(textsize);
			$(".vfontsize").val(textsize);
		}
		var text1container=$("#preview-image").find(".line_1");
		var text1counter=$("#preview-image").find(".line_1 span.copy");
		var copy1=$("#textarea_1").val();
		var text1boundry=$("#preview-image").find(".product_text");
		updatepreviewchangetextsize(text1container,text1counter,copy1,text1boundry,$("#line_1_textsize"));
		updateclass();
	});
	$("#textlarger").click(function(){
		var textsize=$("#line_1_textsize").val();
		textsize=parseInt(textsize);
		if((textsize+1)<=70)
		{
			textsize=textsize+1;
			$("#line_1_textsize").val(textsize);
			$(".vfontsize").val(textsize);
		}
		var text1container=$("#preview-image").find(".line_1");
		var text1counter=$("#preview-image").find(".line_1 span.copy");
		var copy1=$("#textarea_1").val();
		var text1boundry=$("#preview-image").find(".product_text");
		updatepreviewchangetextsize(text1container,text1counter,copy1,text1boundry,$("#line_1_textsize"));
		updateclass();
	});
	$("#textsmaller2").click(function(){
		var textsize=$("#line_2_textsize").val();
		textsize=parseInt(textsize);
		if((textsize-1)>=6)
		{
			textsize=textsize-1;
			$("#line_2_textsize").val(textsize);
			$(".vfontsize2").val(textsize);
		}
		var text2container=$("#preview-image").find(".line_2");
		var text2counter=$("#preview-image").find(".line_2 span.copy");
		var copy2=$("#textarea_2").val();;
		var text2boundry=$("#preview-image").find(".product_text");
		updatepreviewchangetextsize(text2container,text2counter,copy2,text2boundry,$("#line_2_textsize"));
		updateclass();
	});
	$("#textlarger2").click(function(){
		var textsize=$("#line_2_textsize").val();
		textsize=parseInt(textsize);
		if((textsize+1)<=70)
		{
			textsize=textsize+1;
			$("#line_2_textsize").val(textsize);
			$(".vfontsize2").val(textsize);
		}
		var text2container=$("#preview-image").find(".line_2");
		var text2counter=$("#preview-image").find(".line_2 span.copy");
		var copy2=$("#textarea_2").val();;
		var text2boundry=$("#preview-image").find(".product_text");
		updatepreviewchangetextsize(text2container,text2counter,copy2,text2boundry,$("#line_2_textsize"));
		updateclass();
	});
	$('#text_upper').click(function()
	{
		var checkboxv=$(this).val();


		if(checkboxv=="N") // TODO: This is strictly equal (===) on the live site.
		{

			$(this).val("Y");
			setEditDataVal("textupper", "Y");
			updateclass();

			$("#custom-product-images p").addClass("capitalize");
			var x4 = $("#custom-product-images").hasClass("x4");
			var x6 = $("#custom-product-images").hasClass("x6");
				var x9 = $("#custom-product-images").hasClass("x9");
			if (x6 == true || x4 == true){
				$("#custom-product-images p.line_1").addClass("capitalizeSizex6");
				$("#custom-product-images p.line_2").addClass("capitalizeSizex6");
			}else if (x9 == true){
				$("#custom-product-images p.line_1").addClass("capitalizeSizex9");
				$("#custom-product-images p.line_2").addClass("capitalizeSizex9");
			}

			if($("#line_1").length>0)
			{
				var copy1=$("#line_1").val();
				var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
				var text1counter = $("#preview-image").find(".line_1 span.copy");
				var text1container = $("#preview-image").find(".line_1");
				compress_text(text1counter, text1boundry, text1container, copy1);
			}
			if($("#line_2").length>0)
			{
				var copy2=$("#line_2").val();
				var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
				var text2counter = $("#preview-image").find(".line_2 span.copy");
				var text2container = $("#preview-image").find(".line_2");
				compress_text(text2counter, text2boundry, text2container,copy2);
			}

			var copy1 = $("#suffix_selector").val();
			var text1counter = $("#preview-image #suffix-wrap p");
			var text1container = $("#preview-image #suffix-wrap");
			var text1boundry = $("#preview-image #suffix-wrap");
			compress_text_suffix(text1counter, text1boundry, text1container, copy1);

			var copyNum = $("#sidetext").val();
			var textNumcounter = $("#preview-image #streetnum p");
			var textNumcontainer = $("#preview-image #streetnum");
			var textNumboundry = $("#preview-image #streetnum");
			compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);

		}
		else
		{
			$(this).val("N");
			setEditDataVal("textupper", "N");
			updateclass();

			$("#custom-product-images p").removeClass("capitalize");
			var x4 = $("#custom-product-images").hasClass("x4");
			var x6 = $("#custom-product-images").hasClass("x6");
				var x9 = $("#custom-product-images").hasClass("x9");
			if (x6 == true || x4 == true){
				$("#custom-product-images p.line_1").removeClass("capitalizeSizex6");
				$("#custom-product-images p.line_2").removeClass("capitalizeSizex6");
			}else if (x9 == true){
				$("#custom-product-images p.line_1").removeClass("capitalizeSizex9");
				$("#custom-product-images p.line_2").removeClass("capitalizeSizex9");
			}

			if($("#line_1").length>0)
			{
				var copy1=$("#line_1").val();
				var text1boundry = $("#preview-image").find(".vertically-aligned-copy1");
				var text1counter = $("#preview-image").find(".line_1 span.copy");
				var text1container = $("#preview-image").find(".line_1");
				compress_text(text1counter, text1boundry, text1container, copy1);
			}
			if($("#line_2").length>0)
			{
				var copy2=$("#line_2").val();
				var text2boundry = $("#preview-image").find(".vertically-aligned-copy2");
				var text2counter = $("#preview-image").find(".line_2 span.copy");
				var text2container = $("#preview-image").find(".line_2");
				compress_text(text2counter, text2boundry, text2container,copy2);
			}

			var copy1 = $("#suffix_selector").val();
			var text1counter = $("#preview-image #suffix-wrap p");
			var text1container = $("#preview-image #suffix-wrap");
			var text1boundry = $("#preview-image #suffix-wrap");
			compress_text_suffix(text1counter, text1boundry, text1container, copy1);

			var copyNum = $("#sidetext").val();
			var textNumcounter = $("#preview-image #streetnum p");
			var textNumcontainer = $("#preview-image #streetnum");
			var textNumboundry = $("#preview-image #streetnum");
			compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);

		}

	});

	$(".x4 #prefix-wrap p").css({
		scaleX: .5,
		origin: [0, 0]

		}).css("position","absolute");

	$(".x6 #prefix-wrap p").css({
		scaleX: .5,
		origin: [0, 0]

		}).css("position","absolute");


	$(".x9 #prefix-wrap p").css({
		scaleX: .5,
		origin: [0, 0]

		}).css("position","absolute");




	function fontTestIEx6Suf(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 5.5; break;
					case "Arial": var textComp = 5; break;
					case "Brush": var textComp = 6; break;
					case "Century": var textComp = 5; break;
					case "Clarendon": var textComp = 5.5; break;
					case "Futura": var textComp = 5; break;
					case "Highway": var textComp = 3.5; break;
					case "Swiss": var textComp = 4.5; break;
					case "Tekton": var textComp = 4.5; break;
					case "Times_New_Roman": var textComp = 4.85; break;
					case "Zapf": var textComp = 4.5; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestIEx6SufUpper(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 5.5; break;
					case "Arial": var textComp = 5.5; break;
					case "Brush": var textComp = 7; break;
					case "Century": var textComp = 5.5; break;
					case "Clarendon": var textComp = 6; break;
					case "Futura": var textComp = 5; break;
					case "Highway": var textComp = 3.5; break;
					case "Swiss": var textComp = 4.5; break;
					case "Tekton": var textComp = 4.5; break;
					case "Times_New_Roman": var textComp = 5.25; break;
					case "Zapf": var textComp = 4.5; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestIEx9Suf(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 5.5; break;
					case "Arial": var textComp = 5; break;
					case "Brush": var textComp = 6; break;
					case "Century": var textComp = 5; break;
					case "Clarendon": var textComp = 5.5; break;
					case "Futura": var textComp = 5; break;
					case "Highway": var textComp = 3.65; break;
					case "Swiss": var textComp = 4.5; break;
					case "Tekton": var textComp = 4.5; break;
					case "Times_New_Roman": var textComp = 4.85; break;
					case "Zapf": var textComp = 4.5; break;
					}
			   }
		 }
		return textComp;
	}

	function fontTestIEx9SufUpper(){
		var classList = $("#custom-product-images").attr('class').split(/\s+/);

		for (i = 0; i < classList.length; i++) {
			   if(classList[i].length > 0){

				   switch (classList[i]) {
					case "Algerian": var textComp = 5.5; break;
					case "Arial": var textComp = 5.5; break;
					case "Brush": var textComp = 7; break;
					case "Century": var textComp = 5.5; break;
					case "Clarendon": var textComp = 6; break;
					case "Futura": var textComp = 5; break;
					case "Highway": var textComp = 3.95; break;
					case "Swiss": var textComp = 4.5; break;
					case "Tekton": var textComp = 4.5; break;
					case "Times_New_Roman": var textComp = 5.25; break;
					case "Zapf": var textComp = 4.5; break;
					}
			   }
		 }


		return textComp;
	}




	function textWidthSuffix(textcontainer,text){

        var calc = '<span id="tempspan" style="display:none">' + text + '</span>';
        textcontainer.append(calc);
        var widthOriginal = textcontainer.find('span:last').width();
        var x6 = $("#custom-product-images").hasClass("x6");
        var x4 = $("#custom-product-images").hasClass("x4");
        var x9 = $("#custom-product-images").hasClass("x9");
        var textUpper = $("#custom-product-images").hasClass("textupper");

        if ($.support.cssFloat != true) {
            if (x6 == true || x4 == true){
                if (textUpper != true){
                    var compressRate = fontTestIEx6Suf();
                }else {
                    var compressRate = fontTestIEx6SufUpper();
                }

            }else if (x9 == true){
                if (textUpper != true){
                    var compressRate = fontTestIEx9Suf();
                }else {
                    var compressRate = fontTestIEx9SufUpper();
                }
            }

        }else {if (x6 == true || x4 == true){var compressRate = 3; }
				else{var compressRate = 2;}

		}


        var width = widthOriginal * compressRate;

        textcontainer.find('span:last').remove();
        return width;
    };

	function compress_text_suffix(element_to_compress, boundry,textcontainer,text)
   {


		var a1= element_to_compress.width();
		var a2=textWidthSuffix(textcontainer,text);
		if(a1>a2)
			var a=a1;
		else
			var a=a2;
		var b =boundry.width();
		var c = b/a;
		if(c>=1)
		{
			c=1;

			textcontainer.css("position","absolute");
			element_to_compress.css("position","absolute");
			element_to_compress.css({
				scaleX: 1,
				origin: [0, 0]
			});


		}
		else
		{
			c=Math.round(c * 100)/100;
			textcontainer.css("position","absolute");
			element_to_compress.css("position","absolute");
			element_to_compress.css({
				scaleX: c,
				origin: [0, 0]

			});
		}
		$(".compress_suffix").val(c);


	};

	function textWidthStreetnum(textcontainer,text){

		var calc = '<span id="tempspan" style="display:none">' + text + '</span>';
		textcontainer.append(calc);

		var widthOriginal = textcontainer.find('span:last').width();
		var x6 = $("#custom-product-images").hasClass("x6");
		var x4 = $("#custom-product-images").hasClass("x4");
		var x9 = $("#custom-product-images").hasClass("x9");
		var textUpper = $("#custom-product-images").hasClass("textupper");

		if ($.support.cssFloat != true) {
			if (x6 == true || x4 == true){
				if (textUpper != true){
					var compressRate = fontTestIEx6Suf();
				}else {
					var compressRate = fontTestIEx6SufUpper();
				}

			}else if (x9 == true){
				if (textUpper != true){
					var compressRate = fontTestIEx9Suf();
				}else {
					var compressRate = fontTestIEx9SufUpper();
				}
			}

		}else {var compressRate = 2;}

		var width = widthOriginal * compressRate;

		textcontainer.find('span:last').remove();

		return width;
	};




	function compress_text_streetnum(element_to_compress, boundry,textcontainer,text){
	   	var a1= element_to_compress.width();
		var a2=textWidthStreetnum(textcontainer,text);
		if(a1>a2)
			var a=a1;
		else
			var a=a2;
		var b =boundry.width();
		var c = b/a;
		if(c>=1)
		{
			c=1;

			textcontainer.css("position","absolute");
			element_to_compress.css({
				scaleX: 1,
				origin: [0, 0]
			});
			element_to_compress.css("position","absolute");

		}
		else
		{
			c=Math.round(c * 100)/100;
			textcontainer.css("position","absolute");
			element_to_compress.css("position","absolute");
			element_to_compress.css({
				scaleX: c,
				origin: [0, 0]

			});
		}
		$(".compress_streetnum").val(c);
	}
	function compress_text_initialsuffix(element_to_compress, boundry,textcontainer,text)
   {
		var c;

		if($(".compress_suffix").length>0)
		{
			c=$(".compress_suffix").val();
		}
		if(c<1)
		{
			element_to_compress.css("position","absolute");
			textcontainer.css("position", "absolute");
		}
		element_to_compress.css({
		scaleX: c,
		origin: [0, 0]

		});
	}
	function compress_text_initialStreetNum(element_to_compress, boundry,textcontainer,text)
   {
		var c;

		if($(".compress_streetnum").length>0)
		{
			c=$(".compress_streetnum").val();
		}
		if(c<1)
		{
			element_to_compress.css("position","absolute");
			textcontainer.css("position", "absolute");
		}
		element_to_compress.css({
		scaleX: c,
		origin: [0, 0]

		});
	}
	function compress_text_initialline(element_to_compress, boundry,textcontainer,text)
   {
		var c;

		if($(".compress_1").length>0&&textcontainer.hasClass("line_1"))
		{
			c=$(".compress_1").val();
		}
		if($(".compress_2").length>0&&textcontainer.hasClass("line_2"))
		{
			c=$(".compress_2").val();
		}
		if(c<1)
		{
			element_to_compress.css("position","absolute");
			textcontainer.css("position", "absolute");
		}
		element_to_compress.css({
		scaleX: c,
		origin: [0, 0]

		});
	}
	function initialLineoneWidth() {
		var copy1 = $("#line_1").val();
		var text1counter = $("#preview-image").find(".line_1 span.copy");
		var text1container = $("#preview-image").find(".line_1");
		var text1boundry = $("#preview-image").find(".product_text");
	  compress_text_initialline(text1counter, text1boundry, text1container, copy1);
  };
  function initialLinetwoWidth() {
	  var copy2 = $("#line_2").val();
	  var text2counter = $("#preview-image").find(".line_2 span.copy");
	  var text2container = $("#preview-image").find(".line_2");
	  var text2boundry = $("#preview-image").find(".product_text");
	  compress_text_initialline(text2counter, text2boundry, text2container, copy2);
  };
  function initialSuffixWidth() {
	  var copy1 = $("#suffix_selector").val();
		var text1counter = $("#preview-image #suffix-wrap p");
		var text1container = $("#preview-image #suffix-wrap");
		var text1boundry = $("#preview-image #suffix-wrap");
		compress_text_suffix(text1counter, text1boundry, text1container, copy1);


		var copyNum = $("#sidetext").val();
		var textNumcounter = $("#preview-image #streetnum p");
		var textNumcontainer = $("#preview-image #streetnum");
		var textNumboundry = $("#preview-image #streetnum");
		compress_text_streetnum(textNumcounter, textNumboundry, textNumcontainer, copyNum);
	};
	function initialSuffixWidthupload() {
		var copy1 = $("#suffix_selector").val();
		var text1counter = $("#preview-image #suffix-wrap p");
		var text1container = $("#preview-image #suffix-wrap");
		var text1boundry = $("#preview-image #suffix-wrap");
		compress_text_initialsuffix(text1counter, text1boundry, text1container, copy1);
		var copyNum = $("#sidetext").val();
		var textNumcounter = $("#preview-image #streetnum p");
		var textNumcontainer = $("#preview-image #streetnum");
		var textNumboundry = $("#preview-image #streetnum");
		compress_text_initialStreetNum(textNumcounter, textNumboundry, textNumcontainer, copyNum);
	};
	if($("#uploadinitial").val()=="Y")
	{
		$("#uploadinitial").val("N");
		if($('#text_upper').val()=="Y")
		{
			$("#custom-product-images p").addClass("capitalize");
			var x4 = $("#custom-product-images").hasClass("x4");
			var x6 = $("#custom-product-images").hasClass("x6");
				var x9 = $("#custom-product-images").hasClass("x9");
			if (x6 == true || x4 == true){
				$("#custom-product-images p.line_1").addClass("capitalizeSizex6");
				$("#custom-product-images p.line_2").addClass("capitalizeSizex6");
			}else if (x9 == true){
				$("#custom-product-images p.line_1").addClass("capitalizeSizex9");
				$("#custom-product-images p.line_2").addClass("capitalizeSizex9");
			}
		}

		initialSuffixWidthupload();
		initialLineoneWidth();
		initialLinetwoWidth();
	}
	else
		initialSuffixWidth();

});
