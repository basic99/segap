<?php
//date_default_timezone_set("America/New_York");
session_start();
require('se_config.php');
pg_connect($pg_connect);

ini_set("display_errors", 0);
ini_set("error_log", "/var/www/html/segap/logs/php-error.log");

error_log("map ajax");

$mapfile = "/var/www/html/segap/segap.map";

$user_x = $_POST['user_x'];
$user_y = $_POST['user_y'];

$win_w = $_POST['winw'];
$win_h = $_POST['winh'];
$click_x =$_POST['clickx'];
$click_y = $_POST['clicky'];
$canvas_x = $_POST['canvas_x'];
$canvas_y = $_POST['canvas_y'];
$extent_raw = $_POST['extent'];
$zoom = $_POST['zoom'];
$mode = $_POST['mode'];
$layer = $_POST['layers'];
$query_layer = $_POST['query_layer'];
$county_aoi = $_POST['county_aoi'];
$basin_aoi = $_POST['basin_aoi'];
$state_aoi = $_POST['state_aoi'];
$owner_aoi = $_POST['owner_aoi'];
$manage_aoi = $_POST['manage_aoi'];
$status_aoi = $_POST['status_aoi'];
$bird_consv_aoi = $_POST['bird_consv_aoi'];
$lcc_aoi = $_POST['lcc_aoi'];
$ecosys_aoi = $_POST['ecosys_aoi'];

$job_id = $_POST['job_id'];


$post = print_r($_POST, true);
$logfileptr = fopen("/var/log/weblog/segap", "a");
fprintf($logfileptr, "\n\n\nInput  %s  %s\n%s ", date('G:i:s'), __FILE__, $post);
fclose($logfileptr);


$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);

//save extent to rect
$old_extent =  ms_newRectObj();
$extent = explode(" ", $extent_raw);
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);

//check that script is still running after mapobj creation
$query = "insert into check_mapobj(job_id ) values ( $job_id )";
pg_query($query);

//create map object
$map = ms_newMapObj($mapfile);

//check that script is still running after mapobj creation
$query = "delete from check_mapobj where job_id = $job_id";
pg_query($query);



//set layers
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
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/states/", $layer)){
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('states');
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

///////////////////////////////////////////////////////////////////////
//draw hatches for selected areas
/////////////////////////////////////////////////////////////////////////
if (isset($county_aoi) && !empty($county_aoi)){
	$key_gap = explode(":", $county_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('counties_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($basin_aoi) && !empty($basin_aoi)){
	$key_gap = explode(":", $basin_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('basin_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($state_aoi) && !empty($state_aoi)){
	$key_gap = explode(":", $state_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('state_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($owner_aoi) && !empty($owner_aoi)){
	$key_gap = explode(":", $owner_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and own_c_recl = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and own_c_recl = {$key_gap_explode[1]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('owner_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($manage_aoi) && !empty($manage_aoi)){
	$key_gap = explode(":", $manage_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and man_c_recl = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and man_c_recl = {$key_gap_explode[1]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('manage_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($status_aoi) && !empty($status_aoi)){
	$key_gap = explode(":", $status_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and status_c = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and status_c = {$key_gap_explode[1]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('status_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($ecosys_aoi) && !empty($ecosys_aoi)){
	$this_layer = $map->getLayerByName('ecosys_select');
	$this_layer->set('status', MS_ON);
}
if (isset($bird_consv_aoi) && !empty($bird_consv_aoi)){
	$key_gap = explode(":", $bird_consv_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('bcr_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($lcc_aoi) && !empty($lcc_aoi)){
	$key_gap = explode(":", $lcc_aoi);
	$filter = "(gid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (gid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('lcc_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}

//////////////////////////////////////////////////////////////////////////////////////////////
//creating main map
////////////////////////////////////////////////////////////////////////////////////
$mapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";
$maploc = "{$mspath}{$mapname}";
$map->setSize($win_w, $win_h);
//$ret =  json_encode(array("zoom"=>$zoom, "click_point"=>$click_point, "w" =>$win_w, "h"=>$win_h, "old_extent"=>$old_extent));
//echo $ret;
$map->zoompoint($zoom, $click_point, $win_w, $win_h, $old_extent);
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


$ret =  json_encode(array("mapname"=>$mapname,"extent"=>$new_extent, "refname"=>$refurl));
//$ret =  json_encode(array("mapname"=>"test"));

$logfileptr = fopen("/var/log/weblog/segap", "a");
fprintf($logfileptr, "\nOutput  %s  %s\n%s", date('G:i:s'), __FILE__,  $ret);
fclose($logfileptr);

echo $ret;
?>