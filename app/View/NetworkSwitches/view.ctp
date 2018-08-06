<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Alarms', array('action'=>'alarms', $networkswitch['NetworkSwitch']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Events', array('action'=>'events', $networkswitch['NetworkSwitch']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Edit Switch', array('action'=>'edit', $networkswitch['NetworkSwitch']['id']),1);?></li>
        <li><?php
            echo $this->PoundcakeHTML->postLinkIfAdmin('Provision Switch',
                array('controller'=>'networkSwitches','action'=>'provision', $networkswitch['NetworkSwitch']['id'] ),
                array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Provision switch '.$networkswitch['NetworkSwitch']['name'].' into monitoring system'),
                null,
                null );
            ?>
        </li>
        <?php
            // not sure if this is the best way to decide to show/hide the graphs
            // link
            if ( isset($networkswitch['NetworkSwitch']['foreign_id']) ) {
                echo '<li>';
                echo $this->PoundcakeHTML->linkIfAllowed('Graphs', array('action'=>'graphs', $networkswitch['NetworkSwitch']['id']),1);
                echo '</li>';
            }
        ?>
        <?php if (isset($node_detail_url)) {
            echo '<li><i class="icon-info-sign"></i><a href="'.$node_detail_url .'" target="_blank">More Details</a></li>';
        } ?>  
        <li><?php echo $this->PoundcakeHTML->link('List Switches', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Switch</h2>
    <dl class="dl-horizontal">
        <div class="status-icon">
        <dt>Name</dt>
        <dd>
            <?php
                echo $networkswitch['NetworkSwitch']['name'];
                echo $this->element('Common/site_status_icon', array('status' => $networkswitch['NetworkSwitch']['is_down']));
            ?>
        </dd>
        </div>

        <dt>Site</dt><dd>
            <?php
                echo $this->PoundcakeHTML->linkIfAllowed( $networkswitch['Site']['name'], array('action'=>'view', 'controller'=>'sites',$networkswitch['Site']['id']),0);
            ?>
        </dd>

        <?php echo $this->element('Common/provisioning_info',
                array(
                    'provisioned_on' => $networkswitch['NetworkSwitch']['provisioned_on'],
                    'provisioned_by_name' => $provisioned_by_name,
                    'foreign_id' => $networkswitch['NetworkSwitch']['foreign_id'],
                ));
        ?>

        <dt>Serial No</dt><dd><?php echo $networkswitch['NetworkSwitch']['serial'] ? : 'Unknown'; ?></dd>
        <dt>Switch Type</dt><dd><?php echo $networkswitch['SwitchType']['name']; ?></dd>
        <dt>Ports</dt><dd><?php echo $networkswitch['SwitchType']['ports']; ?></dd>
        <dt>Manufacturer</dt><dd><?php echo $networkswitch['SwitchType']['manufacturer']; ?></dd>
        <dt>Model</dt><dd><?php echo $networkswitch['SwitchType']['model']; ?></dd>

        <dt>SNMP Override</dt><dd><?php echo ($networkswitch['NetworkSwitch']['snmp_override'] > 0 ? "Yes" : "No");?></dd>
        <?php
            if ( $snmp_override ) {
                echo '<dt>SNMP Version</dt><dd>'.$networkswitch['SnmpType']['name'].'</dd>';
                echo '<dt>SNMP Community Name</dt><dd>';
                if ( $snmp_community ) {
                  echo $networkswitch['NetworkSwitch']['snmp_community_name'];
                } else {
                    echo '********************';
                }
                echo '</dd>';            
            }
        ?>

        <dt>IP Address (Legacy)</dt><dd><?php echo $networkswitch['NetworkSwitch']['ip_address']; ?></dd>
    </dl>
    
    <dl>
    <dt>Attached Radios</dt>
    <?php
        if (!isset($networkswitch['NetworkRadio'])) {
            echo "<dd>None</dd>";
        } else {
        
            // the array of attached radios doesn't come out sorted by port, by default
            // it would come out in the order they were attached to the switch
            // so re-order the array to make it look more logical
            foreach ($networkswitch['NetworkRadio'] as $radio) {
                //print_r($radio);
                //echo $this->Html->link($contact['first_name']." ".$contact['last_name']), array(
                echo "<dd>";
                echo 'Port '.$radio['switch_port'].' - ';
                echo $this->Html->link(($radio['name']), array(
                    'controller' => 'networkRadios',
                    'action' => 'view',
                    $radio['id']));
                echo " ".$radio['name'];                
                echo "</dd>";
            }
        }
    ?>
    </dl>
</div> <!-- /.span9 -->
</div> <!-- /.row -->


