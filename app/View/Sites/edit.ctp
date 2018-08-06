<?php
    // JQuery for the datepicker
    echo $this->Html->script('poundcake/poundcake-datepicker');
    // disable mouse scroll in the lat/lon fields
    echo $this->Html->script('poundcake/poundcake-scroll');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('View Site', array('action' => 'view', $this->Form->value('Site.id')))?></li>
        <li><?php echo $this->PoundcakeHTML->link('List Sites', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h3>Edit Site</h3>
<?php
    echo $this->Form->create('Site', array('action' => 'edit','type' => 'file'));
    echo $this->Form->input('project_id', array('type'=>'hidden','value'=>$this->Session->read('project_id')));
    echo $this->Form->input('id', array('type'=>'hidden'));
    echo $this->Form->input('name', array('error' => array('attributes' => array('wrap' => 'span', 'class' => 'label label-important')))); // still testing
    echo $this->Form->input('code');
    echo $this->Form->input('zone_id');
    ?>
    <div class="scroller">
    <?php
        // div class of scroller is jQuery to disable scroll in these 2 fields
        echo $this->Form->input('lat', array ('label' => 'Latitude'));
        echo $this->Form->input('lon', array ('label' => 'Longitude'));
    ?>
    </div>
    <?php
    echo $this->Form->input('declination', array('type'=>'hidden'));
    echo $this->Form->input('elevation');
    echo $this->Form->input('elevation_source',array('style' => 'width:100%'));
    
    echo $this->Form->input('install_team_id', array('type'=>'select','options' => $installteams,'label' => 'Install Team', 'empty' => true));
    echo $this->Form->input('install_date', 
        array(
           'class'=>'datepicker', 
           'type'=>'text'
        )
    );
    
    echo $this->Form->input('Site.site_state_id', array('type'=>'select','options' => $sitestates));
    echo $this->Form->input('Site.organization_id', array('type'=>'select','options' => $organizations ));
    echo $this->Form->input('Site.tower_type_id', array('type'=>'select','options' => $towertypes ));
    echo $this->Form->input('Site.tower_member_id', array('type'=>'select','options' => $towermembers ));
    echo $this->Form->input('Site.tower_mount_id', array('type'=>'select','options' => $towermounts ));
    echo $this->Form->input('Site.equipment_space_id', array('type'=>'select','options' => $equipmentspace,'label' => 'Equipment Space'));
    echo $this->Form->input('Site.power_type_id', array('type'=>'select','options' => $powertypes));
    
    // if the site has no switch, give them the option to add one -- otherwise, give them a pick list
    if (empty($this->request->data['NetworkSwitch']['name'])) {
        echo $this->Form->input('NetworkSwitch.name',array('label' => 'Add New Switch', 'required' => false));
    } else {
        echo $this->Form->input('Site.network_switch_id', array('type'=>'select','options' => $networkswitches,'label' => 'Switch'));
    }
        
    // ditto for routers
    if (empty($this->request->data['NetworkRouter']['name'])) {
        echo $this->Form->input('NetworkRouter.name',array('label' => 'Add New Router', 'required' => false));
    } else {
        echo $this->Form->input('Site.network_router_id', array('type'=>'select','options' => $networkrouters,'label' => 'Router'));
    }

    //print_r($this->request->data['NetworkRadios']);
    $c = count($this->request->data['NetworkRadios']);
    //echo "c is".$c;
    if ($c > 0) {
        echo "<table class=\"table table-condensed table-striped\">";
        echo "<th>Radio</th>";
        echo "<th>Frequency</th>";
        echo "<th>SSID</th>";

        foreach ($this->request->data['NetworkRadios'] as $radio) {
            echo "<tr><td>";
            //$radio['name'] = "test";
            //debug( $radio['name'] );
            //debug( $radio['id'] );
            echo $this->PoundcakeHTML->linkIfAllowed($radio['name'], array(
                'controller' => 'networkRadios',
                'action' => 'view',
                $radio['id']));
            /*
            echo $this->Html->link($radio['name']), array(
                'controller' => 'networkRadios',
                'action' => 'view',
                $radio['id']);
            */
            echo "</td>";

            echo "<td>";
            if (!empty($radio['frequency'])) {
                 echo $radio['frequency'];
            } else {
                echo "-";
            }
            echo "</td>";

            echo "<td>";
            if (!empty($radio['ssid'])) {
                 echo $radio['ssid'];
            } else {
                echo "-";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
        </table>

    
    <?php
    echo $this->element('Common/radio_builder');

    echo $this->Form->input('Site.tower_guard',array('style' => 'width:100%','label'=>'Tower Guard Contact Info'));
    echo $this->Form->input('Site.structure_type',array('style' => 'width:100%'));
    echo $this->Form->input('Site.description',array('style' => 'width:100%'));
    echo $this->Form->input('Site.mounting',array('style' => 'width:100%'));
    echo $this->Form->input('Site.access',array('style' => 'width:100%'));
    echo $this->Form->input('Site.storage',array('style' => 'width:100%'));
    echo $this->Form->input('Site.accommodations',array('style' => 'width:100%'));
    echo $this->Form->input('Site.notes',array('style' => 'width:100%'));
    // this is the upload field (now deprecated)
    // echo $this->Upload->edit('Site', $this->Form->fields['Site.id']);
    // single file
    // echo $this->Form->input('Site.file', array('type' => 'file','label'=>'Experimental File Upload'));
    // HTML5 approach - see: http://bakery.cakephp.org/articles/veganista/2012/01/31/html_5_multiple_file_upload_with_cake
    echo $this->Form->input('file.', array('type' => 'file', 'multiple'));
    
    echo $this->Form->submit('Save', array('div' => false,'class'=>'btn btn-primary'));
    echo $this->Form->submit('Cancel', array('name' => 'cancel','div' => false,'class'=>'btn btn-cancel'));
    echo $this->Form->end(); 
    /*
    $options = array(
        'label' => 'Save 2',
        'div' => array(
        'class' => 'btn',
        )
    );
    echo $this->Form->end($options);
    */

    /*
    $this->Js->get('#SchoolCatchmentId');
    $this->Js->event('change',
        $this->Js->request(array(
        'controller' => 'areas',
                'action'=>'getByCatchment'),
                    array('async' => true,
                        'update' => '#SchoolAreaId',
                        'dataExpression' => true,
                        'data'=> $this->Js->serializeForm(
                                array(
                                    'isForm' => true,
                                    'inline'=> true
                                    )
                                ),
                        'method' => 'post'
                        )
                    )
    );
    
    $this->Js->get('#SchoolAreaId');
    $this->Js->event('change',
        $this->Js->request(array(
        'controller' => 'districts',
                'action'=>'getByArea'),
                    array('async' => true,
                        'update' => '#SchoolDistrictId',
                        'dataExpression' => true,
                        'data'=> $this->Js->serializeForm(
                                array(
                                    'isForm' => true,
                                    'inline'=> true
                                    )
                                ),
                        'method' => 'post'
                        )
                    )
    );
    echo $this->Js->writeBuffer(); // Write cached scripts
    */
    
    // and when the radio changes, update the list of antennas
    echo $this->element('Common/radio_change');
?>
