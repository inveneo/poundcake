<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Router', array('action' => 'add')); ?></li>
    </ul>
    </div>

    <?php
        // CakePHPs FormHelper changes camel cased controller names, e.g.
        // NetworkRadios becomes network_radios -- manually creating this search
        // form
    ?>
    <H3>Search</H3>
    <div class="well">
        <form action="/networkRouters/index" id="NetworkRouterIndexForm" method="post">
            <div class="input text"><label for="NetworkRadioName">Name</label>
                <input name="data[NetworkRouter][name]" class="search-query" maxlength="10" type="text" value="*" id="NetworkRouterName">
            
                <div class="btn-toolbar">
                    <div align="center">
                        <input class="btn btn-primary" type="submit" value="Search">
                    </div>
                </div>        
            </div>
        </form>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Routers</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-status"><?php echo $this->Paginator->sort('is_down','Status'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('router_type_id'); ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($networkrouters as $networkrouter): ?>
	<tr>
            <td class="index-status"><?php echo $this->element('Common/site_status_icon', array('status' => $networkrouter['NetworkRouter']['is_down'])); ?></td>
            <td class="index-item"><?php echo $this->Html->link($networkrouter['NetworkRouter']['name'], array('action' => 'view', $networkrouter['NetworkRouter']['id'])); ?></td>
            <td class="index-item"><?php echo $networkrouter['RouterType']['name']; ?></td>
            <td class="index-action">
                <?php echo $this->PoundcakeHTML->linkIfAllowed('Edit', array('action' => 'edit', $networkrouter['NetworkRouter']['id'])); ?>
                <?php
                    //echo $this->PoundcakeHTML->postLinkIfAllowed('Delete', array('action' => 'delete', $networkrouter['NetworkRouter']['id']), null, __('Are you sure you want to delete router %s?', $networkrouter['NetworkRouter']['name']));
                    echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                        array('controller'=>'networkrouters','action'=>'delete', $networkrouter['NetworkRouter']['id']),
                        array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$networkrouter['NetworkRouter']['name']),
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