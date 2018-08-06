
/*
 * 
*/

$(document).ready(function() {
    $("input[type='checkbox'][name^='ProjectMembership']").click(function() {
        // var checkboxName = $(this).attr('name');
        // get the radio buttons in the same row as the checkbox and enabe/disable them
        if ($(this).is(':checked')) {
            // enable the radio buttons if the project is checked
            $(this).parents("tr").find("input[type='radio']").attr('disabled', false );
            // default the view to true
            //$(this).parents("tr").find("input[type='radio'][name='view']").prop('checked', true );
        } else {
            // disable and un-check the radio buttons if the project is un-checked
            $(this).parents("tr").find("input[type='radio']").attr('disabled', true );
            //$(this).parents("tr").find("input[type='radio']").prop('checked', false );
        }
        
    });    
});