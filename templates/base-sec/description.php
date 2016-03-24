<?php
//@TODO: Update variables names below once all three tables are revamped

switch (PAGE_TYPE) {


    case 'category':

        $name = $objCategoryPage->getName();
        $description_image = $objCategoryPage->getDescriptionImage();
        $image_template = $objCategoryPage->getImageTemplate();
        $description_text_html = $objCategoryPage->getDescriptionTextHtml();
        $description_more_info_html = $objCategoryPage->getDescriptionMoreInfoHtml();
        $imagePath = $objCategoryPage->getImage();
        $special_header_class_name = $objCategoryPage->getSpecialHeaderClassName();
        $intro_supplement_html = $objCategoryPage->getIntroSupplementHtml();
        $popup_html = $objCategoryPage->getPopupHtml();

        break;

    case 'grouping':

        $name = $objGroupingPage->getName();
        $description_image = $objGroupingPage->getDescriptionImage();
        $image_template = $objGroupingPage->getImageTemplate();
        $description_text_html = $objGroupingPage->getDescriptionTextHtml();
        $description_more_info_html = $objGroupingPage->getDescriptionMoreInfoHtml();
        $imagePath = $objGroupingPage->getImage();
        $special_header_class_name = $objGroupingPage->getSpecialHeaderClassName();
        $intro_supplement_html = $objGroupingPage->getIntroSupplementHtml();
        $popup_html = $objGroupingPage->getPopupHtml();

        break;

    case 'subcategory':

        $name = $objSubcategoryPage->getName();
        $description_image = $objSubcategoryPage->getDescriptionImage();
        $image_template = $objSubcategoryPage->getImageTemplate();
        $description_text_html = $objSubcategoryPage->getDescriptionTextHtml();
        $description_more_info_html = $objSubcategoryPage->getDescriptionMoreInfoHtml();
        $imagePath = $objSubcategoryPage->getImage();
        $special_header_class_name = $objSubcategoryPage->getSpecialHeaderClassName();
        $intro_supplement_html = $objSubcategoryPage->getIntroSupplementHtml();
        $popup_html = $objSubcategoryPage->getPopupHtml();

    break;

    case 'geotarget':

        $name = $objGeotargetPage->getName();
        $description_image = $objGeotargetPage->getDescriptionImage();
        $image_template = $objGeotargetPage->getImageTemplate();
        $description_text_html = $objGeotargetPage->getDescriptionTextHtml();
        $description_more_info_html = $objGeotargetPage->getDescriptionMoreInfoHtml();
        $imagePath = $objGeotargetPage->getImage();
        $special_header_class_name = $objGeotargetPage->getSpecialHeaderClassName();
        $intro_supplement_html = $objGeotargetPage->getIntroSupplementHtml();
        $popup_html = $objGeotargetPage->getPopupHtml();

        break;

    case 'landing':

        $name = $objLandingPage->getName();
        $description_image = $objLandingPage->getDescriptionImage();
        $image_template = $objLandingPage->getImageTemplate();
        $description_text_html = $objLandingPage->getDescriptionTextHtml();
        $description_more_info_html = $objLandingPage->getDescriptionMoreInfoHtml();
        $imagePath = $objLandingPage->getImage();
        $special_header_class_name = $objLandingPage->getSpecialHeaderClassName();
        $intro_supplement_html = $objLandingPage->getIntroSupplementHtml();
        $popup_html = $objLandingPage->getPopupHtml();

        break;
}

?>
<div id="landing-top"
     class="<?php if (!empty($special_header_class_name)) print $special_header_class_name; else print 'span-24 last'; ?>">

    <div id="landing-page-header" class="landing-header<?php echo($description_image ? '' : ' no-image'); ?>">

        <?php

        if ($description_image) {
            ?>
            <img src="<?php echo IMAGE_URL_PREFIX . htmlspecialchars($description_image, ENT_QUOTES, 'UTF-8'); ?>"
                 id="landing-header-image-top" alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"/>
        <?php
        }
        ?>
        <div id="landing-header-text" class="<?= htmlspecialchars($image_template, ENT_QUOTES, 'UTF-8') ?>">

            <?php
            echo $description_text_html;

            if ($description_more_info_html) {
                ?>
                <div id="product-links"><span>More Info:</span><?php print $description_more_info_html; ?></div>
            <?php
            }

            //Geotarget template
            if (PAGE_TYPE == 'geotarget' && (count($productRow) > 0 || count($federalSigns) > 0)) {

                $flag = 0;

                $objGeotargetPage = GeotargetPage::create(PAGE_ID);
                $objSubcategoryPage = SubcategoryPage::create(PAGE_ID);

                //Get the current page zone, and a list of all zones
                $zone_id = (PAGE_TYPE == 'subcategory' ? PAGE_ID : $objGeotargetPage->getSubcategoryId());
                $link = new Page('subcategory', $zone_id);
                $subcategory_link = $link->getUrl();
                $zones = $objGeotargetPage->getGeotargetList($zone_id);
                $total_zones = count($zones);



                //If there are 15 or less zones uses a three column layout
                if ($total_zones <= 15) {

                    //Number of zones in each column
                    $per_column = ceil($total_zones / 3);

                    //More than 15 zones uses a five column layout
                } else {

                    //Number of zones in each column
                    $per_column = ceil($total_zones / 5);

                    //Special case for numbers that break the column logic (16 is the only confirmed case)
                    if (ceil($total_zones / $per_column) < 5) {
                        $flag = 1;
                    }

                }

            if ($total_zones <= 15) {
 ?>
            <div class="geo-wrapper">
                <p class="button green">
                    <span class="button-text left-side">Change State</span> <span class="sprite-wrapper"><i class="sprite sprite-down-white left-side"></i></span>
                </p>
                <div class="dropdown">
                    <div class="clearfix">
                        <?php
                        } else {
                        ?>
                        <div class="geo-wrapper">
                            <p class="button green">
                                <span class="button-text left-side">Change State</span> <span class="sprite-wrapper"><i class="sprite sprite-down-white left-side"></i></span>
                            </p>
                            <div class="dropdown">
                                <div class="clearfix">
                                    <?php
                                    }

                                    //Reset the counts before the loop
                                    $count1 = 1;
                                    $count2 = 1;

                                    //Loop through all the zones
                                    foreach ($zones AS $zone) {

                                        //If this is the first zone, start a column
                                        if ($count1 == 1) {
                                            ?>
                                            <ul class="left-side">
                                        <?php
                                        }

                                        //Instantiate a new page for the current zone
                                        $link = new Page('geotarget', $zone['id']);

                                        //Output the zone link
                                        $current = ($zone['id'] == PAGE_ID && PAGE_TYPE == 'geotarget' ? " class='current'" : "");
                                        echo "<li" . $current . "><a href='" . $link->getUrl() . "'>" . htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8') . "</a></li>";

                                        //End a column and start a new one
                                        if ($count2 == $per_column) {
                                            ?>
                                            </ul>
                                            <ul class="left-side">
                                            <?php
                                            $count2 = 0;
                                        }

                                        //Case for 16 zones - The number that breaks everything
                                        if (($count1 == $per_column) && $flag == 1) {
                                            $per_column--;
                                            $flag = 0;
                                        }

                                        //Last zone, end
                                        if ($count1 == $total_zones) {
                                            ?>
                                            </ul>
                                        <?php
                                        }

                                        //Increment the counters
                                        $count1++;
                                        $count2++;

                                    }
                                    ?>

                                </div>
                                <div class="clearfix button-wrap">
                                    <p class="left-side bold last-margin"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownSnippet(), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <a href="<?php echo htmlspecialchars($subcategory_link, ENT_QUOTES, 'UTF-8'); ?>" class="button green first-margin"><span class="left-side"><?php echo htmlspecialchars($objSubcategoryPage->getGeotargetDropdownButton(), ENT_QUOTES, 'UTF-8'); ?></span><i class="sprite sprite-right-white"></i></a>
                                </div>
                            </div>
                        </div>

                                    <?php

            }

            ?>
        </div>

        <?=$intro_supplement_html; ?>
        <?=$popup_html; ?>

    </div>
</div>