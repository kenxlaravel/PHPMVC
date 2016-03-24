

$(document).ready(function() {

Tipped.create('.reflectivity-help-popup',
    $('.product-material-reflectivity-container').html(),
    { radius: false, skin: 'ss',
    onShow: function(content, element) {
        console.log('tooltip visible with content: ');
        console.log(content);
    },
    afterUpdate: function(content, element) {
        var refAmount = $(element).attr('data-material-reflectivity');
        console.log('reflectivity set to: ' + refAmount);
        $(content).find('.pmt-ref-overlay').removeClass('pmt-reflectivity-selection-1');
        $(content).find('.pmt-ref-overlay').removeClass('pmt-reflectivity-selection-2');
        $(content).find('.pmt-ref-overlay').removeClass('pmt-reflectivity-selection-3');
        $(content).find('.pmt-ref-overlay').removeClass('pmt-reflectivity-selection-4');
        $(content).find('.pmt-ref-overlay').addClass('pmt-reflectivity-selection-' + refAmount);
    }    
 });


// $('.reflectivity-help-popup').bind('mouseenter', function() {
//  });


//test expanding the product page to the right to replace related product area

if ($('.related-product-image').length === 0) {
// if (1) {    
    expandSkuer();
}

function expandSkuer() {
    $('.product-right-side-container').addClass('ghost');
    $('.product-left-side-container').css('width', '946px');
    $('.product-dual-holder').css('width', '946px');
    $('.product-left-side-container').css('background', 'none');
    $('.product-sku-column').css('width', '624px');
    $('.product-sku-holder').css('width', '624px');
    $('.sku-area-container').css('width', '610px');
    $('.product-subtitle-holder').css('width', '624px');
    $('.product-title-holder').css('width', '624px');
    $('.product-title-right').css('margin-left','160px')
    $('.ps-container').css('width', '624px');
    $('.size-container').css('width', '610px');
    $('.product-size-selector').css('width', '578px');
    $('.sku-contents').css('width', '610px');
    $('.product-radio-holder').css('width', '610px');
    $('.sku-title-inline').css('width', '600px');
    $('.mounting-text').css('width', '490px');
    $('.laminate-text').css('width', '490px');
    $('.material-text').css('width', '490px');
    $('.packaging-text').css('width', '490px');
    $('.sku-area-container:not(:last-child)').css('background', 'url("/new_images/product-page/sku-divider-large.png") bottom center no-repeat');
    $('.product-pricingselect').css('width', '610px');
    $('.product-header-area').css('width', '952px');
    $('.product-under-sku-text').css('float', 'right');
    $('.product-under-sku').css('float', 'right');
    $('.product-collection-text').css('width', '554px');
    $('.product-collection-item-full').css('width', '554px');
}


$('.product-add-cart-return-link').on('click', function() {
    $.fancybox.close();
});

// if ($('.product-left-side-container').height() > $('.product-right-side-container').height()) {
//     $('.product-left-side-container').css("background", "#fff url('/new_images/product-page/related-divider.png') top left repeat-y");
//     $('.product-right-side-container').css('background', '');
// } else if ($('.product-left-side-container').height() < $('.product-right-side-container').height()) {
//     $('.product-right-side-container').css("background", "#fff url('/new_images/product-page/related-divider.png') top left repeat-y");
//     $('.product-left-side-container').css('background', '');
// }

    // $.fancybox({
    //     href: '#testone',
    //     autoscale: true,
    //     afterShow: function () {
    //     },
    //     beforeClose: function () {
         
    //     }
    // });    


//Compliance handler for questuion mark...

$('.compliance-help').on('click', function(){
    //Grab Title and Desc of Compliance clicked...
    var title = $(this).parent().attr('data-compliance-name'),
        desc = $(this).parent().attr('data-compliance-desc');
    $.fancybox({
        content: '<div class="popup-container-title-compliance-desc"><div class="popup-container-title">' + title + '</div><div class="popup-container-width">' + desc + '</div></div>',
        title: name,
        width: '500',
        height: '300'
    });    
});




//Show Price table
//Very Important - Since the minimum qty's columns are dynamic, we need to set the colspan's before showing in the dialog!
$('#see-full-price-table').on('click', function() {
var ptColumnCount;
$('.price-chart-tr-counter').each(function() {
ptColumnCount = $(this).find('.pcpt-f').length;
// var ptColumnCount = $('.price-chart-tr-counter').eq(0).find('.pcpt-f').length;
console.log('column count: ' + ptColumnCount);
$(this).prev('tr').find('.pcpt-h').attr('colspan', ptColumnCount);
})
    $.fancybox({
        href: '#price-chart-popup-container',
        autoscale: true,
        afterShow: function () {
        },
        beforeClose: function () {
         
        }
    });    
}); 

    //===========================================================
    //              Shareaholic Loader
    //===========================================================
    switch(window.location.protocol) {
       case 'http:':
       case 'https:':
    //<![CDATA[
      (function() {
        var shr = document.createElement('script');
        shr.setAttribute('data-cfasync', 'false');
        shr.src = '//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js';
        shr.type = 'text/javascript'; shr.async = 'true';
        shr.onload = shr.onreadystatechange = function() {
          var rs = this.readyState;
          if (rs && rs != 'complete' && rs != 'loaded') return;
          var site_id = '45e24a63238205bba64cbd892ff6615b';
          try { Shareaholic.init(site_id); } catch (e) {}
        };
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(shr, s);
      })();
    //]]>
         break;
    }
    //===========================================================


//Tab Table Switcher

$('.ptab-options').on('click', function() {
    var tabArea = "";
    $('.product-tab-title').removeClass('ptab-selected');
    $(this).parent().addClass('ptab-selected');
    tabArea = $(this).attr('data-tab-id');
     switch(tabArea) {
         case "compliance":
         $(".product-tab-main-container").hide();
         $(".product-tabs-compliance-holder").show();
         return false;
         case "size":
         $(".product-tab-main-container").hide();
         $(".product-tabs-sizes-holder").show();
         return false;
         case "material":
         $(".product-tab-main-container").hide();
         $(".product-tabs-materials-holder").show();
         return false;
         case "printing":
         $(".product-tab-main-container").hide();
         $(".product-tabs-printing-holder").show();
         return false;
     }
});



  
    // //===========================================================
    // //               Product Page Tab Switchers....
    // //===========================================================
    // $(".detail-tab-holder > a").click(function(){
    //  switch($(this).text()) {
    //      case "Compliance":
    //      $(".product-tab-selected-pointer").css("left","0");
    //      $(".tab-pointer-selected").text("Compliance");
    //      $(".product-tab-main-container").hide();
    //      $(".product-tabs-compliance-holder").show();
    //      return false;
    //      case "Size":
    //      $(".product-tab-selected-pointer").css("left","144px");
    //      $(".tab-pointer-selected").text("Size");
    //      $(".product-tab-main-container").hide();
    //      $(".product-tabs-sizes-holder").show();
    //      return false;
    //      case "Material":
    //      $(".product-tab-selected-pointer").css("left","280px");
    //      $(".tab-pointer-selected").text("Material");
    //      $(".product-tab-main-container").hide();
    //      $(".product-tabs-materials-holder").show();
    //      return false;
    //      case "Printing":
    //      $(".product-tab-selected-pointer").css("left","418px");
    //      $(".tab-pointer-selected").text("Printing");
    //      $(".product-tab-main-container").hide();
    //      $(".product-tabs-printing-holder").show();
    //      return false;
    //      case "Installation":
    //      $(".product-tab-selected-pointer").css("left","564px");
    //      $(".tab-pointer-selected").text("Installation");
    //      $(".product-tab-main-container").hide();
    //      $(".product-tabs-installation-holder").show();
    //      return false;
    //  }
    // });
    // //===========================================================


    //===========================================================
    //              Jump To product Page Tables
    //===========================================================
    $(".product-learn-more-jumpto-size").click(function(){
            $(".product-tab-selected-pointer").css("left","144px");
            $(".tab-pointer-selected").text("Size");
            $(".product-tab-main-container").hide();
            $(".product-tabs-sizes-holder").show();
    });
    $(".product-learn-more-jumpto-material").click(function(){
            $(".product-tab-selected-pointer").css("left","280px");
            $(".tab-pointer-selected").text("Material");
            $(".product-tab-main-container").hide();
            $(".product-tabs-materials-holder").show();
    });

    $('.product-materials-table td[rowspan]').nextUntil("tr").addClass('table-rowspan-dashed');
    $('.product-size-table td[rowspan]').next().next().next().next().next().nextUntil("tr").addClass('table-rowspan-dashed');


    $(".product-tab-main-container").hide();
    $(".primary-tab").show();
    //  if ($("span[data-tab-id='compliance']").length > 0) {
    //     $(".product-tabs-compliance-holder").show();
    //     $("span[data-tab-id='compliance']").parent().addClass('ptab-selected');
    //     console.log("show compliance");
    // } else if ($("span[data-tab-id='size']").length > 0) {
    //     $(".product-tabs-sizes-holder").show();
    //     $("span[data-tab-id='size']").parent().addClass('ptab-selected');
    //     console.log("show size");
    // } else if ($("span[data-tab-id='material']").length > 0) {
    //     $(".product-tabs-materials-holder").show();
    //     $("span[data-tab-id='material']").parent().addClass('ptab-selected');
    //     console.log("show material");
    // } else if ($("span[data-tab-id='printing']").length > 0) {
    //     $(".product-tabs-printing-holder").show();
    //     $("span[data-tab-id='printing']").parent().addClass('ptab-selected');
    //     console.log("show printing");
    // }
     






    var currentPageCart = 1,
        currentPagePage = 1,
        currentPage = 1,
        pageLeft = 0,
        cartLeft = 0,
        browserType = 0, // 0 = Page, 1 = Cart
        carouselMax = $('.product-carousel-main-page ul li').length,
        carouselMaxCart = $('.product-carousel-main-cart ul li').length,
        numPages = 1,
        numPagesPage = 1,
        numPagesCart = 1,
        numPagesPagePosts = 1,
        numPagesPageHardware = 1,
        numPagesCartPosts = 1,
        numPagesCartHardware = 1,
        pageFilterPosts = $('.product-carousel-main-page ul li[data-accessory-type~="post"]').length,
        pageFilterHardware = $('.product-carousel-main-page ul li[data-accessory-type~="hardware"]').length,
        cartFilterPosts = $('.product-carousel-main-cart ul li[data-accessory-type~="post"]').length,
        cartFilterHardware = $('.product-carousel-main-cart ul li[data-accessory-type~="hardware"]').length,
        selectors = {
            arrow: ".product-carousel-arrow",
            mainlist: ".product-carousel-main-page ul",
            cartlist: ".product-carousel-main-cart ul",
            mainlistitem: ".product-carousel-main-page ul li",
            cartlistitem: ".product-carousel-main-cart ul li",
            pagearrowleft: ".product-carousel-main-page .product-carousel-arrow-left",
            pagearrowright: ".product-carousel-main-page .product-carousel-arrow-right",
            cartarrowleft: ".product-carousel-main-cart .product-carousel-arrow-left",
            cartarrowright: ".product-carousel-main-cart .product-carousel-arrow-right",
            pagecircles: ".page-browser .product-carousel-under-circles-circle",
            cartcircles: ".cart-browser .product-carousel-under-circles-circle",
        },
        classes = {
            ghostitem: "product-carousel-ghost",
            arrowleft: "product-carousel-arrow-left",
            arrowright: "product-carousel-arrow-right",
            currentcircle: "current-page"
        };

    //Initially figure out counts by browserType
    numPagesPage = carouselMax / 5;
    numPagesCart = carouselMaxCart / 4;
    numPagesPagePosts = pageFilterPosts / 5;
    numPagesPageHardware = pageFilterHardware / 5;
    numPagesCartPosts = cartFilterPosts / 4;
    numPagesCartHardware = cartFilterHardware / 4;
    if (numPagesPage % 1 !== 0) numPagesPage = Math.ceil(numPagesPage);
    if (numPagesCart % 1 !== 0) numPagesCart = Math.ceil(numPagesCart);
    if (numPagesCart % 1 !== 0) numPagesCart = Math.ceil(numPagesCart);
    //Build out Page Circles....
    for (var p = 0; p < (numPagesPage - 1); p++) {
        $('.page-browser').append($('.page-browser div').eq(0).clone());
    }
    $('.page-browser div').eq(0).addClass('current-page');
    for (var p = 0; p < (numPagesCart - 1); p++) {
        $('.cart-browser').append($('.cart-browser div').eq(0).clone());
    }
    $('.cart-browser div').eq(0).addClass('current-page');
    

    $('.product-carousel-action').click(function(e) {
        if ($(this).css('opacity') === '1') {
            //Which accessory browser is this?
                var browserListItem = "",
                    browserArrowLeft = "",
                    browserArrowRight = "",
                    itemCount = 5,
                    subtractCount = 6;
                         
            if ($(this).parent().hasClass('main-page')) {
                console.log('page class');
                browserListItem = selectors.mainlistitem;
                browserArrowLeft = selectors.pagearrowleft;
                browserArrowRight = selectors.pagearrowright;
                itemCount = 5;
                subtractCount = 6;
                numPages = carouselMax / 5;
                currentPage = currentPagePage;
                browserType = 0;
                if (numPages % 1 !== 0) numPages = Math.ceil(numPages);
                $(selectors.mainlist).css('width', (numPages * 840));
            } else {
                console.log('cart class');
                browserListItem = selectors.cartlistitem;
                browserArrowLeft = selectors.cartarrowleft;
                browserArrowRight = selectors.cartarrowright;
                itemCount = 4;
                subtractCount = 5;
                numPages = carouselMaxCart / 4;
                currentPage = currentPageCart;
                browserType = 1;                
                if (numPages % 1 !== 0) numPages = Math.ceil(numPages);
                $(selectors.cartlist).css('width', (numPages * 672));
            }
            //Left or Right?
            if ($(this).hasClass(classes.arrowleft)) {
                //Left
                if (browserType === 0) { currentPagePage--; currentPage--; pageLeft += 840; $(selectors.mainlist).css('left', pageLeft); $(selectors.pagecircles).removeClass(classes.currentcircle); $(selectors.pagecircles).eq(currentPage-1).addClass(classes.currentcircle); } else { currentPageCart--; currentPage--; cartLeft += 672; $(selectors.cartlist).css('left', cartLeft); $(selectors.cartcircles).removeClass(classes.currentcircle); $(selectors.cartcircles).eq(currentPage-1).addClass(classes.currentcircle); }
                if (currentPage === 1) $(browserArrowLeft).css('opacity','0.3');
                if ((currentPage < numPages) && $(browserArrowRight).css('opacity') < 1) $(browserArrowRight).css('opacity','1');
            } else if ($(this).hasClass(classes.arrowright)) { 
                //Right
                if (browserType === 0) { currentPagePage++; currentPage++; pageLeft -= 840; $(selectors.mainlist).css('left', pageLeft); $(selectors.pagecircles).removeClass(classes.currentcircle); $(selectors.pagecircles).eq(currentPagePage-1).addClass(classes.currentcircle);  } else { currentPageCart++; currentPage++; cartLeft -= 672; $(selectors.cartlist).css('left', cartLeft); $(selectors.cartcircles).removeClass(classes.currentcircle); $(selectors.cartcircles).eq(currentPage-1).addClass(classes.currentcircle); }
                if (currentPage === numPages) $(browserArrowRight).css('opacity','0.3');
                if ((currentPage > 1) && $(browserArrowLeft).css('opacity') < 1) $(browserArrowLeft).css('opacity','1');
            } else {
                //Page Dot
                var pIndex = $(this).index() + 1,
                    pSteps = 0;
                
                    if (pIndex < currentPage) {
                        pSteps = -(currentPage - pIndex) * 840;
                    } else {
                        pSteps = -(currentPage - pIndex) * 840;
                    }
                    pSteps = -((pIndex * 840) -840);
                    console.log(pSteps);
                    if (browserType === 0) { currentPagePage=pIndex; currentPage=pIndex; pageLeft = pSteps; $(selectors.mainlist).css('left', pageLeft); $(selectors.pagecircles).removeClass(classes.currentcircle); $(selectors.pagecircles).eq(currentPage-1).addClass(classes.currentcircle); } else { currentPageCart--; currentPage--; cartLeft += 672; $(selectors.cartlist).css('left', cartLeft); $(selectors.cartcircles).removeClass(classes.currentcircle); $(selectors.cartcircles).eq(currentPage-1).addClass(classes.currentcircle); }
                    if (currentPage === 1) $(browserArrowLeft).css('opacity','0.3');
                    if ((currentPage < numPages) && $(browserArrowRight).css('opacity') < 1) $(browserArrowRight).css('opacity','1');
                    if (currentPage === numPages) $(browserArrowRight).css('opacity','0.3');
                    if ((currentPage > 1) && $(browserArrowLeft).css('opacity') < 1) $(browserArrowLeft).css('opacity','1');

            }
        }
        e.preventDefault();
        return false;
    });

   

//Carousel Filter Selector...
var carouselFilters = [];
function pushCarouselFilter(filter) {
    if ($.inArray(filter, carouselFilters) === -1) {
        carouselFilters.push(filter);
    }
}
//Loop through all list items in carousel...
$('.carousel-item-page').each(function() {
    var filterTypes = [],
        filters = "";
    filters = $(this).attr('data-accessory-type');
    if (filters.indexOf(',') !== -1) {
        //Split filters apart and store them in the array
        filterTypes = filters.split(',');
        for (var m = 0; m < filterTypes.length; m++) {
            pushCarouselFilter(filterTypes[m]);
        }
    } else {
        if (filters !== "") {
            pushCarouselFilter(filters);
        }
    }
});
//Build Out Selector...
$('.accessory-browser-page').append('<li class="accessory-filter selected-type">All</li>');    
for (var m = 0; m < carouselFilters.length; m++) {
    $('.accessory-browser-page').append('<li class="accessory-filter">' + carouselFilters[m] + '</li>');    
}


//AccessoryBrowser Filters
$('.accessory-filter').click(function(){
    var filterBy = "";
    //Check if Page or Add to cart...
    if ($(this).parent().hasClass('accessory-browser-page')) {
        $('.accessory-browser-page').find('.accessory-filter').removeClass('selected-type');
        filterBy = $(this).text();
        $(this).addClass('selected-type');
        console.log("Filter By: " + filterBy);
        if (filterBy == "All") {
            //Filter All..
            $('.accessory-browser-type-page').find('.carousel-item-page').removeClass('ghost');
        } else {
            //Filter Specific...
            $('.carousel-item-page').each(function() {
                var filterTypes = [],
                    filters = "";
                filters = $(this).attr('data-accessory-type');
                if (filters.indexOf(filterBy) !== -1) {
                    //Show It
                    $(this).removeClass('ghost');
                } else {
                    //Hide It
                    $(this).addClass('ghost');
                }
            });
        }
            pageLeft = 0;
            currentPagePage = 1;
            currentPage = 1;
            $(selectors.mainlist).css('left', pageLeft);
            carouselMax = $('.carousel-item-page:not(.ghost)').length;
            $(selectors.pagearrowleft).css('opacity','0.3');
            $(selectors.pagearrowright).css('opacity','1');
            $(selectors.mainlist).css('width', 10000);

            numPagesPage = carouselMax / 5;
            if (numPagesPage % 1 !== 0) numPagesPage = Math.ceil(numPagesPage);
            //Build out Page Circles....
            $('.product-carousel-under-circles-circle:not(:eq(0))').remove();
            for (var p = 0; p < (numPagesPage - 1); p++) {
                $('.page-browser').append($('.page-browser div').eq(0).clone());
            }
            var browserArrowLeft = selectors.pagearrowleft,
                browserArrowRight = selectors.pagearrowright;
            $('.product-carousel-under-circles-circle').eq(0).addClass('current-page');
            $('.product-carousel-under-circles-circle:not(:eq(0))').removeClass('current-page');

            $(browserArrowLeft).css('opacity','0.3');
            if (numPagesPage == 1) $(browserArrowRight).css('opacity','0.3');
            console.log(numPages);
    }

});



});

