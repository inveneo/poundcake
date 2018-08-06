<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('View Switch', array('action'=>'view', $id ));?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Alarms for <?php echo $name; ?> </h2>
    <?php echo $this->element( 'Common/alarms', $alarms ); ?>  
</div> <!-- /.span9 -->
</div> <!-- /.row -->