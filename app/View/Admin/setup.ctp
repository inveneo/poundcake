<div class="row">

<div class="span12">
    <H2>Setup</H2>

    <div class="row">
    
        <div class="alert" align="center">
            <P>
        <!--        <button type="button" id="1" class="close" data-dismiss="alert">&times;</button>-->
                <strong>Caution!</strong> Many items in Tower DB are configurable by users with administrator 
                permissions.  Changing some of these values may have an unintended impact to 
                existing projects or the system, overall.  Please proceed with caution.
            </P>
            <!--
            <P> 
                <i class="icon-globe"></i> Settings with this icon are global in nature, and affect all projects.
            </p>
            -->
        </div>
    
        <div class="span4">
            <H4>Basic Administration</H4><hr>
            <UL>
                <li><?php echo $this->Html->link('Roles', '/admin/roles/index'); ?></li>
                <li><?php echo $this->Html->link('Site State Icons', '/admin/siteStateIcons/index'); ?></li>
                <li><?php echo $this->Html->link('System Banner', '/admin/notifications/edit'); ?></li>
                <li><?php echo $this->Html->link('Users', '/admin/users/index'); ?></li>            
            </UL>
        </div>
        
        <div class="span4">
            <H4>Projects</H4><hr>
            <UL>    
                <li><?php echo $this->Html->link('Projects', '/admin/projects/index'); ?></li>
                <li><?php echo $this->Html->link('Site States', '/admin/siteStates/index'); ?></li>
            </UL>            
        </div> <!-- /.span4 -->
        
        <div class="span4">
            <H4>Miscellaneous</H4><hr>
            <UL>    
                <li><?php echo $this->Html->link('Build Items', '/admin/buildItems/index'); ?></li>
                <li><?php echo $this->Html->link('Contact Types', '/admin/contactTypes/index'); ?></li>
                <li><?php echo $this->Html->link('Install Teams', '/admin/installTeams/index'); ?></li>        
                <li><?php echo $this->Html->link('Stats', '/admin/stats'); ?></li>
                <li><?php echo $this->Html->link('Zones', '/admin/zones/index'); ?></li>
                
            </UL>
        </div> <!-- /.span4 -->   
    </div>
    
    <div class="row">
        <div class="span4">
            <H4>Sites</H4><hr>
            <UL>
                <li><?php echo $this->Html->link('Equipment Spaces', '/admin/equipmentSpaces/index'); ?></li>
                <li><?php echo $this->Html->link('Organizations', '/admin/organizations/index'); ?></li>    
                <li><?php echo $this->Html->link('Power Types', '/admin/powerTypes/index'); ?></li>
                <li><?php echo $this->Html->link('Tower Members', '/admin/towerMembers/index'); ?></li>    
                <li><?php echo $this->Html->link('Tower Mounts', '/admin/towerMounts/index'); ?></li>            
                <li><?php echo $this->Html->link('Tower Type', '/admin/towerTypes/index'); ?></li>            
            </UL>
        </div> <!-- /.span4 -->
        
        <div class="span4">
            <H4>Network Equipment</H4><hr>
            <UL>
                <li><?php echo $this->Html->link('Antenna Types', '/admin/antennaTypes/index'); ?></li>
                <li><?php echo $this->Html->link('Configuration Templates', '/admin/configurationTemplates/index'); ?></li>
                <li><?php echo $this->Html->link('Frequencies', '/admin/frequencies/index'); ?></li>
                <li><?php echo $this->Html->link('Interface Types', '/admin/networkInterfaceTypes/index'); ?> </li>
                <li><?php echo $this->Html->link('Radio Bands', '/admin/radioBands/index'); ?></li>
                <li><?php echo $this->Html->link('Radio Modes', '/admin/radioModes/index'); ?> </li>
                <li><?php echo $this->Html->link('Radio Types', '/admin/radioTypes/index'); ?> </li>
                <li><?php echo $this->Html->link('Router Types', '/admin/routerTypes/index'); ?> </li>
                <li><?php echo $this->Html->link('Switch Types', '/admin/switchTypes/index'); ?> </li>
            </UL>
        </div>
        
        <div class="span4">
            <H4>Monitoring</H4><hr>
            <UL>    
                <li><?php echo $this->Html->link('Monitoring System Types', '/admin/monitoringSystemTypes/index'); ?></li>
                <li><?php echo $this->Html->link('Network Services', '/admin/networkServices/index'); ?></li>        
                <li><?php echo $this->Html->link('SNMP Versions', '/admin/snmpTypes/index'); ?></li>        
            </UL>             
        </div> <!-- /.span4 --> 
    </div>
</div> <!-- /.span12 -->
</div> <!-- /.row -->