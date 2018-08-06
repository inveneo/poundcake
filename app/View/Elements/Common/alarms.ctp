<?php
    // this is basically the same as alarms
    // and, this is currently specific to OpenNMS
    if ( count($alarms) > 0 ) {
        foreach ( $alarms as $alarm ) {

            if ( $alarm[ 0 ] === "CRITICAL" ) {
                $class = 'alert-error';
            } if ( $alarm[ 0 ] === "MAJOR" ) {
                $class = 'alert-error';
            } if ( $alarm[ 0 ] === "WARNING" ) {
                $class = 'alert-info';
            } if ( $alarm[ 0 ] === "NORMAL" ) {
                $class = 'alert-success';
            } if ( $alarm[ 0 ] === "MINOR" ) {
                $class = 'alert-info';
            }


            $date = new DateTime( $alarm[ 2 ] );
            $date = $date->format( 'g:i a \o\n l, F j, Y' );
            echo '<div class="alert '.$class.'"><h4>'.$alarm[ 0 ].'</h4></div>';
            // echo '<div class="alert '.$class.'"><h4>'.$alarm[ 0 ].'</h4></div>';
            echo '<p>'.$date.'<BR>'.$alarm[ 1 ] . '</p><BR><BR><BR>';
        }
    } else {
        echo "No alarms found";
    }
?>