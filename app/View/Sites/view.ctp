    <?php
    // if we're under SSL we have to give the Google stuff under SSL, too, or
    // else the browser is likely to complain or just not render SSL/non-SSL
    // content together
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
        echo $this->Html->script('https://maps.google.com/maps/api/js?sensor=true',false);
    } else {
        echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=true',false);
    }
    echo $this->Html->script('gears_init');
    echo $this->Html->script('jquery-ui-map/jquery.ui.map');
    // echo $this->Html->script('poundcake/poundcake');
    echo $this->Html->script('poundcake/poundcake-map');
    echo $this->Html->script('jquery.prettyPhoto');
    echo $this->Html->script('poundcake/poundcake-prettyPhoto');
    echo $this->Html->script('holder');
    echo $this->Html->css('prettyPhoto');
    
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('Edit Site', array('action'=>'edit', $site['Site']['id']),1);?></li>
        <li><?php echo $this->PoundcakeHTML->link('List Sites', array('action'=>'index')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Equipment List', array('action'=>'view', 'ext'=>'pdf', $site['Site']['id']));?></li>
        <li><?php
            echo $this->PoundcakeHTML->link('Work Order', array('action'=>'workorder','ext'=>'xml', $site['Site']['id']));?>
        </li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="row">
    <div class="span9">
        
        <h2><?php echo $site['Site']['code']." ".$site['Site']['name'].' ('.$site['SiteState']['name'].')'?> </h2>
         
        <div class="tabbable"> <!-- Only required for left/right tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><i class="icon-list-alt"></i>Detail</a></li>
                <li><a href="#tab2" data-toggle="tab"><i class="icon-user"></i>Contacts</a></li>
                <li><a href="#tab3" data-toggle="tab"><i class="icon-cog"></i>Network</a></li>
                <li><a href="#tab4" data-toggle="tab"><i class="icon-camera"></i>Files</a></li>
<!--                <li><a href="#tab5" data-toggle="tab"><i class="icon-globe"></i>Profile</a></li>
                <li><a href="#tab6" data-toggle="tab"><i class="icon-signal"></i>Coverage</a></li>-->
            </ul>
            
            <div class="tab-content">
                <!-- Tab 1 -->
                <div class="tab-pane active" id="tab1">
                    <div class="row">
                        <div class="span5">
                            <dl class="dl-horizontal">
                                <dt>Coordinates</dt>
                                <dd><?php echo sprintf("%01.5f",$site['Site']['lat']) . ' ' . sprintf("%01.5f",$site['Site']['lon']); ?></dd>

                                <dt>Declination</dt>
                                <dd><?php echo sprintf("%01.5f",$site['Site']['declination']); ?></dd>

                                <dt>Elevation</dt>
                                <dd><?php echo $site['Site']['elevation'] ? : 'Unknown'; ?></dd>
                                
                                <dt>Elevation Source</dt>
                                <dd><?php echo $site['Site']['elevation_source'] ? : 'Unknown'; ?></dd>
                                
                                <dt>Zone</dt>
                                <dd><?php echo $site['Zone']['name'] ? : 'None'; ?></dd>

                                <dt>Organization</dt>
                                <dd><?php
                                    if ( isset($site['Organization']['name'] ))
                                        echo $this->Html->link( $site['Organization']['name'], array( 'controller' => 'organizations', 'action' => 'view', $site['Organization']['id']));
                                    else
                                        echo 'None';
                                    ?>
                                </dd>

                                <dt>Tower Type</dt>
                                <dd><?php echo $site['TowerType']['name'] ? : 'None'; ?></dd>

                                <dt>Tower Member</dt>
                                <dd><?php echo $site['TowerMember']['name'] ? : 'None'; ?></dd>

                                <dt>Tower Mount</dt>
                                <dd><?php echo $site['TowerMount']['name'] ? : 'None'; ?></dd>

                                <dt>Equipment Space</dt>
                                <dd><?php echo $site['EquipmentSpace']['name'] ? : 'None'; ?></dd>

                                <dt>Power Type</dt>
                                <dd><?php echo $site['PowerType']['name'] ? : 'None'; ?></dd>
                                
                                <dt>Power Consumption</dt>
                                <dd><?php echo $watts; ?>W (<?php echo ($watts * 24)?> Watt hours)</dd>
                            </dl>
                        </div>
                        <div class="span4">
                            <div>
                            <?php
                                echo $this->Form->create('Site', array('action' => 'view'));
                                echo $this->Form->input('sites',
                                        array(
                                            'type'=>'select',
                                            //'length' => 15,
                                            'options' => $sites,
                                            //'label' => 'Select Remote Site',
                                            'label' => '',
                                            'empty' => 'Quick Site Calcs'
                                     )
                                );
                                echo $this->Form->end();
                            ?>                                
                            <div id="RemoteSite"></div>
                            </div>
                        </div>
                        </dl>
                    </div>
                    
                    <div class="map-frame">
                        <div id="map_canvas" style="width:700px;height:400px"></div>
                        <div id="radios" class="item gradient rounded shadow" style="margin:5px;padding:5px 5px 5px 10px;"></div>

                        <?php
                            echo $this->Form->create( 'google_map' );
                            echo $this->Form->input( 'default_lat', array('type'=>'hidden','value' => $site['Site']['lat']));
                            echo $this->Form->input( 'default_lon', array('type'=>'hidden','value' => $site['Site']['lon']));
                            echo $this->Form->input( 'fit_bounds', array( 'type' => 'hidden', 'value' => 0 ));

                            $sitestates = array( 'name' => $site['Site']['code'] );
                            $n = 0;
                            foreach ( $sitestates as $key => $val ) {
                                echo $this->Form->input( 'sitestate_'.$n, array('type'=>'hidden','value'=>$val ));
                                $n++;
                            }
                            echo $this->Form->end();
                            $u = 0;
                            echo "<div style='visibility:hidden; position:absolute;'>";
                            echo '<ul>';
                            $icon = 'data:'.$site['SiteState']['SiteStateIcon']['img_type'].';base64,'.base64_encode( $site['SiteState']['SiteStateIcon']['img_data'] ); 

                            $item = array( 
                                'id' => 'm_'.$u,
                                'icon' => $icon,
                                // see this as to why this needs to be an array
                                // http://stackoverflow.com/questions/9881949/filterable-jquery-ui-map-google-map
                                'tags' => array( $site['Site']['code'] ),
                                'latlng' => array(
                                    'lat' => $site['Site']['lat'],
                                    'lng' => $site['Site']['lon'],
                                )
                            );
                            echo "<li data-gmapping='".json_encode($item)."'>";
                            echo '<p class="info-box"><a href="/sites/view/'.$site['Site']['id'].'">'.$site['Site']['site_vf'].'</a></p><br>';
                            echo "</li></ul></div>";
                         ?>
                     </div> <!-- /.map-frame -->
                     <BR>
                     <dl class="dl-horizontal">
                        <dt>Tower Guard</dt>
                        <dd><?php echo $site['Site']['tower_guard'] ? : 'None'; ?></dd>

                        <dt>Structure Type</dt>
                        <dd><?php echo nl2br( $site['Site']['structure_type'] ) ? : 'None'; ?></dd>

                        <dt>Description</dt>
                        <dd><?php echo nl2br( $site['Site']['description'] ) ? : 'None'; ?></dd>

                        <dt>Mounting</dt>
                        <dd><?php echo nl2br( $site['Site']['mounting'] ) ? : 'None'; ?></P>

                        <dt>Access</dt>
                        <dd><?php echo nl2br( $site['Site']['access'] ) ? : 'None'; ?></dd>

                        <dt>Storage</dt>
                        <dd><?php echo nl2br( $site['Site']['storage'] ) ? : 'None'; ?></dd>

                        <dt>Accommodations</dt>
                        <dd><?php echo nl2br( $site['Site']['accommodations'] ) ? : 'None'; ?></dd>

                        <dt>Notes</dt>
                        <dd><?php echo nl2br( $site['Site']['notes'] ) ? : 'None'; ?></dd>

                        <dt>Install Team</dt>
                        <dd><?php echo nl2br( $site['InstallTeam']['name'] ) ? : 'None'; ?></dd>

                        <dt>Install Date</dt>
                        <dd><?php
                             // format the date as per the project's defined format
                             $date = $site['Site']['install_date'];
                             $format = $site['Project']['datetime_format'];
                             echo date($format,strtotime($date));    
                        ?>
                        </dd>
                    
                        <dt>Hardware Value</dt>
                        <dd>USD $<?php echo $value; ?></dd>
                                
                        <dt>Site Last Modified</dt>
                        <dd><?php echo date($format, strtotime( $site['Site']['modified'] )); ?></dd>
                    </dl>
                </div>
                
                <!-- Tab 2 -->
                <div class="tab-pane" id="tab2">
                    <dl class="dl-horizontal">
                        <?php
                            if ( count($contacts) == 0 ) {
                                echo "None";
                            } else {
                                //echo "<d>";
                                foreach ($contacts as $contact) {
                                    //echo "<LI>";
                                    echo "<dt>Priority ".$contact['Contact']['priority']."</dt><dd>";
                                    echo $this->Html->link(($contact['Contact']['name_vf']), array(
                                        'controller' => 'contacts',
                                        'action' => 'view',
                                        $contact['Contact']['id']));
                                    // show the phone number if it's defined
                                    // else show the email if it's defined
                                    $contact_info = null;
                                    if ( $contact['Contact']['phone'] != null ) {
                                        $contact_info = $contact['Contact']['phone'];
                                    } else if ( $contact['Contact']['email'] != null ) {
                                        $contact_info = $contact['Contact']['email'];
                                    }

                                    if ( !is_null( $contact_info )) {
                                        echo ", ".$contact_info;
                                    }
                                    echo "</dd>";
                                }
                            }
                        ?>
                    </dl>
                </div>
                
                <!-- Tab 3 -->
                <div class="tab-pane" id="tab3">
                    <dl class="dl">
                    <dt>Switch</dt>
                    <dd>
                        <?php
                            if ( isset($site['NetworkSwitch']['name']) ) {
                                echo $this->element('Common/site_status_icon', array('status' => $site['NetworkSwitch']['is_down']));
                                echo '&nbsp;';
                                echo $this->Html->link(
                                        $site['NetworkSwitch']['name'],
                                        array(
                                            'controller' => 'networkSwitches',
                                            'action' => 'view',
                                            $site['NetworkSwitch']['id']));
                            } else {
                                echo "None";
                            }
                        ?>
                    </dd><br>
                    <dt>Router</dt>
                    <dd>
                        <?php
                            if ( isset($site['NetworkRouter']['name']) ) {
                                echo $this->element('Common/site_status_icon', array('status' => $site['NetworkRouter']['is_down']));
                                echo '&nbsp;';
                                echo $this->Html->link(
                                        $site['NetworkRouter']['name'],
                                        array(
                                            'controller' => 'networkRouters',
                                            'action' => 'view',
                                            $site['NetworkRouter']['id']));
                            } else {
                                echo "None";
                            }
                        ?>
                    </dd><br>

                    <dt>Radios</dt>
                    
                        <?php
                            $c = count($site['NetworkRadios']);
                            if ($c == 0) {
                                echo "None";
                            } else {

                                // sort the radios by switch and router port (they can only be connected to one or the other)
                                usort( $site['NetworkRadios'], function ($a, $b) { return $a["switch_port"] - $b["router_port"]; });
                                usort( $site['NetworkRadios'], function ($a, $b) { return $a["router_port"] - $b["router_port"]; });
                                
                                echo "<table class=\"table table-condensed table-striped\">";
                                echo "<tr><th>Name</th>";
                                echo "<th>Router Port</th>";
                                echo "<th>Switch Port</th>";
                                echo "<th>Frequency</th>";
                                echo "<th>SSID</th></tr>";
                                foreach ($site['NetworkRadios'] as $radio) {

                                    echo "<tr><td>";
                                    echo $this->element('Common/site_status_icon', array('status' => $radio['is_down']));
                                    echo '&nbsp;';

                                    echo $this->Html->link($radio['name'], array(
                                        'controller' => 'networkRadios',
                                        'action' => 'view',
                                        $radio['id']));
                                    echo "</td>";

                                    echo '<td>';
                                    if (!empty($radio['router_port'])) {
                                         echo $radio['router_port'];
                                    } else {
                                        echo "-";
                                    }
                                    echo "</td>";
                                    
                                    echo '<td>';
                                    if (!empty($radio['switch_port'])) {
                                         echo $radio['switch_port'];
                                    } else {
                                        echo "-";
                                    }
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
                       
                    </dl>

                    <?php echo $this->element('Common/addrpool_data'); ?>
                </div>

                <!-- Tab 4 -->
                <div class="tab-pane" id="tab4">
                <?php
                    // get any attached files
                    $results = $this->Upload->listing('Site', $site['Site']['id']);
                    // echo print_r($results['files']);
                    // maybe test if the file exists here?
                    if ($results['files'] != null) {
                        // list any uploaded files
                        $directory = $results['directory'];
                        $baseUrl = $results['baseUrl'];
                        $files = $results['files'];
                        $images = array();

                        // we want to display images with thumbnails, so first we have to
                        // look through the files array and move any files that are of
                        // type image into an images array, then display them each
                        // separately
                        $f = count($files);
                        for ($i = 0; $i < $f; $i++) {
                            // look for any images in the files array
                            //debug(exif_imagetype($files[$i]));

                            // an enumerated list of OK image types
                            // see: http://php.net/manual/en/function.exif-imagetype.php
                            $allowed_image_types = array( IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP );
                            // the type of our current file
                            $image_type = exif_imagetype($files[$i]);

                            if ( in_array( $image_type, $allowed_image_types )) {
                                //echo $i." " . basename($files[$i]) ." is OK<br>";
                                array_push($images,$files[$i]); // put it into the images array
                                unset($files[$i]); // remove it from the files array
                            }
                        }

                        // show the non-image files
                        echo '<P><UL>';
                        foreach ( $files as $file ) {
                            $f = basename($file);                    
                            $url = $baseUrl . "/$f";
                            echo '<li>';
                            echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                                // passing the filename seems to lose the extension in the function?  abc just dummy text
                                array('controller'=>'sites','action'=>'foobar', $site['Site']['id'],$f,'abc'),
                                array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$f),
                                null,
                                false
                            );
                            echo '<a href="'.$url.'">';
                            echo $f;
                            echo '</a></li>';
                        }
                       
                        echo '</UL></P>';
                        
                        // now show the images
                        echo '<P><div><ul class="thumbnails">';
                        foreach ( $images as $image ) {
                            $f = basename($image);
                            $ext = pathinfo($image, PATHINFO_EXTENSION);
                            $url = $baseUrl . "/$f";
                            // we're sort of doing two things here 1) creating a link
                            // that has the rel attribute of prettyPhoto so the image
                            // opens up in the prettyPhoto jQuery viewer, and 2) creating
                            // a standard bootstrap thumbnail image, also wrapped by
                            // that same link -- so a user just has to click on the thumbnail
                            // to open the viewer
                            echo '<li class="span3">';
                            echo '<a href="'.$url.'" rel="prettyPhoto[pp_gal]" title="'.$f.'">';
                            echo $this->Html->image( $url, array( 'class'=>'thumbnail' ));
                            // show the filename underneath the thumbnail
                            ?>
                            </a><center><li>
                            <?
                            echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                                // passing the filename seems to lose the extension in the function?  abc just dummy text
                                array('controller'=>'sites','action'=>'foobar', $site['Site']['id'],$f,'abc'),
                                array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$f),
                                null,
                                false
                            );
                            
                            ?>
                            <?php echo $f; ?>
                            </li></center>
                            <?php

                        }
                        echo '</ul></div></p>';


                    } else {
                        echo "<P><UL><LI>None</LI></UL></P>";
                    }
                    ?>
                </div>
                <div class="tab-pane" id="tab5">
                    <?php
                        //echo $this->Html->image('test/profile-1.png', array('alt' => 'Profile #1'));
                        //echo $this->Html->image('test/profile-2.png', array('alt' => 'Profile #2'));
                        //echo $this->Html->image('test/profile-3.png', array('alt' => 'Profile #3'));
                        //echo $this->Html->image('test/profile-data.png', array('alt' => 'Profile Data'));
                    ?>
                </div>
                <div class="tab-pane" id="tab6">
                    <?php
                        //echo $this->Html->image('test/viewshed-1.png', array('alt' => 'Viewshed #1'));
                    ?>
                </div>
            </div>
        </div>           
    </div> <!-- /.row -->
</div> <!-- /.span9 -->

<?php
    //$this->Js->get('#SiteSites')->event('change', $this->Js->alert('Compute Distance To Selected Site'));    
    $this->Js->get('#SiteSites')->event('change',
        $this->Js->request(array(
        'controller' => 'Sites',
            'action'=>'getRemoteSite',$id), // pass the id of the current site in as a parameter
                array('async' => true,
                    'update' => '#RemoteSite',
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
?> 