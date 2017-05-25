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
   FOR i IN  select distinct  gap_status, status_desc from se_steward LOOP
     FOR k IN  select distinct state_fips, state_name from se_steward LOOP
     if i.gap_status <> 0 and i.gap_status <> 4
      then
      select into num count(*) from se_steward where gap_status = i.gap_status and state_fips = k.state_fips;  
         my_geom := NULL;
         RAISE NOTICE 'status code is %,  and state is % and count is % ', i.gap_status, k.state_fips, num;
         num_recs := 0;
         loop_cnt := 0;
          FOR j IN SELECT wkb_geometry, ogc_fid FROM se_steward WHERE  gap_status= i.gap_status and state_fips = k.state_fips LOOP            
            if my_geom IS NULL
               THEN
               my_geom := j.wkb_geometry;
            END IF;
            SELECT INTO my_geom multi((geomunion(my_geom, j.wkb_geometry)));
            num_recs := num_recs + 1;
            RAISE NOTICE 'table se_status row updated, ogc_fid is % mun records %', j.ogc_fid, num_recs;
            loop_cnt := loop_cnt + 1;
            if loop_cnt = 150
               then
               loop_cnt := 0;             
               insert into se_status(status_c, status_d, state_fips, state_name, wkb_geometry) values(i.gap_status, i.status_desc, k.state_fips, k.state_name, my_geom);               
               my_geom := null;
            end if;
          END LOOP;
         insert into se_status(status_c, status_d, state_fips, state_name, wkb_geometry) values(i.gap_status, i.status_desc, k.state_fips, k.state_name, my_geom);
         --select into my_area area(my_geom);
         --RAISE NOTICE 'table sw_owner updated, area is %', my_area;      
         end if;
     END LOOP; 
   END LOOP;
   Return;
END;
$$ LANGUAGE plpgsql;
