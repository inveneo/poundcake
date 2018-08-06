<?php
/**
 * Controller for admin functions.
 *
 * This is a very basic controller to show an admin page.
 * 
 * These tasks would typically be performed by a user with administrative level
 * permissions within Poundcake.
 *
 * Developed against CakePHP 2.2.3 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         AdminController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

class AdminController extends AppController {
    
    /*
     * This controller has no model
     */
    var $uses = array();
    
    /*
     * Main setup/configuration page
     */
    function setup() {
        // show setup page
    }
    
    /*
     * Uses Auth to check the ACL to see if the user is allowed to perform any
     * actions in this controller
     */
    public function isAuthorized($user) {
        return parent::isAuthorized($user);
    }
    
    function stats() {
        $this->loadModel('Project');
        $project_count = $this->Project->find('count');
        
        $this->loadModel('Site');
        $site_count = $this->Site->find('count');
        
        /*
        select sites.name, network_radios.name
        from sites, network_radios
        where sites.id=network_radios.site_id
        and sites.code='DLPCH';


        select sites.name, count(network_radios.id)
        from sites, network_radios
        where sites.id=network_radios.site_id
        -- and sites.code='DLPCH'
        group by sites.id
        order by sites.name;
        
        http://stackoverflow.com/questions/2231495/mysql-avgcount-orders-by-day-of-week-query 
        
        select avg(radio_count) from (
                select sites.id as site_id, sites.name as site_name, count(network_radios.id) as radio_count
                from sites, network_radios
                where sites.id=network_radios.site_id
                group by sites.id
                order by sites.name
                )
        temp 
        */
        $qry = 'select avg(radio_count) as avg_radio_count,
                max(radio_count) as max_radio_count           
                from (
                    select sites.id as site_id, sites.name as site_name, count(network_radios.id) as radio_count
                    from sites, network_radios
                    where sites.id=network_radios.site_id
                    group by sites.id
                    order by sites.name
                )
                temp';
        $radio_counts = $this->Site->query( $qry );
        $avg_radio_count = $radio_counts[0][0]['avg_radio_count'];
        $max_radio_count = $radio_counts[0][0]['max_radio_count'];
        
        $qry = 'select count(mp_radios) as mp_radio_count from (
                select sum(dest_radio_id) as mp_radios
                from radios_radios
                group by dest_radio_id
                HAVING COUNT( DISTINCT src_radio_id ) > 1
        ) temp;';
        $mp_radio_count = $this->Site->query( $qry );
        $mp_radio_count = $mp_radio_count[0][0]['mp_radio_count'];
        
        $this->loadModel('NetworkRadio');
        $radio_count = $this->NetworkRadio->find('count');
        
        $this->loadModel('NetworkRouter');
        $router_count = $this->NetworkRouter->find('count');
        
        $this->loadModel('NetworkSwitch');
        $switch_count = $this->NetworkSwitch->find('count');
        
        $this->loadModel('RadioType');
        $radio_types_tmp = $this->RadioType->find('list');
//        echo '<pre>';print_r($radio_types); echo '</pre>';
        $radio_types = array();
        foreach( $radio_types_tmp as $key => $val ) {
            $r = $this->NetworkRadio->find('count', array('conditions' => array('NetworkRadio.radio_type_id' => $key )));
            $radio_types[ $val ] = $r;
        }
        
        $this->loadModel('AntennaType');
        $antenna_types_tmp = $this->AntennaType->find('list');
//        echo '<pre>';print_r($radio_types); echo '</pre>';
        $antenna_types = array();
        foreach( $antenna_types_tmp as $key => $val ) {
            $r = $this->NetworkRadio->find('count', array('conditions' => array('NetworkRadio.antenna_type_id' => $key )));
            $antenna_types[ $val ] = $r;
        }
        
        $this->loadModel('PowerType');
        $power_types_tmp = $this->PowerType->find('list');
//        echo '<pre>';print_r($radio_types); echo '</pre>';
        $power_types = array();
        foreach( $power_types_tmp as $key => $val ) {
            $r = $this->Site->find('count', array('conditions' => array('Site.power_type_id' => $key )));
            $power_types[ $val ] = $r;
        }
        
        $this->loadModel('User');
        $user_count = $this->User->find('count');
        $this->User->recursive = -1;
        $last_logged_in_user = $this->User->find('first', array('order' => array('User.last_login DESC')));
        $last_logged_in_user = $last_logged_in_user['User']['username'];
        
        $this->loadModel('ChangeLog');
        $release_count = $this->ChangeLog->find('count');
        $last_update = $this->ChangeLog->find('first', array('order' => array('ChangeLog.release_date DESC')));
        $last_update = $last_update['ChangeLog']['release_date'];
        
        $alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $distribution = array();
        foreach ($alphabet as $letter ) {
            $qry = "select count(*) as count from sites where lower(name) like '".$letter."%'";
            $results = $this->ChangeLog->query( $qry );
//            echo '<pre>';
//            print_r($results);
//            echo '</pre>';
            $distribution[ $letter ] = $results[0][0]['count'];
        }
//        echo '<pre>';
//        print_r($distribution);
//        echo '</pre>';
//        die;
        
        $this->set(compact('project_count','site_count','avg_radio_count','max_radio_count','mp_radio_count','radio_count','router_count','switch_count','user_count','last_logged_in_user','last_update'));
        $this->set(compact('distribution','radio_types','antenna_types','power_types','release_count'));
    }
}
?>
