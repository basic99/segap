<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Listing Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="StyleSheet" href="styles/popups.css" type="text/css" />
<link rel="stylesheet" href="styles/custom-theme/jquery-ui-1.8.6.custom.css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
  <script type="text/javascript" src="javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<style type="text/css">
/* <![CDATA[ */
td {width: 200px;}
h3 {text-align: left;}
.ui-widget {font-size: 11px;}
   button {
      width: 100px;
      margin: 50px auto 50px !important;
      display: block !important;
      }
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {   
      $('button').button().click(function() {
         window.close();
      });
      
   });
function set_view(){
   var taxclass = document.getElementById('taxclas').value;
   //alert(taxclass);
   if(taxclass != 'AVES'){
      document.getElementById('pif').style.display = 'none';
   }
}
/* ]]> */
</script>
</head>
<body onload="set_view();">

<?php
require("se_config.php");
pg_connect($pg_connect);

//var_dump($_POST);

$sppcode = strtolower($_POST['sppcode']);
$species = stripslashes($_POST['species']);
$query = "select * from info_spp where strsppc  ilike '{$sppcode}'";
$result = pg_query($query);
$row = pg_fetch_array($result);



//var_dump($row);

?>
<input type="hidden" id="taxclas" value="<?php echo $row['strtaxclas']; ?>" />
<h3><?php echo $row['strscomnam']; ?><br /><i><?php echo $row['strgname']; ?></i></h3>
<hr />
<table>
<tr>
<td><a href="/listcodes/FederalStatusCodes.html" target="fedcodes" onclick="window.open('', 'fedcodes', 'menubar=no,height=150,width=520')"><b>Federal Status</b></a></td> 

<td><?php 
if(strlen($row['usesa_code']) == 0) {
   echo "---";
}else{
   echo $row['usesa_code']; 
} 
?></td>
<tr>

<td colspan="2"><a href="/listcodes/SEStateStatusCodes.html" target="statecodes" onclick="window.open('', 'statecodes', 'menubar=no,height=400,width=720,scrollbars')"><b>State Status</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;Alabama </td>
<td>
<?php 
if(strlen($row['strsprotal']) == 0) {
   echo "---";
}else{
   echo $row['strsprotal']; 
}

?></td>
</tr>

<tr>
<td>&nbsp;&nbsp;Florida </td>
<td>
<?php 
if(strlen($row['strsprotfl']) == 0) {
   echo "---";
}else{
   echo $row['strsprotfl']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Georgia </td>
<td>
<?php  
if(strlen($row['strsprotga']) == 0) {
   echo "---";
}else{
   echo $row['strsprotga']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Kentucky </td>

<td>
<?php 
if(strlen($row['strsprotky']) == 0) {
   echo "---";
}else{
   echo $row['strsprotky']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Mississippi </td>
<td>
<?php 
if(strlen($row['strsprotms']) == 0) {
   echo "---";
}else{
   echo $row['strsprotms']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;North Carolina </td>
<td>
<?php 
if(strlen($row['strsprotnc']) == 0) {
   echo "---";
}else{
   echo $row['strsprotnc']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;South Carolina </td>
<td>
<?php 
if(strlen($row['strsprotsc']) == 0) {
   echo "---";
}else{
   echo $row['strsprotsc']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Tennessee </td>
<td>
<?php 
if(strlen($row['strsprottn']) == 0) {
   echo "---";
}else{
   echo $row['strsprottn']; 
}

?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Virginia </td>
<td>
<?php 
if(strlen($row['strsprotva']) == 0) {
   echo "---";
}else{
   echo $row['strsprotva']; 
}

?>
</td>
</tr>
<tr>
<td colspan="2"><a href="http://www.natureserve.org/explorer/ranking.htm" target="nserv" onclick="window.open('', 'nserv', 'menubar=no,scrollbars=yes,width=800')"><b>Nature Serve Rank</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;Global Rank</td>
<td>
<?php 
if(strlen($row['strgrank']) == 0) {
   echo "---";
}else{
   echo $row['strgrank']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;AL State Rank</td>
<td>
<?php 
if(strlen($row['strsrankal']) == 0) {
   echo "---";
}else{
   echo $row['strsrankal']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;FL State Rank</td>
<td>
<?php  
if(strlen($row['strsrankfl']) == 0) {
   echo "---";
}else{
   echo $row['strsrankfl']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;GA State Rank</td>

<td>
<?php  
if(strlen($row['strsrankga']) == 0) {
   echo "---";
}else{
   echo $row['strsrankga']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;KY State Rank</td>
<td>
<?php
if(strlen($row['strsrankky']) == 0) {
   echo "---";
}else{
   echo $row['strsrankky']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;MS State Rank</td>
<td>
<?php 
if(strlen($row['strsrankms']) == 0) {
   echo "---";
}else{
   echo $row['strsrankms']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;NC State Rank</td>
<td>
<?php 
if(strlen($row['strsranknc']) == 0) {
   echo "---";
}else{
   echo $row['strsranknc']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;SC State Rank</td>
<td>
<?php 
if(strlen($row['strsranksc']) == 0) {
   echo "---";
}else{
   echo $row['strsranksc']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;TN State Rank</td>
<td>
<?php 
if(strlen($row['strsranktn']) == 0) {
   echo "---";
}else{
   echo $row['strsranktn']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;VA State Rank</td>
<td>
<?php 
if(strlen($row['strsrankva']) == 0) {
   echo "---";
}else{
   echo $row['strsrankva']; 
}
?>
</td>

</tr>
<tr>
<td colspan="2"><a href="/listcodes/SGCNStatusCodes.html" target="sgcncodes" onclick="window.open('', 'sgcncodes', 'menubar=no,height=150,width=720')"><b>Species of Greatest Conservation Need</b></a></td>
</tr>
<tr>
<td>&nbsp;&nbsp;AL SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnal']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnal']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;FL SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnfl']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnfl']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;GA SGCN</td>

<td>
<?php 
if(strlen($row['strsgcnga']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnga']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;KY SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnky']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnky']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;MS SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnms']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnms']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;NC SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnnc']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnnc']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;SC SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnsc']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnsc']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;TN SGCN</td>
<td>
<?php 
if(strlen($row['strsgcntn']) == 0) {
   echo "---";
}else{
   echo $row['strsgcntn']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;VA SGCN</td>
<td>
<?php 
if(strlen($row['strsgcnva']) == 0) {
   echo "---";
}else{
   echo $row['strsgcnva']; 
}
?>
</td>

</tr>
</table>

<div id="pif">
<table>
<tr><td colspan="2"><a href="/listcodes/PIFStatusCodes.html" target="pifcodes" onclick="window.open('', 'pifcodes', 'menubar=no,height=550,width=720')"><b>Partners-In-Flight</b></a></td></tr>
<tr>
<td>&nbsp;&nbsp;Great Basin</td>
<td>
<?php 
if(strlen($row['strpif09']) == 0) {
   echo "---";
}else{
   echo $row['strpif09']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;N. Rockies</td>

<td>
<?php  
if(strlen($row['strpif10']) == 0) {
   echo "---";
}else{
   echo $row['strpif10']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;S. Rockies / CO Plateau</td>
<td>
<?php  
if(strlen($row['strpif16']) == 0) {
   echo "---";
}else{
   echo $row['strpif16']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Shortgrass Prairie</td>
<td>
<?php 
if(strlen($row['strpif18']) == 0) {
   echo "---";
}else{
   echo $row['strpif18']; 
}
?>
</td>

</tr>
<tr>
<td>&nbsp;&nbsp;Sonoran and Mohave Dst.</td>
<td>
<?php  
if(strlen($row['strpif33']) == 0) {
   echo "---";
}else{
   echo $row['strpif33']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Sierra Madre Occidental</td>
<td>
<?php 
if(strlen($row['strpif34']) == 0) {
   echo "---";
}else{
   echo $row['strpif34']; 
}
?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;Chihuahuan Desert</td>

<td>
<?php 
if(strlen($row['strpif35']) == 0) {
   echo "---";
}else{
   echo $row['strpif35']; 
}
?>
</td>
</tr>
</table>
</div>

<button>close</button>

</body>
</html>
