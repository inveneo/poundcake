<H3>Legend</H3>
<div class="well well-large legend">
    <table>
        <?php
        for($i = 0; $i < sizeof($allSiteStates); ++$i) {

            echo '<tr><td>';
            echo '<img src="data:'.$allSiteStates[$i]['SiteStateIcon']['img_type'].';base64,'.base64_encode( $allSiteStates[$i]['SiteStateIcon']['img_data'] ) . '" />';
            //echo '&nbsp;'.$allSiteStates[$i]['SiteState']['name'];
            echo '</td><td>';
            echo $this->PoundcakeHTML->linkIfAllowed(
                    $allSiteStates[$i]['SiteState']['name'],
                    array(
                        'controller' => 'sites',
                        'action'=>'index',
                        'Site.site_state_id'=>$allSiteStates[$i]['SiteState']['id'],
                    ),
                    false
                    );
            echo '</td></tr>';
        } 
        ?>
    </table>
</div>