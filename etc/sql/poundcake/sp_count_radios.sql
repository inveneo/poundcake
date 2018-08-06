DROP PROCEDURE IF EXISTS sp_count_radios;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_count_radios(site_id int(10))
    BEGIN 
        SELECT sites.code, radio_types.name, count(*) AS "count" -- , network_radios.name AS "radio_name"
		FROM sites, network_radios, radio_types
		WHERE sites.id = network_radios.site_id
		AND network_radios.radio_type_id = radio_types.id
		AND sites.id=site_id
		GROUP BY radio_type_id;
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;