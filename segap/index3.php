<?php
$state = $_GET['state'];
$ecosys = $_GET['ecosys'];
/*
2 | Virginia                      
3 | Kentucky                      
4 | North Carolina                
5 | Tennessee                     
7 | South Carolina                
8 | Georgia                       
9 | Alabama                       
10 | Mississippi                   
11 | Florida  
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- index.html -->
<head>
<title>SE Online GAP Data Explorer Tool</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script language="javascript" type="text/javascript">
/* <![CDATA[ */


/* ]]> */
</script>
</head>

<frameset rows="*,200,40">

<frameset cols="325,*">
<frame name="controls" src="" noresize="noresize" scrolling="no" />
<frame name="map"
       src="map2.php?state=<?php echo $state; ?>&ecosys=<?php echo $ecosys; ?>&type=predefined&layers=landcover states"
                                 noresize="noresize" scrolling="no" />
</frameset>

<frameset cols="325,*,150">
<frame name="data"  noresize="noresize" src="dummy.html" scrolling="no" />
<frame name="functions"  noresize="noresize" src="dummy.html"  frameborder="0" scrolling="no" />
<frame name="refmap" src="refmap.php" noresize="noresize" scrolling="no" />
</frameset>

<frame name="logos" noresize="noresize" src="logos.html" scrolling="no" />

</frameset>

</html>