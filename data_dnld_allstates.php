<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Online GAP tool links</title>
  <link rel="StyleSheet" href="styles/popups.css" type="text/css" />
  <link rel="stylesheet" href="styles/custom-theme/jquery-ui-1.8.6.custom.css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
  <script type="text/javascript" src="javascript/jquery-ui-1.8.6.custom.min.js" ></script>
  <style type="text/css">
  /* <![CDATA[ */
  h1 {
   text-align: center;
  }
  li {
   margin-left: 100px;
  }
  li.lc {
   margin-bottom: 20px;
  }
  .ui-widget {font-size: 11px;}
   button {
      width: 100px;
      margin: 100px auto 50px !important;
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
  
  /* ]]> */
  </script>
</head>
<body>
   
<h1>SEGAP full extent data download links</h1>

<ul>
   <li class="lc">Land Cover&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.basic.ncsu.edu/segap/datazip/region/lc_segap.zip">http://www.basic.ncsu.edu/segap/datazip/region/lc_segap.zip </a></li>

<?php
require('se_config.php');
pg_connect($pg_connect);

$pds = $_POST['strpds'];

if(strlen($pds) != 0){
   $spp = explode(":", $pds);
   foreach($spp as $a){
      $query = "select  strscomnam from info_spp where strsppc ilike '{$a}'";
      $result = pg_query($query);
      $rows = pg_fetch_all($result);
      printf("<li>%s&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"http://www.basic.ncsu.edu/segap/datazip/region/vert/%s_se00.zip\" >http://www.basic.ncsu.edu/segap/datazip/region/vert/%s_se00.zip</a> </li>", $rows[0]['strscomnam'], $a, $a);
   }
}



?>
</ul>
<button id="closebtn">close</button>

</body>
</html>
