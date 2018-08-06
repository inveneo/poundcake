-- alter table `trcs` add column `lat` DECIMAL(17,14) after `location`;
-- alter table `trcs` add column `lon` DECIMAL(17,14) after `lat`;

DELETE FROM `trcs` where ID>=37;

DROP TRIGGER IF EXISTS `insert_location_trcs`;

-- Can't modify inserted values AFTER insert; has to be a BEFORE trigger.

-- this trigger updates the lat/lon columns based on data inserted into the location
-- column (passed to the write in already encoded format)

DELIMITER ENDTRIGGERDEF
CREATE TRIGGER insert_location_trcs 
BEFORE INSERT ON trcs
FOR EACH ROW
BEGIN
	IF NEW.location IS NOT NULL
	THEN
		-- UPDATE trcs
		-- SET lat = (SELECT X(NEW.location) FROM trcs WHERE id=NEW.id)
		-- WHERE id=NEW.id;
		SET NEW.lat = X(NEW.location);
  		-- SET NEW.lat = X(NEW.location)
  	END IF;
END ENDTRIGGERDEF
DELIMITER ;

insert into trcs(id,trc_name,location) values (37,'foo',GeomFromText('POINT(-10.7262833333333 39.8306833333333)'));
SELECT id,trc_name,lat from trcs where ID=37;