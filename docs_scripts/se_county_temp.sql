CREATE OR REPLACE FUNCTION countyarea() returns void as $$
DECLARE
    i record;
    j record;
    num integer;
    my_geom geometry;
    my_area numeric(16,3);

BEGIN
   FOR i IN  select distinct state, state_fips, county from se_county_temp LOOP
      --select into num count(*) from nc_swsd where huc_8 = i.huc_8;
         my_geom := NULL;
         RAISE NOTICE 'state is %,  and county is %', i.state,  i.county;
         FOR j IN SELECT wkb_geometry FROM se_county_temp WHERE state = i.state and county = i.county LOOP
            if my_geom IS NULL
               THEN
               my_geom := multi(j.wkb_geometry);
            END IF;
            SELECT INTO my_geom multi((geomunion(my_geom, j.wkb_geometry)));
         END LOOP;
         --stac_v = i.huc_8::numeric;
        -- insert into nc_status(stac, wkb_geometry) values(stac_v, my_geom);
         insert into se_county(wkb_geometry, state, state_fips, county) values (my_geom, i.state, i.state_fips, i.county);
         --select into my_area area(my_geom);         
   END LOOP;
   RAISE NOTICE 'process complete';
   Return;
END;
$$ LANGUAGE plpgsql;
