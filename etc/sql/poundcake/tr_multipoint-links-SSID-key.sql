-- **************************************************************************************
-- This trigger updates the radios_radios join table when a radio is insert in the
-- network_radios table.  Radios are linked if their SSIDs match.  In the case of P2MP
-- (Point-to-Multipoint) radio link, the radio that is the multipoint end of that link
-- comes in denoted as such (network_radios.p2mp = 1)
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
	DECLARE link_to INT DEFAULT NULL;
	DECLARE done INT DEFAULT FALSE;
	
	-- Declare a cursor to allow us to loop through all radios which have the same SSID
	-- as the radio that is being inserted	
	DECLARE matching_ssids_cursor CURSOR FOR
	SELECT id
	FROM network_radios
	WHERE ssid = NEW.ssid
	AND ssid <> '';
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	-- cleanup the join table as we're about to re-do the links
	DELETE radios_radios.*
	FROM radios_radios
	LEFT JOIN network_radios ON
	radios_radios.src_radio_id = network_radios.id
	OR radios_radios.dest_radio_id = network_radios.id
	WHERE network_radios.ssid = NEW.ssid;
	   
	-- Because network_radios.id is an auto-increment field, we can't get it as we would
	-- normally, e.g. NEW.id
	-- so we have to query MySQL for what the next auto-increment is then save that back
	SET next_id = ( SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='network_radios' );
	SET NEW.id = next_id;
	
	-- In a P2MP scenario, the last radio inserted becomes the one that every other radio
	-- with the same SSID will link to.  Check if there is already a radio with that SSID
	-- that is marked as the multipoint end
	-- See PC-475 as to why there is a LIMIT 1 on this query
	SET link_to = ( SELECT id FROM network_radios WHERE ssid = NEW.ssid AND p2mp > 0 LIMIT 1 );
	-- insert into temp1 values (link_to,'before');
	-- if a radio has already been marked as the multipoint end, we need to link the radio
	-- currently being inserted to it -- otherwise, this would not happen in the loop below
	IF link_to > 0 THEN
		-- note this inserts two rows
		INSERT INTO radios_radios
		VALUES ( link_to, NEW.id, 'insert_case3' ),
		( NEW.id, link_to, 'insert_case4' );
	END IF;
	
	-- if there is not a radio denoted as the multipoint end, then this radio is 
	-- effectively it -- this is the normal two radio scenario, e.g. A->B and B->A
	IF link_to IS NULL OR NEW.p2mp > 0 THEN
		SET link_to = NEW.id;		
	END IF;
	-- insert into temp1 values (link_to,'after');
	
	OPEN matching_ssids_cursor;
	matching_ssids_loop: LOOP
		FETCH matching_ssids_cursor INTO match_id;
		IF done THEN
        	CLOSE matching_ssids_cursor;
        	SET done = TRUE;
        	LEAVE matching_ssids_loop;
    	END IF;
        
		-- don't link a radio to itself
		IF link_to != match_id THEN
			-- note this inserts two rows
			INSERT INTO radios_radios
			VALUES ( link_to, match_id, 'insert_case1' ),
			( match_id, link_to, 'insert_case2' );
		END IF;
		
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
	DECLARE link_to INT DEFAULT NULL;
	DECLARE done INT DEFAULT FALSE;
	
	-- Declare a cursor to allow us to loop through all radios which have the same SSID
	-- as the radio that is being updated
	DECLARE matching_ssids_cursor CURSOR FOR
	SELECT id
	FROM network_radios
	WHERE ssid = NEW.ssid
	AND ssid <> ''
	AND id != NEW.id; -- exclude the radio being updated so we don't link it to itself
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	-- insert into temp3(debug) values(NEW.ssid);
	
	-- cleanup the join table as we're about to re-do the links
	DELETE radios_radios.*
	FROM radios_radios
	LEFT JOIN network_radios ON
	radios_radios.src_radio_id = network_radios.id
	OR radios_radios.dest_radio_id = network_radios.id
	WHERE network_radios.ssid = NEW.ssid;
	
	-- In a P2MP scenario, the last radio inserted becomes the one that every other radio
	-- with the same SSID will link to.  Check if there is already a radio with that SSID
	-- that is marked as the multipoint end
	-- See PC-475 as to why there is a LIMIT 1 on this query
	SET link_to = ( SELECT id FROM network_radios WHERE ssid = NEW.ssid AND p2mp > 0 LIMIT 1 );
	-- insert into temp1 values (link_to,'before');
	-- if a radio has already been marked as the multipoint end, we need to link the radio
	-- currently being inserted to it -- otherwise, this would not happen in the loop below
	IF link_to > 0 THEN
		-- don't link a radio to itself
		IF link_to != NEW.id THEN
			-- note this inserts two rows
			INSERT INTO radios_radios
			VALUES ( link_to, NEW.id, 'update_case3' ),
			( NEW.id, link_to, 'update_case4' );
		END IF;
	END IF;
	
	-- if there is not a radio denoted as the multipoint end, then this radio is 
	-- effectively it -- this is the normal two radio scenario, e.g. A->B and B->A
	IF link_to IS NULL OR NEW.p2mp > 0 THEN
		SET link_to = NEW.id;		
	END IF;
	-- insert into temp1 values (link_to,'after');
	
	OPEN matching_ssids_cursor;
	matching_ssids_loop: LOOP
		FETCH matching_ssids_cursor INTO match_id;
		IF done THEN
        	CLOSE matching_ssids_cursor;
        	SET done = TRUE;
        	LEAVE matching_ssids_loop;
    	END IF;
        
        -- don't link a radio to itself
		IF link_to != match_id THEN
        	-- note this inserts two rows
			INSERT INTO radios_radios
			VALUES ( link_to, match_id, 'update_case1' ),
			( match_id, link_to, 'update_case2' );
		END IF;
		
	END LOOP matching_ssids_loop;
	
	-- Cleanup our junk
	SET @next_id = null;
	SET @match_id = null;
	SET @done = null;

END $$
DELIMITER ;