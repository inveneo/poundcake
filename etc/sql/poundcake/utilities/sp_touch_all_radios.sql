DROP PROCEDURE IF EXISTS sp_touch_all_radios;

DELIMITER ENDSPDEF
CREATE PROCEDURE sp_touch_all_radios()
BEGIN
	DECLARE all_done INT DEFAULT FALSE;
	DECLARE z CHAR(16);
  	DECLARE a, b INT;
  	DECLARE cur1 CURSOR FOR SELECT id,site_id FROM poundcake.network_radios;
  	DECLARE CONTINUE HANDLER FOR NOT FOUND SET all_done = TRUE;

  	OPEN cur1;

  	read_loop: LOOP
    	FETCH cur1 INTO a, b;
    	IF all_done THEN
    	  	 LEAVE read_loop;
  	  	END IF;
    	UPDATE poundcake.network_radios
    	SET site_id = b
    	WHERE id = a;
  	END LOOP;

  	CLOSE cur1;

END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;

truncate table radios_radios;
call sp_touch_all_radios();
select * from radios_radios;

-- alter table network_radios drop link_id;