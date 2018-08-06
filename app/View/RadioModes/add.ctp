<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Radio Modes', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('RadioMode'); ?>
    <h2>Add Radio Modes</h2>
    <?php
        echo $this->Form->input('name');
        echo $this->Form->input('inverse_mode_id',
                array(
                    'type'=>'select',
                    'options' => $radiomodes,
                    'label'=>'Inverse Mode (Optional)',
                    'empty' => true
                    ));
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
