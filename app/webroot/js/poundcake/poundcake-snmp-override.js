
/*
 * Toggle SNMP version/community name fields based on whether or not the user
 * has checked the box for to override the project's SNMP data
*/

$(document).ready(function() {
    $(".snmp").click(
    function() {
       // the field for SNMP type (version)
       $("[id$='SnmpTypeId']").each(
            function() {
               // $("[id$='SnmpTypeId']").attr('disabled',false);
               // console.log(this.disabled);
               this.disabled = !this.disabled;
               if( !this.disabled ) {
                    // console.log("enable");
                    //$("[id$='SnmpTypeId']").removeAttr("disabled");
                    $(this).prop("disabled", false);
                    $(this).children().each(function(i, opt){
                        $(this).prop("disabled", false);
                    });
               } else {
                   // console.log("disable & clear");
                   $(this).val("");
               }
            }
        );
       // the field for SNMP community string
       $("[id$='SnmpCommunityName']").each(
            function() {
               this.disabled = !this.disabled;
               // if it's disabled then clear the field
               if(this.disabled)
                   $(this).val("");
            }
        );
    });
});
