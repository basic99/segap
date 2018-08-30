<?php
require('se_config.php');
require('se_aoi_class.php');
require('se_range_class.php');
session_start();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Data download</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<link rel="stylesheet" href="styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
body {font-family: sans-serif;}
#selects {position: absolute; left: 500px; top: 50px;}
img {position: fixed; left: 50px; top: 50px;}
.none {visibility: hidden;}
.hdr {font-size: 1.2em; text-align: center;}
#b01 {position: fixed; left: 130px; top: 500px; }
#b02 {position: fixed; left: 250px; top: 500px; }
.ui-widget {font-size: 11px;}
button {width: 100px;
}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {    
	 $("button").button();
	 $("#b01").click(function(evt) {
         evt.preventDefault();
			document.forms[0].reset();
     });
	  $("#b02").click(function(evt) {
         evt.preventDefault();
			document.forms[0].submit();
      });
	   $('#cls').click(function() {
         window.close();
      });
     check_pd();
	  
});
function check_pd(){
	var spp = document.forms[0].spp.value;
	//alert(spp);
	document.forms[0].strpds.value = spp;
	var length = document.forms[0].pds.length;
	for (var i=0;  i<length; i++){
		if(spp == document.forms[0].pds[i].value){
			document.forms[0].pds[i].checked = 'true';
		}
	}
}

//function poll(){
//	var length = document.forms[0].pds.length;
//	var previous = "";
//	for (var i=0;  i<length; i++){
//		if(document.forms[0].pds[i].checked){
//			var selected = document.forms[0].pds[i].value;
//			if (previous.length == 0){
//				previous = selected;
//			}else{
//				previous = previous + ":" + selected;
//			}
//		}
//	}
//	document.forms[0].strpds.value = previous;
//}


function poll(){
	if(document.forms[0].pds.length){
        var length = document.forms[0].pds.length;
        var previous = "";
        for (var i=0;  i<length; i++){
             console.log(i);
           if(document.forms[0].pds[i].checked){
              var selected = document.forms[0].pds[i].value;
              if (previous.length == 0){
                 previous = selected;
              }else{
                 previous = previous + ":" + selected;
              }
           }
        }
   } else {
        var previous = document.forms[0].pds.value;
   }

   
	document.forms[0].strpds.value = previous;
}
/* ]]> */
</script>
</head>
<body >

<?php
if (($_SESSION['username']) == 'visitor' ){
	echo '<h4>Must log in to use this feature.</h4>';
	echo "<button id=\"cls\">Close</button>";
	
	die();
}
//get post data
$avian = $_POST['avian'];
$mammal = $_POST['mammal'];
$reptile = $_POST['reptile'];
$amphibian = $_POST['amphibian'];
$aoi_name = $_POST['aoi_name'];
$search = $_POST['search'];
$sppcode = $_POST['sppcode'];
$richness_species = $_POST['richness_species'];
$richness_map = $_POST['richness_map'];
//var_dump($_POST);


//get range and aoi instances
$rclass = $_SESSION["range".$aoi_name];
$se_aoi_class = $_SESSION[$aoi_name];

//get corners of aoi and add 10%
$min_x = $se_aoi_class->get_minx();
$min_y = $se_aoi_class->get_miny();
$max_x = $se_aoi_class->get_maxx();
$max_y = $se_aoi_class->get_maxy();
$x_adj = ($max_x - $min_x)*0.1;
$y_adj = ($max_y - $min_y)*0.1;
$min_x -= $x_adj;
$min_y -= $y_adj;
$max_x += $x_adj;
$max_y += $y_adj;



//create box
$aoi_extent = ms_newRectObj();
$aoi_extent->setextent($min_x, $min_y, $max_x, $max_y);
$extent_save = $min_x.":".$min_y.":".$max_x.":".$max_y;


$mapfile = "/var/www/html/segap/segap.map";

// draw elevation and states layers
$map = ms_newMapObj($mapfile);
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_ON);
$this_layer = $map->getLayerByName('states');
$this_layer->set('status', MS_ON);

//draw aoi layer
$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//creating main map
$mapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";
$maploc = "{$mspath}{$mapname}";
$map->setSize(400, 400);
$mapimage = $map->draw();

//draw rectangle on image
$aoi_extent->draw($map, $map->getLayerByName('aoi'), $mapimage, 1, '' );

//save image to file
$mapimage->saveImage($maploc);

//change form action if aoi is all ecosystem
if($se_aoi_class->mask_name == 'ecosys'){
	$form_action = 'data_dnld_allstates.php';
} else {
	$form_action = 'data_dnld_submit.php';
}

?>
<img alt="map" src="/server_temp/<?php  echo $mapname; ?>" />
<button id="b01">Reset</button>
<button id="b02">Submit</button>

<form action="<?php echo $form_action;  ?>" target="_self" method="post" >
<input type="hidden" name="aoi_name" value="<?php echo $aoi_name; ?>" />
<input type="hidden" name="ext_save" value="<?php echo $extent_save; ?>" />
<input type="hidden" name="spp" value="<?php echo $sppcode; ?>" />
<input type="hidden" name="strpds"  />
<div id="selects">
<table>
<tr><td colspan="2" class="hdr">general layers</tr>
<tr>
<td><input type="checkbox" name="lcov" /></td><td>landcover</td>
</tr>
<!--
<tr>
<td><input type="checkbox" name="steward" /></td><td>stewardship(vector)</td>
</tr>
-->
<?php
if (!empty($richness_map)) {
	$richness_export = $se_aoi_class->richnessexport($richness_map);
	?>	

	<tr>
	<td><input type="checkbox" checked="checked" name="richness" /></td><td>richness map</td>
	</tr>
	<tr>
	<td class="none">HHH</td><td class="none">HHHHHHH</td>
	</tr>
	
	<?php 
	pg_connect($pg_connect);
	$species_arr = explode(":", $richness_species);
	foreach ($species_arr as $a){
		$query = sprintf("select strscomnam  from info_spp where strsppc ilike '%s'", pg_escape_string($a));
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		printf("<tr><td><input type=checkbox onclick='poll();' name='pds' value='%s'/></td><td>%s</td></tr>" , $a, $row['strscomnam']);
		$species_list .= $row['strscomnam']."\n";
	}
	//$richness_species = preg_replace("/:/","\n", $richness_species);
	
} else { ?>

	<tr>
	<td class="none">HHH</td><td class="none">HHHHHHH</td>
	</tr>
	
	<?php	  
	if (isset($avian) && isset($mammal) && isset($reptile) && isset($amphibian)) {
		echo "<tr><td colspan=\"2\" class=\"hdr\">predicted distribution layers</tr>";
		$rclass->get_species_dnld($avian, $mammal, $reptile, $amphibian, $search);
	}
}

?>
</table>
</div>
<input type="hidden" name="r_export" value="<?php echo $richness_export; ?>" />
<input type="hidden" name="r_species" value="<?php echo $species_list; ?>" />
</form>


</body>
</html>
