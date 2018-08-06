DROP TABLE IF EXISTS `tmp3`;
CREATE TABLE `tmp3` (
	mydata VARCHAR(50)
);

DROP TABLE IF EXISTS `ip_spaces`;
CREATE TABLE `ip_spaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `ip` int unsigned not null,
  `cidr` smallint(5) unsigned NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `project_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addrpool_subnet_parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS tr_ip_address;
DELIMITER $$ 
CREATE TRIGGER tr_ip_address
AFTER INSERT ON ip_spaces
FOR EACH ROW
BEGIN
	DECLARE num_usable INT;
	DECLARE counter INT UNSIGNED DEFAULT 0;
	
	-- DECLARE netmask INT UNSIGNED;	
	-- having issues getting netmask into a variable, so the formula to calculate the
	-- netmask from the cidr is embedded in the calculation (below) for num_usable
	-- calculate the netmask for from the cidr
	-- select inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - cidr))) from ip_spaces where id=1;
	-- SET netmask = inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - NEW.cidr )));
	-- INSERT INTO tmp3(mydata) VALUES( netmask );	
	SET num_usable = inet_aton( inet_ntoa( power( 2, 32 ) - power( 2, ( 32 - NEW.cidr ))) ) ^ (power(2, 32) - 2);
	-- INSERT INTO tmp3(mydata) VALUES( inet_ntoa( NEW.ip ) );
	INSERT INTO tmp3(mydata) VALUES( num_usable );
	
	WHILE counter < num_usable DO
    	
    	INSERT INTO ip_spaces(ip,parent_id)
    	VALUES (
    		NEW.ip + counter,
    		NEW.id
    	);
    	
    	SET counter = counter + 1;
  	END WHILE;
	
	-- Cleanup our junk
	SET num_usable = null;
	-- SET netmask = null;
	
END $$
DELIMITER ;

insert into ip_spaces(name,ip,cidr) values (
	'test',
	inet_aton('10.10.10.0'),
	24
); 
SELECT * FROM tmp3;