<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Switch', array('action' => 'add')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>Switches</h2>
    <table class="table table-condensed table-striped table-hover">
        <thead>
            <tr>
                <th class="index-status"><?php echo $this->Paginator->sort('is_down','Status'); ?></th>
                <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                <th class="index-action"><?php echo 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($networkswitches as $networkswitch): ?>
    <tr>
        <td class="index-status"><?php echo $this->element('Common/site_status_icon', array('status' => $networkswitch['NetworkSwitch']['is_down'])); ?></td>
        <td class="index-item"><?php echo $this->Html->link($networkswitch['NetworkSwitch']['name'], array('action' => 'view', $networkswitch['NetworkSwitch']['id'])); ?></td>
        <td class="index-action">
            <?php echo $this->PoundcakeHTML->linkIfAllowed(('Edit'), array('action' => 'edit', $networkswitch['NetworkSwitch']['id'])); ?>
            <?php echo $this->PoundcakeHTML->postLinkIfAllowed(('Delete'), array('action' => 'delete', $networkswitch['NetworkSwitch']['id']), null, __('Are you sure you want to delete switch %s?', $networkswitch['NetworkSwitch']['name'])); ?>
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