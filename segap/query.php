
<?php
//set mapfile and load mapscript if not already loaded
$mapfile = "/var/www/html/segap/segap.map";

require('se_config.php');
pg_connect($pg_connect);

//function to convert clickpoint to map co-ords
function img2map($width, $height, $point, $ext){
	if ($point->x && $point->y){
		$dpp_x = ($ext->maxx -$ext->minx)/$width;
		$dpp_y = ($ext->maxy -$ext->miny)/$height;
		$p[0] = $ext->minx + $dpp_x*$point->x;
		$p[1] = $ext->maxy - $dpp_y*$point->y;
	}
	return $p;
}

//get form variables
$win_w = $_POST['win_w'];
$win_h = $_POST['win_h'];
//$size = $_POST['win_size'];
$click_x =$_POST['img_x'];
$click_y = $_POST['img_y'] - 68;
$extent_raw = $_POST['extent'];
$zoom = $_POST['zoom'];
$layer = $_POST['layers'];
$mode = $_POST['mode'];
$query_layer = $_POST['query_layer'];

//echo $query_layer;
//create click obj
$click_point = ms_newPointObj();
$click_point->setXY($click_x, $click_y);
//echo "<h3>query results for layer {$query_layer} </h3>";

//save extent to object
$extent = explode(" ", $extent_raw);
$old_extent =  ms_newRectObj();
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);

//create map object
$map = ms_newMapObj($mapfile);
$map->setSize($win_w, $win_h);
list($qx, $qy) = img2map($map->width, $map->height, $click_point, $old_extent);
$qpoint = ms_newPointObj();
$qpoint->setXY($qx,$qy);

if(preg_match("/parcel_nam|man_desc|own_desc|status_desc/", $query_layer)){
	$qlayer = $map->getLayerByName('manage_q');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select {$query_layer} from se_steward where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/basin/", $query_layer)){
	$qlayer = $map->getLayerByName('watersheds');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select cat_name from se_wtshds_cmpl where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/city/", $query_layer)){
	$qlayer = $map->getLayerByName('urban');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select name from se_cities where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/county/", $query_layer)){
	$qlayer = $map->getLayerByName('counties');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select county  from se_county where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/bcr/", $query_layer)){
	$qlayer = $map->getLayerByName('bcr');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select bcr_name  from se_bcr where ogc_fid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/lcc/", $query_layer)){
	$qlayer = $map->getLayerByName('lcc');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$result = $qlayer->getResult(0);
	$result = $result->shapeindex;
	$query = "select lcc_name from se_lcc1 where gid = '{$result}'";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
}
if(preg_match("/landcover/", $query_layer)){
	$qlayer = $map->getLayerByName('landcover');
	$qlayer->queryByPoint($qpoint, MS_SINGLE, 0);
	$qlayer->open();
	$items = $qlayer->getItems(); //not required, use with var_dump($items);
	$shape = $qlayer->getShape(0, 0);
	$x = $shape->values['value_0'];
	$qlayer->close();
	$query = "select description from lcov_desc where cat_num = {$x}";
	if($result2 = pg_query($query)){
		$row = pg_fetch_array($result2);
		$msg = $row[0];
	} else {
		$msg = '';
	}
	//$msg = $x;
}

echo json_encode(array("result"=>$msg));
?>

