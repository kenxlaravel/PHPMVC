$(document).ready(function() {

if ((screen.height>=770))
{
	$("body").addClass("high-res");

} else {

	$("body").addClass("low-res");

};


if ($.fn.browserTouchSupport.touches) {
	$("body").addClass("touch-enabled");

} else {
	$("body").addClass("not-touch-enabled");

};

if ($.layout.name != 'webkit') {
	$("#landing-page .error-message").fadeIn('fast');
};


/*$("input.phone-input").mask("(999)999-9999");*/

$.ajaxSetup({ cache: false });

var ajax_load_large = "<img src='/new_images/ajaxloader/ajax-loader-bw-large.gif' alt='loading...' />";
var ajax_load_small_orange = "<img src='/new_images/ajaxloader/ajax-loader-small-orange.gif' alt='loading...' />";


/*//Flash Tutorial
$(".msie6 .remove-for-ie").remove();
$(".msie7 .remove-for-ie").remove();



$("a#watch-tutorial-button[rel]").overlay({

	 mask: {
        color: '#333',
        loadSpeed: 200,
        opacity: 0.5
    },
     closeOnClick: false
 });*/

/*!Global Validation Settings*/
jQuery.validator.setDefaults({

	/*Set container for error elements */
	errorElement: "span",
	wrapper: "cite",


	invalidHandler: function(e, validator) {
	$('#place-order-button-wrap').removeClass('placing-order');
	var errors = validator.numberOfInvalids();
	if (errors) {
	var message = errors == 1
	? 'You missed 1 field. It has been highlighted below'
	: 'You missed ' + errors + ' required fields.  They have been highlighted on the form.';
	$("div.error-message span").html(message);
	$("div.error-message").show();

	} else {
	$("div.error-message").hide();
	}
	},

	rules: {
		brimar_card_number: {
				maxlength: 10
			}
	},

	messages: {

		shippingmethod: {
			required: "Please choose a shipping method"
		},

		payment : {
		required : "Please select a payment method"
		},

		email : {
		required : "Please enter your email address"
		},

		username : {
		required : "Please enter a username, this is usually your email address"
		},

		userpassword: {
		required : "Please enter your password"
		},

		createpassword: {
		required : "Please create a password"
		},

		confirmpassword: {
		required : "Please enter your password again",
		equalTo : "Please enter the same password again"
		},

		shipfirstname : {
		required : "Please enter your first name"
		},

		name : {
		required : "Please enter your name"
		},

		firstname : {
		required : "Please enter your first name"
		},

		shiplastname : {
		required : "Please enter your last name"
		},

		lastname : {
		required : "Please enter your last name"
		},

		shipphonenumber : {
		required : "Please enter your phone number"
		},

		phonenumber : {
		required : "Please enter your phone number"
		},

		shipaddress1 : {
		required : "Please enter your shipping address"
		},

		address1 : {
		required : "Please enter your billing address"
		},

		billaddress1 : {
		required : "Please enter your billing address"
		},

		shipcity : {
		required : "Please enter your city"
		},

		city2 : {
		required : "Please enter your city"
		},

		billcity : {
		required : "Please enter your city"
		},

		sstate : {
		required : "Please choose a state"
		},

		state : {
		required : "Please choose a state"
		},

		shipzip : {
		required : "Please enter your zip code"
		},

		billzip : {
		required : "Please enter your zip code"
		},

		zipcode : {
		required : "Please enter your zip code"
		},

		brimar_card_number : {
		required: "Please enter your account number",
		maxlength: "The maximum amount of characters is 10. Ex. ABCDEFN001"
		},

		credit_card_number : {
		required : "Please enter your credit card number",
		creditcard : "Please enter a valid credit card number"
		},

		CCExpiresMonth: {
			required: "Please select a expiration date"
		},

		CCExpiresYear: {
			required: "Please select a expiration date"
		},

		security_code : {
		required : "Please enter your security code"
		},

		brimar_security_number : {
		required : "Please enter your security code"
		},

		shippingmethod: {
		required: "Please choose a shipping method"
		},

		payment : {
		required : "Please choose a payment method"
		},

		message: {
		required : "Please enter a message"
		},

		order_number: {
		required : "Please enter your order number"
		},

		credit_card_type : {
		required : "Please choose your credit card type"
		},

		shipphonenumber : {
		required: "Please enter your phone number",
		minlength: 10,
		phoneUS: true
		},

		phonenumber : {
		required : "Please enter your phone number",
		minlength: 10,
		phoneUS: true
		}

},

groups: {
	expirationdate: "CCExpiresMonth CCExpiresYear",
	shipphonenumber: "shipphonenumber",
	billphonenumber: "phonenumber"

},

 errorPlacement: function(error, element) {
 	var parentElement = element.parent();
            if ( element.is(":radio") )
                 error.prependTo( element.parents("div#shipping-options"));
             else if(element.attr("name") == "CCExpiresMonth" || element.attr("name") == "CCExpiresYear")
             	error.appendTo(parentElement).addClass("phone-error");
             else
                error.insertAfter(element);

 			}
});



/*!Feedback Form Page Slide*/


$('form#site-feedback').validate({

invalidHandler: function(e, validator) {
var errors = validator.numberOfInvalids();
if (errors) {
var message = errors == 1
	? 'You missed 1 field. It has been highlighted below'
	: 'You missed ' + errors + ' required fields.  They have been highlighted below';
$("div#feedback-error-message span").html(message);
$("#feedback-intro").hide();
$("div#feedback-error-message").show();

} else {
$("div#feedback-error-message").hide();
$("#feedback-intro").show();
}
},
rules: {
    email: {
      required: true,
      email: true
    }
},
submitHandler: function(form) {
$(form).ajaxSubmit({
	beforeSubmit: showLargeLoadGraphic,
	success: showThankyouMessage

});}});




$("a#feedback").click(function(){

var feedbackForm = $("#feedback-form");
var closeFeedback = $("a.close-siderail")

if (Modernizr.csstransitions) {

	$(feedbackForm).css("display", "block").bind('webkitAnimationEnd', function() {
		$(closeFeedback).click(function(){
			$(feedbackForm).css("-webkit-animation-name","slideouttoright").bind('webkitAnimationEnd', function() {
				$(feedbackForm).css("display", "none").css("-webkit-animation-name","slideinfromright").unbind('webkitAnimationEnd');
		});
		});

	});

} else {

	$(feedbackForm).animate({

		width: ['50%','swing']

	}, "fast", function() {

	$(closeFeedback).click(function(){
		$(feedbackForm).fadeOut();}
	)});

	}
});










$("a#feedback").click(function(){


	$("div#expand-click-area").after("<div id='feedback-form'><a class='close-siderail'><span class='top-rounded'>Close</span></a><div class='load-here span-9'></div></div>");

	var currentWindowwidth = $(window).width();

	var rightToSet = currentWindowwidth / 3;


	var widthToSet = currentWindowwidth / 2;

	var expandedDiv = $("#feedback-form");

  	$("#feedback-form .load-here").load( '/feedback.php', '' , function() {

		validateFeedbackForm()
		$("div#expand-click-area").addClass("shifted-left").animate({left: "-"+rightToSet},"slow");

		$(expandedDiv).animate({width: widthToSet}, "slow");

		var expandedDivwidth = $(expandedDiv).width();

		$("a.close-siderail").fadeIn();

		$("select#feedback-select").change(function(){
			var labelText = $(this).val();
			var labelExplain = "";

			if (labelText == "praise") {

				var labelExplain = "Please, Tell Us What You Like About The Site ";

			} else if (labelText == "idea") {

				var labelExplain = "Please, Tell Us Your Idea";

			} else {

				var labelExplain = "Please, Describe Your Problem";

			}

			$("#feedback-label").fadeOut("fast", function() {

				$(this).html(labelExplain);

			}).fadeIn();


		});


	});


  		//Activate close button
  		$("a.close-siderail").click(function(){

  			$("div#expand-click-area").removeClass("shifted-left").animate({left: 0},"fast");

  			$(expandedDiv).animate({width: 0}, "slow");
  			$(expandedDiv).remove();
  			return false;

	});

return false;

});



function showOther(divToOpen) {
	$(divToOpen).css("display", "block");

};


//Google Landing Pages
if ($.fn.browserTouchSupport.touches) {

	var divToOpen = $("#other-sign-categories");

	$("#see-other-categories").bind("touchstart", function(){

		$(this).css("background-color", "red");

	return false; });

	$("#see-other-categories").bind("touchend", function(){

		showOther(divToOpen);

	return false; });


} else {

$("#see-other-categories").click(function(){

	$("#other-sign-categories").slideDown('fast', function(){

$("#choose-another-category-footer").addClass("opened");

	$("#close-landing-page-menu").click(function(){
		$("#other-sign-categories").slideUp('fast');
		$("#choose-another-category-footer").removeClass("opened");
	});

return false;
});

});}


$("div#movie_container").flashembed("/tutorials/custom_tool_demo.swf", {
 	wmode: 'transparent',
 	expressInstall: '/tutorials/expressinstall.swf'});

//Focus Span next to Radio Buttons

//Clear form fields when user enters text

 $('.clearable').clearField({ blurClass: 'blurredinput', activeClass: 'activeinput' });


//Search Autocomplete

$("#search-input").autocomplete( nxt_ac_words, {
 	selectFirst: false,
	autoFill: false,
	width: 260,
	scroll: false });


//Shopping Cart Preview Using jQuery Tools Tooltip

var shopPreviewconfig = {
     sensitivity: 4, // number = sensitivity threshold (must be 1 or higher)
     interval: 200 // number = milliseconds for onMouseOver polling interval

};

//Prevent people from using spaces in email

function inputRestrictions() {
$("input.nospace").alphanumeric({ichars:' '});
$("input.number-for-sign-input").numeric({});
$("input.quantity").numeric({});
$("input#credit-card-number").alphanumeric({ichars:' '});
$("input#credit-card-number").keyup(function(){
    this.value = this.value.toUpperCase();
});
};

inputRestrictions();

//Set Global Variable for Price


function showShoppingCart(shopPreview) {

$('#shopping-cart-preview').slideDown('fast');
$(shopPreview).removeClass("loading");

}

$('html:not(.msie6,.msie7) a#view-your-shopping-cart').each(function() {
	var shopPreview = $(this);
	$(this).hoverIntent( function() {
	$('dd#how-much span#go-to-cart').text("Go To Cart");
	$('span#totalhide').fadeOut('fast');
	$(this).addClass("showpreview").addClass("loading");

	$('#shopping-cart-preview').load('/shoppingcartpreview.php','',showShoppingCart(shopPreview));
},

function() {
	$('dd#how-much span#go-to-cart').text('My Cart:');
	$('span#totalhide').fadeIn('fast');
	$('#shopping-cart-preview').hide('fast');
	$(shopPreview).removeClass("showpreview");

});

})

//Add to Cart Form validation

	//Add a class of current-form to add-to-cart form inputs to isolate validation
//$('form.addtocart input[value = ""]').nextAll("button").attr("disabled","disabled");
//$('form.addtocart input.add-to-cart-input').attr("disabled","");

//Add To Cart Custom Focus
$("input[name='designapproval']").click(function() {
	$("input[name='designapproval']").parent("div").removeClass("focus");
	$(this).parent("div").addClass("focus");
	var approval = $(this).val();
	$("input[name='designapprovalchoice']").val(approval);

});



$('form.addtocart input').focus(function(){
	$(this).parents("form").addClass("validate-me");
	$(this).nextAll("button").attr("disabled","");

});

//If Item has mounting option show mounting option div

$('form.custom.addtocart input.quantity:text').focus(function(){
$("div.has-material").fadeOut();
$("div.has-number-input").fadeOut();
if ($(this).attr("value")!="") {
	$(this).next("div.has-material").fadeIn("fast"); }
});

$('form.custom.addtocart input:text').keypress(function(){
if ($(this).attr("value")=="") {
	$(this).next("div.has-material").show("fast");
	$(this).next("div.has-number-input").show("fast"); }
});

$('form.custom.addtocart input:text').blur(function(){
	if ($(this).attr("value")=="") {
	$(this).nextAll("div.has-material").hide("fast");
	$(this).nextAll("div.has-number-input").hide("fast");
}
});
//If Item quantity is empty then hide mounting options


$('form.custom.addtocart input').blur(function(){
});
//Validate that quanity field is not empty

function validate(formData, jqForm, options) {

 	$(".add-to-cart-confirmation.showme").removeClass("showme");
    var form = jqForm[0];
    var formParent = $(form).parent();

    if (!form.qtyField.value ) {
        return false;


    } else {

    	$(formParent).addClass("loading");

    }

    var currentquanity = $('tr.data-row.over input[name=qtyField]').fieldValue();
  	var currentmaterial = $('tr.data-row.over input[name=mounting_choice]').fieldValue();

 	$("tr.data-row.over .add-to-cart-confirmation span.qty").text(currentquanity +'')
 	$("tr.data-row.over .add-to-cart-confirmation span.mounting").text(currentmaterial+'')
 	$("tr.data-row.over .add-to-cart-confirmation").addClass("showme");

 	$("div.has-material").fadeOut("fast");


}

function validateNumberform(formData, jqForm, options) {

 	$(".add-to-cart-confirmation.showme").removeClass("showme");
    var form = jqForm[0];
    if (!form.qtyField.value ) {
        return false;
    } else if (!form.number_for_sign.value) {
    	return false;

    }

    var currentquanity = $('tr.data-row.over input[name=qtyField]').fieldValue();
  	var currentnumber = $('tr.data-row.over input[name=number_for_sign]').fieldValue();

 	$("tr.data-row.over .add-to-cart-confirmation span.qty").text(currentquanity +'')
 	$("tr.data-row.over .add-to-cart-confirmation span.number").text('With the number ' + currentnumber+'')
 	$("tr.data-row.over .add-to-cart-confirmation").addClass("showme");

 	$("div.has-number-input").fadeOut("fast");

}


function showAddConfirm(responseText, statusText, xhr, $form) {

	var form = $form
	var formParent = $(form).parent();
	$(formParent).removeClass("loading");

  $(".add-to-cart-confirmation.showme").fadeIn("fast");
 	var updateTop = responseText.split("|");
  $("div#items-in-your-cart-header dl").effect("highlight", {}, 1000);
  $("span#totalhide").text(" " + updateTop[2]);
  $("span#carthide").text(updateTop[1]);

  $(this).resetForm();
   $('.clearable').clearField({ blurClass: 'blurredinput', activeClass: 'activeinput' });



}

//Close confirmation box



$('form.custom.addtocart.has-number-input').ajaxForm({

 	beforeSubmit: validateNumberform,
 	resetForm: true,
 	success: showAddConfirm

});

$('form.addtocart:not(.has-number-input)').ajaxForm({

 	beforeSubmit: validate,
 	resetForm: true,
 	success: showAddConfirm

});


$('.add-to-cart-confirmation a.continue-shopping').click(function(){

	$('.add-to-cart-confirmation.showme').fadeOut("fast");
	return false;


});



function succesfulContactForm() {

	$(".form-container div.loading").fadeOut("fast")
	$(".form-container div.thankyou").fadeIn("fast");


}

function showLargeLoadGraphic() {

	$("form.contacting").fadeOut("fast");
	$(".form-container div.loading").fadeIn("fast").html(ajax_load_large);


}


/* !  Contact Form */
$('form#contact-form').validate({

rules: {
    contact_email: {
      required: true,
      email: true
    }
},
submitHandler: function(form) {
$(form).ajaxSubmit({
	beforeSubmit: showLargeLoadGraphic,
	success: succesfulContactForm

});}});

//If user chooses "other" for return reason then display a message box
$('#return-reason-selector').change(function() {
  if ($(this).val() == 'other') {
     $("#other-reason-for-return").fadeIn();
  } else {

  	 $("#other-reason-for-return").fadeOut();

}});




jQuery.validator.addMethod("nowhitespace", function(value, element) {
	return this.optional(element) || /^\S+$/i.test(value);
}, "Spaces are not allowed");


$('form#new-user-register').validate({
invalidHandler: function(e, validator) {
var errors = validator.numberOfInvalids();
if (errors) {
	var message = errors == 1
		? 'You missed 1 field. It has been highlighted below'
		: 'You missed ' + errors + ' required fields.  They have been highlighted below';
	$("div.error-message span").html(message);
	$("div.error-message").show();

} else {
	$("div.error-message").hide();
}
},
rules: {
    email: {
      required: true,
      email: true
    },
    createpassword: {
    	required: true,
      	minlength: 5,
      	nowhitespace: true

    },
    confirmpassword: {
      equalTo: "#create-password"
    }

  },
submitHandler: function(form) {

	$(form).ajaxSubmit(
{
	success: function(msg){

	if(msg==1)
	{
	javascript:location.reload(true);
	}
	else if(msg==0)
	{
	$('form#new-user-register input#email').removeClass('valid').addClass('error');
	$('div#email-address-already-in-use').show('fast');

}}

});

}});



//Validate Standalone Guest Form

$('form#new-user-guest-checkout').validate({
invalidHandler: function(e, validator) {
var errors = validator.numberOfInvalids();
if (errors) {
	var message = errors == 1
		? 'You missed 1 field. It has been highlighted below'
		: 'You missed ' + errors + ' required fields.  They have been highlighted below';
	$("div.error-message span").html(message);
	$("div.error-message").show();

} else {
	$("div.error-message").hide();
}
},
rules: {
    email: {

      email: true
    },
    createpassword: {

      	minlength: 5,
      	nowhitespace: true

    },
    confirmpassword: {

    	equalTo: "#create-password"
    }

  }
});

$('form#returning-user-signins').validate({
	invalidHandler: function(e, validator) {
	var errors = validator.numberOfInvalids();
	if (errors) {
	var message = errors == 1
	? 'You missed 1 field. It has been highlighted below'
	: 'You missed ' + errors + ' required fields.  They have been highlighted below';
	$("div.error-message span").html(message);
	$("div.error-message").show();

	} else {
	$("div.error-message").hide();
	}
},
rules: {
    username: {
      required: true,
      email: true
    }

  }
});



//Default form tooltip
/*$("input.hastooltip").tooltip({

    // place tooltip on the right edge
    position: "top center",

    relative: true,


 	offset: [-10, 0],

    // a little tweaking of the position

    // use the built-in fadeIn/fadeOut effect
    effect: "fade",

    // custom opacity setting
    opacity: 1,

    // use this single tooltip element
    tip: '.form-tooltip'

});*/



//Show home button
$("div#header.container").hover(function() {
	$("h1#logo").addClass("preview")
},
      function () {
      	$("h1#logo").removeClass("preview");
      }

);



//Add To Cart Panels
$("ul#item-detail-slide-navigation").tabs("div#additional-details-slider > div.panel",{
    effect: 'slide',
    fadeOutSpeed: "fast",
    rotate: true

});

//Add to Cart Switch Details and Atributes

$("div#details-navigation ul").tabs("div#detail-attribute-panel > div.details-panel",{
    effect: 'slide',
    fadeOutSpeed: "fast",
    rotate: true

});

function boldLabels(inputToChange) {
	$(inputToChange).next('label').addClass("chosen");
}

//NFPA Severity Switcher
$("div.choose-an-attribute input[type='radio']").click(function() {
	graphicToChange = $(this).attr("name");
	switchNumberto = $(this).val().replace(/ /g,'_').toLowerCase();
	$("div#"+graphicToChange).removeClass();
	$("div#"+graphicToChange).addClass("hazard_"+switchNumberto);
	$("div#"+graphicToChange+"-hazard label").removeClass("chosen")
	inputToChange = $(this)
	$("input."+graphicToChange).val(switchNumberto);
	boldLabels(inputToChange);
});


//Set up NFPA Choices as Global Variables



//Sign Color Switcher
$("div#choose-color.choose-an-attribute input[type='radio']").click(function() {
	colorSignToChange = $(this).attr("id");
	switchNumberto = $(this).val().replace(/ /g,'_').toLowerCase();

	$("img.current-sign-color").removeClass("current-sign-color").hide();
	$("img#item-"+colorSignToChange).show().addClass("current-sign-color");
	$("div#choose-color.choose-an-attribute label").removeClass("chosen")
	inputToChange = $(this)
	boldLabels(inputToChange);

})



/* !Add to Cart Preview Popup */

$('form:not(.custom) input.add-to-cart-input').focus(function() {
	$("div#loading-text").remove();
	$(this).after("<div id='loading-text' class='noshow'><h4>You've just added</h4><p><span class='qty'></span><span class='item-added'></span></p></div>");

$("td.add-to-cart-form a ").click(function() {

itemAddedAttributes();

}) });

//AddtoCart Verify Quanity if it has a mounting choice then display tooltip

$("form.has-material-option input.remotetooltip").tooltip({

    // place tooltip on the right edge
    position: "center right",

 	offset: [-2, 20],
    // a little tweaking of the position
 	relative: true,

    // use the built-in fadeIn/fadeOut effect
    effect: "fade",

    // custom opacity setting
    opacity: 0.9,

    // use this single tooltip element
    tip: '.new-user-form-tooltip'

});




$('div#expand-click-area').click(function(){
	$("div#loading-text").hide();
	$("div.add-to-cart-confirmation").hide();

})


//Remote Sign In

$("html:not(.msie6,.msie7) body:not('#signin-page') a.signin-remote-button[rel]").overlay({
	expose: {
        color: '#333',
        loadSpeed: 200,
        opacity: 0.5
    }
 });

$("a#account-trigger").click( function(){

	$('div#why-sign-up-and-login').hide('fast');
	$('div#create-an-account').show('fast');
	return false;

});


//Set Equal Heights

$.fn.equalHeights = function(px) {
	$(this).each(function(){

		var currentTallest = 0;
		$(this).children().each(function(i){
			if ($(this).height() > currentTallest) { currentTallest = $(this).height(); }
		});
		//if (!px || !Number.prototype.pxToEm) currentTallest = currentTallest.pxToEm(); //use ems unless px is specified
		// for ie6, set height since min-height isn't supported
		if ($.browser.msie && $.browser.version == 6.0) { $(this).children().css({'height': currentTallest}); }
		$(this).children().css({'min-height': currentTallest});

	});
	return this;

};

//Set Equal Heights for Browse Product Pages
/*
$(window).load(function() {

	$('html:not(.msie6) div.product-subcategory-group').equalHeights(true);
	$('html:not(.msie6) div#item-image-and-description').equalHeights(true);
});
*/



$("div#custom-signs .edit-info").scrollable({

	size: 1

}).navigator();

function removeItem() {
	// get handle to scrollable api
	var api = $("div.edit-form.choose").scrollable();

	// move in 1 millisecond to the end to see what will happen
	api.end(1);

	// remove last item with jQuery's remove() method
	api.getItems().filter(".delete").remove();

	// rebuild scrollable and go one step backward
	api.reload().prev();
}




//Submit Button Rollovers

$('input.search-btn').hover(

	function() {
		$(this).attr({ src: "images/search_btn.png"});
	},
	function() {
		$(this).attr({ src: "images/search_btn_hover.png"});
	}
);


//Show and hide Search refinements

$('a.show-more').click(function() {
	$(this).prev("span.hidden-refined-results").slideToggle("fast").addClass("down");
	return false
});

$('a.show-more').toggle(

function() {

	$("span.button-copy",this).html("Show less ");

	return false
},

function() {

	$("span.button-copy",this).html("Show More ");

	return false
}

);

//TR Mouse overs
$('table tr.data-row').mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});



// function autotabPhoneNums(){
// 	$('#ship-phone-number1, #ship-phone-number2, #ship-phone-number3').autotab_magic().autotab_filter('numeric');
// 	$('#bill-phone-number1, #bill-phone-number2, #bill-phone-number3').autotab_magic().autotab_filter('numeric');
// }

// autotabPhoneNums();

/* !Validate Checkout Form */
$.metadata.setType("attr", "validate");


$(".checkout-form").submit(function () {
	$('#place-order-button-wrap').addClass('placing-order');
}).validate({
	rules: {
		credit_card_number: { required: true, creditcard: true },
		email: { required: true, email: true },
		zipcode: { required: true },
		shipzip: { required: true },
		shippingmethod: { required: true }
	}
});



//Validate Edit Forms
//Shipping


//http://furrybrains.com/2009/01/02/capturing-autofill-as-a-change-event/

$("form#checkout-form").listenForChange();
$("form#edit-shipping-data").listenForChange();



//Checkout Optional Steps


$('#copy-billing').click(function() {
    if ($('#copy-billing').is(":checked")) {

		var
		billing_shipaddress1 = $("#bill-address1").val(),
		billing_shipaddress2 = $("#bill-address2").val(),
		billing_shipcity = $("#bill-city").val(),
		billing_shipstate = $("#billing-add select#state").val(),
		billing_shipzip = $("#bill-zip-code").val(),
		billing_shipcountry = $("#bill-country").val(),
		billing_shipaccount = $("input#applied-shipping-account").val(),
		billing_stateRequired = $("#bill-country").find(":selected").data("stateRequired");

		if ( billing_shipcity != "" && ( !billing_stateRequired || billing_shipstate != "" ) && billing_shipzip != "" && billing_shipaddress1 != "" ) {
			calculateShipping(billing_shipaddress1, billing_shipaddress2, billing_shipcity, billing_shipstate, billing_shipzip, billing_shipcountry, billing_shipaccount);
		} else {
			$('div#shipping-options').html('');
		}

		window.copyingAddress = true;

        $('#shipping-address-fields select#ship-state', ':visible', document.body).each(function(i) {
            $(this).val($('#billing-add select#state').eq(i).val());
            if ($(this).val().length > 0) {
                $(this).addClass("valid");
                $(this).removeClass("error");
                $("#shipping-address cite").hide();
            }
        });
        $('#shipping-address-fields select#ship-country', ':visible', document.body).each(function(i) {
            $(this).val($('#billing-add select#bill-country').eq(i).val());
            if ($(this).val().length > 0) {
                $(this).addClass("valid");
                $(this).removeClass("error");
                $("#shipping-address cite").hide();
                $('div#shipping-options20').html('');
            }
        });
        $("input#ship-company").val($("input#bill-company").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-last-name").val($("input#bill-last-name").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-first-name").val($("input#bill-first-name").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-phone-number").val($("input#bill-phone-number").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-fax-number").val($("input#bill-fax-number").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-address1").val($("input#bill-address1").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-address2").val($("input#bill-address2").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-city").val($("input#bill-city").val()).blur().addClass("valid").removeClass("error");
        $("input#ship-zip-code").val($("input#bill-zip-code").val()).blur().addClass("valid").removeClass("error");

        window.copyingAddress = false;

    } else {

        $("#shipping-options").html("");
        window.copyingAddress = true;
        $('#shipping-address-fields input', ':visible', document.body).each(function(i) {
            $(this).val("");
        });
        window.copyingAddress = false;
        $('#shipping-address-fields select#ship-state option:eq(0)').attr('selected', 'selected')

    }

});

// !Display Tax based on Customer's Shipping State //

$("body.NJ .conditional-tax").show();

function initializeTaxStatus() {

	var
	$tax_area = $(".conditional-tax"),
	$state_select = $("#ship-state"),
	$country_select = $("#ship-country"),
	$copy_billing = $("#copy-billing"),
	selected_state,
	selected_country,
	storeSelectedState = function () {
		selected_state = $state_select.val();
	},
	storeSelectedCountry = function () {
		selected_country = $country_select.val();
	},
	updateTaxArea = function () {
		if ( selected_state === "NJ" && selected_country === "US" ) {
			$tax_area.show();
		} else {
			$tax_area.hide();
		}
	};

	$state_select.change(function (event) {
		storeSelectedState();
		updateTaxArea();
	});

	$country_select.change(function (event) {
		storeSelectedCountry();
		updateTaxArea();
	});

	$copy_billing.click(function(event) {
		storeSelectedState();
		storeSelectedCountry();
		updateTaxArea();
	});


	storeSelectedState();
	storeSelectedCountry();
	updateTaxArea();

}

initializeTaxStatus();

$("a#why-is-my-card-declined[rel]").overlay({
	expose: {
        color: '#333',
        loadSpeed: 200,
        opacity: 0.5
    }

});

$("a#find-ccv-link").click(function() {

	$("#find-ccv").slideDown('fast');

})


//Grab checkout form input values

function displayVerifiedAddress(){

	var verifyshipaddress1 = $('input#ship-address1').val();
	var verifyshipaddress2 = $('input#ship-address2').val();
	var verifyshipcity = $('input#ship-city').val();
	var verifyshipstate = $('input#ship-state').val();
	var verifyshipzip = $('input#ship-zip-code').val();

	var UserShipAddress = [verifyshipaddress1,verifyshipaddress2,verifyshipcity,verifyshipstate,verifyshipzip];
	$('dl.verify-address-preview dd').each(function (i) {
		$(this).text(UserShipAddress[i]);

	});



};

//Tabs for My Account Page
$("div.tab-container").tabs("div.options-tab > div.edit-form",{
	effect: 'slide',
	api: true,
	initialIndex: null

});



/* !Validate Shipping Edit Form */
function validateThisForm() {

	$("#edit-shipping-form").validate({

		rules: {
		shipzip: {
      	required: true


    }}

});

$("#edit-payment-data").validate({

	rules: {
	credit_card_number: {
	required: true,
	creditcard: true
	}}


	});
}


	/* !Get Card */

	function GetCardType(number)
			{
				var re = new RegExp("^4");
				if (number.match(re) != null)
					return "Visa";

				re = new RegExp("^(34|37)");
				if (number.match(re) != null)
					return "Amex";

				re = new RegExp("^5[1-5]");
				if (number.match(re) != null)
					return "Mastercard";

				re = new RegExp("^6(?:011|5[0-9]{2})");
				if (number.match(re) != null)
					return "Discover";
	}


	function intializeCreditCardNumber() {
		$("input#credit-card-number").keyup(function(){
			var number = $(this).val();
			var customersCard = GetCardType(number);
			$("input#payment-choice").val(customersCard);
		});
	};


	function intializeBrimarNumber() {
		$("input#credit-card-number").keyup(function(){
			$("input#payment-choice").val("Brimar");
		});
	};

	function setRequiredFields(){
		$('input#bill-first-name').removeClass('required');
		$('input#bill-last-name').removeClass('required');
		$('input#bill-phone-number').removeClass('required');
		$('input#bill-address1').removeClass('required');
		$('input#bill-city').removeClass('required');
		$('input#state').removeClass('required');
		$('input#bill-zip-code').removeClass('required');
		$('input#credit-card-number').removeClass('required');
		$('input#security-code').removeClass('required');
		$('#credit-card-expires-container select').removeClass('required');

	};

	/* payment method picker */


	function intializePayByThis() {

		var typeOfCard = $("div#pay-by-this-method ul li.selected").attr("id");

			if (typeOfCard == "credit") {
				$(".paypal-billing-wrapper").hide();
				$("button.checkout").show();
				$(".paypal-place-order").hide();
				$(".billing-form-wrapper").show();
				$("#checkbox-copy-wrapper").show();
				$("#paypal-acct").hide();
				$("#credit-card").show();
				$("#credit-card cite").hide();
				$("#credit-card input").removeClass("error valid");
				$("#current-card-header").text("Enter Your Credit Card Information");
				$("input#payment-choice").val("CreditCard");
				$("label#credit-card-number-label span.text").text("Card Number");
				$("input#credit-card-number").attr('name', 'credit_card_number').val("").attr('title', 'Please do not enter any spaces');
				$("div.card-number").removeClass("brimar");
				$("p#net30-pitch").hide();
				$("a#security-code-help").show();
				$("#credit-card-type-wrapper").show();
				$("#credit-card-statement").show();
				$("#credit-card-type-wrapper #credit_card_type").addClass("required");
				$("#credit-card-type-wrapper #credit-card-number").addClass("required");
				$("#save-credit-card-info").show();
				$("#paypal_submit").val("0");
				$("#checkout-form").attr('action', document.URL);
				$("input#security-code").attr('name', 'security_code').val("");
				$("#credit-card-expires-container").fadeIn("fast", function() {
					$(this).children("select").addClass("required");
				});
				$("#place-order-button-wrap").removeClass("paypal-checkout-loader");
				intializeCreditCardNumber();

			} else if (typeOfCard == "brimar"){
				$(".paypal-billing-wrapper").hide();
				$("button.checkout").show();
				$(".paypal-place-order").hide();
				$(".billing-form-wrapper").show();
				$("#checkbox-copy-wrapper").show();
				$("#paypal-acct").hide();
				$("#credit-card").show();
				$("#credit-card cite").hide();
				$("#credit-card input").removeClass("error valid");
				$("#current-card-header").text("Enter Your Brimar Net 30 Account Information");
				$("input#payment-choice").val("Brimar");
				$("label#credit-card-number-label span.text").text("Account Number");
				$("input#credit-card-number").attr('name', 'brimar_card_number').val();
				$("div.card-number").addClass("net30-card");
				$("p#net30-pitch").show();
				$("#credit-card-type-wrapper").hide();
				$("#credit-card-statement").hide();
				$("#credit-card-type-wrapper #credit_card_type").removeClass("required");
				$("#credit-card-type-wrapper #credit-card-number").addClass("required");
				$("#save-credit-card-info").hide();
				$("a#security-code-help").hide();
				$("input#security-code").attr('name', 'brimar_security_number').val();
				$("#paypal_submit").val("0");
				$("#checkout-form").attr('action', document.URL);
				$("#credit-card-expires-container").fadeOut("fast", function() {
					$(this).children("select").removeClass("required");
				});
				$("#place-order-button-wrap").removeClass("paypal-checkout-loader");
				intializeBrimarNumber();

			}else{
				$(".paypal-billing-wrapper").show();
				$("button.checkout").hide();
				$(".paypal-place-order").show();
				$(".billing-form-wrapper").hide();
				$("#checkbox-copy-wrapper").hide();
				$("#credit-card").hide();
				$("#paypal-acct").show();
				$("#credit-card cite").hide();
				$("#credit-card input").removeClass("error valid");
				$("input#payment-choice").val("PayPal");
				$("p#net30-pitch").hide();
				$("#credit-card-type-wrapper").hide();
				$("#credit-card-statement").hide();
				$("#credit-card-type-wrapper #credit_card_type").removeClass("required");
				$("#credit-card-type-wrapper #credit-card-number").removeClass("required");
				$("#save-credit-card-info").hide();
				$("a#security-code-help").hide();
				$("#paypal_submit").val("1");
				$("#checkout-form").attr('action', 'process/expresscheckout.php');
				$("#credit-card-expires-container").fadeOut("fast", function() {
					$(this).children("select").removeClass("required");
				});
				$("#place-order-button-wrap").addClass("paypal-checkout-loader");
			}

		};


if($('form.paypal-form').length > 0){
	setRequiredFields();
	$("button.checkout").hide();
	$(".paypal-place-order").show();
}else{
	intializePayByThis();
}

$("div#pay-by-this-method ul li a").click(function(e){
	$("div#pay-by-this-method ul li").removeClass("selected");
	$(this).parent("li").addClass("selected");
	intializePayByThis();
	e.preventDefault();
	});

/* !Verification Page Scripts */

$('a.edit.inuse').click(function(){

	$("div.inserted").hide('fast').remove();
	var formBeingEdited = $(this).closest('form');

	var thisBody = $(this).parents("body").attr('id');
	if (thisBody == 'verify-page'){
	var edit = $(formBeingEdited).children('input.edit').val();
	var action = $(formBeingEdited).children('input.action').val();
	} else {
	var edit = "";
	var action = "";
	}
	var myContainer = $(this).closest("div.edit-info");
	var myHighlight = $(this).closest("div.edit-info").find("div.highlight-edit");
	var urlToLoad = $(this).attr("rel");
	var loadFormHere = $(myContainer).find("div.add-new");


	if (thisBody == 'verify-page'){
			$(formBeingEdited).data('doingAinlineEdit', 'yes');
		} else {

			$(formBeingEdited).data('doingAinlineEdit', 'no');

		}

	$(myHighlight).effect("highlight", {}, 1000);
	$(loadFormHere).load( urlToLoad,{'edit' : edit,'action':action },function(){
	$(loadFormHere).children("div.inserted").fadeIn('fast').data('currentState', 'on');
	$(loadFormHere).show();
	intializeCreditCardNumber();
	intializePayByThis();
	inputRestrictions();
	initializeTaxStatus();
	validateThisForm();
	var whatIstheState = $(this).children("div.inserted").data('currentState');
	/*$("input.phone-input").mask("(999)999-9999");*/
});
closeEditForm();
return false;
});


$('a.edit.inline').click(function(){
$("div.inserted").hide('fast').remove();
var myHighlight = $(this).parents("div.account-option-input").find("div.highlight-edit");
var formBeingEdited = $(this).parents('form');
$(formBeingEdited).data('doingAinlineEdit', 'yes');
var urlToLoad = $(this).attr("rel");
var action = $(formBeingEdited).children('input.action').val();
var edit = $(formBeingEdited).children('input.edit').val();
var divBeingEdited = $(this).closest("div.account-option-input");
var heightToChange = $(divBeingEdited).parents("div.choose");
var originalHeight = $(divBeingEdited).parents("div.choose").height();
	function setNewHeight(insertedHeight,insertedForm) {
		var newHeight = $(divBeingEdited).position().top + parseFloat(insertedHeight) + $(divBeingEdited).height() + 40;
			$(heightToChange).animate({
				height: newHeight }, 'slow');
			}

	//$(myHighlight).effect("highlight", {}, 1000);
	var loadFormHere = $(divBeingEdited).find("div.add");
	$(loadFormHere).load(urlToLoad,{'edit': edit,'action': action},function(){

	var insertedForm = $(this).children("div.inserted");
	var insertedHeight = $(insertedForm).css('height');
		$(insertedForm).fadeIn('fast');
	//setNewHeight(insertedHeight,insertedForm);
	inputRestrictions();
	intializeCreditCardNumber();
	intializePayByThis();

	validateThisForm();
	/*$("input.phone-input").mask("(999)999-9999");*/
	var whatIstheState = $(this).children("div.inserted").data('currentState');

});
closeEditForm(originalHeight);
return false;
});

/* !Cancel Verify Edit */


function closeEditForm(heightToChange,originalHeight,insertedForm) {
	$("button.canceledit").live("click", function(){
		var api = $("div.tab-container").tabs("div.options-tab > div.edit-form",{api: true});
	$(this).click(function(){ api.click(0); });
		$("div.inserted").fadeOut("fast").remove();
		$(heightToChange).animate({
			height: originalHeight}, 'fast');

		return false
	});

};



/* verify page shipping record makedefult*/
$('a.make-default').live("click", function(){
	var rowToMakeDefault = $(this).parents('div.account-option-input');
	var edit = $(rowToMakeDefault).attr('id');
	var action = 'makedefault'
	var pathname = window.location.pathname;
	var currentDefault = $(this).parents('form').find("div.row.default");
	var buttonCopy = $(rowToMakeDefault).find("a.make-default span.button-copy");
	var defaultButtonCopy = $(currentDefault).find("a.make-default span.button-copy");
	var thisBody = $(this).parents("body").attr('id');
	var urlToLoad = $(this).attr("rel");
	var currentDefaultDisplay = $(this).parents("form").find("div.inuse-highlight");
	var textToReload = $(this).parents("form").find("div.inuse-highlight span.reload-me");
	var sectionId = $(this).parents('form').attr('id');
	$(rowToMakeDefault).addClass('active');

		function changeDefaultlisting() {

			$(rowToMakeDefault).addClass('default').removeClass('active');
			$(currentDefault).removeClass('default');
			$(buttonCopy).text("Current");
			$(defaultButtonCopy).text("Make Default");

		}

	$.ajax({
	type: "POST",
	url: urlToLoad,
	data: {'edit': edit,'action': action},
	success: function(){
	$(currentDefaultDisplay).effect("highlight", {}, 1000);

	if(sectionId == 'edit-shipping-form') {
			$(currentDefaultDisplay).load("/myaccount.php form#edit-shipping-form span.reload-me",function(){
		changeDefaultlisting();

	});} else if (sectionId == 'edit-payment-data') {
		$(currentDefaultDisplay).load("/myaccount.php form#edit-payment-data span.reload-me",function(){
		changeDefaultlisting();
	});}

	}});
});

/* !shipping record delete */
$('a.delete').live("click", function(){
	var rowTodelete = $(this).closest('div.account-option-input');
	var edit = $(rowTodelete).attr('id');
	var action = 'delete'
	var countIcon = $(this).parents("form").find("a.count span.icon");
	var currentCount = $(countIcon).text();
	var urlToLoad = $(this).attr("rel");

	$.prompt('Are Your Sure, you want to delete this address?',{
	buttons: { Ok: true, Cancel: false },
	callback: function(v,m,f){

	if(v){

	$.ajax({
	type: "POST",
	url: urlToLoad,
	data: {'edit': edit,'action': action},
	success: function(){

	$(rowTodelete).hide('fast').remove();
	$(countIcon).text(currentCount - 1);
	if(urlToLoad=="/templates/checkout-sec/billing-status-update.php")
	{
	$("#inuse-payment").removeClass('div.inuse-highlight highlight-edit');
	$("#inuse-payment").hide('fast').remove();
	$("div#default-billing-options").load("process/get_default_billing_address.php", function(response, status, xhr){
	$('div#default-billing-options').fadeIn("slow");
	})
	}


	}
	});

	} else{}

	}
});
return false;
});

$("a.edit.oneform").click(function(){
	var myContainer = $(this).closest("div.edit-info");
	var myHighlight = $(this).closest("div.edit-info").find("div.highlight-edit");
	$(myHighlight).effect("highlight", {}, 1000);
	$(myContainer).find('.edit-form').fadeIn('fast').data('currentState', 'on');
	return false;

});



$('div.noajax button.canceledit').click(function() {
	var myContainerstate = $(this).parents("div.edit-info").find('.edit-form');
	if (myContainerstate.data('currentState', 'on')) {
		myContainerstate.data('currentState', 'off');
		myContainerstate.fadeOut('fast');


	}
	return false;
});

//Add Class to default elements in My Account/Verification Pages

$('div.edit-form input[type="radio"]').click(function() {
	$('div.account-option-input').removeClass('focused');
	$(this).parents('div.account-option-input').addClass('focused rounded-corner');

});


$('div.account-option-input.row').hover(function() {
	$('div.account-option-input.row').removeClass('focused');
	$(this).addClass('focused');

});





/* !Credit Card Input*/
function restrictCreditCardInput(currentCard) {

	$("input.nospace").alphanumeric({ichars:' ',allcaps:true});
	$("input.hastooltip").tooltip({

// place tooltip on the right edge
    position: "top center",

    relative: true,


 	offset: [-10, 0],

    // a little tweaking of the position

    // use the built-in fadeIn/fadeOut effect
    effect: "fade",

    // custom opacity setting
    opacity: 1,

    // use this single tooltip element
    tip: '.form-tooltip'


});
$("#current-card-header").text("Enter Your "+currentCard+" Account Information").effect("highlight", {}, 1000);
 $("body#checkout-page input#credit-card-number").val(currentCard+" Number");
// $('.clearable').clearField({ blurClass: 'blurredinput', activeClass: 'activeinput' });

};



/*direct function*/
function redirect()
{
location.replace("/account");
}



//Edit Forms Ajax Submit and update



$('#edit-shipping-data').ajaxForm({
	dataType:  'json',
	success:   setEditedShippingData,
	clearForm: 'true'

});
$('#edit-billing-data').ajaxForm({
	dataType:  'json',
	success:   setEditedBillingData,
	clearForm: 'true'
});

$('#edit-shipping-method').ajaxForm({
	dataType:  'json',
	success:   setEditedShippingMethod,
	clearForm: 'true'
});

function setEditedShippingData(data) {

		$('address#shipping-info').fadeOut("fast").fadeIn("fast")
		$('span#shipaddress1').text(data.shipaddress1);
		$('span#shipaddress2').text(data.shipaddress2);
		$('span#shipcity').text(data.shipcity);
		$('span#shipstate').text(data.shipstate);
		$('span#shipzip').text(data.shipzip);
		$('span#shipfirstname').text(data.shipfirstname);
		$('span#shiplastname').text(data.shiplastname);
		$('span#shipcompany').text(data.shipcompany);
		$('span#shipcountry').text(data.shipcountry);

		//Close Expanconsoleded Box
		$('div#edit-shipping-address.expandee').slideToggle("fast");
}

function setEditedBillingData(data) {
		$('address#billing-info').fadeOut("fast").fadeIn("fast")
		$('span#address1').text(data.address1);
		$('span#address2').text(data.address2);
		$('span#city').text(data.city);
		$('span#state').text(data.state);
		$('span#zip').text(data.zipcode);
		$('span#firstname').text(data.firstname);
		$('span#lastname').text(data.lastname);
		$('span#company').text(data.company);
		$('span#country').text(data.country);

		//Close Expanded Box
		$('div#edit-billing-address.expandee').slideToggle("fast");
}

function setEditedShippingMethod(data) {
		$('p#current-shipping-method').fadeOut("fast").fadeIn("fast")
		$('p#current-shipping-method').text(data.shippingmethod);


		//Close Expanded Box
		$('div#edit-shipping-method.expandee').slideToggle("fast");
}

//Sort Order History Tables
 $("#orderHistory").tablesorter();


//Help Tooltips
$("a.help").tooltip({

 position: "center left",

 events: {
    def:      "click,hover",                // default show/hide events for an element
    input:     "focus,blur",                        // for all input elements
    widget:    "focus mouseover,blur mouseout"   // select, checkbox, radio, button
               // the tooltip element
},
effect: 'slide',
direction: 'left',
slideOffset: 10,
bounce: true }
);

$("#edit-cart").tooltip({
	position: "center left",
	effect: 'slide',
	direction: 'left',
	slideOffset: 10,
	bounce: true

});



$('.click-to-expand').click(function() {

	$(this).toggleClass('selected');
	$(this).nextAll('div.expandee').slideToggle("fast").addClass("expanded");
	return false;
});

$('a.close').click(function() {

		$(this).parents(".expanded").slideUp("fast").removeClass("expanded");
});

//Close Notices
$('div.notice div.close').click(function() {

		$(this).parent(".notice").fadeOut("fast");
});


//Generate a table of contents
$("#toc").tableOfContents(
      $("body.questions .help-container"),      // Scoped to div#wrapper
      {
        startLevel: 3,    // H2 and up
        depth:      4,    // H2 through H5,
        topLinks:   true // Add "Top" Links to Each Header
      }


    );
  //Smooth Anchor Scrolling

$("a.toc-top-link").click(function(){
$.scrollTo( 0, 500);
return false;
});


/* !Validate Contact Form */
//Change Guest Checkout div based on what Radio button is clicked.
$('input[name=new_user_checkout]:radio#register').click(function() {
		$('div#create-an-account.expandee').fadeIn("fast");
		$('input#new_user_register').fadeIn('fast',function(){
		$('div#new-users-signup p.benifits span').html("Register to use <strong>convenient features</strong> and <strong>quick checkout</strong>");
		});
		$('div#guest_user_submit-container').fadeOut("fast");

		//setRegisterformValidation();

});

$('input[name=new_user_checkout]:radio#check_out_as_guest').click(function() {
		$('div#create-an-account.expandee').fadeOut("fast");
		$('input#guest_user_submit').fadeIn("fast");
		$('div#guest_user_submit-container').fadeIn("fast");
		$('input#new_user_register').fadeOut("fast");
		$('div#new-users-signup p.benifits span').html("Check out without registering for an account");

});


//Show Tax Exempt Message
$("input[name='tax_exempt_status']").change(function(){
	if( $("input[name='tax_exempt_status']:checked").val()=='Y' ) {
		$("p#tax-exempt-notice").fadeIn("slow");

	} else {
		$("p#tax-exempt-notice").fadeOut("fast");
	}


});


//Sort Category menu by letter

$('html:not(.msie6,.msie7) #alpha-list-of-categories').listnav({showCounts: false});
$('div#loading-navigate-by-letter').fadeOut('fast', function(){

	$('div#alpha-list-of-categories-nav').fadeIn('fast');
} );


//Filter Signs by Category
$('ul#filter a').click(function() {

	$('h3#filtered-category').addClass('hidden');
	$('div.product-subcategory-group h3').addClass('hidden');
		$(this).css('outline','none');

		$('ul#filter .current').removeClass('current');
		$(this).parent().addClass('current');
		var filterVal = $(this).text().toLowerCase().replace(/ /g,'-').replace(/\./g,'-').replace(/\//g,'-');

		var headerVal = $(this).text();
		$("div#category-filter").after('<h3 id="filtered-category" class="'+ filterVal +'" >' + headerVal + '</h2>');
		if(filterVal == 'show-all') {
			$('li#show-all a').text('Filter Signs');
			$('li#show-all').addClass('disabled');
			$('div.product-container.hidden').fadeIn('slow').removeClass('hidden');
			$('div.product-container').width(140).css('border-left-style','none');
			$('div.product-subcategory-group h3').fadeIn('slow').removeClass('hidden');
			$('h3#filtered-category.show-all').addClass('hidden');
			$('div.product-subcategory-group').css('display','block').css('border-style','solid');

		} else {

			$('li#show-all a').text('Show All');
			$('li#show-all').removeClass('disabled');
			$('div.product-subcategory-group').css('display','inline').css('border-style','none');
			$('div.product-container').each(function() {

				if(!$(this).hasClass(filterVal)) {
					$(this).fadeOut('normal').addClass('hidden');
				} else {
					$(this).fadeIn('slow').removeClass('hidden').width(138).css('border-left-style','solid');


				}
			});
		}

		return false;
	});




$(".nonjs").fadeOut("fast");

$(".hasjs").fadeIn("slow");

$(".showhidedes").click(function()
	{
		var element_id=$(this).attr("id");
		var selement_sepa=element_id.split('_');
		var hideid=selement_sepa[1];
		$('#hiddendes_'+hideid).toggle();

		if($(this).val()=='+')
		{
			$(this).val('-');
			$(this).parent("div").find("span").html('Hide Details');
		}
		else
		{
			$(this).val('+');
			$(this).parent("div").find("span").html('View Details');
		}
	});
});

if ($.fancybox) {
        $("a.zoom").fancybox();
    }

// JavaScript Document