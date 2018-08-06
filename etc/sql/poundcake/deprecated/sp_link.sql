-- **************************************************************************************
-- This procedure adds a link_id to a given network_radio.id
-- **************************************************************************************

DROP PROCEDURE IF EXISTS sp_add_link_id;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_add_link_id(src int(10), dest int(10))
    BEGIN 
        UPDATE network_radios
        SET link_id = src
        WHERE id = dest;
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;

-- **************************************************************************************
-- This procedure removes a link_id for a given network_radio.id
-- **************************************************************************************

DROP PROCEDURE IF EXISTS sp_rm_link_id;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_rm_link_id(radio_id int(10))
    BEGIN 
        UPDATE network_radios
        SET link_id = NULL
        WHERE id = id;
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;