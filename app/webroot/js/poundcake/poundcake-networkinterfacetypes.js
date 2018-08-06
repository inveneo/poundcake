/*
 * Toggle the number text field when an interface type is selected/de-selected
*/

$(document).ready(function() {
    $(".networkinterfacetype").click(            
    function() {
//        var currentId = $(this).attr('id');
        if ($(this).is(':checked')) {
            $(this).parents('tr').eq(0).find('.networkinterfacetype_number').attr('disabled', false);
            $(this).parents('tr').eq(0).find('.networkinterfacetype_number').val('1');
        } else {
                $(this).parents('tr').eq(0).find('.networkinterfacetype_number').attr('disabled', true);
                $(this).parents('tr').eq(0).find('.networkinterfacetype_number').val('0');
        }   
      
    });
});
