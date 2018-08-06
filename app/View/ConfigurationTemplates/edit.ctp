<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Configuration Templates', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('ConfigurationTemplate'); ?>
    <h2>Edit Configuration Template</h2>
    <?php
        echo $this->Form->input('id');
        echo $this->Form->input('name');
        echo $this->element('ConfigurationTemplates/types');
        echo $this->Form->input('Project.Project',array(
            'label' => __('Projects',true),
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $projects,
            'selected' => $this->Html->value('Project.Project'),
        ));
        echo $this->Form->input('body', array('type' => 'textarea', 'style' => 'width: 500px;', 'escape' => false));
    ?>
    </fieldset>
    <?php
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->