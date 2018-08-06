DROP TABLE IF EXISTS `ip_spaces`;

CREATE TABLE ip_spaces (
    id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id INTEGER(10) DEFAULT NULL,
    lft INTEGER(10) DEFAULT NULL,
    rght INTEGER(10) DEFAULT NULL,
    name VARCHAR(255) DEFAULT '',
    ip_address int unsigned default 0,
    cidr int,
    project_id INTEGER(10) DEFAULT NULL,
    PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS `tmp3`;
CREATE TABLE `tmp3` (
	mydata VARCHAR(50)
);

INSERT INTO ip_spaces(
	`id`,
	`name`,
	`parent_id`,
	`lft`,
	`rght`,
	`ip_address`,
	`cidr`,
	`project_id`
) VALUES (
	1,
	'Clark\'s Network',
	NULL,
	1,
	3,
	inet_aton('10.0.0.0'),
	8,
	1
);

-- INSERT INTO `categories` (`id`, `name`, `parent_id`, `lft`, `rght`) VALUES(1, 'My Categories', NULL, 1, 30);

-- source sp_ip_range.sql;
-- call sp_ip_range( 2 );

select * from tmp3;

-- select inet_ntoa(ip) as ip_address from ip_spaces;
-- convert CIDR to netmask
-- select @netmask := inet_ntoa(power(2, 32) - power(2, (32 - cidr)))  as "net_mask" from ip_spaces where name='test';
-- number of "usable" ips for a given netmask
-- select @num_usable := inet_aton( @netmask ) ^ (power(2, 32) - 2) as "num_usable";

-- CALL sp_ip_range(1);

