$(document).ready(function() {
    function stopWheel(e){
        if(!e){ e = window.event; } /* IE7, IE8, Chrome, Safari */
        if(e.preventDefault) { e.preventDefault(); } /* Chrome, Safari, Firefox */
        e.returnValue = false; /* IE7, IE8 */
    }

    $('.scroller input[type=number]').focus(function(){
       document.onmousewheel = function(){ stopWheel(); } /* IE7, IE8 */
       if(document.addEventListener){ /* Chrome, Safari, Firefox */
            document.addEventListener('DOMMouseScroll', stopWheel, false);
       }
    });

    $('.scroller input[type=number]').blur(function(){
        document.onmousewheel = null;  /* IE7, IE8 */
        if(document.addEventListener){ /* Chrome, Safari, Firefox */
            document.removeEventListener('DOMMouseScroll', stopWheel, false);
        }
    });
});