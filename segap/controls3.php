<?php
require('se_range_class.php');
session_start();
require('se_config.php');
pg_connect($pg_connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/controls234.js"></script>
<style type="text/css">
/* <![CDATA[ */
body {
		  padding: 0px;
		  margin: 2px;}
#tabs {
		  font-size: 11px;
		  width: 315px;}
#tabs-1 {
		  width: 270px;
		  font-size: 16px;}
#tabs-2{
		  padding: 16px 3px 16px 3px;
		  width: 308px;
		  font-size: 11px;
		  overflow: scroll;}
#tabs-2 td{
		  font-size: 14px;
		  text-align: center;}
#tabs-3 {
		  overflow: scroll;
		  width: 270px;
		  font-size: 16px;}
button {
		  margin: 10px 0px 0px 100px;
		  width: 100px;}
span.desc {
		  font-size: 16px;
		  line-height: 2;}
h2 {
		  text-align: center;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
   load_selections();
   $("#tabs").tabs();
   
   $("#opentab").click();
   $("button").button();
   var win_h = $(window).height();
   $("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);
   $(".selcat").click(function() {
      var num_sel = $(".selcat:checked").size();
      if(num_sel == 0){
         $("input[name='species']").filter("[value='all']").attr("checked", true);
      } else {
         $("input[name='species']").filter("[value='prot']").attr("checked", true);
      }
   })
$("#sub").click(function(evt) {
   evt.preventDefault();
   $("#fm2").submit();
});


});
/* ]]> */
</script>
</head>
<body>
<div id="tabs">

<ul>
<li><a href="#tabs-1">View Layers</a></li>
<li><a id="opentab" href="#tabs-2">Select Species</a></li>
<li><a id="legendtab" href="#tabs-3">Legends</a></li>
</ul>
<form  action="">
<div id="tabs-1">
    <ul class="aqtree3clickable">
<li class="aq3open"><a href="#" class="no_link">Foreground</a>
<ul>
<li><input type="checkbox" name="states"   onclick="loadlayers();" /><a>States</a></li>
<li><input type="checkbox" name="cities"  onclick="loadlayers();" /><a>Cities</a></li>
<li><input type="checkbox" name="counties"  onclick="loadlayers();" /><a>Counties</a></li>
<li><input type="checkbox" name="roads"  onclick="loadlayers();" /><a>Roads</a></li>
<li><input type="checkbox" name="basins_river"  onclick="loadlayers();" /><a>Watersheds</a></li>
<li><input type="checkbox" name="hydro"  onclick="loadlayers();" /><a>Rivers</a></li>
<li><input type="checkbox" name="bcr"  onclick="loadlayers();" /><a>BCR</a></li>
<li><input type="checkbox" name="lcc"  onclick="loadlayers();" /><a>LCC</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Stewardship</a>
<ul>
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();">Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();">Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status"onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none" checked="checked" onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();">Land Cover</a></li>
<li><input type="radio" name="background" value="elevation" checked="checked" onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();">Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</div></form>
<div id="tabs-2">
  <?php

$aoi_name = $_POST['aoi_name'];
$type = $_POST['type'];

if (!isset($_SESSION["range".$aoi_name]) ) {
	$_SESSION["range".$aoi_name] = new se_range_class($aoi_name);
}
$rclass = $_SESSION["range".$aoi_name];

//var_dump( $rclass->test());
?>


<form action="controls4.php" method="post" target="controls" id="fm2"  >
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" /> 
<table style="border-collapse:collapse;" id="cntrls3">

<tr>
<th></th><th style="width: 60px;">Species Count</th><th colspan="2">Select Category</th>
</tr>

<tr>
<td style="width:15px;"><input type="radio" name="species" value="all" checked="checked" /></td>
<td style="border: solid black 1px; border-right: white;"><?php echo $rclass->num_species['all_species']; ?></td>
<td colspan="2" style="border: solid black 1px; border-left: white;" >all species in selection area</td>
</tr>

<tr><td colspan="4" style="height: 5px; border-right:  solid 1px white; "></td></tr>

<tr>
<td></td>
<td style="border: solid black 1px; border-right: white; border-bottom: white"><?php echo $rclass->num_species['usesa_code']; ?></td>
<td style="border-top: solid black 1px;"><input type="checkbox" name="usesa_code" class="selcat"  /></td>
<td style="border: solid black 1px; border-bottom: white; border-left: white;"> Federally listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotal']; ?></td>
<td><input type="checkbox" name="strsprotal" class="selcat" /></td>
<td style="border-right: solid black 1px;"> Al state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotfl']; ?></td>
<td><input type="checkbox" name="strsprotfl" class="selcat" /></td>
<td style="border-right: solid black 1px;"> FL state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotga']; ?></td>
<td><input type="checkbox" name="strsprotga" class="selcat" /></td>
<td style="border-right: solid black 1px;"> GA state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotky']; ?></td>
<td><input type="checkbox" name="strsprotky" class="selcat" /></td>
<td style="border-right: solid black 1px;"> KY state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotms']; ?></td>
<td><input type="checkbox" name="strsprotms" class="selcat" /></td>
<td style="border-right: solid black 1px;"> MS state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotnc']; ?></td>
<td><input type="checkbox" name="strsprotnc" class="selcat" /></td>
<td style="border-right: solid black 1px;"> NC state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprotsc']; ?></td>
<td><input type="checkbox" name="strsprotsc" class="selcat" /></td>
<td style="border-right: solid black 1px;"> SC state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsprottn']; ?></td>
<td><input type="checkbox" name="strsprottn" class="selcat" /></td>
<td style="border-right: solid black 1px;"> TN state listed species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['strsprotva']; ?></td>
<td ><input type="checkbox" name="strsprotva" class="selcat" /></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;"> VA state listed species</td>
</tr>

<tr>
<td><input type="radio" name="species" value="prot" /></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strgrank2']; ?></td>
<td><input type="checkbox" name="strgrank2" class="selcat" /></td>
<td style="border-right: solid black 1px;"> NS Global priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsrankal2']; ?></td>
<td><input type="checkbox" name="strsrankal2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS AL priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsrankfl2']; ?></td>
<td><input type="checkbox" name="strsrankfl2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS FL priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsrankga2']; ?></td>
<td><input type="checkbox" name="strsrankga2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS GA priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsrankky2']; ?></td>
<td><input type="checkbox" name="strsrankky2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS KY priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsrankms2']; ?></td>
<td><input type="checkbox" name="strsrankms2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS MS priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsranknc2']; ?></td>
<td><input type="checkbox" name="strsranknc2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS NC priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsranksc2']; ?></td>
<td><input type="checkbox" name="strsranksc2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS SC priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsranktn2']; ?></td>
<td><input type="checkbox" name="strsranktn2" class="selcat" /></td>
<td style="border-right: solid black 1px;">NS TN priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['strsrankva2']; ?></td>
<td ><input type="checkbox" name="strsrankva2" class="selcat" /></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;">NS VA priority species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnal']; ?></td>
<td><input type="checkbox" name="strsgcnal" class="selcat" /></td>
<td style="border-right: solid black 1px;">AL SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnfl']; ?></td>
<td><input type="checkbox" name="strsgcnfl" class="selcat" /></td>
<td style="border-right: solid black 1px;">FL SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnga']; ?></td>
<td><input type="checkbox" name="strsgcnga" class="selcat" /></td>
<td style="border-right: solid black 1px;">GA SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnky']; ?></td>
<td><input type="checkbox" name="strsgcnky" class="selcat" /></td>
<td style="border-right: solid black 1px;">KY SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnms']; ?></td>
<td><input type="checkbox" name="strsgcnms" class="selcat" /></td>
<td style="border-right: solid black 1px;">MS SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnnc']; ?></td>
<td><input type="checkbox" name="strsgcnnc" class="selcat" /></td>
<td style="border-right: solid black 1px;">NC SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcnsc']; ?></td>
<td><input type="checkbox" name="strsgcnsc" class="selcat" /></td>
<td style="border-right: solid black 1px;">SC SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px;"><?php echo $rclass->num_species['strsgcntn']; ?></td>
<td><input type="checkbox" name="strsgcntn" class="selcat" /></td>
<td style="border-right: solid black 1px;">TN SGCN Species</td>
</tr>

<tr>
<td></td>
<td style="border-left: solid black 1px; "><?php echo $rclass->num_species['strsgcnva']; ?></td>
<td ><input type="checkbox" name="strsgcnva" class="selcat" /></td>
<td style="border-right: solid black 1px; border-bottom:solid black 1px;">VA SGCN Species</td>
</tr>


<tr>
<td></td>
<td style="border-left: solid black 1px;"><input type="radio" name="sel" value="and" /> </td>
<td colspan="2" style="border-right: solid black 1px;">AND Select only species in all checked categories</td>
</tr>

<tr>
<td ></td>
<td style="border: solid black 1px; border-top: white; border-right: white;"><input type="radio" name="sel" value="or" checked="checked" /></td>
<td colspan="2" style="border-bottom: solid black 1px; border-right: solid black 1px;"> OR Select species in any checked category </td>
</tr>

</table>
</form>
<button id="sub">Submit</button>
</div>

<div id="tabs-3">
    
<h4><a href="#lcov">GAP Land Cover</a></h4>
<h4><a href="#own">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>

<a name="elev"></a><br />
<h4>Elevation (meters)</h4>
<img alt="status legend" src="/graphics/segap/se_elev_legend.png" />
<br />

<a name="lcov"></a><br />
<h4>GAP Land Cover</4>
<img alt="landcover legend" src="/graphics/segap/se_lcov_1_25.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_26_50.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_51_75.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_76_100.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_101_125.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_126_150.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_151_175.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_176_200.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_201_225.png" />
<br />

<a name="own"></a><br />
<h4>Ownership (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/segap/se_owner_legend.png" />
<br />

<br />
<a name="manage"></a><br />
<h4>Management (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/segap/se_manage_legend.png" />
<br />

<a name="status"></a><br />
<h4>GAP Status (Stewardship)</h4>
<img alt="status legend" src="/graphics/segap/se_status_legend.png" />
<br />
</div>
</body>
</html>
