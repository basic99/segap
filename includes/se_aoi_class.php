<?php
require("se_config.php");
$sebdcon = pg_connect($pg_connect);

date_default_timezone_set('America/New_York');

// ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/html/segap/logs/php-error.log");
error_log("aoi_report_ajax");


//////////////////////////////////////////////////////////////////////////////////
// this class has a constructor that takes as a parameter an AOI name
// the constructor calculates the bounding box and imports a mask into GRASS
// various functions that depend on the AOI can then be called
///////////////////////////////////////////////////////////////////////////////


putenv("GISBASE={$GISBASE}");
putenv("GISRC={$GISRC}");
putenv("PATH={$PATH}");

class se_aoi_class{

	private $aoi_name;
	public $mask_name;
	private $min_x;
	private $min_y;
	private $max_x;
	private $max_y;
	private $area;
	//public  $max_area_exception;

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $a
	 */
	public function __construct($a) {

		global $sedbcon;

		//import max aoi area as global from sw_config.php
		global $max_aoi_area;

		//assign parameter class variable
		$this->aoi_name = $a;

		//get max extents of aoi
		$query_fid = "select ogc_fid from aoi where name='{$this->aoi_name}'";
		$result_i = pg_query($sedbcon, $query_fid);
		$min_x = $min_y = 9999999;
		$max_x = $max_y =  -9999999;
		while ($row_i = pg_fetch_array($result_i)){

			$query_minx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where ogc_fid={$row_i[0]}";
			$query_miny = "select y(pointn(exteriorring(envelope(wkb_geometry)), 1)) from aoi where ogc_fid={$row_i[0]}";
			$query_maxx = "select x(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where ogc_fid={$row_i[0]}";
			$query_maxy = "select y(pointn(exteriorring(envelope(wkb_geometry)), 3)) from aoi where ogc_fid={$row_i[0]}";

			$result = pg_query($sedbcon, $query_minx);
			$row = pg_fetch_array($result);
			$this->min_x = min($row[0], $min_x);
			// $min_x = $row[0]-10000;
			$min_x = $this->min_x;

			$result = pg_query($sedbcon, $query_miny);
			$row = pg_fetch_array($result);
			$this->min_y = min($row[0], $min_y);
			// $min_y = $row[0] - 10000;
			$min_y = $this->min_y;

			$result = pg_query($sedbcon, $query_maxx);
			$row = pg_fetch_array($result);
			$this->max_x = max($row[0], $max_x);
			//$max_x = $row[0] + 10000;
			$max_x = $this->max_x;

			$result = pg_query($sedbcon, $query_maxy);
			$row = pg_fetch_array($result);
			$this->max_y = max($row[0], $max_y);
			// $max_y = $row[0] + 10000;
			$max_y =  $this->max_y;
		}
		$this->area = ($max_x - $min_x) * ($max_y - $min_y);
		if ($this->area > $max_aoi_area) {
			//throw new large_aoi_exception($this->area);
		}

		//check if can use mask already in GRASS
		$query = "select ogc_fid, aoi_data from aoi where name='{$this->aoi_name}'";
		$result = pg_query($sedbcon, $query);
		$row = pg_fetch_array($result);
		if (!empty($row['aoi_data'])) {
			$aoi_data = unserialize($row['aoi_data']);

			if ($aoi_data['ecosys_aoi'] == 1) {
				$this->mask_name = 'ecosys';
				return;
			}
			switch ($aoi_data['state_aoi']){
				case "2":
					$this->mask_name = 'Virginia';
					return;
				case "3":
					$this->mask_name = 'Kentucky';
					return;
				case "4":
					$this->mask_name = 'North_Carolina';
					return;
				case "5":
					$this->mask_name = 'Tennessee';
					return;
				case "7":
					$this->mask_name = 'South_Carolina';
					return;
				case "8":
					$this->mask_name = 'Georgia';
					return;
				case "9":
					$this->mask_name = 'Alabama';
					return;
				case "10":
					$this->mask_name = 'Mississippi';
					return;
				case "11":
					$this->mask_name = 'Florida';
					return;

			}
			switch ($aoi_data['bcr_aoi']) {
				case "1":
					$this->mask_name = 'CENTRAL_HARDWOODS';
					return;
				case "2":
					$this->mask_name = 'MISSISSIPPI_ALLUVIAL_VALLEY';
					return;
				case "3":
					$this->mask_name = 'SOUTHEASTERN_COASTAL_PLAIN';
					return;
				case "4":
					$this->mask_name = 'APPALACHIAN_MOUNTAINS';
					return;
				case "5":
					$this->mask_name = 'PIEDMONT';
					return;
				case "6":
					$this->mask_name = 'PENINSULAR_FLORIDA';
					return;
			}

			switch ($aoi_data['lcc_aoi']) {
				case "1":
					$this->mask_name = 'appalachian_lcc';
					return;
				case "2":
					$this->mask_name = 'northatlantic_lcc';
					return;
				case "3":
					$this->mask_name = 'southatlantic_lcc';
					return;
				case "4":
					$this->mask_name = 'gulfcoast_lcc';
					return;
				case "5":
					$this->mask_name = 'florida_lcc';
					return;
			}
		}

		//create name for mask
		$blank_file = aoi.rand(0,9999999);
		$blank = "/pub/server_temp/".$blank_file;
		$this->mask_name = $blank_file;

		//copy blank file to rectangle of AOI
		$gdal_cmd1 = "/usr/local/bin/gdal_translate -of GTiff -projwin {$min_x} {$max_y} {$max_x} {$min_y} /var/www/html/data/segap/se_blank {$blank} &>/dev/null";
		error_log($gdal_cmd1);
		system($gdal_cmd1);

		//burn aoi into blank file
		$gdal_cmd = "/usr/local/bin/gdal_rasterize -burn 1 -sql \"SELECT AsText(wkb_geometry) FROM  aoi  where aoi.name='{$this->aoi_name}' \"   PG:\"host=localhost port=5432 dbname=segap user=postgres\"  {$blank} &>/dev/null";
		system($gdal_cmd);
		error_log($gdal_cmd);

		//import mask into GRASS
		$grass_cmd=<<<GRASS_SCRIPT
g.region -d &>/dev/null
r.in.gdal input={$blank} output={$blank_file}a &>/dev/null
cat /var/www/html/segap/grass/mask_recl | r.reclass input={$blank_file}a output={$blank_file} &>/dev/null
GRASS_SCRIPT;
error_log($grass_cmd);
		system($grass_cmd);
      $fp = fopen("/pub/server_temp/testcmd", w);
      //fwrite($fp, $grass_cmd);
      fclose($fp);

}
public function get_area(){
	return $this->area;
}
// function for testing only
public function show_vars(){
	echo $this->aoi_name."<br>";
	echo $this->mask_name."<br>";
	echo $this->min_x."<br>";
	echo $this->min_y."<br>";
	echo $this->max_y."<br>";
	echo $this->max_x."<br>";

}

// getter functions for max extent of AOI
public function get_minx(){
	return $this->min_x;
}

public function get_maxx(){
	return $this->max_x;
}

public function get_miny(){
	return $this->min_y;
}

public function get_maxy(){
	return $this->max_y;
}

/////////////////////////////////////////////////////////////////////////
//functions that print reports for all AOI, not dependant on species
//////////////////////////////////////////////////////////////////////////


public function aoi_landcover(){
	$report_name = "report".rand(0,999999);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_lc = '{$this->mask_name}  * se_landcover' &>/dev/null
cat /var/www/html/segap/grass/se_lcov_recl | r.reclass input={$this->mask_name}calc_lc output={$this->mask_name}recl_lc &>/dev/null
r.report -n map={$this->mask_name}recl_lc units=a,h,p 2>/dev/null
GRASS_SCRIPT;
error_log($str);
	return `$str`;
}

public function aoi_management(){
	$report_name = "report".rand(0,999999);
	$str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_man = '{$this->mask_name}  * se_manage' &>/dev/null
cat /var/www/html/segap/grass/se_manage_recl | r.reclass input={$this->mask_name}calc_man output={$this->mask_name}recl_man &>/dev/null
r.report -n map={$this->mask_name}recl_man units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

public function aoi_ownership(){
	$report_name = "report".rand(0,999999);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_own = '{$this->mask_name}  * se_owner' &>/dev/null
cat /var/www/html/segap/grass/se_owner_recl | r.reclass input={$this->mask_name}calc_own output={$this->mask_name}recl_own &>/dev/null
r.report -n map={$this->mask_name}recl_own units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	return `$str`;
}

public function aoi_status(){
	$report_name = "report".rand(0,999999);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}calc_stat = '{$this->mask_name}  * se_status' &>/dev/null
cat /var/www/html/segap/grass/se_status_recl | r.reclass input={$this->mask_name}calc_stat output={$this->mask_name}recl_stat &>/dev/null
r.report -n map={$this->mask_name}recl_stat units=a,h,p 2>/dev/null
GRASS_SCRIPT;
	//system($str);
	//echo "</pre>";
	return `$str`;
}

/////////////////////////////////////////////////////////////////////////////
//functions that print reports for  AOI, are dependant on species
////////////////////////////////////////////////////////////////////////////

public function predicted($a){
	$report_name = "report".rand(0,999999);

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x}
r.mapcalc {$this->mask_name}calc_pred = '{$this->mask_name}  *{$raster}' &>/dev/null
cat /var/www/html/segap/grass/se_pred_recl | r.reclass input={$this->mask_name}calc_pred output={$this->mask_name}recl_pred
r.report -n map={$this->mask_name}recl_pred units=a,h,p | tee /data/server_temp{$report_name}
GRASS_SCRIPT;

	$rep = `$str`;
	return $rep;
}

public function species_status($a){
	$report_name = "report".rand(0,999999);

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x}
r.mapcalc {$this->mask_name}calc_stat_sp = '{$this->mask_name}  * if({$raster}) * se_status' &>/dev/null
cat /var/www/html/segap/grass/se_status_recl | r.reclass input={$this->mask_name}calc_stat_sp output={$this->mask_name}recl_stat_sp
r.report -n map={$this->mask_name}recl_stat_sp units=a,h,p | tee /data/server_temp{$report_name}
GRASS_SCRIPT;

   $rep = `$str`;
	return $rep;

}

public function species_ownership($a){
	$report_name = "report".rand(0,999999);

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x}
r.mapcalc {$this->mask_name}calc_own_sp = '{$this->mask_name}  * if({$raster}) * se_owner' &>/dev/null
cat /var/www/html/segap/grass/se_owner_recl | r.reclass input={$this->mask_name}calc_own_sp output={$this->mask_name}recl_own_sp
r.report -n map={$this->mask_name}recl_own_sp units=a,h,p | tee /data/server_temp{$report_name}
GRASS_SCRIPT;

   $rep = `$str`;
	return $rep;

}

public function species_management($a){
	$report_name = "report".rand(0,999999);

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x}
r.mapcalc {$this->mask_name}calc_man_sp = '{$this->mask_name}  * if({$raster}) *  se_manage' &>/dev/null
cat /var/www/html/segap/grass/se_manage_recl | r.reclass input={$this->mask_name}calc_man_sp output={$this->mask_name}recl_man_sp
r.report -n map={$this->mask_name}recl_man_sp units=a,h,p | tee /data/server_temp{$report_name}
GRASS_SCRIPT;

   $rep = `$str`;
	return $rep;

}

public function species_landcover($a){
	$report_name = "report".rand(0,999999);

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);
	$str=<<<GRASS_SCRIPT

g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x}
r.mapcalc {$this->mask_name}calc_lc_sp = '{$this->mask_name}  * if({$raster}) *  se_landcover' &>/dev/null
cat /var/www/html/segap/grass/se_lcov_recl | r.reclass input={$this->mask_name}calc_lc_sp output={$this->mask_name}recl_lc_sp
r.report -n map={$this->mask_name}recl_lc_sp units=a,h,p | tee /data/server_temp{$report_name}
GRASS_SCRIPT;

   $rep = `$str`;
	return $rep;
}

//////////////////////////////////////////////////////////////////////////////////////
//functions that return handle to map created for single species
//////////////////////////////////////////////////////////////////////////////////////

public function landcover_map($a){

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x}
r.mapcalc {$map} = 'if({$raster}) *  se_landcover_recl' &>/dev/null
cat  /var/www/html/segap/grass/se_lcov_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;

	system($str);
	return $map;
}

public function ownership_map($a){

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x}
r.mapcalc {$map} = 'if({$raster}) *  se_owner_recl' &>/dev/null
cat  /var/www/html/segap/grass/se_owner_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

public function protection_map($a){

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x}
r.mapcalc {$map} = 'if({$raster}) *  se_status' &>/dev/null
cat  /var/www/html/swgap/grass/sw_sta_color | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

public function management_map($a){

	//convert strelcode to raster name
	$raster = "d_".strtolower($a);

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);

	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x}
r.mapcalc {$map} = 'if({$raster}) *  se_manage_recl' &>/dev/null
cat  /var/www/html/segap/grass/se_manage_color  | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

//function that returns handle to map created for richness
//accepts as parameter colon delimted species list

public function richness($a){

	//global $sedbcon;

	$species = explode(":", $a);
	foreach($species as $foo){
      $raster = "d_".strtolower($foo);
      $layers[] = "if({$raster})";
   }
	$layer_str = implode(" + ", $layers);
	$rules_file = "/var/www/html/swgap/grass/richness_rule";

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;

	//create map name
	$map = "map".rand(0,999999);
	$str=<<<GRASS_SCRIPT
g.region n={$max_y} s={$min_y} w={$min_x} e={$max_x}
r.mapcalc  {$map} = '{$layer_str}' &>/dev/null
cat {$rules_file} | r.colors map={$map} color=rules &>/dev/null
GRASS_SCRIPT;
	system($str);
	return $map;
}

public function richnessexport($a){
		$map = "richness".rand(0,9999999).".tif";
		$str=<<<GRASS_SCRIPT
r.out.gdal input={$a} format=GTiff type=Byte output=/pub/richness_export/{$map}  &>/dev/null
GRASS_SCRIPT;
		system($str);
		return $map;

}

public function richnessreport($a){
  $species = explode(":", $a);
	foreach($species as $foo){
      $raster = "d_".strtolower($foo);
      $layers[] = "if({$raster})";
   }
	$layer_str = implode(" + ", $layers);
	//$rules_file = "/var/www/html/swgap/grass/richness_rule";

	//calculate 10% padding
	$x_pad = ($this->max_x - $this->min_x) * 0.1;
	$y_pad = ($this->max_y - $this->min_y) * 0.1;
	$max_x = $this->max_x + $x_pad;
	$min_x = $this->min_x - $x_pad;
	$max_y = $this->max_y + $y_pad;
	$min_y = $this->min_y - $y_pad;
   //var_dump($layers);
   //echo "<pre>";
   $str=<<<GRASS_SCRIPT
g.region n={$this->max_y} s={$this->min_y} w={$this->min_x} e={$this->max_x} &>/dev/null
r.mapcalc {$this->mask_name}richness_report = '{$this->mask_name}  *({$layer_str})' &>/dev/null
r.report -n map={$this->mask_name}richness_report units=a,h,p 2>/dev/null
GRASS_SCRIPT;

   //system($str);
   //echo "</pre>";
   $rep = `$str`;
   return $rep;
}


}

?>