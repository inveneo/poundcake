<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List IP Spaces', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('IpSpace'); ?>
    <h2>Add Public /32</h2>
    <?php
        echo $this->Form->hidden('parent_id', array( 'value' => null ));
        echo $this->Form->hidden('cidr', array( 'value' => 32 ));        
        echo $this->Form->input('name', array( 'value' => 'ISP Assigned IP' ));
        // we set this to a type of text otherwise CakePHP will only accept an
        // integer, and a dotted quad is not considered an integer
        //  echo $this->Form->input('ip_address', array( 'label' => 'IP Address', 'type' => 'text', 'value' => '199.241.202.20' ));
        echo $this->Form->input('ip_address', array( 'label' => 'IP Address', 'type' => 'text', 'value' => '' ));  
        echo $this->Form->hidden('project_id', array( 'value' => $project_id ));        
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->