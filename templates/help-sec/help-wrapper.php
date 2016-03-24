<div id="column-2" class="span-18 last prepend-top-5">
<?php

include $Path_Templates_Help."help-description.php";

$template = str_replace(".php", "", $ObjHelp->template);

echo Template::generate(
    "help-sec/{$template}", array (
        'links'               => $links,
        'ObjHelp'             => $ObjHelp,
        'Path_Templates_Help' => $Path_Templates_Help,
    )
);
?>
</div>