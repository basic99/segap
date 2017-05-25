CREATE OR REPLACE FUNCTION serivers() returns void as $$
DECLARE
    i record;
    j record;
    num integer;
    my_geom1 geometry;
    my_geom2 geometry;
    my_area numeric(16,3);

BEGIN
   for i in select * from se_rivers loop      
      SELECT INTO my_geom1 wkb_geometry from se_bnd where ogc_fid = 2;
      if (intersects(my_geom1, i.wkb_geometry)) then
         --SELECT INTO my_geom2 intersection(my_geom1, i.wkb_geometry);
         --if (not isempty(my_geom2)) and dimension(my_geom2)  = 1 then         
           -- update se_roads set wkb_geometry = my_geom2 where ogc_fid = i.ogc_fid;
         --end if;
      else
         delete from se_rivers where ogc_fid = i.ogc_fid;
      end if;
   end loop;
   Return;
END;
$$ LANGUAGE plpgsql;
