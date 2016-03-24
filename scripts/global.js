(function (window, document, undefined) {

"use strict";

var

// --------------------------------------------------
// Imported globals
// --------------------------------------------------

Modernizr = window.Modernizr,
Handlebars = window.Handlebars,
moment = window.moment,
Recaptcha = window.Recaptcha,
purl = window.purl,
numeral = window.numeral,

// --------------------------------------------------
// Error code constants
// --------------------------------------------------

missing_email = 1,
invalid_email = 2,
missing_pass = 3,
invalid_pass = 4,
pass_mismatch = 5,

// --------------------------------------------------
// Private variables and functions
// --------------------------------------------------

email_regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/i,
password_regex = /^.{8}.*/,
products = [],
priceGrids = [],
quickViewPriceGrids = [],
quickViewUlRecognitionNotes = [],
$window = $(window),
$document = $(document),
$body = $(document.body),
KEYCODE_ENTER = 13,
KEYCODE_ESCAPE = 27,
compareOperators = {
    "==":     function (l, r) { return l == r; },
    "===":    function (l, r) { return l === r; },
    "!=":     function (l, r) { return l != r; },
    "!==":    function (l, r) { return l !== r; },
    "<":      function (l, r) { return l < r; },
    ">":      function (l, r) { return l > r; },
    "<=":     function (l, r) { return l <= r; },
    ">=":     function (l, r) { return l >= r; },
    "typeof": function (l, r) { return typeof l == r; }
},

unCamelCase = function (all, letter) {

    return "-" + (""+all).toLowerCase();

},

removePrefix = function (all, letter) {

    return (""+letter).toLowerCase();

},

trim = function (string) {

    string = "" + string;

    return (String.prototype.trim ? string.trim() : string.replace(/^\s+|\s+$/g, ""));

},

hideProductDialogs = function (options) {

        var i, len;

        options = options || {};

        for (i = 0, len = products.length; i < len; i++) {
            if ((products[i] instanceof Product) && (!(options.except instanceof Product) || options.except !== products[i])) {
                if (options.success === true) { products[i].hideSuccessDialog(); }
                if (options.error === true) { products[i].hideErrorDialog(); }
                if (options.upcharge === true) { products[i].hideUpchargeDialog(); }
            }
        }

},

sortProductsByPosition = function (a, b) {

    return (a.position > b.position) ? 1 : ((a.position < b.position) ? -1 : 0);

},

sortProductsByNameAscending = function (a, b) {

    var name_a = a.name.toLowerCase(),
        name_b = b.name.toLowerCase();

    return (name_a > name_b) ? 1 : ((name_a < name_b) ? -1 : sortProductsByPosition(a, b));

},

sortProductsByNameDescending = function (a, b) {

    var name_a = a.name.toLowerCase(),
        name_b = b.name.toLowerCase();

    return (name_a < name_b) ? 1 : ((name_a > name_b) ? -1 : sortProductsByPosition(a, b));

},

sortProductsByNumberAscending = function (a, b) {

    var num_a = a.sku.toLowerCase(),
        num_b = b.sku.toLowerCase();

    return (num_a > num_b) ? 1 : ((num_a < num_b) ? -1 : sortProductsByPosition(a, b));

},

sortProductsByNumberDescending = function (a, b) {

    var num_a = a.sku.toLowerCase(),
        num_b = b.sku.toLowerCase();

    return (num_a < num_b) ? 1 : ((num_a > num_b) ? -1 : sortProductsByPosition(a, b));

},

formatPrice = function (price, currencySymbol) {

    var formattedPrice;

    if ( numeral ) {

        // Default to $ as the currency symbol.
        if ( typeof currencySymbol === "undefined" || currencySymbol === null ) {
            currencySymbol = "$";
        }

        formattedPrice = numeral(price).format(currencySymbol + "0,0.00");

    } else {

        formattedPrice = "";

    }

    return formattedPrice;

},

parsePrice = function (price) {
    return numeral ? numeral().unformat(price) : 0;
},

showGroupingMenu = function () {

    var $trigger = $(this),
        triggerheight,
        windowheight,
        scrolltop,
        $menu,
        menuheight,
        menutop;

    if (!$trigger.hasClass("current")) {

        windowheight = $window.height();
        $menu = $trigger.children("ul");
        menuheight = $menu.outerHeight(false);

        $trigger.addClass("flyout-parent");
        $trigger.children("a.groupfilter").addClass("current-hover");
        $menu.removeClass("flyout-up").addClass("flyout flyout-down");

        if (windowheight >= menuheight) {

            scrolltop = $window.scrollTop();
            menutop = $menu.offset().top;
            triggerheight = $trigger.outerHeight(false);

            if ((menutop + menuheight > scrolltop + windowheight) && (menutop - menuheight + triggerheight >= scrolltop)) {
                $menu.removeClass("flyout-down").addClass("flyout-up");
            }

        }
    }

},

hideGroupingMenu = function () {

    var $this = $(this);

    $this.removeClass("flyout-parent");
    $this.children("ul").removeClass("flyout flyout-up flyout-down");
    $this.children("a.groupfilter").removeClass("current-hover");

},

// --------------------------------------------------
// Classes
// --------------------------------------------------


Cart = (function () {

    var
    templates = {
        inventoryAlert: "inventoryAlert",
        loadingMessage: "cartLoadingMessage",
        communicationError: "cartCommunicationError",
        freightShipmentNotice: "freightShipmentNotice"
    },
    urls = {
        update: "/update-cart"
    },
    text = {
        defaultShipDate: "Unknown",
        removeAllConfirmation: "Are you sure you want to empty your cart?",
        itemLabelSingular: "Item",
        itemLabelPlural: "Items"
    },
    classes = {
        hidePaypal: "hidden"
    },
    dataAttributes = {
        shipDate: "cartShipDate",
        inventoryAlertProductId: "cartProductId"
    },
    selectors = {
        countDisplay: "div.shopping-cart-item-count span.count",
        subtotalDisplays: "#cart-summary span.subtotal, #invoice-subtotals span.subtotal",
        shipDateDisplay: "div.shopping-cart-item-count span.cart-shipdate",
        removeAll: "form[name=\"removecart\"]",
        products: "tr.items-list",
        freightMessage: "div.freight-message",
        shippingEstimator: "div.shipping-estimator",
        messageHolder: "div.cart-messages",
        checkoutButton: "a.checkout",
        paypalButton: "a.paypal-checkout",
        inventoryAlertApplyButton: ".inventory-alert-apply",
        inventoryAlertDismissButton: ".inventory-alert-dismiss",
        inventoryAlertWrap: "div.cart-inventory-alert",
        inventoryAlertProducts: "tr.items-list",
        inventoryAlertProductQuantityInput: "input.add-to-cart-input"
    };

    function Cart (e) {

        var $e = $(e), freightMessage;

        // Get all of the DOM elements.
        this.e = e;
        this.errorMessage = undefined;
        this.products = this.getProducts();
        this.setSubtotal(this.calculateSubtotal()).setCount(this.countProducts()).setShipDate($e.data(dataAttributes.shipDate));
        this.queue = [];
        this.messageHolder = $e.find(selectors.messageHolder).get(0);
        this.subtotalDisplays = $.makeArray($e.find(selectors.subtotalDisplays));
        this.shipDateDisplay = $e.find(selectors.shipDateDisplay).get(0);
        this.countDisplay = $e.find(selectors.countDisplay).get(0);
        this.removeAllForm = $e.find(selectors.removeAll).get(0);
        this.checkoutButton = $e.find(selectors.checkoutButton).get(0);
        this.paypalButton = $e.find(selectors.paypalButton).get(0);
        this.shippingEstimator = this.getShippingEstimator();

        // Check if the freight message already exists and, if so, store it for future access.
        freightMessage = this.findFreightMessage();
        if (freightMessage) {
            this.freightMessage = freightMessage;
        }

    }

    Cart.prototype.init = function () {

        var product;

        // Initialize the products.
        for ( product in this.products ) {
            if ( this.products.hasOwnProperty(product) ) {
                if ( this.products[product] instanceof CartProduct ) {
                    this.products[product].init();
                }
            }
        }

        // Initialize the shipping estimator.
        if ( this.shippingEstimator instanceof ShippingEstimator ) {
            this.shippingEstimator.init();
        }

        // Initialize the remove-all form.
        $(this.removeAllForm).on("submit", function (event) {
            event.preventDefault();
            if ( window.confirm(text.removeAllConfirmation) ) {
                this.removeAllProducts();
            }
        }.bind(this));

        // Initialize the PayPal and Checkout buttons.
        $(this.checkoutButton).add(this.paypalButton).on("click", function (event) {
            event.preventDefault();
            this.update(function () {
                window.location = $(event.currentTarget).attr("href");
            });
        }.bind(this));

        // Show an inventory alert if some or all of the items in the cart do not have sufficient inventory.
        if ( !this.isInventoryAvailable() ) {
            this.showInventoryAlert();
        }

        return this;

    };

    Cart.prototype.getShippingEstimator = function () {

        var se, $e = $(this.e).find(selectors.shippingEstimator);

        if ( $e.length > 0 ) {
            se = new ShippingEstimator($e.get(0));
        }

        return se;

    };

    Cart.prototype.findFreightMessage = function () {

        var msg, $msg = $(this.e).find(selectors.freightMessage);

        if ( $msg.length > 0 ) {
            msg = $msg.get(0);
        }

        return msg;

    };

    Cart.prototype.updateSubtotalDisplays = function () {

        $(this.subtotalDisplays).text(formatPrice(this.subtotal, ""));
        return this;

    };

    Cart.prototype.updateCountDisplay = function () {

        $(this.countDisplay).text(this.count + " " + ( this.count === 1 ? text.itemLabelSingular : text.itemLabelPlural ));
        return this;

    };

    Cart.prototype.getProducts = function () {

        var products = {};

        $(selectors.products).each(function (i, e) {

            var product = new CartProduct(e, this);
            products[product.id] = product;

        }.bind(this));

        return products;

    };

    Cart.prototype.isInventoryAvailable = function (ids) {

        var product, inventoryAvailable = true, checkIds = false;

        // If an ID or an array of IDs was passed into the function, only check inventory on the specified items.
        if ( typeof ids === "number" ) {
            ids = [ids];
            checkIds = true;
        } else if ( $.isArray(ids) && ids.length > 0 ) {
            checkIds = true;
        }

        for ( product in this.products ) {
            if ( this.products.hasOwnProperty(product) && this.products[product] instanceof CartProduct && this.products[product].qty > 0 && !this.products[product].isAvailable() && ( !checkIds || $.inArray(this.products[product].id, ids) > -1 ) ) {
                inventoryAvailable = false;
                break;
            }
        }

        return inventoryAvailable;

    };

    Cart.prototype.update = function (callback) {

        var i, len, updateData = {
            id: [],
            qty: []
        };

        // Prepare the POST data.
        for ( i = 0, len = this.queue.length; i < len; i++ ) {
            updateData.id[i] = this.queue[i].id;
            updateData.qty[i] = this.queue[i].qty;
        }

        // Hide communication errors, disable user input, and show the loading message.
        this.hideCommunicationError().disableForms().showLoadingMessage();

        // Perform the AJAX request.
        $.ajax({
            url: urls.update,
            type: "POST",
            data: updateData,
            success: function (data, textStatus, jqXHR) {

                var _count = this.count, i, len, product, reload = false;

                // Empty the update queue.
                this.emptyQueue();

                // If a legitimate response was received...
                if ( data && data.products ) {

                    // Loop through each product in the response...
                    for ( i = 0, len = data.products.length; i < len; i++ ) {

                        // If the product was found on the page during initialization...
                        if ( this.products.hasOwnProperty(data.products[i].id) && this.products[data.products[i].id] instanceof CartProduct ) {

                            // Update the product.
                            this.products[data.products[i].id].update(data.products[i].qty, data.products[i].unitprice, data.products[i].inventory);

                        // If the product was not found on the page during initialization...
                        } else {

                            // Force the page to reload because the DOM is out of sync.
                            reload = true;

                        }

                    }

                    // Update the product stock statuses.
                    for ( product in this.products ) {
                        if ( this.products.hasOwnProperty(product) && this.products[product] instanceof CartProduct ) {
                            this.products[product].updateStockStatus();
                        }
                    }

                    // Update the subtotal, count, and ship date according to the data received.
                    this.setSubtotal(this.calculateSubtotal()).setCount(this.countProducts()).setShipDate(data.shipdate);

                    // If the cart is now empty...
                    if ( _count > 0 && this.count === 0 ) {

                        // Force the page to reload so the user can see the empty cart template.
                        reload = true;

                    }

                    // If the cart needs to be fully reloaded...
                    if ( reload ) {

                        // Refresh the page so the user can see the empty cart template.
                        window.location.reload(true);

                    // If the cart does not need to be fully reloaded...
                    } else {

                        // Update the cart summary.
                        this.updateSubtotalDisplays().updateCountDisplay().updateShipDateDisplay();

                        // Update the mini-cart.
                        window.ss.site.updateMiniCart(this.count, this.subtotal);

                        // Show or hide the freight shipment notice depending on whether or not it is required.
                        if ( data.freightshipment === true ) {
                            this.showFreightShipmentNotice();
                        } else {
                            this.hideFreightShipmentNotice();
                        }

                        // Recalculate shipping.
                        if ( this.shippingEstimator instanceof ShippingEstimator ) {
                            this.shippingEstimator.refresh();
                        }

                        // If the everything in the cart has sufficient inventory...
                        if ( this.isInventoryAvailable(updateData.id) ) {

                            // Hide the inventory alert.
                            this.hideInventoryAlert();

                            // Run the callback.
                            if ( typeof callback === "function" ) {
                                callback();
                            }

                        // If some or all of the items in the cart do not have sufficient inventory...
                        } else {

                            // Show the inventory alert and pass the callback through to be executed when everything is resolved.
                            this.showInventoryAlert(callback);

                        }

                    }

                // If a legitimate response was not received...
                } else {

                    // Display a communication error.
                    this.showCommunicationError();

                }

            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {

                // Display a communication error.
                this.showCommunicationError();

            }.bind(this),
            complete: function (jqXHR, textStatus) {

                // Re-enable user input and hide the loading message.
                this.enableForms().hideLoadingMessage();

            }.bind(this)
        });

        return this;

    };

    Cart.prototype.showInventoryAlert = function (callback) {

        var $inventoryAlert, product, data = {
            products: []
        };

        for ( product in this.products ) {
            if ( this.products.hasOwnProperty(product) && this.products[product] instanceof CartProduct && this.products[product].qty > 0 && !this.products[product].isAvailable() ) {
                data.products.push({
                    id: this.products[product].id,
                    image: this.products[product].image,
                    sku: this.products[product].sku,
                    name: this.products[product].name,
                    quantityInCart: this.products[product].qty,
                    totalInCart: this.countProducts(this.products[product].sku),
                    inventory: this.products[product].inventory,
                    unitPrice: this.products[product].unitprice,
                    totalPrice: this.products[product].totalprice,
                    size: this.products[product].size,
                    material: this.products[product].material
                });
            }
        }

        // If all the plugins necessary to display an alert are available...
        if ( $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[templates.inventoryAlert] ) {

            // Generate the alert and attach event listeners.
            $inventoryAlert = $(Handlebars.templates[templates.inventoryAlert](data)).on("click", selectors.inventoryAlertApplyButton, function (event) {

                // Prevent default behavior.
                event.preventDefault();

                // Push changes from dialog to the queue.
                $(event.currentTarget).closest(selectors.inventoryAlertWrap).find(selectors.inventoryAlertProducts).each(function (i, e) {
                    var $e = $(e);
                    this.pushToQueue({
                        id: parseInt($e.data(dataAttributes.inventoryAlertProductId), 10) || 0,
                        qty: parseInt($e.find(selectors.inventoryAlertProductQuantityInput).val(), 10) || 0
                    });
                }.bind(this));

                // Update the cart.
                this.update(callback);

            }.bind(this)).on("click", selectors.inventoryAlertDismissButton, function (event) {

                // Prevent default behavior.
                event.preventDefault();

                // Hide the inventory alert.
                this.hideInventoryAlert();

            }.bind(this));

            // Display the alert via Fancybox.
            $.fancybox({
                modal: true,
                content: $inventoryAlert,
                afterShow: function () {
                    this.inventoryAlertActive = true;
                }.bind(this),
                beforeClose: function () {
                    this.inventoryAlertActive = false;
                }.bind(this)
            });

        }

    };

    Cart.prototype.hideInventoryAlert = function (callback) {

        if ( $.fancybox && this.inventoryAlertActive ) {
            $.fancybox.close();
        }

        return this;

    };

    Cart.prototype.disableForms = function () {

        var product;

        $(this.removeAllForm).find(":input").prop("disabled", true);

        for (product in this.products) {
            if (this.products.hasOwnProperty(product)) {
                if (this.products[product] instanceof CartProduct) {
                    this.products[product].disableForms();
                }
            }
        }

        return this;

    };

    Cart.prototype.enableForms = function () {

        var product;

        $(this.removeAllForm).find(":input").prop("disabled", false);

        for (product in this.products) {
            if (this.products.hasOwnProperty(product)) {
                if (this.products[product] instanceof CartProduct) {
                    this.products[product].enableForms();
                }
            }
        }

        return this;

    };

    Cart.prototype.showLoadingMessage = function () {

        var $msg;

        if ( !this.loadingmsg && Handlebars && Handlebars.templates && Handlebars.templates[templates.loadingMessage] ) {

            // Create the DOM element.
            $msg = $(Handlebars.templates[templates.loadingMessage]());

            // Display the message.
            $(this.e).append($msg);

            // Store the message as a property of the Cart.
            this.loadingmsg = $msg.get(0);

        }

        return this;

    };

    Cart.prototype.hideLoadingMessage = function () {

        if ( this.loadingmsg ) {

            // Hide the message.
            $(this.loadingmsg).remove();

            // Delete the property from the Cart.
            delete this.loadingmsg;

        }

        return this;

    };

    Cart.prototype.showCommunicationError = function () {

        var $error;

        if ( Handlebars && Handlebars.templates && Handlebars.templates[templates.communicationError] ) {

            $error = $(Handlebars.templates[templates.communicationError]()).appendTo(this.messageHolder);

            this.errorMessage = $error.get(0);

        }

        return this;

    };

    Cart.prototype.hideCommunicationError = function () {

        if ( this.errorMessage ) {
            $(this.errorMessage).remove();
        }

        return this;

    };

    Cart.prototype.pushToQueue = function (action) {
        this.queue.push(action);
        return this;
    };

    Cart.prototype.emptyQueue = function () {
        this.queue = [];
        return this;
    };

    Cart.prototype.calculateSubtotal = function () {

        var product, subtotal = 0;

        for (product in this.products) {
            if (this.products.hasOwnProperty(product)) {
                if (this.products[product] instanceof CartProduct) {
                    subtotal += this.products[product].totalprice;
                }
            }
        }

        return subtotal;

    };

    Cart.prototype.setSubtotal = function (price) {

        this.subtotal = +price;
        return this;

    };

    Cart.prototype.setCount = function (count) {

        this.count = parseInt(count, 10);
        return this;

    };

    Cart.prototype.setShipDate = function (date) {

        var momentDate, shipDate = text.defaultShipDate;

        if ( date && moment ) {
            momentDate = moment.utc(date);
            if ( momentDate.isValid() ) {
                shipDate = momentDate.format("MMMM Do, YYYY");
            }
        }

        this.shipDate = shipDate;

        return this;

    };

    Cart.prototype.countProducts = function (sku) {

        var product, count = 0, checkSku = typeof sku === "string";

        for ( product in this.products ) {
            if ( this.products.hasOwnProperty(product) ) {
                if ( this.products[product] instanceof CartProduct && this.products[product].qty > 0 && ( !checkSku || this.products[product].sku === sku ) ) {
                    count += this.products[product].qty;
                }
            }
        }

        return count;

    };

    Cart.prototype.removeAllProducts = function () {

        var product;

        for (product in this.products) {
            if (this.products.hasOwnProperty(product)) {
                if (this.products[product] instanceof CartProduct && this.products[product].qty > 0) {
                    this.products[product].remove();
                    this.products[product].pushChanges();
                }
            }
        }

        this.update();

        return this;

    };

    Cart.prototype.showFreightShipmentNotice = function () {

        if ( !this.freightMessage ) {

            if ( Handlebars && Handlebars.templates && Handlebars.templates[templates.freightShipmentNotice] ) {

                // Display the freight message and store it for future access.
                this.freightMessage = $(Handlebars.templates[templates.freightShipmentNotice]()).appendTo(this.messageHolder).get(0);

            }

            // Hide the PayPal button.
            $(this.paypalButton).addClass(classes.hidePaypal);

            // Update the shipping estimator.
            if (this.shippingEstimator instanceof ShippingEstimator) {
                this.shippingEstimator.showFreightShipmentNotice();
            }

        }

        return this;

    };

    Cart.prototype.hideFreightShipmentNotice = function () {

        if ( this.freightMessage ) {

            // Hide the message.
            $(this.freightMessage).remove();

            // Show the PayPal button.
            $(this.paypalButton).removeClass(classes.hidePaypal);

            // Delete the property from the Cart.
            delete this.freightMessage;

            // Update the shipping estimator.
            if (this.shippingEstimator instanceof ShippingEstimator) {
                this.shippingEstimator.hideFreightShipmentNotice();
            }

        }

        return this;

    };

    Cart.prototype.updateShipDateDisplay = function () {

        $(this.shipDateDisplay).text(this.shipDate);

        if (this.shippingEstimator instanceof ShippingEstimator) {
            this.shippingEstimator.updateShipDateDisplay(this.shipDate);
        }

        return this;

    };

    return Cart;

})(),

CartProduct = (function () {

    var
    STOCK_AVAILABLE = 0,
    STOCK_LIMITED = 1,
    STOCK_UNAVAILABLE = 2,
    cartproduct_name_subselector = "div.product-name",
    cartproduct_image_subselector = "td.sign-thumb img",
    cartproduct_sku_subselector = "span.cartproduct-sku",
    cartproduct_size_subselector = "span.cartproduct-size",
    cartproduct_material_subselector = "span.cartproduct-material",
    cartproduct_qtyinput_subselector = "input.add-to-cart-input",
    cartproduct_updateform_subselector = "form[name=\"cartupdate\"]",
    cartproduct_removeform_subselector = "form[name=\"cartremove\"]",
    cartproduct_unitprice_subselector = "span.unitprice",
    cartproduct_totalprice_subselector = "span.totalprice",
    cartproduct_stocknotice_subselector = "p.inventory-alert",
    cartproduct_details_subselector = "div.custom-attributes",
    cartproduct_detailtrigger_class = "show-hide-product-attributes",
    cartproduct_available_class = "cartproduct-available",
    cartproduct_limited_class = "cartproduct-limited",
    cartproduct_unavailable_class = "cartproduct-unavailable",
    cartproduct_available_text = "",
    cartproduct_limited_text = "Only {{inventory}} in stock.",
    cartproduct_unavailable_text = "No longer available.",
    cartproduct_detailtrigger_text_on = "-",
    cartproduct_detailtrigger_text_off = "+",
    cartproduct_details_collapsed_class = "collapsed",
    cartproduct_remove_text = "Are you sure you want to remove that item from your cart?",
    parseInventory = function (inventory) {
        inventory = parseInt(inventory, 10);
        return isNaN(inventory) ? Infinity : inventory;
    };

    function CartProduct (e, cart) {

        var $e = $(e), data = $e.data();

        this.e = e;
        this.cart = (cart instanceof Cart ? cart : undefined);
        this.id = parseInt(data.cartProductId, 10);
        this.name = $e.find(cartproduct_name_subselector).text();
        this.image = $e.find(cartproduct_image_subselector).attr("src");
        this.sku = $e.find(cartproduct_sku_subselector).text();
        this.size = $e.find(cartproduct_size_subselector).text();
        this.material = $e.find(cartproduct_material_subselector).text();
        this.qty = parseInt(data.cartProductQty, 10);
        this.inventory = parseInventory(data.cartInventory);
        this.input = $e.find(cartproduct_qtyinput_subselector).get(0);
        this.update_form = $e.find(cartproduct_updateform_subselector).get(0);
        this.remove_form = $e.find(cartproduct_removeform_subselector).get(0);
        this.unitprice_display = $e.find(cartproduct_unitprice_subselector).get(0);
        this.totalprice_display = $e.find(cartproduct_totalprice_subselector).get(0);
        this.stockNotice = $e.find(cartproduct_stocknotice_subselector).get(0);
        this.unitprice = +$(this.unitprice_display).text();
        this.totalprice = this.unitprice * this.qty;
        this.details = $e.find(cartproduct_details_subselector).get(0);
        this.details_active = this.checkDetailStatus();

    }

    CartProduct.prototype.init = function () {

        var _this = this;

        // Prepare the detail trigger.
        this.detailtrigger = document.createElement("div");
        $(this.detailtrigger).addClass(cartproduct_detailtrigger_class).text(this.details_active ? cartproduct_detailtrigger_text_on : cartproduct_detailtrigger_text_off).on("click", function (event) {
            _this.toggleDetails();
        });

        // Insert the detail trigger into the DOM.
        $(this.details).prepend(this.detailtrigger);

        // Prepare the update button.
        $(this.update_form).on("submit", function (event) {
            event.preventDefault();
            _this.setQuantity($(_this.input).val());
            _this.pushChanges(true);
        });

        // Prepare the remove button.
        $(this.remove_form).on("submit", function (event) {
            event.preventDefault();
            if (window.confirm(cartproduct_remove_text)) {
                _this.remove();
                _this.pushChanges(true);
            }
        });

    };

    CartProduct.prototype.update = function (qty, price, inventory) {

        // Update the quantity, price, and available inventory.
        this.setQuantity(qty);
        this.setPrice(price);
        this.setInventory(inventory);

        // Update the quantity input to match the latest value.
        $(this.input).val(this.qty);

        // Update the pricing display.
        $(this.unitprice_display).text(formatPrice(this.unitprice, ""));
        $(this.totalprice_display).text(formatPrice(this.unitprice * this.qty, ""));

        // Hide the product if it's no longer in the cart.
        if (this.qty === 0) {
            this.hide();
        }

    };

    CartProduct.prototype.updateStockStatus = function () {

        var stockStatus;

        // Use the updated information to determine the status of the inventory relative to the quantity in the cart.
        stockStatus = this.getStockStatus();

        // Display the stock status.
        if ( stockStatus === STOCK_UNAVAILABLE ) {
            $(this.e).removeClass(cartproduct_available_class).removeClass(cartproduct_limited_class).addClass(cartproduct_unavailable_class);
            $(this.stockNotice).text(cartproduct_unavailable_text);
        } else if ( stockStatus === STOCK_LIMITED ) {
            $(this.e).removeClass(cartproduct_available_class).removeClass(cartproduct_unavailable_class).addClass(cartproduct_limited_class);
            $(this.stockNotice).text(cartproduct_limited_text.replace("{{inventory}}", this.inventory));
        } else {
            $(this.e).removeClass(cartproduct_limited_class).removeClass(cartproduct_unavailable_class).addClass(cartproduct_available_class);
            $(this.stockNotice).text(cartproduct_available_text);
        }

        return this;

    };

    CartProduct.prototype.isAvailable = function () {
        return this.getStockStatus() === STOCK_AVAILABLE;
    };

    CartProduct.prototype.hide = function () {
        $(this.e).hide();
    };

    CartProduct.prototype.pushChanges = function (send) {

        if (this.cart instanceof Cart) {

            this.cart.pushToQueue({
                id: this.id,
                qty: this.qty
            });

            if (send === true) {
                this.cart.update();
            }

        }

    };

    CartProduct.prototype.setQuantity = function (qty) {
        this.qty = parseInt(qty, 10);
    };

    CartProduct.prototype.setPrice = function (unitprice) {
        this.unitprice = +unitprice;
        this.totalprice = this.unitprice * this.qty;
    };

    CartProduct.prototype.setInventory = function (inventory) {

        this.inventory = parseInventory(inventory);
        return this;

    };

    CartProduct.prototype.remove = function () {
        this.setQuantity(0);
    };

    CartProduct.prototype.toggleDetails = function () {

        if (this.details_active) {

            this.hideDetails();

        } else {

            this.showDetails();

        }

    };

    CartProduct.prototype.showDetails = function () {

        this.details_active = true;
        $(this.details).removeClass(cartproduct_details_collapsed_class);
        $(this.detailtrigger).text(cartproduct_detailtrigger_text_on);

    };

    CartProduct.prototype.hideDetails = function () {

        this.details_active = false;
        $(this.details).addClass(cartproduct_details_collapsed_class);
        $(this.detailtrigger).text(cartproduct_detailtrigger_text_off);

    };

    CartProduct.prototype.checkDetailStatus = function () {
        return !$(this.details).hasClass(cartproduct_details_collapsed_class);
    };

    CartProduct.prototype.disableForms = function () {
        $(this.update_form).add(this.remove_form).find(":input").prop("disabled", true);
    };

    CartProduct.prototype.enableForms = function () {
        $(this.update_form).add(this.remove_form).find(":input").prop("disabled", false);
    };

    CartProduct.prototype.getStockStatus = function () {

        var stockStatus, i, len, products, quantity;

        // If this CartProduct is part of a Cart, get the total quantity of all products in the cart with the same SKU. Otherwise just use this item's quantity.
        if ( this.cart instanceof Cart ) {
            quantity = this.cart.countProducts(this.sku);
        } else {
            quantity = this.qty;
        }

        // Compare the quantity to available inventory to determine the status.
        if ( quantity <= this.inventory ) {
            stockStatus = STOCK_AVAILABLE;
        } else if ( this.inventory > 0 ) {
            stockStatus = STOCK_LIMITED;
        } else {
            stockStatus = STOCK_UNAVAILABLE;
        }

        return stockStatus;

    };

    return CartProduct;

})(),

Grid = (function () {

    var

    grid_productgrid_selector = "div.product-grid", // The grid of products.
    grid_products_subselector = "a.product", // Descendant of the grid of products; these are the actual products that will be manipulated.
    grid_controls_selector = ".sort-filter", // The controls.
    grid_status_selector = ".feature-filter", // The status bar.
    grid_status_count_subselector = "p.product-grid-count", // Descendant of the status bar that displays the product count.
    grid_status_filterlist_subselector = "ul.product-grid-filterlist", // Descendant of the status bar that displays the currently-applied filters. Must be a UL.
    grid_control_filter_subselector = "div.product-grid-filter input[type=\"checkbox\"]", // Descendants of the controls that are filter controls. Must be checkbox-type input elements.
    grid_control_sort_subselector = "div.product-grid-sort select", // Descendant of the controls that is the sort control. Must be a select element.
    grid_control_showall_subselector = "div.product-grid-filter p.product-grid-showall", // Descendant of the controls that removes all applied filters.
    grid_filter_count_subselector = "span.filter-count", // Sibling of filter inputs that displays the count.
    grid_product_name_subselector = "div.product-name .product-name-inner", // Descendant of product that holds the product name.
    grid_product_num_subselector = "span.product-num", // Descendant of product that holds the product number.
    grid_dropdown_selector = ".product-grid-dropdown", // Dropdowns.
    grid_trigger_selector = ".product-grid-dropdown-trigger", // Descendants of a dropdown that expands and collapses.
    grid_close_selector = ".product-grid-closedropdown", // Descendants of a dropdown that collapses.
    grid_dropdown_active_class = "active", // The class that will be added to a dropdown to expand it.
    grid_status_clear_selector = "p.product-grid-clearfilters", // Desendant of the status bar that removes all applied filters.
    grid_row_selector = "div.row", // Descendants of the grid of products that holds each row products.
    grid_hider_selector = "div.product-grid-hider", // Descendants of the grid of products where invisible products will be moved.
    grid_status_active_class = "active", // The class that will be added to the status bar when filters are active.
    grid_quickview_selector = "div.quick-view > span.label", // Descendant of a product that triggers the Quick View display.
    grid_query_attr = "productGridSearchQuery",
    grid_rowlimit_attr = "productGridRowlimit",
    grid_pagenum_attr = "productGridPageNum",
    grid_productlimit_attr = "productGridLimit",
    grid_trackid_attr = "productGridTrackingId",
    grid_default_sort = "position";

    function Grid (e) {

        var $e = $(e),
            data = $e.data(),
            rowlimit = parseInt(data[grid_rowlimit_attr], 10),
            pagenum = parseInt(data[grid_pagenum_attr], 10),
            productlimit = parseInt(data[grid_productlimit_attr], 10);

        this.e = e;
        this.status = {
            e: $e.find(grid_status_selector).get(0),
            filters: $e.find(grid_status_selector + " " + grid_status_filterlist_subselector).get(0),
            count: $e.find(grid_status_selector + " " + grid_status_count_subselector).get(0)
        };
        this.grid = {
            e: $e.find(grid_productgrid_selector).get(0),
            rows: $.makeArray($e.find(grid_productgrid_selector + " " + grid_row_selector)),
            hider: $e.find(grid_productgrid_selector + " " + grid_hider_selector).get(0)
        };
        this.query = data[grid_query_attr];
        this.trackid = data[grid_trackid_attr];
        this.pagenum = pagenum > 0 ? pagenum : 1;
        this.productlimit = productlimit > 0 ? productlimit : Infinity;
        this.products = this.getProducts();
        this.controls = this.getControls();
        this.rowlimit = rowlimit >= 1 ? rowlimit : 1;
        this.filters = [];
        this.sort = grid_default_sort;

    }

    Grid.prototype.init = function () {

        this.prepareInterface();
        this.rebuild();

    };

    Grid.prototype.getProducts = function () {

        var products = [], ptype_regex = /^productType([A-Z])/, cap_regex = /[A-Z]/;

        $(this.grid.e).find(grid_products_subselector).each(function (i, e) {

            var $e = $(e), data_attrs = $e.data(), data_attr, types = [];

            for (data_attr in data_attrs) {
                if (data_attrs.hasOwnProperty(data_attr)) {
                    if (data_attrs[data_attr] === true && ptype_regex.test(data_attr)) {
                        types.push(trim(data_attr.replace(ptype_regex, removePrefix).replace(cap_regex, unCamelCase)));
                    }
                }
            }

            products.push(new GridProduct(e, data_attrs.productId, i, trim($e.find(grid_product_name_subselector).text()), trim($e.find(grid_product_num_subselector).text()), types, $e.find(grid_quickview_selector).get(0)));

        });

        return products;

    };

    Grid.prototype.getControls = function () {

        var $controls = $(this.e).find(grid_controls_selector),
            controls = { filters : {} , sorts : {} },
            $showall = $controls.find(grid_control_showall_subselector);

        // Filter controls.

        $controls.find(grid_control_filter_subselector).each(function (i, e) {

            var $e = $(e), val = trim($e.val());
            controls.filters[val] = new GridControl(e, $e.siblings(grid_filter_count_subselector).get(0), $e.closest("li").get(0), "filter", val, trim($e.parent().text()));

        });

        // Sort controls.

        $controls.find(grid_control_sort_subselector).each(function (i, e) {

            var $e = $(e), val = trim($e.val());
            controls.sort = new GridControl(e, null, null, "sort", val, null);

        });

        // Show All control.

        if ($showall.length > 0) {
            controls.showall = new GridControl($showall.get(0), null, null, "showall", null);
        }

        return controls;

    };

    Grid.prototype.prepareInterface = function () {

        this.prepareProducts();
        this.prepareControls();
        this.prepareDropdowns();
        this.prepareStatusBar();

    };

    Grid.prototype.prepareProducts = function () {

        var i, len;

        for (i = 0, len = this.products.length; i < len; i++) {
            this.products[i].prepare(this);
        }

    };

    Grid.prototype.prepareDropdowns = function () {

        $(this.e).find(grid_dropdown_selector).each(function (i, e) {

            var $dropdown = $(e);

            $dropdown.on("click", grid_trigger_selector, function () {

                $dropdown.toggleClass(grid_dropdown_active_class);

            }).on("click", grid_close_selector, function () {

                $dropdown.removeClass(grid_dropdown_active_class);

            });

            $(document).on("click", function (event) {
                if (!$dropdown.is(event.target) && !$dropdown.has(event.target).length) {
                    $dropdown.removeClass(grid_dropdown_active_class);
                }
            });

        });

    };

    Grid.prototype.prepareStatusBar = function () {

        var grid = this, $status = $(this.status.e);

        $status.on("click", grid_status_clear_selector, function () {

            grid.showAllProducts();

        });

        $(this.status.filters).on("click", "li", function () {

            grid.deactivateFilter($(this).data("val"));
            grid.rebuild();

        });

    };

    Grid.prototype.prepareControls = function () {

        var type, val;

        // Prepare the controls.
        for (type in this.controls) {
            if (this.controls.hasOwnProperty(type)) {
                if (this.controls[type] instanceof GridControl) {
                    this.controls[type].prepare(this);
                } else {
                    for (val in this.controls[type]) {
                        if (this.controls[type].hasOwnProperty(val)) {
                            this.controls[type][val].prepare(this);
                        }
                    }
                }
            }
        }

    };

    Grid.prototype.rebuild = function () {

        this.addRemoveProducts();
        this.sortProducts();
        this.updateGrid();

    };

    Grid.prototype.updateGrid = function () {

        var i, len, product, pos = 0, row = 0, $rows = $(this.grid.rows), $hider = $(this.grid.hider), index = 0;

        // Rearrange the elements to match the sorted products.
        for (i = 0, len = this.products.length; i < len; i++) {

            product = this.products[i];

            if (product.visible) {

                product.updateIndex(index);
                index++;

                if (pos >= this.rowlimit) {
                    pos = 0;
                    row++;
                }

                $rows.eq(row).append(product.e);

                pos++;

            } else {

                $hider.append(product.e);

            }

        }

    };

    Grid.prototype.addRemoveProducts = function () {

        var control, control_count, filter;

        this.updateProductVisibility();

        for (filter in this.controls.filters) {

            if (this.controls.filters.hasOwnProperty(filter)) {

                control = this.controls.filters[filter];
                control_count = this.checkProducts(control.val).length;
                control.update(control_count);

            }

        }

        this.updateStatusMessage();

    };

    Grid.prototype.updateProductVisibility = function () {

        var i, len, product, visible = this.checkProducts();

        for (i = 0, len = this.products.length; i < len; i++) {

            product = this.products[i];

            if ($.inArray(product, visible) !== -1) {
                product.setAsVisible();
            } else {
                product.setAsInvisible();
            }

        }

    };

    Grid.prototype.checkProducts = function (extrafilter) {

        var i, j, len, len2, product, include, show = [], filters = [];

        // Copy the filters into a temporary array.
        for (i = 0, len = this.filters.length; i < len; i++) {
            filters.push(this.filters[i]);
        }

        // If it was defined and not already set as one of the filters, add the extra filter from the argument to the array.
        if (typeof extrafilter !== "undefined" && extrafilter !== null && $.inArray(extrafilter, filters) === -1) {
            filters.push(extrafilter);
        }

        // Loop through each product.
        for (i = 0, len = this.products.length; i < len; i++) {

            product = this.products[i];
            include = true;

            // Loop through each filter.
            for (j = 0, len2 = filters.length; j < len2; j++) {

                // If the product does not match the filter, exclude it.
                if ($.inArray(filters[j], product.types) === -1) {
                    include = false;
                    break;
                }

            }

            // If the product passed all filters, add it to the output array.
            if (include) {
                show.push(product);
            }

        }

        return show;

    };

    Grid.prototype.sortProducts = function () {

        // Sort the products.
        if (this.sort === "position") { this.products.sort(sortProductsByPosition); }
        else if (this.sort === "name-asc") { this.products.sort(sortProductsByNameAscending); }
        else if (this.sort === "name-desc") { this.products.sort(sortProductsByNameDescending); }
        else if (this.sort === "num-asc") { this.products.sort(sortProductsByNumberAscending); }
        else if (this.sort === "num-desc") { this.products.sort(sortProductsByNumberDescending); }

    };

    Grid.prototype.addFilter = function (filter) {

        var present = false, i, len;

        filter = ""+filter;

        for (i = 0, len = this.filters.length; i < len; i++) {
            if (this.filters[i] === filter) {
                present = true;
                break;
            }
        }

        if (!present) {
            this.filters.push(filter);
        }

    };

    Grid.prototype.removeFilter = function (filter) {

        var i, len;

        for (i = 0, len = this.filters.length; i < len; i++) {
            if (this.filters[i] === filter) {
                this.filters.splice(i, 1);
                break;
            }
        }

    };

    Grid.prototype.showAllProducts = function () {

        this.deactivateAllFilters();
        this.rebuild();

    };

    Grid.prototype.deactivateAllFilters = function () {

        this.removeAllFilters();
        this.uncheckAllFilterControls();

    };

    Grid.prototype.removeAllFilters = function () {

        this.filters = [];

    };

    Grid.prototype.deactivateFilter = function (filter) {

        this.removeFilter(filter);
        this.uncheckFilterControl(filter);

    };

    Grid.prototype.uncheckAllFilterControls = function () {

        var filter;

        for (filter in this.controls.filters) {
            if (this.controls.filters.hasOwnProperty(filter)) {
                this.uncheckFilterControl(filter);
            }
        }

    };

    Grid.prototype.uncheckFilterControl = function (filter) {

        if (this.controls.filters[filter] instanceof GridControl) {
            this.controls.filters[filter].uncheck();
        }

    };

    Grid.prototype.setSort = function (sort) {
        this.sort = ""+sort;
    };

    Grid.prototype.countVisibleProducts = function () {

        var i, len, count = 0;

        for (i = 0, len = this.products.length; i < len; i++) {
            if (this.products[i].visible) { count++; }
        }

        return count;

    };

    Grid.prototype.updateStatusMessage = function () {

        var i,
            len,
            total = this.products.length,
            $status = $(this.status.e),
            $filterlist = $(this.status.filters),
            count = this.countVisibleProducts();

        count = parseInt(count, 10);

        // Update the visible count message.
        $(this.status.count).text("Viewing " + count + " of " + total + " " + (total > 1 ? "items" : "item"));

        // Empty the list of filters.
        $filterlist.empty();

        // If there are any active filters...
        if (this.filters.length > 0) {

            // Show the filter bar.
            $status.addClass(grid_status_active_class);

            // Add each active filter to the list.
            for (i = 0, len = this.filters.length; i < len; i++) {
                $("<li />").text(this.controls.filters[this.filters[i]].name).data("val", this.filters[i]).appendTo($filterlist);
            }

        // If there are no active filters...
        } else {

            // Hide the filter bar.
            $status.removeClass(grid_status_active_class);

        }

    };

    Grid.prototype.getQuickviewLinkList = function () {

        var i, len, product, list = [];

        for (i = 0, len = this.products.length; i < len; i++) {

            product = this.products[i];

            if (product.visible) {

                list.push({
                    href: product.quickviewurl,
                    title: product.name,
                    type: "ajax"
                });

            }

        }

        return list;

    };

    Grid.prototype.searchTrackingEnabled = function () {
        return typeof this.query === "string" && this.query.length > 0 && typeof this.trackid === "string" && this.trackid.length > 0;
    };

    Grid.prototype.getQuery = function () {
        return this.query;
    };

    Grid.prototype.getPageNum = function () {
        return this.pagenum;
    };

    Grid.prototype.getProductLimit = function () {
        return this.productlimit;
    };

    Grid.prototype.getTrackingId = function () {
        return this.trackid;
    };

    return Grid;

})(),

GridControl = (function () {

    var

    grid_control_inactive_class = "transparency-50"; // The class that will be added to the closest ancestor LI of a filter control when that control is inactive.

    function GridControl (e, display, wrap, type, val, name) {

        this.e = e;
        this.display = display;
        this.wrap = wrap;
        this.type = "" + type;
        this.val = "" + val;
        this.name = "" + name;

    }

    GridControl.prototype.disable = function () {

        $(this.e).prop("disabled", true);
        $(this.wrap).addClass(grid_control_inactive_class);

    };

    GridControl.prototype.enable = function () {

        $(this.e).prop("disabled", false);
        $(this.wrap).removeClass(grid_control_inactive_class);

    };

    GridControl.prototype.update = function (count) {

        var msg = "";

        count = parseInt(count, 10);

        if (count > 0) {
            this.enable();
            if (!this.checked()) { msg = "(" + count + ")"; }
        } else {
            this.disable();
        }

        $(this.display).text(msg);

    };

    GridControl.prototype.checked = function () {
        return !!$(this.e).prop("checked");
    };

    GridControl.prototype.uncheck = function () {
        $(this.e).prop("checked", false);
    };

    GridControl.prototype.prepare = function (grid) {

        var control = this;

        if (this.type === "filter") {

            $(this.e).on("change", function () {
                if (control.checked()) {
                    grid.addFilter(control.val);
                } else {
                    grid.removeFilter(control.val);
                }
                grid.rebuild();
            });

        } else if (this.type === "sort") {

            $(this.e).on("change", function () {
                control.updateVal();
                grid.setSort(control.val);
                grid.rebuild();
            }).customSelect();

        } else if (this.type === "showall") {

            $(this.e).on("click", function () {
                grid.showAllProducts();
            });

        }

    };

    GridControl.prototype.updateVal = function () {

        this.val = trim($(this.e).val());

    };

    return GridControl;

})(),

GridProduct = (function () {

    var
    initializeQuickViewContent = function () {

        // Note: This function is intended for use only as a FancyBox callback.

        // Destroy existing content.
        destroyQuickViewContent();

        // Initialize price grids.
        $(this.inner).find("table.scrollable-price-grid").each(function (i, e) {
            var priceGrid = new PriceGrid(e, true);
            quickViewPriceGrids.push(priceGrid);
            priceGrids.push(priceGrid);
        });

        // Initialize UL Recognition notes.
        $(this.inner).find("div.ul-note").each(function (i, e) {
            quickViewUlRecognitionNotes.push((new UlRecognitionNote(e)).init());
        });

    },
    destroyQuickViewContent = function () {

        // Note: This function is intended for use only as a FancyBox callback.

        var i, len, j, len2;

        // Destroy price grids.
        if ( quickViewPriceGrids.length > 0 ) {
            for ( i = 0, len = priceGrids.length; i < len; i++ ) {
                for ( j = 0, len2 = quickViewPriceGrids.length; j < len2; j++ ) {
                    if ( priceGrids[i] === quickViewPriceGrids[j] ) {
                        priceGrids[i].destroy();
                        priceGrids.splice(i, 1);
                    }
                }
            }
            quickViewPriceGrids = [];
        }

        // Destroy the UL Recognition notes.
        if ( quickViewUlRecognitionNotes.length > 0 ) {
            for ( i = 0, len = quickViewUlRecognitionNotes.length; i < len; i++ ) {
                quickViewUlRecognitionNotes[i].destroy();
            }
            quickViewUlRecognitionNotes = [];
        }

    };

    function GridProduct (e, id, position, name, sku, types, quickview) {

        this.e = e;
        this.id = id;
        this.position = parseInt(position, 10);
        this.index = parseInt(position, 10);
        this.name = "" + name;
        this.sku = "" + sku;
        this.types = types;
        this.quickview = quickview;
        this.quickviewurl = this.getQuickviewUrl();
        this.visible = true;

    }

    GridProduct.prototype.updateVisibility = function (visible) {

        this.visible = !!visible;

    };

    GridProduct.prototype.setAsVisible = function () {

        this.updateVisibility(true);

    };

    GridProduct.prototype.setAsInvisible = function () {

        this.updateVisibility(false);

    };

    GridProduct.prototype.getQuickviewUrl = function () {
        return "/quickview/p" + encodeURIComponent(this.id);
    };

    GridProduct.prototype.updateIndex = function (i) {

        this.index = parseInt(i, 10);

    };

    GridProduct.prototype.prepare = function (Grid) {

        var GridProduct = this;

        if ($.fancybox) {

            $(this.quickview).on("click", function (e) {
                e.preventDefault();
                $.fancybox(Grid.getQuickviewLinkList(), {
                    index: GridProduct.index,
                    mouseWheel: false,
                    maxWidth: 800,
                    afterShow: initializeQuickViewContent,
                    beforeClose: destroyQuickViewContent
                });
            });

        }

        if ( Grid.searchTrackingEnabled() ) {

            $(this.e).on("click", function (event) {

                $(document.createElement("img")).attr("src", "http://analytics.nextopia.net/x.php?r=" + Math.floor(Math.random()*200) + "&v=" + encodeURIComponent(Grid.getQuery()) + "&w=" + encodeURIComponent(GridProduct.sku) + "&x=" + encodeURIComponent( GridProduct.position + 1 + ( (Grid.getPageNum() - 1) * Grid.getProductLimit() ) ) + "&z=1&y=" + encodeURIComponent(Grid.getTrackingId()));

            });

        }

    };

    return GridProduct;

})(),

MiniCart = (function () {

    var
    previewItemLimit = 3,
    templates = {
        preview: 'miniCart'
    },
    urls = {
        previewData: "/shoppingcartpreview.php"
    },
    selectors = {
        button: ".minicart-button",
        quantity: ".minicart-quantity",
        price: ".minicart-price",
        preview: ".minicart-preview"
    },
    classes = {
        active: "minicart-active",
        loading: "minicart-loading",
        loaded: "minicart-loaded",
        empty: "minicart-empty"
    },
    text = {
        zeroPrice: "Cart Empty",
        zeroQuantity: "0",
        additionalItemsText: "+ {{itemCount}} more {{itemLabel}} in the cart",
        buttonText: "View Cart ({{itemCount}} {{itemLabel}})",
        itemLabelSingular: "item",
        itemLabelPlural: "items"
    },
    formatQuantity = function (qty) {
        return "" + parseQuantity(qty);
    },
    parseQuantity = function (qty) {
        return parseInt(qty, 10) || 0;
    };

    function MiniCart (e) {

        var $e = $(e).eq(0);

        // Find DOM elements.
        this.dom = {
            $root: $e,
            $button: $e.find(selectors.button).eq(0),
            $quantity: $e.find(selectors.quantity).eq(0),
            $price: $e.find(selectors.price).eq(0),
            $preview: $e.find(selectors.preview).eq(0)
        };

        // Initialize properties.
        this.quantity = parseQuantity(this.dom.$quantity.text());
        this.price = parsePrice(this.dom.$price.text());
        this.previewCached = false;

    }

    MiniCart.prototype.init = function () {

        if ( $().hoverIntent ) {

            // Attach the hoverIntent in function to the button.
            this.dom.$button.hoverIntent(function () {
                this.activate();
            }.bind(this), function () {});

            // Attach the hoverIntent out function to the root.
            this.dom.$root.hoverIntent(function () {}, function () {
                this.deactivate();
            }.bind(this));

        }

        // Update the button text and class.
        this._updateTotals();

        return this;

    };

    MiniCart.prototype.activate = function () {

        // Update the class.
        this.dom.$root.addClass(classes.active);

        // If there are items in the cart and the preview isn't already cached, load the preview.
        if ( this.quantity > 0 && !this.previewCached ) {

            // Update the class.
            this.dom.$root.addClass(classes.loading);

            // Get the cart data from the server.
            $.ajax({
                url: urls.previewData,
                dataType: "json",
                success: function (data, textStatus, jqXHR) {

                    var i, len, parsed = {}, omittedItems, omittedItemCount;

                    // Parse the data.
                    parsed.totalPrice = data.totalPrice;
                    parsed.totalQuantity = data.totalQuantity;
                    parsed.cartLink = data.cartLink;
                    parsed.guaranteeLink = data.guaranteeLink;
                    parsed.buttonText = text.buttonText.replace("{{itemCount}}", data.totalQuantity).replace("{{itemLabel}}", data.totalQuantity > 1 ? text.itemLabelPlural : text.itemLabelSingular);

                    if ( data.items ) {

                        // Parse the items. Also reduce the item rows displayed to the limit if necessary.
                        parsed.items = data.items.slice(0, previewItemLimit);

                        // If the item rows were truncated...
                        if ( data.items.length > previewItemLimit ) {

                            // Count the total quantity of items truncated.
                            omittedItems = data.items.slice(previewItemLimit);
                            omittedItemCount = 0;
                            for ( i = 0, len = omittedItems.length; i < len; i++ ) {
                                omittedItemCount += +omittedItems[i].quantity;
                            }

                            // Generate a message to explain that there are items in the cart not behing shown.
                            parsed.additionalItemsText = text.additionalItemsText.replace("{{itemCount}}", omittedItemCount).replace("{{itemLabel}}", omittedItemCount > 1 ? text.itemLabelPlural : text.itemLabelSingular);

                        }

                    }

                    // Update the totals and the preview.
                    this._updateTotals(parsed.totalQuantity, parsed.totalPrice)._updatePreview(parsed);

                }.bind(this),
                error: function (jqXHR, textStatus, errorThrown) {

                    // Update the preview.
                    this._clearPreview();

                }.bind(this),
                complete: function (jqXHR, textStatus) {

                    // Update the class.
                    this.dom.$root.removeClass(classes.loading);

                }.bind(this)
            });

        }

        return this;

    };

    MiniCart.prototype.deactivate = function () {

        // Update the class.
        this.dom.$root.removeClass(classes.active);

        return this;

    };

    MiniCart.prototype.update = function (quantity, price) {

        return this._clearPreview()._updateTotals(quantity, price);

    };

    MiniCart.prototype._updateTotals = function (quantity, price) {

        // Update the quantity if it was provided.
        if ( typeof quantity !== "undefined" && quantity !== null ) {
            this.quantity = parseQuantity(quantity);
        }

        // Update the price if it was provided.
        if ( typeof price !== "undefined" && price !== null ) {
            this.price = parsePrice(price);
        }

        // Update the button text.
        this.dom.$price.text(this.price > 0 ? formatPrice(this.price) : text.zeroPrice);
        this.dom.$quantity.text(this.quantity > 0 ? formatQuantity(this.quantity) : text.zeroQuantity);

        // Update the class.
        if ( this.quantity > 0 ) {
            this.dom.$root.removeClass(classes.empty);
        } else {
            this.dom.$root.addClass(classes.empty);
        }

        return this;

    };

    MiniCart.prototype._clearPreview = function () {

        // Remove the preview from the DOM.
        this.dom.$preview.empty();

        // Update the cache status.
        this.previewCached = false;

        // Update the class.
        this.dom.$root.removeClass(classes.loaded);

        return this;

    };

    MiniCart.prototype._updatePreview = function (data) {

        if ( typeof data !== "undefined" && data !== null ) {

            // Render the preview.
            if ( Handlebars && Handlebars.templates && Handlebars.templates[templates.preview] ) {
                this.dom.$preview.html(Handlebars.templates[templates.preview](data));
            }

            // Update the cache status.
            this.previewCached = true;

            // Update the class.
            this.dom.$root.addClass(classes.loaded);

        }

        return this;

    };

    return MiniCart;

})(),

Product = (function () {

    var
    templates = {
        addedToCartSuccess: "addedToCartSuccess",
        addedToCartError: "addedToCartError"
    },
    form_selector = "form.addtocart",
    qty_subselector = "input[name=\"qty\"]",
    upcharge_dialog_subselector = "div.product-options",
    cart_submit_type = "POST",
    cart_error_headline = "Could Not Add Item to Cart",
    customer_service_msg = "Please contact our customer service department at 800-274-6271 for assistance.",
    loading_class = "loading",
    upcharge_dialog_active_class = "show-options",
    success_dialog_closebutton_selector = ".button.cancel",
    error_dialog_closebutton_selector = ".button.cancel",
    quantity_error_msg = "You must enter a valid quantity.",
    dialog_reverse_class = "reversed";

    function Product (e) {

        var $e = $(e), $form = $e.find(form_selector).first();

        this.form = $form.get(0);
        this.submit_url = $form.attr("action");
        this.qty_input = $form.find(qty_subselector).get(0);
        this.upcharge_dialog = $form.find(upcharge_dialog_subselector).get(0);

    }

    Product.prototype.init = function () {

        var _this = this,
            $form = $(this.form),
            dialog_hover = false,
            dialog_focus = false,
            timer;

        products.push(this);

        $form.on("submit", function (event) {
            event.preventDefault();
            hideProductDialogs({
                success: true,
                error: true,
                upcharge: true
            });
            _this.addToCart();
        }).on("focusin", function (event) {
            window.clearTimeout(timer);
        }).on("focusout", function (event) {
            timer = window.setTimeout(function () {
                dialog_focus = false;
                if (!dialog_hover) {
                    _this.hideUpchargeDialog();
                }
            }, 125);
        });

        if ($().hoverIntent) {

            $form.hoverIntent({
                timeout: 500,
                over: function () {
                    dialog_hover = true;
                },
                out: function () {
                    dialog_hover = false;
                    if (!dialog_focus) {
                        _this.hideUpchargeDialog();
                    }
                }
            });

        }

        $(this.qty_input).on("focus", function (event) {
            dialog_focus = true;
            hideProductDialogs({
                success: true,
                error: true,
                upcharge: true,
                except: this
            });
            _this.showUpchargeDialog();
        });

    };

    Product.prototype.addToCart = function () {

        var _this = this;

        if (this.getQuantityVal() > 0) {

            this.showLoadingMessage();

            $.ajax({
                url: this.submit_url,
                type: cart_submit_type,
                data: $(this.form).serialize(),
                success: function (data, textStatus, jqXHR) {
                    _this.parseCartResponse(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    _this.showErrorDialog();
                },
                complete: function (jqXHR, textStatus) {
                    _this.hideLoadingMessage();
                }
            });

        } else {

            this.showErrorDialog(quantity_error_msg);

        }


    };

    Product.prototype.parseCartResponse = function (data) {

        var i, len, parsed = {};

        if (data) {

            if ( data.success === true ) {

                // Parse the response.
                parsed.oldQuantity = data.oldQuantity;
                parsed.newQuantity = data.newQuantity;
                parsed.quantityAdded = data.newQuantity - data.oldQuantity;
                parsed.unitPrice = data.unitprice;
                parsed.totalPrice = data.unitprice * data.newQuantity;
                parsed.savings = data.savings;
                parsed.image = data.image;
                parsed.cartUrl = data.carturl;
                parsed.accuracyImage = data.accuracyImage;
                parsed.imageUploaded = data.imageUploaded;
                parsed.attrs = [];
                if ( data.attrs instanceof Array && data.attrs.length > 0 ) {
                    for ( i = 0, len = data.attrs.length; i < len; i++ ) {
                        if ( typeof data.attrs[i].attr === "string" && data.attrs[i].attr.length > 0 && typeof data.attrs[i].val === "string" && data.attrs[i].val.length > 0 ) {
                            parsed.attrs.push({ attribute: data.attrs[i].attr, value: data.attrs[i].val });
                        }
                    }
                }

                window.ss.site.updateMiniCart(data.cartcount, data.subtotal);
                this.showSuccessDialog(parsed);
                this.resetQuantityInput();

            } else {

                parsed.errors = [];
                if ( data.errors instanceof Array && data.errors.length > 0 ) {
                    for ( i = 0, len = data.errors.length; i < len; i++ ) {
                        if ( typeof data.errors[i] === "string" && data.errors[i].length > 0 ) {
                            parsed.errors.push(data.errors[i]);
                        }
                    }
                }

                this.showErrorDialog(parsed.errors);

            }

        }

    };

    Product.prototype.showSuccessDialog = function (data) {

        var $dialog;

        if ( typeof data !== "undefined" && data !== null && Handlebars && Handlebars.templates && Handlebars.templates[templates.addedToCartSuccess] ) {

            // Create the dialog.
            $dialog = $(Handlebars.templates[templates.addedToCartSuccess](data));

            // Attach the close-button event and append the dialog to the form.
            $dialog.on("click", success_dialog_closebutton_selector, function (event) {
                this.hideSuccessDialog();
            }.bind(this)).appendTo(this.form);

            // Reposition the dialog if it doesn't fit in its current location.
            if ( $dialog.offset().left < 0 ) {
                $dialog.addClass(dialog_reverse_class);
                if ( ($dialog.offset().left + $dialog.outerWidth(false)) > $window.width() ) {
                    $dialog.removeClass(dialog_reverse_class);
                }
            }

            // Store the DOM element for future removal.
            this.successmsg = $dialog.get(0);

        }

    };

    Product.prototype.hideSuccessDialog = function () {

        if (this.successmsg) {
            $(this.successmsg).remove();
            delete this.successmsg;
        }

    };

    Product.prototype.showErrorDialog = function (errors) {

        var $dialog;

        // Convert single errors to a new one-item array.
        if (typeof errors === "string") {
            errors = [errors];
        }

        if ( Handlebars && Handlebars.templates && Handlebars.templates[templates.addedToCartError] ) {

            // Create the dialog.
            $dialog = $(Handlebars.templates[templates.addedToCartError]({
                errors: errors
            }));

            // Attach the close-button event and append the dialog to the form.
            $dialog.on("click", error_dialog_closebutton_selector, function (event) {
                this.hideErrorDialog();
            }.bind(this)).appendTo(this.form);

            // Reposition the dialog if it doesn't fit in its current location.
            if ( $dialog.offset().left < 0 ) {
                $dialog.addClass(dialog_reverse_class);
                if ( ($dialog.offset().left + $dialog.outerWidth(false)) > $window.width() ) {
                    $dialog.removeClass(dialog_reverse_class);
                }
            }

            // Store the DOM element for future removal.
            this.errormsg = $dialog.get(0);

        }

    };

    Product.prototype.hideErrorDialog = function () {

        if (this.errormsg) {
            $(this.errormsg).remove();
            delete this.errormsg;
        }

    };

    Product.prototype.showLoadingMessage = function () {
        $(this.form).addClass(loading_class);
    };

    Product.prototype.hideLoadingMessage = function () {
        $(this.form).removeClass(loading_class);
    };

    Product.prototype.showUpchargeDialog = function () {

        var $dialog = $(this.upcharge_dialog);

        // Show the dialog.
        $(this.form).addClass(upcharge_dialog_active_class);

        // Reposition the dialog if it doesn't fit in its current location.
        if ( $dialog.length > 0 && $dialog.offset().left < 0 ) {
            $dialog.addClass(dialog_reverse_class);
            if ( ($dialog.offset().left + $dialog.outerWidth(false)) > $window.width() ) {
                $dialog.removeClass(dialog_reverse_class);
            }
        }

    };

    Product.prototype.hideUpchargeDialog = function () {
        $(this.form).removeClass(upcharge_dialog_active_class);
        $(this.upcharge_dialog).removeClass(dialog_reverse_class);
    };

    Product.prototype.getQuantityVal = function () {
        return +$(this.qty_input).val();
    };

    Product.prototype.resetQuantityInput = function () {
        $(this.qty_input).val("").trigger("blur");
    };

    return Product;

})(),

ShippingEstimate = (function () {

    function ShippingEstimate (carrier, name, price, arrival_timestamp, hint) {
        this.carrier = "" + carrier;
        this.name = "" + name;
        this.price = +price;
        this.arrivalDate = new Date(arrival_timestamp);
        this.hint = typeof hint === "string" ? hint : "";
    }

    ShippingEstimate.prototype.getCarrier = function () {
        return this.carrier;
    };

    ShippingEstimate.prototype.getName = function () {
        return this.name;
    };

    ShippingEstimate.prototype.getPrice = function () {
        return this.price;
    };

    ShippingEstimate.prototype.getArrivalDate = function () {
        return this.arrivalDate;
    };

    ShippingEstimate.prototype.getHint = function () {
        return this.hint;
    };

    return ShippingEstimate;

})(),

ShippingEstimator = (function () {

    var

    statuses = {
        unloaded: "unloaded",
        loading: "loading",
        loaded: "loaded",
        loadedNoResults: "loadedNoResults",
        freightDisable: "freightDisable"
    },

    addressTypes = {
        commercial: "commercial",
        residential: "residential",
        pickup: "pickup"
    },

    selectors = {
        calcForm: "form",
        zipInput: "input.shipping-estimator-zip-input",
        commercialInput: "input.shipping-estimator-commercial-input",
        residentialInput: "input.shipping-estimator-residential-input",
        headline: "p.shipping-estimator-headline",
        subhead: "p.shipping-estimator-subhead",
        shipDateNotice: "span.shipest-shipdate",
        errorWrap: "ul.shipping-estimator-errors",
        commercialResultWrap: "div.shipping-estimator-viewer-commercial",
        residentialResultWrap: "div.shipping-estimator-viewer-residential",
        pickupResultWrap: "div.shipping-estimator-viewer-pickup",
        resultTable: "table",
        commercialTab: "a.shipping-estimator-tab-commercial",
        residentialTab: "a.shipping-estimator-tab-residential",
        pickupTab: "a.shipping-estimator-tab-pickup"
    },

    classes = {
        statusUnloaded: "",
        statusLoading: "loading",
        statusLoaded: "loaded",
        statusLoadedNoResults: "loaded-no-results",
        statusFreightDisable: "freight-disable",
        hint: "shipping-method-hint",
        tabActive: "activetab",
        errorMessage: "error"
    },

    dataAttrs = {
        freightRequired: "freightShipment",
        pickupAvailable: "pickupAvailable"
    },

    exprs = {
        zip: /^\s*\d{5}([-\s]\d{4})?\s*$/
    },

    text = {
        headlineDefault: "Shipping Estimator",
        headlineFreight: "Freight Shipping Required",
        subheadDefault: "Enter a US ZIP code to preview your shipping options. Need to ship outside the US? No problem; proceed to checkout to calculate shipping.",
        subheadFreight: "After placing your order, you will not be charged until Customer Service calls you to discuss shipping arrangements and pricing.",
        errorInvalidZip: "Please enter a valid US ZIP code.",
        errorUnknown: "An unknown error was encountered. Please contact our customer service department for assistance.",
        tableheadMethod: "Shipping Method",
        tableheadPrice: "Price",
        tableheadArrival: "Estimated Arrival"
    };

    function ShippingEstimator (e) {

        var $e = $(e), $calcForm = $e.find(selectors.calcForm).eq(0);

        this.dom = {
            root: $e.get(0),
            calcForm: $calcForm.get(0),
            zipInput: $e.find(selectors.zipInput).get(0),
            commercialInput: $e.find(selectors.commercialInput).get(0),
            residentialInput: $e.find(selectors.residentialInput).get(0),
            headline: $e.find(selectors.headline).get(0),
            subhead: $e.find(selectors.subhead).get(0),
            shipDateNotice: $e.find(selectors.shipDateNotice).get(0),
            errorWrap: $e.find(selectors.errorWrap).get(0),
            commercialResultWrap: $e.find(selectors.commercialResultWrap).get(0),
            residentialResultWrap: $e.find(selectors.residentialResultWrap).get(0),
            pickupResultWrap: $e.find(selectors.pickupResultWrap).get(0),
            commercialTab: $e.find(selectors.commercialTab).get(0),
            residentialTab: $e.find(selectors.residentialTab).get(0),
            pickupTab: $e.find(selectors.pickupTab).get(0)
        };
        this.submitUrl = $calcForm.attr("action");
        this.submitMethod = $calcForm.attr("method");
        this.estimates = {};
        this.errors = [];
        this.freightRequired = !!$e.data(dataAttrs.freightRequired);
        this.pickupAvailable = !!$e.data(dataAttrs.pickupAvailable);
        this.zip = this.getZipFromDom();
        this.addressType = this.getAddressTypeFromDom();
        this.tab = this.getActiveTabFromDom();
        this.status = this.getStatusFromDom();

    }

    ShippingEstimator.prototype.init = function () {

        var _this = this;

        // Prepare form submission.
        $(this.dom.calcForm).on("submit", function (event) {

            event.preventDefault();

            // Pull the necessary attributes from the DOM.
            _this.zip = _this.getZipFromDom();
            _this.addressType = _this.getAddressTypeFromDom();

            // Update the headline, empty the cache, and request new estimates.
            _this.emptyCache().updateHeadline().requestEstimates();

        });

        // Prepare tab controls.
        $(this.dom.commercialTab).on("click", function (event) {
            event.preventDefault();
            _this.switchToTab(addressTypes.commercial);
        });
        $(this.dom.residentialTab).on("click", function (event) {
            event.preventDefault();
            _this.switchToTab(addressTypes.residential);
        });
        $(this.dom.pickupTab).on("click", function (event) {
            event.preventDefault();
            _this.switchToTab(addressTypes.pickup);
        });

        // Request estimates if the form is already pre-filled on when initialized.
        if ( this.status !== statuses.loaded && this.getZipValidity() ) {
            this.emptyCache().requestEstimates();
        }

        return this;

    };

    ShippingEstimator.prototype.switchToTab = function (addressType) {

        var
        $commercialInput = $(this.dom.commercialInput),
        $residentialInput = $(this.dom.residentialInput),
        $commercialTab = $(this.dom.commercialTab).add(this.dom.commercialResultWrap),
        $residentialTab = $(this.dom.residentialTab).add(this.dom.residentialResultWrap),
        $pickupTab = $(this.dom.pickupTab).add(this.dom.pickupResultWrap);

        if ( addressType === addressTypes.commercial ) {

            // Switch to the tab.
            $residentialTab.add($pickupTab).removeClass(classes.tabActive);
            $commercialTab.addClass(classes.tabActive);
            this.tab = addressTypes.commercial;

            // Check the corresponding input.
            $commercialInput.prop("checked", true);

            // Update the stored address type.
            this.addressType = addressTypes.commercial;

            // Request estimates.
            this.requestEstimates();

        } else if ( addressType === addressTypes.residential ) {

            // Switch to the tab.
            $commercialTab.add($pickupTab).removeClass(classes.tabActive);
            $residentialTab.addClass(classes.tabActive);
            this.tab = addressTypes.residential;

            // Check the corresponding input.
            $residentialInput.prop("checked", true);

            // Update the stored address type.
            this.addressType = addressTypes.residential;

            // Request estimates.
            this.requestEstimates();

        } else if ( addressType === addressTypes.pickup ) {

            // Switch to the tab.
            $commercialTab.add($residentialTab).removeClass(classes.tabActive);
            $pickupTab.addClass(classes.tabActive);
            this.tab = addressTypes.pickup;

            // Uncheck the address type inputs.
            $commercialInput.add($residentialInput).prop("checked", false);

            // Update the stored address type.
            this.addressType = addressTypes.pickup;

        }

        return this;

    };

    ShippingEstimator.prototype.getZipFromDom = function () {

        return "" + $(this.dom.zipInput).val();

    };

    ShippingEstimator.prototype.getAddressTypeFromDom = function () {

        // Return residential if that's selected. Otherwise default to commercial.
        return $(this.dom.residentialInput).prop("checked") ? addressTypes.residential : $(this.dom.commercialInput).prop("checked") ? addressTypes.commercial : null;

    };

    ShippingEstimator.prototype.getActiveTabFromDom = function () {

        var active;

        if ( $(this.dom.commercialTab).hasClass(classes.tabActive) ) {
            active = addressTypes.commercial;
        } else if ( $(this.dom.residentialTab).hasClass(classes.tabActive) ) {
            active = addressTypes.residential;
        } else if ( $(this.dom.pickupTab).hasClass(classes.tabActive) ) {
            active = addressTypes.pickup;
        }

        return active;

    };

    ShippingEstimator.prototype.getStatusFromDom = function () {

        var $root = $(this.dom.root);

        return $root.hasClass(classes.statusLoading) ? statuses.loading : $root.hasClass(classes.statusLoaded) ? statuses.loaded : $root.hasClass(classes.statusLoadedNoResults) ? statuses.loadedNoResults : $root.hasClass(classes.statusFreightDisable) ? statuses.freightDisable : statuses.unloaded;

    };

    ShippingEstimator.prototype.getZipValidity = function () {

        return exprs.zip.test(this.zip);

    };

    ShippingEstimator.prototype.updateStatus = function (status) {

        var $root;

        if ( typeof status === "undefined" || status === null ) {

            status = typeof this.ajax !== "undefined" && this.ajax !== null ? statuses.loading : this.getCacheStatus() ? statuses.loaded : this.errors.length > 0 ? statuses.loadedNoResults : statuses.unloaded;

        }

        this.status = status;

        if ( this.status !== this.getStatusFromDom() ) {

            $root = $(this.dom.root);

            // Remove all status classes.
            $root.removeClass([classes.statusUnloaded, classes.statusLoading, classes.statusLoaded, classes.statusLoadedNoResults, classes.statusFreightDisable].join(" "));

            // Add the new class.
            if ( this.status === statuses.unloaded ) {
                $root.addClass(classes.statusUnloaded);
            } else if ( this.status === statuses.loading ) {
                $root.addClass(classes.statusLoading);
            } else if ( this.status === statuses.loaded ) {
                $root.addClass(classes.statusLoaded);
            } else if ( this.status === statuses.loadedNoResults ) {
                $root.addClass(classes.statusLoadedNoResults);
            } else if ( this.status === statuses.freightDisable ) {
                $root.addClass(classes.statusFreightDisable);
            }

        }

        return this;

    };

    ShippingEstimator.prototype.updateHeadline = function (reset) {

        var headline;

        if ( this.freightRequired ) {
            headline = text.headlineFreight;
        } else {
            headline = text.headlineDefault;
            if ( reset !== true && this.getZipValidity() ) {
                headline += " (" + trim(this.zip) + ")";
            }
        }

        $(this.dom.headline).text(headline);

        return this;

    };

    ShippingEstimator.prototype.updateSubhead = function () {

        $(this.dom.subhead).text(this.freightRequired ? text.subheadFreight : text.subheadDefault);

        return this;

    };

    ShippingEstimator.prototype.updateShipDateDisplay = function (date) {

        $(this.dom.shipDateNotice).text(date);

        return this;

    };

    ShippingEstimator.prototype.requestEstimates = function () {

        var _this = this;

        if ( !this.getCacheStatus(this.addressType) ) {

            if ( typeof this.ajax !== "undefined" && this.ajax !== null ) {
                this.ajax.abort();
            }

            if ( this.getZipValidity() ) {

                this.updateStatus(statuses.loading);

                this.ajax = $.ajax({
                    url: this.submitUrl,
                    type: this.submitMethod,
                    data: {
                        zip: this.zip,
                        addressType: this.addressType
                    },
                    success: function (data, textStatus, jqXHR) {
                        _this.parseEstimates(data).showEstimates(data ? data.addressType : undefined).showErrors();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if ( textStatus !== "abort" ) {
                            _this.updateStatus(statuses.loadedNoResults).showError();
                        }
                    },
                    complete: function (jqXHR, textStatus) {
                        delete _this.ajax;
                    }
                });

            } else {

                this.updateStatus(statuses.loadedNoResults).showError(text.errorInvalidZip);

            }

        }

    };

    ShippingEstimator.prototype.parseEstimates = function (data) {

        var i, len;

        if ( data ) {

            if ( data.addressType === addressTypes.commercial || data.addressType === addressTypes.residential ) {

                this.removeEstimatesFromDom(data.addressType);
                this.estimates[data.addressType] = [];

                // Parse and store the estimates.
                if ( data.rates instanceof Array && data.rates.length > 0 ) {
                    for ( i = 0, len = data.rates.length; i < len; i++ ) {
                        this.estimates[data.addressType].push(new ShippingEstimate(data.rates[i].carrier, data.rates[i].name, data.rates[i].price, data.rates[i].arrivalDate, data.rates[i].hint));
                    }
                }

            }

            // Parse and store the errrors.
            if ( data.errors instanceof Array && data.errors.length > 0 ) {
                for ( i = 0, len = data.errors.length; i < len; i++ ) {
                    if ( typeof data.errors[i] === "string" ) {
                        this.errors.push(data.errors[i]);
                    }
                }
            }

            // Determine whether or not customer pickup should be shown.
            this.pickupAvailable = data.pickupAvailable === true;

        } else {

            this.errors.push(text.errorUnknown);

        }

        return this;

    };

    ShippingEstimator.prototype.showEstimates = function (addressType) {

        var i, len, table, thead, tbody, tr, $td_name, $td_price, $td_arrival, $wrap, estimates = [];

        if ( addressType === addressTypes.commercial || addressType === addressTypes.residential ) {

            for ( i = 0, len = this.estimates[addressType].length; i < len; i++ ) {

                if ( this.estimates[addressType][i] instanceof ShippingEstimate ) {
                    estimates.push({
                        name: this.estimates[addressType][i].getCarrier() + " " + this.estimates[addressType][i].getName(),
                        price: formatPrice(this.estimates[addressType][i].getPrice()),
                        arrival_date: moment ? moment.utc(this.estimates[addressType][i].getArrivalDate()).format("MMMM Do, YYYY") : "",
                        hint: this.estimates[addressType][i].getHint()
                    });
                }

            }

        }

        if (estimates.length > 0) {

            table = document.createElement("table");
            thead = document.createElement("thead");
            tr = document.createElement("tr");

            $(table).append(thead);
            $(thead).append(tr);
            $(tr).append($(document.createElement("th")).text(text.tableheadMethod)).append($(document.createElement("th")).text(text.tableheadPrice)).append($(document.createElement("th")).text(text.tableheadArrival));

            tbody = document.createElement("tbody");

            for ( i = 0, len = estimates.length; i < len; i++ ) {

                // Create the name cell.
                $td_name = $(document.createElement("td")).text(estimates[i].name);

                // Add the hint to the name cell, if applicable.
                if ( estimates[i].hint.length > 0 ) {
                    $td_name.append($(document.createElement("div")).addClass(classes.hint).text(estimates[i].hint));
                }

                // Create the price cell.
                $td_price = $(document.createElement("td")).text(estimates[i].price);

                // Create the arrival cell.
                $td_arrival = $(document.createElement("td")).text(estimates[i].arrival_date);

                // Construct the row and attach it to the table.
                $(tbody).append($(document.createElement("tr")).append($td_name).append($td_price).append($td_arrival));

            }

            $(table).append(tbody);

            if ( addressType === addressTypes.commercial ) {
                $(this.dom.commercialResultWrap).append(table);
            } else if ( addressType === addressTypes.residential ) {
                $(this.dom.residentialResultWrap).append(table);
            }

            this.updateStatus(statuses.loaded);

        } else {

            this.updateStatus(statuses.loadedNoResults);

        }

        this.switchToTab(addressType);

        if ( this.pickupAvailable ) {
            this.showPickupTab();
        } else {
            this.hidePickupTab();
        }

        return this;

    };

    ShippingEstimator.prototype.showError = function (msg) {

        $(this.dom.errorWrap).append($(document.createElement("li")).addClass(classes.errorMessage).text(typeof msg === "string" ? msg : text.errorUnknown));

        return this;

    };

    ShippingEstimator.prototype.showErrors = function () {

        var i, len;

        for ( i = 0, len = this.errors.length; i < len; i++ ) {
            this.showError(this.errors[i]);
        }

        return this;

    };

    ShippingEstimator.prototype.refresh = function () {

        if ( this.getCacheStatus() && !this.freightRequired ) {
            this.emptyCache().requestEstimates();
        }

        return this;

    };

    ShippingEstimator.prototype.showFreightShipmentNotice = function () {

        if ( !this.freightRequired ) {

            this.freightRequired = true;
            this.updateStatus(statuses.freightDisable).updateHeadline().updateSubhead();

        }

        return this;

    };

    ShippingEstimator.prototype.hideFreightShipmentNotice = function () {

        if ( this.freightRequired ) {

            this.freightRequired = false;
            this.updateStatus().updateHeadline().updateSubhead();

        }

        return this;

    };

    ShippingEstimator.prototype.removeEstimatesFromDom = function (addressType) {

        var $wraps = $();

        if ( addressType === addressTypes.commercial ) {
            $wraps = $wraps.add(this.dom.commercialResultWrap);
        } else if ( addressType === addressTypes.residential ) {
            $wraps = $wraps.add(this.dom.residentialResultWrap);
        } else if ( typeof addressType === "undefined" || addressType === null ) {
            $wraps = $wraps.add(this.dom.commercialResultWrap).add(this.dom.residentialResultWrap);
        }

        $wraps.find(selectors.resultTable).remove();

        return this;

    };

    ShippingEstimator.prototype.emptyCache = function () {

        // Remove the estimates from the cache and the DOM.
        this.removeEstimatesFromDom();
        this.estimates = {};

        // Remove the error cache and the DOM.
        $(this.dom.errorWrap).empty();
        this.errors = [];

        return this;

    };

    ShippingEstimator.prototype.getCacheStatus = function (addressType) {

        var cached = false;

        if ( typeof addressType !== "undefined" && addressType !== null && addressType !== addressTypes.pickup ) {
            cached = !!this.estimates.hasOwnProperty(addressType);
        } else {
            for ( addressType in this.estimates ) {
                if ( this.estimates.hasOwnProperty(addressType) ) {
                    cached = true;
                    break;
                }
            }
        }

        return cached;

    };

    ShippingEstimator.prototype.showPickupTab = function () {

        $(this.dom.pickupTab).add(this.dom.pickupResultWrap).show();
        return this;

    };

    ShippingEstimator.prototype.hidePickupTab = function () {

        $(this.dom.pickupTab).add(this.dom.pickupResultWrap).hide();
        return this;

    };

    return ShippingEstimator;

})(),

Tab = (function () {

    var

    tab_controL_active_class = "active",
    tab_content_hidden_class = "hidden";

    function Tab (e, index) {
        this.control = e;
        this.content = this.getContent();
        this.index = index;
    }

    Tab.prototype.prepare = function (TabArea) {

        var index = this.index;

        $(this.control).on("click", function (event) {
            event.preventDefault();
            TabArea.switchToTab(index);
        });

    };

    Tab.prototype.getContent = function () {

        var href = $(this.control).attr("href").replace(/.*(?=#[^\s]+$)/, ""),
            content;

        if ( typeof href === "string" && href.charAt(0) === "#" ) {
            content = $(href).get(0);
        }

        return content;

    };

    Tab.prototype.activate = function () {
        this.activateControl();
        this.showContent();
    };

    Tab.prototype.deactivate = function () {
        this.deactivateControl();
        this.hideContent();
    };

    Tab.prototype.activateControl = function () {
        $(this.control).addClass(tab_controL_active_class);
    };

    Tab.prototype.deactivateControl = function () {
        $(this.control).removeClass(tab_controL_active_class);
    };

    Tab.prototype.showContent = function () {
        $(this.content).removeClass(tab_content_hidden_class);
    };

    Tab.prototype.hideContent = function () {
        $(this.content).addClass(tab_content_hidden_class);
    };

    return Tab;

})(),

TabArea = (function () {

    var

    tab_control_subselector = ".tab-control";

    function TabArea (e) {
        this.e = e;
        this.tabs = this.getTabs();
    }

    TabArea.prototype.getTabs = function () {

        var tabs = [];

        $(this.e).find(tab_control_subselector).each(function (i, e) {

            tabs.push(new Tab(e, i));

        });

        return tabs;

    };

    TabArea.prototype.prepareInterface = function () {

        var i, len;

        for (i = 0, len = this.tabs.length; i < len; i++) {

            this.tabs[i].prepare(this);

        }

        if (this.tabs.length > 0) { this.switchToTab(0); }

    };

    TabArea.prototype.switchToTab = function (index) {

        var i, len;

        for (i = 0, len = this.tabs.length; i < len; i++) {
            if (i === index) {
                this.tabs[i].activate();
            } else {
                this.tabs[i].deactivate();
            }
        }

    };

    return TabArea;

})(),

LanguageSelector = (function () {

    var

    dialog_close_subselector = "a.cancel-button";

    function LanguageSelector (dialog, trigger) {

        this.dialog = dialog;
        this.trigger = trigger;

    }

    LanguageSelector.prototype.init = function () {

        var _this = this;

        $(this.trigger).on("click", function (event) {
            event.preventDefault();
            _this.showDialog();
        });

        $(this.dialog).find(dialog_close_subselector).on("click", function (event) {
            event.preventDefault();
            _this.hideDialog();
        });

    };

    LanguageSelector.prototype.showDialog = function () {

        if ($.fancybox) {
            $.fancybox($(this.dialog));
        }

    };

    LanguageSelector.prototype.hideDialog = function () {

        if ($.fancybox) {
            $.fancybox.close();
        }

    };

    return LanguageSelector;

})(),

ReturnForm = (function () {

    var

    reason_select_subselector = "#return-reason-selector",
    reason_textarea_subselector = "#other-reason-for-return",
    reason_other_value = "other";

    function ReturnForm (e) {

        var $e = $(e);

        this.e = e;
        this.reasonselect = $e.find(reason_select_subselector).get(0);
        this.reasontextarea = $e.find(reason_textarea_subselector).get(0);

    }

    ReturnForm.prototype.init = function () {

        var _this = this;

        $(this.reasonselect).on("change", function (event) {
            _this.updateReasonTextareaVisibility();
        });

        this.updateReasonTextareaVisibility();

    };

    ReturnForm.prototype.updateReasonTextareaVisibility = function () {

        var $reasontextarea = $(this.reasontextarea);

        if ($(this.reasonselect).val() === reason_other_value) {
            $reasontextarea.show();
        } else {
            $reasontextarea.hide();
        }

    };

    return ReturnForm;

})(),

AccountForm = (function () {

    var
    register_form_subselector = "#new-user-guest-checkout",
    signin_form_subselector = "#returning-user-signins",
    guest_signin_form_subselector = "#guest-checkout",
    error_wrap_subselector = "div.notice-icon",
    error_wrap_valid_class = "hidden",

    getErrorMessage = function (error, action, text) {

        var msg;

        switch (error) {
            case missing_email: msg = "You must enter an email address to " + action + "."; break;
            case invalid_email: msg = "\u201C" + text + "\u201D is not a valid email address. You must enter a valid email address to " + action + "."; break;
            case missing_pass: msg = "You must enter a password to " + action + "."; break;
            case invalid_pass: msg = "Your password must be at least eight characters long."; break;
            case pass_mismatch: msg = "The passwords you entered do not match."; break;
            default: error = "There was an error when trying to " + action + ". Please try again or contact Customer Service for assistance.";
        }

        return msg;

    };

    function AccountForm (e) {

        var
        $e = $(e),
        register = $e.find(register_form_subselector).get(0),
        signin = $e.find(signin_form_subselector).get(0),
        guest_signin = $e.find(guest_signin_form_subselector).get(0);

        this.e = e;
        this.register = register ? new RegisterForm(register, this) : null;
        this.signin = signin ? new SignInForm(signin, this) : null;
        this.guest_signin = guest_signin ? new GuestSignInForm(guest_signin, this) : null;

        this.error_wrap = $e.find(error_wrap_subselector).get(0);

    }

    AccountForm.prototype.init = function () {

        this.disableBrowserValidation();

        if (this.register instanceof RegisterForm) {
            this.register.init();
        }

        if (this.signin instanceof SignInForm) {
            this.signin.init();
        }

        if (this.guest_signin instanceof GuestSignInForm) {
            this.guest_signin.init();
        }

    };

    AccountForm.prototype.disableBrowserValidation = function () {
        $(this.e).find("form").attr("novalidate", "novalidate");
    };

    AccountForm.prototype.displayErrors = function (action, errors, text) {

        var i, len, headline_msg, $errorwrap = $(this.error_wrap), $headline, $list;

        errors = (errors instanceof Array) ? errors : [];

        // Empty previous errors.
        $errorwrap.empty();

        if (errors.length > 0) {

            // Create the headline.
            $headline = $(document.createElement("p"));

            // Normalize the action.
            action = action ? action : "complete your request";

            // If there was a single error...
            if (errors.length === 1) {

                // Write the headline.
                headline_msg = getErrorMessage(errors[0], action, text);

            // If there were multiple errors...
            } else {

                // Create the list.
                $list = $(document.createElement("ul"));

                // Write the headline.
                headline_msg = "There were errors trying to " + action + ".";

                // Add each error to the list.
                for (i = 0, len = errors.length; i < len; i++) {
                    $list.append($(document.createElement("li")).text(getErrorMessage(errors[i], action, text)));
                }

            }

            // Remove the valid class and append the content.
            $errorwrap.removeClass(error_wrap_valid_class).append($headline.text(headline_msg)).append($list);

        } else {

            // Add the valid class.
            $errorwrap.addClass(error_wrap_valid_class);

        }

    };

    return AccountForm;

})(),

RegisterForm = (function () {

    var

    passwords_subselector = "input[type=\"password\"]",
    email_subselector = "input[type=\"email\"]",
    action = "create an account";

    function RegisterForm (e, accountform) {

        var $e = $(e), $passes = $e.find(passwords_subselector);

        this.$form = $e;
        this.email = new AccountField($e.find(email_subselector).get(0));
        this.pass1 = new AccountField($passes.get(0));
        this.pass2 = new AccountField($passes.get(1));

        this.accountform =  (accountform instanceof AccountForm) ? accountform : null;

    }

    RegisterForm.prototype.init = function () {

        var _this = this;

        this.$form.on("submit", function (event) {
            return _this.validate();
        });

        this.email.init();
        this.pass1.init();
        this.pass2.init();

    };

    RegisterForm.prototype.validate = function () {

        var
        i,
        len,
        fieldmap,
        errors = [],
        email = this.email.getValue(),
        pass1 = this.pass1.getValue(),
        pass2 = this.pass2.getValue(),
        email_error = false,
        pass_error = false;

        // Check the email address.
        if (email.length === 0) {
            errors.push(missing_email);
            email_error = true;
        } else if (!email_regex.test(email)) {
            errors.push(invalid_email);
            email_error = true;
        }

        // Check the passwords.
        if (pass1.length === 0) {
            errors.push(missing_pass);
            pass_error = true;
        } else if (!password_regex.test(pass1)) {
            errors.push(invalid_pass);
            pass_error = true;
        } else if (pass1 !== pass2) {
            errors.push(pass_mismatch);
            pass_error = true;
        }

        // Highlight or unhighlight the controls as necessary.
        fieldmap = [ [this.email, email_error], [this.pass1, pass_error], [this.pass2, pass_error] ];
        for (i = 0, len = fieldmap.length; i < len; i++) {
            if (fieldmap[i][1]) {
                fieldmap[i][0].highlight();
            } else {
                fieldmap[i][0].unhighlight();
            }
        }

        if (this.accountform instanceof AccountForm) {
            this.accountform.displayErrors(action, errors, email);
        }

        return errors.length === 0;

    };

    return RegisterForm;

})(),

SignInForm = (function () {

    var

    password_subselector = "input[type=\"password\"]",
    email_subselector = "input[type=\"email\"]",
    action = "sign in";

    function SignInForm (e, accountform) {

        var $e = $(e), $passes = $e.find(password_subselector);

        this.$form = $e;
        this.email = new AccountField($e.find(email_subselector).get(0));
        this.pass = new AccountField($passes.get(0));

        this.accountform =  (accountform instanceof AccountForm) ? accountform : null;

    }

    SignInForm.prototype.init = function () {

        var _this = this;

        this.$form.on("submit", function (event) {
            return _this.validate();
        });

        this.email.init();
        this.pass.init();

    };

    SignInForm.prototype.validate = function () {

        var
        i,
        len,
        fieldmap,
        errors = [],
        email = this.email.getValue(),
        pass = this.pass.getValue(),
        email_error = false,
        pass_error = false;

        // Check the email address.
        if (email.length === 0) {
            errors.push(missing_email);
            email_error = true;
        } else if (!email_regex.test(email)) {
            errors.push(invalid_email);
            email_error = true;
        }

        // Check the password.
        if (pass.length === 0) {
            errors.push(missing_pass);
            pass_error = true;
        }

        // Highlight or unhighlight the controls as necessary.
        fieldmap = [ [this.email, email_error], [this.pass, pass_error] ];
        for (i = 0, len = fieldmap.length; i < len; i++) {
            if (fieldmap[i][1]) {
                fieldmap[i][0].highlight();
            } else {
                fieldmap[i][0].unhighlight();
            }
        }

        if (this.accountform instanceof AccountForm) {
            this.accountform.displayErrors(action, errors, email);
        }

        return errors.length === 0;

    };

    return SignInForm;

})(),

GuestSignInForm = (function () {

    var

    password_subselector = "input[type=\"password\"]",
    email_subselector = "input[type=\"email\"]",
    action = "check out";

    function GuestSignInForm (e, accountform) {

        var $e = $(e), $passes = $e.find(password_subselector);

        this.$form = $e;
        this.email = new AccountField($e.find(email_subselector).get(0));

        this.accountform =  (accountform instanceof AccountForm) ? accountform : null;

    }

    GuestSignInForm.prototype.init = function () {

        var _this = this;

        this.$form.on("submit", function (event) {
            return _this.validate();
        });

        this.email.init();

    };

    GuestSignInForm.prototype.validate = function () {

        var
        errors = [],
        email = this.email.getValue(),
        email_error = false;

        // Check the email address.
        if (email.length === 0) {
            errors.push(missing_email);
            email_error = true;
        } else if (!email_regex.test(email)) {
            errors.push(invalid_email);
            email_error = true;
        }

        // Highlight or unhighlight the controls as necessary.
        if (email_error) {
            this.email.highlight();
        } else {
            this.email.unhighlight();
        }

        if (this.accountform instanceof AccountForm) {
            this.accountform.displayErrors(action, errors, email);
        }

        return errors.length === 0;

    };

    return GuestSignInForm;

})(),

AccountField = (function () {

    var

    controlwrap_parent_selector = "div.control-wrap",
    controlwrap_highlight_class = "error";

    function AccountField (e) {

        var $e = $(e);

        this.$e = $e;
        this.$wrap = $e.closest(controlwrap_parent_selector);

    }

    AccountField.prototype.init = function () {

        var _this = this;

        this.$e.on("keyup", function () {
            _this.unhighlight();
        });

    };

    AccountField.prototype.highlight = function () {
        this.$wrap.addClass(controlwrap_highlight_class);
    };

    AccountField.prototype.unhighlight = function () {
        this.$wrap.removeClass(controlwrap_highlight_class);
    };

    AccountField.prototype.getValue = function () {
        return "" + this.$e.val();
    };

    return AccountField;

})(),

GuestRegistrationForm = (function () {

    var
    openbutton_subselector = "a.guest-registration-password",
    closebutton_subselector = "a.guest-registration-cancel";

    function GuestRegistrationForm (e) {

        var
        $e = $(e),
        $openbutton = $e.find(openbutton_subselector),
        formlink = $openbutton.attr("href").replace(/.*(?=#[^\s]+$)/, ""),
        formwrap;

        // Find and store the wrapper.
        this.e = $e.get(0);

        // Find and store the open button.
        this.openbutton = $openbutton.get(0);

        // Find and store the close button.
        this.closebutton = $e.find(closebutton_subselector).get(0);

        // Find and store the form.
        if ( typeof formlink === "string" && formlink.charAt(0) === "#" ) {
            formwrap = $(formlink).get(0);
        }
        this.formwrap = formwrap;

    }

    GuestRegistrationForm.prototype.init = function () {

        var _this = this;

        $(this.openbutton).on("click", function (event) {
            event.preventDefault();
            _this.openForm();
        });

        $(this.closebutton).on("click", function (event) {
            event.preventDefault();
            _this.closeForm();
        });

    };

    GuestRegistrationForm.prototype.openForm = function () {

        var $formwrap = $(this.formwrap);

        if ( $.fancybox && $formwrap.length > 0 ) {
            $.fancybox($formwrap);
        }

    };

    GuestRegistrationForm.prototype.closeForm = function () {

        if ( $.fancybox ) {
            $.fancybox.close();
        }

    };

    return GuestRegistrationForm;

})(),

DesignSaver = (function () {

    var
    parent_wrap_selector = "#custom-sign-edit-preview",
    loading_class = "loading",
    signedin_msg_selector = "div.saveforlater-signedin",
    signedin_msg_closebutton_subselector = "div.signedin-message-close-button",
    signedin_msg_savebutton_subselector = "a.signedin-message-save-button",
    submit_type = "GET",
    success_class = "saveforlater-success",
    success_headline_text = "Save For Later: Success",
    success_headline_class = "h4 h4-rev pad-left-10",
    success_msg_text = "Your design has been saved to your account.",
    success_msg_class = "pad-left-10 prepend-top pad-right-10",
    success_buttonwrap_class = "pad-right-10 append-bottom right-side",
    success_cancelbutton_text = "Done",
    success_cancelbutton_class = "button",
    success_accountbutton_text = "Go To My Account",
    success_accountbutton_class = "button blue",
    signin_class = "saveforlater-error",
    signin_headline_text = "Save For Later",
    signin_headline_class = "h4 h4-rev pad-left-10",
    signin_msg_text = "You must be signed in to save custom designs.",
    signin_msg_class = "pad-left-10 prepend-top pad-right-10",
    signin_buttonwrap_class = "pad-right-10 append-bottom right-side",
    signin_cancelbutton_text = "Cancel",
    signin_cancelbutton_class = "button",
    signin_button_text = "Sign In",
    signin_button_class = "button blue",
    error_class = "saveforlater-error",
    error_headline_text = "Could Not Save for Later",
    error_headline_class = "h4 h4-rev pad-left-10",
    error_msg_class = "pad-left-10 prepend-top pad-right-10",
    error_buttonwrap_class = "pad-left-10 append-bottom",
    error_button_text = "Close",
    error_button_class = "button",
    communication_error_text = "We were unable to save your design. Please contact customer service for assistance.";

    function DesignSaver (anchor) {

        var $anchor = $(anchor), $wrap = $anchor.closest(parent_wrap_selector), $signedin_dialog = $wrap.children(signedin_msg_selector);

        this.anchor = anchor;
        this.save_url = $anchor.attr("href");
        this.wrap = $wrap.get(0);

        if ($signedin_dialog.length > 0) {
            this.signedin_dialog = $signedin_dialog.get(0);
        }

    }

    DesignSaver.prototype.init = function () {

        var
        _this = this,
        $savebuttons = this.signedin_dialog ? $(this.anchor).add($(this.signedin_dialog).find(signedin_msg_savebutton_subselector)) : $(this.anchor);

        // Initialize the save button(s).
        $savebuttons.on("click", function (event) {
            event.preventDefault();
            _this.save();
        });

        // Initialize the "signed in" message close button (if present).
        if (this.signedin_dialog) {
            $(this.signedin_dialog).on("click", signedin_msg_closebutton_subselector, function (event) {
                _this.hideSignedInDialog();
            });
        }


    };

    DesignSaver.prototype.save = function () {

        var _this = this;

        this.hideDialogs();
        this.showLoadingMessage();

        $.ajax({
            url: this.save_url,
            type: submit_type,
            success: function (data, textStatus, jqXHR) {
                if (data.success === true) {
                    _this.showSuccessMessage(data.accounturl);
                } else if (data.signedin === false) {
                    _this.showSignInDialog(data.signinurl);
                } else {
                    _this.showErrorDialog( data.errors ? data.errors : communication_error_text );
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                _this.showErrorDialog(communication_error_text);
            },
            complete: function (jqXHR, textStatus) {
                _this.hideLoadingMessage();
            }
        });

    };

    DesignSaver.prototype.showSuccessMessage = function (accounturl) {

        var _this = this, $success_msg, $headline, $message, $cancel_btn, $account_btn;

        // Hide existing dialogs.
        this.hideDialogs();

        // Create the message.
        $success_msg = $(document.createElement("div")).addClass(success_class);
        $headline = $(document.createElement("p")).addClass(success_headline_class).text(success_headline_text);
        $message = $(document.createElement("p")).addClass(success_msg_class).text(success_msg_text);
        $cancel_btn = $(document.createElement("div")).addClass(success_cancelbutton_class).text(success_cancelbutton_text);
        $account_btn = $(document.createElement("a")).addClass(success_accountbutton_class).attr("href", accounturl).text(success_accountbutton_text);
        $success_msg.append($headline).append($message).append($(document.createElement("div")).addClass(success_buttonwrap_class).append($cancel_btn).append($account_btn));

        // Save the message to the DesignSaver object.
        this.success_message = $success_msg.get(0);

        // Initialize the cancel button.
        $cancel_btn.on("click", function (event) {
            _this.hideSuccessMessage();
        });

        // Display the message.
        $(this.wrap).append($success_msg);

    };

    DesignSaver.prototype.hideSuccessMessage = function () {

        if (this.success_message) {

            // Hide the message.
            $(this.success_message).remove();

            // Delete the message from the DesignSaver object.
            delete this.success_message;

        }

    };

    DesignSaver.prototype.showSignInDialog = function (signinurl) {

        var _this = this, $signin_dialog, $headline, $message, $cancel_btn, $signin_btn;

        // Hide existing dialogs.
        this.hideDialogs();

        // Create the message.
        $signin_dialog = $(document.createElement("div")).addClass(signin_class);
        $headline = $(document.createElement("p")).addClass(signin_headline_class).text(signin_headline_text);
        $message = $(document.createElement("p")).addClass(signin_msg_class).text(signin_msg_text);
        $cancel_btn = $(document.createElement("div")).addClass(signin_cancelbutton_class).text(signin_cancelbutton_text);
        $signin_btn = $(document.createElement("a")).addClass(signin_button_class).attr("href", signinurl).text(signin_button_text);
        $signin_dialog.append($headline).append($message).append($(document.createElement("div")).addClass(signin_buttonwrap_class).append($cancel_btn).append($signin_btn));

        // Save the message to the DesignSaver object.
        this.signin_dialog = $signin_dialog.get(0);

        // Initialize the cancel button.
        $cancel_btn.on("click", function (event) {
            _this.hideSignInDialog();
        });

        // Display the message.
        $(this.wrap).append($signin_dialog);

    };

    DesignSaver.prototype.hideSignInDialog = function () {

        if (this.signin_dialog) {

            // Hide the message.
            $(this.signin_dialog).remove();

            // Delete the message from the DesignSaver object.
            delete this.signin_dialog;

        }

    };

    DesignSaver.prototype.showErrorDialog = function (errors) {

        var _this = this, i, len, $error_dialog, $headline, $message, $button;

        // Convert single errors to a new one-item array.
        if (typeof errors === "string") {
            errors = [errors];
        }

        // Hide existing dialogs.
        this.hideDialogs();

        // Create the message.
        $error_dialog = $(document.createElement("div")).addClass(error_class);
        $headline = $(document.createElement("p")).addClass(error_headline_class).text(error_headline_text);
        $message = $(document.createElement("ul")).addClass(error_msg_class);
        for (i = 0, len = errors.length; i < len; i++) { $message.append($(document.createElement("li")).text(errors[i])); }
        $button = $(document.createElement("div")).addClass(error_button_class).text(error_button_text);
        $error_dialog.append($headline).append($message).append($(document.createElement("div")).addClass(error_buttonwrap_class).append($button));

        // Save the message to the DesignSaver object.
        this.error_dialog = $error_dialog.get(0);

        // Initialize the cancel button.
        $button.on("click", function (event) {
            _this.hideErrorDialog();
        });

        // Display the message.
        $(this.wrap).append($error_dialog);

    };

    DesignSaver.prototype.hideErrorDialog = function () {

        if (this.error_dialog) {

            // Hide the message.
            $(this.error_dialog).remove();

            // Delete the message from the DesignSaver object.
            delete this.error_dialog;

        }

    };

    DesignSaver.prototype.hideSignedInDialog = function () {

        if (this.signedin_dialog) {

            // Hide the message.
            $(this.signedin_dialog).remove();

            // Delete the message from the DesignSaver object.
            delete this.signedin_dialog;

        }

    };

    DesignSaver.prototype.showLoadingMessage = function () {

        $(this.wrap).addClass(loading_class);

    };

    DesignSaver.prototype.hideLoadingMessage = function () {

        $(this.wrap).removeClass(loading_class);

    };

    DesignSaver.prototype.hideDialogs = function () {

        this.hideSuccessMessage();
        this.hideSignInDialog();
        this.hideErrorDialog();
        this.hideSignedInDialog();

    };

    return DesignSaver;

})(),

EmailChanger = (function () {

    var
    form_enabled_class = "emailform-active",
    button_disabled_class = "hidden",
    form_cancel_subselector = "button.cancel";

    function EmailChanger (e) {

        var
        $e = $(e),
        href = $e.attr("href").replace(/.*(?=#[^\s]+$)/, ""),
        formwrap;

        // Find and store the on button.
        this.onbutton = $e.get(0);

        // Find and store the form.
        if ( typeof href === "string" && href.charAt(0) === "#" ) {
            formwrap = $(href).get(0);
        }
        this.formwrap = formwrap;

        // Find and store the off button.
        this.offbutton = $(this.formwrap).find(form_cancel_subselector).get(0);

    }

    EmailChanger.prototype.init = function () {

        var _this = this;

        $(this.onbutton).on("click", function (event) {
            event.preventDefault();
            _this.showForm();
        });

        $(this.offbutton).on("click", function (event) {
            event.preventDefault();
            _this.hideForm();
        });

    };

    EmailChanger.prototype.showForm = function () {
        $(this.formwrap).addClass(form_enabled_class);
        $(this.onbutton).addClass(button_disabled_class);
    };

    EmailChanger.prototype.hideForm = function () {
        $(this.formwrap).removeClass(form_enabled_class);
        $(this.onbutton).removeClass(button_disabled_class);
    };

    return EmailChanger;

})(),

PasswordChanger = (function () {

    var
    form_enabled_class = "passwordform-active",
    button_disabled_class = "hidden",
    form_cancel_subselector = "button.cancel";

    function PasswordChanger (e) {

        var
        $e = $(e),
        href = $e.attr("href").replace(/.*(?=#[^\s]+$)/, ""),
        formwrap;

        // Find and store the on button.
        this.onbutton = $e.get(0);

        // Find and store the form.
        if ( typeof href === "string" && href.charAt(0) === "#" ) {
            formwrap = $(href).get(0);
        }
        this.formwrap = formwrap;

        // Find and store the off button.
        this.offbutton = $(this.formwrap).find(form_cancel_subselector).get(0);

    }

    PasswordChanger.prototype.init = function () {

        var _this = this;

        $(this.onbutton).on("click", function (event) {
            event.preventDefault();
            _this.showForm();
        });

        $(this.offbutton).on("click", function (event) {
            event.preventDefault();
            _this.hideForm();
        });

    };

    PasswordChanger.prototype.showForm = function () {
        $(this.formwrap).addClass(form_enabled_class);
        $(this.onbutton).addClass(button_disabled_class);
    };

    PasswordChanger.prototype.hideForm = function () {
        $(this.formwrap).removeClass(form_enabled_class);
        $(this.onbutton).removeClass(button_disabled_class);
    };

    return PasswordChanger;

})(),

Net30Linker = (function () {

    var
    form_enabled_class = "netthirtyform-active",
    button_disabled_class = "hidden",
    form_cancel_subselector = "button.cancel";

    function Net30Linker (e) {

        var
        $e = $(e),
        href = $e.attr("href").replace(/.*(?=#[^\s]+$)/, ""),
        formwrap;

        // Find and store the on button.
        this.onbutton = $e.get(0);

        // Find and store the form.
        if ( typeof href === "string" && href.charAt(0) === "#" ) {
            formwrap = $(href).get(0);
        }
        this.formwrap = formwrap;

        // Find and store the off button.
        this.offbutton = $(this.formwrap).find(form_cancel_subselector).get(0);

    }

    Net30Linker.prototype.init = function () {

        var _this = this;

        $(this.onbutton).on("click", function (event) {
            event.preventDefault();
            _this.showForm();
        });

        $(this.offbutton).on("click", function (event) {
            event.preventDefault();
            _this.hideForm();
        });

    };

    Net30Linker.prototype.showForm = function () {
        $(this.formwrap).addClass(form_enabled_class);
        $(this.onbutton).addClass(button_disabled_class);
    };

    Net30Linker.prototype.hideForm = function () {
        $(this.formwrap).removeClass(form_enabled_class);
        $(this.onbutton).removeClass(button_disabled_class);
    };

    return Net30Linker;

})(),

OrderViewer = (function () {

    var
    order_subselector = "tbody.order-row";

    function OrderViewer (e) {

        var $e = $(e);

        this.wrap = $e.get(0);
        this.orders = this.getOrders();

    }

    OrderViewer.prototype.init = function () {

        var i, len;

        for (i = 0, len = this.orders.length; i < len; i++) {
            if ( this.orders[i] instanceof Order ) {
                this.orders[i].init();
            }
        }

    };

    OrderViewer.prototype.getOrders = function () {

        var i, len, orders = [], $orders = $(this.wrap).find(order_subselector);

        for (i = 0, len = $orders.length; i < len; i++) {
            orders.push(new Order($orders.get(i)));
        }

        return orders;

    };

    return OrderViewer;

})(),

Order = (function () {

    var
    toggle_subselector = "a.reorder_button",
    product_subselector = "tr.items-list",
    visible_class = "visible",
    toggle_text_show = "View/Reorder",
    toggle_text_hide = "Hide Details";

    function Order (e) {

        var $e = $(e);

        this.e = $e.get(0);
        this.toggle = $e.find(toggle_subselector).get(0);
        this.visible = !!$e.hasClass(visible_class);
        this.products = this.getProducts();

    }

    Order.prototype.init = function () {

        var i, len, _this = this;

        // Initialize the toggle button.
        $(this.toggle).on("click", function (event) {
            event.preventDefault();
            _this.toggleVisibility();
        });

        // Initialize the products.
        for (i = 0, len = this.products.length; i < len; i++) {
            if ( this.products[i] instanceof OrderProduct ) {
                this.products[i].init();
            }
        }

    };

    Order.prototype.getProducts = function () {

        var i, len, products = [], $products = $(this.e).find(product_subselector);

        for (i = 0, len = $products.length; i < len; i++) {
            products.push(new OrderProduct($products.get(i)));
        }

        return products;

    };

    Order.prototype.toggleVisibility = function () {
        if ( this.visible ) {
            this.hide();
        } else {
            this.show();
        }
    };

    Order.prototype.show = function () {

        if ( !this.visible ) {
            $(this.e).addClass(visible_class);
            $(this.toggle).text(toggle_text_hide);
            this.visible = true;
        }

    };

    Order.prototype.hide = function () {

        if ( this.visible ) {
            $(this.e).removeClass(visible_class);
            $(this.toggle).text(toggle_text_show);
            this.visible = false;
        }

    };

    return Order;

})(),

OrderProduct = (function () {

    var
    details_subselector = "div.custom-attributes",
    detailtrigger_class = "show-hide-product-attributes",
    detailtrigger_text_on = "-",
    detailtrigger_text_off = "+",
    details_collapsed_class = "collapsed";

    function OrderProduct (e) {

        var $e = $(e);

        this.details = $e.find(details_subselector).get(0);
        this.details_active = this.checkDetailStatus();

    }

    OrderProduct.prototype.init = function () {

        var _this = this;

        // Prepare the detail trigger.
        this.detailtrigger = document.createElement("div");
        $(this.detailtrigger).addClass(detailtrigger_class).text(this.details_active ? detailtrigger_text_on : detailtrigger_text_off).on("click", function (event) {
            _this.toggleDetails();
        });

        // Insert the detail trigger into the DOM.
        $(this.details).prepend(this.detailtrigger);

    };

    OrderProduct.prototype.toggleDetails = function () {

        if ( this.details_active ) {
            this.hideDetails();
        } else {
            this.showDetails();
        }

    };

    OrderProduct.prototype.showDetails = function () {

        if ( !this.details_active ) {

            $(this.details).removeClass(details_collapsed_class);
            $(this.detailtrigger).text(detailtrigger_text_on);
            this.details_active = true;

        }

    };

    OrderProduct.prototype.hideDetails = function () {

        if ( this.details_active ) {

            $(this.details).addClass(details_collapsed_class);
            $(this.detailtrigger).text(detailtrigger_text_off);
            this.details_active = false;

        }

    };

    OrderProduct.prototype.checkDetailStatus = function () {
        return !$(this.details).hasClass(details_collapsed_class);
    };

    return OrderProduct;

})(),

SavedDesignViewer = (function () {

    var
    design_subselector = "li.sign-container",
    designs_per_page = 18,
    pagewrap_class = "pagewrap-outer",
    pages_class = "pagewrap",
    page_class = "page",
    controlwrap_class = "controlwrap",
    control_prev_class = "prev",
    control_prev_text = "Prev",
    control_next_class = "next",
    control_next_text = "Next",
    control_page_class = "page",
    page_animation_duration = 125,
    current_page_control_class = "current",

    newPage = function () { return $(document.createElement("ul")).addClass(page_class); };

    function SavedDesignViewer (e) {
        var $e = $(e);

        this.e = $e.get(0);
        this.designs = this.getDesigns();

    }

    SavedDesignViewer.prototype.init = function () {

        var _this = this, i, num, len, $pages_wrap, $pages, $page, $controls, $prev, $next, $pagecontrols, $pagecontrol, paginationEventHandler = function (event) { _this.goToPage(event.data, true); };

        this.pagecount = 0;
        this.pagecontrols = [];

        if ( this.designs.length > 0 ) {

            $pages_wrap = $(document.createElement("div")).addClass(pagewrap_class).append($pages = $(document.createElement("div")).addClass(pages_class));
            $page = newPage();

            // Store the page wrap element for later manipulation.
            this.pagewrap = $pages_wrap.get(0);

            // Create the pages.
            for ( i = 0, num = 1, len = this.designs.length; i < len; i++, num++ ) {

                // Append the design to the page.
                $page.append(this.designs[i]);

                // If this is the last design in the page, or the last design overall...
                if ( num === len || num % designs_per_page === 0 ) {

                    // Append the page.
                    $pages.append($page);
                    this.pagecount++;

                    // Create a new page if necessary.
                    if ( num < len ) {
                        $page = newPage();
                    }

                }

            }

            if ( this.pagecount > 1 ) {

                $controls = $(document.createElement("div")).addClass(controlwrap_class);

                $prev = $(document.createElement("div")).addClass(control_prev_class).text(control_prev_text).on("click", function (event) {
                    _this.goToPage(_this.page - 1, true);
                });

                $next = $(document.createElement("div")).addClass(control_next_class).text(control_next_text).on("click", function (event) {
                    _this.goToPage(_this.page + 1, true);
                });

                $pagecontrols = $();

                // Create the pagination controls.
                for ( i = 0, num = 1; i < this.pagecount; i++, num++ ) {

                    // Build the control.
                    $pagecontrol = $(document.createElement("div")).addClass(control_page_class).text(num).on("click", num, paginationEventHandler);

                    // Add the control to the group.
                    $pagecontrols = $pagecontrols.add($pagecontrol);

                    // Store the control for later manipulation.
                    this.pagecontrols[num] = $pagecontrol.get(0);

                }

                $controls.append($prev).append($pagecontrols).append($next);

            }

            // Empty the viewer and insert the pages.
            $(this.e).empty().append($pages_wrap).append($controls);

            // Go to the first page.
            this.goToPage(1);

        }

    };

    SavedDesignViewer.prototype.getDesigns = function () {

        var i, len, designs = [], $designs = $(this.e).find(design_subselector);

        for ( i = 0, len = $designs.length; i < len; i++ ) {

            designs.push($designs.get(i));

        }

        return designs;

    };

    SavedDesignViewer.prototype.goToPage = function (num, animate) {

        var
        i,
        $pagewrap,
        scrollpos,
        page = (isNaN(num) || num < 1) ? 1 : num > this.pagecount ? this.pagecount : parseInt(num, 10);

        if ( this.page !== page ) {

            // Prepare the pagewrap jQuery object.
            $pagewrap = $(this.pagewrap);

            // Update the page number.
            this.page = page;

            // Scroll to the page.
            scrollpos = $pagewrap.innerWidth() * (this.page - 1);
            if ( animate ) {
                $pagewrap.stop().animate({ scrollLeft: scrollpos }, page_animation_duration);
            } else {
                $pagewrap.scrollLeft(scrollpos);
            }

            // Highlight the page indicator.
            for ( i = 1; i <= this.pagecount; i++ ) {
                if ( i === page ) {
                    $(this.pagecontrols[i]).addClass(current_page_control_class);
                } else {
                    $(this.pagecontrols[i]).removeClass(current_page_control_class);
                }
            }

        }

    };

    return SavedDesignViewer;

})(),

AddressBook = (function () {

    var
    quickchangeform_subselector = "#edit-shipping-form",
    address_subselector = "div.ma-address",
    addbutton_subselector = "a.addressbook-add-address",
    errorwrap_subselector = "div.addressbook-errors",
    errormsg_class = "error clear",
    unknown_error_text = "Could not change your default address information. Please contact customer service for assistance.";

    function AddressBook (e) {

        var _this = this, $e = $(e), $addbutton = $e.find(addbutton_subselector), addhref = $addbutton.attr("href").replace(/.*(?=#[^\s]+$)/, "");

        // The form's DOM element.
        this.e = $e.get(0);

        // The quick-change form (for setting the default shipping and billing addresses).
        this.quickchangeform = $e.find(quickchangeform_subselector).get(0);

        // The saved addresses.
        this.addresses = [];
        $(this.e).find(address_subselector).each(function (i, e) {
            _this.addresses.push(new Address(e, _this));
        });

        // The new address button.
        this.addbutton = $addbutton.get(0);

        // The new address form.
        if ( typeof addhref === "string" && addhref.charAt(0) === "#" ) {
            this.addform =  new AddressForm($(addhref).get(0));
        }

        // Error holder.
        this.errorwrap = $e.find(errorwrap_subselector).get(0);

    }

    AddressBook.prototype.init = function () {

        var i, len, _this = this, $form = $(this.quickchangeform);

        // Form submission.
        $form.on("submit", function (event) {
            event.preventDefault();
            $.ajax({
                url: $form.attr("action"),
                type: "POST",
                data: $form.serialize(),
                success: function (data, textStatus, jqXHR) {
                    _this.updateDefaults(data.defaultshippingid, data.defaultbillingid);
                    _this.showErrors(data.errors);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    _this.showErrors(unknown_error_text);
                }
            });

        });

        // Addresses.
        for ( i = 0, len = this.addresses.length; i < len; i++ ) {
            if ( this.addresses[i] instanceof Address ) {
                this.addresses[i].init();
            }
        }

        // Show / hide the update form.
        $(this.addbutton).on("click", function (event) {
            event.preventDefault();
            _this.showAddForm();
        });

        if ( this.addform instanceof AddressForm ) {

            // Initialize the address form functionality.
            this.addform.init();

            // Show / hide the update form.
            $(this.addform.getCancelButton()).on("click", function (event) {
                event.preventDefault();
                _this.hideAddForm();
            });

        }

    };

    AddressBook.prototype.submitForm = function () {

        $(this.quickchangeform).trigger("submit");

    };

    AddressBook.prototype.updateDefaults = function (defaultshippingid, defaultbillingid) {

        var i, len, addressid;

        // Go through each addres...
        for ( i = 0, len = this.addresses.length; i < len; i++ ) {

            if ( this.addresses[i] instanceof Address ) {

                // Get the address ID.
                addressid = this.addresses[i].getId();

                // Check if the address is the default shipping or billing address, and update accordingly.
                this.addresses[i].updateDefaults((addressid === defaultshippingid), (addressid === defaultbillingid));

            }

        }

    };

    AddressBook.prototype.showErrors = function (errors) {

        var i, len, $errorwrap = $(this.errorwrap);

        // Empty old errors.
        $errorwrap.empty();

        // Normalize string and array arguments to a simple array.
        errors = typeof errors === "string" ? [errors] : errors;

        if ( errors instanceof Array && errors.length > 0 ) {

            for ( i = 0, len = errors.length; i < len; i++ ) {
                $errorwrap.append($(document.createElement("p")).addClass(errormsg_class).text(errors[i]));
            }

        }

    };

    AddressBook.prototype.showAddForm = function () {

        if ( $.fancybox && this.addform instanceof AddressForm ) {
            $.fancybox($(this.addform.getElement()));
        }

    };

    AddressBook.prototype.hideAddForm = function () {

        if ( $.fancybox ) {
            $.fancybox.close();
        }

    };

    return AddressBook;

})(),

Address = (function () {

    var
    addressid_attr = "addressId",
    updatebutton_subselector = "a.update-address-button",
    deletebutton_subselector = "a.delete-address-button",
    defaultshippingradio_subselector = "input.default-shipping",
    defaultbillingradio_subselector = "input.default-billing",
    defaultshipping_class = "address-defaultshipping",
    defaultbilling_class = "address-defaultbilling",
    delete_address_msg = "Are you sure you want to delete this address? This cannot be undone.";

    function Address (e, addressbook) {

        var $e = $(e), $updatebutton = $e.find(updatebutton_subselector), updatehref = $updatebutton.attr("href").replace(/.*(?=#[^\s]+$)/, "");

        // The DOM element.
        this.e = $e.get(0);

        // The adress ID.
        this.addressid = $e.data(addressid_attr);

        // The parent address book.
        this.addressbook = addressbook instanceof AddressBook ? addressbook : undefined;

        // The update button.
        this.updatebutton = $updatebutton.get(0);

        // The update button.
        this.deletebutton = $e.find(deletebutton_subselector).get(0);

        // The update form.
        if ( typeof updatehref === "string" && updatehref.charAt(0) === "#" ) {
            this.updateform = new AddressForm($(updatehref).get(0));
        }

        // The quick-change form's default shipping and billing radios.
        this.defaultshippingradio = $e.find(defaultshippingradio_subselector).get(0);
        this.defaultbillingradio = $e.find(defaultbillingradio_subselector).get(0);

    }

    Address.prototype.init = function () {

        var _this = this;

        // Send quick-change default shipping and billing changes to the server.
        $(this.defaultshippingradio).add(this.defaultbillingradio).on("change", function (event) {
            if ( _this.addressbook instanceof AddressBook ) {
                _this.addressbook.submitForm();
            }
        });

        // Show / hide the update form.
        $(this.updatebutton).on("click", function (event) {
            event.preventDefault();
            _this.showUpdateForm();
        });

        // Show a confirmation dialog when deleting an address.
        $(this.deletebutton).on("click", function (event) {
            return window.confirm(delete_address_msg);
        });

        if ( this.updateform instanceof AddressForm ) {

            // Initialize the address form functionality.
            this.updateform.init();

            // Show / hide the update form.
            $(this.updateform.getCancelButton()).on("click", function (event) {
                event.preventDefault();
                _this.hideUpdateForm();
            });

        }

    };

    Address.prototype.updateDefaults = function (defaultshipping, defaultbilling) {

        // Add or remove the default shipping highlight class.
        if ( defaultshipping ) {
            $(this.e).addClass(defaultshipping_class);
        } else {
            $(this.e).removeClass(defaultshipping_class);
        }

        // Add or remove the default billing highlight class.
        if ( defaultbilling ) {
            $(this.e).addClass(defaultbilling_class);
        } else {
            $(this.e).removeClass(defaultbilling_class);
        }

        // Check or uncheck the radio buttons.
        $(this.defaultshippingradio).prop("checked", !!defaultshipping);
        $(this.defaultbillingradio).prop("checked", !!defaultbilling);

        // Check or uncheck the update form's checbkboxes.
        if ( this.updateform instanceof AddressForm ) {
            this.updateform.setDefaultShipping(!!defaultshipping);
            this.updateform.setDefaultBilling(!!defaultbilling);
        }

    };

    Address.prototype.showUpdateForm = function () {

        if ( $.fancybox && this.updateform instanceof AddressForm ) {
            $.fancybox($(this.updateform.getElement()));
        }

    };

    Address.prototype.hideUpdateForm = function () {

        if ( $.fancybox ) {
            $.fancybox.close();
        }

    };

    Address.prototype.getId = function () {
        return this.addressid;
    };

    return Address;

})(),

AddressForm = (function () {

    var
    country_subselector = "select.address-country-select",
    statewrap_subselector = "div.address-state-wrap",
    statewrap_hidden_class = "hidden",
    state_requirement_attr = "stateRequired",
    cancelbutton_subselector = "button.cancel",
    defaultshippingcheckbox_subselector = "input.default-shipping",
    defaultbillingcheckbox_subselector = "input.default-billing";

    function AddressForm (e) {

        var $e = $(e);

        this.e = $e.get(0);
        this.countryselect = $e.find(country_subselector).get(0);
        this.statewrap = $e.find(statewrap_subselector).get(0);
        this.cancelbutton = $e.find(cancelbutton_subselector).get(0);
        this.defaultshippingcheckbox = $e.find(defaultshippingcheckbox_subselector).get(0);
        this.defaultbillingcheckbox = $e.find(defaultbillingcheckbox_subselector).get(0);

    }

    AddressForm.prototype.init = function () {

        var
        _this = this,
        $countryselect = $(this.countryselect),
        updateStateWrap = function () {
            _this.updateStateWrap($countryselect.find(":selected").data(state_requirement_attr) !== false);
        };

        // Update the state wrap and then update it again every time the country changes.
        updateStateWrap();
        $countryselect.on("change", updateStateWrap);

    };

    AddressForm.prototype.updateStateWrap = function (staterequired) {

        if ( staterequired ) {
            this.showStateWrap();
        } else {
            this.hideStateWrap();
        }

    };

    AddressForm.prototype.showStateWrap = function () {
        $(this.statewrap).removeClass(statewrap_hidden_class);
    };

    AddressForm.prototype.hideStateWrap = function () {
        $(this.statewrap).addClass(statewrap_hidden_class);
    };

    AddressForm.prototype.getElement = function () {
        return this.e;
    };

    AddressForm.prototype.getCancelButton = function () {
        return this.cancelbutton;
    };

    AddressForm.prototype.setDefaultShipping = function (defaultshipping) {
        $(this.defaultshippingcheckbox).prop("checked", !!defaultshipping);
    };

    AddressForm.prototype.setDefaultBilling = function (defaultbilling) {
        $(this.defaultbillingcheckbox).prop("checked", !!defaultbilling);
    };

    return AddressForm;

})(),

SearchNav = (function () {

    var
    submenu_subselector = "div.search-sidebar-submenu";

    function SearchNav (e) {

        var $e = $(e), _this = this;

        // Prepare object properties.
        this.e = $e.get(0);
        this.menus = [];

        // Get the submenus.
        $e.find(submenu_subselector).each(function (i, e) {
            _this.menus.push(new SearchNavMenu(e));
        });

    }

    SearchNav.prototype.init = function () {

        var i, len;

        // Initialize the submenus.
        for ( i = 0, len = this.menus.length; i < len; i++ ) {

            if ( this.menus[i] instanceof SearchNavMenu ) {
                this.menus[i].init();
            }

        }

    };

    return SearchNav;

})(),

SearchNavMenu = (function () {

    var
    itemcount_cutoff = 10,
    list_subselector = "ul.alpha-list-of-categories",
    items_subselector = "li", // Note that these are selected as children of list_subselector.
    togglebutton_class = "showmore",
    togglebutton_text_collapsed = "Show More\u2026",
    togglebutton_text_expanded = "Show Less\u2026",
    item_hidden_class = "hidden";

    function SearchNavMenu (e) {

        var $e = $(e), $list = $e.find(list_subselector);

        this.e = $e.get(0);
        this.list = $list.get(0);
        this.items = $.makeArray($list.children(items_subselector));

    }

    SearchNavMenu.prototype.init = function () {

        var $toggle, _this = this;

        if ( this.items.length > itemcount_cutoff ) {

            // Collapse the submenu to show only the top items.
            this.showTopItems();

            // Store the menu state.
            this.expanded = false;

            // Create the toggle button and initialize its functionality.
            $toggle = $(document.createElement("div")).addClass(togglebutton_class).text(togglebutton_text_collapsed).on("click", function (event) {

                if ( _this.expanded ) {
                    _this.showTopItems();
                } else {
                    _this.showAllItems();
                }

            });

            // Append the toggle button to the submenu.
            $(this.e).append($toggle);

            // Store the toggle button DOM element.
            this.togglebutton = $toggle.get(0);

        }

    };

    SearchNavMenu.prototype.showAllItems = function () {

        var i, len;

        // Show all of the items.
        for ( i = 0, len = this.items.length; i < len; i++ ) {
            $(this.items[i]).removeClass(item_hidden_class);
        }

        // Update the toggle button text.
        this.setToggleButtonText(togglebutton_text_expanded);

        // Update the menu state.
        this.expanded = true;

    };

    SearchNavMenu.prototype.showTopItems = function () {

        var i, len;

        // Show the top items.
        for ( i = 0; i < itemcount_cutoff; i++ ) {
            $(this.items[i]).removeClass(item_hidden_class);
        }

        // Hide the rest of the items.
        for ( i = itemcount_cutoff, len = this.items.length; i < len; i++ ) {
            $(this.items[i]).addClass(item_hidden_class);
        }

        // Update the toggle button text.
        this.setToggleButtonText(togglebutton_text_collapsed);

        // Update the menu state.
        this.expanded = false;

    };

    SearchNavMenu.prototype.setToggleButtonText = function (text) {
        $(this.togglebutton).text(text);
    };

    return SearchNavMenu;

})(),

PriceGrid = (function () {

    var
    defaultClass = "",
    fixedClass = "fixed",
    bottomClass = "bottom",
    bothClasses = fixedClass + " " + bottomClass;

    function PriceGrid (e, detectScrollParent) {

        var
        $e = $(e),
        $scrollParent = detectScrollParent ? $(e).scrollParent() : $window;

        this.$e = $e;
        this.$header = $e.children("thead");
        this.tableHeight = $e.outerHeight(false);
        this.headerHeight = this.$header.outerHeight(false);
        this.currentClass = $e.hasClass(fixedClass) ? fixedClass : this.$e.hasClass(bottomClass) ? bottomClass : defaultClass;
        this.$scrollParent = $scrollParent.closest("body").length > 0 ? $scrollParent : $window;
        this.boundScrollEvent = function () {
            this.updateScroll();
        }.bind(this);
        this.initialized = false;

        this.init();

    }

    PriceGrid.prototype.init = function () {

        if ( !this.initialized ) {
            this.$scrollParent.add(window).on("scroll resize", this.boundScrollEvent);
            this.initialized = true;
        }

        return this;

    };

    PriceGrid.prototype.destroy = function () {

        this.$scrollParent.add(window).off("scroll resize", this.boundScrollEvent);

        return this;

    };

    PriceGrid.prototype.updateScroll = function () {

        var
        scrollParentHeight = this.$scrollParent.innerHeight(),
        scrollParentOffset = this.$scrollParent.offset(),
        scrollParentTop = scrollParentOffset ? scrollParentOffset.top : 0,
        scrollParentPosition = this.$scrollParent === $window ? window.ss.site.getViewportStart() + this.$scrollParent.scrollTop() : 0,
        tableOffset = this.$e.offset(),
        tableTop = tableOffset ? tableOffset.top - scrollParentTop : 0,
        tableBottom = tableTop + this.tableHeight - this.headerHeight;

        // If the scroll parent is big enough to accomodate the table header and is scrolled past the the top of the table...
        if ( scrollParentHeight > this.headerHeight && scrollParentPosition > tableTop ) {

            // If the scroll parent has not also scrolled past the bottom of the table...
            if ( scrollParentPosition < tableBottom ) {

                this.fixHeaderToView();

            // If the scroll parent has scrolled past the bottom of the table...
            } else {

                this.stickHeaderToBottom();

            }

        // If the scroll parent is smaller than the table header or is not scrolled past the top of the table...
        } else {

            this.stickHeaderToTop();

        }

    };

    PriceGrid.prototype.fixHeaderToView = function () {

        if ( this.currentClass !== fixedClass ) {
            this.$e.removeClass(bottomClass).addClass(fixedClass);
            this.currentClass = fixedClass;
        }

    };

    PriceGrid.prototype.stickHeaderToBottom = function () {

        if ( this.currentClass !== bottomClass ) {
            this.$e.removeClass(fixedClass).addClass(bottomClass);
            this.currentClass = bottomClass;
        }

    };

    PriceGrid.prototype.stickHeaderToTop = function () {

        if ( this.currentClass !== defaultClass ) {
            this.$e.removeClass(bothClasses);
            this.currentClass = defaultClass;
        }

    };

    return PriceGrid;

})(),

OrderTrackingPicker = (function () {

    var
    formSelector = "form",
    orderInputSelector = "input[type=\"text\"]",
    dropdownSelector = "div.dropdown",
    dropdownOptionSubselector = "li",
    orderNumberSubselector = "span.track-order-num",
    dropdownHiddenClass = "hidden";

    function OrderTrackingPicker (e) {

        var
        $e = $(e),
        $form = $e.find(formSelector).eq(0),
        $orderInput = $e.find(orderInputSelector).eq(0),
        $dropdown = $e.find(dropdownSelector).eq(0);

        this.dom = {
            $form: $form,
            $orderInput: $orderInput,
            $dropdown: $dropdown,
            $dropdownOptions: $dropdown.find(dropdownOptionSubselector)
        };

    }

    OrderTrackingPicker.prototype.init = function () {

        var
        _this = this,
        updateDropdownDisplay = function () {
            if ( _this.dom.$orderInput.is(":focus") || _this.dom.$dropdown.is(":hover") ) {
                _this.showDropdown();
            } else {
                _this.hideDropdown();
            }
        };

        this.dom.$orderInput.on("focus blur", updateDropdownDisplay);

        this.dom.$dropdown.on("mouseenter mouseleave", updateDropdownDisplay);

        this.dom.$dropdownOptions.on("click", function (e) {
            e.preventDefault();
            _this.hideDropdown();
            _this.fillInput($(this).find(orderNumberSubselector).text());
            _this.dom.$form.submit();
        });

    };

    OrderTrackingPicker.prototype.showDropdown = function () {
        this.dom.$dropdown.removeClass(dropdownHiddenClass);
    };

    OrderTrackingPicker.prototype.hideDropdown = function () {
        this.dom.$dropdown.addClass(dropdownHiddenClass);
    };

    OrderTrackingPicker.prototype.fillInput = function (val) {
        this.dom.$orderInput.val(val);
    };

    return OrderTrackingPicker;

})(),

Site = (function () {

    var
    minimumStickyWindowDimensions = {
        width: 980,
        height: 450
    },
    selectors = {
        miniCart: ".minicart",
        accountMenu: "div.header-icon.icon-user",
        navigationWrap: "nav",
        navigationMenus: "nav > div > div > ul > li"
    },
    classes = {
        sticky: "scroll-active"
    };

    function Site () {

        this.dom = {
            $root: $body,
            $navigation: $body.find(selectors.navigationWrap)
        };

        this.headerIsSticky = false;
        this.miniCart = new MiniCart($body.find(selectors.miniCart).get(0));
        this.accountMenu = new AccountMenu($body.find(selectors.accountMenu).get(0));
        this.navigationMenus = [];
        $body.find(selectors.navigationMenus).each(function (i, el) {
            this.navigationMenus.push(new NavigationMenu(el, this));
        }.bind(this));
        this.stickyModeBreakPoint = NaN;

    }

    Site.prototype.init = function () {

        var i, len, navigationOffset = this.dom.$navigation.offset();

        // Calculate the sticky mode breakpoint. This is in init() so that it happens only on DOM ready after styles have been applied.
        if ( navigationOffset ) {
            this.stickyModeBreakPoint = navigationOffset.top;
        }

        // NOTE: The sticky header is disabled in touch-based browsers and IE 7 and lower.
        if ( Modernizr && !Modernizr.touch && !$("html").hasClass("lt-ie8") ) {

            // Calculate the height of the sticky header and then switch the stickiness to the correct value.
            this.makeHeaderSticky();
            this.stickyHeaderHeight = this.dom.$navigation.outerHeight(false);
            this.updateStickiness();

            // Update whether or not the template is in sticky mode whenever the browser is scrolled or resized.
            $window.on("scroll resize", function () {
                this.updateStickiness();
            }.bind(this));

        }

        // Initialize the minicart.
        this.miniCart.init();

        // Initialize the account menu.
        this.accountMenu.init();

        // Initialize the navigation menus.
        for ( i = 0, len = this.navigationMenus.length; i < len; i++ ) {
            this.navigationMenus[i].init();
        }

        return this;

    };

    Site.prototype.updateStickiness = function () {

        if ( $window.height() >= minimumStickyWindowDimensions.height && $window.width() >= minimumStickyWindowDimensions.width && $window.scrollTop() >= this.stickyModeBreakPoint ) {
            this.makeHeaderSticky();
        } else {
            this.makeHeaderUnsticky();
        }

        return this;

    };

    Site.prototype.makeHeaderSticky = function () {
        this.headerIsSticky = true;
        this.dom.$root.addClass(classes.sticky);
        return this;
    };

    Site.prototype.makeHeaderUnsticky = function () {
        this.headerIsSticky = false;
        this.dom.$root.removeClass(classes.sticky);
        return this;
    };

    Site.prototype.updateMiniCart = function (qty, price) {
        this.miniCart.update(qty, price);
        return this;
    };

    Site.prototype.updateChatStatus = function (online) {
        if ( online ) {
            this.markChatAsOnline();
        } else {
            this.markChatAsOffline();
        }
        return this;
    };

    Site.prototype.getViewportStart = function () {
        return this.headerIsSticky ? this.stickyHeaderHeight : 0;
    };

    return Site;

})(),

NavigationMenu = (function () {

    var
    selectors = {
        button: ".nav-menu-link",
        primaryLink: "a"
    },
    classes = {
        expanded: "expanded"
    };

    function NavigationMenu (e, site) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e,
            $primaryLink: $e.children(selectors.primaryLink).eq(0)
        };

        this.site = site;

        this.expanded = false;

    }

    NavigationMenu.prototype.init = function () {

        if ( Modernizr && Modernizr.touch ) {

            // Prevent touch-based browsers from navigating to the primary link when they tap the button...
            this.dom.$primaryLink.on("click", function (event) {
                event.preventDefault();
            }.bind(this));

            // ... and instead have taps on that button toggle the dropdown.
            this.dom.$root.on("click", function (event) {
                if ( !this.expanded ) {
                    this.expand();
                } else {
                    this.collapse();
                }
            }.bind(this));

        } else if ( $().hoverIntent ) {

            // Show and hide the menu using HoverIntent to prevent accidental dropdown triggers.
            this.dom.$root.hoverIntent({
                timeout: 250,
                over: function () {
                    this.expand();
                }.bind(this),
                out: function () {
                    this.collapse();
                }.bind(this)
            });

        }

        return this;

    };

    NavigationMenu.prototype.expand = function () {

        var i, len;

        if ( !this.expanded ) {

            this.dom.$root.addClass(classes.expanded);
            this.expanded = true;

            // Close the other navigation menus.
            if ( this.site && this.site.navigationMenus && this.site.navigationMenus.length > 0 ) {
                for ( i = 0, len = this.site.navigationMenus.length; i < len; i++ ) {
                    if ( this.site.navigationMenus[i] instanceof NavigationMenu && this.site.navigationMenus[i] !== this ) {
                        this.site.navigationMenus[i].collapse();
                    }
                }
            }

        }

        return this;

    };

    NavigationMenu.prototype.collapse = function () {

        if ( this.expanded ) {
            this.dom.$root.removeClass(classes.expanded);
            this.expanded = false;
        }

        return this;

    };

    return NavigationMenu;

})(),

AccountMenu = (function () {

    var
    selectors = {
        button: "a.header-account-link"
    },
    classes = {
        expanded: "expanded"
    };

    function AccountMenu (e) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e,
            $button: $e.find(selectors.button).eq(0)
        };

        this.expanded = false;

    }

    AccountMenu.prototype.init = function () {

        if ( Modernizr && Modernizr.touch ) {

            // Prevent touch-based browsers from navigating to the main account page when they tap the button...
            this.dom.$button.on("click", function (event) {
                event.preventDefault();
            }.bind(this));

            // ... and instead have taps on that button toggle the dropdown.
            this.dom.$root.on("click", function (event) {
                if ( !this.expanded ) {
                    this.expand();
                } else {
                    this.collapse();
                }
            }.bind(this));

        } else if ( $().hoverIntent ) {

            // Show and hide the menu using HoverIntent.
            this.dom.$root.hoverIntent(function () {
                this.expand();
            }.bind(this), function () {
                this.collapse();
            }.bind(this));

        }

        return this;

    };

    AccountMenu.prototype.expand = function () {
        this.dom.$root.addClass(classes.expanded);
        this.expanded = true;
        return this;
    };

    AccountMenu.prototype.collapse = function () {
        this.dom.$root.removeClass(classes.expanded);
        this.expanded = false;
        return this;
    };

    return AccountMenu;

})(),

CartSaver = (function () {

    var

    CARTSAVER_ADD = "add",
    CARTSAVER_REPLACE = "replace",

    triggerSelector = ".save-cart",
    infoPromptSelector = ".save-cart-dialog",
    infoPromptDetailToggleSelector = ".show-notes",
    infoPromptNameInputSelector = "input.cart-name",
    infoPromptNotesInputSelector = ".save-notes",
    infoPromptCancelButtonSelector = ".save-cancel",
    errorDialogCloseButtonSubselector = ".close-button",
    successDialogCloseButtonSubselector = ".close-button",
    conflictPromptCancelButtonSubselector = ".conflict-cancel-button",
    conflictPromptAddButtonSubselector = ".add-button",
    conflictPromptReplaceButtonSubselector = ".replace-button",

    loadingTemplate = "saveCartLoading",
    errorTemplate = "saveCartError",
    successTemplate = "saveCartSuccess",
    conflictTemplate = "saveCartConflict",
    nameErrorTemplate = "saveCartNameError",

    infoPromptActiveClass = "prompt-visible", // Added to the root element when the information prompt should be visible.
    infoPromptDetailClass = "notes-visible"; // Added to the info prompt wrapper when the notes field should be visible and the note field activation button should be hidden.

    function CartSaver (e) {

        var $e = $(e).eq(0);

        // Find all the required DOM elements.
        this.dom = {};
        this.dom.$root = $e;
        this.dom.$trigger = $e.find(triggerSelector).eq(0);
        this.dom.$infoPrompt = $e.find(infoPromptSelector).eq(0);
        this.dom.$infoPromptDetailToggle = this.dom.$infoPrompt.find(infoPromptDetailToggleSelector).eq(0);
        this.dom.$infoPromptNameInput = this.dom.$infoPrompt.find(infoPromptNameInputSelector).eq(0);
        this.dom.$infoPromptNotesInput = this.dom.$infoPrompt.find(infoPromptNotesInputSelector).eq(0);
        this.dom.$infoPromptCancelButton = this.dom.$infoPrompt.find(infoPromptCancelButtonSelector).eq(0);
        this.dom.$nameError = null;

    }

    CartSaver.prototype.init = function () {

        // Show the info prompt when the trigger is clicked.
        this.dom.$trigger.on("click", function (event) {
            event.preventDefault();
            this.showInfoPrompt();
        }.bind(this));

        // Hide the prompt when clicking outside or clicking outside of it.
        $document.on("click", function (event) {
            if ( this.infoPromptVisible && !this.dom.$infoPrompt.is(event.target) && !this.dom.$infoPrompt.has(event.target).length && !this.dom.$trigger.is(event.target)) {
                this.hideInfoPrompt();
            }
        }.bind(this));

        // Hide the prompt when the escape key is pressed.
        $document.on("keyup", function (event) {
            if ( event.keyCode === KEYCODE_ESCAPE && this.infoPromptVisible ) {
                this.hideInfoPrompt();
            }
        }.bind(this));

        // Hide the prompt when the cancel button is clicked.
        this.dom.$infoPromptCancelButton.on("click", function (event) {
            event.preventDefault();
            this.hideInfoPrompt();
        }.bind(this));

        // Show the notes input and hide the toggle when clicking the toggle.
        this.dom.$infoPromptDetailToggle.on("click", function (event) {
            event.preventDefault();
            this.dom.$infoPrompt.addClass(infoPromptDetailClass);
            this.dom.$infoPromptDetailToggle.hide();
        }.bind(this));

        // Hide the prompt and save the cart when clicking the "Save Cart" button.
        this.dom.$infoPrompt.on("submit", function (event) {
            event.preventDefault();
            if ( this.dom.$infoPromptNameInput.val() !== "" ) {
                this.hideNameError().hideInfoPrompt().save();
            } else {
                this.showNameError();
            }
        }.bind(this));

        return this;

    };

    CartSaver.prototype.showInfoPrompt = function () {

        // Show the prompt.
        this.dom.$root.addClass(infoPromptActiveClass);

        // Focus the name input.
        this.dom.$infoPromptNameInput.trigger("focus");

        // Note that the info prompt is now visible.
        this.infoPromptVisible = true;

        return this;

    };

    CartSaver.prototype.hideInfoPrompt = function () {

        // Hide the prompt.
        this.dom.$root.removeClass(infoPromptActiveClass);

        // If empty, hide the notes input and show the toggle.
        if ( this.dom.$infoPromptNotesInput.val() === "" ) {
            this.dom.$infoPrompt.removeClass(infoPromptDetailClass);
            this.dom.$infoPromptDetailToggle.show();
        }

        // Note that the info prompt is no longer visible.
        this.infoPromptVisible = false;

        return this;

    };

    CartSaver.prototype.save = function (action) {

        // Prepare the POST data.
        var postData = {
            action: action === CARTSAVER_ADD ? "add" : action === CARTSAVER_REPLACE ? "replace" : "save",
            cartName: this.dom.$infoPromptNameInput.val(),
            cartNotes: this.dom.$infoPromptNotesInput.val()
        };

        // Show the loading dialog.
        this.showLoadingDialog();

        // Send an AJAX request.
       $.ajax({
            url: this.dom.$infoPrompt.attr("action"),
            type: "POST",
            data: postData,
            success: function (data, textStatus, jqXHR) {

                // Hide the loading dialog.
                this.hideLoadingDialog();

                if ( data ) {

                    if ( data.success ) {

                         if ( purl().attr("path").toLowerCase() === purl(data.accountUrl).attr("path").toLowerCase() ) {

                            // Refresh the page.
                            window.location.reload(true);

                        } else {

                            // Show the success dialog.
                            this.resetInformationPrompt().showSuccessDialog({
                                cartName: postData.cartName,
                                accountUrl: data.accountUrl
                            });

                        }

                    } else if ( data.nameConflict ) {

                        // Show the conflict prompt.
                        this.showConflictPrompt({
                            cartName: postData.cartName
                        });

                    } else {

                        // Show the error dialog.
                        this.showErrorDialog();

                    }

                } else {

                    // Show the error dialog.
                    this.showErrorDialog();

                }

            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {

                // Hide the loading dialog and show the error dialog.
                this.hideLoadingDialog().showErrorDialog();

            }.bind(this)
        });

        return this;

    };

    CartSaver.prototype.showLoadingDialog = function () {

        // Show the loading dialog.
        if ( !this.loading && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[loadingTemplate] ) {
            $.fancybox({
                modal: true,
                content: $(Handlebars.templates[loadingTemplate]()),
                afterShow: function () {
                    this.loading = true;
                }.bind(this),
                beforeClose: function () {
                    this.loading = false;
                }.bind(this)
            });
        }

        return this;

    };

    CartSaver.prototype.hideLoadingDialog = function () {

        // Close the loading dialog.
        if ( this.loading && $.fancybox ) {
            $.fancybox.close();
        }

        return this;

    };

    CartSaver.prototype.showErrorDialog = function () {

        var $errorDialog;

        if ( !this.errorDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[errorTemplate] ) {

            // Prepare the DOM elements.
            $errorDialog = $(Handlebars.templates[errorTemplate]());

            // Attach an event to hide the error dialog when the close button is clicked.
            $errorDialog.on("click", errorDialogCloseButtonSubselector, function (event) {
                event.preventDefault();
                this.hideErrorDialog();
            }.bind(this));

            // Show the error dialog.
            $.fancybox({
                modal: true,
                content: $errorDialog,
                afterShow: function () {
                    this.errorDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.errorDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    CartSaver.prototype.showNameError = function () {

        if ( this.dom.$nameError === null && Handlebars && Handlebars.templates && Handlebars.templates[nameErrorTemplate] ) {

            this.dom.$nameError = $(Handlebars.templates[nameErrorTemplate]());
            this.dom.$nameError.insertBefore(this.dom.$infoPromptNameInput);

        }

        return this;

    };

    CartSaver.prototype.hideNameError = function () {

        if ( this.dom.$nameError !== null ) {
            this.dom.$nameError.remove();
            this.dom.$nameError = null;
        }

        return this;

    };

    CartSaver.prototype.hideErrorDialog = function () {

        // Hide the error dialog.
        if ( this.errorDisplay && $.fancybox ) {
            $.fancybox.close();
        }

        return this;

    };

    CartSaver.prototype.showSuccessDialog = function (data) {

        var $successDialog;

        if ( !this.successDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[successTemplate] ) {

            // Prepare the DOM elements.
            $successDialog = $(Handlebars.templates[successTemplate](data));

            // Attach an event to hide the success dialog when the close button is clicked.
            $successDialog.on("click", successDialogCloseButtonSubselector, function (event) {
                event.preventDefault();
                this.hideSuccessDialog();
                window.location.reload(true);
            }.bind(this));

            // Show the success dialog.
            $.fancybox({
                modal: true,
                content: $successDialog,
                afterShow: function () {
                    this.successDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.successDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    CartSaver.prototype.hideSuccessDialog = function () {

        // Hide the success dialog.
        if ( this.successDisplay && $.fancybox ) {
            $.fancybox.close();
        }

        return this;

    };

    CartSaver.prototype.showConflictPrompt = function (data) {

        var $conflictPrompt;

        if ( !this.conflictDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[conflictTemplate] ) {

            // Prepare the DOM elements.
            $conflictPrompt = $(Handlebars.templates[conflictTemplate](data));

            // Hide the conflict prompt when the user clicks the cancel button.
            $conflictPrompt.on("click", conflictPromptCancelButtonSubselector, function (event) {
                event.preventDefault();
                this.hideConflictPrompt();
            }.bind(this));

            // Hide the conflict prompt and save again (with forced "add" argument) when the user clicks the "Add" button.
            $conflictPrompt.on("click", conflictPromptAddButtonSubselector, function (event) {
                event.preventDefault();
                this.hideConflictPrompt().save(CARTSAVER_ADD);
            }.bind(this));

            // Hide the conflict prompt and save again (with forced "replace" argument) when the user clicks the "Replace" button.
            $conflictPrompt.on("click", conflictPromptReplaceButtonSubselector, function (event) {
                event.preventDefault();
                this.hideConflictPrompt().save(CARTSAVER_REPLACE);
            }.bind(this));

            // Show the success dialog.
            $.fancybox({
                modal: true,
                content: $conflictPrompt,
                afterShow: function () {
                    this.conflictDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.conflictDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    CartSaver.prototype.hideConflictPrompt = function () {

        // Hide the conflict prompt.
        if ( this.conflictDisplay && $.fancybox ) {
            $.fancybox.close();
        }

        return this;

    };

    CartSaver.prototype.resetInformationPrompt = function () {

        // Empty the name and notes fields.
        this.dom.$infoPromptNameInput.val("");
        this.dom.$infoPromptNotesInput.val("");

        return this;

    };


    return CartSaver;

})(),

SavedCart = (function () {

    var

    LOAD_ACTION_ADD = 1,
    LOAD_ACTION_REPLACE = 2,

    loadUrl = "/load-cart",
    deleteUrl = "/delete-cart",
    detailsUrl = "/cart-details",

    idHashDataAttribute = "savedCartIdHash",
    nameDataAttribute = "savedCartName",
    ownerEmailDataAttribute = "savedCartOwnerEmailAddress",

    loadingDialogTemplate = "savedCartLoading",
    errorDialogTemplate = "savedCartError",
    mergeConflictPromptTemplate = "savedCartMergeConflict",
    deleteConfirmationPromptTemplate = "savedCartDeleteConfirmation",
    deletingDialogTemplate = "savedCartDeleting",
    detailsTemplate = "savedCartDetails",
    detailsErrorMessageTemplate = "savedCartDetailsErrorMessage",
    detailsLoadingMessageTemplate = "savedCartDetailsLoadingMessage",
    emailerTemplate = "savedCartEmailer",

    expandedClass = "expanded",

    expandedStatusLabel = "Collapse",
    collapsedStatusLabel = "Expand",

    expansionTriggersSelector = ".expansion-trigger",
    expansionStatusLabelSelector = ".expansion-status-label",
    loadButtonSelector = ".load-cart",
    detailsContainerSelector = ".order-details-open",
    detailsProductsSubselector = "tr.items-list",
    detailsDeleteButtonSubselector = ".delete",
    detailsLoadButtonSubselector = ".load",
    detailsEmailButtonSubselector = ".emailer",
    errorDialogDimissButtonSubselector = ".close-button",
    mergeConflictPromptAddButtonSubselector = ".add-items",
    mergeConflictPromptReplaceButtonSubselector = ".replace-items",
    mergeConflictPromptCancelButtonSubselector = ".close-button",
    deleteConfirmationPromptConfirmButtonSubselector = ".confirm",
    deleteConfirmationPromptCancelButtonSubselector = ".close-button";

    function SavedCart (e) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e,
            $expansionTriggers: $e.find(expansionTriggersSelector),
            $expansionStatusLabel: $e.find(expansionStatusLabelSelector).eq(0),
            $loadButton: $e.find(loadButtonSelector).eq(0),
            $detailsContainer: $e.find(detailsContainerSelector).eq(0)
        };

        this.idHash = "" + $e.data(idHashDataAttribute);
        this.cartName = "" + $e.data(nameDataAttribute);
        this.ownerEmail = "" + $e.data(ownerEmailDataAttribute);
        this.loading = false;
        this.deleting = false;
        this.errorDisplay = false;
        this.mergeConflictDisplay = false;
        this.deleteConfirmationDisplay = false;
        this.emailerDisplay = false;
        this.detailsExpanded = false;

    }

    SavedCart.prototype.init = function () {

        // Prepare the load button.
        this.dom.$loadButton.on("click", function (event) {
            event.preventDefault();
            this.load();
        }.bind(this));

        // Prepare the expansion triggers.
        this.dom.$expansionTriggers.on("click", function (event) {
            event.preventDefault();
            if ( this.detailsExpanded ) {
                this.hideDetails();
            } else {
                this.showDetails();
            }
        }.bind(this));

        return this;

    };

    SavedCart.prototype.load = function (loadAction) {

        // Show loading dialog.
        this._showLoadingDialog();

        // Send AJAX request.
        $.ajax({
            url: loadUrl,
            type: "POST",
            data: {
                action: loadAction === LOAD_ACTION_ADD ? "add" : loadAction === LOAD_ACTION_REPLACE ? "replace" : "soft-load",
                idHash: this.idHash
            },
            success: function (data, textStatus, jqXHR) {

                // Hide the loading dialog.
                this._hideLoadingDialog();

                // If no errors were encountered by the server...
                if ( data && ( !data.errors || data.errors.length === 0 ) ) {

                    // If there was no merge conflict...
                    if ( data.loaded ) {

                        // If the user is already on the cart page...
                        if ( purl().attr("path").toLowerCase() === purl(data.cartUrl).attr("path").toLowerCase() ) {

                            // Refresh the page.
                            window.location.reload(true);

                        // If the user is not already on the cart page...
                        } else {

                            // Redirect to the cart.
                            window.location = data.cartUrl;

                        }

                    // If a merge conflict was detected...
                    } else {

                        // Show the merge conflict prompt.
                        this._showMergeConflictPrompt();

                    }

                // If errors were encountered by the server...
                } else {

                    // Show an error dialog.
                    this._showErrorDialog(data ? data.errors : null);

                }

            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {

                // Hide the loading dialog and show an error dialog.
                this._hideLoadingDialog()._showErrorDialog();

            }.bind(this)
        });

        return this;

    };

    SavedCart.prototype.remove = function (force) {

        if ( !force ) {

            // Show the delete confirmation prompt.
            this._showDeleteConfirmationPrompt();

        } else {

            // Show the loading dialog.
            this._showDeletingDialog();

            // Request the cart deletion via AJAX.
            $.ajax({
                url: deleteUrl,
                type: "POST",
                data: {
                    idHash: this.idHash
                },
                success: function (data, textStatus, jqXHR) {

                    // Hide the loading dialog.
                    this._hideDeletingDialog();

                    // If the cart was deleted...
                    if ( data && data.deleted ) {

                        // Refresh the page.
                        window.location.reload(true);

                    // If the cart was not deleted...
                    } else {

                        // Show an error dialog.
                        this._showErrorDialog(data ? data.errors : null);

                    }

                }.bind(this),
                error: function (jqXHR, textStatus, errorThrown) {

                    // Hide the loading dialog and show an error dialog.
                    this._hideDeletingDialog()._showErrorDialog();

                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype.showDetails = function (forceUpdate) {

        // Expand.
        this.dom.$root.addClass(expandedClass);

        // Update the expansion status label.
        this.dom.$expansionStatusLabel.text(collapsedStatusLabel);
        this.detailsExpanded = true;

        if ( !this.detailsLoaded || forceUpdate ) {

            // Show "Details Loading" message.
            if ( Handlebars && Handlebars.templates && Handlebars.templates[detailsLoadingMessageTemplate] ) {
                this.dom.$detailsContainer.html(Handlebars.templates[detailsLoadingMessageTemplate]());
            }

            $.ajax({
                url: detailsUrl,
                type: "POST",
                data: {
                    idHash: this.idHash
                },
                success: function (data, textStatus, jqXHR) {

                    var $details;

                    // Hide "Details Loading" message.
                    this.dom.$detailsContainer.empty();

                    if ( data && data.savedCarts ) {

                        if ( Handlebars && Handlebars.templates && Handlebars.templates[detailsTemplate] ) {

                            // Generate DOM elements.
                            $details = $(Handlebars.templates[detailsTemplate](data));

                            // Instantiate OrderProducts.
                            $details.find(detailsProductsSubselector).each(function (i, e) {
                                (new OrderProduct(e)).init();
                            }.bind(this));

                            $details.on("click", detailsDeleteButtonSubselector, function (event) {

                                // Attach event to handle delete functionality.
                                event.preventDefault();
                                this.remove();

                            }.bind(this)).on("click", detailsLoadButtonSubselector, function (event) {

                                // Attach event to handle load functionality.
                                event.preventDefault();
                                this.load();

                            }.bind(this)).on("click", detailsEmailButtonSubselector, function (event) {

                                // Show the email form dialog.
                                event.preventDefault();
                                this.showEmailer();

                            }.bind(this));

                            // Insert the content.
                            this.dom.$detailsContainer.empty().append($details);

                        }

                    } else {

                        // Hide "Details Loading" message.
                        this.dom.$detailsContainer.empty();

                        // Show "Error Loading Details" message.
                        this._showDetailsErrorMessage();

                    }

                }.bind(this),
                error: function (jqXHR, textStatus, errorThrown) {

                    // Show "Error Loading Details" message.
                    this._showDetailsErrorMessage();

                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype.hideDetails = function () {

        // Collapse.
        this.dom.$root.removeClass(expandedClass);

        // Update the expansion status label.
        this.dom.$expansionStatusLabel.text(expandedStatusLabel);
        this.detailsExpanded = false;

        return this;

    };

    SavedCart.prototype._showDetailsErrorMessage = function () {

        if ( Handlebars && Handlebars.templates && Handlebars.templates[detailsErrorMessageTemplate] ) {

            this.dom.$detailsContainer.html(Handlebars.templates[detailsErrorMessageTemplate]());

        }

        return this;

    };

    SavedCart.prototype._showLoadingDialog = function () {

        if ( !this.loading && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[loadingDialogTemplate] ) {

            // Show the loading dialog.
            $.fancybox({
                modal: true,
                content: $(Handlebars.templates[loadingDialogTemplate]()),
                afterShow: function () {
                    this.loading = true;
                }.bind(this),
                beforeClose: function () {
                    this.loading = false;
                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype._hideLoadingDialog = function () {

        if ( this.loading && $.fancybox ) {

            // Hide the loading dialog.
            $.fancybox.close();

        }

        return this;

    };

    SavedCart.prototype._showDeletingDialog = function () {

        if ( !this.deleting && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[deletingDialogTemplate]) {

            // Show the loading dialog.
            $.fancybox({
                modal: true,
                content: $(Handlebars.templates[deletingDialogTemplate]()),
                afterShow: function () {
                    this.deleting = true;
                }.bind(this),
                beforeClose: function () {
                    this.deleting = false;
                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype._hideDeletingDialog = function () {

        if ( this.deleting && $.fancybox ) {

            // Hide the loading dialog.
            $.fancybox.close();

        }

        return this;

    };

    SavedCart.prototype._showErrorDialog = function (errors) {

        var $errorDialog;

        if ( !this.errorDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[errorDialogTemplate] ) {

            // Prepare the DOM elements.
            $errorDialog = $(Handlebars.templates[errorDialogTemplate]({
                errors: errors
            }));

            // Attach an event to hide the dialog when the dismiss button is clicked.
            $errorDialog.on("click", errorDialogDimissButtonSubselector, function (event) {
                event.preventDefault();
                this._hideErrorDialog();
            }.bind(this));

            // Show the loading dialog.
            $.fancybox({
                modal: true,
                content: $errorDialog,
                afterShow: function () {
                    this.errorDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.errorDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype._hideErrorDialog = function () {

        if ( this.errorDisplay && $.fancybox ) {

            // Hide the loading dialog.
            $.fancybox.close();

        }

        return this;

    };

    SavedCart.prototype._showMergeConflictPrompt = function () {

        var $mergeConflictPrompt;

        if ( !this.mergeConflictDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[mergeConflictPromptTemplate] ) {

            // Prepare the DOM elements.
            $mergeConflictPrompt = $(Handlebars.templates[mergeConflictPromptTemplate]());

            // Attach an event to hide the dialog when the dismiss button is clicked.
            $mergeConflictPrompt.on("click", mergeConflictPromptAddButtonSubselector, function (event) {

                // When the "Add" button is clicked, hide the merge conflict prompt and attempt to load again (passing an "Add" request through via AJAX).
                event.preventDefault();
                this._hideMergeConflictPrompt().load(LOAD_ACTION_ADD);

            }.bind(this)).on("click", mergeConflictPromptReplaceButtonSubselector, function (event) {

                // When the "Replace" button is clicked, hide the merge conflict prompt and attempt to load again (passing an "Replace" request through via AJAX).
                event.preventDefault();
                this._hideMergeConflictPrompt().load(LOAD_ACTION_REPLACE);

            }.bind(this)).on("click", mergeConflictPromptCancelButtonSubselector, function (event) {

                // When the "Cancel" button is clicked, hide the merge conflict prompt.
                event.preventDefault();
                this._hideMergeConflictPrompt();

            }.bind(this));

            // Show the merge conflict prompt.
            $.fancybox({
                modal: true,
                content: $mergeConflictPrompt,
                afterShow: function () {
                    this.mergeConflictDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.mergeConflictDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype._hideMergeConflictPrompt = function () {

        if ( this.mergeConflictDisplay && $.fancybox ) {

            // Hide the merge conflict prompt.
            $.fancybox.close();

        }

        return this;

    };

    SavedCart.prototype._showDeleteConfirmationPrompt = function () {

        var $deleteConfirmationPrompt;

        if ( !this.deleteConfirmationDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[deleteConfirmationPromptTemplate] ) {

            // Prepare the DOM elements.
            $deleteConfirmationPrompt = $(Handlebars.templates[deleteConfirmationPromptTemplate]({
                cartName: this.cartName
            }));

            // Attach an event to hide the dialog when the dismiss button is clicked.
            $deleteConfirmationPrompt.on("click", deleteConfirmationPromptConfirmButtonSubselector, function (event) {

                // When the "Confirm" button is clicked, hide the delete confirmation prompt and perform the deletion.
                event.preventDefault();
                this._hideDeleteConfirmationPrompt().remove(true);

            }.bind(this)).on("click", deleteConfirmationPromptCancelButtonSubselector, function (event) {

                // When the "Cancel" button is clicked, hide the delete confirmation prompt.
                event.preventDefault();
                this._hideDeleteConfirmationPrompt();

            }.bind(this));

            // Show the delete confirmation prompt.
            $.fancybox({
                modal: true,
                content: $deleteConfirmationPrompt,
                afterShow: function () {
                    this.deleteConfirmationDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.deleteConfirmationDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    SavedCart.prototype._hideDeleteConfirmationPrompt = function () {

        if ( this.deleteConfirmationDisplay && $.fancybox ) {

            // Hide the delete confirmation prompt.
            $.fancybox.close();

        }

        return this;

    };

    SavedCart.prototype.showEmailer = function () {

        var $emailer;

        if ( !this.emailerDisplay && $.fancybox && Handlebars && Handlebars.templates && Handlebars.templates[emailerTemplate] ) {

            // Prepare the DOM elements.
            $emailer = $(Handlebars.templates[emailerTemplate]({
                userEmail: this.ownerEmail
            }));

            // Create and initialize the emailer.
            (new SavedCartEmailer($emailer, this.idHash, function () {
                if ( this.emailerDisplay && $.fancybox ) {
                    $.fancybox.close();
                }
                return this;
            }.bind(this))).init();

            // Show the delete confirmation prompt.
            $.fancybox({
                modal: true,
                content: $emailer,
                afterShow: function () {
                    this.emailerDisplay = true;
                }.bind(this),
                beforeClose: function () {
                    this.emailerDisplay = false;
                }.bind(this)
            });

        }

        return this;

    };

    return SavedCart;

})(),

SavedCartEmailer = (function () {

    var

    ERROR_MISSING_EMAIL = 0,
    ERROR_INVALID_EMAIL = 1,
    INTERACTION_MODE_SINGLE = 2,
    INTERACTION_MODE_SEQUENTIAL = 3,

    sendUrl = "/email-saved-cart",

    validationErrorClass = "email-error",
    sendingClass = "email-sending",
    successClass = "email-success",

    successTemplate = "savedCartEmailerSuccess",

    missingEmailErrorText = "To send this cart, please enter the recipient's email address.",
    invalidEmailErrorText = "The email address you entered is not valid. Please correct it and try again.",
    unknownErrorText = "An unknown error was encountered. Please try again or contact customer service for assistance.",

    formSelector = ".email-cart-form",
    noticeContainerSelector = ".email-cart-notice-wrapper",
    recipientInputSelector = ".email-cart-recipient",
    greetingInputSelector = ".email-cart-message",
    copySenderCheckboxSelector = ".email-cart-cc",
    sendButtonSelector = ".email-cart-send-button",
    cancelButtonSelector = ".email-cart-cancel-button",
    successMessageWrapSelector = ".email-cart-success-message-wrapper",
    successDialogButtonSubselector = ".email-cart-done-button";

    function SavedCartEmailer ($e, cartIdHash, finishedCallback, sequentialMode) {

        this.dom = {
            $root: $e,
            $form: $e.find(formSelector).eq(0),
            $noticeContainer: $e.find(noticeContainerSelector).eq(0),
            $recipientInput: $e.find(recipientInputSelector).eq(0),
            $greetingInput: $e.find(greetingInputSelector).eq(0),
            $copySenderCheckbox: $e.find(copySenderCheckboxSelector).eq(0),
            $sendButton: $e.find(sendButtonSelector).eq(0),
            $cancelButton: $e.find(cancelButtonSelector).eq(0),
            $successMessageWrap: $e.find(successMessageWrapSelector).eq(0)
        };

        this.cartIdHash = cartIdHash;
        this.finishedCallback = typeof finishedCallback === "function" ? finishedCallback : function () { return null; };
        this.interactionMode = sequentialMode ? INTERACTION_MODE_SEQUENTIAL : INTERACTION_MODE_SINGLE; // Sequential mode allows the success message to dismiss when its button is clicked, allowing the form to be submitted multiple times in a row.

        this.errors = [];
        this.ajax = null;
        this.successMessageDisplay = false;

    }

    SavedCartEmailer.prototype.init = function () {

        // When the form is submitted, validate the input and either send or display errors accordingly.
        this.dom.$form.on("submit", function (event) {

            var i, len;

            // Prevent default functionality.
            event.preventDefault();

            // If the email isn't already sending...
            if ( this.ajax === null ) {

                // Hide any existing errors and (re)validate.
                this.hideErrors().validate();

                // If there were no errors detected during validation...
                if ( this.errors.length === 0 ) {

                    // Send the email.
                    this.sendEmail();

                // If there were errors detected during validation...
                } else {

                    // Display error notices to the user.
                    for ( i = 0, len = this.errors.length; i < len; i++ ) {
                        if ( this.errors[i] === ERROR_MISSING_EMAIL ) {
                            this.dom.$recipientInput.addClass(validationErrorClass);
                            this.showError(missingEmailErrorText);
                        } else if ( this.errors[i] === ERROR_INVALID_EMAIL ) {
                            this.dom.$recipientInput.addClass(validationErrorClass);
                            this.showError(invalidEmailErrorText);
                        }
                    }

                }

            }

        }.bind(this));

        // When the "Cancel" button is clicked, cancel any open AJAX requests and fire the supplied finisher callback.
        this.dom.$cancelButton.on("click", function (event) {

            event.preventDefault();

            // If an email is being sent via AJAX, cancel it.
            if ( this.ajax !== null ) {
                this.ajax.abort();
            }

            // Fire the supplied finisher callback.
            this.finishedCallback();

        }.bind(this));

        return this;

    };

    SavedCartEmailer.prototype.validate = function () {

        var recipientAddress = trim(this.dom.$recipientInput.val());

        this.errors = [];

        if ( recipientAddress === "" ) {
            this.errors.push(ERROR_MISSING_EMAIL);
        } else if ( !email_regex.test(recipientAddress) ) {
            this.errors.push(ERROR_INVALID_EMAIL);
        }

        return this;

    };

    SavedCartEmailer.prototype.sendEmail = function () {

        this.showSendingMessage();

        this.ajax = $.ajax({
            url: sendUrl,
            type: "POST",
            data: {
                cartIdHash: this.cartIdHash,
                recipient: trim(this.dom.$recipientInput.val()),
                greeting: this.dom.$greetingInput.val(),
                copySender: !!this.dom.$copySenderCheckbox.prop("checked")
            },
            success: function (data, textStatus, jqXHR) {
                this.hideSendingMessage();
                if ( data && data.success ) {
                    this.showSuccessMessage(data.recipientAddress);
                } else {
                    this.showError(data.errorMessage ? data.errorMessage : unknownErrorText);
                }
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                this.hideSendingMessage().showError(unknownErrorText);
            }.bind(this),
            complete: function (jqXHR, textStatus) {
                this.ajax = null;
            }.bind(this)
        });

        return this;

    };

    SavedCartEmailer.prototype.showSendingMessage = function () {

        this.dom.$root.addClass(sendingClass);
        return this;

    };

    SavedCartEmailer.prototype.hideSendingMessage = function () {

        this.dom.$root.removeClass(sendingClass);

        return this;

    };

    SavedCartEmailer.prototype.showSuccessMessage = function (recipientAddress) {

        var $successMessage;

        if ( !this.successMessageDisplay && Handlebars && Handlebars.templates && Handlebars.templates[successTemplate] ) {

            // Generate the DOM elements.
            $successMessage = $(Handlebars.templates[successTemplate]({
                recipientAddress: recipientAddress
            }));

            // Attach an event to hide this message and trigger the finisher callback when the button is clicked.
            $successMessage.on("click", successDialogButtonSubselector, function (event) {
                event.preventDefault();
                if ( this.interactionMode === INTERACTION_MODE_SEQUENTIAL ) {
                    this.hideSuccessMessage();
                }
                this.finishedCallback();
            }.bind(this));

            // Show the success message.
            this.dom.$root.addClass(successClass);
            this.dom.$successMessageWrap.append($successMessage);

            // Update the flag.
            this.successMessageDisplay = true;

        }

        return this;

    };

    SavedCartEmailer.prototype.hideSuccessMessage = function () {

        if ( this.successMessageDisplay ) {

            // Hide the success message.
            this.dom.$successMessageWrap.empty();
            this.dom.$root.removeClass(successClass);

            // Update the flag.
            this.successMessageDisplay = false;

        }

        return this;

    };

    SavedCartEmailer.prototype.showError = function (errorMessage) {

        this.dom.$noticeContainer.addClass(validationErrorClass);
        this.dom.$noticeContainer.append($(document.createElement("p")).text(errorMessage));

        return this;

    };

    SavedCartEmailer.prototype.hideErrors = function () {

        this.dom.$noticeContainer.removeClass(validationErrorClass).empty();
        this.dom.$recipientInput.removeClass(validationErrorClass);

        return this;

    };

    return SavedCartEmailer;

})(),

CartLoader = (function () {

    var

    loadUrl = "/recently-saved-carts",

    triggerSelector = ".load-cart",
    tooltipSelector = ".load-cart-dialog",
    resultsCancelSubselector = ".load-cancel",
    resultsSavedCartViewerSubselector = ".saved-cart-viewer",
    errorMessageCloseButtonSubselector = "div.close-button",

    loadingMessageTemplate = "cartLoaderLoading",
    errorMessageTemplate = "cartLoaderError",
    resultsTemplate = "cartLoaderResults";

    function CartLoader (e) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e,
            $trigger: $e.find(triggerSelector).eq(0),
            $tooltip: $e.find(tooltipSelector).eq(0)
        };

        this.tooltipVisible = false;
        this.loading = false;
        this.loaded = false;
        this.errorDisplay = false;

        this.savedCartViewer = null;

    }

    CartLoader.prototype.init = function () {

        // Show the tooltip and load the saved carts when the trigger is clicked.
        this.dom.$trigger.on("click", function (event) {
            event.preventDefault();
            this.showTooltip().loadSavedCarts();
        }.bind(this));

        // Hide the prompt when clicking outside or clicking outside of it.
        $document.on("click", function (event) {
            if ( this.tooltipVisible && !this.dom.$tooltip.is(event.target) && !this.dom.$tooltip.has(event.target).length && !this.dom.$trigger.is(event.target)) {
                this.hideTooltip();
            }
        }.bind(this));

        // Hide the prompt when the escape key is pressed.
        $document.on("keyup", function (event) {
            if ( event.keyCode === KEYCODE_ESCAPE && this.tooltipVisible ) {
                this.hideTooltip();
            }
        }.bind(this));

        return this;

    };

    CartLoader.prototype.loadSavedCarts = function (forceUpdate) {

        if ( !this.loaded || forceUpdate ) {

            // Show the loading message.
            this._showLoadingMessage();

            // Load the saved carts via AJAX.
            $.ajax({
                url: loadUrl,
                type: "GET",
                success: function (data, textStatus, jqXHR) {

                    var $results;

                    // Hide the loading message.
                    this._hideLoadingMessage();

                    if ( data && data.savedCarts && data.savedCarts.length ) {

                        if ( Handlebars && Handlebars.templates && Handlebars.templates[resultsTemplate] ) {

                            // Generate the DOM elements.
                            $results = $(Handlebars.templates[resultsTemplate](data));

                            // Attach an event to hide the tooltip when the cancel button is clicked.
                            $results.on("click", resultsCancelSubselector, function (event) {
                                event.preventDefault();
                                this.hideTooltip();
                            }.bind(this));

                            // Create and initialize the SavedCartViewer.
                            this.savedCartViewer = new SavedCartViewer($results.find(resultsSavedCartViewerSubselector).get(0));
                            this.savedCartViewer.init();

                            // Display the results.
                            this.dom.$tooltip.empty().append($results);

                        }

                        this.loaded = true;

                    } else {

                        // Show the error message.
                        this._showErrorMessage();

                    }

                }.bind(this),
                error: function (jqXHR, textStatus, errorThrown) {

                    // Hide the loading message.
                    this._hideLoadingMessage()._showErrorMessage();

                }.bind(this)
            });

        }

        return this;

    };

    CartLoader.prototype.showTooltip = function () {

        if ( !this.tooltipVisible ) {
            this.dom.$tooltip.show();
            this.tooltipVisible = true;
        }

        return this;

    };

    CartLoader.prototype.hideTooltip = function () {

        if ( this.tooltipVisible ) {
            this.dom.$tooltip.hide();
            this.tooltipVisible = false;
        }

        return this;

    };

    CartLoader.prototype._showLoadingMessage = function () {

        if ( !this.loading && Handlebars && Handlebars.templates && Handlebars.templates[loadingMessageTemplate] ) {

            this.dom.$tooltip.html(Handlebars.templates[loadingMessageTemplate]());
            this.loading = true;

        }

        return this;

    };

    CartLoader.prototype._hideLoadingMessage = function () {

        if ( this.loading ) {

            this.dom.$tooltip.empty();
            this.loading = false;

        }

        return this;

    };

    CartLoader.prototype._showErrorMessage = function () {

        var $errorMessage;

        if ( !this.errorDisplay && Handlebars && Handlebars.templates && Handlebars.templates[errorMessageTemplate] ) {

            // Generate the error message.
            $errorMessage = $(Handlebars.templates[errorMessageTemplate]());

            // Attach an event to allow the close button inside to function.
            $errorMessage.on("click", errorMessageCloseButtonSubselector, function (event) {
                event.preventDefault();
                this._hideErrorMessage();
            }.bind(this));

            // Hide the tooltip.
            this.hideTooltip();

            // Show the error message.
            $.fancybox({
                modal: true,
                content: $errorMessage
            });

            // Set a flag noting that the error is currently being displayed.
            this.errorDisplay = true;

        }

        return this;

    };

    CartLoader.prototype._hideErrorMessage = function () {

        if ( this.errorDisplay && $.fancybox ) {

            // Hide the error message.
            $.fancybox.close();

            // Set a flag noting that the error is no longer being displayed.
            this.errorDisplay = false;

        }

        return this;

    };

    return CartLoader;

})(),

SavedCartViewer = (function () {

    var

    savedCartsSelector = ".saved-cart";

    function SavedCartViewer (e) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e
        };

        // Find the saved carts.
        this.savedCarts = [];
        this.dom.$root.find(savedCartsSelector).each(function (i, e) {
            this.savedCarts.push(new SavedCart(e));
        }.bind(this));

    }

    SavedCartViewer.prototype.init = function () {

        var i, len;

        // Initialize the saved carts.
        for ( i = 0, len = this.savedCarts.length; i < len; i++ ) {
            this.savedCarts[i].init();
        }

        return this;

    };

    return SavedCartViewer;

})(),

UlRecognitionNote = (function () {

    var
    triggerSelector = ".ul-toolip-link",
    tooltipSelector = ".ul-tooltip";

    function UlRecognitionNote (e) {

        var $e = $(e).eq(0);

        this.dom = {
            $root: $e,
            $trigger: $e.find(triggerSelector).eq(0),
            $tooltip: $e.find(tooltipSelector).eq(0)
        };

        this.events = {
            documentClick: function (event) {
                if ( this.tooltipVisible && !this.dom.$trigger.is(event.target) && !this.dom.$tooltip.is(event.target) && !this.dom.$trigger.has(event.target).length && !this.dom.$tooltip.has(event.target).length ) {
                    this.hideTooltip();
                }
            }.bind(this),
            triggerClick: function (event) {
                event.preventDefault();
                if ( this.tooltipVisible ) {
                    this.hideTooltip();
                } else {
                    this.showTooltip();
                }
            }.bind(this)
        };

        this.tooltipVisible = false;

    }

    UlRecognitionNote.prototype.init = function () {

        // Toggle the tooltip when the trigger is clicked.
        this.dom.$trigger.on("click", this.events.triggerClick.bind(this));

        // Hide the tooltip when when clicking outside of it.
        $document.on("click", this.events.documentClick.bind(this));

        return this;

    };

    UlRecognitionNote.prototype.destroy = function () {

        // Detach the events that were attached in init().
        this.dom.$trigger.off("click", this.events.triggerClick);
        $document.off("click", this.events.documentClick);

        return this;

    };

    UlRecognitionNote.prototype.showTooltip = function () {

        if ( !this.tooltipVisible ) {
            this.dom.$tooltip.show();
            this.tooltipVisible = true;
        }

        return this;

    };

    UlRecognitionNote.prototype.hideTooltip = function () {

        if ( this.tooltipVisible ) {
            this.dom.$tooltip.hide();
            this.tooltipVisible = false;
        }

        return this;

    };

    return UlRecognitionNote;

})(),

ProductTabTables = (function() {

    function ProductTabTables (e) {
        var $e = $(e).eq(0);

        this.dom = {
            $root: $e
        };
        return this;
    }

    ProductTabTables.prototype.init = function () {
        this.dom.$root.hide();
        return this;
    };

    return ProductTabTables;

})(),

Skuer = (function() {
    var selectors = {
        sizeActionButton: ".product-size-list li .pslist",
        sizeControlList: ".psl",
        sizeButtons: ".product-size-list .psitem", 
        sizeControlShell: ".product-size-list",
        skuCode: ".product-sku-number-mini"
    },
        classes = {
        sizeControlSelected: "selsize",
        sizeControlSelectedArrow: "liselsize",
        hideSizeSelector: "inline-size-selector",
        ghoster: "ghost"
    },
        
        skuContainer = null,
        tWidth = 0,
        qty = 0,
        cushion = 0,
        maxWidth = 428, //428 //596
        padding = 0,
        sizeId = 0,
        materialId = 0,
        laminateId = 1,
        mountingId = 1,
        packagingId = 1,
        currentQty = 1,
        productID = 0,
        cpiValue = 0,
        isCustom = false,
        currentPrice = 0.00,
        finalPrice = 0.00,
        numSkus = 0,
        skuCode = "",
        imagePath = "",
        productType = "Sign",
        inStock = true,
        onSale = false,
        inventory = 0,
        skuSon = "",
        OutofStockLock = false,
        complianceIds = [],
        pricing = [],
        massStockCheck = false,
        userClick = [0,0,0,0],
        freightRequired = false,
        innerUnits = 0,
        packageInclusionNote = "",
        packageInclusionNoteLabel = "",
        packageName = "",
        packageNamePlural = "",
        dedicatedPackageCount = 0,
        user_qty = 1,
        productStateURL = "",
        sizeOutofStock = false,
        minQty = 1,
        preconfiguredSku = "",
        nullSizes = false,
        nullMaterials = false,
        nullLaminates = false,
        nullMountings = false,
        nullPackagings = false;        


    function Skuer (e) {
        var $e = $(e).eq(0);

        this.dom = {
            $root: $e
        };

        skuContainer = $e;

        //Skuer Handlers
        $(skuContainer).find(selectors.sizeActionButton).on("click", function(v) {
            if (!OutofStockLock) {
                this.sizeChange(v.target);
            }
        }.bind(this));
        $(skuContainer).find('input[name=radiomaterial]').on("click", function(v) {
            if (!OutofStockLock) {
                materialId = $(v.target).attr('value');
                userClick[0] = $(v.target).attr('value');
                console.log('clicked material value: ' + $(v.target).attr('value'));
                this.setupLaminates(false);
            }
        }.bind(this));
        $(skuContainer).find('input[name=radiolaminate]').on("click", function(v) {
            if (!OutofStockLock) {
                laminateId = $(v.target).attr('value');
                console.log('clicked laminate value: ' + $(v.target).attr('value'));
                userClick[1] = laminateId;
                this.setupMountings(false);
            }
        }.bind(this));
        $(skuContainer).find('input[name=radiomounting]').on("click", function(v) {
            if (!OutofStockLock) {
                mountingId = $(v.target).attr('value');
                userClick[2] = mountingId;
                console.log('clicked mounting value: ' + $(v.target).attr('value'));
                this.setupPackagingSelect();
                //this.refreshByMountingHoles(sizeId, materialId, laminateId, mountingId);
            }
        }.bind(this));
        $(skuContainer).find('.product-packaging-list').on("click", 'input[name=radiopackaging]', function(v) {
            if (!OutofStockLock) {
                //userClick[3] = packagingId;
                console.log('clicked value: ' + $(v.target).attr('value'));
                if (!isNaN(parseFloat($(v.target).attr('value'))) && isFinite($(v.target).attr('value'))) {
                    innerUnits = $(v.target).attr('value');
                    console.log('----innerUnits: ' + $(v.target).attr('value'));
                } else {
                    console.log('before user clicked packaging: ' + packageInclusionNote);
                    packageInclusionNote = $(v.target).attr('value');
                    if (packageInclusionNote != null && typeof packageInclusionNote != undefined && packageInclusionNote != '') {
                        console.log('packageInclusionNote: ' + packageInclusionNote);
                        packageInclusionNote = packageInclusionNote.replace(/["']/g, "");
                    }
                    console.log('user clicked packaging: ' + packageInclusionNote);
                }
                this.setupPackagingSelect();
            }
        }.bind(this));

        $(skuContainer).find('.add-to-cart-button-product').on('click', function(e) {
            if (!OutofStockLock) {
                this.addtoCart();
                e.preventDefault();
                return false;
            }
        }.bind(this));

        //Price Table Select Buttons...
        $('.price-table-select-button').on('click', function() {
            this.setupSkuerBySkuCode($(this).attr('data-sku-id'));
        }.bind(this));

        //Price Change
        $(skuContainer).find('.product-under-sku-quantity input').change(function() {
            //Pricing (Total Price)
            currentQty = $(skuContainer).find('.product-under-sku-quantity input').attr('value');
            user_qty = currentQty;
            for (var b = 0; b < pricing.length; b++) {
                if (b+1 < pricing.length) {
                    if (currentQty >= pricing[b].minimumQuantity && currentQty < pricing[b+1].minimumQuantity) {
                        currentPrice = pricing[b].price;
                        $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$'));
                    }
                }
                if (b+1 == pricing.length) {
                    if (currentQty >= pricing[b].minimumQuantity) {
                        currentPrice = pricing[b].price;
                        $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$' ));
                    }
                }
            }
            if (user_qty < minQty) {
                $(skuContainer).find('.product-under-sku-total-label span').text('$0.00');
            }
        }.bind(this));
    }


    Skuer.prototype.init = function() {
        console.log('container: ');
        console.log(skuContainer);

        Tipped.create('.user-qty-box', $('.min-qty-error').html(), {
            showOn: false,
            hideOn: 'click',
            //hideAfter: 2200,
            hideDelay: 10,
            hideOnClickOutside: false,
            skin: 'minqty',
            padding: false,
            position: 'right',
            close: true,
            onShow: function(content, element) {
                console.log('tooltip visible with content: ');
                console.log(content);
                window.setTimeout(function() {
                    Tipped.hide('.user-qty-box');
                }, 7200);
            },
            afterUpdate: function(content, element) {
                $(content).find('.min-qty-num-tip').text(minQty + "+");
            }    
        });

        if ($(skuContainer).length > 0) {
            //Set Skuer JSON...
             skuSon = JSON.parse($('.product-page-wrapper').attr('data-skuer-json'));
        }

        if (skuSon != '') {

            //Hide Compliances...
            $(skuContainer).find('.compliance-option').addClass('ghost');

            numSkus = skuSon.skus.length;
            productStateURL = skuSon.urlState;
            var pcs = skuSon.preConfiguredSku;
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].id == pcs) {
                    preconfiguredSku = skuSon.skus[m].skuCode;
                    console.log('preconfigured sku: ' + preconfiguredSku);
                    break;
                }
            }            
            
        //try {
            //TESTING ONLY...
            console.log('# of Skus: ' + numSkus);
            // if ($(skuContainer).find('.product-material-list').children().length === 1) {
            //     if ($(skuContainer).find('.product-material-list').find('label').text() == "N/A") {
            //         $(skuContainer).find('.product-material-list').empty();
            //     }
            // }
            console.log('material length: ' + $(skuContainer).find('.product-material-list').children().length);

            //------END TESTING--------

            //Fix Dashed vertical line height
            if ($('.related-product-image').length === 0) { maxWidth = 574; }

            //SKU Element Null Check...
            if ($(skuContainer).find('.product-size-list').children().length === 0) { nullSizes = true; sizeId = null; }
            if ($(skuContainer).find('.product-material-list').children().length === 0) { nullMaterials = true; materialId = null; this.shouldHideArea(0); }
            if ($(skuContainer).find('.product-laminate-list').children().length === 0) { nullLaminates = true; laminateId = null; this.shouldHideArea(1); }
            if ($(skuContainer).find('.product-mounting-list').children().length === 0) { nullMountings = true; mountingId = null; this.shouldHideArea(2); }
            //if ($(skuContainer).find('.product-packaging-list').length == 0) { nullPackagings = true; packageId = null; }

            console.log("nullSizes: " + nullSizes);
            console.log("nullMaterials: " + nullMaterials);
            console.log("nullLaminates: " + nullLaminates);
            console.log("nullMountings: " + nullMountings);

            //Check if Sizes Exist...
            if (!nullSizes) {
                //Get Selected Size ID...
                sizeId = $(skuContainer).find('input[name=radiosize]:checked').eq(0).attr('value');
                //Calculate Sizes and Adjust or Switch to Radio Builder
                $(skuContainer).find(selectors.sizeButtons).each(function() {
                    tWidth = tWidth + $(this).width();
                    qty++;
                 });
                cushion = maxWidth - tWidth;
                $(skuContainer).find(selectors.sizeControlShell).removeClass(classes.hideSizeSelector);
                if (cushion < 5) {
                    //Size Control Will Not Fit, Switch To Radio Support...
                    $(skuContainer).find('.psl').parent().remove();
                    $(skuContainer).find('.product-size-selector ul li').addClass('product-radio-holder').addClass('product-radio-size-adj');
                    $(skuContainer).find('.product-size-selector').contents().unwrap();
                    $(skuContainer).find('.size-container').contents().unwrap();
                    $(skuContainer).find('.ps-container').removeClass('ps-container');
                    $(skuContainer).find('.product-size-list').removeClass('product-size-list');
                    $(skuContainer).find('.liselsize').removeClass('liselsize');
                    $(skuContainer).find('.pslist').removeClass('pslist');
                    $(skuContainer).find('.psitem').removeClass('psitem');
                    $(skuContainer).find('.selsize').removeClass('selsize');
                } else {
                    //Size Control Will Fit, Distribute Padding...
                    padding = (cushion / qty);
                    padding = Math.floor((padding / 2));
                    $(skuContainer).find(selectors.sizeButtons).each(function() {
                        $(this).css("padding-left",padding);
                        $(this).css("padding-right",padding);
                    });  
                }
            }

            //Set Preconfigured Sku...
            this.setupSkuerBySkuCode(preconfiguredSku);

            //Setup Lower Areas...
            // if (!nullMaterials) {
            //     this.setupMaterials(false);
            // } else {
            //     if (!nullLaminates) {
            //         this.setupLaminates(false);
            //     } else {
            //         if (!nullMountings) {
            //             this.setupMountings(false);
            //         } else {
            //             //Everything is null, jump to packaging setup
            //             this.setupPackagings(false);
            //         }
            //     }
            // }
        //} catch (err) {
        //    console.log('error: ' + err);
        //}
    }
        return this;
    };

    Skuer.prototype.setupMaterials = function(wasUserClicked) {
        if (!nullMaterials) {
            $(skuContainer).find('.skuer-material-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].sizeId == sizeId) {
                    $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").parent().removeClass(classes.ghoster);
                    if (!skuSon.skus[m].inStock) {
                        //Material Not In Stock...
                        $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").prop('disabled', true).parent().addClass('out-of-stock');
                    } else {
                        $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").prop('disabled', false).parent().removeClass('out-of-stock');
                    }
                }
            }
            if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(0);
            } else if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(0);
            } else if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(0);
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[0] > 0) {
                    console.log("userClick: " + userClick[0]);
                    $(skuContainer).find('.skuer-material-radio').find('input[type=radio]').attr('checked', false);
                    if (!$(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().hasClass(classes.ghoster) && $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').prop('disabled') === false) {
                        materialId = $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').attr('checked',true).attr('value');    
                        //userClick[0] = 0;
                    } else {
                        sizeOutofStock = this.setFirstMaterialAvailable();
                        console.log('materialId: ' + materialId + ' [' + $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().find('label').text() +']');
                    }
                } else {
                    sizeOutofStock = this.setFirstMaterialAvailable();
                    console.log('materialId: ' + materialId + ' [' + $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().find('label').text() +']');
                }
            }
        }
        this.setupLaminates(false);
    };

    Skuer.prototype.setFirstMaterialAvailable = function(){
        //Loop and Set first radio not hidden or disabled...
        //Return Boolean Logic: Is Entire size out if stock?
        //Set first material as selected Id to populate pricing even tho it's out of stock so its not empty
        console.log('entering setFirstMaterialAvailable');
        var mId = 0, mFound = false;
        $(skuContainer).find('.skuer-material-radio').each(function(i, x) {
            if (i === 0) {mId = $(this).find('input[type=radio]').attr('value');}
            if (!$(this).hasClass('ghost') && $(this).find('input[type=radio]').prop('disabled') === false) {
                materialId = $(this).find('input[type=radio]').attr('checked',true).attr('value');
                mFound = true;
                return false;
            }
        });
        if (!mFound) {
            materialId = mId;
            return true;
        }
    };

    Skuer.prototype.setupLaminates = function(wasUserClicked) {
        if (!nullLaminates) {
            $(skuContainer).find('.skuer-laminate-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId) {
                    $(skuContainer).find('.skuer-laminate-radio').find("[value='" + skuSon.skus[m].laminateId + "']").parent().removeClass(classes.ghoster);
                }
            }
            if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(1);
                console.log('making laminate inline');
            } else if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(1);
                console.log('making laminate option');
            } else if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(1);
                console.log('making laminate hidden');
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[1] > 0) {
                    console.log("userClick: " + userClick[1]);
                    $(skuContainer).find('.skuer-laminate-radio').find('input[type=radio]').attr('checked', false);
                    if ($(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').parent().hasClass(classes.ghoster)) {
                        laminateId = $(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');    
                        userClick[1] = 0;
                    } else {
                        $(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').attr('checked',true);
                        laminateId = userClick[1];
                        console.log('laminateId: ' + laminateId + ' [' + $(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').parent().find('label').text() +']');
                    }
                } else {
                    //If not, set first option...
                    laminateId = $(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');
                }
            }
        }
        this.setupMountings(false);
        return this;
    };

    Skuer.prototype.setupMountings = function(wasUserClicked) {
        if (!nullMountings) {
            $(skuContainer).find('.skuer-mounting-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId))  {
                    $(skuContainer).find('.skuer-mounting-radio').find("[value='" + skuSon.skus[m].mountingHoleArrangementId + "']").parent().removeClass(classes.ghoster);
                }
            }
            if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(2);
                console.log('making mounting inline');
            } else if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(2);
                console.log('making mounting option');
            } else if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(2);
                console.log('making mounting hidden');
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[2] > 0) {
                    console.log("userClick: " + userClick[2]);
                    $(skuContainer).find('.skuer-mounting-radio').find('input[type=radio]').attr('checked', false);
                    if ($(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[2] + ']').parent().hasClass(classes.ghoster)) {
                        mountingId = $(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');    
                        userClick[2] = 0;
                    } else {
                        $(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[2] + ']').attr('checked',true);
                        mountingId = userClick[2];
                        console.log('mountingId: ' + mountingId + ' [' + $(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[3] + ']').parent().find('label').text() +']');
                    }
                } else {
                    //If not, set first option...
                    mountingId = $(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');
                }
            }
        }
        this.setupPackagings(false);
        return this;
    };

    Skuer.prototype.setupPackagings = function(wasUserClicked) {
        console.log('entering setupPackagings()...' + Date());
        this.logSkuer();        
        var pkCount = 0,
            pkText = "",
            pkUnique = true,
            pkArray = [],
            pkUnits = [],
            pkIncArray = [];
        for (var m = 0; m < numSkus; m++) {
            if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId))  {
            //if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId))  {
                innerUnits = skuSon.skus[m].innerUnits;
                packageInclusionNote = skuSon.skus[m].packageInclusionNote;
                packageInclusionNoteLabel = packageInclusionNote;
                console.log("inclusion label:");
                console.log(packageInclusionNoteLabel);
                if (packageInclusionNote !== null) {packageInclusionNote = packageInclusionNote.replace(/["']/g, "");}
                packageName = skuSon.skus[m].packageName;
                packageNamePlural = skuSon.skus[m].packagePlural;
                dedicatedPackageCount = skuSon.skus[m].dedicatedPackageCount;
                packagingId = skuSon.skus[m].packagingId;
                console.log('InnerUnits: ' + skuSon.skus[m].innerUnits + " | packagingId: " + skuSon.skus[m].packagingId + " | materialId" + skuSon.skus[m].materialId + " | incNote:" + skuSon.skus[m].packageInclusionNote + " | dedCount:" + skuSon.skus[m].dedicatedPackageCount);
                pkText = this.buildPackaging();
                pkArray.push(pkText);
                pkIncArray.push(this.getInclusionNote());
                pkUnits.push(innerUnits);
                pkCount++;
            }
        }
        //See if we should have radio buttons or single line of text...
        if (pkArray.length > 1) {
            for (var m = 1; m < pkArray.length; m++) {
                if (pkArray[0] != pkArray[m]) {
                    pkUnique = false;
                    break;
                }
            }
        }

        if (pkCount == 1 || pkUnique) {
            this.makeAreaInline(3);
            $(skuContainer).find('.packaging-text').text(pkText);
        } else if (pkCount > 1) {
            this.makeAreaOption(3);
            $(skuContainer).find('.product-packaging-list').empty();
            for (var m = 0; m < pkArray.length; m++) {
                if (m === 0) {
                    if (pkUnits[m] > 1) {
                        //Go by innerUnits...
                        $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradio' + m + '" value="'+pkUnits[m]+'" type="radio" name="radiopackaging" checked><label for="packagingradio' + m + '">'+pkArray[m]+'</label></li>');
                        innerUnits = pkUnits[m];
                    } else {
                        //Go by Inclusion Note...
                        $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradio' + m + '" value="'+pkIncArray[m]+'" type="radio" name="radiopackaging" checked><label for="packagingradio' + m + '">'+pkArray[m]+'</label></li>');
                        packageInclusionNote = pkIncArray[m];
                    }
                } else {
                    if (pkUnits[m] > 1) {
                        //Go by innerUnits...
                        $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradio' + m + '" value="'+pkUnits[m]+'" type="radio" name="radiopackaging"><label for="packagingradio' + m + '">'+pkArray[m]+'</label></li>');
                    } else {
                        //Go by Inclusion Note...
                        $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradio' + m + '" value="'+pkIncArray[m]+'" type="radio" name="radiopackaging"><label for="packagingradio' + m + '">'+pkArray[m]+'</label></li>');
                        packageInclusionNote = pkIncArray[m];
                    }
                }
            }
        }
        //Do Final SKU Setup...
        this.setSkuData();
        this.setupPricing();
        return this;
    };

    Skuer.prototype.buildPackaging = function() {
        var packageText = "";
        if (packagingId === null) {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNoteLabel === "") {
                        packageText = "Sold Individually. Ships in " + dedicatedPackageCount + " separate packages.";
                    } else {
                        packageText = "Sold Individually - " + packageInclusionNoteLabel + " Ships in " + dedicatedPackageCount + " separate packages.";
                    }
                } else {
                    if (packageInclusionNoteLabel === "") {
                        packageText = "Sold Individually. Ships in its own package.";
                    } else {
                        packageText = "Sold Individually - " + packageInclusionNoteLabel + " Ships in its own package.";
                    }
                }
            } else {
                if (packageInclusionNoteLabel === "") {
                    packageText = "Sold Individually";
                } else {
                    packageText = "Sold Individually - " + packageInclusionNoteLabel;
                }
            }
        } else {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNoteLabel === "") {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + ". Ships in " + dedicatedPackageCount + " separate packages.";
                    } else {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNoteLabel + " Ships in " + dedicatedPackageCount + " separate packages.";
                    }
                } else {
                    if (packageInclusionNoteLabel === "") {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + ". Ships in its own package.";
                    } else {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNoteLabel + " Ships in its own package.";
                    }
                }
            } else {
                if (packageInclusionNoteLabel === "") {
                    packageText = "Sold in " + packageNamePlural + " of " + innerUnits;
                } else {
                    packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNoteLabel;
                }
            }
        }
        console.log(packageText);
        return packageText;
    };

    Skuer.prototype.getInclusionNote = function() {
        var packageText = "";
        if (packagingId === null) {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = null;
                } else {
                    packageText = packageInclusionNote;
                }
            }
        } else {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = null;
                } else {
                    packageText = packageInclusionNote;
                }
            }
        }
        console.log(packageText);
        if (packageText !== null) {packageText = packageText.replace(/["']/g, "");}
        return packageText;
    };

    Skuer.prototype.setupPackagingSelect = function() {
        this.setSkuData();
        this.setupPricing();
    };

    Skuer.prototype.setupSkuerBySkuCode = function(_skuCode) {
        //Capture skuCode Info...
        var _sizeId = null,
            _materialId = null,
            _laminateId = null,
            _mountingId = null,
            _packagingId = null,
            _innerUnits = 1;
        for (var m = 0; m < numSkus; m++) {
            if (skuSon.skus[m].skuCode == _skuCode) {
                _sizeId = skuSon.skus[m].sizeId;
                sizeId = _sizeId;
                _materialId = skuSon.skus[m].materialId;
                materialId = _materialId;
                _laminateId = skuSon.skus[m].laminateId;
                laminateId = _laminateId;
                _mountingId = skuSon.skus[m].mountingId;
                mountingId = _mountingId;
                _packagingId = skuSon.skus[m].packagingId;
                packagingId = _packagingId;
                _innerUnits = skuSon.skus[m].innerUnits;
                innerUnits = _innerUnits;
                userClick[0] = materialId;
                userClick[1] = laminateId;
                userClick[2] = mountingId;
                break;
            }
        }        
        //Are we inline or vertical for sizes?
        if ($(skuContainer).find('.product-size-selector').length > 0) {
            //Inline - Remove Selectors and Select Size of SkuCode...
            $(skuContainer).find('.psitem').removeClass('selsize');
            $(skuContainer).find('.psitem').parent().removeClass('liselsize');
            $(skuContainer).find('input[value="' + _sizeId + '"]').next('label').addClass('selsize').parent().addClass('liselsize');
            $(skuContainer).find('input[value="' + _sizeId + '"]').prop('checked', true);
            this.setupMaterials();
            window.scrollTo(0, 0);
            $.fancybox.close();
        } else {
            //Stacked (vertical list)
            $(skuContainer).find('input[value="' + _sizeId + '"]').prop('checked', true);
            this.setupMaterials();
            window.scrollTo(0, 0);
            $.fancybox.close();
        }
    };

    Skuer.prototype.getSkuData = function(){
        isCustom = skuSon.custom;
        cpiValue = skuSon.cpi_value;
        productID = skuSon.product_id;
        var tempIncNote = "";
        for (var m = 0; m < numSkus; m++) {
            if (innerUnits > 1) {
                tempIncNote = skuSon.skus[m].packageInclusionNote;
                if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
                if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId && skuSon.skus[m].packagingId == packagingId && skuSon.skus[m].innerUnits == innerUnits))  {
                //if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId && skuSon.skus[m].packagingId == packagingId && tempIncNote == packageInclusionNote && skuSon.skus[m].innerUnits == innerUnits))  {
                    console.log('pinS: ' + packageInclusionNote);
                    console.log('pinJ: ' + skuSon.skus[m].packageInclusionNote);
                    innerUnits = skuSon.skus[m].innerUnits;
                    packageInclusionNote = skuSon.skus[m].packageInclusionNote;
                    packageInclusionNote = packageInclusionNote.replace(/["']/g, "");
                    packageName = skuSon.skus[m].packageName;
                    complianceIds = skuSon.skus[m].complianceIds;
                    packageNamePlural = skuSon.skus[m].packagePlural;
                    minQty = skuSon.skus[m].pricing[0]['minimumQuantity'];
                    console.log("minQty: " + minQty);
                    dedicatedPackageCount = skuSon.skus[m].dedicatedPackageCount;
                    skuCode = skuSon.skus[m].skuCode;
                    imagePath = skuSon.skus[m].image;
                    productType = skuSon.skus[m].type;
                    inStock = skuSon.skus[m].inStock;
                    onSale = skuSon.skus[m].onSale;
                    inventory = skuSon.skus[m].inventory;
                    freightRequired = skuSon.skus[m].freightRequired;
                    console.log('laminateId from JSON: ' + skuSon.skus[m].laminateId);
                    console.log('laminateId from Skuer: ' + laminateId);
                    //complianceIds = [];

                    //Sanitize SkuSon Data...
                    if (freightRequired === null) { freightRequired = false; }
                    if (inventory === null) { inventory = 0; }
                    if (onSale === null) { onSale = false; }
                    if (inStock === null) { inStock = false; }
                }
            } else {
                tempIncNote = skuSon.skus[m].packageInclusionNote;
                if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
                if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId && skuSon.skus[m].packagingId == packagingId && tempIncNote == packageInclusionNote))  {
                    console.log('pinS: ' + packageInclusionNote);
                    console.log('pinJ: ' + skuSon.skus[m].packageInclusionNote);
                    innerUnits = skuSon.skus[m].innerUnits;
                    // packageInclusionNote = skuSon.skus[m].packageInclusionNote;
                    // packageInclusionNote = packageInclusionNote.replace(/["']/g, "");
                    packageName = skuSon.skus[m].packageName;
                    packageNamePlural = skuSon.skus[m].packagePlural;
                    dedicatedPackageCount = skuSon.skus[m].dedicatedPackageCount;
                    skuCode = skuSon.skus[m].skuCode;
                    complianceIds = skuSon.skus[m].complianceIds;
                    imagePath = skuSon.skus[m].image;
                    minQty = skuSon.skus[m].pricing[0]['minimumQuantity'];
                    console.log("minQty: " + minQty);
                    productType = skuSon.skus[m].type;
                    inStock = skuSon.skus[m].inStock;
                    onSale = skuSon.skus[m].onSale;
                    inventory = skuSon.skus[m].inventory;
                    freightRequired = skuSon.skus[m].freightRequired;
                    console.log('laminateId from JSON: ' + skuSon.skus[m].laminateId);
                    console.log('laminateId from Skuer: ' + laminateId);
                    //complianceIds = [];

                    //Sanitize SkuSon Data...
                    if (freightRequired === null) { freightRequired = false; }
                    if (inventory === null) { inventory = 0; }
                    if (onSale === null) { onSale = false; }
                    if (inStock === null) { inStock = false; }
                }
            }
        }
    };

    Skuer.prototype.setSkuData = function() {
        this.getSkuData();
        this.setStockDate();
        $(skuContainer).find('.product-type-reads').text(productType + " Reads:");
        $(skuContainer).find('.translate-sign-edit').text("Translate " + productType);
        $(skuContainer).find('.tweak-sign-edit').text("Tweak " + productType);
        $(skuContainer).find('.product-sku-number-mini').text(skuCode);

        if (freightRequired) {
            $('.product-special-freight').removeClass(classes.ghoster);
        } else {
            $('.product-special-freight').addClass(classes.ghoster);
        }

        //On Sale...
        if (onSale) {
            console.log('on sale...');
            $(skuContainer).find('.product-under-sku').addClass('on-sale');
            $(skuContainer).find('.product-instock').addClass('on-sale');
            $(skuContainer).find('.product-instock-date').addClass('on-sale');
            $(skuContainer).find('.on-sale-image-overlay').removeClass('ghost');
        } else {
            if ($(skuContainer).find('.product-under-sku').hasClass('on-sale')) {
                $(skuContainer).find('.product-under-sku').removeClass('on-sale');
                $(skuContainer).find('.product-instock').removeClass('on-sale');
                $(skuContainer).find('.on-sale-image-overlay').addClass('ghost');                
            }
        }

        //Check if the entire size was out of stock...
        if (sizeOutofStock) {
            sizeOutofStock = false;
            console.log('size out of stock...');
            this.OutofStockSize();
        } else {
            console.log('size in stock...');
            $(skuContainer).find('.product-image-container').removeClass('out-of-stock');            
            $(skuContainer).find('.sku-area-container:not(:first-of-type)').removeClass('out-of-stock');
            $(skuContainer).find('.product-under-sku').removeClass('out-of-stock');
            $(skuContainer).find('.add-to-cart-button-product').removeClass('red-cart-button').addClass('green-cart-button');
            $(skuContainer).find('.add-to-cart-button-product').attr('value','+ Add to Cart');
            $(skuContainer).find('.add-to-cart-button-product').prop('disabled',false);
            $(skuContainer).find('.product-out-of-stock-button-design').addClass('ghost');
            $(skuContainer).find('.product-buttons-main').removeClass('ghost');            
            $(skuContainer).find('.product-under-sku-quantity input[type=number]').prop('disabled', false);
            $(skuContainer).find('.product-instock').removeClass('out-of-stock');
            $(skuContainer).find('.product-instock-date').removeClass('out-of-stock');
            $(skuContainer).find('.product-instock').text('In Stock.');
            $(skuContainer).find('.product-instock').show();
            $(skuContainer).find('.product-instock-date').show();
        }

        //Check if everything is out of stock...
        var atleastOneinStock = false;
        if (!massStockCheck) {
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].inStock) {
                    atleastOneinStock = true;
                }
            }
            if (!atleastOneinStock) {
                //Entire Product is Out...
                this.OutofStockProduct();
                OutofStockLock = true;
            }
            massStockCheck = true;
        }

        //Check if Overlaminate should be N/A...
        if ($(skuContainer).find('.product-laminate-list').children().length > 0 && laminateId === null) {
            this.makeAreaInline(7);
        }
        //Check if Mounting should be N/A...
        if ($(skuContainer).find('.product-mounting-list').children().length > 0 && mountingId === null) {
            this.makeAreaInline(8);
        }

        //Set Compliances...
        if (complianceIds !== null) {
            $(skuContainer).find('compliance-option').addClass('ghost');
            for (var x = 0; x < Object.keys(complianceIds).length; x++) {
                $(skuContainer).find('li[data-compliance-id="' + complianceIds[Object.keys(complianceIds)[x]] + '"]').removeClass('ghost');
            }
        }
    };

    Skuer.prototype.OutofStockSize = function() {
        $(skuContainer).find('.product-image-container').addClass('out-of-stock');            
        $(skuContainer).find('.sku-area-container:not(:first-of-type)').addClass('out-of-stock');
        $(skuContainer).find('.product-under-sku').addClass('out-of-stock');
        $(skuContainer).find('.add-to-cart-button-product').addClass('red-cart-button').removeClass('green-cart-button');
        $(skuContainer).find('.add-to-cart-button-product').attr('value','Out of Stock');
        $(skuContainer).find('.add-to-cart-button-product').prop('disabled',true);
        $(skuContainer).find('.product-out-of-stock-button-design').removeClass('ghost');
        $(skuContainer).find('.product-buttons-main').addClass('ghost');
        $(skuContainer).find('.product-under-sku-quantity input[type=number]').prop('disabled', true);
        $(skuContainer).find('.sku-material-area').addClass('out-of-stock');            
        $(skuContainer).find('.sku-laminate-area').addClass('out-of-stock');
        $(skuContainer).find('.sku-mounting-area').addClass('out-of-stock');
        $(skuContainer).find('.sku-packaging-area').addClass('out-of-stock');
        $(skuContainer).find('.product-instock').hide();
        $(skuContainer).find('.product-instock-date').hide();        
    };

    Skuer.prototype.OutofStockProduct = function() {
        $(skuContainer).find('.product-sku-holder').addClass('out-of-stock');            
        $(skuContainer).find('.product-image-container').addClass('out-of-stock');            
        $(skuContainer).find('.sku-area-container:not(:first-of-type)').addClass('out-of-stock');
        $(skuContainer).find('.product-under-sku').addClass('out-of-stock');
        $(skuContainer).find('.add-to-cart-button-product').addClass('red-cart-button').removeClass('green-cart-button');
        $(skuContainer).find('.add-to-cart-button-product').attr('value','Out of Stock');
        $(skuContainer).find('.add-to-cart-button-product').prop('disabled',true);
        $(skuContainer).find('.product-out-of-stock-button-design').removeClass('ghost');
        $(skuContainer).find('.product-buttons-main').addClass('ghost');
        $(skuContainer).find('.product-under-sku-quantity input[type=number]').prop('disabled', true);
        $(skuContainer).find('.sku-material-area').addClass('out-of-stock');            
        $(skuContainer).find('.sku-laminate-area').addClass('out-of-stock');
        $(skuContainer).find('.sku-mounting-area').addClass('out-of-stock');
        $(skuContainer).find('.sku-packaging-area').addClass('out-of-stock');
        $(skuContainer).find('.product-instock').hide();
        $(skuContainer).find('.product-instock-date').hide();        
    };

    Skuer.prototype.addtoCart = function() {
        //Check MinQty is OK...
        if (user_qty < minQty) {
            //Show Popup...
            Tipped.show('.user-qty-box');
            console.log('should show popup');
        } else {

        //Grab form target...
        var fTarget = $(skuContainer).find('.product-page-form').attr('target');
        console.log('sending add to cart request: qty:' + user_qty + ' sku_code: ' + skuCode + ' id: ' + productID + ' target: ' + fTarget + ' Type: ' + (isCustom ? 'flash' : 'stock') + ' cpi: ' + cpiValue);

        var pType =
            $.ajax({
                url: fTarget,
                data: {
                    'sku_code': skuCode,
                    'id': productID,
                    'type': (isCustom ? 'flash' : 'stock'),
                    'qty': user_qty,
                    'cpi': cpiValue,
                    'product_state_url': productStateURL
                }
            }).done(function(data) {
                console.log('add to cart response: ');
                console.log(data);
                if (data.success) {
                    $('.minicart-price').text(formatPrice(data.subtotal, '$'));
                    $('.minicart-quantity').text(data.cartcount);
                    //Add to cart dialog setup
                    console.log(data.attrs[1]['val']);
                    $('.add-cart-size-val').text(data.attrs[0]['val']);
                    $('.add-cart-material-val').text(data.attrs[1]['val']);
                    $('.product-add-cart-blue-totals-top').text(data.cartcount + ' Items');
                    $('.product-add-cart-blue-totals-bottom').text('Subtotal: ' + formatPrice(data.subtotal, '$'));
                    $('.add-cart-qty-val').text(user_qty);
                    $('.add-cart-price-val').text(formatPrice((currentPrice * user_qty), '$'));
                    $.fancybox({
                        href: '#add-to-cart-dialog',
                        afterShow: function () {
                        },
                        beforeClose: function () {
                         
                        }
                    });
                }
            });
        }
    };

    Skuer.prototype.logSkuer = function() {
        console.log("SizeID: " + sizeId);
        console.log("MaterialID: " + materialId);
        console.log("LaminateID: " + laminateId);
        console.log("MountingID: " + mountingId);
    };

    Skuer.prototype.sizeChange = function(sizeControl) {
        //Handle visual change of element selection as well as handle Skuer size selection
        //Start Visual Change
        $(skuContainer).find(selectors.sizeActionButton).removeClass(classes.sizeControlSelected);
        $(skuContainer).find(".product-size-list li").removeClass(classes.sizeControlSelectedArrow);
        if ($(skuContainer).find(sizeControl).hasClass(classes.sizeControlSelected) === false) {
            $(skuContainer).find(sizeControl).addClass(classes.sizeControlSelected);
            $(skuContainer).find(sizeControl).parent().addClass(classes.sizeControlSelectedArrow);
            $(skuContainer).find(selectors.sizeControlList).css("background-color","c8dae5");
            $(skuContainer).find(sizeControl).parent().prev("li").find(".psl").css("background-color","f0f5f9");
            $(skuContainer).find(sizeControl).parent().next("li").find(".psl").css("background-color","f0f5f9");
        }
        //Start Skuer Size Selection
        sizeId = $(skuContainer).find(sizeControl).prev('input[type=radio]').attr('value');
        this.setupMaterials(false);
        return this;
    };


    Skuer.prototype.shouldHideArea = function(_areaId) { // 0:material, 1:laminate, 2:mounting
        for (var m = 0; m < numSkus; m++) {
        switch (_areaId) {
            case 0:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId !== null) {
                    $(skuContainer).find('.sku-material-area').removeClass('ghost');
                    return false;
                }
                break;
            case 1:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId !== null) {
                    $(skuContainer).find('.sku-laminate-area').removeClass('ghost');
                    return false;
                }
                break;
            case 2:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId !== null) {
                    $(skuContainer).find('.sku-mounting-area').removeClass('ghost');
                    return false;
                }
                break;
        }
        }
        switch (_areaId) {
            case 0:
            $(skuContainer).find('.sku-material-area').addClass('ghost');
            break;
            case 1:
            $(skuContainer).find('.sku-laminate-area').addClass('ghost');
            break;
            case 2:
            $(skuContainer).find('.sku-mounting-area').addClass('ghost');
            break;
        }
        return true;
    };

    Skuer.prototype.makeAreaInline = function(_areaId) { // 0:material, 1:laminate, 2:mounting, 3:packaging, 6:materialN/A, 7:laminateN/A, 8:mountingN/A
        switch (_areaId) {
            case 0:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').addClass(classes.ghoster);
                $(skuContainer).find('.material-text').text($('.skuer-material-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-material-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 1:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').addClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').text($('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 2:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').addClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').text($('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 3:
                $(skuContainer).find('.sku-packaging-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-packaging-list').addClass(classes.ghoster);
                //$(skuContainer).find('.packaging-text').text($('.skuer-packaging-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-packaging-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 6:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').addClass(classes.ghoster);
                $(skuContainer).find('.material-text').text('N/A');
                $(skuContainer).find('.sku-material-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 7:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').addClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').text('N/A');
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 8:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').addClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').text('N/A');
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
        }
    };

    Skuer.prototype.makeAreaOption = function(_areaId) { // 0:material, 1:laminate, 2:mounting, 3:packaging
        switch (_areaId) {
            case 0:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').removeClass(classes.ghoster);
                $(skuContainer).find('.material-text').empty();
                $(skuContainer).find('.sku-material-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 1:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').removeClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').empty();
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 2:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').removeClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').empty();
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 3:
                $(skuContainer).find('.sku-packaging-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-packaging-list').removeClass(classes.ghoster);
                $(skuContainer).find('.packaging-text').empty();
                $(skuContainer).find('.sku-packaging-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
        }
    };

    Skuer.prototype.setSku = function() {
        this.getSkuDetails();
        $(skuContainer).find(selectors.skuCode).text(skuCode);
        $(skuContainer).find(".product-type-reads").text(productType + " Reads");
        $(skuContainer).find(".translate-sign-edit").text('Translate ' + productType);
        $(skuContainer).find(".tweak-sign-edit").text('Tweak ' + productType);
        
        //this.setInStock(inStock);
        this.setStockDate();
        if (freightRequired) {
            $('.product-special-freight').removeClass(classes.ghoster);
        } else {
            $('.product-special-freight').addClass(classes.ghoster);
        }
        return this;
    };

    Skuer.prototype.initSku = function() {
        sizeId = $(skuContainer).find('input[name=radiosize]:checked').eq(0).attr('value');
        materialId = $(skuContainer).find('input[name=radiomaterial]:checked').eq(0).attr('value');
        laminateId = $(skuContainer).find('input[name=radiolaminate]:checked').eq(0).attr('value');
        mountingId = $(skuContainer).find('input[name=radiomounting]:checked').eq(0).attr('value');
        packagingId = $(skuContainer).find('input[name=radiopackaging]:checked').eq(0).attr('value');
        this.setSku();
        //console.log("Selected Material ID: " + $('input[name=radiomaterial]:checked').attr('data-material-id'));
        return this;
    };

    Skuer.prototype.setupPricing = function() {
        var currentQty = 1,
            currentPrice = 0.00,
            finalPrice = 0.00;        
        //Pricing Text (Qty + Price Each) - Tiered or Single Pricing?
        pricing = this.grabPricing();
        // console.log(pricing);
        if (pricing.length > 1) {
            //tier
            $(skuContainer).find('.sku-area-container:last-of-type').removeClass('single-price-tier');
            $(skuContainer).find('.sku-area-container:nth-last-child(2)').removeClass('single-price-tier-nobg');

            $(skuContainer).find('.product-price-content-per').empty();
            $(skuContainer).find('.product-price-content').addClass(classes.ghoster);
            $(skuContainer).find('.product-price-content-per').addClass(classes.ghoster);
            $(skuContainer).find('.product-pricingselect .product-pricingnode').addClass(classes.ghoster);
            for (var p = 0; p < pricing.length; p++) {
                $(skuContainer).find('.product-pricingselect .product-pricingnode').eq(p).removeClass(classes.ghoster);
                $(skuContainer).find('.product-price-content').eq(p).html(formatPrice(pricing[p].price, '$'));
                $(skuContainer).find('.product-price-content').removeClass(classes.ghoster);
                if (innerUnits > 1) {
                    $(skuContainer).find('.product-price-content-per').eq(p).html(formatPrice((pricing[p].price / innerUnits), '$') + "/" + productType + ")");
                    $(skuContainer).find('.product-price-content-per').removeClass(classes.ghoster);
                }
                if (p == pricing.length - 1) {
                    $(skuContainer).find('.product-pricingselect .product-pricingnode .product-pricingnodetitle').eq(p).html(pricing[p].minimumQuantity + " +");
                } else {
                    $(skuContainer).find('.product-pricingselect .product-pricingnode .product-pricingnodetitle').eq(p).html(pricing[p].minimumQuantity + " - " + (pricing[p+1].minimumQuantity - 1));
                }
            }
        } else {
            //single
            $(skuContainer).find('.sku-area-container:last-of-type').addClass('single-price-tier');
            $(skuContainer).find('.sku-area-container:nth-last-child(2)').addClass('single-price-tier-nobg');
        }
        //Pricing (Total Price)
        currentQty = $(skuContainer).find('.product-under-sku-quantity input').attr('value');
        for (var b = 0; b < pricing.length; b++) {
            if (b+1 < pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity && currentQty < pricing[b+1].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$'));
                }
            }
            if (b+1 == pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$' ));
                }
            }
        }
        return this;
    };


    Skuer.prototype.grabPricing = function() {
        var pricing = [], tempIncNote;
        for (var p = 0; p < numSkus; p++) {
            if (innerUnits > 1) {
                //innerUnits...
                tempIncNote = skuSon.skus[p].packageInclusionNote;
                if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
                if (skuSon.skus[p].sizeId == sizeId && skuSon.skus[p].materialId == materialId && skuSon.skus[p].laminateId == laminateId && skuSon.skus[p].mountingHoleArrangementId == mountingId && skuSon.skus[p].packagingId == packagingId && skuSon.skus[p].innerUnits == innerUnits) {
                //if (skuSon.skus[p].sizeId == sizeId && skuSon.skus[p].materialId == materialId && skuSon.skus[p].laminateId == laminateId && skuSon.skus[p].mountingHoleArrangementId == mountingId && skuSon.skus[p].packagingId == packagingId && skuSon.skus[p].innerUnits == innerUnits && tempIncNote == packageInclusionNote) {
                    pricing = skuSon.skus[p].pricing;
                    break;
                }
            } else {
                //Inclusion Note...
                tempIncNote = skuSon.skus[p].packageInclusionNote;
                if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
                if (skuSon.skus[p].sizeId == sizeId && skuSon.skus[p].materialId == materialId && skuSon.skus[p].laminateId == laminateId && skuSon.skus[p].mountingHoleArrangementId == mountingId && skuSon.skus[p].packagingId == packagingId && tempIncNote == packageInclusionNote) {
                    pricing = skuSon.skus[p].pricing;
                    break;
                }
            }
        }
        return pricing;
    };

   

    Skuer.prototype.setStockDate = function() {
        if (1) { //if (inStock) {
            $.ajax({
                url: "/process/get_shipping_date.php",
                data: {
                    'sku_code': skuCode
                }
            }).done(function(data) {
                $(skuContainer).find('.product-instock-date').text('Ships on ' + data + '.');
            });
        } else {
            //Set Out of Stock
        }
        return this;
    };


    return Skuer;

})(),

SkuerQB = (function() {
    var selectors = {
        sizeActionButton: ".product-size-list li .pslist",
        sizeControlList: ".psl",
        sizeButtons: ".product-size-list .psitem", 
        sizeControlShell: ".product-size-list",
        skuCode: ".product-sku-number-mini"
    },
        classes = {
        sizeControlSelected: "selsize",
        sizeControlSelectedArrow: "liselsize",
        hideSizeSelector: "inline-size-selector",
        ghoster: "ghost"
    },
        
        skuContainer = null,
        tWidth = 0,
        qty = 0,
        cushion = 0,
        maxWidth = 428, //428 //596
        padding = 0,
        sizeId = 0,
        materialId = 0,
        laminateId = 1,
        mountingId = 1,
        packagingId = 1,
        currentQty = 1,
        productID = 0,
        cpiValue = 0,
        isCustom = false,
        currentPrice = 0.00,
        skuSon = "",
        finalPrice = 0.00,
        numSkus = 0,
        skuCode = "",
        imagePath = "",
        productType = "Sign",
        inStock = true,
        onSale = false,
        inventory = 0,
        complianceIds = [],
        pricing = [],
        userClick = [0,0,0,0],
        freightRequired = false,
        innerUnits = 0,
        packageInclusionNote = 0,
        packageName = "",
        packageNamePlural = "",
        dedicatedPackageCount = 0,
        user_qty = 1,
        productStateURL = "",
        sizeOutofStock = false,
        nullSizes = false,
        nullMaterials = false,
        nullLaminates = false,
        nullMountings = false,
        nullPackagings = false;        


    function SkuerQB (e) {
        var $e = $(e).eq(0);

        this.dom = {
            $root: $e
        };

       skuContainer = $e;

    //Skuer Handlers
    $(skuContainer).find(selectors.sizeActionButton).on("click", function(v) {
        this.sizeChange(v.target);
    }.bind(this));
    $(skuContainer).find('input[name=radiomaterialqb]').on("click", function(v) {
        materialId = $(v.target).attr('value');
        userClick[0] = $(v.target).attr('value');
        this.setupLaminates(false);
    }.bind(this));
    $(skuContainer).find('input[name=radiolaminateqb]').on("click", function() {
        laminateId = $(this).attr('value');
        userClick[1] = laminateId;
        this.setupMountings(true);
    }.bind(this));
    $(skuContainer).find('input[name=radiomountingqb]').on("click", function() {
        mountingId = $(this).attr('value');
        userClick[2] = mountingId;
        //this.refreshByMountingHoles(sizeId, materialId, laminateId, mountingId);
    }.bind(this));
    $(skuContainer).find('.product-packaging-list').on("click", 'input[name=radiopackagingqb]', function() {
        //userClick[3] = packagingId;
        console.log('before user clicked packaging: ' + packageInclusionNote);
        packageInclusionNote = $(this).attr('value');
        packageInclusionNote = packageInclusionNote.replace(/["']/g, "");
        console.log('user clicked packaging: ' + packageInclusionNote);
        this.setupPackagingSelect();
    }.bind(this));

    $(skuContainer).find('.add-to-cart-button-product').on('click', function(e) {
        this.addtoCart();
        e.preventDefault();
        return false;
    }.bind(this));

    //Price Table Select Buttons...
    $('.price-table-select-button').on('click', function() {
        this.setupSkuerBySkuCode($(this).attr('data-sku-id'));
    }.bind(this));


    //Price Change

    $(skuContainer).find('.product-under-sku-quantity input').change(function() {
        //Pricing (Total Price)
        currentQty = $(skuContainer).find('.product-under-sku-quantity input').attr('value');
        user_qty = currentQty;
        for (var b = 0; b < pricing.length; b++) {
            if (b+1 < pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity && currentQty < pricing[b+1].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$'));
                }
            }
            if (b+1 == pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$' ));
                }
            }
        }
    
    }.bind(this));

    }


    SkuerQB.prototype.init = function() {
        console.log('container: ');
        console.log(skuContainer);

        //Set Skuer JSON...
        //skuSon = JSON.parse($(skuContainer).attr('data-skuer-json'));        

        if (skuSon) {
            numSkus = skuSon.skus.length;
            productStateURL = skuSon.urlState;
        //try {
            //TESTING ONLY...
            console.log('# of Skus: ' + numSkus);
            // if ($(skuContainer).find('.product-material-list').children().length === 1) {
            //     if ($(skuContainer).find('.product-material-list').find('label').text() == "N/A") {
            //         $(skuContainer).find('.product-material-list').empty();
            //     }
            // }
            console.log('material length: ' + $(skuContainer).find('.product-material-list').children().length);

            //------END TESTING--------

            //SKU Element Null Check...
            if ($(skuContainer).find('.product-size-list').children().length === 0) { nullSizes = true; sizeId = null; }
            if ($(skuContainer).find('.product-material-list').children().length === 0) { nullMaterials = true; materialId = null; this.shouldHideArea(0); }
            if ($(skuContainer).find('.product-laminate-list').children().length === 0) { nullLaminates = true; laminateId = null; this.shouldHideArea(1); }
            if ($(skuContainer).find('.product-mounting-list').children().length === 0) { nullMountings = true; mountingId = null; this.shouldHideArea(2); }
            //if ($(skuContainer).find('.product-packaging-list').length == 0) { nullPackagings = true; packageId = null; }

            console.log("nullSizes: " + nullSizes);
            console.log("nullMaterials: " + nullMaterials);
            console.log("nullLaminates: " + nullLaminates);
            console.log("nullMountings: " + nullMountings);

            //Check if Sizes Exist...
            if (!nullSizes) {
                //Get Selected Size ID...
                sizeId = $(skuContainer).find('input[name=radiosizeqb]:checked').eq(0).attr('value');
                //Calculate Sizes and Adjust or Switch to Radio Builder
                $(skuContainer).find(selectors.sizeButtons).each(function(i, v) {
                    console.log(v);
                    tWidth = tWidth + $(this).width();
                    qty++;
                 });
                cushion = maxWidth - tWidth;
                $(skuContainer).find(selectors.sizeControlShell).removeClass(classes.hideSizeSelector);
                if (cushion < 5) {
                    //Size Control Will Not Fit, Switch To Radio Support...
                    $(skuContainer).find('.psl').parent().remove();
                    $(skuContainer).find('.product-size-selector ul li').addClass('product-radio-holder').addClass('product-radio-size-adj');
                    $(skuContainer).find('.product-size-selector').contents().unwrap();
                    $(skuContainer).find('.size-container').contents().unwrap();
                    $(skuContainer).find('.ps-container').removeClass('ps-container');
                    $(skuContainer).find('.product-size-list').removeClass('product-size-list');
                    $(skuContainer).find('.liselsize').removeClass('liselsize');
                    $(skuContainer).find('.pslist').removeClass('pslist');
                    $(skuContainer).find('.psitem').removeClass('psitem');
                    $(skuContainer).find('.selsize').removeClass('selsize');
                } else {
                    //Size Control Will Fit, Distribute Padding...
                    padding = (cushion / qty);
                    padding = Math.floor((padding / 2));
                    $(skuContainer).find(selectors.sizeButtons).each(function(i, v) {
                        $(this).css("padding-left",padding);
                        $(this).css("padding-right",padding);
                    });  
                }
            }
            //Setup Lower Areas...
            if (!nullMaterials) {
                this.setupMaterials(false);
            } else {
                if (!nullLaminates) {
                    this.setupLaminates(false);
                } else {
                    if (!nullMountings) {
                        this.setupMountings(false);
                    } else {
                        //Everything is null, jump to packaging setup
                        this.setupPackagings(false);
                    }
                }
            }
        //} catch (err) {
        //    console.log('error: ' + err);
        //}
        
        }
        return this;
    };

    SkuerQB.prototype.setupMaterials = function(wasUserClicked) {
        if (!nullMaterials) {
            $(skuContainer).find('.skuer-material-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].sizeId == sizeId) {
                    $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").parent().removeClass(classes.ghoster);
                    if (!skuSon.skus[m].inStock) {
                        //Material Not In Stock...
                        $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").prop('disabled', true).parent().addClass('out-of-stock');
                    } else {
                        $(skuContainer).find('.skuer-material-radio').find("[value='" + skuSon.skus[m].materialId + "']").prop('disabled', false).parent().removeClass('out-of-stock');
                    }
                }
            }
            if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(0);
            } else if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(0);
            } else if ($(skuContainer).find('.skuer-material-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(0);
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[0] > 0) {
                    console.log("userClick: " + userClick[0]);
                    $(skuContainer).find('.skuer-material-radio').find('input[type=radio]').attr('checked', false);
                    if (!$(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().hasClass(classes.ghoster) && $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').prop('disabled') === false) {
                        materialId = $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').attr('checked',true).attr('value');    
                        //userClick[0] = 0;
                    } else {
                        sizeOutofStock = this.setFirstMaterialAvailable();
                        console.log('materialId: ' + materialId + ' [' + $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().find('label').text() +']');
                    }
                } else {
                    sizeOutofStock = this.setFirstMaterialAvailable();
                    console.log('materialId: ' + materialId + ' [' + $(skuContainer).find('.skuer-material-radio').find('input[value=' + userClick[0] + ']').parent().find('label').text() +']');
                }
            }
        }
        this.setupLaminates(false);
    };

    SkuerQB.prototype.setFirstMaterialAvailable = function(){
        //Loop and Set first radio not hidden or disabled...
        //Return Boolean Logic: Is Entire size out if stock?
        //Set first material as selected Id to populate pricing even tho it's out of stock so its not empty
        console.log('entering setFirstMaterialAvailable');
        var mId = 0, mFound = false;
        $(skuContainer).find('.skuer-material-radio').each(function(i, x) {
            if (i === 0) {mId = $(this).find('input[type=radio]').attr('value');}
            if (!$(this).hasClass('ghost') && $(this).find('input[type=radio]').prop('disabled') === false) {
                materialId = $(this).find('input[type=radio]').attr('checked',true).attr('value');
                mFound = true;
                return false;
            }
        });
        if (!mFound) {
            materialId = mId;
            return true;
        }
    };

    SkuerQB.prototype.setupLaminates = function(wasUserClicked) {
        if (!nullLaminates) {
            $(skuContainer).find('.skuer-laminate-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId) {
                    $(skuContainer).find('.skuer-laminate-radio').find("[value='" + skuSon.skus[m].laminateId + "']").parent().removeClass(classes.ghoster);
                }
            }
            if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(1);
                console.log('making laminate inline');
            } else if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(1);
                console.log('making laminate option');
            } else if ($(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(1);
                console.log('making laminate hidden');
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[1] > 0) {
                    console.log("userClick: " + userClick[1]);
                    $(skuContainer).find('.skuer-laminate-radio').find('input[type=radio]').attr('checked', false);
                    if ($(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').parent().hasClass(classes.ghoster)) {
                        laminateId = $(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');    
                        userClick[1] = 0;
                    } else {
                        $(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').attr('checked',true);
                        laminateId = userClick[1];
                        console.log('laminateId: ' + laminateId + ' [' + $(skuContainer).find('.skuer-laminate-radio').find('input[value=' + userClick[1] + ']').parent().find('label').text() +']');
                    }
                } else {
                    //If not, set first option...
                    laminateId = $(skuContainer).find('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');
                }
            }
        }
        this.setupMountings(false);
        return this;
    };

    SkuerQB.prototype.setupMountings = function(wasUserClicked) {
        if (!nullMountings) {
            $(skuContainer).find('.skuer-mounting-radio').addClass(classes.ghoster);
            for (var m = 0; m < numSkus; m++) {
                if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId))  {
                    $(skuContainer).find('.skuer-mounting-radio').find("[value='" + skuSon.skus[m].mountingHoleArrangementId + "']").parent().removeClass(classes.ghoster);
                }
            }
            if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length == 1) {
                this.makeAreaInline(2);
                console.log('making mounting inline');
            } else if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length > 1) {
                this.makeAreaOption(2);
                console.log('making mounting option');
            } else if ($(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').length === 0) {
                this.shouldHideArea(2);
                console.log('making mounting hidden');
            }
            //Set Checked Radio...
            if (wasUserClicked) {

            } else {
                //Check If Previous UserClick Exists...
                if (userClick[2] > 0) {
                    console.log("userClick: " + userClick[2]);
                    $(skuContainer).find('.skuer-mounting-radio').find('input[type=radio]').attr('checked', false);
                    if ($(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[2] + ']').parent().hasClass(classes.ghoster)) {
                        mountingId = $(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');    
                        userClick[2] = 0;
                    } else {
                        $(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[2] + ']').attr('checked',true);
                        mountingId = userClick[2];
                        console.log('mountingId: ' + mountingId + ' [' + $(skuContainer).find('.skuer-mounting-radio').find('input[value=' + userClick[3] + ']').parent().find('label').text() +']');
                    }
                } else {
                    //If not, set first option...
                    mountingId = $(skuContainer).find('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('input[type=radio]').attr('checked',true).attr('value');
                }
            }
        }
        this.setupPackagings();
    };

    SkuerQB.prototype.setupPackagings = function(wasUserClicked) {
        this.logSkuer();        
        var pkCount = 0,
            pkText = "",
            pkUnique = true,
            pkArray = [],
            pkIncArray = [];
        for (var m = 0; m < numSkus; m++) {
            if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId))  {
            //if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId))  {
                innerUnits = skuSon.skus[m].innerUnits;
                packageInclusionNote = skuSon.skus[m].packageInclusionNote;
                if (packageInclusionNote !== null) {packageInclusionNote = packageInclusionNote.replace(/["']/g, "");}
                packageName = skuSon.skus[m].packageName;
                packageNamePlural = skuSon.skus[m].packagePlural;
                dedicatedPackageCount = skuSon.skus[m].dedicatedPackageCount;
                packagingId = skuSon.skus[m].packagingId;
                console.log('InnerUnits: ' + skuSon.skus[m].innerUnits + " | packagingId: " + skuSon.skus[m].packagingId + " | materialId" + skuSon.skus[m].materialId + " | incNote:" + skuSon.skus[m].packageInclusionNote + " | dedCount:" + skuSon.skus[m].dedicatedPackageCount);
                pkText = this.buildPackaging();
                pkArray.push(pkText);
                pkIncArray.push(this.getInclusionNote());
                pkCount++;
            }
        }
        //See if we should have radio buttons or single line of text...
        if (pkArray.length > 1) {
            for (var m = 1; m < pkArray.length; m++) {
                if (pkArray[0] != pkArray[m]) {
                    pkUnique = false;
                    break;
                }
            }
        }

        if (pkCount == 1 || pkUnique) {
            this.makeAreaInline(3);
            $(skuContainer).find('.packaging-text').text(pkText);
        } else if (pkCount > 1) {
            this.makeAreaOption(3);
            $(skuContainer).find('.product-packaging-list').empty();
            for (var m = 0; m < pkArray.length; m++) {
                if (m === 0) {
                    $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradioqb' + m + '" value="'+pkIncArray[m]+'" type="radio" name="radiopackagingqb" checked><label for="packagingradioqb' + m + '">'+pkArray[m]+'</label></li>');
                    packageInclusionNote = pkIncArray[m];
                } else {
                    $(skuContainer).find('.product-packaging-list').append('<li class="product-radio-holder skuer-packaging-radio"><input id="packagingradioqb' + m + '" value="'+pkIncArray[m]+'" type="radio" name="radiopackagingqb"><label for="packagingradioqb' + m + '">'+pkArray[m]+'</label></li>');
                }
            }
        }
        //Do Final SKU Setup...
        this.setSkuData();
        this.setupPricing();
    };

    SkuerQB.prototype.buildPackaging = function() {
        var packageText = "";
        if (packagingId === null) {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = "Sold Individually. Ships in " + dedicatedPackageCount + " separate packages.";
                    } else {
                        packageText = "Sold Individually - " + packageInclusionNote + " Ships in " + dedicatedPackageCount + " separate packages.";
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = "Sold Individually. Ships in its own package.";
                    } else {
                        packageText = "Sold Individually - " + packageInclusionNote + " Ships in its own package.";
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = "Sold Individually";
                } else {
                    packageText = "Sold Individually - " + packageInclusionNote;
                }
            }
        } else {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + ". Ships in " + dedicatedPackageCount + " separate packages.";
                    } else {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNote + " Ships in " + dedicatedPackageCount + " separate packages.";
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + ". Ships in its own package.";
                    } else {
                        packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNote + " Ships in its own package.";
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = "Sold in " + packageNamePlural + " of " + innerUnits;
                } else {
                    packageText = "Sold in " + packageNamePlural + " of " + innerUnits + " - " + packageInclusionNote;
                }
            }
        }
        console.log(packageText);
        return packageText;
    };

    SkuerQB.prototype.getInclusionNote = function() {
        var packageText = "";
        if (packagingId === null) {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = null;
                } else {
                    packageText = packageInclusionNote;
                }
            }
        } else {
            if (dedicatedPackageCount > 0) {
                if (dedicatedPackageCount > 1) {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                } else {
                    if (packageInclusionNote === "") {
                        packageText = null;
                    } else {
                        packageText = packageInclusionNote;
                    }
                }
            } else {
                if (packageInclusionNote === "") {
                    packageText = null;
                } else {
                    packageText = packageInclusionNote;
                }
            }
        }
        console.log(packageText);
        if (packageText !== null) {packageText = packageText.replace(/["']/g, "");}
        return packageText;
    };

    SkuerQB.prototype.setupPackagingSelect = function() {
        this.setSkuData();
        this.setupPricing();
    };

    SkuerQB.prototype.setupSkuerBySkuCode = function(_skuCode) {
        //Capture skuCode Info...
        var _sizeId = null,
            _materialId = null,
            _laminateId = null,
            _mountingId = null,
            _packagingId = null;
        for (var m = 0; m < numSkus; m++) {
            if (skuSon.skus[m].skuCode == _skuCode) {
                _sizeId = skuSon.skus[m].sizeId;
                sizeId = _sizeId;
                _materialId = skuSon.skus[m].materialId;
                materialId = _materialId;
                _laminateId = skuSon.skus[m].laminateId;
                laminateId = _laminateId;
                _mountingId = skuSon.skus[m].mountingId;
                mountingId = _mountingId;
                _packagingId = skuSon.skus[m].packagingId;
                packagingId = _packagingId;
                userClick[0] = materialId;
                userClick[1] = laminateId;
                userClick[2] = mountingId;
                break;
            }
        }        
        //Are we inline or vertical for sizes?
        if ($(skuContainer).find('.product-size-selector').length > 0) {
            //Inline - Remove Selectors and Select Size of SkuCode...
            $(skuContainer).find('.psitem').removeClass('selsize');
            $(skuContainer).find('.psitem').parent().removeClass('liselsize');
            $(skuContainer).find('input[value="' + _sizeId + '"]').next('label').addClass('selsize').parent().addClass('liselsize');
            $(skuContainer).find('input[value="' + _sizeId + '"]').prop('checked', true);
            this.setupMaterials();
            window.scrollTo(0, 0);
            $.fancybox.close();
        } else {
            //Stacked (vertical list)
            $(skuContainer).find('input[value="' + _sizeId + '"]').prop('checked', true);
            this.setupMaterials();
            window.scrollTo(0, 0);
            $.fancybox.close();
        }
    };

    SkuerQB.prototype.getSkuData = function(){
        isCustom = skuSon.custom;
        cpiValue = skuSon.cpi_value;
        productID = skuSon.product_id;
        var tempIncNote = "";
        for (var m = 0; m < numSkus; m++) {
            tempIncNote = skuSon.skus[m].packageInclusionNote;
            if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
            if ((skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId == mountingId && skuSon.skus[m].packagingId == packagingId && tempIncNote == packageInclusionNote))  {
                console.log('pinS: ' + packageInclusionNote);
                console.log('pinJ: ' + skuSon.skus[m].packageInclusionNote);
                innerUnits = skuSon.skus[m].innerUnits;
                // packageInclusionNote = skuSon.skus[m].packageInclusionNote;
                // packageInclusionNote = packageInclusionNote.replace(/["']/g, "");
                packageName = skuSon.skus[m].packageName;
                packageNamePlural = skuSon.skus[m].packagePlural;
                dedicatedPackageCount = skuSon.skus[m].dedicatedPackageCount;
                skuCode = skuSon.skus[m].skuCode;
                imagePath = skuSon.skus[m].image;
                productType = skuSon.skus[m].type;
                inStock = skuSon.skus[m].inStock;
                onSale = skuSon.skus[m].onSale;
                inventory = skuSon.skus[m].inventory;
                freightRequired = skuSon.skus[m].freightRequired;
                console.log('laminateId from JSON: ' + skuSon.skus[m].laminateId);
                console.log('laminateId from Skuer: ' + laminateId);
                //complianceIds = [];

                //Sanitize SkuSon Data...
                if (freightRequired === null) { freightRequired = false; }
                if (inventory === null) { inventory = 0; }
                if (onSale === null) { onSale = false; }
                if (inStock === null) { inStock = false; }
            }
        }
    };

    SkuerQB.prototype.setSkuData = function() {
        this.getSkuData();
        this.setStockDate();
        $(skuContainer).find('.product-type-reads').text(productType + " Reads:");
        $(skuContainer).find('.translate-sign-edit').text("Translate " + productType);
        $(skuContainer).find('.tweak-sign-edit').text("Tweak " + productType);
        $(skuContainer).find('.product-sku-number-mini').text(skuCode);

        if (freightRequired) {
            $('.product-special-freight').removeClass(classes.ghoster);
        } else {
            $('.product-special-freight').addClass(classes.ghoster);
        }

        //On Sale...
        if (onSale) {
            console.log('on sale...');
            $(skuContainer).find('.product-under-sku').addClass('on-sale');
            $(skuContainer).find('.product-instock').addClass('on-sale');
            $(skuContainer).find('.product-instock-date').addClass('on-sale');
            $(skuContainer).find('.on-sale-image-overlay').removeClass('ghost');
        } else {
            if ($(skuContainer).find('.product-under-sku').hasClass('on-sale')) {
                $(skuContainer).find('.product-under-sku').removeClass('on-sale');
                $(skuContainer).find('.product-instock').removeClass('on-sale');
                $(skuContainer).find('.on-sale-image-overlay').addClass('ghost');                
            }
        }

        //Check if the entire size was out of stock...
        if (sizeOutofStock) {
            sizeOutofStock = false;
            console.log('size out of stock...');
            $(skuContainer).find('.product-image-container').addClass('out-of-stock');            
            $(skuContainer).find('.sku-area-container:not(:first-of-type)').addClass('out-of-stock');
            $(skuContainer).find('.product-under-sku').addClass('out-of-stock');
            $(skuContainer).find('.add-to-cart-button-product').addClass('red-cart-button').removeClass('green-cart-button');
            $(skuContainer).find('.add-to-cart-button-product').attr('value','Out of Stock');
            $(skuContainer).find('.add-to-cart-button-product').prop('disabled',true);
            $(skuContainer).find('.product-out-of-stock-button-design').removeClass('ghost');
            $(skuContainer).find('.product-buttons-main').addClass('ghost');
            $(skuContainer).find('.product-under-sku-quantity input[type=number]').prop('disabled', true);
            $(skuContainer).find('.sku-material-area').addClass('out-of-stock');            
            $(skuContainer).find('.sku-laminate-area').addClass('out-of-stock');
            $(skuContainer).find('.sku-mounting-area').addClass('out-of-stock');
            $(skuContainer).find('.sku-packaging-area').addClass('out-of-stock');
            $(skuContainer).find('.product-instock').hide();
            $(skuContainer).find('.product-instock-date').hide();
        } else {
            console.log('size in stock...');
            $(skuContainer).find('.product-image-container').removeClass('out-of-stock');            
            $(skuContainer).find('.sku-area-container:not(:first-of-type)').removeClass('out-of-stock');
            $(skuContainer).find('.product-under-sku').removeClass('out-of-stock');
            $(skuContainer).find('.add-to-cart-button-product').removeClass('red-cart-button').addClass('green-cart-button');
            $(skuContainer).find('.add-to-cart-button-product').attr('value','+ Add to Cart');
            $(skuContainer).find('.add-to-cart-button-product').prop('disabled',false);
            $(skuContainer).find('.product-out-of-stock-button-design').addClass('ghost');
            $(skuContainer).find('.product-buttons-main').removeClass('ghost');            
            $(skuContainer).find('.product-under-sku-quantity input[type=number]').prop('disabled', false);
            $(skuContainer).find('.product-instock').removeClass('out-of-stock');
            $(skuContainer).find('.product-instock-date').removeClass('out-of-stock');
            $(skuContainer).find('.product-instock').text('In Stock.');
            $(skuContainer).find('.product-instock').show();
            $(skuContainer).find('.product-instock-date').show();
        }

        //Check if Overlaminate should be N/A...
        if ($(skuContainer).find('.product-laminate-list').children().length > 0 && laminateId === null) {
            this.makeAreaInline(7);
        }
        //Check if Mounting should be N/A...
        if ($(skuContainer).find('.product-mounting-list').children().length > 0 && mountingId === null) {
            this.makeAreaInline(8);
        }


    };

    SkuerQB.prototype.addtoCart = function() {
        //Grab form target...
        var fTarget = $(skuContainer).find('.product-page-form').attr('target');
        console.log('sending add to cart request: qty:' + user_qty + ' sku_code: ' + skuCode + ' id: ' + productID + ' target: ' + fTarget + ' Type: ' + (isCustom ? 'flash' : 'stock') + ' cpi: ' + cpiValue);

        var pType =
            $.ajax({
                url: fTarget,
                data: {
                    'sku_code': skuCode,
                    'id': productID,
                    'type': (isCustom ? 'flash' : 'stock'),
                    'qty': user_qty,
                    'cpi': cpiValue,
                    'product_state_url': productStateURL
                }
            }).done(function(data) {
                console.log('add to cart response: ' + data);
                $.fancybox({
                    href: '#add-to-cart-dialog',
                    afterShow: function () {
                    },
                    beforeClose: function () {
                     
                    }
                });
            });

    };

    SkuerQB.prototype.logSkuer = function() {
        console.log("SizeID: " + sizeId);
        console.log("MaterialID: " + materialId);
        console.log("LaminateID: " + laminateId);
        console.log("MountingID: " + mountingId);
    };

    SkuerQB.prototype.sizeChange = function(sizeControl) {
        //Handle visual change of element selection as well as handle Skuer size selection
        //Start Visual Change
        $(skuContainer).find(selectors.sizeActionButton).removeClass(classes.sizeControlSelected);
        $(skuContainer).find(".product-size-list li").removeClass(classes.sizeControlSelectedArrow);
        if ($(skuContainer).find(sizeControl).hasClass(classes.sizeControlSelected) === false) {
            $(skuContainer).find(sizeControl).addClass(classes.sizeControlSelected);
            $(skuContainer).find(sizeControl).parent().addClass(classes.sizeControlSelectedArrow);
            $(skuContainer).find(selectors.sizeControlList).css("background-color","c8dae5");
            $(skuContainer).find(sizeControl).parent().prev("li").find(".psl").css("background-color","f0f5f9");
            $(skuContainer).find(sizeControl).parent().next("li").find(".psl").css("background-color","f0f5f9");
        }
        //Start Skuer Size Selection
        sizeId = $(skuContainer).find(sizeControl).prev('input[type=radio]').attr('value');
        this.setupMaterials(false);
        return this;
    };


    SkuerQB.prototype.shouldHideArea = function(_areaId) { // 0:material, 1:laminate, 2:mounting
        for (var m = 0; m < numSkus; m++) {
        switch (_areaId) {
            case 0:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId !== null) {
                    $(skuContainer).find('.sku-material-area').removeClass('ghost');
                    return false;
                }
                break;
            case 1:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId !== null) {
                    $(skuContainer).find('.sku-laminate-area').removeClass('ghost');
                    return false;
                }
                break;
            case 2:
                if (skuSon.skus[m].sizeId == sizeId && skuSon.skus[m].materialId == materialId && skuSon.skus[m].laminateId == laminateId && skuSon.skus[m].mountingHoleArrangementId !== null) {
                    $(skuContainer).find('.sku-mounting-area').removeClass('ghost');
                    return false;
                }
                break;
        }
        }
        switch (_areaId) {
            case 0:
            $(skuContainer).find('.sku-material-area').addClass('ghost');
            break;
            case 1:
            $(skuContainer).find('.sku-laminate-area').addClass('ghost');
            break;
            case 2:
            $(skuContainer).find('.sku-mounting-area').addClass('ghost');
            break;
        }
        return true;
    };

    SkuerQB.prototype.makeAreaInline = function(_areaId) { // 0:material, 1:laminate, 2:mounting, 3:packaging, 6:materialN/A, 7:laminateN/A, 8:mountingN/A
        switch (_areaId) {
            case 0:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').addClass(classes.ghoster);
                $(skuContainer).find('.material-text').text($('.skuer-material-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-material-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 1:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').addClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').text($('.skuer-laminate-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 2:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').addClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').text($('.skuer-mounting-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 3:
                $(skuContainer).find('.sku-packaging-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-packaging-list').addClass(classes.ghoster);
                //$(skuContainer).find('.packaging-text').text($('.skuer-packaging-radio:not(.' + classes.ghoster +')').eq(0).find('label').text());
                $(skuContainer).find('.sku-packaging-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 6:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').addClass(classes.ghoster);
                $(skuContainer).find('.material-text').text('N/A');
                $(skuContainer).find('.sku-material-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 7:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').addClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').text('N/A');
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
            case 8:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').addClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').text('N/A');
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents').addClass('sku-contents-empty').removeClass('sku-contents');
                break;
        }
    };

    SkuerQB.prototype.makeAreaOption = function(_areaId) { // 0:material, 1:laminate, 2:mounting, 3:packaging
        switch (_areaId) {
            case 0:
                $(skuContainer).find('.sku-material-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-material-list').removeClass(classes.ghoster);
                $(skuContainer).find('.material-text').empty();
                $(skuContainer).find('.sku-material-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 1:
                $(skuContainer).find('.sku-laminate-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-laminate-list').removeClass(classes.ghoster);
                $(skuContainer).find('.laminate-text').empty();
                $(skuContainer).find('.sku-laminate-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 2:
                $(skuContainer).find('.sku-mounting-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-mounting-list').removeClass(classes.ghoster);
                $(skuContainer).find('.mounting-text').empty();
                $(skuContainer).find('.sku-mounting-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
            case 3:
                $(skuContainer).find('.sku-packaging-area').removeClass(classes.ghoster);
                $(skuContainer).find('.product-packaging-list').removeClass(classes.ghoster);
                $(skuContainer).find('.packaging-text').empty();
                $(skuContainer).find('.sku-packaging-area').find('.sku-contents-empty').addClass('sku-contents').removeClass('sku-contents-empty');
                break;
        }
    };

    SkuerQB.prototype.setSku = function() {
        this.getSkuDetails();
        $(skuContainer).find(selectors.skuCode).text(skuCode);
        $(skuContainer).find(".product-type-reads").text(productType + " Reads");
        $(skuContainer).find(".translate-sign-edit").text('Translate ' + productType);
        $(skuContainer).find(".tweak-sign-edit").text('Tweak ' + productType);
        
        //this.setInStock(inStock);
        this.setStockDate();
        if (freightRequired) {
            $('.product-special-freight').removeClass(classes.ghoster);
        } else {
            $('.product-special-freight').addClass(classes.ghoster);
        }
        return this;
    };

    SkuerQB.prototype.initSku = function() {
        sizeId = $(skuContainer).find('input[name=radiosizeqb]:checked').eq(0).attr('value');
        materialId = $(skuContainer).find('input[name=radiomaterialqb]:checked').eq(0).attr('value');
        laminateId = $(skuContainer).find('input[name=radiolaminateqb]:checked').eq(0).attr('value');
        mountingId = $(skuContainer).find('input[name=radiomountingqb]:checked').eq(0).attr('value');
        packagingId = $(skuContainer).find('input[name=radiopackagingqb]:checked').eq(0).attr('value');
        this.setSku();
        //console.log("Selected Material ID: " + $('input[name=radiomaterial]:checked').attr('data-material-id'));
        return this;
    };

    SkuerQB.prototype.setupPricing = function() {
        var currentQty = 1,
            currentPrice = 0.00,
            finalPrice = 0.00;        
        //Pricing Text (Qty + Price Each) - Tiered or Single Pricing?
        pricing = this.grabPricing();
        // console.log(pricing);
        if (pricing.length > 1) {
            //tier
            $(skuContainer).find('.sku-area-container:last-of-type').removeClass('single-price-tier');
            $(skuContainer).find('.sku-area-container:nth-last-child(2)').removeClass('single-price-tier-nobg');

            $(skuContainer).find('.product-price-content-per').empty();
            $(skuContainer).find('.product-price-content').addClass(classes.ghoster);
            $(skuContainer).find('.product-price-content-per').addClass(classes.ghoster);
            $(skuContainer).find('.product-pricingselect .product-pricingnode').addClass(classes.ghoster);
            for (var p = 0; p < pricing.length; p++) {
                $(skuContainer).find('.product-pricingselect .product-pricingnode').eq(p).removeClass(classes.ghoster);
                $(skuContainer).find('.product-price-content').eq(p).html(formatPrice(pricing[p].price, '$'));
                $(skuContainer).find('.product-price-content').removeClass(classes.ghoster);
                if (innerUnits > 1) {
                    $(skuContainer).find('.product-price-content-per').eq(p).html(formatPrice((pricing[p].price / innerUnits), '$') + "/" + productType + ")");
                    $(skuContainer).find('.product-price-content-per').removeClass(classes.ghoster);
                }
                if (p == pricing.length - 1) {
                    $(skuContainer).find('.product-pricingselect .product-pricingnode .product-pricingnodetitle').eq(p).html(pricing[p].minimumQuantity + " +");
                } else {
                    $(skuContainer).find('.product-pricingselect .product-pricingnode .product-pricingnodetitle').eq(p).html(pricing[p].minimumQuantity + " - " + (pricing[p+1].minimumQuantity - 1));
                }
            }
        } else {
            //single
            $(skuContainer).find('.sku-area-container:last-of-type').addClass('single-price-tier');
            $(skuContainer).find('.sku-area-container:nth-last-child(2)').addClass('single-price-tier-nobg');
        }
        //Pricing (Total Price)
        currentQty = $(skuContainer).find('.product-under-sku-quantity input').attr('value');
        for (var b = 0; b < pricing.length; b++) {
            if (b+1 < pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity && currentQty < pricing[b+1].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$'));
                }
            }
            if (b+1 == pricing.length) {
                if (currentQty >= pricing[b].minimumQuantity) {
                    currentPrice = pricing[b].price;
                    $(skuContainer).find('.product-under-sku-total-label span').html(formatPrice((currentPrice * currentQty), '$' ));
                }
            }
        }
        return this;
    };


    SkuerQB.prototype.grabPricing = function() {
        var pricing = [], tempIncNote;
        for (var p = 0; p < numSkus; p++) {
            tempIncNote = skuSon.skus[p].packageInclusionNote;
            if (tempIncNote !== null) {tempIncNote = tempIncNote.replace(/["']/g, "");}
            if (skuSon.skus[p].sizeId == sizeId && skuSon.skus[p].materialId == materialId && skuSon.skus[p].laminateId == laminateId && skuSon.skus[p].mountingHoleArrangementId == mountingId && skuSon.skus[p].packagingId == packagingId && tempIncNote == packageInclusionNote) {
                pricing = skuSon.skus[p].pricing;
                break;
            }
        }
        return pricing;
    };

   

    SkuerQB.prototype.setStockDate = function() {
        if (1) { //if (inStock) {
            $.ajax({
                url: "/process/get_shipping_date.php",
                data: {
                    'sku_code': skuCode
                }
            }).done(function(data) {
                $(skuContainer).find('.product-instock-date').text('Ships on ' + data + '.');
            });
        } else {
            //Set Out of Stock
        }
        return this;
    };


    return SkuerQB;

})(),



ss = {

// --------------------------------------------------
// Public variables
// --------------------------------------------------

version: "1.1",
site: new Site(),

// --------------------------------------------------
// Public functions
// --------------------------------------------------

preparePolyfills: function () {

    // Input placeholders.
    if ( Modernizr && !Modernizr.input.placeholder ) {
        $("input[placeholder]").placeholder();
    }

},

prepareCategoryNav: function () {

    var $nav = $("#vertical-list-of-categories li.category");

    if ($nav.length > 0) {

        if ($().hoverIntent) {
            $nav.hoverIntent({
                sensitivity: 4,
                interval: 75,
                timeout: 100,
                over: showGroupingMenu,
                out: hideGroupingMenu
            });
        }

    }

},

prepareGeotargetNav: function () {

    $("div.geo-wrapper").each(function (i, e) {

        var $wrap = $(e),
            $button = $wrap.children("p.button").eq(0),
            $dropdown = $wrap.children("div.dropdown").eq(0),
            $triggers = $button.add($dropdown),
            activeclass = "open";

        $button.on("click", function (event) {
            $wrap.toggleClass(activeclass);
        });

        $(document).on("click", function (event) {
            if (!$triggers.is(event.target) && !$triggers.has(event.target).length) {
                $wrap.removeClass(activeclass);
            }
        });

    });

},

prepareDialogLinks: function () {

    if ($.fancybox) {
        $("#product-links a, div.detailed-products > div.detailed-feature a").fancybox({type:"ajax"});
        $("a.zoom").fancybox();
    }

},

prepareGrids: function () {

    $("div.product-grid-wrap").each(function (i, e) {
        (new Grid(e)).init();
    });

},

prepareTabAreas: function () {

    $("div.tabs").each(function (i, e) {
        (new TabArea(e)).prepareInterface();
    });

},

prepareCart: function () {

    $("#shopping-cart-wrapper").each(function (i, e) {
        (new Cart(e)).init();
    });

},

prepareProducts: function (isolatestreetsigns) {

    /* Note: isolatestreetsigns is only needed for the street sign tool. It allows
     *       post-DOMready calls to this function to not double-initialize the accessories
     *       on street sign pages. Remove this when the street sign tool is not used.
     */

    $(isolatestreetsigns ? "div.viewmaterial tr.product" : "tr.product-addtocart-row").each(function (i, e) {

        (new Product(e)).init();

    });

},

prepareLanguageSelectors: function () {

    var $dialog = $("#language-popup"),
        $trigger = $("#choose-language");

    if ($trigger.length > 0 && $dialog.length > 0) {
        (new LanguageSelector($dialog.get(0), $trigger.get(0))).init();
    }

},

prepareReturnForm: function () {

    $("form.return-form").each(function (i, e) {
        (new ReturnForm(e)).init();
    });

},

prepareAccountForm: function () {

    $("div.account-form").each(function (i, e) {
        (new AccountForm(e)).init();
    });

},

prepareDesignSavers: function () {

    $("a.design-saver").each(function (i, e) {
        (new DesignSaver(e)).init();
    });

},

prepareEmailChangers: function () {

    $("#edit_email_link").each(function (i, e) {
        (new EmailChanger(e)).init();
    });

},

preparePasswordChangers: function () {

    $("#edit_email_password_link").each(function (i, e) {
        (new PasswordChanger(e)).init();
    });

},

prepareNet30Linkers: function () {

    $("#edit_netthirty").each(function (i, e) {
        (new Net30Linker(e)).init();
    });

},

prepareOrderViewers: function () {

    $("#orderhistory").each(function (i, e) {
        (new OrderViewer(e)).init();
    });

},

prepareSavedDesignViewers: function () {

    $("div.saved-design-viewer").each(function (i, e) {
        (new SavedDesignViewer(e)).init();
    });

},

prepareAddressBooks: function () {

    $("#address-list").each(function (i, e) {
        (new AddressBook(e)).init();
    });

},

prepareSearchNav: function () {

    $("div.sidebar-nav.search-results").each(function (i, e) {
        (new SearchNav(e)).init();
    });

},

prepareGuestRegistrationForms: function () {

    $("div.guest-registration-form").each(function (i, e) {
        (new GuestRegistrationForm(e)).init();
    });

},

preparePriceGrids: function () {

    // Instantiate the PriceGrid objects.
    $("table.scrollable-price-grid").each(function (i, e) {
        priceGrids.push(new PriceGrid(e));
    });

},

prepareOrderTrackingPickers: function () {

    $("div.order-tracking-picker").each(function (i, e) {
        (new OrderTrackingPicker(e)).init();
    });

},

prepareUlRecognitionNotes: function () {

    $("div.ul-note").each(function (i, e) {
        (new UlRecognitionNote(e)).init();
    });

},

prepareToppers: function () {

    var
    $popupTrigger = $("#ib-popuplink"),
    $popup = $("#ib-popup");

    if ( $popupTrigger.length && $popup.length ) {
        $popupTrigger.fancybox($popup);
    }

},

prepareRecaptchas: function () {

    if ( Recaptcha ) {

        $(".recaptcha").each(function (i, e) {
            var $e = $(e);
            Recaptcha.create($e.data("recaptchaKey"), $e.attr("id"), {
                theme: "clean"
            });
        });

    }

},

prepareCartSavers: function () {

    $(".save-cart-wrapper").each(function (i, e) {
        (new CartSaver(e)).init();
    });

},

prepareCartLoaders: function () {

    $(".load-cart-wrapper").each(function (i, e) {
        (new CartLoader(e)).init();
    });

},

prepareSavedCartViewers: function () {

    $(".saved-cart-viewer").each(function (i, e) {
        (new SavedCartViewer(e)).init();
    });

},

prepareProductTabTables: function () {
    $(".product-tab-main-container").each(function (i, e) {
        if (i > 0) {
            (new ProductTabTables(e)).init();
        }
    });
    $(".detail-tab-holder > a").click(function(){
     switch($(this).text()) {
         case "Compliance":
         $(".product-tab-selected-pointer").css("left","0");
         $(".tab-pointer-selected").text("Compliance");
         $(".product-tab-main-container").hide();
         $(".product-tabs-compliance-holder").show();
         return false;
         case "Size":
         $(".product-tab-selected-pointer").css("left","144px");
         $(".tab-pointer-selected").text("Size");
         $(".product-tab-main-container").hide();
         $(".product-tabs-sizes-holder").show();
         return false;
         case "Material":
         $(".product-tab-selected-pointer").css("left","280px");
         $(".tab-pointer-selected").text("Material");
         $(".product-tab-main-container").hide();
         $(".product-tabs-materials-holder").show();
         return false;
         case "Printing":
         $(".product-tab-selected-pointer").css("left","418px");
         $(".tab-pointer-selected").text("Printing");
         $(".product-tab-main-container").hide();
         $(".product-tabs-printing-holder").show();
         return false;
         case "Installation":
         $(".product-tab-selected-pointer").css("left","564px");
         $(".tab-pointer-selected").text("Installation");
         $(".product-tab-main-container").hide();
         $(".product-tabs-installation-holder").show();
         return false;
     }
    });
},

prepareProductPopups: function () {
    if ($.fancybox) {
        if ($(".product-image-zoom-hover").length > 0) {
            $(".product-image-zoom-hover").fancybox();
        }
    }
},

prepareSkuer: function () {
    $(".main-skuer").each(function (i, e) {
        (new Skuer(e)).init();
    });    
    
},

prepareQuickBuy: function () {
    // $(".qb-skuer").each(function (i, e) {
    //     (new SkuerQB(e)).init();
    // });    

    $('.quick-view span').on('click', function() {

        $.fancybox({
            'href': '#quick-view-container',
            'autoScale': true,
            'autoDimensions': true
            //'content': $('#quick-view-container').html(),
            // 'width': 990,
            // 'height': 560
            // 'type': 'iframe'

        });  
    });
}


};

// --------------------------------------------------
// Handlebars Configuration
// --------------------------------------------------
if ( Handlebars ) {

    // Prepare the Handlebars.templates object.
    if ( typeof Handlebars.templates === "undefined" || Handlebars.templates === null ) {
        Handlebars.templates = {};
    }

    // Add all templates to the Handlebars.templates object on DOM ready.
    $(function () {
        $("script[type=\"text/x-handlebars-template\"]").each(function (i, e) {
            var $e = $(e), templateId = $e.data('handlebarsTemplateId');
            if ( typeof templateId === "string" && typeof Handlebars.templates[templateId] === "undefined" ) {
                Handlebars.templates[templateId] = Handlebars.compile($e.html());
            }
        });
    });

    // Add all necessary helpers to Handlebars.
    Handlebars.registerHelper("formatPrice", function (price, currencySymbol) {
        return formatPrice(price, typeof currencySymbol === "string" ? currencySymbol : null);
    });

    Handlebars.registerHelper("formatShortDate", function (utcTimestamp) {

        var shortDate, momentDate;

        if ( utcTimestamp && moment ) {
            momentDate = moment.utc(utcTimestamp);
            if ( momentDate.isValid() ) {
                shortDate = momentDate.format("l");
            }
        }

        return shortDate;

    });

    Handlebars.registerHelper("formatDate", function (utcTimestamp) {

        var shortDate, momentDate;

        if ( utcTimestamp && moment ) {
            momentDate = moment.utc(utcTimestamp);
            if ( momentDate.isValid() ) {
                shortDate = momentDate.format("MMMM Do, YYYY");
            }
        }

        return shortDate;

    });

    Handlebars.registerHelper("truncateString", function (string, limit) {

        if ( typeof string !== "undefined" && string !== null ) {

            // Convert the input to a string.
            string = "" + string;

            if ( typeof limit !== "undefined" && limit !== null ) {

                // Convert the input to an integer.
                limit = parseInt(limit, 10);

                // Truncate the string if it's longer than the limit (but never show less than one character of the input string).
                if ( !isNaN(limit) && string.length > limit ) {
                    string = string.substr(0, limit > 2 ? limit - 1 : 1) + "\u2026";
                }

            }

        }

        return string;

    });

    Handlebars.registerHelper("compare", function (lvalue, operator, rvalue, options) {

        var result;

        if ( arguments.length < 3 ) {
            throw new Error("Handlebars Helper 'compare' needs 2 parameters");
        }

        if ( options === undefined ) {
            options = rvalue;
            rvalue = operator;
            operator = "===";
        }

        if ( !compareOperators || !compareOperators[operator] ) {
            throw new Error("Handlebars Helper 'compare' doesn't know the operator " + operator);
        }

        return compareOperators[operator](lvalue, rvalue) ? options.fn(this) : options.inverse(this);

    });

    Handlebars.registerHelper("breaklines", function (text) {
        return new Handlebars.SafeString(Handlebars.Utils.escapeExpression(text).replace(/(\r\n|\n|\r)/gm, "<br>"));
    });



}

// --------------------------------------------------
// Global variables
// --------------------------------------------------

window.ss = ss;

})(this, document);

$(function () {

"use strict";

// --------------------------------------------------
// DOM ready
// --------------------------------------------------

// Grab globals.
var ss = window.ss;

// jQuery AJAX configuration.
$.ajaxSetup({
    cache: false
});

// Fancybox configuration.
$.extend($.fancybox.defaults.helpers, {
    overlay: {
        locked: false
    }
});

// Prepare functionality.
ss.site.init();
ss.preparePolyfills();
ss.prepareCategoryNav();
ss.prepareGeotargetNav();
ss.prepareDialogLinks();
ss.prepareToppers();
ss.prepareGrids();
ss.prepareTabAreas();
ss.prepareCart();
ss.prepareProducts();
ss.prepareReturnForm();
ss.prepareLanguageSelectors();
ss.prepareAccountForm();
ss.prepareDesignSavers();
ss.prepareEmailChangers();
ss.preparePasswordChangers();
ss.prepareNet30Linkers();
ss.prepareOrderViewers();
ss.prepareSavedDesignViewers();
ss.prepareUlRecognitionNotes();
ss.prepareAddressBooks();
ss.prepareSearchNav();
ss.prepareGuestRegistrationForms();
ss.preparePriceGrids();
ss.prepareOrderTrackingPickers();
ss.prepareRecaptchas();
ss.prepareCartSavers();
ss.prepareCartLoaders();
ss.prepareSavedCartViewers();
ss.prepareProductTabTables();
ss.prepareProductPopups();
ss.prepareSkuer();
ss.prepareQuickBuy();
});


