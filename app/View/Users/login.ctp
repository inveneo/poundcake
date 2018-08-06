<BR>
<?php
    echo $this->Session->flash('auth');
    //echo $this->Form->create('User',array('class' => 'form-horizontal'));
    echo $this->Form->create('User');
?>

    <div class="control-group">
      <?php echo $this->Form->input('username',array('class' => 'search-query', 'placeholder' => 'Username' )); ?>
    </div>

    <div class="control-group">
        <?php echo $this->Form->input('password',array('class' => 'search-query', 'placeholder' => 'Password' )); ?>
    </div>

    <div class="control-group">
        <?php echo $this->Form->submit('Login', array('div' => false,'class'=>'btn btn-primary')); ?>
    </div>

    <div class="control-group"></div>
    <?php
        // not using AutoLogin right now, but maybe in the future?  See the Utility plugin
        echo $this->Form->input('auto_login', array('type' => 'checkbox', 'label' => 'Keep me logged in for 2-weeks'));
    ?>
    </div>
    
  <div align="center">
      Request access to Tower DB by emailing &#116;&#111;&#119;&#101;&#114;&#100;&#098;&#045;&#097;&#099;&#099;&#101;&#115;&#115;&#064;&#105;&#110;&#118;&#101;&#110;&#101;&#111;&#046;&#111;&#114;&#103;.
  </div>

  <?php echo $this->Form->end(); ?>
</div>
