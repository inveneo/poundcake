<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Build Items', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('BuildItem');
    //echo $this->element('sql_dump'); ?>
    <h2>Add Build Item</h2>
    <?php
        echo $this->Form->input('name');
        echo $this->Form->input('quantity');
        echo $this->Form->input('build_item_type_id', array('type'=>'select','options' => $builditemtypes)); //,'empty' => true));
        echo $this->Form->input('Project.Project',array(
            'label' => __('Projects',true),
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $projects,
            'selected' => $this->Html->value('Project.Project'),
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