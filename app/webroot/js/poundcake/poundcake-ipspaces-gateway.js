
/*
 * Enable or disable the gateway field on IPSpaces add/edit if the user
 * has selected a /32
*/

$(document).ready(function() {
    
$("#IpSpaceCidr").change(            
    function() {
        if ( $("#IpSpaceCidr").val() == 32 ) {
            $("#IpSpaceGatewayId").removeAttr( 'disabled', false ); 
            $("#IpSpaceGatewayId").children().each(function(i, opt){
                $(this).prop("disabled", false);
            });
        } else {
            $("#IpSpaceGatewayId").attr("disabled","disabled");
            $("#IpSpaceGatewayId").val( "" );
        }
    });
});

