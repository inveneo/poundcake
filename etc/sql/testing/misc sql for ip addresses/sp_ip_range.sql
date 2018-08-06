-- inet_ntoa turns unsigned int into dotted quad
-- inet_aton turns dotted quad into unsigned int

DROP PROCEDURE IF EXISTS sp_ip_range;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_ip_range( id int(10) )
    BEGIN
    DECLARE v_num_usable INT;
    DECLARE v_cidr INT;
    DECLARE v_counter INT UNSIGNED DEFAULT 0;
    DECLARE v_ip INT UNSIGNED;
    
    SELECT cidr,ip into v_cidr,v_ip FROM ip_spaces WHERE ID = id;
    insert into tmp3(mydata) values (v_cidr);
	insert into tmp3(mydata) values (v_ip);
	
	-- DECLARE netmask INT UNSIGNED;	
	-- having issues getting netmask into a variable, so the formula to calculate the
	-- netmask from the cidr is embedded in the calculation (below) for num_usable
	-- calculate the netmask for from the cidr
	-- select inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - cidr))) from ip_spaces where id=1;
	-- SET netmask = inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - NEW.cidr )));
	-- INSERT INTO tmp3(mydata) VALUES( netmask );	
	-- SET v_num_usable = inet_aton( inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - v_cidr ))) ) ^ (power(2, 32) - 2);
	-- insert into tmp3(mydata) values (v_num_usable);
	
	WHILE v_counter < v_num_usable DO
    	
    	-- SELECT inet_ntoa(v_ip + v_counter) as "ip_in_range";
    	-- INSERT INTO ip_spaces(
    	SET v_counter = v_counter + 1;
  	END WHILE;
  	
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;

-- call sp_ip_range( 1 );