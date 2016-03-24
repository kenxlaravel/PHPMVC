
<?//echo '<pre>'; print_r(get_defined_vars()); echo '</pre>'; die();?>

<div id="item-image-and-description" class="love" style="min-width: 400px;  vertical-align: top;">

	<div style="margin: 0px auto; margin-top: 5px; float: left; width: 100%;">

		<div style="clear: both:">

			<?//echo '<pre>'; print_r($product); echo '</pre>'; ?>

			<h2><?=$product->getDefaultProductName(); ?></h2>

		</div>

		<div id="item-thumbnail" style="float: left; width: 280px;">

			<a href="#" alt="" /><img src="<?=$product->getDefaultImage(); ?>" /></a>

			<div style="float: left; width: 245px; padding-top: 10px; font-size: 12px;">

				<p><strong>Item#</strong> <?=$product->getProductNumber(); ?></p>

				<p><strong>Sign Reads:</strong> <?=$product->getByLegend(); ?></p>

				<p><strong>Language:</strong>

					<?=($product->getLanguage()) ? $product->getLanguage()->getName() : NULL; ?></p>

<!--				<p><strong>Compliances:</strong>

					<?php /*foreach($product->getSkus() as $productSkus): */?>

						<?php /*if( !is_null($productSkus->getCompliances()->getName()) ): */?>

							<?/*=$productSkus->getCompliances()->getName();*/?>,

						<?php /*endif; */?>

					<?php /*endforeach; */?>

				</p>-->

			</div>

		</div>

		<div style="float: left; min-width: 380px; overflow: hidden;">

			<div style="padding-bottom: 10px;">

				<h3>Size:</h3>

				<div style="border: 1px solid #fff; width: 100%; float: left;"><?/*=implode( " | ", $product->getSizes()->getName() ); */?></div>

			</div>

			<div>

				<h3>Material:</h3>

				<div style="">
<!--
					<?php /*foreach ($product['materials'] as $productMaterials) : */?>

						<?php /*foreach($productMaterials as $index => $materialNames) : */?>

							<input type="radio" name="materials" value="<?/*=$index;*/?>"> <?/*=$materialNames;*/?><br/>

						<?php /*endforeach; */?>

					--><?php /*endforeach; */?>

				</div>

			</div>

		<!--	<?php /*if( $product['mountingHoles'] ) : */?>

				<div>

					<h3>Mounting Holes:</h3>

					<div style="">

						<?php /*foreach ($product['mountingHoles'] as $productMountingHoles) : */?>

							<?/*=$productMountingHoles->getShortName() */?>

						<?php /*endforeach;*/?>

					</div>

				</div>

			--><?php /*endif; */?>

			<div>

				<h3>Packaging:</h3>

				<div style="">

					<?php /*foreach($product->getSkus() as $productSkus) : */?>

						<?php /*if( !is_null($productSkus->getPackageInclusionNote()) ): */?>

							<?/*= $productSkus->getPackageInclusionNote(); break;*/?><br />

						<?php /*endif; */?>

					<?php /*endforeach; */?>

				</div>

			</div>

		</div>

		<?php if( $product->getProductRecommendations() ) : ?>

			<div style="float: left; padding-left: 25px;">

				<h3>Related Products</h3>

				<?php foreach($product->getProductRecommendations() as $recommended) : ?>

					<div style="float: left; padding-left: 25px; padding-top: 10px; clear: both;">

						<?php foreach($recommended->getRecommendProduct() as $productRecommended): ?>

							<a href="#">
								<img style="width: 100px; height: 100px;" src="<?=$productRecommended->getDefaultImage(); ?>" alt="image">
							</a><br />

						<?php endforeach; ?>

						<p><strong><?= $recommended->getTitle(); ?></strong></p>

						<p>adfasdfasdfasdfasdfasdfaf<br>sadfasdfasfasdf</p>

					</div>

				<?php endforeach; ?>

			</div>

		<?php endif; ?>

		<div style="clear: both; min-width: 200px;">

			<div style="border-bottom: 1px solid #ccc; padding: 10px 0 0;">

				<?=$product->getDescription(); ?>

			</div>
-->
			<?php if( $product->getProductAccessories() ) : ?>

				<div>
					<h3>Recommended Accessories:</h3>

					<div style="text-align: center;">

						<?php foreach($product->getProductAccessories() as $accessories) : ?>

							<div style="float: left; padding-left: 25px;">

								<?php foreach($accessories->getAccessoryProduct() as $productAccessories): ?>

									<a href="#">

										<img style="width: 120px; height: 120px;" src="<?=$productAccessories->getDefaultImage(); ?>" alt="image">\

									</a><br />

								<?php endforeach; ?>

								<p><?= $accessories->getTitle(); ?></p>

								<p>adfasdfasdfasdfasdfasdfaf<br>sadfasdfasfasdf</p>

							</div>

						<?php endforeach; ?>

					</div>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
