<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List IP Spaces', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('IpSpace'); ?>
    <h2>Edit IP Space</h2>    
    <?php    
        echo '<label>IP Address</label>';
        echo $ip_address;
        echo $this->Form->input('name', array( 'required' => false ));
        
        // only show the gateway field if ther are valid /32s to set for a gateway
        if ( isset($gw_addresses ) && (count($gw_addresses) > 0 ) ) {
            echo $this->Form->input('gateway_id',
                    array(
                        'label' => 'Gateway',
                        'options' => $gw_addresses,
                        'empty'=> true
                )
            );
        }
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->