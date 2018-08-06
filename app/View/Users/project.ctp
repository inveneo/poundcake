<div class="row">
<div class="span3">

</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('User'); ?>
    <h2>Switch Project</h2>
    <?php
        echo $this->Form->input('Project.Project',array(
            'label' => __('Projects',true),
            'type' => 'select',
            //'multiple' => 'checkbox',
            'options' => $projects,
            'selected' => $this->Html->value('Project.Project'),
        ));

        echo $this->Form->end('Switch');
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
