<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New Configuration Template', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
    
    <?php
    
        $terms = array(
            'DNS1' => 'from Project',
            'DNS2' => 'from Project',
            'HOSTNAME' => 'from Radio (Radio Name)',
            'IPADDRESS' => 'from IpSpace',
            'INSECUREPASSWORD' => 'from Project',
            'GATEWAY' => 'from IpSpace',
            'LAT' => 'from Site',
            'LINKDISTANCE' => 'Compute link distance',
            'ACKTIMEOUT' => 'Compute ack timeout',
            'LON' => 'from Site',
            'SITECODE' => 'from Site',
            'SNMPCONTACT' => 'from Project',
            'SNMPCOMMUNITY' => 'from Project',
            'SECUREPASSWORD' => 'from Project',
            'SECUREPASSWORDHASH' => 'from Project',
            'SUBNETMASK' => 'from IpSpace',
            'SSID' => 'from Radio',
            'FREQUENCY' => 'from Radio'
        );
        // sort the array by key
        ksort($terms);
    ?>
 
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Configuration Templates</h2>
        
        <H4>Misc</H4>
        <div class="row">
             <div class="span9">
                 <ul>
                     <li>
                        <code>
                        # Put a hashtag in front of a comment
                        </code>
                    </li>
                 </ul>
             </div>
        </div>
         
        <H4>Keys</H4>
             <div class="row">
            <?php
                $mid = floor(count($terms)/2);
                $terms1 = array_slice($terms,0,$mid,true);
                $terms2 = array_slice($terms,$mid);
                
                echo '<div class="span4"><ul>';
                foreach ($terms1 as $key => $value) {
                    echo '<li><code>%'.$key ."% ".$value.'</code></li>';
                }
                echo '</ul></div><div class="span5"><ul>';
                foreach ($terms2 as $key => $value) {
                    echo '<li><code>%'.$key ."% ".$value.'</code></li>';
                }
                echo '</ul></div>';
            ?>
        </div>
        
        <div class="span9">   
	<table class="table table-condensed table-striped table-hover">
        <thead>
            <tr>
                <th class="index-item"><?php echo $this->Paginator->sort('name'); ?></th>
                <th class="index-action"><?php echo 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
	<?php
	foreach ($configuration_templates as $configuration_template): ?>
	<tr>
            <td class="index-item"><?php echo $configuration_template['ConfigurationTemplate']['name'];?></td>
            <td class="index-action">
                <?php
                    echo $this->PoundcakeHTML->link('Edit', array('action' => 'edit', $configuration_template['ConfigurationTemplate']['id']));
                    echo '&nbsp;';
                    echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                            array('controller'=>'configuration_templates','action'=>'delete', $configuration_template['ConfigurationTemplate']['id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$configuration_template['ConfigurationTemplate']['name']),
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