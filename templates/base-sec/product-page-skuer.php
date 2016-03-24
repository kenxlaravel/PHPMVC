<div itemscope itemtype="http://schema.org/Product" class="product-page-wrapper" data-skuer-json='<?= Encoder::html($skuJson); ?>'>

		<!-- quick buy / skuer	-->
			<?php echo Template::generate('base-sec/quick_buy_skuer', array(
					  "page"                   => $page,
					  "totalSizes"             => $totalSizes,
					  "sizeradio"              => $sizeradio,
					  "Product"                => $Product,
					  'customProduct'          => $customProduct,
					  "ObjPageProduct"         => $ObjPageProduct,
					  "skuSon"                 => $skuSon,
					  "uniqueSizes"            => $uniqueSizes,
					  "translationFams"        => $translationFams,
					  "defaultSkuId"           => $defaultSkuId,
					  "productCompliances"     => $productCompliances,
					  "productCollections"     => $productCollections,
					  "uniqueMaterials"        => $uniqueMaterials,
					  "uniqueLaminates"        => $uniqueLaminates,
					  "uniqueMountingHoles"    => $uniqueMountingHoles,
					  "productStateParameters" => $productStateParameters
				)
			);
			?>
		<!-- End Quick Buy / Skuer Product Page	-->

	<?php if( count ($productRecommendations) >= 1): ?>
		<div class="product-right-side-container">

			<!--DEV NOTE: For all related products make sure you use the lowest price for the products displayed-->
			<div class="product-related-title">Related Products</div>
			<?php foreach ($productRecommendations AS $recommended): ?>

				<div itemprop="isSimilarTo" itemscope itemType="http://schema.org/Product" class="related-products-container">
					<div class="related-product-image"><a href="<?= Encoder::html($recommended->getProductPage ()->getUrl ()); ?>">
						<img itemprop="image" src="<?= Encoder::html($recommended->getImages("grid")); ?>" alt="<?= Encoder::html($recommended->getProductName()); ?>"></a>
					</div>
					<div itemprop="name" class="related-product-name">
						<a href="<?php Encoder::html($recommended->getProductPage()->getUrl ()); ?>"><?= Encoder::html($recommended->getProductName()); ?></a>
					</div>
					<div itemprop="sku" class="related-product-name"><?= Encoder::html($recommended->getProductNumber()); ?></div>
						<meta itemprop="description" content="<?= Encoder::html($recommended->getDescription()); ?>" />
						<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<meta itemprop="price" content="10.00" />
						<meta itemprop="priceCurrency" content="USD" />
						<link itemprop="availability" href="http://schema.org/InStock" />
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div style="clear:both;"></div>
	<!-- End Top of Product Page -->

	<div class="product-mid-dash-divider"></div>

	<div class="product-description-mid" itemprop="description">
		<p><?= $Product->getDescription(); ?></p>
	</div>

	<!--Start Tab Area-->
	<?php if( !is_null($Product->getComplianceTabPosition()) || !is_null($Product->getSizeTabPosition ()) || !is_null($Product->getMaterialTabPosition ()) || !is_null($Product->getPrintingTabPosition ()) ): ?>

		<div class="product-master-tab-holder">
			<div class="product-tab-table-main">
				<?php $isPrimary = NULL; foreach( $pageTabs as $tabIds => $tabs ): ?>
					<?php if( $tabIds <= 0 ): $isPrimary[strtolower($tabs['name'])] = " primary-tab"; ?>
						<div class="product-tab-title ptab-selected"><span class="ptab-options" data-tab-id="<?= Encoder::html(strtolower($tabs['name'])); ?>"><?= Encoder::html($tabs['name']); ?></span></div>
					<?php else: $isPrimary[$tabs['name']] = "ghost"; ?>
						<div class="product-tab-title"><span class="ptab-options" data-tab-id="<?= Encoder::html(strtolower($tabs['name'])); ?>"><?= Encoder::html($tabs['name']); ?></span></div>
					<?php endif;?>
				<?php endforeach; ?>
			</div>

		<div style="clear:both"></div>
	<!-- <div class="product-tab-selected-pointer"><div class="tab-pointer-selected">Material</div></div> -->
		<div class="product-tab-selection-bar">

<!-- 			<span class="detail-tab-holder"><a href="#">Compliance</a></span>
			<span class="detail-tab-holder"><a href="#">Size</a></span>
			<span class="detail-tab-holder"><a href="#">Material</a></span>
			<span class="detail-tab-holder"><a href="#">Printing</a></span>
			<span class="detail-tab-holder"><a href="#">Installation</a></span>
 -->		</div>
		<div class="product-tab-line-under"></div>
		<div class="product-tab-line-left"></div>
		<div class="product-tab-line-right"></div>

	</div>
		<div style="clear:both"></div>
		<div class="product-tabs-content-container">

		<div class="product-tabs-materials-holder product-tab-main-container <?= isset($isPrimary['material']) ? $isPrimary['material'] : ""; ?>">

			<?php if( $materialTab['displayMaterials'] ): ?>
				<span class="product-material-headline">Which Material Should I Choose?</span>

				<?php if( !is_null($Product->getMaterialIntro ()) ): ?>
					<span class="product-material-text"><?= $Product->getMaterialIntro(); ?>.<br/><br/></span>
				<?php endif; ?>

					<table class="product-materials-table" cellspacing="0" border="1">
						<thead>
							<tr>
								<th class="pmt-a">Material</th>

								<? if( $materialTab['displayThickness'] ) : ?>
									<th class="pmt-b">Thickness</th>
								<? endif; ?>

								<? if( $materialTab['displayDurability'] ) : ?>
									<th class="pmt-c">Outdoor Durability</th>
								<? endif; ?>

								<? if( $materialTab['displayTemperatureRange'] ) : ?>
									<th class="pmt-d">Service Temp. Range</th>
								<? endif; ?>

								<?php if( $materialTab['displayAvailableSizes'] ): ?>
									<th class="pmt-e">Available<br/>Sizes</th>
								<?php endif; ?>

								<? if( $materialTab['displayOverlaminates'] ) : ?>
									<th class="pmt-f">Overlaminate</th>
								<? endif; ?>

							</tr>
						</thead>

						<tbody>

							<?php foreach ($materialTab['productMaterials'] AS $category) : ?>

								<?php if( $materialTab['displayCategory'] ): ?>
									<tr>
										<th class="material-header-type" colspan="6"> <?= Encoder::html(isset($category["materialCategory"]) ? $category["materialCategory"]->getName() : NULL); ?> </th>
									</tr>
								<?php endif; ?>

								<?php foreach ($category["materialGroups"] AS $materialGroup) : ?>

									<tr>
										<td rowspan="<?= $materialGroup['materialGroupRowSpan']; ?>">
											<span class="product-materials-table-sub-type"><?= Encoder::html($materialGroup['materialGroup']->getName()); ?> </span>
											<span class="product-materials-table-sub-type-desc"><?= Encoder::html($materialGroup['materialGroup']->getDescription()); ?></span>

											<?php foreach ($materialGroup['materials'] as $reflectivity): ?>

												<?php if( !is_null($reflectivity['reflectivity']) && !empty($reflectivity['reflectivity']) ): ?>

													<span class="product-materials-table-sub-type">
														<span class="product-materials-table-reflectivity-label">Reflectivity: </span>

														<!-- This logic can be optimized-->
														<?php if( $reflectivity['reflectivity']->getRating() == 3): ?>

															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>

														<?php elseif( $reflectivity['reflectivity']->getRating() == 2 ): ?>

															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
															<span class="product-materials-table-reflectivity-empty">&nbsp;</span>

														<?php else: ?>

															<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
															<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
															<span class="product-materials-table-reflectivity-empty">&nbsp;</span>

														<?php endif; ?>

														<div data-material-reflectivity="<?= $reflectivity['reflectivity']->getRating(); ?>" class="question-mark-gray product-margin-top-6 reflectivity-help-popup"></div>
													</span>

												<?php break; endif; ?>

											<?php endforeach; ?>
										</td>

									<?php foreach ($materialGroup['materials'] AS $material) : ?>

										<?php if( !empty($material['material']) ): ?>

											<?php if( $materialTab['displayThickness'] ) : ?>
												<?php if( !is_null ($material['material']->getThickness ()) ): ?>
													<td rowspan= <?= $material['materialRowSpan']; ?>><?= Encoder::html(Length::create($material['material']->getThickness(), 'inches')->getDisplayValue($material['material']->getThicknessUnit()->getPluralName(), FALSE)); ?>
												<?php else: ?>
													<td rowspan='<?= $material['materialRowSpan']; ?>'>N/A</td>
												<?php endif; ?>
											<?php endif; ?>

											<?php if( $materialTab['displayDurability'] ): ?>
												<?php if( !is_null($material['material']->getDurability ()) ): ?>
													<td rowspan= <?= $material['materialRowSpan']; ?>> <?= Encoder::html($material['material']->getDurability()); ?> </td>
												<?php else: ?>
													<td rowspan='<?= $material['materialRowSpan']; ?>'>N/A</td>
												<?php endif; ?>
											<?php endif; ?>

											<?php if( $materialTab['displayTemperatureRange'] ): ?>
												<?php if( !is_null ($material['material']->getServiceTemperatureRange ())): ?>
													<td rowspan= <?= $material['materialRowSpan']; ?>> <?= Encoder::html($material['material']->getServiceTemperatureRange()); ?> </td>
												<?php else: ?>
													<td rowspan=<?= $material['materialRowSpan']; ?> >N/A</td>
												<?php endif; ?>
											<?php endif; ?>

										<?php endif; ?>

										<!--Setup the size and laminate group-->
										<?php $i = 0; foreach ($material['sizeLaminateGroups'] AS $index => $sizeLaminateGroup) : ?>
											<?php if( $i > 0 ) : ?>
												<tr >

											<? endif; ?>

											<?php if( count($sizeLaminateGroup['sizes']) >= 1 ): ?>

												<td>
													<?php foreach ($sizeLaminateGroup['sizes'] AS $size) : ?>
														<ul>
															<li><?= $size->getName(); ?></li>
														</ul>
													<?php endforeach; ?>
												</td>

											<?php endif; ?>

											<?php if( $materialTab['displayOverlaminates'] ): ?>
												<td rowspan='1'>

												<?php if( count ($sizeLaminateGroup['laminates']) > 0 ): ?>

													<?php foreach ($sizeLaminateGroup['laminates'] as $laminate) : ?>

														<ul>
															<li> <?= Encoder::html($laminate->getName() ? $laminate->getName() : 'N/A'); ?> </li>
														</ul>

													<?php endforeach; ?>

												<?php else: ?>
													N/A
												<?php endif; ?>

												</td>

											<?php endif; ?>


										<?= ( $i > 0 ) ? "</tr>" : ""; ?>

										<?php $i++; endforeach; ?>
										</tr>
									<?php endforeach; ?>

								<?php endforeach; ?>

							<?php endforeach; ?>
						</tbody>
					</table>

				<?php endif; ?>

			<?php if( !is_null($Product->getMaterialOutro ()) ): ?>
				<div class="material-more-options-container">
					<span class="material-more-options-text"><?= $Product->getMaterialOutro(); ?></span>
					<span class="material-more-options-text">Need this product in a material or size not shown? Ask our sign experts for a free quote on products for your exact needs. <a href="#" class="product-page-link">Contact Us Now</a></span>
				</div>
			<?php endif; ?>

		</div>

		<div style="clear:both"></div>
		<div class="product-tabs-compliance-holder product-tab-main-container <?= $isPrimary['compliance']; ?>">
			<?php if( !is_null($Product->getComplianceFile()) ): ?>
				<span class="product-material-headline">Compliance</span>

				<div class="product-material-text">
					<?php include ($pathComplianceDialog. $Product->getComplianceFile()); ?>
				</div>
				<div style="clear:both"></div>
			<?php endif; ?>

		</div>
		<!--DEV NOTE: This printing tab text should be the last tab html block within this file. Order: Compliance, Size, Material, Installation, Printing-->
		<div class="product-tabs-printing-holder product-tab-main-container <?= $isPrimary; ?>">

			<span class="product-material-text">
				Danger Construction Area Authorized Personnel Only Signs are printed using the latest digital printing technologies and processes. This
				greatly reduces set-up time, allowing SafetySign.com to print your sign quickly without set-up charges or minimums.<br/><br/>

				SafetySign.com prints your text and graphics directly onto the substrate without lamination using UV inks. Your sign text and graphics will
				not peel, unlike signs from companies that laminate graphics over the sign material.<br/><br/>
			</span>

			<?php if( $Product->getShowPrintingInfo() ): ?>

				<div class="product-printing-tab-dual-section-holder">
					<div class="product-printing-tab-dual-section-left"><img src="/new_images/product-page/printing-dummy.png" alt="SafetySign Printing"/></div>
					<div class="product-printing-tab-dual-section-right">
						<div class="product-page-title-text-bold">Printing Methods</div>
						<div class="product-page-content-text-block">
							<?= $Product->getPrintingIntro(); ?>
						</div>
					</div>
				</div>

			<?php endif; ?>

		</div>

		<!-- Size table -->
		<div class="product-tabs-sizes-holder product-tab-main-container <?= isset($isPrimary['size']) ? $isPrimary['size'] : ""; ?>">

			<?php if( $sizeTab['options']['displaySizes'] ): ?>

				<span class="product-material-headline">Which Size Sign Should I Choose?</span>
				<?php if( !empty($sizeTab['intro']) ): ?>
					<span class="product-material-text"><?= Encoder::html($sizeTab['intro']); ?><br/><br/></span>
				<?php endif; ?>

				<table class="product-size-table" cellspacing="0" border="1">
					<thead>
						<tr>
							<th rowspan="2" class="pst-a">Size</th>

							<!-- If we have volume, don't show width/height -->
							<?php if( $sizeTab['options']['displayVolumes'] ): ?>
								<th rowspan="2" class="pst-b">Volume</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayWidths'] && !$sizeTab['options']['displayVolumes'] ): ?>
								<th rowspan="2" class="pst-d">Width</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayDiameter'] ): ?>
								<th>Diameter</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayHeights'] && !$sizeTab['options']['displayVolumes'] ): ?>
								<th rowspan="2" class="pst-e">Height</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayLengths'] ): ?>
								<th rowspan="2">Length</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayDepths'] ): ?>
								<th>Depth</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMinPipeDiameters'] ): ?>
								<th>Min Pipe Diameter</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMaxPipeDiameters'] ): ?>
								<th>Max Pipe Diameter</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displaytextSize'] ): ?>
								<th rowspan="2" class="pst-f">Average<br/>Text Size</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMaxViewing'] ): ?>
								<th class="pst-th-bottom">Max Viewing Distance</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMaterials']): ?>
								<th rowspan="2" class="pst-h">Available Materials</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayCornerRadiuses'] ): ?>
								<th rowspan="2" class="pst-i">Corner Radius</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMountingHoles'] ): ?>
								<th rowspan="2" class="pst-j">Mounting<br/>Holes</th>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayBluePrints'] ): ?>
								<th rowspan="2" class="pst-j">Diagram</th>
							<?php endif; ?>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($sizeTab['groupedSizes'] as $sizeIndex => $size): ?>

							<tr>

							<?php if( $sizeTab['options']['displaySizes'] ): ?>
								<?php if( !is_null($size['size']) ): ?>
									<td rowspan="<?= Encoder::html($size['rowSpan']); ?>" ><?= Encoder::html($size['size']->getName()); ?></td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayVolumes'] ): ?>
								<?php if( !is_null ($size['size']->getVolume ()) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html ($size['size']->getVolume ()); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayWidths'] && !$sizeTab['options']['displayVolumes'] ): ?>
								<?php if( !is_null ($size['size']) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html(Length::create ($size['size']->getWidth (), 'inches')->getDisplayValue(Unit::create($size['size']->getWidthDisplayUnitId())->getPluralName())); ?></td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( !is_null ($size['size']) && $sizeTab['options']['displayDiameter'] ): ?>
								<?php if( !is_null ($size['size']->getDiameter ()) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html (Length::create ($size['size']->getDiameter (), 'inches')->getDisplayValue (Unit::create($size['size']->getDiameterDisplayUnitId())->getPluralName())); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayHeights'] && !$sizeTab['options']['displayVolumes'] ): ?>
								<?php if( !is_null ($size['size']) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html (Length::create ($size['size']->getHeight (), 'inches')->getDisplayValue (Unit::create($size['size']->getHeightDisplayUnitId())->getPluralName())); ?></td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayLengths'] ): ?>
								<?php if( !is_null ($size['size']) && !is_null ($size['size']->getLength ()) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">
										<?= Encoder::html (Length::create ($size['size']->getLength (), 'inches')->getDisplayValue (Unit::create ($size['size']->getLengthDisplayUnitId ())->getPluralName ())); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( !is_null ($size['size']) && $sizeTab['options']['displayDepths'] ): ?>
								<?php if( !is_null($size['size']->getDepth()) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html (Length::create ($size['size']->getDepth (), 'inches')->getDisplayValue (Unit::create($size['size']->getDepthDisplayUnitId())->getPluralName())); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>


							<!-- Display Max/Min PipeDiameters -->
							<?php if( !is_null ($size['size']) && $sizeTab['options']['displayMinPipeDiameters'] ): ?>
								<?php if( !is_null ($size['size']->getMinPipeDiameter ()) ): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html (
											Length::create (
												$size['size']->getMinPipeDiameter (), 'inches')->getDisplayValue (Unit::create ($size['size']->getMinimumPipeDiameterDisplayUnitId ())->getPluralName ())); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>
							<!-- Max -->
							<?php if( $sizeTab['options']['displayMaxPipeDiameters'] ): ?>
								<?php if( !is_null ($size['size']) && !is_null ($size['size']->getMaxPipeDiameter ())): ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>"><?= Encoder::html (Length::create ($size['size']->getMaxPipeDiameter (), 'inches')->getDisplayValue (Unit::create ($size['size']->getMaximumPipeDiameterDisplayUnitId ())->getPluralName ())); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html ($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>
							<?php endif; ?>

							<?php if( $sizeTab['options']['displayMaxViewing'] && $sizeTab['options']['displaytextSize'] ): ?>

								<?php if( !is_null ($size['size']) && isset($size['textSize']) ): ?>
									<td rowspan="<?= Encoder::html($size['rowSpan']); ?>"><?= Encoder::html(Length::create($size['textSize']->getHeight(), 'inches')->getDisplayValue('inches')); ?></td>
                                    <?php

                                        $feetDecimal = Length::create($size['textSize']->calculateMaxViewingDistance(), 'inches')->toUnit('feet');
                                        $feetObj = Length::create(floor($feetDecimal), 'feet');
                                        $inchesFeet = $feetDecimal - $feetObj->getValue();
                                        $feet = $feetObj->getValue();
                                        $inches = round(Length::create($inchesFeet, 'feet')->toUnit('inches'));

                                        if ( $inches > 0 ) {
                                            $inches .= '″';
                                        }

                                        $maxViewingDistance = ( !empty($feet) ? $feetObj->getValue().'′ ' : '');
                                        $maxViewingDistance .=( !empty($inches) ? $inches : '');

                                    ?>
									<td rowspan="<?= Encoder::html($size['rowSpan']); ?>"><?= Encoder::html('Up to ' . $maxViewingDistance ); ?></td>
								<?php else: ?>
									<td rowspan="<?= Encoder::html($size['rowSpan']); ?>">N/A</td>
									<td rowspan="<?= Encoder::html($size['rowSpan']); ?>">N/A</td>
								<?php endif; ?>

							<?php endif; ?>


							<?php $sizeIds = $sizeTab['options']['displaySizes'] && !is_null ($size['size']) ? $size['size']->getId() : $sizeIndex; ?>

							<?php foreach ($sizeTab['materials'][$sizeIds] as $materialGroupIndex => $materialGroups): ?>

								<?php $mRowSpan = count($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['mountingHoles']) == 0 ? 1 :
												  count($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['mountingHoles']); ?>

								<!-- Display Materials -->
								<?php if( $sizeTab['options']['displayMaterials'] ): ?>

									<td rowspan="1">
										<?php if( !is_null ($materialGroups) ): ?>
											<ul><?php foreach ($materialGroups as $materialIndex => $material): ?>
												<li><?= Encoder::html(!is_null($material) && !is_null($material->getName ()) ? $material->getName() : "N/A"); ?></li>
											<?php endforeach; ?></ul>
										<?php else:?>N/A<?php endif; ?>
									</td>
								<?php endif; ?>
								<!-- End Display Materials -->

								<!-- Start Corner Radius -->
								<?php if( count($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['cornerRadius']) > 0): ?>
									<?php if( $sizeTab['options']['displayCornerRadiuses'] ): ?>
										<!-- Abu, change this logic below. Jason wont like that -->
                                        <?php foreach($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['cornerRadius'] as $raidusId => $radius): ?>
                                        <td rowspan="1"> <?= Encoder::html(Length::create($radius->getCornerRadius(), 'inches')->getDisplayValue(Unit::create(CornerRadius::create($radius->getId())->getCornerRadiusDisplayUnitId())->getPluralName())); ?></td>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif; ?>
								<!-- End Display Corner Radius -->


								<?php if( $sizeTab['options']['displayMountingHoles'] || $sizeTab['options']['displayBluePrints']): ?>

									<td rowspan="1"><ul>

										<?php foreach ($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['mountingHoles'] as $mhIndex => $mountingHole): ?>
											<li><?= !empty($mountingHole) && !is_null($mountingHole->getShortName()) ? Encoder::html($mountingHole->getShortName()) : 'N/A'; ?></li>
										<?php endforeach; ?>
									</ul></td>

									<?php if( $sizeTab['options']['displayBluePrints'] ): ?>

										<td rowspan="1">

											<?php if(!empty($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['blueprint']) ): ?>

												<ul>

												<?php foreach ($sizeTab['mountingHoles'][$sizeIds][$materialGroupIndex]['blueprint'] as $diagrams): ?>

													<?php if( method_exists($diagrams, "getImageFile") && !is_null($diagrams->getImageFile ()) ): ?>
														<li>
															<a href="#<?= Encoder::html($diagrams->getImageFile()); ?>" alt="<?= Encoder::html ($diagrams->getBluePrintToolTips()); ?>">Diagram</a>
														</li>
													<?php else: ?>

														<li>N/A</li>
													<?php endif; ?>

												<?php endforeach; ?>

												</ul>

											<?php else: ?>
												N/A
											<?php endif; ?>
										</td>
									<?php endif; ?>

									</tr>

								<?php endif; ?>

							</tr>

						<?php endforeach; ?>

					<?php endforeach; ?>

					</tbody>
				</table>

			<?php endif; ?>

			<?php if ($sizeTab['outro']): ?>
				<div class="material-more-options-container">
					<span class="material-more-options-label">More Options</span>
					<span class="size-outro material-more-options-text"><?= Encoder::html($sizeTab['outro']); ?>&nbsp;<a href="#" class="product-page-link">Contact Us Now</a></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="ghost">
	<div class="product-added-to-cart-dialog" id="add-to-cart-dialog">
		<div class="product-add-cart-holder">
			<div class="product-add-cart-top-area">
				<div class="product-add-cart-top-left">
					<div class="product-add-cart-title-holder">
						<span class="product-add-cart-title"><!-- &#10004;&nbsp; --><img src="/new_images/product-page/checkmark-blk.png" />Added to Cart</span>
					</div>
					<div class="product-add-cart-added-image"><img width="60" src="<?= Encoder::html ($Product->getProductImage ("medium")); ?>" alt="<?= Encoder::html ($Product->getProductName ()); ?>" /></div>
					<div class="product-add-cart-added-details">
						<div class="product-add-cart-added-details-productname"><?= $Product->getProductName (); ?></div>
						<dl class="product-add-cart-dl">
							<dt class="">Size:</dt>
							<dd class="add-cart-size-val"></dd>
							<dt class="">Material:</dt>
							<dd class="add-cart-material-val"></dd>
							<dt class="">Packaging:</dt>
							<dd class="">Sold Individually</dd>
							<dt class="">&nbsp;</dt>
							<dd class="">&nbsp;</dd>
							<dt class="">Qty:</dt>
							<dd class="add-cart-qty-val"></dd>
							<dt class="">Total Price:</dt>
							<dd class="add-cart-price-val"></dd>
						</dl>
					</div>
				</div>
				<div class="product-add-cart-top-right">
					<div class="product-add-cart-right-title">Your Cart</div>
					<div class="product-add-cart-blue-totals">
						<div class="product-add-cart-blue-totals-top">2 Items</div>
						<div class="product-add-cart-blue-totals-middle"></div>
						<div class="product-add-cart-blue-totals-bottom">Subtotal: $45.35</div>
					</div>
				</div>
				<div class="product-add-cart-top-bottom"><span class="product-add-cart-return-underline"><a href="#" class="product-add-cart-return-link">Back to Product Page</a></span><a class="product-view-cart-button" href="#" title=""><span>View Cart&nbsp;&nbsp;</span></a></div>
			</div>
			<div class="product-add-cart-middle-area">

	<?php if( 0 >= 1): ?>
		<?php if( count($productAccessories) >= 1): ?>


	<div class="product-page-title-type-holder product-page-title-type-holder-top">
		<div class="product-add-cart-title-holder-carousel">
			<div class="product-add-cart-title">Complete Your Purchase With...</div>
		</div>
		<div class="product-add-cart-options-holder-carousel product-cart-options-holder-carousel cart-page">
			<ul class="accessory-browser-page">
			</ul>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div class="product-carousel-main-cart cart-page">
	<div class="product-carousel-arrow-left product-carousel-action"></div>
	<div class="product-carousel-main-cart-ulholder">
	<ul class="accessory-browser-type-cart">

		<?php foreach($productAccessories AS $accessorieIndex => $accessories): ?>
			<li class="carousel-item-page" data-accessory-type="<?= $accessories->getAccessoryTypeName(); ?>">
			<div class="product-carousel-item">
				<div class="product-carousel-image">
					<a href="<?= Encoder::html($accessories->getProductUrl()); ?>">--
						<img src="<?= Encoder::html($accessories->getProductImage()); ?>" /></a>
				</div>
				<div class="product-carousel-text"><?= Encoder::html($accessories->getProductName()); ?></div>
				<div class="product-carousel-sub-text"><?= Encoder::html($accessories->getProductSubtitle()); ?></div>
			</div>
			</li>
		<?php endforeach; ?>
	</ul>
	</div>
	<div class="product-carousel-arrow-right product-carousel-action"></div>
	</div>
	<div style="clear:both;"></div>

	<div class="product-carousel-under-circles cart-browser cart-page">
		<div class="product-carousel-action product-carousel-under-circles-circle"></div>
	</div>

	<?php endif; ?>
	<?php endif; ?>


			</div>
			<div class="product-add-cart-bottom-area">
				Select a product above to quick view
			</div>
		</div>
	</div>
	</div>

	<div class="product-button-edit ghost">
		<a class="product-edit-design-button orange-button" href="#" title=""><span>&nbsp;&nbsp;Edit Design</span></a>
	</div>
	<div class="product-button-design ghost">
		<a class="orange-button design-button-product" href="" title=""><span>Design Your Own Custom</span></a>
	</div>

	<!-- Bottom of Product Page -->
	<br/><br/>

	<?php if( count($productAccessories) >= 1): ?>


	<div class="product-page-title-type-holder product-page-title-type-holder-top">
		<div class="product-add-cart-title-holder-carousel">
			<div class="product-add-cart-title">Recommended Accessories</div>
		</div>
		<div class="product-add-cart-options-holder-carousel product-page-options-holder-carousel main-page">
			<ul class="accessory-browser-page">
			</ul>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div class="product-carousel-main-page main-page">

	<?php if( count($productAccessories) > 5 ): ?>
		<div class="product-carousel-arrow-left product-carousel-action"></div>
	<?php endif; ?>

	<div class="product-carousel-main-page-ulholder">
	<ul class="accessory-browser-type-page">

		<?php foreach($productAccessories AS $accessorieIndex => $accessories): ?>
			<li class="carousel-item-page" data-accessory-type="<?= $accessories->getAccessoryTypeName(); ?>">
			<div class="product-carousel-item">
				<div class="product-carousel-image">
					<a href="<?= Encoder::html ($accessories->getProductName ()); ?>">
						<img src="<?= Encoder::html($accessories->getProductImage()); ?>" /></div></a>
				<div class="product-carousel-text"><?= Encoder::html($accessories->getProductName()); ?></div>
				<div class="product-carousel-sub-text"><?= Encoder::html($accessories->getProductSubtitle()); ?></div>
			</div>
			</li>
		<?php endforeach; ?>
	</ul>
	</div>

	<?php if (count ($productAccessories) > 5): ?>
		<div class="product-carousel-arrow-right product-carousel-action"></div>
	<?php endif; ?>

	</div>
	<div style="clear:both;"></div>

	<?php if( count ($productAccessories) > 5 ): ?>
		<div class="product-carousel-under-circles page-browser main-page">
			<div class="product-carousel-action product-carousel-under-circles-circle"></div>
		</div>
	<?php endif; ?>

	<?php endif; ?>


</div>




		<!-- ALL SKU's HERE - THIS IS AN EXAMPLE ONLY-->
<!-- 		<div itemprop="isRelatedTo" itemscope="" itemtype="http://schema.org/Product">
			<meta itemprop="sku" content="1234-AK">
			<meta itemprop="name" content="Construction Area Authorized Only Sign">
			<meta itemprop="description" content="Construction Area Authorized Only Sign">

			<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="10.00">
				<link itemprop="availability" href="http://schema.org/InStock">
				<meta itemprop="priceCurrency" content="USD">
			</div>
		</div>


		<div itemprop="isRelatedTo" itemscope="" itemtype="http://schema.org/Product">
			<meta itemprop="sku" content="5678-BK">
			<meta itemprop="name" content="Construction Area Authorized Only Sign">
			<meta itemprop="description" content="Construction Area Authorized Only Sign">

			<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="12.50">
				<link itemprop="availability" href="http://schema.org/InStock">
				<meta itemprop="priceCurrency" content="USD">
			</div>
		</div>


		<div itemprop="isRelatedTo" itemscope="" itemtype="http://schema.org/Product">
			<meta itemprop="sku" content="9876-CK">
			<meta itemprop="name" content="Construction Area Authorized Only Sign">
			<meta itemprop="description" content="Construction Area Authorized Only Sign">

			<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="18.80">
				<link itemprop="availability" href="http://schema.org/InStock">
				<meta itemprop="priceCurrency" content="USD">
			</div>
		</div> -->
		<!-- END ALL SKUS -->


<div class="ghost">
	<div class="price-chart-popup-container" id="price-chart-popup-container" style="">
<!--Pricing Table-->

<?php foreach ($priceTables as $sizeId => $size): ?>

		<div class="price-chart-size-title"><?= $size['size']; ?></div>
		<table class="price-chart-popup-table" cellpadding="0" cellspacing="0">
			<tbody>

			<tr>
				<th class="pcpt-a" rowspan="2">Materials</th>
				<th class="pcpt-b" rowspan="2">Packaging</th>
				<th class="pcpt-c" rowspan="2">Overlaminate</th>
				<th class="pcpt-d" rowspan="2">Mounting Holes</th>
				<th class="pcpt-e" rowspan="2">SKU</th>
				<th class="pcpt-h" colspan="6">Quantity/Price</th>
				<th class="pcpt-g" rowspan="2"></th>
			</tr>

			<?php foreach ($size['skus'] as $skuIndex => $materialGroups): ?>

				<?php foreach ($materialGroups['prices'] as $prices): ?>
					<tr class="price-chart-tr-counter">
						<?php foreach ($prices as $price): ?>
							<th class="pcpt-f"><?= Encoder::html($price['minimumQuantity']); ?></th>
						<?php endforeach; ?>
					</tr>

				<?php endforeach; ?>

				<?php foreach($materialGroups['materials'] as $materialIds => $materials): ?>

				<tr>
					<td><?= Encoder::html( !is_null($materials) ? $materials : "N/A"); ?></td>
					<td>
						<?php foreach($materialGroups['packaging'][$materialIds] as $packaging): ?>
						<?= Encoder::html($packaging); ?>
						<?php endforeach; ?>
					</td>
					<td><?= Encoder::html(!is_null($materialGroups['laminates'][$materialIds]) ? $materialGroups['laminates'][$materialIds] : "N/A"); ?></td>
					<td><?= Encoder::html(!is_null($materialGroups['mountingHoles'][$materialIds]) ? $materialGroups['mountingHoles'][$materialIds] : "N/A"); ?></td>
					<td><?= Encoder::html(!is_null($materialGroups['skuName'][$materialIds]) ? $materialGroups['skuName'][$materialIds] : "N/A"); ?></td>

					<?php foreach($materialGroups['prices'] as $prices): ?>

						<?php foreach($prices as $price): ?>

							<td>$<?= Encoder::html(number_format ($price['price'], 2, '.', ',')); ?></td>

						<?php endforeach; ?>

					<?php endforeach; ?>

					<td><a class="green-button-select price-table-select-button" data-sku-id="<?= Encoder::html($materialGroups['skuName'][$materialIds]); ?>" href="#" title=""><span>Select</span></a></td>
				</tr>

				<?php endforeach; ?>

			<?php endforeach; ?>

			</tbody>
		</table>
	

<?php endforeach; ?>
</div></div>


<div class="ghost">
<div class="product-material-reflectivity-container">
<div class="product-material-table-reflectivity-container">
	<div class="pmt-close-btn"></div>
	<div class="pmt-ref-overlay pmt-reflectivity-selection-shadow"></div>
	<div class="pmt-ref-overlay pmt-reflectivity-selection"></div>
	<div class="pmt-reflectivity-examples">
		<div class="pmt-reflectivity-image-a">&nbsp;</div>
		<div class="pmt-reflectivity-image-b">
			<img src="/new_images/product-page/reflectivity-a.png" alt="Non-Reflective "></div>
		<div class="pmt-reflectivity-image-c">
			<img src="/new_images/product-page/reflectivity-b.png" alt="Engineering Grade Type I Reflectivity"></div>
		<div class="pmt-reflectivity-image-d">
			<img src="/new_images/product-page/reflectivity-c.png" alt="High Intensity Prismatic Type IV Reflectivity"></div>
		<div class="pmt-reflectivity-image-e">
			<img src="/new_images/product-page/reflectivity-d.png" alt="Diamond Grade Type IX Reflectivity"></div>
	</div>
	<div class="pmt-reflectivity-chart">
		<table border="1">
			<thead>
			<tr>
				<th class="pmt-reflectivity-chart-a">&nbsp;</th>
				<th class="pmt-reflectivity-chart-b">Non-Reflective</th>
				<th class="pmt-reflectivity-chart-c">Engineering Grade</th>
				<th class="pmt-reflectivity-chart-d">High Intensity Prismatic</th>
				<th class="pmt-reflectivity-chart-e">Diamond Grade</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>ATSM&nbsp;D4956<br>Type</td>
				<td>�</td>
				<td>Type I</td>
				<td>Type IV</td>
				<td>Type IX</td>
			</tr>
			<tr>
				<td>Brightness</td>
				<td>�</td>
				<td>Standard Reflective Material</td>
				<td>3x More than Standard Reflective</td>
				<td>10x More than Standard Reflective</td>
			</tr>
			<tr>
				<td>Night-time Visibility</td>
				<td>�</td>
				<td>500'</td>
				<td>1,000'</td>
				<td>1,500'</td>
			</tr>
			<tr>
				<td>Reflectivity</td>
				<td>
					<div class="pmt-reflectivity-holder">
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
					</div>
				</td>
				<td>
					<div class="pmt-reflectivity-holder">
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
					</div>
				</td>
				<td>
					<div class="pmt-reflectivity-holder">
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
						<span class="product-materials-table-reflectivity-empty">&nbsp;</span>
					</div>
				</td>
				<td>
					<div class="pmt-reflectivity-holder">
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
						<span class="product-materials-table-reflectivity-solid">&nbsp;</span>
					</div>
				</td>
			</tr>

			</tbody>
		</table>
	</div>

</div>
</div>
</div>

<div class="ghost">
<div class="min-qty-error">
	<div class="minimum-tooltip-content">
		<div class="minimum-qty-title">Oops!</div>
		<div class="minimum-qty-content">This item can only be purchased with quantity <span class="min-qty-num-tip">X+</span>. Please enter a new quantity and try again.</div>
	</div>
</div>
</div>











<?php $hideMe = TRUE; ?>

<!-- Translate dialog -->
<?php if( $Product->getCustom() === FALSE && $hideMe !== TRUE): ?>
<div class="translate-sign-left-holder">
	<div class="translate-sign-preview-text">Preview:</div>
	<div class="translate-sign-product-image-holder">
		<img src="<?= Encoder::html ($Product->getProductImage ("large")); ?>" alt="<?= Encoder::html($Product->getProductName ()); ?>"></div>
</div>
<div class="translate-sign-right-holder">
	<div class="translate-sign-right-language">
		<div class="title15bold marginbottom6">Choose a Language:</div>
		<?php foreach($translationFams as $pid => $lang): $o = ($pid == $Product->getId () ? "CHECKED" : NULL) ?>
			<div class="product-radio-holder radiolang">
				<input id="radiolang<?= $pid; ?>" name="radiolang" value="<?= $pid; ?>" type="radio" <?= $o; ?> >
				<label for="radiolang<?= $pid; ?>"><?= Encoder::html($lang['name']); ?></label>
			</div>
		<?php endforeach; ?>
		<a class="green-big-button" href="" title=""><span>Apply</span></a>
		<div class="translate-sign-bottom-tweak-title">Need another language?</div>
		<div class="translate-sign-bottom-tweak-text">Write your own translation. Custom pricing will apply.</div>
		<a class="orange-button" href="#" title=""><span>Tweak Sign</span></a>
	</div>
</div>
<div style="clear:both;"></div>
<?php endif; ?>