<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Install Teams', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Install Team</h2>
    <P><B>Name:</B>&nbsp;<?php echo $installteam['InstallTeam']['name']; ?></P>
    
    <?php
    //echo '<pre>';
    echo '<UL>';
    foreach ($installteam['Contact'] as $key => $value) {
        //print_r($installteam['Contact']);
        echo '<LI>'.$this->Html->link($installteam['Contact'][$key]['name_vf'], array('action' => 'view', 'controller' => 'contacts',$installteam['Contact'][$key]['id'])).'</LI>';
    }
    echo '</UL>';
    //echo '</pre>';  
?>
    
</div> <!-- /.span9 -->
</div> <!-- /.row -->
