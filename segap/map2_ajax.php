<?php
//date_default_timezone_set("America/New_York");

date_default_timezone_set('America/New_York');

// ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/html/segap/logs/php-error.log");
error_log("map2_ajax");

require('se_aoi_class.php');
session_start();

require('se_config.php');
pg_connect($pg_connect);
require('se_define_aoi.php');



//click points for navigation
$click_x = $_POST['clickx'];
$click_y = $_POST['clicky'];
//click points  for custom aoi
$posix = $_POST['posi_x'];
$posiy = $_POST['posi_y'];


//aoi name for saved aoi
$aoi_name_saved = $_POST['aoi_name_saved'];

$extent = $_POST['extent'];
$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$layer = $_POST['layers'];

//ogc_fid for predefined aoi
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$status_aoi = $_POST['status_aoi'];
$county_aoi = $_POST['county_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$state_aoi = $_POST['state_aoi'];
$bcr_aoi = $_POST['bird_consv_aoi'];
$lcc_aoi = $_POST['lcc_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];

$zoom_aoi = $_POST['zoomaoi'];
$zoom = $_POST['zoom'];
$mode = $_POST['mode'];
$aoi_name = $_POST['aoi_name'];
$sppcode = $_POST['sppcode'];
$species_layer = $_POST['species_layer'];
$species_layer_prev = $_POST['species_layer_prev'];
$map_species = $_POST['map_species'];
$richness_species = $_POST['richness_species'];
$type = $_POST['type'];
$user_name = $_SESSION['username'];
$job_id = $_POST['job_id'];
$file_shp = $_POST['shapefile'];
$pred_transp = $_POST['pred_transp'];
$range_transp = $_POST['range_transp'];
$range_transp_prev = $_POST['range_transp_prev'];

$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/segap", "a");
fprintf($logfileptr, "\n\n\nInput  %s  %s\n%s ", date('G:i:s'), __FILE__, $post);
fclose($logfileptr);


$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id );";
pg_query($query);

//create mapobj
$mapfile = "/var/www/html/segap/segap.map";
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where job_id = $job_id";
pg_query($query);


//if AOI is undefined then create it in postgis and create new AOI object else get aoi from form variable
if (strlen($aoi_name) ==0){
	//create aoi name
	$now = localtime(time(),1);
	$aoi_name = "aoi".$now['tm_yday'].rand(0,9999999);
	if ($type == 'custom'){
		get_custom_aoi($aoi_name, $posix, $posiy, $extent, $win_w, $win_h );
	}elseif(($type == 'predefined') || ($type == 'permalink')){
		$aoi_predefined['owner_aoi'] = $owner_aoi;
		$aoi_predefined['manage_aoi'] = $manage_aoi;
		$aoi_predefined['status_aoi'] = $status_aoi;
		$aoi_predefined['county_aoi'] = $county_aoi;
		$aoi_predefined['basin_aoi'] = $basin_aoi;
		$aoi_predefined['state_aoi'] = $state_aoi;
		$aoi_predefined['bcr_aoi'] = $bcr_aoi;
		$aoi_predefined['lcc_aoi'] = $lcc_aoi;
		$aoi_predefined['ecosys_aoi'] = $ecosys_aoi;
		$aoi_predef_save = pg_escape_string(serialize($aoi_predefined));
		$query = "update aoi set aoi_data = '{$aoi_predef_save}' where name = '{$aoi_name}'";

		get_predefined_aoi($aoi_name, $owner_aoi, $manage_aoi, $status_aoi, $county_aoi, $state_aoi, $basin_aoi, $bcr_aoi, $ecosys_aoi, $lcc_aoi);
		pg_query($query);
	}elseif($type == 'uploaded') {
		get_uploaded_aoi($aoi_name, $file_shp);
	}elseif ($type == 'saved_aoi'){
		$aoi_name = $aoi_name_saved;
		$query = "select description from aoi where name = '{$aoi_name}'";
		$result = pg_query($query);
		$row = pg_fetch_array($result);
		$aoi_desc = $row['description'];
	}
	$new_page = true;
	//$se_aoi_class = new se_aoi_class($aoi_name);
	$_SESSION[$aoi_name] = new se_aoi_class($aoi_name);
}else{
	$new_page = false;
}
$se_aoi_class =  $_SESSION[$aoi_name];

$aoi_area = $se_aoi_class->get_area();

//create mapobj
$map = ms_newMapObj($mapfile);
$mapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";
$maploc = "{$mspath}{$mapname}";

//get calculated maps for single species or richness from aoi_class, but first test to see if we can use previous map
if (preg_match("/habitat/", $species_layer) && !preg_match("/habitat/", $species_layer_prev)) {
		$map_species = $se_aoi_class->landcover_map($sppcode);
}
if (preg_match("/ownership/", $species_layer) && !preg_match("/ownership/", $species_layer_prev)) {
		$map_species = $se_aoi_class->ownership_map($sppcode);
}
if (preg_match("/status/", $species_layer) && !preg_match("/status/", $species_layer_prev)) {
		$map_species = $se_aoi_class->protection_map($sppcode);
}
if (preg_match("/manage/", $species_layer) && !preg_match("/manage/", $species_layer_prev)) {
		$map_species = $se_aoi_class->management_map($sppcode);
}
if (preg_match("/richness/", $species_layer) && !preg_match("/richness/", $species_layer_prev)) {
		$map_species = $se_aoi_class->richness($richness_species);
}

//convert sppcode to raster name
$raster = "d_".strtolower($sppcode);

//set layers from controls
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}

if(preg_match("/cities/", $layer)){
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/counties/", $layer)){
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_OFF);
}

if(preg_match("/roads/", $layer)){
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/states/", $layer)){
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/wtshds/", $layer)){
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/bcr/", $layer)){
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/lcc/", $layer)){
	$this_layer = $map->getLayerByName('lcc');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('lcc');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/ownership/", $layer)){
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/management/", $layer)){
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/status/", $layer)){
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_OFF);
}

$rangemap = "r_".strtolower($sppcode);
$data = "wkb_geometry from ".$rangemap;

// table lost moving from meatacomet

//set raster to display species maps
// if(preg_match("/range/", $species_layer)){
// $this_layer = $map->getLayerByName('rangemaps');
// $this_layer->set('data', $data);
// $this_layer->set('status', MS_ON);
// $this_layer->set('opacity', $range_transp);
//set layers from controls
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
/*
//turn off other rasters
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_OFF);
$this_layer = $map->getLayerByName('landcover');
$this_layer->set('status', MS_OFF);
*/
}


if(preg_match("/habitat|ownership|status|manage|richness/", $species_layer)){
$this_layer = $map->getLayerByName('mapcalc');
//echo ($grass_raster.$map_species);
$this_layer->set('data', $grass_raster.$map_species);
$this_layer->set('status', MS_ON);
//turn off other rasters
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_OFF);
$this_layer = $map->getLayerByName('landcover');
$this_layer->set('status', MS_OFF);
}


if(preg_match("/predicted/", $species_layer)){
$this_layer = $map->getLayerByName('mapcalc');
$this_layer->set('data', $grass_raster_perm.$raster);
$this_layer->set('status', MS_ON);
$this_layer->set('opacity', $pred_transp);
//set layers from controls
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
/*
 //turn off other rasters
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_OFF);
$this_layer = $map->getLayerByName('landcover');
$this_layer->set('status', MS_OFF);
*/
}

$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);

//calculate extent from class variables the first time or zoom to aoi, else use previous extent
$extent_obj =  ms_newRectObj();

// function to calculate bounding box for species range
function range_extent($code) {
		  pg_connect("host=localhost dbname=segap_ranges user=postgres");
		  $query = "select st_xmax(wkb_geometry) from r_{$code}";
		  $result = pg_query($query);
		  $array = pg_fetch_all($result);
		  foreach($array as $row) {
			  if(!isset($max_x)){
				  $max_x = $row["st_xmax"];
			  }
			  $max_x = max($row["st_xmax"], $max_x);
		  }

		  $query = "select st_xmin(wkb_geometry) from r_{$code}";
		  $result = pg_query($query);
		  $array = pg_fetch_all($result);
		  foreach($array as $row) {
			  if(!isset($min_x)){
				  $min_x = $row["st_xmin"];
			  }
			  $min_x = min($row["st_xmin"], $min_x);
		  }

		  $query = "select st_ymax(wkb_geometry) from r_{$code}";
		  $result = pg_query($query);
		  $array = pg_fetch_all($result);
		  foreach($array as $row) {
			  if(!isset($max_y)){
				  $max_y = $row["st_ymax"];
			  }
			  $max_y= max($row["st_ymax"], $max_y);
		  }

		  $query = "select st_ymin(wkb_geometry) from r_{$code}";
		  $result = pg_query($query);
		  $array = pg_fetch_all($result);
		  foreach($array as $row) {
			  if(!isset($min_y)){
				  $min_y = $row["st_ymin"];
			  }
			  $min_y = min($row["st_ymin"], $min_y);
		  }

		  $extent = $min_x ." ".$min_y." ".$max_x." ".$max_y;
		  return $extent;
}

if (($new_page  || $zoom_aoi) && ($type != 'permalink')) {
	$min_x = $se_aoi_class->get_minx();
	$min_y = $se_aoi_class->get_miny();
	$max_x = $se_aoi_class->get_maxx();
	$max_y = $se_aoi_class->get_maxy();
	$x_adj = ($max_x - $min_x)*0.1;
	$y_adj = ($max_y - $min_y)*0.1;
	$extent_obj->setExtent($min_x-$x_adj, $min_y-$y_adj, $max_x+$x_adj, $max_y+$y_adj);
}elseif (($new_page  || $zoom_aoi) && ($type == 'permalink')) {
	$mapext = explode(" ", range_extent($sppcode));
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extent_obj->setExtent($minx, $miny, $maxx, $maxy);
}else {
	$mapext = explode(" ", $extent);
	$minx = $mapext[0];
	$miny = $mapext[1];
	$maxx = $mapext[2];
	$maxy = $mapext[3];
	$extent_obj->setExtent($minx, $miny, $maxx, $maxy);
}
$map->setSize($win_w, $win_h);
$map->zoompoint($zoom, $click_point, $win_w, $win_h, $extent_obj);
$mapimage = $map->draw();
$mapimage->saveImage($maploc);


//create ref map
$refname="refmap".rand(0,9999999).".png";
$refurl="/server_temp/".$refname;
$refname = $mspath.$refname;
$refimage = $map->drawReferenceMap();
$refimage->saveImage($refname);


//get new extent
$new_extent = 	sprintf("%3.6f",$map->extent->minx)." ".
sprintf("%3.6f",$map->extent->miny)." ".
sprintf("%3.6f",$map->extent->maxx)." ".
sprintf("%3.6f",$map->extent->maxy);


$ret = json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl, "aoiname"=>$aoi_name, "mapspecies"=>$map_species, "aoiarea"=>$aoi_area));

$logfileptr = fopen("/var/log/weblog/segap", "a");
fprintf($logfileptr, "\nOutput %s  %s\n%s", date('G:i:s'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;
//ob_flush();
//flush();
?>