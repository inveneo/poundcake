<?php
    // jQuery to enable/disable fields based on checkbox
    echo $this->Html->script('poundcake/poundcake-primary-ip-override');
?>

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
    <h2>Add Root IP Space</h2>
    <?php
        echo $this->Form->hidden('parent_id', array( 'value' => null ));        
        echo $this->Form->input('name', array( 'value' => $project_name ));
        echo $this->Form->input('cidr', array( 'label' => 'CIDR', 'options' => $cidrs, 'selected'=> '0' ));
        echo $this->Form->input('ip_address',
                array(
                    'type' => 'text',
                    'label'=>'IP Address',
                    'value' => '10.0.0.0'
                )
        );
        echo $this->Form->hidden('project_id', array( 'value' => $project_id ));     
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->