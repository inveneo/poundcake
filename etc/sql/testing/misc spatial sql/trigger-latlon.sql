-- alter table `trcs` add column `lat` DECIMAL(17,14) after `location`;
-- alter table `trcs` add column `lon` DECIMAL(17,14) after `lat`;
-- ALTER TABLE `trcs` ADD coords Point;

-- This trigger is meant to take the lat/lon (which is sent to the db
-- as plain text) and encode them into the location field

DELETE FROM `trcs` where ID>=37;

DROP TRIGGER IF EXISTS `insert_location_trcs`;

-- Can't modify inserted values AFTER insert; has to be a BEFORE trigger.

DELIMITER ENDTRIGGERDEF
CREATE TRIGGER insert_location_trcs 
BEFORE INSERT ON trcs
FOR EACH ROW
BEGIN
	
	SET NEW.location = Point(NEW.lat, NEW.lon);
	
	-- DECLARE lat decimal(17,14) DEFAULT 0.00000;
	-- DECLARE lon decimal(17,14) DEFAULT 0.00000;
	-- SET lat = NEW.lat;
	-- SET lon = NEW.lon;
	-- SET @point = 'POINT( NEW.lat NEW.lon )';
	-- IF ( @lat IS NOT NULL ) AND ( @lon IS NOT NULL )
	-- THEN
		-- SET NEW.location = GeomFromText('POINT(NEW.lat NEW.lon)');
		-- SET NEW.location = GeomFromText('POINT(-10.7262833333333 39.8306833333333)');
		-- SET NEW.location = GeomFromText( 'POINT( @lat @lon )' );
  	-- END IF;
END ENDTRIGGERDEF
DELIMITER ;

insert into trcs(id,trc_name,lat,lon) values (37,'foo',-10.7262,13.5532);

SELECT id,trc_name,lat,lon, location, X(location),Y(location) from trcs where ID=37;