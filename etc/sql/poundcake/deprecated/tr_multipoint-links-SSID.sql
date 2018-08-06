-- **************************************************************************************
-- This trigger updates the radios_radios join table when a radio is insert in the
-- network_radios table.  Radios are linked if their SSIDs match.  In the case of P2MP
-- (Point-to-Multipoint) radio link, the last radio inserted becomes the multipoint
-- end of that link.
--
-- NOTE:  This is very similar to the tr_network_radio_update update trigger
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_network_radio_insert;
DELIMITER $$ 
CREATE TRIGGER tr_network_radio_insert
BEFORE INSERT ON network_radios
FOR EACH ROW
BEGIN
	DECLARE next_id INT;
	DECLARE match_id INT;
	DECLARE done INT DEFAULT FALSE;
	
	-- Declare a cursor to allow us to loop through all radios which have the same SSID
	-- as the radio that is being inserted	
	DECLARE matching_ssids_cursor CURSOR FOR
	SELECT id
	FROM network_radios
	WHERE ssid = NEW.ssid;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	-- Because network_radios.id is an auto-increment field, we can't get it as we would
	-- normally, e.g. NEW.id
	-- so we have to query MySQL for what the next auto-increment is then save that back
	SET next_id = ( SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='network_radios' );
	SET NEW.id = next_id;
	
	OPEN matching_ssids_cursor;
	matching_ssids_loop: LOOP
		FETCH matching_ssids_cursor INTO match_id;
		IF done THEN
        	CLOSE matching_ssids_cursor;
        	SET done = TRUE;
        	LEAVE matching_ssids_loop;
    	END IF;
        
        DELETE FROM radios_radios
        WHERE src_radio_id = match_id
        OR dest_radio_id = match_id;
        
        -- note this inserts two rows
		INSERT INTO radios_radios
		VALUES ( NEW.id, match_id, 'insert_case1' ),
		( match_id, NEW.id, 'insert_case2' );
			
	END LOOP matching_ssids_loop;
	
	-- Cleanup our junk
	SET @next_id = null;
	SET @match_id = null;
	SET @done = null;
	
END $$
DELIMITER ;

-- **************************************************************************************
-- This trigger cleans up the radios_radios join table when a radio is deleted from the
-- network_radios table
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_network_radio_delete;
DELIMITER $$ 
CREATE TRIGGER tr_network_radio_delete
BEFORE DELETE ON network_radios
FOR EACH ROW
BEGIN

	DELETE FROM radios_radios
	WHERE ( src_radio_id = OLD.id ) OR ( dest_radio_id = OLD.id );

END $$
DELIMITER ;

-- **************************************************************************************
-- This trigger updates the radios_radios join table when a radio is updated in the
-- network_radios table.  As with insert, matches are made by SSID and the last radio
-- saved becomes the multipoint end of a P2MP link.
--
-- NOTE:  This is very similar to the tr_network_radio_insert insert trigger
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_network_radio_update;
DELIMITER $$ 
CREATE TRIGGER tr_network_radio_update
BEFORE UPDATE ON network_radios
FOR EACH ROW
BEGIN
	-- see documentation on the insert trigger
	DECLARE next_id INT;
	DECLARE match_id INT;
	DECLARE done INT DEFAULT FALSE;
	
	-- Declare a cursor to allow us to loop through all radios which have the same SSID
	-- as the radio that is being updated
	DECLARE matching_ssids_cursor CURSOR FOR
	SELECT id
	FROM network_radios
	WHERE ssid = NEW.ssid
	AND id != NEW.id; -- exclude this radio so we don't link to itself
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	OPEN matching_ssids_cursor;
	matching_ssids_loop: LOOP
		FETCH matching_ssids_cursor INTO match_id;
		IF done THEN
        	CLOSE matching_ssids_cursor;
        	SET done = TRUE;
        	LEAVE matching_ssids_loop;
    	END IF;
        
        DELETE FROM radios_radios
        WHERE src_radio_id = match_id
        OR dest_radio_id = match_id;
        
        -- note this inserts two rows
		INSERT INTO radios_radios
		VALUES ( NEW.id, match_id, 'update_case1' ),
		( match_id, NEW.id, 'update_case2' );
			
	END LOOP matching_ssids_loop;
	
	-- Cleanup our junk
	SET @next_id = null;
	SET @match_id = null;
	SET @done = null;

END $$
DELIMITER ;