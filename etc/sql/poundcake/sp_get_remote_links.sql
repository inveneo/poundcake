DROP PROCEDURE IF EXISTS sp_get_remote_links;
DELIMITER ENDSPDEF
-- return the IDs of any radios attached to this radio
-- if this is a multipoint link, there may be many radios attached to it
CREATE PROCEDURE sp_get_remote_links(radio_id int(10))
    BEGIN 
    	SELECT dest_radio_id,
    	network_radios.name,
    	network_radios.ssid,
    	network_radios.is_down,
    	network_radios.frequency,
    	radio_modes.name AS "radio_mode_name"
    	
		FROM radios_radios, network_radios, radio_modes
		WHERE src_radio_id = radio_id
		AND network_radios.id = dest_radio_id
		AND network_radios.radio_mode_id = radio_modes.id;
		
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;