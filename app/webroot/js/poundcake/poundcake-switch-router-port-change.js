$(document).ready(function() {
    // if they've picked a switch port, set the router port to empty
    $("#NetworkRadioSwitchPort").change(
        function() {
           $("#NetworkRadioRouterPort").val($("#NetworkRadioRouterPort option:first").val());
        });
    // and vice versa
    $("#NetworkRadioRouterPort").change(
        function() {
          $("#NetworkRadioSwitchPort").val($("#NetworkRadioSwitchPort option:first").val());
        });
});
