<?php
    echo $this->Html->script('poundcake/poundcake-accordian');
    echo $this->Html->script('bootstrap.min'); // for the tooltips
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php
            /*if ( $internal_space_count == 0 ) {
                // echo $this->PoundcakeHTML->linkIfAdmin('New Root IP Space', array('action' => 'root'));
                echo $this->PoundcakeHTML->linkIfAdmin('New IP Space', array('action' => 'root')); 
            }  else {
                echo $this->PoundcakeHTML->linkIfAdmin('New IP Space', array('action' => 'add'));
            } */ 
            echo $this->PoundcakeHTML->linkIfAdmin('New IP Space', array('action' => 'root')); 
            ?>
        </li>
<!--        <li><?php //echo $this->PoundcakeHTML->linkIfAdmin('New Public /32', array('action' => 'ip')); ?></li>-->
    </ul>
    </div>
    
    <H3>Legend</H3>
    <div class="well">
        <UL>
        <?php
            echo '<LI>'.PoundcakeHTMLHelper::ICON_NEW." New</LI>";
            echo '<LI>'.PoundcakeHTMLHelper::ICON_DELETE." Delete</LI>";
            echo '<LI>'.PoundcakeHTMLHelper::ICON_EDIT." Edit</LI>";     
            echo '<LI><i class="icon-align-justify"></i> Fill</LI>';
            echo '<LI><i class="icon-resize-full"></i> Expand/Collapse</LI>';
        ?>
        </UL>
    </div>
</div> <!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>IP Spaces</h2>
        
        <?php
            if ( sizeof($ip_spaces) == 0 ) {
                echo '<div class="alert">';
                // echo "<strong>Notice!</strong>  There is no Root IP Space or Public /32 is defined for ".$this->Session->read('project_name').".";
                echo "<strong>Notice!</strong>  There are no IP Spaces defined for ".$this->Session->read('project_name').".";
                echo '</div>';
            } else {
                // Because we're using a recursive/static function below
                // (recursiveIpSpaces) to draw our Tree structure, we
                // don't have access to $this -- which means we cannot use the
                // HTML helper to create our delete confirmation dialog box as we
                // are doing everywhere else in the application -- so here is our
                // workaround -- get the HTML that the HTML helper would otherwise
                // create for us, then do a string replace substituting
                // DELETE_ID with the id to delete
                $delete_confirm_html = $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                    array('controller'=>'ipSpaces', 'action'=>'delete', 'DELETE_ID'),
                    array(
                        'method' => 'post',
                        'class'=>'confirm link',
                        'data-original-title' => 'Delete IP Space and children',
                        'data-placement' => 'bottom',
                        'data-dialog_msg'=>'Confirm delete of IP Space'
                        ),
                    null,
                    false // don't show the text "Delete" -- icon only
                );
                
                // as above, but this warning is for the fill option
//                $fill_confirm_html = $this->PoundcakeHTML->postLinkIfAllowed('Fill',
//                    array('controller'=>'ipSpaces', 'action'=>'fill', 'FILL_ID'),
//                    array(
//                        'method' => 'post',
//                        'class'=>'confirm link',
//                        'data-original-title' => 'Fill this IP Space with /32s (maximum 254)',
//                        'data-placement' => 'bottom',
//                        'data-dialog_msg'=>'Fill IP Space with /32 host records'
//                        ),
//                    null,
//                    false // don't show the text "Fill" -- icon only
//                );
                $fill_confirm_html = $this->PoundcakeHTML->postLinkIfAllowed('Fill',
                    array('controller'=>'ipSpaces', 'action'=>'fill', 'FILL_ID'),
                    array(
                        'method' => 'post',
                        'class'=>'prompt link',
                        'data-original-title' => 'Fill IP Space with host records',
                        'data-placement' => 'bottom',
                        'data-dialog_msg'=>'How many host records (Max 254)'
                        ),
                    null,
                    false // don't show the text "Fill" -- icon only
                );
                
                
//                echo '<pre>';
//                var_dump( $fill_confirm_html );
//                echo '</pre>';
//                die;
                
                // Using CakePHP Tree Behavior
                // For this view, see http://bakery.cakephp.org/articles/blackbit/2012/12/20/display_tree_index_with_ol_and_li
                recursiveIpSpaces( $ip_spaces, $this->Session->read('role'), $delete_confirm_html, $fill_confirm_html );
            }
        ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->

<?php 
function recursiveIpSpaces( $array ,$role, $delete_confirm_html, $fill_confirm_html ) { 
    
    if (count($array)) { 
        echo '<ul id="ip_space">';
        
        foreach ($array as $vals) { 
            //var_dump($vals);
            echo "<li id=\"".$vals['IpSpace']['id']."\">";
           
            echo $vals['IpSpace']['name'];
            echo ' '.$vals['IpSpace']['ip_address'];
            
            if ( $vals['IpSpace']['cidr'] == 32 ) {
                echo '/'.$vals['IpSpace']['parent_cidr'];
            } else {
                echo '/'.$vals['IpSpace']['cidr'];
            }
            
            // show the gateway address, if set
            if ( $vals['IpSpace']['gw_address'] > 0 ) {
                echo ' (Gateway: '.$vals['IpSpace']['gw_address'].') ';
            }
            
            // must be an admin to see add/edit/delete/fill icons
            if ( $role === 'admin' ) {
                echo '&nbsp;&nbsp;&nbsp;';
                echo '<a href="/ipSpaces/edit/';
                echo $vals['IpSpace']['id'];
                echo '" class="link" data-original-title="Edit IP Space" data-placement="bottom">';
                echo '<i class="icon-edit"></i></a>';
                
                /* Commenting this out -- see PC-567
                echo '<a href="/ipSpaces/find/';
                echo $vals['IpSpace']['id'];
                echo '" class="link" data-original-title="Search for matching device" data-placement="bottom">';
                echo '<i class="icon-search"></i></a>';
                */
                // don't show the plus sign if it's a /32
                // or the fill link
                if ( $vals['IpSpace']['cidr'] < 32 ) {
                    echo '<a href="/ipSpaces/add/';
                    echo $vals['IpSpace']['id'];
                    echo '" class="link" data-original-title="Add network or host" data-placement="bottom">';
                    echo '<i class="icon-plus"></i></a>';
                
                    $fill_confirm_html_tmp0 = preg_replace( '/(FILL_ID)/', $vals['IpSpace']['id'], $fill_confirm_html );
                    // we also have to replace the default arrow icon (from the HTML Helper) with a custom icon
                    $fill_confirm_html_tmp = preg_replace( '/icon-circle-arrow-right/', 'icon-align-justify', $fill_confirm_html_tmp0 );
                    //$fill_confirm_html_tmp = preg_replace( '/href="#"/', 'href="#" onclick="$(this).closest(\'form\').submit()"', $fill_confirm_html_tmp0 );
                    // <a href="#" onclick="$(this).closest('form').submit()"
                    echo $fill_confirm_html_tmp; // Spit out the HTML we (manually) created abovee
                    $fill_confirm_html_tmp = null;                    
                }
                
                $delete_confirm_html_tmp = preg_replace( '/(DELETE_ID)/', $vals['IpSpace']['id'], $delete_confirm_html );
                echo $delete_confirm_html_tmp; // Spit out the HTML we (manually) created above
                $delete_confirm_html_tmp = null;            
                
                // show the expand or collapose icon if it's not a /32 and has children underneath
                if ( $vals['IpSpace']['cidr'] < 32 ) {                    
                    echo '<a href="#" class="toggle link" ';
                    echo 'data-original-title="Expand or collapse" data-placement="bottom">';
                    echo '<i class="icon-resize-full"></i></a>';
                }
            }
            
            if (count($vals['children'])) { 
                //echo '<div class=\"toggle-pane\">';
                recursiveIpSpaces( $vals['children'], $role, $delete_confirm_html, $fill_confirm_html );
                //echo '</div>';
            } 
            echo "</li>\n";
        } 
        echo "</ul>\n"; 
    } 
} ?>