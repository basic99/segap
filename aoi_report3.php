<?php
require('se_aoi_class.php');
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>AOI GRASS Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<link rel="stylesheet" href="styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
@media print {
  #btncont {display: none; }
}
  
.ui-widget {
  font-size: 11px;}
button {
  width: 100px;
  margin: 20px;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
var start;
$(function() {
   $("button").button();
  $("#prnrep").click(function(evt) {
         evt.preventDefault();
			window.print();
      });
  $("#sprdsht").click(function(evt) {
         evt.preventDefault();
			spreadsheet();
      });
  $("#cls").click(function(evt) {
         evt.preventDefault();
			window.close();
      });
  
	var aoiname = $('#aoiname').val();
	var report = $('#report').val();
	var species = $('#species').val();
	var sppcode = $('#sppcode').val();
	var reportid = $('#reportid').val();

	$.ajax({
		type: "POST",
		url: "aoi_report_ajax.php",
		data: { aoiname: aoiname, report: report, species: species, sppcode: sppcode, reportid: reportid },
		timeout: 20000,
		complete: function(){
			$('#somecontent').append("<p>Report successfully submitted.</p>");
		}
	});


	$.post("aoi_report_ajax2.php", {reportid: reportid},
	function(data){
		$('#timer').html("0");
		start = data.time;
		timeout(reportid);
	}, "json");

});

function timeout(reportidp){
	$.post("aoi_report_ajax2.php", { reportid: reportidp},
	function(data){
		var elapsed = data.time - start;
		$('#timer').html("<p>Report running time is " + elapsed + " seconds</p>");
		if(data.status){
			$('#somecontent').hide().html(data.rep).show("normal");
			$('#timer').html("<p>Report run time was " + elapsed + " seconds</p> ");
		} else {
			setTimeout("timeout(" + reportidp + ")", 5000);
		}
	}, "json");

}


function spreadsheet(){
	var pretag = document.getElementsByTagName("pre");
	var content = pretag[0].innerHTML;
	$.ajax({
		type: "POST",
		url: "aoi_report_ss.php",
		data: { content: content },
		dataType: "json",
		success: function(data){
			document.forms[0].action = "/server_temp/" + data.ssreport;
			document.forms[0].submit();
		}
	});


}
/* ]]> */
</script>
</head>
<body>

<?php
$aoi_name = $_POST['aoi_name'];
$a = $_SESSION[$aoi_name];

$report = $_POST['report'];
$sppcode = $_POST['sppcode'];
$species = stripslashes($_POST['species']);
$reportid = rand(100000000, 999999999);
?>

<div id="somecontent">
<h1>Generating report</h1>

<img alt="loading icon"  src="/graphics/ncgap/ajax-loader.gif" />
<br>
<br>
<br>
</div>
<div id="timer">

</div>

<div id="btncont">
<button id="prnrep" >Print report</button>
<button id="sprdsht">Spreadsheet</button>
<button id="cls">Close</button>
</div>

<form target="_blank" method="GET">
</form>

<form >
<input  type="hidden" name="aoi_name" id="aoiname" value="<?php echo $aoi_name; ?>" /> 
<input type="hidden" name="report" id="report" value="<?php echo $report; ?>" />
<input type="hidden" name="species" id="species" value="<?php echo $species ?>" />
<input type="hidden" name="sppcode" id="sppcode" value="<?php echo $sppcode ?>" />
<input type="hidden" name="reportid" id="reportid" value="<?php echo $reportid ?>" />
<input type="hidden" name="content"  />
</form>

</body>
</html>
