<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

class AppController extends Controller {

    const MAX_CIDRS = 32;
    const NETWORKRADIO = 0; // used by ConfigurationTemplates
    const NETWORKROUTER = 1; // used by ConfigurationTemplates
    const NETWORKSWITCH = 2; // used by ConfigurationTemplates
    
    public $actsAs = array('Containable'); // Containable rules -- took forever to get used to it
    
    /*
     * Compinents - used for the login/ACL
     */
    public $components = array(
        'Session',
        'Cookie',
        // see the Utility plugin, AutoLogin feature, AutoLogin must come before Auth!
        'Utility.AutoLogin' => array(
            'cookieName' => 'PoundcakeUser',
            'expires' => '+2 weeks',
            'redirect' => false
        ),
        'Auth' => array(
            //'loginRedirect' => array('controller' => 'schools', 'action' => 'overview'),\
            // if the user is not logged in, 
            // if the logged in user accesses a page they are not supposed to (by 
            // typing in the URL manually), send them here
            'loginRedirect' => array('controller' => 'sites', 'action' => 'overview'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'authorize' => array('Controller')
        ),
        'RequestHandler'        
    );

    /*
     * Helpers we use:
     * - PoundcakeHTML makes de-links hyperlinks for view-only users
     * - Less.Less @see http://mindthecode.com/using-less-in-cakephp/
     */
    var $helpers = array(
        'PoundcakeHTML',
        'Less.Less'
    );
    
    /*
     * Save an array of all the organizations the user may be assigned to
     */
    public function getOrganizationsForCurrentProject($obj) {
        // $this->set('organizations',$this->Contact->Organization->find('list'));
        // the above does not give us what we want -- it gives us a list of all
        // organiations (for use in the select on the Contact add/edit pages)
        // However, we only want to show orgs for the currently selected project
        // e.g. contact <-> organization <-> organizations_projects <-> project
        // I could not sort out how to do this with a Cake join (see below)
        // so I'm doing it manually by running my own SQL and creating the array manually

        /* begin does not work:
        $orgs = $this->Contact->Organization->find('list');
        echo '<pre>';
        echo "For Project ID: ".$this->Session->read('project_id');
        echo "<br>";
        print_r($orgs);
        
        foreach ($orgs as $key => $value) {
            $this->loadModel('Organization', $key);
            $org = $this->Organization->read(null, $key);
            echo ">";
            print_r($org);
        }
        echo '</pre>';
        echo "------------";
        
        $this->Contact->Organization->bindModel(array('hasOne' => array('OrganizationsProjects')));
        $organizations = $this->Contact->Organization->find('all',array(
            'fields' => array('DISTINCT Organization.name'),
            //'fields' => array('Organization.id', 'Organization.name'),
            'conditions'=> array(
                    //'Contact.organization_id = OrganizationsProject.organization_id',
                    'OrganizationsProject.project_id' => $this->Session->read('project_id'),
                    //'OrganizationsProject.organization_id' => 14
                ),
            'recursive' => 0,
            'joins' => array( 
                array('table' => 'organizations_projects', 
                'alias' => 'OrganizationsProject', 
                'type' => 'INNER',  
                'conditions'=> array(
                    //'Contact.organization_id = OrganizationsProject.organization_id',
                    'OrganizationsProject.project_id' => $this->Session->read('project_id'),
                    //'OrganizationsProject.organization_id' => 14
                )
        ))));
        end does not work
        */
        
        $sql = "select distinct organization_id, name from organizations_projects, organizations where project_id=".$this->Session->read('project_id');
        $sql .= " and organizations.id=organizations_projects.organization_id";
        $results = $this->$obj->query($sql);        
        $organizations = array( 0 => 'Unknown' );
        foreach ($results as $key => $value) {
            $organizations[$value['organizations_projects']['organization_id']] = $value['organizations']['name'];
        }
        $this->set(compact('organizations'));
    }
    
    /*
     * Return all the sites for the current project
     */
    function getAllSitesForProject( $exclude_id = '*' ) {
        // set sites to the list of sites the currently selected/active project
        // also returns the ID for the first site in that list
        // optionally allow the caller to filter out a specific site from that list
        // by passing in a site_id (as exclude_id)
        $conditions = array (
            'conditions' => array(
                'Site.project_id' => $this->Session->read('project_id'),
                'Site.id <>' => $exclude_id
                
        ));
        $this->loadModel('Site');
        // list or findByProjectId?  Hmm...
        //$this->set('sites', $this->Site->find('list', $conditions));
        $sites = $this->Site->find('list', $conditions);
        $this->set( 'sites', $sites );
//        echo $exclude_id.'<br>';
//        echo '<pre>';print_r($sites); echo '</pre>';die;
        return key($sites); // return the ID of the first site        
    }
    
    /*
    public function afterLayout() {
        //$this->Session->delete('Message.flash');
        //$this->Session->setFlash('Your stuff has been saved.');
    }
    */
    
    /*
     * Standard call back function -- used for the login/ACL.
     */
    public function beforeFilter() {
        // allows access to nothing if not logged in
        // by only allowing them access to the logout page
        $this->Auth->allow('logout');
    }
    
    /*
     * Sets two variables (snmp_override, snmp_community) to true/false if
     * the NetworkRadios/NetworkRouters/NetworkSwitch has custom SNMP
     * values defined.
     * 
     * @see NetworkRadios, NetworkRouters, NetworkSwitches
     */
    protected function checkSnmp() {
        $model = $this->modelClass;
        
        $snmp_override = false;
        $snmp_community = false;
        if ( $this->$model->field('snmp_override') > 0 ) {
            $snmp_override = true;
            // if they are not a view-only user then they can see the community
            // string
            if ( !$this->isViewOnly() ) {
                $snmp_community = true;
            }
        }
        $this->set(compact('snmp_override','snmp_community'));
    }
    
    /*
     * Check the ACL to see if this user can perform actions on the view within
     * the controller
     */
    public function isViewOnly( ) {
        $ret = false;
        if ( $this->Session->read('role') === 'view' )
            $ret = true;
        return $ret;
    }
    
    /*
     * Check the ACL to see if this user can perform actions on the view within
     * the controller
     */
    public function isAuthorized( $user ) {
        // there are 3 role alias' in the system:  admin, edit, view
        // users with the rolealias of admin can access every page
        // some controllers override this to allow the rolealias' of
        // edit or view to access the page    
        if ( $this->Session->read('role') === 'admin' ) {
            return true;
        }
        
        // default deny
        return false;
    }
    
    
    /*
     * Save an array of projects the SiteState may be assigned to
     */
    protected function getProjects() {
        // get the current model context -- this function is called from
        // at least the SiteStates and TowerTypes controllers
        $model = $this->modelClass;
        
        // note this does not filter the list of projects available to the
        // current user -- this returns all projects
        $this->set('projects',$this->$model->Project->find('list',
            array(
                'order' => array(
                    'Project.name'
            )))
        );
    }
    
    protected function getConfigurationTemplates() {
//         $model = $this->modelClass;
        // eventually we'll want to filter by radio/router/switch
        $this->loadModel('ConfigurationTemplate');
        $configuration_templates = $this->ConfigurationTemplate->getConfigurationTemplatesByProjectId( $this->Session->read('project_id') );
        $this->set(compact('configuration_templates'));
        
    }
    
    /*
     * Call back for AutoLogin, part of the Utility plugin
     * @see http://milesj.me/code/cakephp/utility
     * Also see setup in bootstrap.php
     */
    public function _autoLogin( $user ) {
        // var_dump( $user );        
        // re-load key session variables here
        // load the project_id -- this is actually saved to the user table, so
        // we can retrieve it pretty easily
        $this->Session->write('project_id', $user['project_id'] );

        // load the User model
        $this->loadModel('User');
        $this->User->recursive = 2;
        $this->User->id = $user['id'];
        $user = $this->User->read( null, $user['id'] );
//        var_dump( $user );
//        $this->User->Behaviors->load('Containable');
//        $projects = $this->User->find('all',
//                array(
//                    'contain' => 'ProjectMembership',
//                    'conditions' => array('ProjectMembership.user_id =' => $user['id'] )
//                    )
//        );
//        var_dump( $projects ); die;
        
        $user['User']['auto_login'] = true;
//        echo '<pre>';
//        var_dump( $user );
//        var_dump( $user2 );
        // so that we an find any project memberships
        $projects = $user['ProjectMembership'];
        foreach ( $projects as $project ) {
//            echo "Comparing:".$project['project_id']." to ".$user['User']['project_id']." <BR>";
            if ( $project['project_id'] == $user['User']['project_id'] ) {
                $role_id = $project['role_id'];
                break;
            }
        }
//        echo '</pre>';
        // if they are an amin (admin field is 1 on the User model), then their
        // role alias is admin regardless
        if ( $user['User']['admin'] > 0 ) {
            $rolealias = 'admin';
        } else {
            // otherwise we get the role alias off the project's membership
            $this->loadModel('Role');
            $this->Role->id = $role_id;
            $role = $this->Role->read();
            $rolealias = $role['Role']['rolealias'];
        }
        
//        var_dump($rolealias);
        $this->Session->write( 'role', $rolealias );
//        var_dump($role);
//        var_dump($rolealias);
//        die;
        
        // load the project so we can get the project name
        $this->loadModel('Project');
        $this->Project->id = $user['User']['project_id'];
        $project = $this->Project->read( null, $user['User']['project_id'] );
//        var_dump($project);die;
        $this->Session->write('project_name', $project['Project']['name'].' - Updated by AutoLogin at '.  date("H:i:s") );
//        var_dump( $user );
    }
    
    public function _autoLoginError( $x ) {
        echo "_autoLoginError:<BR>";
        var_dump($x);
        die;
    }
    
    /*
   * 
   */
    protected function getCidrs( $c, $host = true ) {
        $cidrs = array();
        // we don't want to have /32 in this list, so we'll do up until 1 less,
        // then add "host" at the end
        for ($n = $c + 1; $n <=  self::MAX_CIDRS - 2; $n++ ) {
            $cidrs[ $n ] = '/'.$n;
        }
        // include the option for a /32 host record
        if ( $host ) {
            $cidrs[ 32 ] = 'Host';
        }
        $this->set(compact('cidrs'));
    }
    
    /*
     * Standard callback function before a view is rendered
     * Used to grab any system messages an admin may have set, and get the 
     * release number
     */
    function beforeRender() {
        $this->response->disableCache();
        $banner_ctr = $this->Session->read('banner_ctr');
        $this->set('user', $this->Auth->user());
        // only check for this stuff every 10 page loads
        
        // echo "banner_ctr: ".$banner_ctr."<BR>";  
        if ( (is_null($banner_ctr)) || ($banner_ctr  >= 10 )) {
            $banner_ctr = 0;
            // Banner stuff
            // I just need an object on which I can call query, ChangeLog seems OK?
            // maybe this doesn't need to be done here
            $query = "select message from notifications";
            $this->loadModel('ChangeLog');
            $result = $this->ChangeLog->query($query);
            if( count( $result ) > 0 )
                $this->Session->write('banner',$result[0]['notifications']['message']);
            else
                 $this->Session->write('banner','');
            
            // Version stuff
            $query = "select max(version) as version from change_logs";
            $result = $this->ChangeLog->query($query);
            $this->Session->write('version',$result[0][0]['version']);
        }
        // up our counter
        $banner_ctr++;
        $this->Session->write('banner_ctr', $banner_ctr);        
    }
    
    /*
     * Build a URL with all the search elements in it - the resulting URL will
     * be: example.com/cake/posts/index/Search.keywords:mykeyword/Search.tag_id:3        
     */
    function search() {
        // the page we will redirect to at the end
        $url['action'] = 'index';
        foreach ($this->data as $k=>$v) {
            foreach ($v as $kk=>$vv){
                $vv .= '*';
                // remove forward slashes -- site codes may have them!
                $vv = str_replace('/','*',$vv);
                $url[$k.'.'.$kk]=$vv; 
            } 
        }
        // redirect the user to the url
        $this->redirect($url, null, true);
    }
    
    /*
     * Return IP addresses (note: plural) for a given item name, used for the
     * HRBN addrpool integration.
     * 
     * @see MySQL stored procedures.
     */
    public function getAllAddrpoolIPAddresses($name) {
        $project_id = $this->Session->read('project_id');
        // only return addrpool data for HRBN projects
        if ( ($project_id == 1 ) || ($project_id == 9 ) ) {
            return ClassRegistry::init('AddrpoolIPAddress')->getAllAddrpoolIPAddresses($name);
        }
        return false;
    }
    
    /*
     * Return IP addresses (note: singular) for a given item name, used for the
     * HRBN addrpool integration.
     * 
     * @see MySQL stored procedures.
     */
    public function getAddrpoolIPAddress($name) {
        $project_id = $this->Session->read('project_id');
        // only return addrpool data for HRBN projects
        if ( ($project_id == 1 ) || ($project_id == 9 ) ) {
            return ClassRegistry::init('AddrpoolIPAddress')->getAddrpoolIPAddress($name);
        }
        return false;
    }
    
    /*
     * Return gateway addresses for a given item name, used for the
     * HRBN addrpool integration.
     * 
     * @see MySQL stored procedures.
     */
    public function getAddrpoolGatewayAddress($name) {
        $project_id = $this->Session->read('project_id');
        // only return addrpool data for HRBN projects
        if ( ($project_id == 1 ) || ($project_id == 9 )) {
            return ClassRegistry::init('AddrpoolIPAddress')->getAddrpoolGatewayAddress($name);
        }
        return false;
    }
    
    /*
     * Utility function to make an XML string into a human-readable, an 
     * indented work of art
     * 
     * @param string $xml
     * @param boolean $html_output
     * @see: http://gdatatips.blogspot.com/2008/11/xml-php-pretty-printer.html
     */
    public function xmlpp($xml, $html_output=false) {  
        $xml_obj = new SimpleXMLElement($xml);  
        $level = 4;  
        $indent = 0; // current indentation level  
        $pretty = array();  

        // get an array containing each XML element  
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));  

        // shift off opening XML tag if present  
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {  
          $pretty[] = array_shift($xml);  
        }  

        foreach ($xml as $el) {  
          if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {  
              // opening tag, increase indent  
              $pretty[] = str_repeat(' ', $indent) . $el;  
              $indent += $level;  
          } else {  
            if (preg_match('/^<\/.+>$/', $el)) {              
              $indent -= $level;  // closing tag, decrease indent  
            }  
            if ($indent < 0) {  
              $indent += $level;  
            }  
            $pretty[] = str_repeat(' ', $indent) . $el;  
          }  
        }     
        $xml = implode("\n", $pretty);     
        return ($html_output) ? htmlentities($xml) : $xml;  
    }
    
    /*
     * Return an HttpSocket for use in a ReST call
     */
    protected function getMonitoringSystemSocket( $username, $password ) {
        App::uses('HttpSocket', 'Network/Http');
        $ret = null;
        if ( !(is_null( $username )) && !(is_null( $password )) ) {
            // for future reference, JSON HttpSocket example:  http://ask.cakephp.org/questions/view/how_to_post_json_with_httpsocket
            $HttpSocket = new HttpSocket();
            $HttpSocket->configAuth(
                    'Basic',
                    $username,
                    $password
                );
            $ret = $HttpSocket;
        }
        return $ret;
    }
    
    /*
     * Generic cancel function on form submit
     */
    protected function handleCancel( $redirect = 'index' ) {
        if (isset($this->params['data']['cancel'])) {
            $this->redirect(array('action' => $redirect ));
        }
    }
    
    /*
     * This function generates XML from a data array
     * 
     * @see http://www.viper007bond.com/2011/06/29/easily-create-xml-in-php-using-a-data-array/
     */
    protected function generateXMLElement( $dom, $data ) {
        if ( empty( $data['name'] ) )
            return false;

        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );

        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }

        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;

            $child = $this->generateXMLElement( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }

        return $element;
    } 
    
    /*
     * This just verifies that the URL is valid -- this is currently over-used
     */
    protected function validateURL( $url ) {
        $ret = true;
        //$url = 'http://la1b.inveneo.org:8980/opennms/rest';
        
        // this first check is if the URL is syntactically valid
        if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE) {
            $ret = false;
        } else {    
            // now let's check by DNS
            $hostname = parse_url($url); // get just the hostname, lose the http:// etc.
//            echo $hostname."<BR>";
            $ip = gethostbynamel( $hostname['host'] );
//            echo '<pre>';
//            var_dump( $ip );
//            echo long2ip( $ip );
//            echo ip2long( $ip );
//            echo '</pre>';        
            if( ip2long( $ip[0] ) == null ) {
                $ret = false;
            }
        }
        
//        if ( $ret ) {
//            echo "URL is OK";
//        } else {
//            echo "Bad URL: $url<BR>";
//        }

        return $ret;
    }
}
?>