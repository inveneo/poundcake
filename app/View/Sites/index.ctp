<div class="row">
    <?php //echo $this->element('sql_dump'); ?>
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
        <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Site', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Site Linker', array('action' => 'linker')); ?></li>
        <li><?php echo $this->PoundcakeHTML->linkIfAdmin('KML Import', array('action' => 'import')); ?></li>
        <li><?php
            // make the KML link that appears in the URL bar a little prettier by removing: whitespace, (, )
            // this is basiclly duplicated in SitesController::export
            //$project_name = preg_replace('/\s+/', '', $this->Session->read('project_name'));
            //$project_name = preg_replace('/(\(|\))/', '', $project_name);
            echo $this->PoundcakeHTML->linkIfAllowed('KML Export', array('action'=>'export', 'ext'=>'kml' ));
            ?>
        </li>        
    </ul>
    </div>
    
    <?php echo $this->element('Common/search'); ?>
    
    <H3>Install Teams</H3>
    <div class="well">
    <ul>
        <?php
        if ( count($installteams) > 0 ) {
            foreach ($installteams as $key => $value) {
                echo '<LI>';
                echo $this->Html->link(($value), array('action' => 'schedule',$key));
                echo '</LI>';
            }
        } else {
                echo '<LI>None</LI>';
            }
        ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
<h2>Sites</h2>
<table class="table table-condensed table-striped table-hover">
<thead>
   <tr>
       <th class="index-status"><?php echo $this->Paginator->sort('is_down','Status'); ?></th>
       <th class="index-item"><?php echo $this->Paginator->sort('code'); ?></th>
       <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
       <th class="index-item"><?php echo $this->Paginator->sort('Organization'); ?></th>
       <th class="index-item"><?php echo $this->Paginator->sort('site_state_id'); ?></th>
       <th class="index-item"><?php echo $this->Paginator->sort('Zone.name','Zone');?></th> <!-- zone_id -->
       <th class="index-action">Actions</th>
   </tr>
</thead>
<tbody>
    <?php
    foreach ($sites as $site): ?>            
       <tr>
           <td class="index-status">
               <?php
                    //$status = $site['Site']['is_down'];
                    echo $this->element('Common/site_status_icon', array('status' => $site['Site']['is_down']));
                ?>
           </td>
           <td class="index-item"><?php
                    echo $this->Html->link($site['Site']['code'],
                    array('controller' => 'sites', 'action' => 'view', $site['Site']['id']));
                ?>
           </td>
           <td class="index-item">
               <?php
                //echo $this->Html->link($site['Site']['name'],
               // truncate site names longer than 20 characters
                $name = substr($site['Site']['name'], 0, 20);
                if (strlen($site['Site']['name']) > 20 ) {
                    $name .= "...";
                }
                echo $this->Html->link ($name, array('controller' => 'sites', 'action' => 'view', $site['Site']['id']));
                ?>
               
           </td>
           <td class="index-item"><?php echo $site['Organization']['name']; ?></td>
           <td class="index-item"><?php echo $site['SiteState']['name']; ?></td>
           <td class="index-item"><?php echo $site['Zone']['name']; ?></td>
           <td class="index-item">
<!--               <button class="btn btn-mini"></button>-->
            <?php echo $this->PoundcakeHTML->linkIfAllowed('Edit', array('action'=>'edit', $site['Site']['id']));?>
            <?php
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                        array('controller'=>'sites','action'=>'delete', $site['Site']['id']),
                        array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$site['Site']['name']),
                        null
                    );
            ?>
           </td>
       </tr>
    <?php
    endforeach;
    ?>
<tbody>
</table>

<?php
    // include pagination
    echo $this->element('Common/pagination');
?>

</div>
</div> <!-- /row -->