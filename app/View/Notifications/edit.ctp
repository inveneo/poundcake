<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('Notification'); ?>
    
    
    <h2>Edit Notification Message</h2>
    <?php
        echo $this->Form->input('id', array( 'type' => 'hidden' ));
//        $attributes = array( 'legend' => false, 'labels' => false, 'style'=>'none;');
//        echo $this->Form->radio('icon', $icons);
//        foreach ( $icons as $key => $value ) {
//            echo '<label class="checkbox inline">';
//            echo '<input type="radio" name="icons" value="'.$key.'"> '.$value.'<br>';
//            echo '</label>';
//        }
//        echo '<br><br><br>';
        echo $this->Form->input('message', array('type' => 'textarea', 'style' => 'width: 500px;', 'escape' => false));
    ?>
    <br>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->