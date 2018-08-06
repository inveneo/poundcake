<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Changes', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Change</h2>
    <dl class="dl-horizontal">
    <dt>Version</dt><dd><?php echo $changeLog['ChangeLog']['version']; ?></dd>
    <dt>Date</dt>
    <dd>
        <?php
        //echo $changeLog['ChangeLog']['release_date'];
        $date = new DateTime($changeLog['ChangeLog']['release_date']);
        echo $date->format('Y-m-d');
        ?>
    </dd>
    </dl>
    <BR>
    <dl>
    <dd><?php echo $changeLog['ChangeLog']['description']; ?></dd>
    </dl>
</div> <!-- /.span9 -->
</div> <!-- /.row -->
