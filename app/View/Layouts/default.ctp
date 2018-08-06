<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


$cakeDescription = __d('poundcake', 'Tower DB');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
            <?php echo $cakeDescription ?>:
            <?php echo $title_for_layout; ?>
	</title>
    
    <!-- Favicon from http://www.favicon.cc/?action=icon&file_id=198724
    <link href="data:image/x-icon;base64,AAABAAEAEBAAAAAAAABoBQAAFgAAACgAAAAQAAAAIAAAAAEACAAAAAAAAAEAAAAAAAAAAAAAAAEAAAAAAAAAAAAAdXV1AEVFRQC/v78ABQUFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQABAAEAAQAAAAAAAAAAAAEBAAEAAQEAAAAAAAAAAAABAAEAAQABAAAAAAAAAAAAAQEAAQABAQAAAAAAAAAAAAABAQABAQAAAAAAAAAAAAAAAQABAAEAAAAAAAAAAAAAAAEBAAEBAAAAAAAAAAAAAAABAAEAAQAAAAAAAAAAAAAAAAEAAQAAAAAAAAAAAAAAAAABAQEAAAAAAAAAAAAAAEAAAQABAAAAQAAAAAAAAMEAQAABAAEAQQDAAAAAAAEAAAAAgACAAQABAAAAAAABAAAAAAAAAAEAAQAAAAAAAQAAAACAAIABAAEAAAAAAADBAEAAAAABAEEAwAAAPVfAADynwAA9V8AAPKfAAD5PwAA+r8AAPk/AAD6vwAA/X8AAPx/AADtbwAAwocAANRXAADUVwAA1FcAAMOHAAA=" rel="icon" type="image/x-icon" />
    -->
	<?php
            // jQuery                
            echo $this->Html->script('jquery-1.9.1.min');
            echo $this->Html->script('jquery-ui/jquery-ui-1.10.3.custom.min');
            echo $this->Html->script('bootstrap.min'); // Bootstrap's jQuery file
            echo $this->Html->script('poundcake/poundcake'); // Our custom jQuery file available to all pages
            echo $this->Html->script('bootbox.min'); // Bootbox jQuery -- for the dialogs
            echo $this->Html->script('jquery.form'); // Testing this out, see http://www.malsup.com/jquery/form/#download
            
            echo $this->Html->meta('icon');
            echo $this->fetch('meta');
            echo $this->fetch('css');
            echo $this->fetch('script');            
            $this->Js->JqueryEngine->jQueryObject = '$j';
echo $this->Html->scriptBlock(
    'var $j = jQuery.noConflict();',
    array('inline' => false)
);
// Tell jQuery to go into noconflict mode
            // CSS
            echo $this->Html->css('bootstrap'); // Bootstrap's CSS file
            //echo $this->Html->css('cosmo/bootstrap-bootstrap.min'); // testing Cosmo from Bootswatch
            //echo $this->Html->css('cerulean/cerulean-bootstrap.min'); // testing Cerulean from Bootswatch
            //echo $this->Html->css('spacelab/spacelab-bootstrap.min'); // testing Spacelab from Bootswatch
            echo $this->Html->css('poundcake'); // our own custom CSS file -- some of this overrides Bootstrap's CSS
            echo $this->Html->css('jasny-bootstrap');  // Jasny is used for the File upload widget
            echo $this->Html->css('jquery-ui-1.10.3.custom.min'); // used at least by the Datepicker, not sure what else uses jQuery UI
            
            // LESS 
            // @see http://mindthecode.com/using-less-in-cakephp/
            // @see https://github.com/leafo/lessphp
            echo $this->Less->css('variables');
            echo $this->Less->css('bootswatch');
            //echo $this->Less->css('cosmo/cosmo-variables'); // testing Cosmo from Bootswatch
            //echo $this->Less->css('cosmo/cosmo-bootswatch'); // testing Cosmo from Bootswatch
            //echo $this->Less->css('cerulean/cerulean-variables'); // testing Cerulean from Bootswatch
            //echo $this->Less->css('cerulean/cerulean-bootswatch'); // testing Cerulean from Bootswatch
            //echo $this->Less->css('spacelab/spacelab-variables'); // testing Spacelab from Bootswatch
            //echo $this->Less->css('spacelab/spacelab-bootswatch'); // testing Spacelab from Bootswatch
        ?>
    
        <style type="text/css"> 
        /* bootstrap Google map fix
        See:  http://gis.yohman.com/up206b/tutorials/5-2/
        */
        #map_canvas img {
            max-width: none;
        }
        body {
          padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }

        @media (min-width: 768px) { 
          .sb-fixed{
                  position: fixed;
                  } 
               }
          .box{
              min-height: 20px;
              padding: 19px;
              margin-bottom: 20px;
              background-color: whiteSmoke;
              border: 1px solid #EEE;
              border: 1px solid rgba(0, 0, 0, 0.05);
              -webkit-border-radius: 4px;
              -moz-border-radius: 4px;
              border-radius: 4px;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
              -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
          }
        </style>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
</head>
<body>
    <?php    
    // this is to make the generic cancel button (jQuery on dialogs) work
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        echo '<input type="hidden" id="backTo" value="';
        echo $_SERVER['HTTP_REFERER'];
        echo '"></input>';
    }
    
    if( !defined( 'MAINTENANCE' )) {
        $maint_file = ROOT . DS . APP_DIR . DS . 'maintenance.txt';
        $maint = file_get_contents( $maint_file );
        //var_dump( $maint );
        define( 'MAINTENANCE', $maint );
        // set this to 1 to enable the maintenance page
        //define( 'MAINTENANCE', 0 ); 
        // 24.20.20.146 -- Clark's office at TenPod
        // when behind the load balancer, you have to use HTTP_X_FORWARDED_FOR
        // otherwise REMOTE_ADDR is the address of the load balancer
        // if( MAINTENANCE > 0 && $_SERVER['REMOTE_ADDR'] != '24.20.20.146' ) {
        if( MAINTENANCE > 0 && $_SERVER['HTTP_X_FORWARDED_FOR'] != '24.20.20.146' ) {
            include_once( 'maintenance.ctp');
            die(); 
        }
    }
?>
    
<div class="navbar navbar-fixed-top"> <!-- was: navbar-fixed-top -->    
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="/sites/overview">Tower DB <?php echo $this->Session->read('version');?>&nbsp;&nbsp;</a>
                <div class="main-navigation">
                    <ul class="nav">
                    <?php
                        if ( isset($user) ) {
                            include 'navigation.ctp';
                        }
                    ?>
                    </ul>
                </div>
            <?php
                // if the user is logged in, show a button on the right side with
                // links to change their password, etc.
                if ( isset($user) ) {
                    include 'user.ctp';
                }
            ?>
            </div>
        </div>
    </div> <!--/.navbar -->
    
    <!-- show banner, if any -- this would be set in AppController::beforeRender -->
    <div class="container">
    <div class="row">
    <?php
        // if the user has already dismissed the dialog box, then don't show it again
        $dialog_closed = $this->Session->read('dialog_closed');
        if ( ! $dialog_closed ) {
            $banner = $this->Session->read('banner');
            if ( strlen($banner) > 0 ) {
                echo '<div class="alert" align="center">';
                echo '<button type="button" id="1" class="close" data-dismiss="alert">&times;</button>';
                echo $banner;
                echo '</div>';
            }
        }
        $flashClass = "alert-spacer";
        $flashMessage = "";
        if( $this->Session->check('Message.flash') ) {
            $flashMessage = $this->Session->flash();
            $flashClass = "alert-success";
            if (strlen(strstr($flashMessage,'Error')) > 0) {
                $flashClass = "alert-error";
            } else if (strlen(strstr($flashMessage,'Info'))>0) {
                $flashClass = "alert-info";
            }
        }
        
        if ( strlen($flashMessage) > 0 ) {
            echo '<div align="center" id="flash" class="alert '.$flashClass.'">';
            echo $flashMessage;
            echo '</div>';
        }

    ?>
        </div>
        <?php echo $content_for_layout; ?>
        
        <div id="footer" align="center"><BR><BR>
                        
        <div class="row" align="center">
            <?php include 'breadcrumb.ctp'; ?>
        </div> <!--/.bread crumbs -->
    
        <?php
            $project_name = $this->Session->read('project_name');
            
            // only show this stuff if the user is logged in
            if ( !(is_null($project_name))) {
                echo "Current Project: ";
                // echo $project_name != "" ? $project_name : "Unknown";
                echo $this->Html->link( $project_name , array('action' => 'summary', 'controller' => '/projects', $this->Session->read('project_id') ));
                // echo $this->PoundcakeHTML->linkIfAllowed( $project_name, array( 'controller' => 'projects', $this->Session->read('project_id')), null );
                echo " | ";
                echo $this->Html->link(('Switch Project'), array('action' => 'project', 'controller' => 'users', CakeSession::read("Auth.User.id")));
            }
            
            echo '<small>';
            $role = $this->Session->read('role');
            if ( $role != "" ) {
                echo '<br>Current Role: '.$role;
            }
            
            $copyright = 'Copyright © 2012';
            $current_year = date("Y");
            if ($current_year > 2012)
                $copyright = '<BR>Copyright © 2012-'.$current_year;
            echo $copyright . ' All Rights Reserved.<BR>';
            
            // again, only show if logged in
            if ( !(is_null($project_name))) {
                echo $this->Html->link(('Version History'), array('action' => 'index', 'controller' => 'changeLog'));
            }
            echo '</small>';
                       
        ?>
            
    <?php
//        echo $this->element('sql_dump');
//        echo Configure::version(); // show Cake version
    ?>
    </div> <!-- /footer -->
    <?php
//        echo "<BR>Session info:<BR><UL>";
//        foreach($_SESSION as $key => $value) {
//            echo  '<li>Current session variable ' . $key . ' is: ' . $value . '</li>';
//        }
//        echo "</UL>";
    ?>
    </div> <!-- /.container -->
</body>
</html>