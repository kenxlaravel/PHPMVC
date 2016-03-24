<div id="navigation-dropdown">
<?php 
if($main_category_data['main_category_id'])
{
?>
<div id="column-1" class="column span-6 sidebar-nav">
<h3 id="sign-category" class="section-headline"><?php print $main_category_data['name'];?></h3>
<div id="vertical-list-of-categories">
<ul id="alpha-list-of-categories"> 
 <?php 
								   	$category_data=$ObjMainCategory->LeftNavGroupMainCategoryCategoryProductPage($mid);
									$category_count=count($category_data);
									if($category_count>0)
									{
									foreach($category_data as $key => $value)
									{
									$i++;
									if($categorygroup_id==$value['subcategory_id']){$display="";}else {$display="none";}
								    ?>
                                  <li class="category<?php if($categorygroup_id==$value['subcategory_id']){ print " current";}?>">
<a href="<?php print $website.urlencode($value['main_category']).'.html?group='.urlencode($value['name']).'';?>" class="js groupfilter" ><span ><?php print $value['name'];?></span> </a>
<p id="hiddendes_<? print $i; ?>" style="display:<?php print $display;?>;">
<?php
$category_group_data=$ObjMainCategory->LeftNavGroupCategoryList($value[subcategory_id]);
$category_group_count=count($category_group_data);
if($category_group_count>0)
{
foreach($category_group_data as $key => $valuesub)
{
?>
<a class="subgroup<?php if($cid==$valuesub['subcategory_id']) {print " subgroupcategorybg";}?>"  href="<?php print $website;?><?php print urlencode($value['main_category']);?>/<?php print urlencode($valuesub['name']);?>.html?group=<?php print urlencode($value['name']);?>"><span><?php print $valuesub['name'];?></span></a>
<?php
}//end of sub group
}//end of sub group count
?>
                                   <?php 
											}//end of foreach
									?>

<?php										}//end of count 
                                    ?>

</li>                                    
</ul>
 </div>	
 <div id="category-list-bottom" class="bottom-rounded"></div>	
	</div>
<?php } ?>   
</div>