<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New Site State', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Copy Site States', array('action' => 'copy')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>    
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Site States</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('sequence'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('project_id'); ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($siteStates as $siteState): ?>
	<tr>
            <td class="index-item"><?php echo $this->Html->link(($siteState['SiteState']['name']), array('action' => 'view', $siteState['SiteState']['id'])); ?></td>
            <td class="index-item"><?php echo $siteState['SiteState']['sequence']; ?></td>
            <td class="index-item"><?php echo $siteState['Project']['name']; ?></td>
            <td class="index-action">
            <?php
                echo $this->PoundcakeHTML->link('Edit', array('action' => 'edit', $siteState['SiteState']['id']));
                echo '&nbsp;';
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                            array('controller'=>'siteStates','action'=>'delete', $siteState['SiteState']['id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$siteState['SiteState']['name']),
                            null
                        );
            ?>
            </td>
	</tr>
        <?php endforeach; ?>
            </tbody>
        </table>

	
	<?php
            // include pagination
            // file is in ./View/Elements/Common/pagination.ctp
            echo $this->element('Common/pagination');
        ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->