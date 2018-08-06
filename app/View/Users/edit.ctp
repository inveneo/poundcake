
<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Users', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('User'); ?>
    <h2>Edit User: <?php echo $username ?></h2>
    
    <div class="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Note!</strong> If adding or removing Administrator permission for a user,
        you may leave the password field blank.  This will preserve their existing
        password, it will not reset the user’s password to empty.
    </div>
    
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('password',array('required'=>false));
        echo $this->Form->input('admin', array('label' => 'System Administrator'));        
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
    When promoting an existing user to Administrator, leaving the password field empty
    will preservie their existing password.
    
</div> <!-- /.span9 -->
</div> <!-- /.row -->