<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New Network Service', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Network Services</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th class="index-item"><?php echo 'Checked'; ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($networkservices as $item): ?>
	<tr>
            <td class="index-item"><?php echo $item['NetworkService']['name'];?></td>
            <td class="index-item"><?php if ( $item['NetworkService']['default'] > 0 ) { echo "Yes"; } ?></td>
            <td class="index-action">
            <?php
                echo $this->PoundcakeHTML->link('Edit', array('action' => 'edit', $item['NetworkService']['id']));
                echo '&nbsp;';
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                        array('controller'=>'networkservices','action'=>'delete', $item['NetworkService']['id']),
                        array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$item['NetworkService']['name']),
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
            echo $this->element('Common/pagination');
        ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->