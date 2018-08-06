<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Antenna Types', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('AntennaType'); ?>
    <h2>Edit Antenna Type</h2>
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        // checkboxes for compatable radio types
        echo $this->Form->input('RadioType.RadioType',array(
            'label' => 'Compatible Radios',
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $radioTypes,
        ));
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
