CREATE OR REPLACE FUNCTION county_fips() returns void as $$
DECLARE
    i record;
    j record;
    num integer;
   

BEGIN
   FOR i IN  select  ogc_fid, state_fips2, county from se_county LOOP
      --select into num count(*) from nc_swsd where huc_8 = i.huc_8;
      --num = cast(i.state_fips as integer);
	select fips into num from se_cnty_rng where county ilike i.county and state_fips = i.state_fips2;
         
         update se_county set fips = num where ogc_fid = i.ogc_fid;
         --select into my_area area(my_geom);         
   END LOOP;
   RAISE NOTICE 'process complete';
   Return;
END;
$$ LANGUAGE plpgsql;
