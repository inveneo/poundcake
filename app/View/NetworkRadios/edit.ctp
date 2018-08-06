<?php
    // jQuery to enable/disable fields based on checkbox
    echo $this->Html->script('poundcake/poundcake-snmp-override');
    echo $this->Html->script('poundcake/poundcake-switch-router-port-change');
?>
    
<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('View Radio', array('action' => 'view', $id)); ?></li>    
        <li><?php echo $this->PoundcakeHTML->link('List Radios', array('action' => 'index')); ?></li>        
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>Edit Radio</h2>
    <?php
        //echo $this->element('sql_dump');
        echo $this->Form->create('NetworkRadio');
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        echo $this->Form->input('site_id', array('type'=>'select','options' => $sites,'empty' => false));
        echo $this->Form->input('foreign_id',array('type' => 'text','label'=>'Foreign ID'));
        echo $this->Form->input('network_switch_id', array( 'value' => $network_switch_id, 'type' => 'hidden' ));
        echo $this->Form->input('switch_port', array(
            'label' => 'Switch/Port #',
            'type' => 'select',
            'options' => $networkswitches,
            'empty' => true
        ));
        echo $this->Form->input('network_router_id', array( 'value' => $network_router_id, 'type' => 'hidden' ));
        echo $this->Form->input('router_port', array(
            'label' => 'Router/Port #',
            'type' => 'select',
            'options' => $networkrouters,
            'empty' => true
        ));
        
        echo $this->Form->input('sector', array('label'=>'Sector Radio' ));
        // if the radio is a sector then enable the azimuth field
        $sector_disabled = ($this->data['NetworkRadio']['sector'] > 0 ? false : true);
        echo $this->Form->input('true_azimuth', array('label'=>'True Azimuth','disabled' => $sector_disabled));
        echo $this->Form->input('radio_type_id', array('type'=>'select','options' => $radiotypes));
        echo $this->Form->input('antenna_type_id', array('type'=>'select','options' => $antennatypes));
        echo $this->Form->input('radio_mode_id', array('type'=>'select','options' => $radiomodes));
        echo $this->Form->input('elevation');
        echo $this->Form->input('min_height', array('label'=>'Min. Height (meters)'));
        echo $this->Form->input('frequency', array('type'=>'select','options' => $frequencies));
        echo $this->Form->input('ssid', array('label'=>'SSID'));
        echo $this->Form->input('p2mp', array('label'=>'Multipoint end of P2MP link'));
        echo $this->Form->input('configuration_template_id', array('label'=>'Configuration Template','options' => $configuration_templates,'empty'=>true));
        
        // the IPv4 behavior is correctly decoding the IP address, but this doesn't
        // seem to populate the form:
        // echo $this->Form->input('ip_address', array('label'=>'Primary IP'));
//        echo $this->Form->input('ip_address', array(
//            'label' => 'IP Address (Legacy)',
//            'type' => 'text',
//            // 'value' => '1\.2\.3\.4'
//            'value' => $this->data['NetworkRadio']['ip_address'],
//            'placeholder' => 'For Testing'
//        ));
//        
//        echo $this->Form->input('ip_space_id', array('type'=>'select','label'=>'IP Address','options' => $ip_spaces));
        /*
        var_dump($network_interface_types);
        if (isset($this->data['NetworkRadio']['NetworkInterfaceIpSpace'])) {
            foreach( $this->data['NetworkRadio']['NetworkInterfaceIpSpace'] as $n ) {
// id of the correspoding NetworkInterfaceType
                echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.network_interface_type_id',
                        array(
                            'value' => $n['id'],
                            'type' => 'hidden'
                    ));
                echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.ip_space_id',
                        array(
                            'type' => 'select',
                            'label' => $nit['name'].$y, // eth0, eth1, etc.
                            'options' => $ip_spaces,
                            'empty' => true
                        ));
                // id of the correspoding NetworkInterfaceType
                echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.network_interface_type_id',
                        array(
                            'value' => $nit['id'],
                            'type' => 'hidden'
                    ));
                // the number of the interface
                echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.if_number',
                        array(
                            'value' => $y,
                            'type' => 'hidden'
                    ));                        
            }
        } else {
            echo $this->element('network_interface_ip_space_new');
        }
        */
        
        echo $this->element('Common/snmp_override');  // include fields for SNMP override
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->

<?php
    // include the jQuery to handle updating the list of available
    // switches for the selected site
    echo $this->element('Common/site_change');
    // and when the radio changes, update the frequecines and antennas
    echo $this->element('Common/radio_change');
?>