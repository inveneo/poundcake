<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Projects', array('action' => 'index')); ?>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Project</h2>
    <dl>
    <dt>Name</dt><dd><?php echo $project['Project']['name']; ?></dd>
    <dt>Coordinates</dt><dd><?php echo sprintf("%01.5f", $project['Project']['default_lat']) .', '. sprintf("%01.5f", $project['Project']['default_lon']) ?></dd>
    <dt>Workorder Title</dt><dd><?php echo $project['Project']['workorder_title']; ?></dd>
    <dt>Datetime Format</dt><dd><?php echo $project['Project']['datetime_format']; ?></dd>
    <dt>SNMP Version</dt><dd><?php echo $project['SnmpType']['name']; ?></dd>
    <dt>SNMP Community Name</dt><dd><?php echo $project['Project']['snmp_community_name']; ?></dd>
    <dt>SNMP Contact</dt><dd><?php echo $project['Project']['snmp_contact']; ?></dd>
    <dt>Read-Only Monitoring System Integration</dt><dd><?php echo ( $project['Project']['read_only'] > 0 ? "Yes" :  "No" ) ?></dd>
    <dt>Monitoring System Type</dt><dd><?php echo $project['MonitoringSystemType']['name']; ?></dd>
    <dt>Monitoring System Username</dt><dd><?php echo $project['Project']['monitoring_system_username']; ?></dd>
    <dt>Monitoring System Password</dt><dd><?php echo $project['Project']['monitoring_system_password']; ?></dd>
    <dt>Monitoring System ReST URL</dt><dd><?php echo $project['Project']['monitoring_system_url']; ?></dd>
    <dt>Primary Nameserver</dt><dd><?php echo $project['Project']['dns1']; ?></dd>
    <dt>Secondary Nameserver</dt><dd><?php echo $project['Project']['dns2']; ?></dd>
    </dl>
    
    <dl>
        <dt>Users with access to this project:</B>
        <?php
            foreach ($project_users as $user ) {
                echo '<dd>';
                echo $user['username'];
                echo ', '.$user['role'];
                echo '</dd>';
            }
        ?>
    </dl>
    
</div> <!-- /.span9 -->
</div> <!-- /.row -->
