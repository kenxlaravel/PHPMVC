<div id="breadcrumbs" class="top-rounded container">

<?php



 if ( isset($result) && $result['breadcrumbs'] ){
?>
	<p class="clearfix search-breadcrumbs">
		<a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">SafetySign.com</a>
		<span class='keyword'>
		<?php foreach ( $result['breadcrumbs'] as $searchBreadcrumb ) { ?>
			&gt; <a href="<?php echo htmlspecialchars($searchBreadcrumb['url']); ?>"><?php echo htmlspecialchars($searchBreadcrumb['label'] . ': ' . $searchBreadcrumb['name'], ENT_QUOTES, 'UTF-8'); ?></a>
		<?php } ?>
		</span>
	</p>
<?php
	}else{
?>
	 <ul class="clearfix">
<?php

$total = count($breadcrumbs);
$i = 0;
//If we have breadcrumbs, loop through them
if ($total) {
	foreach($breadcrumbs as $key => $value) {

		$i++;

		$text = htmlspecialchars($value['text'], ENT_QUOTES, 'UTF-8');
		$link = htmlspecialchars(isset($value['link']) ? $value['link'] : NULL, ENT_QUOTES, 'UTF-8');

		if ($i == 1) { $top_level = $value; }
		// do not unfold the HTML below. browsers render a space before text when li and text are broken onto separate lines.
		?>
		<li <?php echo ($i != $total && !empty($link) ? 'itemscope itemtype="http://data-vocabulary.org/Breadcrumb" ' : ''); echo ($i == $total ? 'class="': '');echo ($i == $total ? 'current' : '');echo (PAGE_TYPE == 'help' && $i == $total  ? ' help-breadcrumb' : '');echo ($i == $total ? '"': '');?>><?php echo ($i != $total && !empty($link) ? '<a itemprop="url" href="' . $link . '"><span itemprop="title">' : ''); echo $text; echo ($i != $total && !empty($link) ? '</span></a>' : ''); ?></li>
<?php
			}
		}
?>
	</ul>
	<?php }
?>
</div>