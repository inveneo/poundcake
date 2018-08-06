<div class="row">
<div class="span3">
    
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Projects', array('action' => 'index')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
        <?php
        echo $this->element('Common/date_format');
        echo $this->element('Common/zoom_level');
    ?>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('Project'); ?>
    <h2>Edit Project</h2>
<!--    <div class="tabbable">  Only required for left/right tabs 
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Basic</a></li>
            <li><a href="#tab2" data-toggle="tab">IP Spaces</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">-->
            <?php
                echo $this->Form->input('id');
                echo $this->Form->input('name');
                echo $this->Form->input('default_lat', array( 'label' => 'Default Latitude' ));
                echo $this->Form->input('default_lon', array( 'label' => 'Default Longitude' ));
                echo $this->Form->input('secure_password', array( 'type'=>'password', 'label' => 'Secure Password' ));
                echo $this->Form->input('insecure_password', array( 'type'=>'password', 'label' => 'Insecure Password' ));
                
                echo $this->Form->input('workorder_title', array( 'label' => 'Title for Workorder' ));
                echo $this->Form->input('datetime_format', array( 'label' => 'Datetime Format (PHP compatible)' ));
                echo $this->Form->input('snmp_type_id', array('type'=>'select','options' => $snmptypes, 'label' => 'SNMP Version', 'empty' => true));
                echo $this->Form->input('snmp_community_name', array( 'label' => 'SNMP Community Name' ));
                echo $this->Form->input('snmp_contact', array( 'label' => 'SNMP Contact' ));
                echo $this->Form->input('read_only', array( 'label' => 'Read-Only Monitoring System Integration' ));
                echo $this->Form->input('monitoring_system_type_id', array('type'=>'select','options' => $monitoringSystemTypes, 'label' => 'Monitoring System Type', 'empty' => true));
                echo $this->Form->input('monitoring_system_username', array( 'label' => 'Monitoring System Username' ));
                echo $this->Form->input('monitoring_system_password', array( 'type'=>'password', 'label' => 'Monitoring System Password' ));
                //echo $this->Form->input('monitoring_system_password', array( 'label' => 'Monitoring System Password' ));
                echo $this->Form->input('monitoring_system_url', array( 'label' => 'Complete URL to ReST API','placeholder' => '' ));
                
                echo $this->Form->input('dns1', array( 'type' => 'text', 'label' => 'Primary Nameserver' ));
                echo $this->Form->input('dns2', array( 'type' => 'text', 'label' => 'Secondary Nameserver' ));
                
                
//                $ips = null;
//                $size = sizeof($ip_addresses);
//                $u = 0;
//                foreach ( $ip_addresses as $key => $val ) {
//                   $ips .= $val;
//                   if ( $u < $size-1 ) {
//                       $ips .= ',';
//                   }
//                   $u++;
//                }
//                
//                echo $this->Form->input('ip_addresses', array( 'value'=> $ips, 'label'=>'Public IP Addresses', 'rows' => '5', 'cols' => '5'));
                echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
                echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
                echo $this->Form->end(); 
            ?>
<!--            </div>
            <div class="tab-pane" id="tab2">
            </div>
        </div>
    </div>-->
    
</div> <!-- /.span9 -->
</div> <!-- /.row -->

