
/*
 * This datepicker is from:
 * http://nik.chankov.net/2011/07/16/setting-datepicker-with-jquery-in-cakephp-project/
 */
$(document).ready(function() {
   $( "input.datepicker" ).dp({
      dateFormat: 'dd.mm.yy', 
      altFormat: 'yy-mm-dd'
   }); 
});

(function( $ ) {
    
    $.widget( "ui.dp", {
            _create: function() {
                var el = this.element.hide();
                this.options.altField = el;
                var input = this.input = $('<input>').insertBefore( el )
                input.focusout(function(){
                        if(input.val() == ''){
                            el.val('');
                        }
                    });
                input.datepicker(this.options)
                if(convertDate(el.val()) != null){
                    this.input.datepicker('setDate', convertDate(el.val()));
                }
            },
            destroy: function() {
                this.input.remove();
                this.element.show();
                $.Widget.prototype.destroy.call( this );
            }
    });
    
    var convertDate = function(date){
      if(typeof(date) != 'undefined' && date != null && date != ''){
        return new Date(date);
      } else {
        return null;
      }
    }
})( jQuery );