<?php
    // jQuery to enable/disable fields based on checkbox
    echo $this->Html->script('poundcake/poundcake-snmp-override');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('View Switch', array('action' => 'view', $id)); ?></li>    
        <li><?php echo $this->PoundcakeHTML->link('List Switches', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('NetworkSwitch'); ?>
    <h2>Edit Switch</h2>
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        echo $this->Form->input('serial');
        echo $this->Form->input('switch_type_id', array('type'=>'select','options' => $switchtypes));
        // see comments in edit for why this field is here
        echo $this->Form->input('old_site_id', array('type'=>'hidden','value' => $old_site_id ));
        echo $this->Form->input('Site.id', array('type'=>'select','options' => $sites));
        echo $this->Form->input('foreign_id',array('type' => 'text','label'=>'Foreign ID'));
        echo $this->element('Common/snmp_override');  // include fiels for SNMP override
        // the IPv4 behavior is correctly decoding the IP address, but this doesn't
        // seem to populate the form:
        // echo $this->Form->input('ip_address', array('label'=>'Primary IP'));
        echo $this->Form->input('ip_address', array(
            'label' => 'IP Address (Legacy)',
            'type' => 'text',
            // 'value' => '1\.2\.3\.4'
            'value' => $this->data['NetworkSwitch']['ip_address'],
            'placeholder' => 'For Testing'
        ));
        echo $this->Form->input('ip_space_id', array('type'=>'select','label' => 'IP Address','options' => $ip_spaces));
        echo $this->Form->input('project_id', array('type'=>'hidden','value' => $project_id ));
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
