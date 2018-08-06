<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New Role', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Roles</h2>
        
        <div class="alert">
<!--            <button type="button" class="close" data-dismiss="alert">&times;</button>-->
            <strong>Caution!</strong> Roles are not totally dynamic in Tower DB.
            Adding or editing an existing role is likely to have unintended consequences.
        </div>
        
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($roles as $role): ?>
	<tr>
            <td class="index-item"><?php echo $role['Role']['name'];?></td>
<!--            <td class="index-action">-->
            <td class="index-action">
                <?php
                    echo $this->PoundcakeHTML->linkIfAllowed('Edit', array('action' => 'edit', $role['Role']['id']));
                    echo '&nbsp;';
                    echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                        array('controller'=>'roles','action'=>'delete', $role['Role']['id']),
                        array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$role['Role']['name']),
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