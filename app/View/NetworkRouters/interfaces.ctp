<?php
    // jQuery to enable/disable fields based on checkbox
    // echo $this->Html->script('poundcake/sector');
    echo $this->Html->script('poundcake/poundcake-snmp-override');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('View Router', array('action' => 'view',$id,null)); ?>
        <li><?php echo $this->PoundcakeHTML->link('List Routers', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('NetworkInterfaceIpSpace');
    if ( count($interfaces) > 0 ) {
        echo '<h2>Interfaces</h2>';       
    } else {
        echo '<h2>Interface</h2>';
    }
    ?>
    <?php
        $primary_interfaces = array();
        $selected = 0;
        
        $n = 0;
        foreach( $interfaces as $interface ) {
            $label = $if_name.$n;
            $primary_interfaces = $primary_interfaces +  array( $n => $label );
            if ( $interface['NetworkInterfaceIpSpace']['if_primary'] > 0 ) {
                $selected = $n;
            }
            
            echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.ip_space_id',
                    array(
                        'type' => 'select',
                        'label' => $label, // eth0, eth1, etc.
                        'options' => $ip_spaces,
                        'empty' => true,
                        'escape' => false, // because the $ip_spaces array is nested hierarchically with spaces
                        'default' => $interface['NetworkInterfaceIpSpace']['ip_space_id']
                    ));
            // the number of the interface
            echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.if_number',
                    array(
                        'value' => $n,
                        'type' => 'hidden'
                ));
            echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.network_interface_type_id',
                    array(
                        'value' => $interface['NetworkInterfaceIpSpace']['network_interface_type_id'],
                        'type' => 'hidden'
                ));
            echo $this->Form->input('NetworkInterfaceIpSpace.'.$n.'.network_router_id',
                    array(
                        'value' => $id, // NetworkRouter.id
                        'type' => 'hidden'
                ));                
            $n++;
        }
        
        echo $this->Form->input('if_primary', array(
            'label' => 'Primary Interface',
            'options' => $primary_interfaces,
            'empty' => true,
            'default' => $selected
        ));
        
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->