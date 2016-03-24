<?php
require("../bs_common.php");
$ObjStreetsign=new Streetsign();
if($_REQUEST['action']=='1')
{

}
else if($_REQUEST['action']=='2')
{
	$ObjStreetsign->GetCustomImages($_REQUEST['color'],$_REQUEST['size'],$_REQUEST['layout']);
}
else if($_REQUEST['action']=='3')
{
	$ObjStreetsign->GetParametersJson($_REQUEST['color'],$_REQUEST['size'],$_REQUEST['layout'],$_REQUEST['image_position'],$_REQUEST['arrow_position']);
}
?>