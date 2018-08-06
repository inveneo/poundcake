<?php
    // Jasny's file upload
    echo $this->Html->script('jasny/bootstrap-fileupload'); 
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Site State Icons', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('SiteStateIcon', array('type' => 'file')); ?>
    <h2>Edit Site State</h2>
    <?php
        echo $this->Form->input('id');
    ?>
    <p>Current Icon</p>
    <?php
        echo '<img src="data:'.$this->request->data['SiteStateIcon']['img_type'].';base64,'.base64_encode( $this->request->data['SiteStateIcon']['img_data'] ) . '" />';
    ?>
    <br><br>
    <p>Upload New Icon</p>
    <div class="fileupload fileupload-new" data-provides="fileupload">
      <div class="fileupload-new thumbnail" style="width: 100px; height: 75px;"><img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" /></div>
      <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
      <div>
        <span class="btn btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" name="data[SiteStateIcon][File]"/></span>
<!--        <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>-->
      </div>
    </div>
    
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->