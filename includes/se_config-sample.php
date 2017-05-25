<?php
//copy file to se_config.php and make changes as necessary

//location of GRASS raster data for webserver
$grass_raster = "/data2/southeast/webserv/cellhd/";

//location of GRASS raster data for permanent
$grass_raster_perm = "/data2/southeast/PERMANENT/cellhd/";

$GISBASE = "/usr/local/grass-6.2.1";

// copy .grassrc6 from /home/webserv
$GISRC = "/var/www/html/segap/grassrc";

$PATH = "/usr/local/grass-6.2.1/bin:/usr/local/grass-6.2.1/scripts:/usr/local/bin:/usr/bin:/bin";

//set max aoi and large bb area in square meters
$max_aoi_area =   850000000000;
$large_aoi_area = 100000000000;
?>