<?php
    // jQuery to enable/disable fields based on checkbox
    echo $this->Html->script('poundcake/poundcake-snmp-override');
    
    echo $this->Html->script('jasny/bootstrap-fileupload'); 
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Sites', array('action'=>'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <div class="row">
        <h2>Site Survey</h2>        
        <div class="span9">
        
        <?php        
            echo $this->Form->create('Site', array('type' => 'file'));
        ?>
        <BR><BR>
        
        
        <div class="fileupload fileupload-new" data-provides="fileupload">
  <div class="input-append">
    <div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div>
    <span class="btn btn-file"><span class="fileupload-new">Select file</span><span class="fileupload-exists">Change</span><input type="file" name="data[Site][File]"/></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
  </div>
</div>
        <?php
            //echo $this->Form->file('File');
            //echo $this->Form->end('Import',array('class'=>'btn btn-primary'));
            echo $this->Form->submit('Import', array('div' => false,'class'=>'btn btn-primary'));
            echo $this->Form->end(); 
        ?>
        </div>
        
    </div> <!-- ./row -->
 </div> <!-- ./span9 -->