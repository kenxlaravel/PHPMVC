
<div><!-- Intro --> <? echo Encoder::html($materialTab['intro']) ?></div>

<table border="1" >

<thead>

<tr>
	<th> Material </th>

	<? if ( $materialTab['displayThickness'] ) :?>

		<th>Thickness</th>

	<? endif;?>

	<? if ( $materialTab['displayDurability'] ) :?>

		<th>Outdoor Durability</th>

	<? endif;?>

	<? if ( $materialTab['displayTemperatureRange'] ) :?>

		<th>Service Temperature Range</th>

	<? endif;?>


	<? if ( $materialTab['displayAvailableSizes'] ) :?>

		<th>Available Sizes</th>

	<? endif;?>

	<? if ( $materialTab['displayOverlaminates'] ) : ?>

		<th>Overlaminates</th>

	<? endif;?>

</tr>

</thead>


<? foreach ( $materialTab['productMaterials'] AS $category ) : ?>

	<tbody>

	<? if ( $materialTab['displayCategory'] ) :?>

		<tr >

			<th style="background: grey; width: 100%; text-align: left;" colspan="20" > <? echo Encoder::html($category["materialCategory"]->getName()); ?> </th>

		</tr>

	<? endif; ?>

	<? foreach ($category["materialGroups"] AS $materialGroup ) : ?>

		<tr >

		<td width="20%" rowspan = <? echo $materialGroup['materialGroupRowSpan']; ?> >
			<b> <? echo Encoder::html($materialGroup['materialGroup']->getName()); ?> </b> <br>
			<? echo Encoder::html($materialGroup['materialGroup']->getDescription()); ?>
		</td>

		<? foreach ( $materialGroup['materials'] AS $material ) :?>

			<? if ( $materialTab['displayThickness'] ) :?>

				<?php if( !empty($material['material']) ): ?>

					<td bgcolor="lightblue" rowspan = <? echo $material['materialRowSpan']; ?>> <? echo Encoder::html($material['material']->getThickness()); ?>

					<?php if( !is_null($material['reflectivity'])) : ?>

						 <?=Encoder::html($material['reflectivity']->getRating() != 0 ? $material['reflectivity']->getRating() : ''); ?>

					<?php endif; ?>

                    </td>

				<?php else: ?>

                <td bgcolor="lightblue" rowspan = '<? echo $material['materialRowSpan']; ?>'>&ndash;</td>

				<?php endif; ?>

			<? endif; ?>

			<? if ( $materialTab['displayDurability'] ) :?>


				<?php if( !is_null($material['material']) ): ?>

					<td bgcolor="lightblue" rowspan = <? echo $material['materialRowSpan']; ?>> <? echo Encoder::html($material['material']->getDurability()); ?> </td>

				<?php else: ?>

						<td bgcolor="lightblue" rowspan = '<? echo $material['materialRowSpan']; ?>'>&ndash;</td>

				<?php endif; ?>

			<? endif; ?>

			<? if ( $materialTab['displayTemperatureRange'] ) :?>


				<?php if( !is_null($material['material']) ): ?>

					<td bgcolor="lightblue" rowspan = <? echo $material['materialRowSpan']; ?>> <? echo Encoder::html($material['material']->getServiceTemperatureRange()); ?> </td>

				<?php else: ?>

						<td bgcolor="lightblue" rowspan = '<? echo $material['materialRowSpan']; ?>'>&ndash;</td>

				<?php endif; ?>

			<? endif; ?>


			<? $i = 0;?>

			<? foreach ($material['sizeLaminateGroups'] AS $index => $sizeLaminateGroup) :?>

				<? if ( $i > 0 ) :?>

					<tr class="data-row product-addtocart-row">

				<? endif; ?>

				<td bgcolor="yellow" rowspan =  '1' >

					<? foreach ( $sizeLaminateGroup['sizes'] AS $size ) :?>

						<ul>

							<li> <? echo Encoder::html($size->getName()); ?> </li>

						</ul>

					<? endforeach; // size group ?>
				</td>
				<? if ( $materialTab['displayOverlaminates'] ) : ;?>

					<td bgcolor="lightgreen" rowspan =  '1'>

						<? if( count($sizeLaminateGroup['laminates']) > 0 ): ?>

							<? foreach ($sizeLaminateGroup['laminates'] AS $laminate) : ?>

								<ul>

									<li> <? echo Encoder::html( $laminate->getName() ? $laminate->getName() : '–' ); ?> </li>

								</ul>

							<? endforeach; // Laminates ?>

						<? else : ?>

							<? echo '&ndash;';?>

						<? endif; // If laminate field exists but there is no data for this particular material?>

					</td>

				<? endif; // If laminates field exists?>
				<? if ( $i > 0 ) :?>
					</tr>
				<? endif; ?>
				<? $i++; endforeach; // sizeLaminateGroups ?>
			</tr>

		<? endforeach; // Materials ?>

	<? endforeach; // Groups ?>

	</tbody>

<? endforeach; // Categories ?>

</table>

<br/>