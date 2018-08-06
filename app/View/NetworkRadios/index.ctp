<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Radio', array('action' => 'add')); ?></li>
    </ul>
    </div>
    
    <?php
        // CakePHPs FormHelper changes camel cased controller names, e.g.
        // NetworkRadios becomes network_radios -- manually creating this search
        // form
    ?>
    <H3>Search</H3>
    <div class="well">
        <form action="/networkRadios/index" id="NetworkRadioIndexForm" method="post">
            <div class="input text"><label for="NetworkRadioName">Name</label>
                <input name="data[NetworkRadio][name]" class="search-query" maxlength="10" type="text" value="*" id="NetworkRadioName">
            
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
    <h2>Radios</h2>
    <table class="table table-condensed table-striped table-hover">
        <thead>
            <tr>
                <th class="index-status"><?php echo $this->Paginator->sort('is_down','Status'); ?></th>
                <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                <th class="index-item"><?php echo $this->Paginator->sort('site_id'); ?></th>
                <th class="index-item"><?php echo $this->Paginator->sort('radio_type_id'); ?></th>
                <th class="index-item"><?php echo $this->Paginator->sort('ssid','SSID'); ?></th>
                <th class="index-action"><?php echo 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($networkradios as $networkradio): ?>
    <tr>
        <td class="index-status"><?php echo $this->element('Common/site_status_icon', array('status' => $networkradio['NetworkRadio']['is_down'])); ?></td>
        <td class="index-item"><?php echo $this->Html->link($networkradio['NetworkRadio']['name'], array('action' => 'view', $networkradio['NetworkRadio']['id']))?></td>
        <td class="index-item"><?php echo $networkradio['Site']['site_vf'];?></td>
        <td class="index-item"><?php echo $networkradio['RadioType']['name'];?></td>
        <td class="index-item"><?php echo $networkradio['NetworkRadio']['ssid'];?></td>
        <td class="index-action">
            <?php
                echo $this->PoundcakeHTML->linkIfAllowed('Edit', array('action' => 'edit', $networkradio['NetworkRadio']['id']));
                echo '&nbsp;';
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                    array('controller'=>'networkRadios','action'=>'delete', $networkradio['NetworkRadio']['id']),
                    array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$networkradio['NetworkRadio']['name']),
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