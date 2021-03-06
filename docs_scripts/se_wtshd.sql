CREATE OR REPLACE FUNCTION sewtshd() returns void as $$
DECLARE
    i record;
    j record;
    num integer;
    my_geom1 geometry;
    my_geom2 geometry;
    my_area numeric(16,3);

BEGIN
   SELECT INTO my_geom1 multi(wkb_geometry) from se_bnd where ogc_fid = 2;
   for i in select * from se_wtshds loop        
      if (intersects(my_geom1, i.wkb_geometry)) then
         SELECT INTO my_geom2 multi(intersection(my_geom1, i.wkb_geometry));         
         update se_wtshds set wkb_geometry = multi(my_geom2) where ogc_fid = i.ogc_fid;
      else
         delete from se_wtshds where ogc_fid = i.ogc_fid;
      end if;
   end loop;
   Return;
END;
$$ LANGUAGE plpgsql;
