<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Site States', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php  ?>
    <h2>Copy Site States</h2>
    <p>
        Copy an existing set of Site States to another project.  Note that:
        <ul>
            <li>This is a <strong>destructive copy</strong> - any Site States that may
                already exist in the destination project are unceremoniously deleted.</li>
            <li>All sites in the destination project are set to the Site State with
                the lowest sequence number.</li>
        </ul>
    It is recommended this action be performed on new projects before sites are added as manual data cleanup may be necessary.
    </p>
    <?php
        echo $this->Form->create('SiteState');
        echo $this->Form->input('project_src', array('type'=>'select','options' => $projects, 'label' => 'Source Project'));
        echo $this->Form->input('project_dest', array('type'=>'select','options' => $projects, 'label' => 'Destination Project'));
        echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
        echo $this->Form->end(); 
   ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->