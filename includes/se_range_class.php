<?php
$sedbcon = pg_connect("host=localhost dbname=segap user=postgres");

class se_range_class
{
	//private $range;
   //array with spp codes of species contained in AOI
	private $unique_species;   
   //array with number of species in AOI for each prot column in table info_spp
	public $num_species;
   //array with 4 elements for total selected species in 4 animal classes
	private $tot_class;
	private $query;
   private $test;

	function __construct($aoi_name)	{
		global $sedbcon;
      if($aoi_name == 'linkedspecies'){
         $aoi_predefined['ecosys_aoi'] = 1;
      } else {
         $query = "select aoi_data from aoi where name = '{$aoi_name}'";
         $result = pg_query($sedbcon, $query);
         $row = pg_fetch_array($result);
         $aoi_predefined = unserialize($row['aoi_data']);
         
         $key_gapown = explode(":", $aoi_predefined['owner_aoi']);
         $key_gapman = explode(":", $aoi_predefined['manage_aoi']);
         $key_gapstatus = explode(":", $aoi_predefined['status_aoi']);
         $key_county = explode(":", $aoi_predefined['county_aoi']);
         $key_basin = explode(":", $aoi_predefined['basin_aoi']);
         $key_state = explode(":", $aoi_predefined['state_aoi']);
         $key_bcr = explode(":", $aoi_predefined['bcr_aoi']);
         $key_lcc = explode(":", $aoi_predefined['lcc_aoi']);
         
         if (strlen($key_gapown[0] == 0)) unset($key_gapown);
         if (strlen($key_gapman[0] == 0)) unset($key_gapman);
         if (strlen($key_county[0] == 0)) unset($key_county);
         if (strlen($key_basin[0] == 0)) unset($key_basin);
         if (strlen($key_state[0] == 0)) unset($key_state);
         if (strlen($key_bcr[0] == 0)) unset($key_bcr);
         if (strlen($key_lcc[0] == 0)) unset($key_lcc);
      
      }
      
      //get array with values that are info_spp column names and create array $cols 
      $query = "select * from info_spp";
      $result = pg_query($sedbcon, $query);
      $row = pg_fetch_assoc($result);
      $col_num = 0;
      $cols["all_species"] = 0;
      foreach($row as $key=>$val){
         if($col_num++ > 5){
            $cols[$key] = 0;
         }      
      }
      
      //check to see if can use range calculations for predefined areas
      $use_predefined = false;
      if(is_array($aoi_predefined)){
         foreach($aoi_predefined as $foo){
            if(strlen($foo) != 0){
               $use_predefined = true;
            }
         }
      }
      if($use_predefined){
         
         //get all species for ecosystem
         if($aoi_predefined['ecosys_aoi'] == 1){
            $query = "select distinct sppc from se_state_rng";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
         }
         
          //get bcr from table primary key
         //add predicted species to array $species from table se_bcr_rng
         if(isset($key_bcr)){
            foreach($key_bcr as $val){
               $query = "select bcr from se_bcr where ogc_fid = '{$val}'";
               $result = pg_query($sedbcon, $query);
               $row = pg_fetch_array($result);
               $query = "select sppc from se_bcr_rng where rngc != 0 and bcr = '{$row["bcr"]}'";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }        
         }
         
         //get key for table se_states and join with table se_states_rng
         // for list of species in array $species
         if(isset($key_state)){
            foreach($key_state as $val){
               $query = "select sppc  from se_state_rng as rng, se_states as shp
               where shp.ogc_fid = {$val} and shp.state_name = rng.state_name and rng.rngc != 0;";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         //get species for counties
         if(isset($key_county)){
            foreach($key_county as $val){
               $query = "select rng.sppc from se_cnty_rng as rng, se_county as shp
               where rng.fips = shp.fips  and shp.ogc_fid = '{$val}' and rng.rngc != 0";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         //get species for basins
         if(isset($key_basin)){
            foreach($key_basin as $val){
               $query = "select rng.cat_num, rng.sppc, rng.rngc from se_wtshd_rng as rng, se_wtshds as shp
               where shp.ogc_fid = {$val} and shp.cat_num::integer = rng.cat_num and rng.rngc != 0";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         // get species for lcc
         if(isset($key_lcc)){
            foreach($key_lcc as $val){
               $query = "select sppc from se_lcc_rng as rng, se_lcc1 as shp
               where shp.gid = {$val} and shp.lcc_code = rng.lcc_code and rng.rngc != 0";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         //get species for ownership
         if(isset($key_gapown)){
            foreach($key_gapown as $val){
               $fips = explode("|", $val);
               $fips2 = $fips[1]*100 + $fips[0];
               $query = "select * from se_owner_rng where classstate = {$fips2}";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         if(isset($key_gapman)){
            foreach($key_gapman as $val){
               $fips = explode("|", $val);
               $fips2 = $fips[1]*100 + $fips[0];
               $query = "select * from se_manage_rng where classstate = {$fips2}";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
         if(isset($key_gapstatus)){
            foreach($key_gapstatus as $val){
               $fips = explode("|", $val);
               $fips2 = $fips[1]*100 + $fips[0];
               $query = "select * from se_status_rng where classstate = {$fips2}";
               $result = pg_query($sedbcon, $query);
               while($row = pg_fetch_array($result)){
                  $species[] = $row['sppc'];
               }
            }
         }
         
      } else {
         $query1 = "select se_hex_id from se_rng_hex where intersects(
            (select wkb_geometry from aoi where name = '{$aoi_name}'),
            se_rng_hex.the_geom)";
			$result = pg_query($sedbcon, $query1);
         while($row = pg_fetch_array($result)){
            $hex_id[] = $row['se_hex_id'];
         }
         foreach($hex_id as $id){
            $query2 = "select *  from se_rng_hex_vals where hex_id = {$id}";
            $result = pg_query($sedbcon, $query2);
            while($row = pg_fetch_array($result)){
               $species[] = $row['sppc'];
            }
         }			
      }
      
      //list of species present in range by spp code
      //by removing redundant ones from array $species
      $unique_species = array_unique($species);
      $this->unique_species = $unique_species;
         
      //loop through all species in array $unique_species
      //for each species check table info_spp for status and increment value in array $cols
      foreach($unique_species as $spp){
         $query = "select * from info_spp where strsppc ilike '{$spp}'";
         $result = pg_query($sedbcon, $query);
         while($row = pg_fetch_assoc($result)){
            $cols["all_species"]++;
            $col_num = 0;
            foreach($row as $key=>$val){
               if($col_num++ > 5){
                   if($row[$key] != null){
                     $cols[$key]++;
                  }
               }  
           }
         }
      }
      
      //////////////////////////////////////////////////////////////
      $this->test =$fips;
      $this->num_species =$cols;
   }
   
  
   //construct query string that selects certain protection classes
   function num_class($species, $sel, $protcats){
		global $sedbcon;
      $totclass["mammal"] = $totclass["amphibian"] = $totclass["reptilian"] = $totclass["avain"] = 0;
		$query = "select strtaxclass, strsppc, strgname, strscomnam from info_spp ";
      if(sizeof($protcats) != 0){
         $i = 0;
         foreach($protcats as $foo){
            if($i++ == 0) {
                  $query = $query." where ({$foo} is not null";
                  $i++;
            }else{
                  $query = $query." {$sel} {$foo} is not null";
            }
         }
         $query .=")";
      }
      
      $result = pg_query($sedbcon, $query);
      while($row = pg_fetch_array($result)){
         foreach($this->unique_species as $spp){
            if(strcasecmp( $row["strsppc"], $spp) == 0){
               switch($row["strtaxclass"]){
                  case "Mammalian":
                     $totclass["mammal"]++;
                     break;
                  case "Amphibian":
                     $totclass["amphibian"]++;
                     break;
                  case "Reptilian":
                     $totclass["reptilian"]++;
                     break;
                  case "Avian":
                     $totclass["avian"]++;
                     break;
                     
               }
            }
         }
      }
      $this->query = $query;
      $this->tot_class = $totclass;
      return $this->tot_class;
   }
   
   function get_species_search($avian, $mammal, $reptile, $amphibian, $language, $search){
      global $sedbcon;
      $query = $this->query;
      if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
      $query .= " order by strsort";
      $result = pg_query($sedbcon, $query);
      while($row = pg_fetch_array($result)){
         foreach($this->unique_species as $spp){
            switch ($language){
					case "strscomnam":
						$display = strtolower($row[$language]);
						break;
					case "strgname":
						$display = ucfirst($row[$language]);
						break;
				}
            if(strcasecmp( $row["strsppc"], $spp) == 0){
               if((strcasecmp($row['strtaxclass'], "Mammalian") == 0) && $mammal == 'on'){
                  $line .= sprintf("<option value='%s'>%s</option>", $row['strsppc'], $display);
               }
               if((strcasecmp($row['strtaxclass'], "Amphibian") == 0) && $amphibian == 'on'){
                  $line .= sprintf("<option value='%s'>%s</option>", $row['strsppc'], $display);
               }
               if((strcasecmp($row['strtaxclass'], "Reptilian") == 0) && $reptile == 'on'){
                  $line .= sprintf("<option value='%s'>%s</option>", $row['strsppc'], $display);
               }
               if((strcasecmp($row['strtaxclass'], "Avian") == 0) && $avian == 'on'){
                  $line .= sprintf("<option value='%s'>%s</option>", $row['strsppc'], $display);
               }
            }
         }
      }
      return $line;
   }
   
   function get_species_dnld($avian, $mammal, $reptile, $amphibian, $search){
      global $sedbcon;
      $query = $this->query;
       if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
      $query .= " order by strsort";
      $result = pg_query($sedbcon, $query);
      while($row = pg_fetch_array($result)){
         foreach($this->unique_species as $spp){           
            if(strcasecmp( $row["strsppc"], $spp) == 0){
               if((strcasecmp($row['strtaxclass'], "Mammalian") == 0) && $mammal == 'on'){
                  echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strsppc']."' /></td><td>".$row['strscomnam']."</td></tr>";
               }
               if((strcasecmp($row['strtaxclass'], "Amphibian") == 0) && $amphibian == 'on'){
                  echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strsppc']."' /></td><td>".$row['strscomnam']."</td></tr>";
               }
               if((strcasecmp($row['strtaxclass'], "Reptilian") == 0) && $reptile == 'on'){
                  echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strsppc']."' /></td><td>".$row['strscomnam']."</td></tr>";
               }
               if((strcasecmp($row['strtaxclass'], "Avian") == 0) && $avian == 'on'){
                  echo "<tr><td><input type='checkbox' onclick='poll();' name='pds' value='".$row['strsppc']."' /></td><td>".$row['strscomnam']."</td></tr>";
               }
            }
         }
      }
      return $line;
   }
   
   function get_species_ss($avian, $mammal, $reptile, $amphibian, $search, $protcats){
      $protcat_text = array(
           "usesa_code" => "Federally Listed",
           "strsprotal" => "Al state listed",
           "strsprotfl" => "FL state listed",
           "strsprotga" => "GA state listed",
           "strsprotky" => "KY state listed",
           "strsprotms" => "MS state listed",
           "strsprotnc" => "NC state listed",
           "strsprotsc" => "SC state listed",
           "strsprottn" => "TN state listed",
           "strsprotva" => "VA state listed",
           "strgrank2" => "NS Global priority",
           "strsrankal2" => "NS AL priority",
           "strsrankfl2" => "NS FL priority",
           "strsrankga2" => "NS GA priority",
           "strsrankky2" => "NS KY priority",
           "strsrankms2" => "NS MS priority",
           "strsranknc2" => "NS NC priority",
           "strsranksc2" => "NS SC priority",
           "strsranktn2" => "NS TN priority",
           "strsrankva2" => "NS VA priority",
           "strsgcnal" => "AL SGCN",
           "strsgcnfl" => "FL SGCN",
           "strsgcnga" => "GA SGCN",
           "strsgcnky" => "KY SGCN",
           "strsgcnms" => "MS SGCN",
           "strsgcnnc" => "NC SGCN",
           "strsgcnsc" => "SC SGCN",
           "strsgcntn" => "TN SGCN",
           "strsgcnva" => "VA SGCN"
                            );
      
      $somecontent = "sppcode \t scientific name \t commom name \t ";
      foreach(json_decode($protcats) as $protcat) {
         $somecontent .= $protcat_text[$protcat]."\t";
      }
      $somecontent .= "\n";
		$report_name = "report".rand(0,999999).".xls";
      //open file for writing and write column headers
		$handle = fopen("/pub/server_temp/{$report_name}", "w+");
		
		fwrite($handle, $somecontent);
      
		global $sedbcon;
      $query = $this->query;
      if(strpos($query, "where") === false){
			$query .= " where strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%'" ;
		} else {
			$query .= " and (strscomnam ilike '%{$search}%'";
			$query .= " or strgname ilike '%{$search}%')" ;
		}
      $query .= " order by strsort";
      
      function write_row($handle2, $spp2, $strgname2, $strcomnam2, $protcats2) {
         global $sedbcon;
         fwrite($handle2, $spp2."\t".$strgname2."\t".$strcomnam2);
         foreach(json_decode($protcats2) as $protct2) {
            $query = "select {$protct2} from info_spp where strsppc ilike '%{$spp2}%'";
            $result = pg_query($sedbcon, $query);
            $row = pg_fetch_row($result);
            fwrite($handle2, "\t".$row[0]);
         }
        fwrite($handle2, "\n");
      }
      
      $result = pg_query($sedbcon, $query);
       while($row = pg_fetch_array($result)){
         foreach($this->unique_species as $spp){           
            if(strcasecmp( $row["strsppc"], $spp) == 0){
               if((strcasecmp($row['strtaxclass'], "Mammalian") == 0) && $mammal == 'on'){
                  write_row($handle, $spp, $row['strgname'], $row['strscomnam'], $protcats);
               }
               if((strcasecmp($row['strtaxclass'], "Amphibian") == 0) && $amphibian == 'on'){
                  write_row($handle, $spp, $row['strgname'], $row['strscomnam'], $protcats);
               }
               if((strcasecmp($row['strtaxclass'], "Reptilian") == 0) && $reptile == 'on'){
                 write_row($handle, $spp, $row['strgname'], $row['strscomnam'], $protcats);
               }
               if((strcasecmp($row['strtaxclass'], "Avian") == 0) && $avian == 'on'){
                 write_row($handle, $spp, $row['strgname'], $row['strscomnam'], $protcats);
               }
            }
         }
      }

		
      
      fclose($handle);
      return $report_name;
   }
   
    function test(){
      var_dump($this->test);
	}
}




?>