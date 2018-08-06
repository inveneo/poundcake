<?php
    // this is basically the same as alarms
    // and, this is currently specific to OpenNMS
    if ( count($events) > 0 ) {
        foreach ( $events as $event ) {

            if ( $event[ 0 ] === "CRITICAL" ) {
                $class = 'alert-error';
            } if ( $event[ 0 ] === "MAJOR" ) {
                $class = 'alert-error';
            } if ( $event[ 0 ] === "WARNING" ) {
                $class = 'alert-info';
            } if ( $event[ 0 ] === "NORMAL" ) {
                $class = 'alert-success';
            } if ( $event[ 0 ] === "MINOR" ) {
                $class = 'alert-info';
            }

            $date = new DateTime( $event[ 2 ] );
            $date = $date->format( 'g:i a \o\n l, F j, Y' );
            echo '<div class="alert '.$class.'"><h4>'.$event[ 0 ].'</h4></div>';
            // echo '<div class="alert '.$class.'"><h4>'.$alarm[ 0 ].'</h4></div>';
            echo '<p>'.$date.'<BR>'.$event[ 1 ] . '</p><BR><BR><BR>';
        }
    } else {
        echo "No events found";
    }
?>