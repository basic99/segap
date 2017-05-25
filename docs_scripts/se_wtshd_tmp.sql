CREATE OR REPLACE FUNCTION countyarea() returns void as $$
DECLARE
    i record;
    j record;
    num integer;
    my_geom geometry;
    my_area numeric(16,3);

BEGIN
   FOR i IN  select distinct cat_name from se_wtshds_tmp LOOP
      --select into num count(*) from nc_swsd where huc_8 = i.huc_8;
         my_geom := NULL;
         RAISE NOTICE 'wtshedis %,  ', i.cat_name;
         FOR j IN SELECT wkb_geometry FROM se_wtshds_tmp WHERE cat_name = i.cat_name  LOOP
            if my_geom IS NULL
               THEN
               my_geom := multi(j.wkb_geometry);
            END IF;
            SELECT INTO my_geom multi((geomunion(my_geom, j.wkb_geometry)));
         END LOOP;
         --stac_v = i.huc_8::numeric;
        -- insert into nc_status(stac, wkb_geometry) values(stac_v, my_geom);
         insert into se_wtshds(wkb_geometry, cat_name) values (my_geom, i.cat_name);
         --select into my_area area(my_geom);         
   END LOOP;
   RAISE NOTICE 'process complete';
   Return;
END;
$$ LANGUAGE plpgsql;
