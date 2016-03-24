<div class="product-left-side-container">
    <div class="product-header-area">
        <div class="product-title-left">
            <h2 itemprop="name" class="product-title-holder"><?= $Product->getProductName (); ?></h2>

            <div class="product-subtitle-holder"><?= $Product->getProductSubtitle (); ?></div>
        </div>
        <div class="product-title-right">
            <div class="sharing-links">
                <div class='shareaholic-canvas' data-app='share_buttons' data-app-id='10270057'></div>
            </div>
        </div>
        <div class="product-line-hide"></div>
    </div>

    <div class="product-dual-holder main-skuer">
    <div class="product-image-details-column">
        <div class="product-image-container">
            <div class="on-sale-image-overlay ghost"></div>
            <div class="out-of-stock">OUT OF STOCK</div>
            --
            <?php if( isset($customProduct['custom_image'], $customProduct['custom_product_id']) ):?>
                <div class="product-image-zoom-hover" href="<?= Encoder::html ($Product->getProductImage ("cZoom").$customProduct['custom_image']); ?>"></div>
                <img itemprop="image" src="<?= Encoder::html ($Product->getProductImage ("custom")).$customProduct['custom_image']; ?>" alt="<?= Encoder::html ($Product->getProductName ()); ?>"></div>
            <?php else: ?>
                <div class="product-image-zoom-hover" href="<?= Encoder::html ($Product->getProductImage ("zoom")); ?>"></div>
                <img itemprop="image" src="<?= Encoder::html ($Product->getProductImage ("medium")); ?>" alt="<?= Encoder::html ($Product->getProductName ()); ?>"></div>
            <?php endif; ?>

        <div class="buttons-under-image-container">

            <div class="product-buttons-main">
                <?php if( $Product->getCustom () ): ?>
                    <a class="orange-button edit-design-product" href="?status=edit&design_id=<?= $customProduct['design_id']; ?>&" title="">
                        <span class="design-sign-edit">Edit Design</span></a>
                <?php endif; ?>

                <?php if( count ($translationFams) >= 2 || isset($translationFams[0]['count']) && $translationFams[0]['count'] >= 2 ): ?>
                    <a class="orange-button translate-button-product" href="#product-translate-sign-dialog" title="">
                        <span class="translate-sign-edit">Translate <?= Encoder::html($skuSon['skus'][$defaultSkuId]['type']) ?></span></a>
                <?php endif; ?>

                <?php if( !is_null ($ObjPageProduct->getBuilderTweakToolId()) ): ?>
                    <a class="orange-button" href="<?php echo $page->getUrl ().'?mode=tweak&s=' .ProductStateParameter::encode($productStateParameters); ?>" title="">
                        <span class="tweak-button-product">Tweak <?= Encoder::html ($skuSon['skus'][$defaultSkuId]['type']) ?></span></a>
                <?php endif; ?>

                <?php if ( $Product->getDesignYourOwnCustomLink() ): ?>
                <div class="">
                    <a class="orange-button design-button-product" title="" href="<?php echo $Product->getDesignYourOwnCustomLink(); ?>">
                    <span class="design-sign-edit">Design Your Own Custom <?= Encoder::html ($skuSon['skus'][$defaultSkuId]['type']) ?></span></a>
                </div>
                <?php endif; ?>
<!--                --><?php //if( !is_null ($Product->getBuilderTweakToolId ()) ): ?>
<!--                    <a class="orange-button" href="--><?php //echo $page->getUrl ().'?mode=edit' ?><!--" title="">-->
<!--                        <span class="tweak-sign-edit">Edit --><?//= Encoder::html ($skuSon['skus'][$defaultSkuId]['type']) ?><!--</span></a>-->
<!--                --><?php //endif; ?>
            </div>


        </div>

        <div class="product-info-list">
            <dl class="pdata2">
                <meta itemprop="brand" content="SafetySign.com"/>
                <meta itemprop="manufacturer" content="Brimar"/>
                <dt class="pdata2">Item #:</dt>
                <dd class="pdata2"><?= Encoder::html ($Product->getDisplayNumber ()); ?></dd>

                <!-- DEV NOTES: this dd tag below is limited to 122 characters of text then include 3 trailing periods ..., also it is imperative to use non breaking spaces within the See Full Text link as it is shown here -->
                <?php if( !is_null ($Product->getByLegend ()) ): ?>
                    <dt class="pdata2 product-type-reads"><?= Encoder::html ($skuSon['skus'][$defaultSkuId]['type']) ?>
                        Reads:
                    </dt>
                    <dd class="pdata2">
                        <?= substr (Encoder::html ($Product->getByLegend ()), 0, 122); ?>
                        <?php if( strlen ($Product->getByLegend ()) >= 123 ): ?>
                            ...&nbsp;<a href="">See&nbsp;Full&nbsp;Text</a>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>

                <?php if( !is_null ($Product->getArtworkDescription ()) ): ?>
                    <dt class="pdata2"><?= Encoder::html ($skuSon['skus'][$defaultSkuId]['type']) ?> Design:</dt>
                    <dd class="pdata2"><?= Encoder::html ($Product->getArtworkDescription ()); ?></dd>
                <?php endif; ?>

                <?php if( count ($translationFams) > 1 || count ($translationFams) == 1 && $translationFams[0]['count'] >= 2): ?>
                    <dt class="pdata2">Language:</dt>
                    <dd class="pdata2">
                        <?php foreach ($translationFams as $id => $languages): ?>
                            <?php if( $id === $Product->getId () ): ?>
                                <?= Encoder::html ($languages['name']); ?>,
                            <?php else: ?>
                                <a href="<?= $languages['url']; ?>"><?= Encoder::html ($languages['name']); ?>,</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dd>
                <?php endif; ?>

                <?php if( count ($skuSon['skus'][$defaultSkuId]['complianceIds']) >= 1 ): ?>
                    <dt class="pdata2">Compliance:</dt>
                    <dd class="pdata2">
                        <ul>
                            <?php foreach ($skuSon['skus'][$defaultSkuId]['complianceIds'] as $compliances): ?>
                                <li data-compliance-id="<?= $compliances ?>" data-compliance-name="<?= Encoder::html($productCompliances[$compliances]->getName()); ?>" data-compliance-desc="<?= Encoder::html($productCompliances[$compliances]->getDescription()); ?>" class="compliance-option"><?= $productCompliances[$compliances]->getComplianceGroup()->getName(); ?> <div class="question-mark-gray compliance-help"></div></li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                <?php endif; ?>


                <!-- <dd class="pdata2">IL Public Act<div class="question-mark-gray"></div><br/>MUTCD<div href="#compliance-popup-desc" class="compliance-help question-mark-gray"></div><br/>NY Building Code<div class="question-mark-gray"></div><br/>OSHA 1969<div class="question-mark-gray"></div></dd> -->
<!--                 <dt class="pdata2"></dt>
                <dd class="pdata2">
                    <div class="compliance-line">
                        <a href="#compliance-configurations-product">Other configurations</a> of this product comply with
                        ANSI.
                    </div>
                </dd>
 -->            </dl>

            <?php if( !is_null ($Product->getNote ()) ): ?>
                <div class="product-note-box">
                    <div class="product-note-box-title">Note:</div>
                    <div class="product-note-right">
                        <div class="product-note-box-text"><?= $Product->getNote (); ?></div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            <?php endif; ?>

        </div>
    </div>
    <!--DEV NOTES: Form tag left blank until we figure out how we're linking this to the backend-->
    <form class="product-page-form" target="/process/add_to_cart.php">
        <div class="product-sku-column">
            <div class="product-sku-holder">
                <!-- <div class="product-sku-out-of-stock"></div> -->

                <?php if( isset($productCollections['collections'], $productCollections['collections']['name']) ): ?>
                    <div class="sku-area-container">
                        <span class="sku-title-style"><?= $productCollections['collections']['name']; ?></span>
                        <?= Encoder::html ($productCollections['collections']['products'][$Product->getId ()]['name']); ?>

                        <div class="product-collection-item-full">

                            <div class="product-collection-text">
                                <?= Encoder::html (
                                    $productCollections['collections']['products'][$Product->getId ()]['subtitle']
                                ); ?>
                            </div>
                        </div>

                        <?php if( count ($productCollections['collections']['products']) >= 2 ): ?>
                            <div class="sku-contents paddingbottom4px">
                                <div class="product-sku-top">
                                    <div class="product-sku-leftfull also-available-text">Also Available:</div>
                                </div>
                                <?php foreach ($productCollections['collections']['products'] as $collectionId => $collectionsProducts): ?>
                                    <?php if( $collectionId != $Product->getId () ): ?>
                                        <div class="product-collection-item-full">
                                            <a href="<?= Encoder::html ($collectionsProducts['link']); ?>"><?= Encoder::html ($collectionsProducts['name']); ?></a>
                                            <div class="product-collection-text"><?= Encoder::html ($collectionsProducts['subtitle']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if( count ($uniqueSizes) >= 1 && !empty($uniqueSizes) ): ?>
                    <div class="sku-area-container">
                        <span class="sku-title">Size:</span>
                        <span class="sku-learn-more"><a href="#tab-table" class="product-learn-more-jumpto-size">Learn More</a></span>

                        <div class="sku-contents ps-container">
                            <div class="size-container">
                                <div class="product-size-selector">
                                    <ul class="inline-size-selector product-size-list">

                                        <?php foreach ($uniqueSizes AS $sizeIndex => $sizes): ?>

                                            <?php if( $sizeradio == 0 ): ?>
                                                <meta itemprop="width" content="<?= Encoder::html ($sizes->getWidth ()); ?>"/>
                                                <meta itemprop="height" content="<?= Encoder::html ($sizes->getHeight ()); ?>"/>
                                            <?php endif; ?>

                                            <?= ($sizeradio == 0) ? '<li class="liselsize">' : '<li>'; ?>
                                                <input id="sizeradio<?= $sizeradio; ?>" type="radio" class="skuer-size-radio" name="radiosize" value="<?= Encoder::html ($sizes->getId ()); ?>" <?= ($sizeradio == 0) ? "checked" : ''; ?>>
                                                    <label for="sizeradio<?= $sizeradio; ?>" class="pslist psitem <?= $sizeradio == 0 ? 'selsize' : ''; ?>">
                                                    <?= Encoder::html ($sizes->getName ()); ?></label>
                                                    </li>
                                                    <?php if( $sizeradio < $totalSizes - 1 ): ?>
                                                <li>
                                                    <div class="psl prodsizelined"></div>
                                                </li>
                                            <?php endif; ?>
                                            <?php $sizeradio += 1; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if( !empty($uniqueMaterials) ): ?>

                    <div class="sku-area-container sku-material-area">
                        <span class="sku-learn-more"><a href="#tab-table" class="product-learn-more-jumpto-material">Learn More</a></span>
                        <span class="sku-title-inline"><span class="sku-title-inner">Material:&nbsp;</span>
                            <span class="material-text"></span>
                        </span>

                        <div class="sku-contents ">
                            <ul class="product-material-list">
                                <?php foreach ($uniqueMaterials AS $materialId => $material): ?>
                                    <li class="product-radio-holder skuer-material-radio">
                                        <input id="materialradio<?= $material['id']; ?>" value="<?= $material['id']; ?>" type="radio" name="radiomaterial">
                                        <label for="materialradio<?= $material['id']; ?>"><?= $material['name']; ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if( !empty($uniqueLaminates) ): ?>
                    <div class="sku-area-container sku-laminate-area">
                        <span class="sku-title-inline"><span class="sku-title-inner">Laminate:&nbsp;</span>
                            <span class="laminate-text"></span>
                        </span>
                        <div class="sku-contents">
                            <ul class="product-laminate-list">
                                <?php $i = 0; foreach ($uniqueLaminates as $laminateId => $laminate): ?>
                                    <li class="product-radio-holder skuer-laminate-radio">
                                        <input id="laminateradio<?= $laminateId; ?>" value="<?= $laminateId; ?>" type="radio" name="radiolaminate">
                                        <label for="laminateradio<?= $laminateId; ?>"><?= $laminate['name']; ?></label>
                                    </li>
                                <?php $i++; endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if( !empty($uniqueMountingHoles) ): ?>
                    <div class="sku-area-container sku-mounting-area ghost">
                        <span class="sku-title-inline"><span class="sku-title-inner">Mounting Holes:&nbsp;</span>
                            <span class="mounting-text"></span>
                        </span>
                        <div class="sku-contents">
                            <ul class="product-mounting-list">
                                <?php $i = 0; foreach ($uniqueMountingHoles as $mountingHoleIndex => $mountingHole): ?>
                                    <li class="product-radio-holder skuer-mounting-radio">
                                        <input id="mountingradio<?= $mountingHoleIndex; ?>"
                                               value="<?= $mountingHoleIndex; ?>" type="radio" name="radiomounting">
                                        <label for="mountingradio<?= $mountingHoleIndex; ?>"><?= $mountingHole['name']; ?></label>
                                    </li>
                                <?php $i++; endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if( $Product->getCustom() ): ?>

                    <div class="sku-area-container sku-design-area">
                        <span class="sku-title-inline"><span class="sku-title-inner">Design Options:&nbsp;</span>
                            <span class="design-text"></span>
                        </span>

                        <div class="sku-contents">
                            <ul class="product-design-list">
                              <li class="product-radio-holder"><input id="" value="" type="radio" name="designOption">
                                    <label for="designOption"></label> Adjust My Design For Best Appearance<Br>Our experts will review your sign and make
                                  adjustments to ensure optimum printing</label></li>

                                <li class="product-radio-holder"><input id="" value="" type="radio" name="designOption">
                                    <label for="designOption"></label> A Print My Design As Shown<br>
                                    Your sign will be printed exactly as it appears to the left.</label></li>
                            </ul>
                        </div>
                    </div>


                <?php endif; ?>

                <div class="sku-area-container sku-packaging-area">
                    <span class="sku-title-inline"><span class="sku-title-inner">Packaging:&nbsp;</span><span class="packaging-text"></span></span>
                    <div class="sku-contents">
                        <ul class="product-packaging-list">
                            <li class="product-radio-holder skuer-packaging-radio">
                                <input id="packagingradio<?= $skuIndex; ?>" data-packaging-id="<?= $skuIndex; ?>" type="radio" name="radiopackaging">
                                <label for="packagingradio<?= $skuIndex; ?>"><?= $skuSon['skus'][$defaultSkuId]['packageInclusionNote']; ?></label>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="sku-area-container">
                    <span class="sku-title-pricing">pricing</span>

                    <?php if( count ($skuSon['skus'][$defaultSkuId]['pricing']) > 1 ): ?>
                        <span class="sku-learn-more-price">
                            <a href="#price-chart-popup-container" class="full-price-viewer" id="see-full-price-table">See Complete Pricing Tables</a>
                        </span>
                    <?php endif; ?>
                    <span itemprop="sku" class="product-sku-number-mini"><?= $skuSon['skus'][$defaultSkuId]['skuCode']; ?></span>

                    <div class="sku-contents">
                        <div class="product-pricingselect">
                            <div class="product-pricingdescnode">
                                <div class="product-pricingdesctitle">Quantity</div>
                                <div class="product-pricingdesccontent"><b>Price Each:</b></div>
                            </div>

                            <!-- 								<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-pricingnode">
                                                                <div class="product-pricingnodetitle"></div>
                                                                <div class="product-pricingnodecontent product-price-content"></div>
                                                                <link itemprop="availability" href="http://schema.org/InStock" />
                                                            </div> -->
                            <?php foreach ($skuSon['skus'][$defaultSkuId]['pricing'] as $pricingIndex => $pricingTiers): ?>
                                <div class="product-pricingnode">
                                    <div class="product-pricingnodetitle"><?= Encoder::html (
                                            $pricingTiers['minimumQuantity']
                                        ); ?></div>
                                    <div class="product-pricingnodecontent product-price-content">$<?= Encoder::html (
                                            $pricingTiers['price']
                                        ); ?></div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                        <div class="product-message-area">
                            <b>Need a quote for a large order?</b>&nbsp;Call 800-274-6271 or
                            <div id="cifpFm" style="z-index:100;position:absolute"></div>
                            <div id="scfpFm" style="display:inline"></div>
                            <div id="sdfpFm" style="display:none"></div>
                            <script type="text/javascript">var sefpFm = document.createElement("script");
                                sefpFm.type = "text/javascript";
                                var sefpFms = (location.protocol.indexOf("https") == 0 ? "https" : "http") + "://image.providesupport.com/js/1hx9685lbev3n13jtak217mktq/safe-textlink.js?ps_h=fpFm&ps_t=" + new Date().getTime() + "&online-link-html=chat%20live&offline-link-html=email%20us";
                                setTimeout("sefpFm.src=sefpFms;document.getElementById('sdfpFm').appendChild(sefpFm)", 1)</script>
                            <noscript>
                                <div style="display:inline"><a
                                        href="http://www.providesupport.com?messenger=1hx9685lbev3n13jtak217mktq">Online
                                        Customer Support</a></div>
                            </noscript>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-under-sku">
                <div class="product-under-sku-qty-label">Qty:</div>
                <div class="product-under-sku-quantity">
                    <input type="number" min="1" value="1" class="user-qty-box">
                    <!-- 								<div class="product-quantity-input"><input value="1" /></div>
                                            <div class="product-quantity-arrows">
                                                <div></div><div></div>
                                            </div>
                    -->            </div>

                <div class="product-under-sku-total">
                    <div class="product-under-sku-total-label">Total Price:&nbsp;<span>$0.00</span></div>
                    <?php if( $Product->getIsManufactured() ): ?>
                    <div class="product-under-sku-total-lowprice">Guaranteed Low Price
                        <div class="question-mark-gray"></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="product-under-sku-total-cartbtn"><input type="submit"
                                                                    class="green-cart-button add-to-cart-button-product"
                                                                    value="+ Add to Cart"></div>
            </div>
            <div class="product-under-sku-text">
                <div class="under-sku-right">
                    <span class="product-instock">In Stock.</span>&nbsp;<span class="product-instock-date">Ships <?= $skuSon[$defaultSkuId]['skuLeadTime']; ?></span>
                    <span class="product-special-freight">Special freight arrangements are neccessary. <a href="#">Learn more</a></span>
                </div>
            </div>
            <a name="tab-table"></a>
     </form>
    </div>
  </div>
</div>
