<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Site States', array('action' => 'index')); ?>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Site State</h2>
    <dl class="dl-horizontal">
        <dt>Name</dt>
        <dd><?php echo $siteState['SiteState']['name']; ?></dd>
        <dt>Icon</dt>
        <dd><?php echo '<img src="data:'.$siteState['SiteStateIcon']['img_type'].';base64,'.base64_encode( $siteState['SiteStateIcon']['img_data'] ) . '" />'; ?></dd>        
    </dl>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
