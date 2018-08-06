<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Sites', array('action'=>'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h3>
        <?php
        //print_r($teamname);
        echo $teamname;
        ?>
    </h3>
<table class="table table-condensed table-striped table-hover">
<thead>
   <tr>
       <th>Date</th>
       <th>Site Code</th>
   </tr>
</thead>
<tbody>
    <?php
    foreach ($schedule as $site): ?>
       <tr>
           <?php
//                echo '<pre>';
//                print_r($site);
//                echo '</pre>';
                
           ?>
           <td>
               <?php
               
                if (isset($site['sites']['install_date'])) {
                    $date = new DateTime($site['sites']['install_date']);
                    echo date_format($date, 'Y-m-d');
                } else {
                    echo "Not Assigned";
                }
            ?>
           </td>
           <td><?php
                    echo $this->Html->link($site['sites']['code'],
                    array('controller' => 'sites', 'action' => 'view', $site['sites']['id']));
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
    //echo $this->element('Common/pagination');
?>

</div> <!-- /.span9 -->
</div> <!-- /.row -->