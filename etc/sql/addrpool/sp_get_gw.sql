-- call sp_get_gw("CBRIT-BAYCT");
-- GW for CBRIT-BAYCT is 10.248.128.9
-- call sp_get_gw("BAYCT-CBRIT");
-- GW for BAYCT-CBRIT is 10.248.128.12


-- Relevant links:
-- http://www.finnie.org/2007/12/05/mysql-and-cidr-selection/
-- http://databaseblog.myname.nl/2011/07/working-with-ips-in-mysql-and-mariadb.html
-- http://social.msdn.microsoft.com/Forums/en-US/transactsql/thread/cc810f02-a12c-4c4f-afda-3ba5950da69f/
-- this goes onto the addrpool db

  
-- this goes onto the addrpool db
DROP PROCEDURE IF EXISTS sp_get_gw;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_get_gw(code varchar(255))
BEGIN
	DECLARE addr char(15);
	DECLARE dest,src,ip,starting_ip char(15);
	DECLARE oct1, oct2, oct3, oct4 char(15);
	DECLARE base1, slash1 char(15);

	-- test data
	-- roughly yes.  So if you are looking for the gateway for BOUTL-OBLEO, you need to find a /32 IP in the same IP 				
	-- range as BOUTL-OBLEO that has the name BOUTL
	-- if you are looking for the gateway for OBLEO-BOUTL, you need to find a /32 IP in the same IP range as OBLEO boutl with the name OBELO
	
	 -- returns everything to the right of the '-'
	SELECT SUBSTRING_INDEX(code,'-',-1) INTO dest;
	-- returns everything to the left of the '-'
	SELECT SUBSTRING_INDEX(code,'-',+1) INTO src;
	
	-- Get the IP for the site
	SET ip = sp_get_ip_address(code);
	
	IF ip IS NOT NULL THEN
	-- SELECT ip;
	-- calculate range of ips that represent the /29 of which that ip is a member
	-- then search for an ip in that range that has the name of the src

	-- Get the IP at the beginning of the range -- we use /29
	SET starting_ip = INET_NTOA(INET_ATON(cidr_to_mask(29)) & INET_ATON(ip));
	-- SELECT starting_ip;
	
	SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(starting_ip, '.', 1), ".", -1) INTO oct1;
	SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(starting_ip, '.', 2), ".", -1) INTO oct2;
	SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(starting_ip, '.', 3), ".", -1) INTO oct3;
	SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(starting_ip, '.', 4), ".", -1) INTO oct4;
	
	-- now look for a host that matches src in that range
	CALL sp_cidr_to_ip_range(oct1, oct2, oct3, oct4, 29, src);
	END IF;
	
END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_cidr_to_ip_range;
DELIMITER ENDSPDEF
CREATE PROCEDURE sp_cidr_to_ip_range(q1 INT, q2 INT, q3 INT, q4 INT, bitmask INT, src char(15))
BEGIN
	DECLARE hosts, c int;
	DECLARE ret char(15);
	SET ret = '0.0.0.0';
	
	-- SELECT CONCAT(q1,'.',q2,'.',q3,'.',q4);
	-- SELECT bitmask;
	-- SELECT src;
	
	SET hosts = POWER(2,(32 - bitmask));
	SET c = 1;
	
	WHILE c < hosts DO
		IF q4 < 255 THEN
			SET q4 = q4 + 1;
		ELSE
		IF q3 < 255 THEN
			BEGIN
    			SET q4 = 0;
        		SET q3 = q3 + 1;
			END;
		ELSE
		IF q2 < 255 THEN
			BEGIN
    			SET q4 = 0;
    			SET q3 = 0;
   				SET q2 = q2 + 1;
			END;
		END IF; -- q2
		END IF; -- q3
		END IF; -- q4
		
		SET ret = NULL;
		
		-- SELECT CONCAT(q1,'.',q2,'.',q3,'.',q4);
		SELECT base
		INTO ret
		FROM addrpool_subnet
		WHERE base = CONCAT(q1,'.',q2,'.',q3,'.',q4)
		AND slash = 32
		AND name = src;
		
		IF ret IS NOT NULL THEN
			SELECT ret;
		END IF;
		
		SET c = c + 1; 
				
	END WHILE;
	
	
	
END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;

-- http://databaseblog.myname.nl/2011/07/working-with-ips-in-mysql-and-mariadb.html
DROP FUNCTION IF EXISTS cidr_to_mask;
DELIMITER ENDSPDEF
CREATE FUNCTION cidr_to_mask(cidr INT(2)) RETURNS CHAR(15) DETERMINISTIC
BEGIN
	RETURN INET_NTOA(CONV(CONCAT(REPEAT(1,cidr),REPEAT(0,32-cidr)),2,10));
END ENDSPDEF
DELIMITER ;

DROP FUNCTION IF EXISTS mask_to_cidr;
DELIMITER ENDSPDEF
CREATE FUNCTION mask_to_cidr (mask CHAR(15)) RETURNS INT(2) DETERMINISTIC
BEGIN
	RETURN BIT_COUNT(INET_ATON(mask));
END ENDSPDEF
DELIMITER ;