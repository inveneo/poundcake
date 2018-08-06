<dt>Provisioned On</dt>
<dd><?php echo $provisioned_on ? : 'Unknown'; ?></dd>

<dt>Provisioned By</dt>
<dd><?php echo $provisioned_by_name ? : 'Unknown'; ?></dd>

<dt>Foreign ID</dt>
<dd><?php echo $foreign_id ? : 'Unknown'; ?></dd>

<dt>Last Checked:</dt>
<dd>
    <?php
        if ( $checked == null ) {
            echo "Unknown";
        } else {
            $date = new DateTime( $checked );
            //echo $date->format($datetime_format);
            echo $date->format( 'g:i a \o\n l, F j, Y' ) ? : 'Unknown';
        }
    ?>
</dd>