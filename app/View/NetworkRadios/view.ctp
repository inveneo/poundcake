<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Alarms', array('action'=>'alarms', $networkradio['NetworkRadio']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Events', array('action'=>'events', $networkradio['NetworkRadio']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Edit Radio', array('action'=>'edit', $networkradio['NetworkRadio']['id']),1);?></li>
        <li><?php
            echo $this->PoundcakeHTML->postLinkIfAdmin('Provision Radio',
                array('controller'=>'networkRadios','action'=>'provision', $networkradio['NetworkRadio']['id'] ),
                array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Provision radio '.$networkradio['NetworkRadio']['name'].' into monitoring system'),
                null,
                null );
            ?>
        </li>
        <?php
            // not sure if this is the best way to decide to show/hide the graphs
            // link
            if ( isset($networkradio['NetworkRadio']['foreign_id']) ) {
                echo '<li>';
                echo $this->PoundcakeHTML->linkIfAllowed('Graphs', array('action'=>'graphs', $networkradio['NetworkRadio']['id']),1);
                echo '</li>';
            }
        ?>
        <?php if (isset($node_detail_url)) {
            echo '<li><i class="icon-info-sign"></i><a href="'.$node_detail_url .'" target="_blank">More Details</a></li>';
        } ?>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Configuration File', array('action'=>'config', $networkradio['NetworkRadio']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('List Radios', array('action' => 'index')); ?></li>        
    </ul>
    </div>
    
    <H3>Interfaces</H3>
    <div class="well">
        <ul>
        <?php
        foreach ($network_interface_types as $nit ) {
            echo '<li>';
            echo $this->PoundcakeHTML->linkIfAllowed(
                'Edit '.$nit['NetworkInterfaceType']['name'],
                array('controller' => 'networkRadios',
                    'action' => 'interfaces',
                    $id,
                    $nit['RadioTypeNetworkInterfaceTypes']['id'],
                    $nit['RadioTypeNetworkInterfaceTypes']['number'],
                )
            );
            echo '</li>';
        }
        ?>
        </ul>
    </div>
    
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Radio</h2>
    <dl class="dl-horizontal">
    <div class="status-icon">
    <dt>Name</dt>
    <dd>
        <?php
            echo $networkradio['NetworkRadio']['name'];
            echo $this->element('Common/site_status_icon', array('status' => $networkradio['NetworkRadio']['is_down']));
        ?>
    </dd>
    </div>
    <dt>Site</dt><dd>
        <?php
            echo $this->PoundcakeHTML->linkIfAllowed( $networkradio['Site']['name'], array('action'=>'view', 'controller'=>'sites',$networkradio['Site']['id']),0);
        ?>
    </dd>
    
    <?php echo $this->element('Common/provisioning_info',
            array(
                'provisioned_on' => $networkradio['NetworkRadio']['provisioned_on'],
                'provisioned_by_name' => $provisioned_by_name,
                'foreign_id' => $networkradio['NetworkRadio']['foreign_id'],
            ));
    ?>
   
    <dt>Serial No</dt><dd><?php echo $networkradio['NetworkRadio']['serial'] ? : 'Unknown'; ?></dd>
    <dt>Sector</dt><dd><?php echo ($networkradio['NetworkRadio']['sector'] > 0 ? "Yes" : "No"); ?>
    <dt>Radio Type</dt><dd><?php echo $networkradio['RadioType']['name'] ? : 'Unknown'; ?></dd>
    <dt>Antenna</dt><dd><?php echo $networkradio['AntennaType']['name'] ? : 'Unknown'; ?></dd>
    <dt>Radio Mode</dt><dd>
        <?php
        echo $networkradio['RadioMode']['name'] ? : 'Unknown';
        if ($networkradio['NetworkRadio']['sector'] = 0)
            echo '<BR><I>Radio Mode for '.$links[0]['network_radios']['name'].': '.$links[0]['radio_modes']['radio_mode_name'] . '</I>';        
    ?></dd>    
    <?php
        // if this is a sector radio, show the true/mag azimuth values from the database
        if ( $sector ) {
            echo '<dt>True Azimuth</dt><dd>';
            echo sprintf("%01.2f",$networkradio['NetworkRadio']['true_azimuth']).'°';
            echo '</P>';
            echo '<dt>Magnetic Azimuth</dt><dd>';
            echo sprintf("%01.2f",$networkradio['NetworkRadio']['mag_azimuth']).'°';
            echo '</P>';
        }
    ?>
    
    <dt>Elevation</dt><dd><?php
        echo $networkradio['NetworkRadio']['elevation'] ? : 'Unknown';
        if ( isset($networkradio['NetworkRadio']['elevation']) )
            echo '°';
    ?>
    </dd>
    <dt>Min. Height (meters)</dt><dd><?php echo $networkradio['NetworkRadio']['min_height'] ? : 'Unknown'; ?></dd>
    <dt>Frequency</dt><dd>
        <?php
            $label = $networkradio['NetworkRadio']['frequency'] .' MHz ('.$networkradio['Frequency']['name'].')';
            echo  $label ? : 'Unknown';
            //echo $networkradio['NetworkRadio']['frequency'] ? : '???'.' MHz';
            //if ( $sector && isset($links[0]) ) 
                //echo '<BR><I>Frequency for '.$links[0]['network_radios']['name'].': '.$links[0]['network_radios']['frequency'] . '</I>';
        ?></dd>
    <dt>SSID</dt>
    <dd>
        <?php echo $networkradio['NetworkRadio']['ssid'] ? : 'Unknown';
        if ( $sector && isset($links[0]) ) 
            echo '<BR><I>SSID for '.$links[0]['network_radios']['name'].': '.$links[0]['network_radios']['ssid'] . '</I>';
        ?>
    </dd>
    <dt>Multipoint Link</dt><dd><?php echo ($networkradio['NetworkRadio']['p2mp'] > 0 ? "Yes" : "No");?></dd>
    
    <?php
//        $label = "";
//        $name = "";
//        $port = "";;
        if ( $networkradio['NetworkRadio']['switch_port'] > 0 ) {
            $label = "Switch";
            $name = $networkradio['NetworkSwitch']['name'];
            $port = $networkradio['NetworkRadio']['switch_port'];
        } elseif ( $networkradio['NetworkRadio']['router_port'] > 0 ) {
            $label = "Router";
            $name = $networkradio['NetworkRouter']['name'];
            $port = $networkradio['NetworkRadio']['router_port'];
        }
        if ( isset($label) && isset($name) && isset($port) ) {
            echo "<dt>$label</dt><dd>$name</dd>";
            echo "<dt>$label</dt><dd>$port</dd>";
        }
    ?>
    
    <dt>SNMP Override</dt><dd><?php echo ($networkradio['NetworkRadio']['snmp_override'] > 0 ? "Yes" : "No");?>
    <?php
        if ( $snmp_override ) {
            echo '<ul>';
            echo '<li>SNMP Version:  '.$networkradio['SnmpType']['name'].'</li>';
            echo '<li>SNMP Community Name: ';
            if ( $snmp_community ) {
              echo $networkradio['NetworkRadio']['snmp_community_name'];
            } else {
                echo '********************';
            }
            echo '</li></ul>';            
        }
    ?>
    
    <dt>IP Address (Legacy)</dt><dd><?php echo $networkradio['NetworkRadio']['ip_address']; ?></dd>
    <dt>Configuration Template</dt><dd><?php echo $networkradio['ConfigurationTemplate']['name']; ?></dd>
    </dl>
    
    <dl>
    <dt>Interfaces</dt>
    <dl  class="dl-horizontal">
        <?php
            foreach ( $if_array as $if ) {
                if ( $if['ip_address'] > 0 ) {
                    echo '<dt>'.$if['if_name'].'</dt>';
                    echo '<dd>';
                    echo $this->PoundcakeHTML->linkIfAllowed(
                        $if['ip_address'],
                        array('controller' => 'IpSpaces',
                            'action' => 'index',
                            $this->Session->read('project_id' )
                        ),
                        false // no icon
                    );
                    if ( $if['if_primary'] ) {
                        echo "&nbsp;<strong>Primary</strong>";
                    }
                    echo '</dd>';
                
                }
            }
            
            
        ?>
    </dl>
    </dl>
    
    <dl class="dl-horizontal">
    <dt>Link Information</dt>
    <dd>
        <?php
        if (count($links) == 0 ) {
            echo "None";
        } else {
        foreach ($links as $link) { ?>
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th colspan="2">Radio Name</th>
                <th>Distance</th>
                <th>True Azimuth</td>
                <th>Magnetic Azimuth</td>
                <th>Actions</td>
            </tr>
            <tr>
                <td style="text-align: center;">
                <?php echo $this->element('Common/site_status_icon', array('status' => $link['network_radios']['is_down'])); ?>
                </td>
                <td>
                    <?php
                    
                        echo $this->PoundcakeHTML->linkIfAllowed(
                            $link['network_radios']['name'],
                            array('action' => 'view',
                                $link['radios_radios']['dest_radio_id']
                                ),
                            null);
                    ?>
                </td>
                <td>
                    <?php
                    if ($sector)
                        echo "N/A";
                    else
                        echo sprintf("%01.2f",$link['network_radios']['link_distance']) .' Km';
                    ?>
                </td>
                <td>
                    <?php
                    if ($sector)
                        echo "N/A";
                    else
                        echo sprintf("%01.2f",$link['network_radios']['true_azimuth']) . '° to ' . $link['network_radios']['name'];
                    ?>
                </td>
                <td>
                   <?php
                   if ($sector)
                        echo "N/A";
                    else
                        echo sprintf("%01.2f",$link['network_radios']['mag_azimuth']) . '° to ' . $link['network_radios']['name'];
                    ?>
                </td>
                <td>
                    <?php
                        echo $this->PoundcakeHTML->postLinkIfAllowed('Pull Config',
                            array('controller'=>'networkRadios','action'=>'pullConfig', $networkradio['NetworkRadio']['id'],$link['radios_radios']['dest_radio_id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Pull config from '.$networkradio['NetworkRadio']['name']),
                            null,
                            'Pull SSID, frequency and mode from '.$link['network_radios']['name'].'?'
                        );
                    ?>
                </td>
            </tr>
        </table>
    <?php }} ?>
    </dd>
    <?php echo $this->element('Common/addrpool_data'); ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->


