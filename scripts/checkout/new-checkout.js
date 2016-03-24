function calculateShipping (address1, address2, city, state, zip, country, shipaccount) {

	$("#shipping-options").hide();
	$("#shipping-options-wrapper").fadeIn("fast");
	$("#no-shipping-notice").addClass("hidden").removeClass("notice");
	$("div.shipping-options-loader").removeClass("hidden");

	if ( typeof window.shippingCalcAjax !== "undefined" && window.shippingCalcAjax !== null ) {
		window.shippingCalcAjax.onreadystatechange = function () {}; // Necessary to prevent jQuery 1.4 from handling the abort callback improperly.
		window.shippingCalcAjax.abort();
		window.shippingCalcAjax = null;
	}

	window.shippingCalcAjax = $.ajax({
		url: "process/calculate-shipping-ajax.php",
		data: {
			shipaddress1: address1,
			shipaddress2: address2,
			shipcity: city,
			shipstate: state,
			shipzip: zip,
			shipcountry: country,
			shipaccount: shipaccount
		},
		success: function (data, textStatus, xhr) {
			$("#shipping-options").show().html(data);
			$("div.shipping-options-loader").addClass("hidden");
			$("input.numeric-only").numeric({allow:"."});
			$("#shipping-options20").html("");
			$("#shipping-options").slideDown("fast");
			window.shippingCalcAjax = null;
		}
	});

}

$(document).ready(function() {

$(".nonjs").fadeOut("fast");

$(".hasjs").fadeIn("slow");


	//$('input#ship-zip-code').listenForChange();
	$("input.phone-input").listenForChange();
	$("select#ship-state").listenForChange();


	$("#shipping-address input").listenForChange();

	$('#checkout-page form#guest-checkout').validate({
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
			  email: true

		  }
	});




	$('#edit-shopping-cart-button').click(function () {
		$('div#edit-cart-warning').fadeIn("fast");
	});

	$('#cancel-edit-cart').click(function () {
		$('div#edit-cart-warning').fadeOut("fast");
	});



	$('#shippingchooser').click(function () {
		$('div#shipping-options-wrapper').hide();
		var
		shipaddress1 = $("div#shipping-address input#ship-address1").val(),
		shipaddress2 = $("div#shipping-address input#ship-address2").val(),
		shipcity = $("div#shipping-address input#ship-city").val(),
		shipstate = $("div#shipping-address select#ship-state").val(),
		shipzip = $("div#shipping-address input#ship-zip-code").val(),
		shipcountry = $("div#shipping-address select#ship-country").val(),
		shipaccount = $("input#applied-shipping-account").val(),
		stateRequired = $("#ship-country").find(":selected").data("stateRequired");

		if ( !window.copyingAddress ) {
			if ( shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
				calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
			} else {
				$('div#shipping-options').html('');
				$('div#shipping-options20').html('');
				$('div#shipping-options-wrapper').fadeIn("fast");
				$("#no-shipping-notice").addClass("notice").removeClass("hidden");
			}
		}
	});



	$('input#ship-zip-code').bind('blur', function () {

		var
		shipaddress1 = $("div#shipping-address input#ship-address1").val(),
		shipaddress2 = $("div#shipping-address input#ship-address2").val(),
		shipcity = $("div#shipping-address input#ship-city").val(),
		shipstate = $("div#shipping-address select#ship-state").val(),
		shipzip = $("div#shipping-address input#ship-zip-code").val(),
		shipcountry = $("div#shipping-address select#ship-country").val(),
		shipaccount = $("input#applied-shipping-account").val(),
		stateRequired = $("#ship-country").find(":selected").data("stateRequired");

		if ( !window.copyingAddress ) {
			if ( shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
				calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
			} else {
				$('div#shipping-options').html('');
			}
		}

		});

	$('select#ship-state').bind('change', function () {

		var
		shipaddress1 = $("div#shipping-address input#ship-address1").val(),
		shipaddress2 = $("div#shipping-address input#ship-address2").val(),
		shipcity = $("div#shipping-address input#ship-city").val(),
		shipstate = $("div#shipping-address select#ship-state").val(),
		shipzip = $("div#shipping-address input#ship-zip-code").val(),
		shipcountry = $("div#shipping-address select#ship-country").val(),
		shipaccount = $("input#applied-shipping-account").val(),
		stateRequired = $("#ship-country").find(":selected").data("stateRequired");

		if ( !window.copyingAddress ) {
			if ( shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
				calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
			} else {
				$('div#shipping-options').html('');
			}
		}

		});

	$('select#ship-country').bind('change', function () {

		var
		shipaddress1 = $("div#shipping-address input#ship-address1").val(),
		shipaddress2 = $("div#shipping-address input#ship-address2").val(),
		shipcity = $("div#shipping-address input#ship-city").val(),
		shipstate = $("div#shipping-address select#ship-state").val(),
		shipzip = $("div#shipping-address input#ship-zip-code").val(),
		shipcountry = $("div#shipping-address select#ship-country").val(),
		shipaccount = $("input#applied-shipping-account").val(),
		stateRequired = $("#ship-country").find(":selected").data("stateRequired");

		if ( !window.copyingAddress ) {
			if ( shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
				calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
			} else {
				$('div#shipping-options').html('');
			}
		}

	});

	(function () {

		var

		$trigger = $("p.customer-ship-account-trigger a"),
		$formWrap = $("div.customer-ship-account"),
		$accountInput = $formWrap.find("#shipping-account"),
		$appliedAccountInput = $formWrap.find("#applied-shipping-account"),
		$submitButton = $formWrap.find("#shipping-account-apply"),
		$cancelButton = $formWrap.find("div.customer-ship-account-cancel"),

		trim = function (string) {
			string = "" + string;
			return (String.prototype.trim ? string.trim() : string.replace(/^\s+|\s+$/g, ""));
		},

		showAccountErrorNotice = function () {
			hideAccountErrorNotice();
			$formWrap.prepend($(document.createElement("p")).attr("id", "customer-ship-account-error").addClass("error").text("The account number you entered is invalid. Please enter your six-digit UPS or nine-digit FedEx account number."));
		},

		hideAccountErrorNotice = function () {
			$("#customer-ship-account-error").remove();
		},

		applyAccount = function () {

			var
			shipaddress1 = $("div#shipping-address input#ship-address1").val(),
			shipaddress2 = $("div#shipping-address input#ship-address2").val(),
			shipcity = $("div#shipping-address input#ship-city").val(),
			shipstate = $("div#shipping-address select#ship-state").val(),
			shipzip = $("div#shipping-address input#ship-zip-code").val(),
			shipcountry = $("div#shipping-address select#ship-country").val(),
			shipaccount = trim($accountInput.val()),
			shipaccountSanitized = shipaccount.replace(/[^0-9a-z]/gi, ""),
			stateRequired = $("#ship-country").find(":selected").data("stateRequired"),
			scrollToShipping = false;

			// Update the hidden input.
			$appliedAccountInput.val(shipaccount);

			if ( shipaccount.length === 0 ) {

				// Nothing was entered. Reset to the default view and collapse.
				hideAccountErrorNotice();
				$trigger.text("Click here to enter your own UPS or FedEx account number.").next().text("(optional)");
				$submitButton.text("Apply Account");
				$cancelButton.text("Cancel");
				scrollToShipping = true;

			} else if ( shipaccountSanitized.length === 6 || shipaccountSanitized.length === 9 ) {

				// A valid account number was entered. Update the display.
				hideAccountErrorNotice();
				$trigger.text("Using UPS / FedEx account " + shipaccount + ". Click to change.").next().text("");
				$submitButton.text("Update Account");
				$cancelButton.text("Remove");
				scrollToShipping = true;

			} else {

				// An invalid account number was entered. Reset to the default view and show an error.
				showAccountErrorNotice();
				$trigger.text("Click here to enter your own UPS or FedEx account number.").next().text("(optional)");
				$submitButton.text("Apply Account");
				$cancelButton.text("Cancel");

			}

			// Calculate shipping.
			if ( !window.copyingAddress ) {
				if ( shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
					calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
				} else {
					$('div#shipping-options').html('');
				}
			}

			if ( scrollToShipping ) {

				// Hide the form.
				$formWrap.addClass("hidden");

				// Focus on shipping rates.
				$('html, body').animate({ scrollTop: $('div#shipping-det').offset().top }, 'slow');

			}

		};

        $accountInput.keydown(function (event) {
            if ( +event.which === 13 ) {
                applyAccount();
                return false;
            }
        });

		$trigger.click(function (event) {
			event.preventDefault();
			$formWrap.removeClass("hidden");
		});

		$submitButton.click(function (event) {
			event.preventDefault();
			applyAccount();
		});

		$cancelButton.click(function (event) {
			event.preventDefault();
			$accountInput.val("");
			applyAccount();
		});

	})();

	emptyZipHighlight();

	function emptyZipHighlight() {
		var shipzip = $("div#shipping-address input#ship-zip-code").attr("value");

	}



	// Customer pickup and shipping method hints.
	$("#shipping-options-wrapper").delegate(".pickup-details, a.shipping-method-hint-trigger", "click", function (event) {
		event.preventDefault();
		$.fancybox($($(this).attr("href").replace(/.*(?=#[^\s]+$)/, "")).get(0));
	});


	$("#terms-link").fancybox({
		'type': 'iframe',
		'showCloseButton': true
	});

	$("input.numeric-only").numeric({allow:"."});



/* Show/Hide State Selectors */

function updateBillingStateSelector () {

	var
	$stateSelector = $("#billing-add div.address-state-wrap"),
	hiddenClass = "hidden";

	if ( $("#bill-country").find(":selected").data("stateRequired") !== false ) {
		$stateSelector.removeClass(hiddenClass);
	} else {
		$stateSelector.addClass(hiddenClass);
	}

}

function updateShippingStateSelector () {

	var
	$stateSelector = $("#ship-state").parent(),
	hiddenClass = "hidden";

	if ( $("#ship-country").find(":selected").data("stateRequired") !== false ) {
		$stateSelector.removeClass(hiddenClass);
	} else {
		$stateSelector.addClass(hiddenClass);
	}

}

$("#bill-country").change(function (event) { updateBillingStateSelector(); });
$("#ship-country").change(function (event) { updateShippingStateSelector(); });
$("#copy-billing").click(function (event) { updateShippingStateSelector(); });

updateBillingStateSelector();
updateShippingStateSelector();

(function () {

	var

	default_address_change_url = "/process/setdefaultaddress.php",

	reloadPage = function () {

		var i, len, keyVal, params = {}, paramStrings = window.location.search.substr(1).split("&");

		// Get the current URL parameters as an object.
		if ( paramStrings !== "" ) {

			for ( i = 0, len = paramStrings.length; i < len; i++ ) {

				keyVal = paramStrings[i].split("=");

				if ( keyVal.length === 2 ) {
					params[keyVal[0]] = decodeURIComponent(keyVal[1].replace(/\+/g, " "));
				} else if ( keyVal.length === 1 ) {
					params[keyVal[0]] = "";
				}

			}

		}

		// Add/update the reload parameter.
		params.reload = (new Date()).getTime();

		// Reload the page.
		window.location.href = window.location.protocol + "//" + window.location.host + window.location.pathname + "?" + $.param(params);

	};

	$("#default_shipping_address").change(function (event) {

		$.ajax({
			url: default_address_change_url,
			type: "POST",
			data: { id : $(this).val(), type : "shipping" },
			success: function (data, textStatus, jqXHR) {
				reloadPage();
			}
		});

	});

	$("#default_billing_address").change(function (event) {

		$.ajax({
			url: default_address_change_url,
			type: "POST",
			data: { id : $(this).val(), type : "billing" },
			success: function (data, textStatus, jqXHR) {
				reloadPage();
			}
		});

	});

})();

// Calculate shipping on page load (when data is available).
(function () {

	var
	shipaddress1 = $("#ship-address1").val(),
	shipaddress2 = $("#ship-address2").val(),
	shipcity = $("#ship-city").val(),
	shipstate = $("#ship-state").val(),
	shipzip = $("#ship-zip-code").val(),
	shipcountry = $("#ship-country").val(),
	shipaccount = $("input#applied-shipping-account").val(),
	stateRequired = $("#ship-country").find(":selected").data("stateRequired");

	if ( !window.copyingAddress && shipcity != "" && ( !stateRequired || shipstate != "" ) && shipzip != "" && shipaddress1 != "" ) {
		calculateShipping(shipaddress1, shipaddress2, shipcity, shipstate, shipzip, shipcountry, shipaccount);
	}

})();

});

//Prevent Coupon Enter Press From Submitting Order
$(function(){
    $("#coupon-code").keypress(function (e) {
        if (e.keyCode == 13) {
            CouponCheck();
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
            return false;
        }
    });
});