
/*
 * Toggle SNMP version/community name fields based on whether or not the user
 * has checked the box for to override the project's SNMP data
*/

$(document).ready(function() {
    
    $("#IpSpaceCidr").change(            
    function() {
        if ( $("#IpSpaceCidr").val() == 32 ) {
            //console.log("enable or disable");
            $("#IpSpacePrimaryIp").prop("disabled", false);
        } else {
            $("#IpSpacePrimaryIp").prop("disabled", true);
            $("#IpSpacePrimaryIp").attr("checked", false);
        }
    });
});
