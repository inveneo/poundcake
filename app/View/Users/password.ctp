<div class="row">
<div class="span3">
    &nbsp;
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>Change Password</h2>
    <?php
        // http://stackoverflow.com/questions/2868665/is-there-a-better-way-to-change-user-password-in-cakephp-using-auth
        echo $this->Form->create('User');
        echo $this->Form->input('id');
        echo $this->Form->input('username',array('type'=>'hidden'));
        echo $this->Form->input('role_id',array('type'=>'hidden'));
        echo $this->Form->input('pwd_current', array('label' => 'Current password', 'type'=>'password', 'value'=>'', 'autocomplete'=>'off'));
        echo $this->Form->input('password', array('label' => 'New password','type'=>'password', 'value'=>'', 'autocomplete'=>'off'));
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
