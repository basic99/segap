<?php
$reportid = $_POST['reportid'];
$now = time();
require('se_config.php');

$sedbcon = pg_connect($pg_connect);
$query = "select report from se_reports2 where reportid = {$reportid}";
$result = pg_query($sedbcon, $query);
if($row = pg_fetch_array($result))	{
	$report = $row['report'];
	$status = true;
} else {
	$status = false;
}

echo json_encode(array("time"=>$now, "status"=>$status, "rep"=>$report));die();

?>