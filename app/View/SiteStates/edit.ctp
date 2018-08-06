<?php
    // Jasny's file upload
    echo $this->Html->script('jasny/bootstrap-fileupload'); 
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Site States', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php  ?>
    <h2>Edit Site State</h2>
    <?php
        echo $this->Form->create('SiteState');
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        echo $this->Form->input('project_id');
        echo $this->Form->input('sequence');
        echo $this->element('Common/site_state_icons');
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
   ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->