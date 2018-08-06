
<?php
    // icons from here: http://www.wpclipart.com/blanks/shapes/color_labels/circle/color_label_circle_yellow.png.html                   
    switch ( true ) {
        case ( $status == null ):
            $icon = 'grey.png';
            $alt = 'Unknown Status';
            break;
        case ( $status == 0 ):
            $icon = 'green.png';
            $alt = 'All Nodes Up';
            break;
        case (( $status > 0 ) && ( $status < 1 )):
            $icon = 'yellow.png';
            $alt = 'Some Nodes Down';
            break;
        case ( $status == 1 ):
            $icon = 'red.png';
            $alt = 'All Nodes Down';
            break;
        default:
            $icon = 'gray.png';
            $alt = 'Unknown Status';
            break;
    }
    // $status = (int)$status;    
//    if ( is_null( $status )) {
//        $icon = 'grey.png';
//        $alt = 'Unknown Status';
//    } elseif ( $status === 0 ) {
//        $icon = 'green.png';
//        $alt = 'All Nodes Up';
//    } elseif (( $status > 0 ) && ( $status < 1 )) {
//        $icon = 'yellow.png';
//        $alt = 'Some Nodes Down';
//    } elseif ( $status === 1 ) {
//        $icon = 'red.png';
//        $alt = 'All Nodes Down';
//    } else {
//        $icon = 'gray.png';
//        $alt = 'Unknown Status';
//    }
//    echo '<pre>';
//    print_r( $status );
//    debug( $status );
//    var_dump( $status );
//    echo '</pre>';
    
    echo $this->Html->image( $icon, array( 'alt' => $alt )); 
?>