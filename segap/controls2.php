<?php
require('se_config.php');
pg_connect($pg_connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>controls2_php</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/aqtree3clickable.css" />
<link rel="stylesheet" href="../styles/custom-theme/jquery-ui-1.8.6.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" ></script>
<script type="text/javascript" src="../javascript/jquery-ui-1.8.6.custom.min.js" ></script>
<script type="text/javascript" src="../javascript/aqtree3clickable.js"></script>
<script type="text/javascript" src="../javascript/controls_tab1.js"></script>
<script type="text/javascript" src="../javascript/controls234.js"></script>
<style type="text/css">
/* <![CDATA[ */
body {padding: 0px;
   margin: 2px;}
#tabs {font-size: 11px;
   width: 315px;}
#tabs-1{ width: 270px;
   font-size: 16px;
   overflow: scroll;}
#tabs-2{ width: 283px;
   font-size: 11px;
   overflow: scroll;}
#tabs-3 {overflow: scroll;
   width: 270px;
   font-size: 16px;}
button {float: right;
   width: 120px;
   clear: both;}
span.desc {font-size: 16px;
   line-height: 2;
   }
h2 {text-align: center;
   }
hr {clear: both;}
/* ]]> */
</style>
<script language="javascript" type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
load_selections();
$("#tabs").tabs();
$("#opentab").click();
$("button").button();
var win_h = $(window).height();
$("#tabs-1,#tabs-2,#tabs-3").height(win_h - 78);

$("#lc").click(function(evt) {
   evt.preventDefault();
   lc_report();
});
$("#mgmt").click(function(evt) {
      evt.preventDefault();
      manage_report();
   });
$("#owner").click(function(evt) {
      evt.preventDefault();
      owner_report();
   });
$("#gapst").click(function(evt) {
      evt.preventDefault();
      status_report();
   });
$("#selsp").click(function(evt) {
      evt.preventDefault();
      parent.map.document.forms.fm3.submit();
   });

});
/* ]]> */
</script>
</head>
<body>
<div id="tabs">

<ul>
<li><a href="#tabs-1">View Layers</a></li>
<li><a id="opentab" href="#tabs-2">AOI Info</a></li>
<li><a id="legendtab" href="#tabs-3">Legends</a></li>
</ul>
<form  action="">
<div id="tabs-1">
    <ul class="aqtree3clickable">
<li class="aq3open"><a href="#" class="no_link">Foreground</a>
<ul>
<li><input type="checkbox" name="states"   onclick="loadlayers();" /><a>States</a></li>
<li><input type="checkbox" name="cities"  onclick="loadlayers();" /><a>Cities</a></li>
<li><input type="checkbox" name="counties"  onclick="loadlayers();" /><a>Counties</a></li>
<li><input type="checkbox" name="roads"  onclick="loadlayers();" /><a>Roads</a></li>
<li><input type="checkbox" name="basins_river"  onclick="loadlayers();" /><a>Watersheds</a></li>
<li><input type="checkbox" name="hydro"  onclick="loadlayers();" /><a>Rivers</a></li>
<li><input type="checkbox" name="bcr"  onclick="loadlayers();" /><a>BCR</a></li>
<li><input type="checkbox" name="lcc"  onclick="loadlayers();" /><a>LCC</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Stewardship</a>
<ul>
<li><input type="radio" name="steward" value="gapown"  onclick="loadlayers();" /><a href="#own" onclick="show_lgnd();">Ownership</a></li>
<li><input type="radio" name="steward" value="gapman"  onclick="loadlayers();" /><a href="#manage" onclick="show_lgnd();">Management</a></li>
<li><input type="radio" name="steward" value="gapsta"  onclick="loadlayers();" /><a href="#status"onclick="show_lgnd();" >Status</a></li>
<li><input type="radio" name="steward" value="none" checked="checked" onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
<li><a href="#" class="no_link">Background</a>
<ul>
<li><input type="radio" name="background" value="landcover"  onclick="loadlayers();" /><a href="#lcov" onclick="show_lgnd();">Land Cover</a></li>
<li><input type="radio" name="background" value="elevation" checked="checked" onclick="loadlayers();" /><a href="#elev" onclick="show_lgnd();">Elevation</a></li>
<li><input type="radio" name="background" value="none"  onclick="loadlayers();" /><a>none</a></li>
</ul>
</li>
</ul>
</div>
<div id="tabs-2">
    <h2>Land Cover</h2>
<span class="desc"> Land Cover </span><button id="lc">Calculate</button>
<h2>Stewardship</h2>
<span class="desc"> Management </span><button id="mgmt">Calculate</button>
<span class="desc"> Ownership </span><button id="owner">Calculate</button>
<span class="desc"> GAP&nbsp;Status </span><button id="gapst">Calculate</button>
<hr />
<button id="selsp"> Select Species </button>
</div>
</form>
<div id="tabs-3">
    
<h4><a href="#lcov">GAP Land Cover</a></h4>
<h4><a href="#own">Ownership (Stewardship)</a></h4>
<h4><a href="#manage">Management (Stewardship)</a></h4>
<h4><a href="#status">GAP Status (Stewardship)</a></h4>

<a name="elev"></a><br />
<h4>Elevation (meters)</h4>
<img alt="status legend" src="/graphics/segap/se_elev_legend.png" />
<br />

<a name="lcov"></a><br />
<h4>GAP Land Cover</4>
<img alt="landcover legend" src="/graphics/segap/se_lcov_1_25.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_26_50.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_51_75.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_76_100.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_101_125.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_126_150.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_151_175.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_176_200.png" />
<img alt="landcover legend" src="/graphics/segap/se_lcov_201_225.png" />
<br />

<a name="own"></a><br />
<h4>Ownership (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/segap/se_owner_legend.png" />
<br />

<br />
<a name="manage"></a><br />
<h4>Management (Stewardship)</h4>
<img alt="elevation legend" src="/graphics/segap/se_manage_legend.png" />
<br />

<a name="status"></a><br />
<h4>GAP Status (Stewardship)</h4>
<img alt="status legend" src="/graphics/segap/se_status_legend.png" />
<br />
</div>

</body>
</html>
