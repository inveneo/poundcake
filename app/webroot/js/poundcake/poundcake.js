/*
 * This datepicker is from:
 * http://nik.chankov.net/2011/07/16/setting-datepicker-with-jquery-in-cakephp-project/
 */

$(document).ready(function () {
    
    // show Bootstrap tooltips when there is a class 'link'
    $('.link').tooltip();
    
    cakebootstrap();
    
    errorstrap();
    
    bootbox_dialogs();
    
    // this calls the dialog function in the UsersController which sets a session
    // variable to not display dialog box any more once the user has closed it
    $('.alert').bind('close', function () {
//        console.log("closed");        
        $.ajax({
            url: '/users/dialog'//,
//            success: function (response) {//response is value returned from php (for your example it's "bye bye"
//              alert(response);
//            }
         });
    });
       
    // if the user clicks "cancel", go back to the page which is stored as the hidden
    // input backTo -- taken from the breadcrumbs array
    $('.btn-cancel').click(function() {
        // event.preventDefault();
        // history.back was not working consistently, sometimes requiring two clicks
        // to go back -- so instead, grab the destination to go to when the cancel
        // button is clicked from the backTo variable, hidden in default.ctp
        $(this).attr('disabled', 'disabled'); // so no HTML5 form field validation occurs
        window.location =  $('#backTo').val();
        // history.back(-1); // go back
        return false;
      });
      
    // datepicker
//    $( "input.datepicker" ).dp({
//        dateFormat: 'dd.mm.yy', 
//        altFormat: 'yy-mm-dd'
//    });
    // the res of the datepicker is in poundcake-datepicker.js
   
    // fade out flash messages after n miliseconds
    // replace with empty block to maintain space
    $('#flash').fadeTo(4000, 0, function(){  })
    
    // checkbox on Radio add/edit for denoting it as a sector -- enables/disables
    // the true azimuth field
    $('#NetworkRadioSector').click( function() {
        $("[id$='Azimuth']").each( function() {
            this.disabled = !this.disabled;
            // if it's disabled then clear the field
            //if(this.disabled)
                $(this).val("");
         });
    });

    // checkboes on the main search
    // Checkbox stuff from: http://www.abeautifulsite.net/blog/2007/12/jquery-checkboxes-select-all-select-none-and-invert-selection/
    // Select all
    $("A[href='#select_all']").click( function() {
        $("#" + $(this).attr('rel') + " INPUT[type='checkbox']").attr('checked', true);
        return false;
    });

    // Select none
    $("A[href='#select_none']").click( function() {
        $("#" + $(this).attr('rel') + " INPUT[type='checkbox']").attr('checked', false);
        return false;
    });

    // Invert selection
    $("A[href='#invert_selection']").click( function() {
        $("#" + $(this).attr('rel') + " INPUT[type='checkbox']").each( function() {
            $(this).attr('checked', !$(this).attr('checked'));
        });
        return false;
    });
    
    // Select none
    $("A[href='#clear_all']").click( function() {
        $("#" + $(this).attr('rel') + " INPUT[type='text']").val('');
        return false;
    });
    
    /*
    // tabs on admin interface
    $('#home a').click(function (e) {
        e.preventDefault();
        $(this).tabs('show');
    })
    
    $('#profile a').click(function (e) {
        e.preventDefault();
        $(this).tabs('show');
    })
    
    
    $('#messages a').click(function (e) {
        e.preventDefault();
        $(this).tabs('show');
    })
    
    
    $('#settings a').click(function (e) {
        e.preventDefault();
        $(this).tabs('show');
    })
    
    $('a[data-toggle="tab"]').on('shown', function (e) {
        e.target // activated tab
        e.relatedTarget // previous tab
    })
    */

    /*
    
    // this highlights the current tab
    $(".main-navigation .nav li a").each(function() {
        var str=location.href.toLowerCase();
        $(".nav li a").each(function() {
            // this changes the main navigation
            if (str.indexOf(this.href.toLowerCase()) > -1) {
                //$("li.active").removeClass("active");
                //$(this).parent().addClass("active");
                $(this).parent().css({
                    //'background-color':'#0088cc',
                    'text-shadow':'0 -1px 0 rgba(0, 0, 0, 0.2)',
                    'color':'#ffffff'
                });
                return false;
            } else if ( str.match(/overview/)) {
                $("li.active").removeClass("active");
                $(".nav li a").eq(0).css({
                    //'background-color':'#0088cc',
                    'text-shadow':'0 -1px 0 rgba(0, 0, 0, 0.2)',
                    'color':'#ffffff'
                });
                return false;
            } else if ( str.match(/admin/)) {
                // this keeps admin highlighted even when we're on sub-pages
                // of the admin UI such as /admin/buildItems/index
                //alert("Admin page!");
                $("li.active").removeClass("active");
                //$(".nav li a").eq(6).addClass("active"); // doesn't work!
                $(".nav li a").eq(6).css({
                    //'background-color':'#0088cc',
                    'text-shadow':'0 -1px 0 rgba(0, 0, 0, 0.2)',
                    'color':'#ffffff'
                });
                return false;            
            };           
        });
        $("li.active").removeClass("active");
        $(this).parent().addClass("");
        //alert($(this).text());
    });
    */
});

// CakePHP Twitter Bootstrappifier
// https://gist.github.com/2035441
function cakebootstrap()
{
    //All submit forms wrapped to div.action
    $('form[class*="form-horizontal"] input[type="submit"][class!="btn btn-primary"]').wrap('<div class="form-actions" />');
    //All submit forms converted to primary button
    $('form[class*="form-horizontal"] input[type="submit"]').addClass('btn btn-primary');
    //All index actions converted into pretty buttons
    $('form[class*="form-horizontal"] td[class="actions"] > a[class!="btn"]').addClass('btn');

    //All (div.inputs) with default FormHelper style (div.input > label ~ input)
    //converted into Twitter Bootstrap Style (div.clearfix > label ~ div.input)

    $('form[class*="form-horizontal"] div[class!="input added"].input').removeClass().addClass('control-group');
    $('form[class*="form-horizontal"] div.control-group').closest('form').addClass('form-horizontal');
    $('form[class*="form-horizontal"] div.control-group label').addClass('control-label');
    $('form[class*="form-horizontal"] div.control-group > label ~ input').wrap('<div class="controls" />');
    $('form[class*="form-horizontal"] div.control-group > label ~ select').wrap('<div class="controls" />');
    $('form[class*="form-horizontal"] div.control-group > label ~ textarea').wrap('<div class="controls" />');
    $('form[class*="form-horizontal"] div.control-group > input[type="checkbox"]').each(function(e, data){
            $(this).parent().find('label').insertBefore(this);
            $(this).wrap('<div class="controls" />');
    });
    $('form[class*="form-horizontal"] div.control-group > input ~ label').insertBefore();
    $('div.pagination ul li a.disabled').parent().addClass('disabled');
    //$('div.pagination ul li.active').wrapInner('<a href="#" />');
}

//Default CakePHP Error inputs are converted to twitter bootstrap style
function errorstrap()
{
    $('.message').addClass('alert-message error');
    $('.flash_success').addClass('alert-message success');
    $('.flash_warning').addClass('alert-message warning');
    $('form[class*="form-horizontal"] .form-error').addClass('error');
    $('form[class*="form-horizontal"] .form-error').closest('.control-group').addClass('error');
    $('form[class*="form-horizontal"] .error .error-message').each(function(e, data){
            $(data).parent().find('.controls').append('<span class="help-inline">' + $(this).text() + '</span>');
            $(data).remove();
    });
}

function bootbox_dialogs() {
    
    /*
     * Generic way to handle Bootbox dialog boxes for confirmation
     * from users on an action such a "Delete" from a href link
     */
    $('a.confirm').removeAttr('onclick');
    $('a.confirm').click(function(e) {
        e.preventDefault();
        
        // get the form element preceding the link
        var action = $(this).prev('form').attr('action');
        
        // the text to display in the dialog box comes in as an HTML5
        // data attribute, data-dialog_msg="Foo"
        var string = $(this).data( 'dialog_msg' );
        
        bootbox.confirm( string + '?', function(confirmed) {
            if (confirmed) {
                $.ajax({
                        type: 'POST',
                        url: action,
                        success: function() {
                            // console.log( action );
                            window.location.reload(true);
                        },
                        error: function() {
                            console.log( 'error');
                        }
                });
                return false;
            }
        });
        return false;
    });
    
    /*
     * Generic way to handle Bootbox dialog boxes for prompt
     * that requires capturing some input
     */
    $('a.prompt').removeAttr('onclick');
    $('a.prompt').click(function(e) {
        e.preventDefault();
        
        // get the form element preceding the link
        var action = $(this).prev('form').attr('action');
        
        // the text to display in the dialog box comes in as an HTML5
        // data attribute, data-dialog_msg="Foo"
        var string = $(this).data( 'dialog_msg' );
        
        bootbox.prompt( string + '?', function(result) {
            if ( result != null ) {
                // we want the resulting URL to be soemthing like:
                // ipSpaces/fill/543/9
                // ...fill IP space with the ID of 543 with 9 hosts
                action += '/' + result;
                console.log( action );
                console.log( result );
                $.ajax({
                        type: 'POST',
                        url: action,
                        success: function() {
                            console.log( 'success');
                            window.location.reload(true);
                        },
                        error: function() { console.log( 'error'); }
                });
                return false;
            }
        });
        return false;
    });
}