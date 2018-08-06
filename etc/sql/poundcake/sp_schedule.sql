DROP PROCEDURE IF EXISTS sp_schedule;
-- Switch delimiter so the ; will work in the function body
DELIMITER ENDSPDEF
-- Create the procedure
CREATE PROCEDURE sp_schedule(install_team_id int(10))
    BEGIN
		SELECT sites.id, sites.code, sites.install_date, sites.name, install_teams.name
		FROM sites, install_teams
		WHERE sites.install_team_id = install_team_id
		AND install_teams.id = install_team_id
		ORDER BY sites.install_date ASC;
    END ENDSPDEF
-- Switch the delimiter back to ;
DELIMITER ;