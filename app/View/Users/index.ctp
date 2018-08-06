<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New User', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
    
    <H3>Search</H3>
    <?php
      echo $this->Form->create(
          'User',
          array('action'=>'search','class' => 'well')
      );
      echo $this->Form->input('username',array('escape' => true,'class' => 'search-query'));
      ?>
    <span class="help-block"></span>
    <?php
        echo $this->Form->submit('Search', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->end(); 
    ?>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Users</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('last_login'); ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
        foreach ($users as $user): ?>
	<tr>
		<td class="index-item"><?php echo $user['User']['username']; ?>&nbsp;</td>
		<td class="index-item"><?php
                    $date = new DateTime($user['User']['last_login']);
                    echo date_format($date, 'D m/d/y g:ia');
                    //echo $user['User']['modified']; ?>&nbsp;</td>
		<td class="index-action">
                    <?php
                        echo $this->PoundcakeHTML->link('Edit', array('action' => 'edit', $user['User']['id']));
                        echo '&nbsp;';
                        
                        // if the user is an administrator, don't make this a hyperlink
                        // thoush someone could manually craft the url to get to the page
                        // any edits to ProjectMembers will be ignored
                        if ( $user['User']['admin'] ) {
                            echo '<i class="icon-play-circle"></i>Permissions';
                        } else {
                            echo $this->PoundcakeHTML->link('Permissions', array('action' => 'permissions', $user['User']['id']));
                        }
                        echo '&nbsp;';
                        
                        echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                            array('controller'=>'users','action'=>'delete', $user['User']['id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$user['User']['username']),
                            null
                        )
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
