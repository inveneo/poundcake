<?php
    // this is used on SiteState add/edit
?>
<table>
    <?php
        $u = 1;
        $mod_by = 11;
        foreach ( $all_icons as $icon ) {
            if ( ( $u % $mod_by ) === 0 ) {
                echo '<tr>';
                $u = 1;
            }
            echo '<td align="center"><img src="data:'.$icon['SiteStateIcon']['img_type'].';base64,'.base64_encode( $icon['SiteStateIcon']['img_data'] ) . '" />';
            echo '<BR>';
            echo '<input type="radio" name="data[SiteState][site_state_icon_id]" value="'.$icon['SiteStateIcon']['id'].'"';
            
            if ( $this->request->data != null ) {
                if ( $icon['SiteStateIcon']['id'] == $this->request->data['SiteState']['site_state_icon_id'] ) {
                    echo ' checked';
                }
            }
            echo '>';
            echo '</td>&nbsp;&nbsp;&nbsp;';
            if ( ( $u % $mod_by ) === 0 ) {
                echo '</tr>';
            }
            $u++;
        }
    ?>
</table>
<BR>