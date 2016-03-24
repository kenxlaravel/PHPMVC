


<!-- DASHES ON NULL -->

<div ><?=Encoder::html($sizeTab['intro']);?></div>

<table class="accesory-pricing-table" style="border: 1px solid #ffc; ">

    <thead>

     <?php if( $sizeTab['options']['displaytextSize'] ): ?>
		<tr>
			<th colspan="<?=$sizeTab['colspan'];?>" style="text-align: right;">Max Viewing Distance</th>
		</tr>
	 <?php endif; ?>

     <tr>
        <th>Size</th>
        <!-- If we have volume, don't show with/height -->
  	  	<?php if( $sizeTab['options']['displayVolumes'] ): ?>
        	<th>Volume</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displayLengths'] ): ?>
        	<th>Length</th>
        <?php endif; ?>

  	  	<?php if( $sizeTab['options']['displayWidths'] && !$sizeTab['options']['displayVolumes'] ): ?>
        	<th>Width</th>
        <?php endif; ?>

  	  	<?php if( $sizeTab['options']['displayHeights'] && !$sizeTab['options']['displayVolumes'] ): ?>
        	<th>Height</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displaytextSize'] ): ?>
        	<th>Average<br>Text Size</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displayMinPipeDiameters'] ): ?>
        	<th>Min Pipe Diameter</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displayMaxPipeDiameters'] ): ?>
        	<th>Max Pipe Diameter</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displayFavorable'] ): ?>
        	<th>Favorable</th>
        <?php endif; ?>

        <?php if( $sizeTab['options']['displayUnfavorable'] ): ?>
        	<th>Unfavorable</th>
        <?php endif; ?>

        <th>Available Materials</th>

      	<?php if( $sizeTab['options']['displayCornerRadiuses'] ): ?>
        	<th>Corner Radius</th>
        <?php endif; ?>

      	<?php if( $sizeTab['options']['displayMountingHoles'] ): ?>
        	<th>Mounting Holes</th>
        <?php endif; ?>

	 </tr>
	</thead>

	<tbody>

		<?php foreach ($sizeTab['groupedSizes'] as $sizeIndex => $size): ?>

			<tr >
				<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
					<?=Encoder::html($size['size']->getName());?><br>
					<img src="blueprints/<?=Encoder::html($size['blueprint']); ?>" alt="blueprint">
				</td>

				<?php if( $sizeTab['options']['displayVolumes'] ): ?>

					<?php if( !is_null($size['size']->getVolume()) ): ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['size']->getVolume());?>
						</td>

					<?php else: ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>

					<?php endif; ?>

				<?php endif; ?>

				<?php if( $sizeTab['options']['displayLengths'] ): ?>

					<?php if( !is_null($size['size']->getLength()) ): ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >

							<?=Encoder::html($size['size']->getLength());?>

						</td>

					<?php else: ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>

					<?php endif; ?>

				<?php endif; ?>

				<?php if( $sizeTab['options']['displayWidths'] && !$sizeTab['options']['displayVolumes'] ): ?>

					<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
						<?=Encoder::html($size['size']->getWidth());?>
					</td>

				<?php endif; ?>

				<?php if( $sizeTab['options']['displayHeights'] && !$sizeTab['options']['displayVolumes'] ): ?>

					<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
						<?=Encoder::html($size['size']->getHeight());?>
					</td>

				<?php endif; ?>

			   	<?php if( $sizeTab['options']['displaytextSize'] ): ?>

					<?php if( !is_null($size['textSize']) ): ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['textSize']->getHeight());?>
						</td>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['textSize']->getMaximumFavorableViewingDistance());?>
						</td>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['textSize']->getMaximumUnfavorableViewingDistance());?>
						</td>

					<?php else: ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>
						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>
						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>

					<?php endif; ?>

				<?php endif; ?>


				<!-- Display Max/Min PipeDiameters -->
		        <?php if( $sizeTab['options']['displayMinPipeDiameters'] ): ?>

		        	<?php if( !is_null($size['size']->getMinPipeDiameter()) ): ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['size']->getMinPipeDiameter());?>
						</td>

					<?php else: ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>

					<?php endif; ?>

		        <?php endif; ?>

		        <?php if( $sizeTab['options']['displayMaxPipeDiameters'] ): ?>

		        	<?php if( !is_null($size['size']->getMaxPipeDiameter()) ): ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >
							<?=Encoder::html($size['size']->getMaxPipeDiameter());?>
						</td>

					<?php else: ?>

						<td rowspan="<?=Encoder::html($size['rowSpan']);?>" >&ndash;</td>

					<?php endif; ?>

		        <?php endif; ?>

				<?php foreach($sizeTab['materials'][$size['size']->getId()] as $materialGroupIndex => $materialGroups): ?>


					<?php 

						$mRowSpan = count($sizeTab['mountingHoles'][$size['size']->getId()][$materialGroupIndex]['mountingHoles']) == 0 ? 1 :
									count($sizeTab['mountingHoles'][$size['size']->getId()][$materialGroupIndex]['mountingHoles']);
					?>

					<!-- Display Materials -->
					<td  rowspan="<?=Encoder::html($mRowSpan);?>">
			 			<ul>
			 				<?php foreach($materialGroups as $materialIndex => $material): ?>

							<li><?=Encoder::html($material->getName()); ?></li>

							<?php endforeach; ?>
						</ul>
					</td>
					<!-- End Display Materials -->

					<?php if( count($sizeTab['mountingHoles'][$size['size']->getId()][$materialGroupIndex]['cornerRadius']) >= 1 ): ?>

						<?php if( $sizeTab['options']['displayCornerRadiuses'] ): ?>

							<?php foreach($sizeTab['mountingHoles'][$size['size']->getId()][$materialGroupIndex]['cornerRadius'] as $crIndex => $radius): ?>

								<td  rowspan="<?=Encoder::html($mRowSpan);?>">
									<?=Encoder::html($radius);?>
								</td>

							<?php endforeach; ?>

						<?php endif; ?>

					<?php endif; ?>

					<?php if( $sizeTab['options']['displayMountingHoles'] ): ?>

						<?php if( $mRowSpan >= 1 ): ?>

							<?php foreach($sizeTab['mountingHoles'][$size['size']->getId()][$materialGroupIndex]['mountingHoles'] as $mhIndex => $mountingHole): ?>

								<?php if( !is_null($mountingHole->getShortName()) ) : ?>

									<td rowspan="1">

										<?=Encoder::html($mountingHole->getShortName());?>
									</td>

								<?php else: ?>

									<td >&ndash;</td>

								<?php endif; ?>

							</tr>

							<?php endforeach; ?>

						<?php else: ?>

								<td >&ndash;</td>

						<?php endif; ?>

					<?php endif; ?>

					</tr>

			<?php endforeach; ?>

		<?php endforeach; ?>

	</tbody>
</table>

<br><br>
<div><?=Encoder::html($sizeTab['outro'])?></div>