<?php // Include the necessary front-end templates.

$links = (!isset($links)) ? NULL : $links;

$FrontEndTemplateIncluder->addHandlebarsTemplate('miniCart');

$totalQuantity = (int) ($ObjShoppingCart instanceof Cart ? $ObjShoppingCart->getTotalQuantity() : 0);
$subTotal = (float) ($ObjShoppingCart instanceof Cart ? $ObjShoppingCart->getSubTotal() : 0);

?>
<header itemscope itemtype="http://schema.org/WebSite">
	<meta itemprop="url" content="<?php  echo htmlspecialchars($links['home'], ENT_QUOTES, 'UTF-8'); ?>">
	<div id="verified-approved-wrapper" class="container">
		<div id="verified-approved">
			<p id="top-level"><i class="sprite sprite-lock-gold"></i> Verified Secure | <span id="stars"><i class="sprite sprite-star-gold"></i><i class="sprite sprite-star-gold"></i><i class="sprite sprite-star-gold"></i><i class="sprite sprite-star-gold"></i><i class="sprite sprite-star-gold"></i></span> Customer Approved</p>
		</div>
	</div>
	<div class="container">

		<div itemscope itemtype="http://schema.org/Organization" id="logo-accountinfo-support-promo">
			<a itemprop="url" href="<?php echo htmlspecialchars($links['home'], ENT_QUOTES, 'UTF-8'); ?>" title="Safety Signs by SafetySign.com">
				<img itemprop="logo" src="<?php print IMAGE_URL_PREFIX; ?>/new_images/ss-logo-2014.png" alt="SafetySign.com">
			</a>
		</div>

		<div id="user-account-info">
			<div class="header-icon icon-phone">Call Us 800-274-6271</div>
			<div class ="header-icon icon-contact"><a href="<?php echo htmlspecialchars($links['contact'], ENT_QUOTES, 'UTF-8'); ?>">Email Us</a></div>
			<div class="header-icon icon-chat"><div id="cio5dO" style="z-index:100;position:absolute"></div><div id="sco5dO" style="display:inline"></div><div id="sdo5dO" style="display:none"></div><script type="text/javascript">var seo5dO=document.createElement("script");seo5dO.type="text/javascript";var seo5dOs=(location.protocol.indexOf("https")==0?"https":"http")+"://image.providesupport.com/js/1hx9685lbev3n13jtak217mktq/safe-textlink.js?ps_h=o5dO&ps_t="+new Date().getTime()+"&online-link-html=Chat%20Live&offline-link-html=Chat%20Offline";setTimeout("seo5dO.src=seo5dOs;document.getElementById('sdo5dO').appendChild(seo5dO)",1)</script><noscript><div style="display:inline"><a href="http://messenger.providesupport.com/messenger/1hx9685lbev3n13jtak217mktq.html" target="_blank">Online Customer Support</a></div></noscript></div>
			<?php if(isset($_SESSION['Username']) && $_SESSION['UserType']=='U') { ?>
			<div class="header-icon icon-user">
				<a href="<?php echo htmlspecialchars($links['account'], ENT_QUOTES, 'UTF-8'); ?>" class="header-account-link">Your Account</a>
				<div class="header-signin">
					<?php if ($_SESSION['Username'] != ' ') {
						if (mb_strlen($_SESSION['Username']) > 35){ ?>
						<p class="h5">Hello,
							<?php print htmlspecialchars(mb_substr($_SESSION['Username'], 0, 35), ENT_QUOTES, 'UTF-8') . '&hellip;'; ?></p>
							<?php	}else{ ?>
							<p class="h5">Hello, <?php print htmlspecialchars($_SESSION['Username'], ENT_QUOTES, 'UTF-8'); }?> </p>
							<?php	if (mb_strlen($_SESSION['Useremail']) > 35){?>
							<p class="note-text"><?php print htmlspecialchars(mb_substr($_SESSION['Useremail'], 0, 35), ENT_QUOTES, 'UTF-8') . '&hellip;'; ?></p>
							<?php }else{ ?>
							<p class="note-text"><?php print htmlspecialchars($_SESSION['Useremail'],ENT_QUOTES,'UTF-8'); ?></p>
							<?php }
						} else { ?>
						<?php if (mb_strlen($_SESSION['Useremail']) > 35){?>
						<p class="h5">Hello!</p>
						<p class="note-text"><?php print htmlspecialchars(mb_substr($_SESSION['Useremail'], 0, 35), ENT_QUOTES, 'UTF-8') . '&hellip;'; ?></p>
						<?php }else{ ?>
						<p class="h5">Hello!</p>
						<p class="note-text"><?php print htmlspecialchars($_SESSION['Useremail'],ENT_QUOTES,'UTF-8');?> </p>
						<?php }
					} ?>
					<ul>

						<li><a href="<?php print htmlspecialchars($links['tracking'], ENT_QUOTES, 'UTF-8'); ?>">Track An Order</a></li>
						<li><a href="<?php print htmlspecialchars($links['orderhistory'], ENT_QUOTES, 'UTF-8'); ?>">Order History</a></li>
						<li><a href="<?php print htmlspecialchars($links['savedcarts'], ENT_QUOTES, 'UTF-8'); ?>">Saved Carts</a></li>
					</ul>
					<a href="<?php print URL_PREFIX_HTTPS; ?>/sign-out" class="button left-side">Sign Out</a>
					<a href="<?php print htmlspecialchars($links['account'], ENT_QUOTES, 'UTF-8'); ?>" class="button blue right-side">Account Details</a>
				</div>
			</div>

			<?php }else{ ?>
			<div class="header-icon icon-user"><a href="<?php print htmlspecialchars($links['signin'], ENT_QUOTES, 'UTF-8'); ?>">Sign In or Register</a></div>

			<?php
		}
		?>


	</div>


	<div id="search-bar">
		<div class="search-box">
			<form accept-charset="utf-8" class="site_search" name="search_form" method="GET" action="<?php echo htmlspecialchars($links['search'], ENT_QUOTES, 'UTF-8'); ?>" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
				<meta itemprop="target" content="<?php echo htmlspecialchars($links['search'], ENT_QUOTES, 'UTF-8'); ?>?keywords={query}">
				<input type="text" class="text" name="keywords" placeholder="Search by Keyword or Item #" value="<?php if ( isset($_GET['keywords']) && $_GET['keywords'] != '' ) { print htmlspecialchars($_GET['keywords'],ENT_QUOTES,'UTF-8');}?>" size="30" id="search-input" itemprop="query-input">
				<button id="search" type="submit" class="button">Search</button>
			</form>
		</div>
		<div class="minicart <?php if ($totalQuantity <= 0 && $subTotal <=0) { echo 'minicart-empty';} ?>" data-cart-item-count="<?php echo htmlspecialchars(json_encode( $totalQuantity), ENT_QUOTES, 'UTF-8'); ?>">
			<a class="minicart-button" href="<?php print htmlspecialchars($links['cart'], ENT_QUOTES, 'UTF-8');?>" >
				<span class="minicart-quantity"><?php print $totalQuantity; ?></span>
				<span class="minicart-price"><?php if ($totalQuantity <= 0 && $subTotal <=0) { echo 'Cart Empty'; } else{ ?>$<?php print number_format($subTotal, 2);}?></span>
			</a>
			<div class="minicart-preview"></div>

		</div>
	</div>
</div>

<nav>
	<div>
		<div class="sticky-logo"><a href="<?php echo website;?>">SafetySign.com</a></div>
		<div class="nav-menu-wrapper">
			<?php
			//Grab a listing of all main menu items from cache
			$menuCache = $ObjMenu->getNavigation();

			$count = count($menuCache);
			if ($count>0) {
				?>
				<ul>
					<?php
					//Loop through all menu items and display
					foreach($menuCache as $key => $cvalue) {

						//Getting neccessary data
						$main_menu_name = $cvalue['main_menu_name'];
						$main_menu_css_class = $cvalue['main_menu_css_class'];
						$main_category_image = $cvalue['main_category_image'];
						$column_breakpoint = $cvalue['column_breakpoint'];
						$clink = $cvalue['clink'];
						$validate_link = $cvalue['validate_link'];
						$sub_menu = $cvalue['sub_menu'];

						?>
						<li class="<?php print htmlspecialchars($main_menu_css_class, ENT_QUOTES, 'UTF-8'); ?>"><?php if($validate_link) print "<a href='" . htmlspecialchars($clink, ENT_QUOTES, 'UTF-8') . "' class='nav-menu-link'>" . htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8') . "</a>"; else  print '<span>'.htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8'). '</span>';?>
						<div class="nav-submenu">
							<div>
								<?php if($validate_link){?><p class="h3"><?php print "<a href='" . htmlspecialchars($clink, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8') . "</a>";?></p><?php } ?>
								<ul class="left-side">
									<?php

									$breakpoint = 1;
									foreach ($sub_menu as $svalue) {

										$sub_name = $svalue['sub_name'];
										$slink = $svalue['slink'];
										?>
										<li><?php echo "<a href='" . htmlspecialchars($slink, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($sub_name, ENT_QUOTES, 'UTF-8') . "</a>"; ?></li>

										<?php
										if($column_breakpoint == $breakpoint) print '</ul><ul class="left-side">';

										$breakpoint++;
									} ?>
									<?php if($validate_link){?><li class = "last-menu-item-link"><?php print "<a href='" . htmlspecialchars($clink, ENT_QUOTES, 'UTF-8') . "'>" . "Shop All " .htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8') . "</a><span>&#62;</span></li>"; }?>
									</ul>
								</div>
								<?php if($validate_link){?><div class="nav-link-placeholder"><a href="<?php print htmlspecialchars($clink, ENT_QUOTES, 'UTF-8'); ?>"><img alt="<?php print htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8');?>" src="<?php echo IMAGE_URL_PREFIX; ?>/new_images/header-dropdown-nav/<?php echo htmlspecialchars($main_category_image, ENT_QUOTES, 'UTF-8');?>"></a></div><?php }else{ ?><div class="nav-link-placeholder"><img alt="<?php print htmlspecialchars($main_menu_name, ENT_QUOTES, 'UTF-8');?>" src="<?php echo IMAGE_URL_PREFIX; ?>/new_images/header-dropdown-nav/<?php echo htmlspecialchars($main_category_image, ENT_QUOTES, 'UTF-8');?>"></div><?php } ?>
							</div>
						</li>
						<?php

					}
					?>



				</ul>
				<?php
			}
			?>
		</div>
		<div class="sticky-chat">
			<div id="cimJgB" style="z-index:100;position:absolute"></div>
			<div id="scmJgB" style="display:inline"></div><div id="sdmJgB" style="display:none"></div>
			<script type="text/javascript">var semJgB=document.createElement("script");semJgB.type="text/javascript";var semJgBs=(location.protocol.indexOf("https")==0?"https":"http")+"://image.providesupport.com/js/1hx9685lbev3n13jtak217mktq/safe-textlink.js?ps_h=mJgB&ps_t="+new Date().getTime()+"&online-link-html=Chat%20Live&offline-link-html=Chat%20Offline";setTimeout("semJgB.src=semJgBs;document.getElementById('sdmJgB').appendChild(semJgB)",1)</script>
			<noscript>
				<div style="display:inline">
					<a href="http://www.providesupport.com?messenger=1hx9685lbev3n13jtak217mktq">Customer Support</a>
				</div>
			</noscript>
		</div>
	</div>
</nav>

</header>