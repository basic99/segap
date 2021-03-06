CREATE OR REPLACE FUNCTION steward_man_temp() returns void as $$
DECLARE
    i record;
    j record;
    k record;
    num integer;
    num_recs integer;
    loop_cnt integer;
    my_geom geometry;
    my_area numeric(16,3);
BEGIN
   FOR i IN  select distinct man_c_recl, man_desc from se_steward LOOP
     FOR k IN  select distinct state_fips, state_name from se_steward LOOP
     if i.man_c_recl <> 17 and i.man_c_recl <> 18
      then
      select into num count(*) from se_steward where man_c_recl = i.man_c_recl and state_fips = k.state_fips;  
         my_geom := NULL;
         RAISE NOTICE 'manage code is %,  and state is % and count is % ', i.man_c_recl, k.state_fips, num;
         num_recs := 0;
         loop_cnt := 0;
          FOR j IN SELECT wkb_geometry, ogc_fid FROM se_steward WHERE man_c_recl = i.man_c_recl and state_fips = k.state_fips LOOP            
            if my_geom IS NULL
               THEN
               my_geom := j.wkb_geometry;
            END IF;
            SELECT INTO my_geom multi((geomunion(my_geom, j.wkb_geometry)));
            num_recs := num_recs + 1;
            RAISE NOTICE 'table se_owner row updated, ogc_fid is % mun records %', j.ogc_fid, num_recs;
            loop_cnt := loop_cnt + 1;
            if loop_cnt = 150
               then
               loop_cnt := 0;             
               insert into se_manage(man_c_recl, man_desc, state_fips, state_name, wkb_geometry) values(i.man_c_recl, i.man_desc, k.state_fips, k.state_name, my_geom);               
               my_geom := null;
            end if;
          END LOOP;
         insert into se_manage(man_c_recl, man_desc, state_fips, state_name, wkb_geometry) values(i.man_c_recl, i.man_desc, k.state_fips, k.state_name, my_geom);
         --select into my_area area(my_geom);
         --RAISE NOTICE 'table sw_owner updated, area is %', my_area;      
         end if;
     END LOOP; 
   END LOOP;
   Return;
END;
$$ LANGUAGE plpgsql;
