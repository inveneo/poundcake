<?php
    // jQuery to enable/disable quantity field when an interface type is selected/de-selected
    echo $this->Html->script('poundcake/poundcake-networkinterfacetypes');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Radio Types', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('RadioType'); ?>
    <h2>Edit Radio Type</h2>
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        echo $this->Form->input('manufacturer');
        echo $this->Form->input('watts');
        echo $this->Form->input('value');
        // checkboxes for NetworkInterfaceTypes
        echo $this->element('Common/network_interface_types');
        
        echo $this->Form->input('radio_band_id', array('type'=>'select','options' => $radiobands));
        // checkboxes for compatable antenna types
        echo $this->Form->input('AntennaType.AntennaType',array(
            'label' => 'Compatible Antennas',
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $antennaTypes,
        ));
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
