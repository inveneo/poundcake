<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Alarms', array('action'=>'alarms', $networkrouter['NetworkRouter']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Events', array('action'=>'events', $networkrouter['NetworkRouter']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Edit Router', array('action'=>'edit', $networkrouter['NetworkRouter']['id']),1);?></li>
        <li><?php
            echo $this->PoundcakeHTML->postLinkIfAdmin('Provision Router',
                array('controller'=>'networkRouters','action'=>'provision', $networkrouter['NetworkRouter']['id'] ),
                array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Provision router '.$networkrouter['NetworkRouter']['name'].' into monitoring system'),
                null,
                null );
            ?>
        </li>
        <?php
            // not sure if this is the best way to decide to show/hide the graphs
            // link
            if ( isset($networkrouter['NetworkRouter']['foreign_id']) ) {
                echo '<li>';
                echo $this->PoundcakeHTML->linkIfAllowed('Graphs', array('action'=>'graphs', $networkrouter['NetworkRouter']['id']),1);
                echo '</li>';
            }
        ?>
        <?php if (isset($node_detail_url)) {
            echo '<li><i class="icon-info-sign"></i><a href="'.$node_detail_url .'" target="_blank">More Details</a></li>';
        } ?>    
        <li><?php echo $this->PoundcakeHTML->link('List Routers', array('action' => 'index')); ?>
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
                array('controller' => 'networkRouters',
                    'action' => 'interfaces',
                    $id,
                    $nit['RouterTypeNetworkInterfaceTypes']['id'],
                    $nit['RouterTypeNetworkInterfaceTypes']['number'],
                )
            );
            echo '</li>';
        }
        ?>
        </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Router</h2>
    <dl class="dl-horizontal">
    <div class="status-icon">
    <dt>Name</dt>
    <dd>
        <?php
            echo $networkrouter['NetworkRouter']['name'];
            echo $this->element('Common/site_status_icon', array('status' => $networkrouter['NetworkRouter']['is_down']));
        ?>
    </dd>
    </div>
    
    <dt>Site</dt><dd>
        <?php
            echo $this->PoundcakeHTML->linkIfAllowed( $networkrouter['Site']['name'], array('action'=>'view', 'controller'=>'sites',$networkrouter['Site']['id']),0);
        ?>
    </dd>
    
    <?php echo $this->element('Common/provisioning_info',
            array(
                'provisioned_on' => $networkrouter['NetworkRouter']['provisioned_on'],
                'provisioned_by_name' => $provisioned_by_name,
                'foreign_id' => $networkrouter['NetworkRouter']['foreign_id'],
            ));
    ?>
    
    <dt>Manufacturer</dt><dd><?php echo $networkrouter['RouterType']['manufacturer']; ?></dd>
    <dt>Model</dt><dd><?php echo $networkrouter['RouterType']['model']; ?></dd>
    <dt>Serial No</dt><dd><?php echo $networkrouter['NetworkRouter']['serial'] ? : 'Unknown'; ?></dd>
    <dt>SNMP Override</dt><dd><?php echo ($networkrouter['NetworkRouter']['snmp_override'] > 0 ? "Yes" : "No");?>
    <dt>IP Address (Legacy)</dt><dd><?php echo $networkrouter['NetworkRouter']['ip_address']; ?>
    <?php
        if ( $snmp_override ) {
            echo '<ul>';
            echo '<li>SNMP Version:  '.$networkrouter['SnmpType']['name'].'</li>';
            echo '<li>SNMP Community Name: ';
            if ( $snmp_community ) {
              echo $networkrouter['NetworkRouter']['snmp_community_name'];
            } else {
                echo '********************';
            }
            echo '</li></ul>';            
        }
    ?>
    </dl>
    
    <dl>
    <dt>Attached Radios</dt>
    <?php
        if (!isset($networkrouter['NetworkRadio'])) {
            echo "<dd>None</dd>";
        } else {
        
            // the array of attached radios doesn't come out sorted by port, by default
            // it would come out in the order they were attached to the switch
            // so re-order the array to make it look more logical
            foreach ($networkrouter['NetworkRadio'] as $radio) {
                //print_r($radio);
                //echo $this->Html->link($contact['first_name']." ".$contact['last_name']), array(
                echo "<dd>";
                echo 'Port '.$radio['router_port'].' - ';
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
</div> <!-- /.span9 -->
</div> <!-- /.row -->


