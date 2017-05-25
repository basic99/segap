CREATE OR REPLACE FUNCTION manage_code() returns void as $$
DECLARE
  i record;
  j record;
  rec_int boolean;
BEGIN
  FOR i IN  select * from  manage_desc LOOP
     update se_steward set man_desc = i.manage_desc where  man_c_recl = i.manage_c_reclass;
  END LOOP;
END;
$$ LANGUAGE plpgsql;