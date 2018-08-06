DROP PROCEDURE IF EXISTS sp_count_antennas;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_count_antennas(site_id int(10))
    BEGIN 
		SELECT sites.code, antenna_types.name, count(*) AS "count"
		FROM sites, network_radios, antenna_types
		WHERE sites.id = network_radios.site_id
		AND network_radios.antenna_type_id = antenna_types.id
		AND sites.id=site_id
		GROUP BY antenna_type_id;
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;