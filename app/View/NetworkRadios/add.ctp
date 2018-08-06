<?php
    // jQuery to enable/disable fields based on checkbox
    // echo $this->Html->script('poundcake/sector');
    echo $this->Html->script('poundcake/poundcake-snmp-override');
    echo $this->Html->script('poundcake/poundcake-switch-router-port-change');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Radios', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('NetworkRadio'); ?>
    <h2>Add Radio</h2>
    <?php
        echo $this->Form->input('name');
        echo $this->Form->input('site_id', array('type'=>'select','options' => $sites,'empty' => false));
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
        echo $this->Form->input('serial');
        echo $this->Form->input('sector', array('label'=>'Sector Radio'));
        // sector will default to being un-checked, so we can disable this field by default
        echo $this->Form->input('true_azimuth', array('label'=>'True Azimuth','disabled' => true));
        echo $this->Form->input('radio_type_id', array('type'=>'select','options' => $radiotypes,'default'=>'1'));
        echo $this->Form->input('antenna_type_id', array('type'=>'select','options' => $antennatypes));
        echo $this->Form->input('radio_mode_id', array('type'=>'select','options' => $radiomodes));
        echo $this->Form->input('elevation');
        echo $this->Form->input('min_height', array('label'=>'Min. Height (meters)','value'=>'20')); // default value set in model?  Setting _schema not working?!
        echo $this->Form->input('frequency', array('type'=>'select','options' => $frequencies));
        echo $this->Form->input('ssid', array('label'=>'SSID'));
        echo $this->Form->input('p2mp', array('label'=>'Multipoint end of P2MP link'));
        echo $this->Form->input('ip_address',
                array('type' => 'text',
                    'label'=>'IP Address (Legacy)'
                )
        );
        echo $this->Form->input('configuration_template_id', array('label'=>'Configuration Template','options' => $configuration_templates,'empty'=>true));
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->

<?php
    // include the JQuery to handle updating the list of available
    // switches for the selected site
    echo $this->element('Common/site_change');
    // and when the radio changes, update the frequencies
    echo $this->element('Common/radio_change');
?>