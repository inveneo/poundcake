<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Contact', array('action' => 'add')); ?></li>
    </ul>
    </div>
    <H3>Search</H3>
    <?php
    
      echo $this->Form->create(
          'Contact',
          // calls the search function on the SchoolsController
          array('action'=>'search','class' => 'well')
      );
     
      echo $this->Form->input('first_name',array('escape' => true,'class' => 'search-query','required' => false));
      echo '<br>';
      echo $this->Form->input('last_name',array('class' => 'search-query','required' => false));
      echo '<br>';
      ?>
    <div align="center">
    <span class="help-block"></span>
    <?php
        $options = array(
            'label' => 'Search',
            //'name' => 'Update',
            //'div' => array(        'class' => 'glass-pill',    ));
            'div' => false,
            'class'=>'btn btn-primary'
            );
        
        echo $this->Form->end($options);
    ?>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Contacts</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item"><?php echo $this->Paginator->sort('name_vf','Name'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('title'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('Organization.name','Organization'); ?></th>
                    <th class="index-item" style='text-align:center'><?php echo $this->Paginator->sort('priority'); ?></th>
                    <th class="index-action"><?php echo ('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($contacts as $contact): ?>
                <?
//                echo '<pre>';
//                print_r($contact);
//                echo '</pre>';
                ?>
	<tr>
            <td class="index-item"><?php
                //echo $contact['Contact']['name_vf'];
                    echo $this->Html->link($contact['Contact']['name_vf'],
                    array('controller' => 'contacts', 'action' => 'view', $contact['Contact']['id']));
                ?>
            </td>
            <td class="index-item"><?php echo $contact['Contact']['title'];?></td>
            <td class="index-item"><?php echo $contact['Organization']['name'];?></td>
            <td class="index-item" style='text-align:center'><?php echo $contact['Contact']['priority'];?></td>
            <td class="index-action">
            <?php echo $this->PoundcakeHTML->linkIfAllowed(('Edit'), array('action' => 'edit', $contact['Contact']['id'])); ?>
            <?php //echo $this->PoundcakeHTML->postLinkIfAllowed(('Delete'), array('action' => 'delete', $contact['Contact']['id']), null, __('Are you sure you want to delete contact %s?', $contact['Contact']['first_name'])); ?>
            <?php
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                    array('controller'=>'contacts','action'=>'delete', $contact['Contact']['id']),
                    array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$contact['Contact']['name_vf']),
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