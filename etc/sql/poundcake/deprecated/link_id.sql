-- **************************************************************************************
-- This trigger looks for a radio that corresponds to the newly inserted radio, then
-- links them via the link_id field -- since a trigger cannot update rows in the same
-- table, the procedure sp_add_link_id is also called immediately after this -- that
-- links the other radio back to the new radio
-- Radios are named in the format XXXXX-YYYYY; a corresponding radio would be YYYYY-XXXXX
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_network_radio_insert;
DELIMITER $$ 
CREATE TRIGGER tr_network_radio_insert
BEFORE INSERT ON network_radios
FOR EACH ROW
BEGIN
	-- Radios are named XXXXX-YYYYY however there can be
	-- a different number of characters on each, e.g.
	-- XXXX-YYYYY
	-- YY-XXXXX
	-- Etc.
	
	 -- returns everything to the right of the '-'
	SELECT SUBSTRING_INDEX(NEW.name,'-',-1) INTO @dest;
	-- returns everything to the left of the '-'
	SELECT SUBSTRING_INDEX(NEW.name,'-',+1) INTO @src;
	-- now swap them to find the opposite radio name as per our naming convention
	SELECT CONCAT(@dest, '-', @src) INTO @dest_name;

	-- get the ID of the corresponding radio
	SELECT id INTO @dest_radio_id
	FROM network_radios
	WHERE name = @dest_name;
	-- link the two radios
	IF ( @dest_radio_id > 0 ) THEN
		SET NEW.link_id = @dest_radio_id;
	END IF;
	
	-- ******* BEGIN experimental join table
	
	
	-- ******* END experimental join table
	
	
END $$
DELIMITER ;

-- **************************************************************************************
-- This trigger updates the link_id for an updated radio, or clears it if there's
-- no match
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_network_radio_update;
DELIMITER $$ 
CREATE TRIGGER tr_network_radio_update
BEFORE UPDATE ON network_radios
FOR EACH ROW
BEGIN
	-- see notes in trigger network_radio_insert
	SELECT SUBSTRING_INDEX(NEW.name,'-',-1) INTO @dest;
	SELECT SUBSTRING_INDEX(NEW.name,'-',+1) INTO @src;
	SELECT CONCAT(@dest, '-', @src) INTO @dest_name;
	
	-- get the ID of the corresponding radio
	SELECT id INTO @dest_radio_id
	FROM network_radios
	WHERE name = @dest_name;
	-- link the two radios
	IF ( @dest_radio_id > 0 ) THEN
		SET NEW.link_id = @dest_radio_id;
	ELSE
		SET NEW.link_id = NULL;
	END IF;
END $$
DELIMITER ;