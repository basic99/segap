<?php
//copy file to se_config.php and make changes as necessary

//location of GRASS raster data for webserver
$grass_raster = "/data/southeast/webserv/cellhd/";

//location of GRASS raster data for permanent
$grass_raster_perm = "/data/southeast/PERMANENT/cellhd/";

$GISBASE = "/usr/local/grass-6.4.0svn";

// copy .grassrc6 from /home/webserv
$GISRC = "/var/www/html/segap/grassrc";

$PATH = "/usr/local/grass-6.4.0svn/bin:/usr/local/grass-6.4.0svn/scripts:/usr/local/bin:/usr/bin:/bin";

//set max aoi and large bb area in square meters
$max_aoi_area =   850000000000;
$large_aoi_area = 100000000000;


$mspath = "/pub/server_temp/";

$pg_connect = "host=localhost dbname=segap user=postgres";

ini_set("log_errors", 1);
?>