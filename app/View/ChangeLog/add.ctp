<?php
    // JQuery for the datepicker
    echo $this->Html->script('poundcake/poundcake-datepicker');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Changes', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('ChangeLog'); ?>
    <h2>Add Change</h2>
    <?php
        echo $this->Form->input('version');
        echo $this->Form->input('release_date', 
            array(
               'class'=>'datepicker', 
               'type'=>'text'
            )
        );
        echo $this->Form->input('description');
    ?>
    </fieldset>
    <?php 
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->