<?php 
require('se_range_class.php');
session_start();
require("se_config.php");
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
body {padding: 0px; margin: 2px;}
#tabs {font-size: 11px; width: 315px;}
#tabs-1 { width: 270px; font-size: 16px;}
#tabs-2{ width: 270px; font-size: 11px;}
#tabs-3 {overflow: scroll; width: 270px; font-size: 16px;}
button { width: 100px; margin: 15px;}
span.desc {font-size: 16px; line-height: 2;}
h2 {text-align: center;}
#tabs-2 li,td {list-style: none; font-size: 16px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
   <?php
   if(!isset($_GET['aoiname'])){
     echo "functions_action();";
   }
   ?>
   document.forms[1].submit();
   $("#tabs").tabs();
   $("#opentab").click();
   $("button").button();
   var win_h = $(window).height();
   $("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);
   
   $("#sub").click(function(evt) {
      document.forms[1].submit();	  
   });
   $("#back").click(function(evt) {
		  evt.preventDefault();
		  change_categories();
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
if(isset($_GET['aoiname'])){
   //var_dump($_GET);
   $aoi_name = $_GET['aoiname'];
  // $species_sel = $_GET['species'];
   $species = "all";
   if (!isset($_SESSION["range".$aoi_name]) ) {
      $_SESSION["range".$aoi_name] = new se_range_class($aoi_name);
   }
   //$rclass = $_SESSION["range".$aoi_name];
} else {
   //var_dump($_POST);
   foreach($_POST as $key=>$foo){
   switch($key){
      case "aoi_name":
         $aoi_name = $foo;
         break;
      case "species":
         $species = $foo;
         break;
      case "sel":
         $sel = $foo;
         break;
      default:
         $protcats[] = $key; 
   }
}
   
}
//var_dump($protcats);
$rclass = $_SESSION["range".$aoi_name];
$tot_class = $rclass->num_class($species, $sel, $protcats);
?>
<form action="select_species.php" method="post" target="data">
<input  type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="protcats" value='<?php echo json_encode($protcats); ?>' />
<input type="hidden" name="type" value='reload' />

<table>
<tr><th>Species Group</th><th>Total Count</th><th>Display</th></tr>

<tr>
<td>Avian Species</td>
<td class="cnt"><?php echo $tot_class['avian']; ?></td>
<td class="cnt"><input type="checkbox" name="avian" checked="checked" /></td>
</tr>

<tr>
<td>Mammalian Species</td>
<td class="cnt"><?php  echo  $tot_class['mammal']; ?></td>
<td class="cnt"><input type="checkbox" name="mammal" checked="checked" /></td>
</tr>

<tr>
<td>Reptilian Species</td>
<td class="cnt"><?php echo $tot_class['reptilian']; ?></td>
<td class="cnt"><input type="checkbox" name="reptile" checked="checked" /></td>
</tr>

<tr>
<td>Amphibian Species</td>
<td class="cnt"><?php echo $tot_class['amphibian']; ?></td>
<td class="cnt"><input type="checkbox" name="amphibian" checked="checked" /></td>
</tr>
</table>
 
<button id="back">Back</button>
<button id="sub">Submit</button>

<ul>
<li>
	<input id="modesingle" type="radio" name="mode" checked="checked" value="single" onclick="functions_action();" /><label for="modesingle" > Single&nbsp;species&nbsp;Mode</label>	  
</li>
<li>
	<input id="modemult" type="radio" name="mode"  value="multiple" onclick="functions_action();" /> <label for="modemult" > Multiple&nbsp;Species&nbsp;Mode </label>	  
</li>
</ul>
   </form>
</div>

<div id="tabs-3">
    
<h4><a href="#lcov">GAP Land Cover</a></h4>
<h4><a href="#own">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>
<h4><a href="#predicted">Predicted Distributions &amp;<br /> Known Ranges</a></h4>

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

<a name="predicted"></a><br />
<h4>Predicted Distributions</h4>
<img alt="status legend" src="/graphics/segap/predicted_leg.png" />
<br />

<a name="range"></a><br />
<h4>Known Ranges</h4>
<img alt="status legend" src="/graphics/segap/range_leg.png" />
<br />
</div>


</body>
</html>
