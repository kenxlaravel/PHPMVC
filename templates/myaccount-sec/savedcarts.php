<div class="myaccount-section container saved-carts-page" id="saved-carts">

	<?
		//If the user has more than 5 orders, give them a total and a view all button
		if ($saved_carts == 0 && $page->getNickname() == 'savedcarts') {?>
			<p class="h4 append-bottom">Saved Shopping Carts</p>
			<?

			if ($totalQuantity > 0){ ?>
				<div class="save-cart-wrapper">
				<p>You have items in your cart. Would you like to save it?</p>
				<p><a href="" class="button blue save-cart">Save Shopping Cart</a></p>
			<? }else{ ?>
				<p>Add items to your cart and then visit the Shopping Cart to save carts.</p>
			<?php } ?>
			<form method="POST" action="/save-cart" class="save-cart-dialog">
				<p class="h3">Save Cart</p>
				<p>Give your cart a name and click the Save button below to save it to your account.</p>
				<input type="text" class="text cart-name" name="keywords" placeholder="Enter Cart Name" value="" size="100">
				<div class="prepend-top note-wrapper">
					<span>Notes</span> <span class="note-text">(optional)</span>
					<textarea type="text" class="clear save-notes" name="keywords" value="" size="255"></textarea>
				</div>
				<div class="save-cart-controls prepend-top">
					<p class="left-side show-notes"><a href="#" class="underline">Add a note</a> <span class="note-text">(optional)</span></p>

					<div class="right-side"><span class="button save-cancel">Cancel</span> <button type="submit" class="button green save-cart" >Save Cart</button></div>
				</div>
			</form>
			</div>
		<?   	}else{ ?>

			<p class="h4 left-side append-bottom">Saved Shopping Carts</p>
			<?
			//If the user has more than 5 orders, give them a total and a view all button
			if ($saved_carts > 5 && $page->getNickname() != 'savedcarts') {?>

				<?php $savedCarts = array_slice($savedCarts, 0, 5); ?>
				<p class="left-side first-margin prepend-top-5">(Showing 5 most recent saved carts. <a class="underline" href="<?print htmlspecialchars($link->getUrl(), ENT_QUOTES, 'UTF-8');?>">View All</a>)</p>

			<? }?>


			<div class="save-cart-wrapper right-side">
				<? if (isset($totalQuantity) && $totalQuantity > 0){ ?>
					<p class="right-side">You have items in your cart. <a href="#" class="underline save-cart">Save Shopping Cart</a></p>
				<? } ?>
				<form method="POST" action="/save-cart" class="save-cart-dialog">
					<p class="h3">Save Cart</p>
					<p>Give your cart a name and click the Save button below to save it to your account.</p>
					<input type="text" class="text cart-name" name="keywords" placeholder="Enter Cart Name" value="" size="30">
					<div class="prepend-top note-wrapper">
						<span>Notes</span> <span class="note-text">(optional)</span>
						<textarea type="text" class="clear save-notes" name="keywords" value="" size="30"></textarea>
					</div>
					<div class="save-cart-controls prepend-top">
						<p class="left-side show-notes"><a href="#" class="underline">Add a note</a> <span class="note-text">(optional)</span></p>

						<div class="right-side"><span class="button save-cancel">Cancel</span> <button type="submit" class="button green save-cart" >Save Cart</button></div>
					</div>
				</form>
			</div>

			<table id="savedCarts" class="saved-cart-viewer">
			<thead>
			<tr>
				<th class="sc-table-col-1">Cart Name</th>
				<th class="sc-table-col-2">Date</th>
				<th class="sc-table-col-3">Total Qty.</th>
				<th class="sc-table-col-4">Notes</th>
				<th class="sc-table-col-5">Details</th>
			</tr>
			</thead><!-- TO DO: the tbody needs to wrap every two rows -->

			<?			foreach($savedCarts AS $cart){?>

				<tbody class="order-row saved-cart" data-saved-cart-id-hash="<?php echo $cart['cart_hash']; ?>" data-saved-cart-name="<?php echo htmlspecialchars($cart['cart_name'], ENT_QUOTES, 'UTF-8');?>" data-saved-cart-owner-email-address="<?php print htmlspecialchars($customerData,ENT_QUOTES,'UTF-8');?>">
				<tr class="data-row order-details-open-head">
					<td class="sc-table-col-1">
						<? if(!empty($cart['cart_name']) && mb_strlen($cart['cart_name']) > 35){
							?>
							<p class="expansion-trigger fake-link product-name-link no-margin-bottom"><?php echo htmlspecialchars(substr($cart['cart_name'], 0, 35), ENT_QUOTES, 'UTF-8'); ?> &hellip;</p>
						<? }else{?>
							<p class="expansion-trigger fake-link product-name-link no-margin-bottom"><?php echo htmlspecialchars($cart['cart_name'], ENT_QUOTES, 'UTF-8'); ?></p>
						<?php } ?>
					</td>
					<td class="sc-table-col-2"><?=date("m/d/Y", strtotime($cart['modification_time']))?></td>
					<td class="sc-table-col-3"><?=$cart['quantity_sum']?></td>
					<td class="sc-table-col-4">
						<? if(!empty($cart['cart_note']) && mb_strlen($cart['cart_note']) > 45){
							?><p class="expansion-trigger fake-link no-margin-bottom"><?php echo htmlspecialchars(substr($cart['cart_note'], 0, 45), ENT_QUOTES, 'UTF-8') ?> &hellip;</p>
						<? }else{ ?>
							<p class="expansion-trigger fake-link no-margin-bottom"><?php echo htmlspecialchars($cart['cart_note'], ENT_QUOTES, 'UTF-8') ?></p>
						<?php } ?>
					</td>
					<td class="sc-table-col-5">
						<div class="show-details-buttons"><a href=""class="button small-text blue expansion-trigger">View</a><a class="button small-text green load-cart" href="">Load Cart</a></div><div class="hide-details expansion-trigger"><p class="fake-link no-margin-bottom">Hide Details</p></div>
					</td>
				</tr>
				<tr class="order-details-wrapper">
					<td colspan=5 class="order-details-open"><?=$FrontEndTemplateIncluder->getHtml(); ?></td>
				</tr>

				</tbody>
			<? } ?>
			</table><?if($saved_carts > 5 && $page->getNickname() != 'savedcarts'){?>
				<a class="right-side" href="<?print htmlspecialchars($link->getUrl(), ENT_QUOTES, 'UTF-8');?>">View All Saved Carts &gt;&gt;</a>

			<?}?>

		<?   	} ?>
</div>


<?php

?>














