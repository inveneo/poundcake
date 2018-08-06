<?php
    // Jasny's file upload
    echo $this->Html->script('jasny/bootstrap-fileupload'); 
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Site State Icons', array('action' => 'index')); ?>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php
        //echo $this->Form->create('SiteStateIcon',array('action' => 'add', 'type' => 'file'));
        echo $this->Form->create('SiteStateIcon', array('type' => 'file'));
    ?>
    <h2>Add Site State Icon</h2>
    <div class="fileupload fileupload-new" data-provides="fileupload">
      <div class="fileupload-new thumbnail" style="width: 100px; height: 75px;"><img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" /></div>
      <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
      <div>
        <span class="btn btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" name="data[SiteStateIcon][File]"/></span>
<!--        <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>-->
      </div>
    </div>
    <?php
//        foreach ( $all_icons as $icon ) {
//            echo '<img src="data:'.$icon['SiteStateIcon']['img_type'].';base64,'.base64_encode( $icon['SiteStateIcon']['img_data'] ) . '" />';
//            echo $this->Form->input('sequence');
//        }
    ?>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
