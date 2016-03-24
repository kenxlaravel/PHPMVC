<?php
	if (!empty($ObjHelp->content_header)){ ?>
<p class="h3 h3-rev pad-left-15">
	<?php echo htmlspecialchars($ObjHelp->content_header, ENT_QUOTES, 'UTF-8'); ?>
</p>
<?php
}
	if (!empty($ObjHelp->content_intro_html)) {
?>
<p class="pad-left-15 prepend-top">
	<?php echo $ObjHelp->content_intro_html; ?>
</p>
<?php
	}
?>