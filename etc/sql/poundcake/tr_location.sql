-- **************************************************************************************
-- This trigger inserts a slave row in the locations table when the master row is
-- inserted in the sites table
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_location_insert;
DELIMITER $$ 
CREATE TRIGGER tr_location_insert
AFTER INSERT ON sites
FOR EACH ROW
BEGIN
	-- IF ( ( NEW.id IS NOT NULL ) AND ( NEW.lat IS NOT NULL ) AND ( NEW.lon IS NOT NULL ) )
	IF ( (NULLIF(NEW.id,'')) AND (NULLIF(NEW.lat,'')) AND (NULLIF(NEW.lon,'')) )
	THEN 
		INSERT INTO locations(id, location)
		VALUES ( NEW.id, POINT(NEW.lat, NEW.lon) );
  	END IF;
END $$
DELIMITER ; 

-- **************************************************************************************
-- This trigger updates or inserts (if it doesn't exist) the slave row in the locations
-- table when the master row in the sites table
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_location_update;
DELIMITER $$ 
CREATE TRIGGER tr_location_update
AFTER UPDATE ON sites
FOR EACH ROW
BEGIN
	IF ( (NULLIF(NEW.id,'')) AND (NULLIF(NEW.lat,'')) AND (NULLIF(NEW.lon,'')) )
	THEN
		-- If the row already exists then we can update it, otherwise we need to insert it
		IF ( SELECT EXISTS(SELECT 1 FROM locations WHERE id = NEW.id) ) THEN
			UPDATE locations
			SET location = POINT(NEW.lat, NEW.lon)
			WHERE id = NEW.id;
		ELSE
			INSERT INTO locations(id, location)
			VALUES ( NEW.id, POINT(NEW.lat, NEW.lon) );
		END IF;
  	END IF;	
END $$
DELIMITER ; 

-- **************************************************************************************
-- This trigger deletes the corresponding slave row in the locations table when the master
-- row in the sites table is deleted
-- **************************************************************************************

DROP TRIGGER IF EXISTS tr_location_delete;
DELIMITER $$ 
CREATE TRIGGER tr_location_delete
BEFORE DELETE ON sites
FOR EACH ROW
BEGIN
	DELETE FROM locations
	WHERE id = OLD.id;
END $$
DELIMITER ; 

