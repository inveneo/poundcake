<?php

    // for serving views as files
    // http://www.dereuromark.de/2011/11/21/serving-views-as-files-in-cake2/
    //Router::parseExtensions('pdf');
    //Router::parseExtensions('kml');
    Router::parseExtensions();
    
    
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
    
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
        Router::connect('/', array('controller' => 'users', 'action' => 'login'));
        
        // prepend the word "admin" on the URL for these specific controllers
        // e.g. so that /projects/edit/3 appears as /admin/projects/edit/3
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'projects'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'roles'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'users'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'antennaTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'radioModes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'radioTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'equipmentSpaces'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'organizations'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'powerTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'towerMembers'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'towerMounts'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'towerTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'buildItems'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'contactTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'installTeams'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'notifications'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'monitoringSystemTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'routerTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'switchTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'siteStates'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'zones'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'snmpTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'networkServices'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'frequencies'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'radioBands'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'siteStateIcons'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'networkInterfaceTypes'));
        Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'configurationTemplates'));
        //Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'report_manager'));
        //Router::connect('/admin/:controller/:action/*', array(), array('controller' => 'ipSpaces'));
        
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
        
        
         

/**
 * Load all plugin routes.  See the CakePlugin documentation on 
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
