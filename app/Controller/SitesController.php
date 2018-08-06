<?php
/**
 * Controller for sites.
 *
 * This is a very basic controller to add/view/update/delete sites, a
 * core object within the Poundcake application.
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
 * @since         SitesController precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

class SitesController extends AppController {
    
    // enable/disable debug statements in my most problematic areas
    const CRON_DEBUG = 1;
    const KML_DEBUG = 0;
    
    /*
     * Helpers we use:
     * - AjaxMultiUpload is used for the file upload plugin
     * - PoundcakeHTML makes de-links hyperlinks for view-only users
     */
    public $helpers = array(
        'AjaxMultiUpload.Upload',
        'PoundcakeHTML'
    );
    
    /*
     * Compoenents to import
     */
    public $components = array('AjaxMultiUpload.Upload','RequestHandler'); //,'DebugKit.Toolbar'
    
    /*
     * Field names for the form itself, 'model' => 'Site'
     */
    public $presetVars = array(
        array(
            'field' => 'name',
            'type' => 'value'
        ),
    );
    
    /*
     * Callback function, can't remember exact usage
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // allow anyone access to the cron page -- this should be restricted
        // by Apache
        $this->Auth->allow( 'cron' );
        $this->set('name', $this->Site->name);
    }
    
    /*
     * Main listing for all Sites - is a bit more complicated as it also handels
     * search, loading past searches and wildcarding searches
     */
    function index($id = null) {
        
        $this->getSiteStates();

        // passing checkbox search parameters in-between pages with the
        // Pagination controller is not working!  So we're using the session
        // variable here.
        $loadPastSearch = false;
        
        // this is true if the user is navigating around the paginated results
        if ( $this->params->action == 'index' ) {            
            $loadPastSearch = true;
        }

        // this is true when the user hits the index page for the first time
        if ( ( $this->params->action == 'index' ) && ($this->Session->read('conditions') == null) ) {            
            $loadPastSearch = false;
        }
        
        // this is a new search
        if ( isset($this->params->data['Site']) ) {
             $loadPastSearch = false;
        }

        // if the form has no values, and the users is coming from the index page
        // (i.e. going in-between paginated results) grab conditions from the Session
        // if they are coming from somewhere else, default to a new search
        if ( $loadPastSearch ) {
            $conditions = $this->Session->read('conditions');
            // this just makes sure that any past searches re-populate in the
            // fields for site code, site name, and the checkboxes
            // on the search form
            
            // turn % back into *
            $code = '';
            if (isset($conditions['AND']['0']['AND'])) {
                $code = str_replace('%','*',$conditions['AND']['0']['AND']['Site.code LIKE']);
                //echo "Site code from past search: ". $code."<br>";
            }
            $this->request->data['Site']['code'] =  $code;
                    
            // turn % back into *
            $name = '';
            if (isset($conditions['AND']['0']['AND'])) {
                $name = str_replace('%','*',$conditions['AND']['0']['AND']['Site.name LIKE']);
                //echo "Site name from past search: ". $name."<br>";
            }
            $this->request->data['Site']['name'] =  $name;
            
            // this array just tells the view what boxes to keep checked
            // when the page refreshes
            $checked = array();
            if (isset($conditions['AND']['1']['OR'])) {
                foreach ($conditions['AND']['1']['OR'] as $key => $val) {
                    if (isset($val['Site.site_state_id']))
                        array_push($checked,$val['Site.site_state_id']);
                }
            }
            $this->request->data['Site']['site_state_id'] = $checked;            
            
        } else {
            // get search stuff from the form that was sent in
            $conditions = "";                
            $code_arg = "";
            $name_arg = "";
            $site_state_id_arg = "";
            // this indexes should always exist, but they may be empty!

            if (isset($this->data['Site']['code'])) {
                $code_arg = str_replace('*','%',$this->data['Site']['code']);
            }
            if (isset($this->data['Site']['name'])) {
                $name_arg = str_replace('*','%',$this->data['Site']['name']);
            }

            $site_states = array();
            if (isset($this->data['Site']['site_state_id'])) {
                $states = $this->data['Site']['site_state_id'];            
                if ( isset($states[0] )) {
                    foreach ( $states as $state ) {
                        array_push( $site_states, array('Site.site_state_id' => $state) );
                    }
                }           
            }

            // greedy search
            $code_arg .= '%';
            $name_arg .= '%';
            
            // we basically have to have something in the site_state_id field, so if the
            // user didn't check anything, stick a wildcard in there
            if ( count( $site_states ) == 0 ) {
                $site_states = array('Site.site_state_id LIKE' => '%');
            }

            $conditions = array(
                'AND' => array(
                    array('AND' => array(
                        'Site.code LIKE' => $code_arg,
                        'Site.name LIKE' => $name_arg,
                        //'Site.site_state_id ' => $site_state_id_arg,
                        'Site.project_id' => $this->Session->read('project_id') // only show sites for the current project
                    )),
                    array('OR' => $site_states)
            ));

            $this->Session->write( 'conditions', $conditions );
        }
        
        // paginate the results
        $this->paginate = array(
            // limit is the number per page 
            'limit' => 20,
            'conditions' => $conditions,
            'order' => array(
                'Site.code' => 'asc',
                'Site.name' => 'asc',
            ),
        );
        
        $data = $this->paginate('Site');     
        $this->set('sites',$data);        
        $this->getInstallTeams();
    }
 
    /*
     * Save an array of site states for use in the legend
     */
    public function buildLegend() {
        // ISNULL here puts any SiteStates that don't have a defined sequence
        // at the end of the list
        //$allSiteStates = $this->Site->SiteState->findAllByProjectId( 1 ,array('order' => array('ISNULL(sequence), sequence ASC')));
        $allSiteStates = $this->Site->SiteState->findAllByProjectId( $this->Session->read('project_id') );
        $this->set(compact( 'allSiteStates' ));
    }
    
    /*
     * Save variables with the project's default lat/lon
     */    
    private function setDefaultLatLon( $id ) {
        if ( $id > 0 ) {
            $project = $this->Site->Project->findById( $id );
            $default_lat = $project['Project']['default_lat'];
            $default_lon = $project['Project']['default_lon'];
        }
        $this->set(compact( 'default_lat', 'default_lon', 'default_zoom' ));
    }
    
    /*
     * Draw the overview map but show links in-between sites, color code the
     * placemarkers at the site to the site's status -- gray (unknown), green
     * (all radios OK), yellow (some radios OK), or red (all radios down).
     */
    public function topology() {
        $conditions = array(
            'AND' => array(
                'Site.project_id' => $this->Session->read('project_id') // only show sites for the current project
            )
        );
        
        $this->Site->recursive = 1; // we need to access the Site's NetworkRadio array
        $sites = $this->Site->find('all', array('conditions' => $conditions));
        
        $s = array();
        $u = 0;
        $n = 0;
        
//        echo '<pre>';
        foreach ($sites as $site ) {
            //print_r( $site );
            $s[$u]['id'] = $site['Site']['id'];
            $s[$u]['name'] = $site['Site']['name'];
            $s[$u]['code'] = $site['Site']['code'];
            $s[$u]['site_vf'] = $site['Site']['site_vf'];
            $s[$u]['src_lat'] = $site['Site']['lat'];
            $s[$u]['src_lon'] = $site['Site']['lon'];
            $s[$u]['is_down'] = $site['Site']['is_down'];
            
            $n = $u;
            foreach ($site['NetworkRadios'] as $radio ) {
                // for each radio that's attached to a site, we need to find out
                // what that radio is linked to -- but we do this manually, since
                // one radio can be linked to many radios
                
//                echo( 'Radio ID '. $radio['id'].'<br>');                               
                $query = 'select dest_radio_id from radios_radios where src_radio_id=('.$radio['id'].')';
                $results = $this->Site->query( $query );
//                if ( count($results) > 1 ) {
//                    var_dump( count($results));
//                    echo '<br>';
//                    var_dump( $query);
//                }
                // typically this is an array of 1 item, but there could be
                // many -- which would be the case of a P2MP radio
                foreach( $results as $dest_radio ) {
                    // we now have to load the other radio to get the name of the site
                    // that it is attached to
                    $this->loadModel('NetworkRadio'); //, $dest_radio['radios_radios']['dest_radio_id'] );
                    $this->NetworkRadio->id = $dest_radio['radios_radios']['dest_radio_id'];
                    $dest_site = $this->NetworkRadio->read();
                    
                    // we want to avoid adding links in both directions
                    // this is a little sloppy, maybe someone smarter than me can
                    // design a better way to do this
                    // echo "Ckecking:  ".$site['Site']['name'].": ".$site['Site']['lat'].", ".$site['Site']['lon']." --> ".$dest_site['Site']['lat'].", ".$dest_site['Site']['lon']."<BR>";
                    //if ( 1 ) {
                    // if ( ! $this->oppositeLinkExists( $s, $site['Site']['lat'], $site['Site']['lon'] ) ) { 
                    if ( ! $this->oppositeLinkExists( $s, $site['Site']['lat'], $site['Site']['lon'], $dest_site['Site']['lat'], $dest_site['Site']['lon'] ) ) {     
                        $s[$n]['id'] = $site['Site']['id'];
                        $s[$n]['name'] = $site['Site']['name'];
                        $s[$n]['code'] = $site['Site']['code'];
                        $s[$n]['site_vf'] = $site['Site']['site_vf'];
                        $s[$n]['src_lat'] = $site['Site']['lat'];
                        $s[$n]['src_lon'] = $site['Site']['lon'];
                        $s[$n]['is_down'] = $site['Site']['is_down'];
                        $s[$n]['dest_lat'] = $dest_site['Site']['lat'];
                        $s[$n]['dest_lon'] = $dest_site['Site']['lon'];
                        $n++;
                    }
//                    print_r($dest_site);
//                    echo ' > '.$site['Site']['name'].' links to '.$dest_site['Site']['name'].'<br>';
                    $dest_site = null;
                    //$n++;
                }
                $results = null;
            }
            $u = $n;
            $u++;
            
        }
//        debug($s);
//        die;
        $this->set('sites', $s);
        $this->setDefaultLatLon( $this->Session->read('project_id') );
    }
    
    /*
     * Check if the link exists in our array
     */
    private function oppositeLinkExists( $s, $src_lat, $src_lon, $dest_lat, $dest_lon ) {
        foreach ($s as $site ) {
            // var_dump( $site );
            // we need to check if the dest_lat key exists in the site,
            // otherwise we'll get a key does not exist error below
            if ( array_key_exists( 'dest_lat', $site ) ) {
                if ( ( $site['src_lat'] == $dest_lat ) && ( $site['src_lon'] == $dest_lon ) && ( $site['dest_lat'] == $src_lat ) && ( $site['dest_lon'] == $src_lon ) ) {
    //                    echo "<B>Found</B><BR>";
    //                    var_dump($site);
    //                    die;
                    return 1;
                }
            }
        }
        return 0;
    }
        
    public function overview() {
        
        $conditions = array(
            'AND' => array(
                'Site.project_id' => $this->Session->read('project_id') // only show sites for the current project
            )
        );
        
        $sites = $this->Site->find('all',
                array( 'conditions' => $conditions ),
                array(
                   'contain' => true
//                    'contain' => array (
//                        'SiteState' => array(
//                            'SiteStateIcon' => array('fields' => array('id', 'name'))
//                        )
//                    ),
                )
        );
        
        // this is a hack, but when the overivew page suddenly started throwing out of memory
        // errors I switched from recursion to Containable -- and I could not
        // get SiteStateIcons returned in my find query above, so the hack here is that we are
        // manually putting them back into the array; overview.ctp is basically the same
        // as it was before
        $n = 0;
        foreach ( $sites as $site ) {
            $ssi = $this->Site->SiteState->read(null, $site['Site']['site_state_id']);
            $sites[$n]['SiteState']['SiteStateIcon'] = $ssi['SiteStateIcon'];
            $n++;
        }
        
        $this->setDefaultLatLon( $this->Session->read('project_id') );
        $this->set(compact( 'sites' ));
        $this->getSiteStates();
        $this->buildLegend();
    }
    
    /*
     * Import a KML file of sites
     */
    public function import() {
        /*
         'name' => 'Udot school wifi mast (1).kml',
	'type' => 'application/vnd.google-earth.kml+xml',
	'tmp_name' => '/Applications/MAMP/tmp/php/phpgHN5ul',
	'error' => (int) 0,
	'size' => (int) 1516
)
         */
//        $this->request->data['Site']['File']['name'] = 'Udot school wifi mast (1).kml';
//        $this->request->data['Site']['File']['type'] = 'application/vnd.google-earth.kml+xml';
//        $this->request->data['Site']['File']['tmp_name'] = '/Applications/MAMP/tmp/php/phpgHN5ul';
//        $this->request->data['Site']['File']['error'] = 0;
//        $this->request->data['Site']['File']['size'] = 1516;
        
        if ($this->request->data != null ) {
            
            $overwrite = false;
            if ( isset($this->request->data['overwrite'] )) {
                if ( $this->request->data['overwrite'] == 'true') {
                    $overwrite = true;
                }
            }
            
            // debug($this->request->data['Site']['File']);
            if (is_uploaded_file( $this->request->data['Site']['File']['tmp_name'] )) {
                $fileData = fread(fopen($this->request->data['Site']['File']['tmp_name'], "r"), $this->request->data['Site']['File']['size']);

                $filename = $this->request->data['Site']['File']['tmp_name'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $filename);
                
                // .kmz files are MIME type application/zip
                if ( $mime == 'application/xml' ) {
                    $xml = simplexml_load_file( $filename );
                    if ( $xml != null ) {
                        // Sites need to be in a default state or else they won't
                        // appear on the overview map -- so let's get the first
                        // state manually here
                        
                        $sites = array();
                        $sid = $this->Site->SiteState->query('select id from site_states where sequence is not null and project_id='.$this->Session->read('project_id').' order by sequence limit 1');                        
                        // we cannot import sites unless there is at least one SiteState defined for the project
                        if ( sizeof($sid) == 0) {
                            $this->Session->setFlash('Error! Cannot import sites until at least one Site State is defined in the project.');
                            $this->redirect(array('action' => 'index'));                            
                        } else {
                            if ( $sid > 0 ) {
                                $found_sites = array();
                                $sites = $this->parseKML( $xml->children(), $sid[0]['site_states']['id'], $found_sites );
                            }
                            
                            if ( self::KML_DEBUG ) {
                                debug( $sites );
                                die;
                            }
                            
                            $this->set(compact('sites'));
                            if ( sizeof( $sites ) > 0 ) {
                                $this->render('confirm');
                            } else {
                                $this->Session->setFlash('Error! No placemarks found in KML.');
                                $this->redirect(array('action' => 'index'));
                            }
                        }
                    }
                }
            }
        }        
    }
    
    /*
     * Recursive KML parser
     */
    private function parseKML( $xml, $default_state, $found_sites ) {
        
        if ( self::KML_DEBUG ) {
            echo '<pre>found_sites:<BR>';
            print_r( $found_sites  );
            echo '</pre>';
        }
        
        foreach ( $xml->children() as $child ) {
            if ( $child instanceof SimpleXMLElement ) {
                
                if ( self::KML_DEBUG ) {
                    echo '<pre>Looking at:';
                    print_r($child->getName());
                    echo '</pre>';
                }
                
                if ( $child->getName() == 'Placemark' ) {
                    // get the name of the placemark - note we must cast to string here
                    $name = (string)$child->name;
                    
                    // get the coordinates
                    $coords = explode(",", $child->Point->coordinates);
                    $lon = $coords[0];
                    $lat = $coords[1];
                    
                    // save our junk back to the array
                    $data['Site']['name'] = $name;
                    $data['Site']['code'] = $this->getSiteCode( $name );
                    $data['Site']['lat'] = $lat;
                    $data['Site']['lon'] = $lon;
                    $data['Site']['site_state_id'] = $default_state;
                    $data['Site']['project_id'] = $this->Session->read('project_id');
                    array_push( $found_sites, $data );
                    
                    if ( self::KML_DEBUG ) {
                        echo '<pre>';
                        print_r( "*** Placemark: $name at $lat, $lon *** <BR>");
                        var_dump($child);
                        echo '</pre>';        
                    }                       
                }
                $found_sites = $this->parseKML( $child, $default_state, $found_sites );
            }
        }
        
        if ( self::KML_DEBUG ) {
            echo '<pre>Found Sites: ';
            print_r( count($found_sites) );
            echo '</pre>';
        }
        
        return $found_sites;
    }

    public function import_sites( ) {
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            $s = sizeof( $this->request->data );
            foreach( $this->request->data['Site'] as $data ) {
                $site = explode( "|", $data );                
                $site['Site']['name'] = $site[0];
                // $site['Site']['code'] = $site[1];
                $site['Site']['code'] = $this->getSiteCode( $site[1] );
                $site['Site']['lat'] = $site[2];
                $site['Site']['lon'] = $site[3];
                $site['Site']['site_state_id'] = $site[4];
                $site['Site']['project_id'] = $site[5];
                // $data['Site']['project_id'] = $this->Session->read('project_id');
                $this->Site->create();
                $newsite = $this->Site->save( $site, false ); // no field validation
                $id = $newsite['Site']['id']; // this is the ID of the most recently saved Site
            }
            $this->Session->setFlash('Success! KML import complete.');
            
            // if they imported just one site, take them to that page, otherwise
            // go to the index listing
            if ( ( $s == 1 ) && ( $id > 0 )) {
                $this->redirect(array('controller' => 'sites', 'action' => 'view', $id ));
            } else {
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->Session->setFlash('Error!  KML import failed.');
        $this->redirect(array('action' => 'index'));
    }
    
    /*
     * Export sites into a KML file
     * @see https://developers.google.com/kml/articles/phpmysqlkml
     */
    public function export( $id = null ) {
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        // Creates the root KML element and appends it to the root document.
        $node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
        $parNode = $dom->appendChild($node);
        // Creates a KML Document element and append it to the KML element.
        $dnode = $dom->createElement('Document');
        $docNode = $parNode->appendChild($dnode);
        // Creates the two Style elements, one for restaurant and one for bar, and append the elements to the Document element.
        $restStyleNode = $dom->createElement('Style');
        $restStyleNode->setAttribute('id', 'restaurantStyle');
        $restIconstyleNode = $dom->createElement('IconStyle');
        $restIconstyleNode->setAttribute('id', 'restaurantIcon');
        $restIconNode = $dom->createElement('Icon');
        $restHref = $dom->createElement('href', 'http://maps.google.com/mapfiles/kml/pal2/icon63.png');
        $restIconNode->appendChild($restHref);
        $restIconstyleNode->appendChild($restIconNode);
        $restStyleNode->appendChild($restIconstyleNode);
        $docNode->appendChild($restStyleNode);
        $barStyleNode = $dom->createElement('Style');
        $barStyleNode->setAttribute('id', 'barStyle');
        $barIconstyleNode = $dom->createElement('IconStyle');
        $barIconstyleNode->setAttribute('id', 'barIcon');
        $barIconNode = $dom->createElement('Icon');
        $barHref = $dom->createElement('href', 'http://maps.google.com/mapfiles/kml/pal2/icon27.png');
        $barIconNode->appendChild($barHref);
        $barIconstyleNode->appendChild($barIconNode);
        $barStyleNode->appendChild($barIconstyleNode);
        $docNode->appendChild($barStyleNode);

        $this->Site->recursive = -1; // we only need Site data, not related data
        if ( $id > 0 ) {
            $this->Site->id = $id;
            $this->set('id',$id);
            $site = $this->Site->read(null, $id);
            $sites = array( $site ); // ehough we're only getting one site
            $filename = $this->Site->data['Site']['name'];
            
        } else {
            // if the user ran a search then grab that search and generate the KML
            // based on that set of sites, else grab all sites for this project
            $conditions = $this->Session->read('conditions');
            
            if ( $conditions != null ) {
                $sites = $this->Site->find('all',array('conditions' => $conditions ));
            } else {
                $sites = $this->Site->findAllByProjectId( $this->Session->read('project_id') );
            }
            
            //$project_name = preg_replace('/\s+/', '', $this->Session->read('project_name'));
            $filename = preg_replace('/(\(|\))/', '', $this->Session->read('project_name'));
        }
        
        foreach ($sites as $site ) {
            $node = $dom->createElement('Placemark');
            $placeNode = $docNode->appendChild($node);
            // Creates an id attribute and assign it the value of id column.
            $placeNode->setAttribute('id', 'placemark' . $site['Site']['id']); // CakePHP primary key -- not sure if this should be code?
            // Create name, and description elements and assigns them the values of the name and address columns from the results.
            // cleanup the name -- some names (e.g. Goâve) result in KML
            // that Google Earth cannot accept
            $name = preg_replace('/[^(\x20-\x7F)]*/','', $site['Site']['name']);
            $nameNode = $dom->createElement('name',htmlentities( $name ));
            $placeNode->appendChild($nameNode);
            // Creates a Point element.
            $pointNode = $dom->createElement('Point');
            $placeNode->appendChild($pointNode);
            // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
            $coorStr = $site['Site']['lon'] . ','  . $site['Site']['lat'];
            $coorNode = $dom->createElement('coordinates', $coorStr);
            $pointNode->appendChild($coorNode);
        }

        $kmlOutput = $dom->saveXML();
        $this->set('data',$kmlOutput);
        
        
        $this->set('filename',$filename);
        
        $this->layout = 'blank';
        $this->render('export');
        $this->layout = 'default';        
    }
    
    /*
     * 
     */
    function getContacts($id = null) {
        // sometimes I think my head is going to explode - I had a hard time finding
        // contacts for this site's tower owner, this is the model setup:
        // 
        // Site belongsTo Organization
        // Organization hasMany Sites
        // Organization hasMany Contacts
        // Contact belongsTo Organization
        
        // get the ID of the current site's tower owner
        $id = $this->Site->data['Site']['organization_id'];
        //echo "ID is" . $id;
        $conditions = array (
            //'id' => $id // tower_owner.id = site.organization_id
            'organization_id' => $this->Site->data['Site']['organization_id']
        );
        
        $contacts = $this->Site->Organization->Contact->find(
                'all',
                array('conditions' => $conditions,
                //array('order' => $order)
                'order' => 'priority ASC')
        );
        
        $i=0;
        foreach ($contacts as $contact) {
            $contacts[$i++]['Contact']['phone'] = str_replace(',', '<br/>', $contact['Contact']['phone']);
            //$i++;
        }

        // clean up the listing, since some of these can be long
        $this->set(compact('contacts'));
    }
    
    /*
     * Get an array of build items -- items for the "board" build
     */
    function getBuildItems( $id ) {
        $this->loadModel('BuildItem');
        $this->BuildItem->bindModel(array('belongsTo' => array('BuildItemType' => 
                            array('foreignKey' => 'build_item_type_id'))));
        // I could not sort out how to do this find with conditions on a HABTM
        // relationship
        // see:  http://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#joining-tables
        // where it says:  Suppose a Book hasAndBelongsToMany Tag association.
        $options['joins'] = array(
            array( 
                'table' => 'build_items_projects', 
                'alias' => 'BuildItemsProjects', 
                'type' => 'inner',  
                'conditions'=> array(
                    'BuildItem.id = BuildItemsProjects.build_item_id'
                )
            ),
            array('table' => 'projects',
                'alias' => 'Project',
                'type' => 'inner',
                'conditions' => array(
                    'BuildItemsProjects.project_id = Project.id',
                    'Project.id = '.$this->Session->read('project_id')
                )
            )
        );
        $options['fields'] = array(
            'BuildItem.name',
            'BuildItem.quantity',
            'BuildItemType.name'
        );
        
        $builditems =  $this->BuildItem->find('all', $options);        
//        echo '<pre>1:';
//        print_r($builditems);
//        echo '</pre>';
//        die;
        $this->set('builditems', $builditems);
       
        // sum up all the radios, antennas for this site
        $query = 'call sp_count_radios('.$id.')';
        $this->set('radio_counts', $this->Site->query( $query ));
        $query = 'call sp_count_antennas('.$id.')';
        $this->set('antenna_counts', $this->Site->query( $query ));
      
        // this is probably not the best way to do this, but if an admin deletes
        // then re-creates a power source I can't assume the primary key for the
        // 24/48 volt PowerType
        $board = array('quantity' => '1','name' => $this->Site->data['PowerType']['volts']. ' Volt Board');

        $this->set('board', $board);
    }
    
    /*
     * 
     */
    function schedule($id) {
        $this->Site->InstallTeam->id=$id;
        $this->set('teamname',$this->Site->InstallTeam->field('name'));
        if ($id != null) {
            $query = 'call sp_schedule('.$id.')';
            $this->set('schedule',$this->Site->InstallTeam->query( $query ));
        }
    }
    
    /*
     * Manually check if the user is allowed to view this site by checking them
     * against the projects table
     */
    function isAllowed($project_id, $uid) {
        $this->loadModel('User');    
        $this->User->id = $this->Auth->user('id');
        $this->User->read();
        // if the user is an administrator, then they are allowed
        
        $ret = false;
        if ( $this->User->field('admin') ) {
            $ret = true;
        } else {
            //  There is probably a Cake-ier way to do this, sometimes you gotta
            //  go bare metal
            $query = 'select * from project_memberships where user_id='.$uid.' and project_id='.$project_id;
            $result = $this->Site->query($query);
            $ret = (sizeof($result) > 0 ? true : false);
        }
        
        return $ret;
    }
    
    /*
     * View a site
     */
    function view($id = null) {
        $this->Site->recursive = 2;
        $this->Site->id = $id;
        $this->set('id',$id);
        
        if (!$this->Site->exists()) {
            throw new NotFoundException('Invalid site');
        }
  
        $site = $this->Site->read(null, $id);
        
        // don't go any further if the user is not in the same project as this site
        if (!$this->isAllowed($this->Site->data['Site']['project_id'], $this->Auth->user('id')) ) {
            $this->redirect(array('action' => 'index'));
        }
        
        $this->getContacts($id);
        $this->getAllSitesForProject( $id ); // exclude this site from the list of sites
        
        // sum up the watts and value for the devices on this site
        $network_router_id = $this->Site->field('network_router_id');
        $network_switch_id = $this->Site->field('network_switch_id');
        
        $watts = 0;
        $value = 0;
        if ( $network_router_id > 0 ) {
            $router = $this->Site->NetworkRouter->findById( $network_router_id );
            $watts += $router['RouterType']['watts'];
            $value += $router['RouterType']['value'];
        }
        if ( $network_switch_id > 0 ) {
            $switch = $this->Site->NetworkSwitch->findById( $network_switch_id );
            $watts += $switch['SwitchType']['watts'];
            $value += $switch['SwitchType']['value'];
        }
        
        $radios = $this->Site->NetworkRadios->findAllBySiteId($id);
        $n = 0;
        foreach ($radios as $radio) {
            // get the ID of any remote radio
            // this site could be a multipoint end, so there could be more than
            // one attached radio
            $query = 'call sp_get_remote_links('.$radio['NetworkRadios']['id'].')';
            $links = $this->Site->query( $query );
            foreach ($links as $link) {
                $d = $this->getLinkLatLon($link['radios_radios']['dest_radio_id']);
                $radio['NetworkRadios']['link_lat'] = $d[0];
                $radio['NetworkRadios']['link_lon'] = $d[1];
                $radio['NetworkRadios']['link_icon'] = $d[2];
                $radio['NetworkRadios']['window_text'] = $d[3];
            }
            $radios[$n] = $radio;
            $n++;
            
            $watts += $radio['RadioType']['watts'];
            $value += $radio['RadioType']['value'];
        }
        
        $this->getBuildItems( $id );
        $ip_addresses = $this->getAllAddrpoolIPAddresses( $this->Site->data['Site']['code'] );
        $this->set(compact( 'site', 'ip_addresses', 'radios', 'watts', 'value' ));
    }
    
    /*
     * 
     */
    function getRemoteSite($id) {
        // get the lat/lon of the current site
        $site = $this->Site->read(null,$id);
        $lat = $site['Site']['lat'];
        $lon = $site['Site']['lon'];
        // get the lat/lon of the remote site
        $r_site_id = $this->request->data['Site']['sites'];
        $r_site = $this->Site->read(null,$r_site_id);
        $r_lat = $r_site['Site']['lat'];
        $r_lon = $r_site['Site']['lon'];
        $r_dist = $this->Site->getDistance($lat, $lon, $r_lat,$r_lon);
        
        $true_azimuth = $this->Site->getBearing($lat, $lon, $r_lat, $r_lon);
        $declination = $this->Site->getDeclination($lat,$lon);        
        $mag_azimuth = 0;
        if ($true_azimuth > 0) {
            $mag_azimuth = $true_azimuth - $declination;
        }
        
        $this->set('remote',array($r_dist,$true_azimuth,$mag_azimuth));
        $this->layout = 'ajax';
    }
    
    /*
     * Retuns an array containing the lat/lon of the Site for the remote radio
     */
    function getLinkLatLon($r_radio_id) {
        $this->loadModel('NetworkRadio',$r_radio_id);
        $r_radio = $this->NetworkRadio->read(null,$r_radio_id);
        $r_site = $this->Site->read(null,$r_radio['NetworkRadio']['site_id']);
        $this->loadModel('SiteStateIcon');
        //$r_icon = $this->SiteStateIcon->read(null, $r_site['SiteState']['id']); 
//        $r_icon = $this->SiteStateIcon->read(null, 1); 
//        echo '<pre>';
//        var_dump($r_site);
//        var_dump($r_site['SiteState']['id']);
//        var_dump($r_icon);
//        echo '</pre>'; die;
        return array (
            $r_site['Site']['lat'],
            $r_site['Site']['lon'],
            null, // 'data:'.$r_icon['SiteStateIcon']['img_type'].';base64,'.base64_encode( $r_icon['SiteStateIcon']['img_data'] ),
            // 'data:'.$r_site['SiteStateIcon']['img_type'].';base64,'.base64_encode( $r_site['SiteStateIcon']['img_data'] ),
            $r_site['Site']['site_vf']
        );        
    }
    
    /*
     * Sets an array of zones
     */
    function getZones() {
        $this->set('zones',$this->Site->Zone->find('list',array('order' => array('Zone.name ASC'))));
    }
    
    /*
     * Save an array of connectivity types
     */
    function getConnectivityTypes() {
        $this->Site->ConnectivityType->
        $this->set('connectivitytypes',$this->Site->ConnectivityType->find('list'));
    }
    
    /*
     * Save an array of site states
     */
    function getSiteStates() {
        $sitestates = $this->Site->SiteState->find('list', array(
            'conditions' => array('project_id' => $this->Session->read('project_id') )
        ));
        $this->set(compact( 'sitestates' ));
    }
    
    /*
     * Save an array of power types
     */
    function getPowerTypes() {
        $powertypes = array( 0 => 'Unknown' );
        $powertypes += $this->Site->PowerType->find('list');
        $this->set(compact('powertypes'));
    }
    
    /*
     * Save an array of tower types
     */
    function getTowerTypes() {
        // TowerTypes are project-specific, so the find is a little different here
        $towertypes = array( 0 => 'Unknown' );
        $towertypes += $this->Site->TowerType->find('list', array(
            'conditions' => array('project_id' => $this->Session->read('project_id') )
        ));
        $this->set(compact('towertypes'));
    }
    
    /*
     * Save an array of tower members
     */
    function getTowerMembers() {
        $towermembers = array( 0 => 'Unknown' );
        $towermembers += $this->Site->TowerMember->find('list');
        $this->set(compact('towermembers'));
    }
    
    /*
     * Save an array of equipment spaces
     */
    function getEquipmentSpace() {
        $equipmentspace = array( 0 => 'Unknown' );
        $equipmentspace += $this->Site->EquipmentSpace->find('list');
        $this->set(compact('equipmentspace'));
    }
    
    /*
     * Save an array of tower mounts
     */
    function getTowerMounts() {
        $towermounts = array( 0 => 'Unknown' );
        $towermounts += $this->Site->TowerMount->find('list');
        $this->set(compact('towermounts'));
    }   
    
    /*
     * Save an array of network switches
     */
    function getNetworkSwitches() {
        $networkswitches = $this->Site->NetworkSwitch->find('list', array(
            'conditions' => array('project_id' => $this->Session->read('project_id') )
        ));
        $this->set( 'networkswitches',$networkswitches );
//        $this->set( 'networkswitches', $this->Site->NetworkSwitch->find('list') );
    }
    
    /*
     * Save an array of network routers
     */
    function getNetworkRouters() {
        $networkrouters = $this->Site->NetworkRouter->find('list', array(
            'conditions' => array('project_id' => $this->Session->read('project_id') )
        ));
        $this->set( 'networkrouters',$networkrouters );
//        $this->set( 'networkrouters',$this->Site->NetworkRouter->find('list') );
    }
    
    /*
     * Save an array of network radios
     */
    function getNetworkRadios() {
        // Cake uses lazy binding, so we must explicitly bind here
        $this->Site->bindModel(array('hasMany' => array('NetworkRadio' => 
                             array('foreignKey' => 'site_id'))));
        $this->set('networkradios',$this->Site->NetworkRadio->find('list'));
    }
    
    /*
     * Save an array of radio types
     */
    function getRadioTypes() {
        $this->set('radiotypes',$this->Site->NetworkRadio->RadioType->find('list',
            array(
                'order' => array(
                    'RadioType.name ASC'
            )))
        );
    }
    
    /*
     * Save an array of antenna types
     */
    function getAntennaTypes() {
        $this->set('antennatypes',$this->Site->NetworkRadio->RadioType->AntennaType->find('list',
            array(
                'order' => array(
                    'AntennaType.name ASC'
            )))
        );
    }
    
    /*
     * Save an array of install teams
     */
    function getInstallTeams() {
        $this->set('installteams',$this->Site->InstallTeam->find('list',
            array(
                'conditions' => array('project_id' => $this->Session->read('project_id') ),
                'order' => array(
                    'InstallTeam.name ASC'
            )))
        );
    }
    
    /*
     * Return all the organizations the user may be assigned to
     */
    function getOrganizations() {
        // moved this into AppController since ContactsController has to do the same thing
        return parent::getOrganizationsForCurrentProject('Site');
    }
    
    /*
     *
     */
    function add() {
        $this->Site->create();
        
        // Cake has lazy model binding -- it seems we have to do this to allow
        // the saveAssociated bit to work
        $this->Site->bindModel(array('hasMany' => array('NetworkRadio' => 
                             array('foreignKey' => 'site_id'))));

        $this->getOrganizations();
        $this->getSiteStates();
        $this->getPowerTypes();
        $this->getTowerTypes();
        $this->getTowerMembers();
        $this->getEquipmentSpace();
        $this->getTowerMounts();
        $this->getNetworkSwitches();
        $this->getNetworkRouters();
        $this->getRadioTypes();
        $this->getAntennaTypes();
        $this->getInstallTeams();
        $this->getZones();
        
        // the user clicked Save on Add screen
        if ($this->request->is('post')) {
            // AppController::handleCancel();
            // normally we'd just save here, e.g.
            // if ($this->Site->save($this->request->data)) {
            // saveAssociated allows us to save Radios (Site hasMany Radio) from the
            // site add page
            
            // this allows us to save these attached items even though we don't yet
            // have a site ID
            unset($this->Site->NetworkSwitch->validate['site_id']);
            unset($this->Site->NetworkRouter->validate['site_id']);
            unset($this->Site->NetworkRadio->validate['site_id']);
            
            // remove any blank entries from the array of NetworkRadios
            $this->data = Set::filter($this->data);

            // if there is a lat/lon, compute the declination then save it back to the request object
            if (isset($this->request->data['Site']['lat']) && isset($this->request->data['Site']['lon'])) {
                $this->request->data['Site']['declination'] = $this->Site->getDeclination($this->request->data['Site']['lat'],$this->request->data['Site']['lon']);
            }
            
            $this->request->data['Site']['code'] = $this->getSiteCode( $this->request->data['Site']['code'] );
            
            //if ($this->Site->saveAssociated($this->request->data, array('validate'=>true))) {
            if ($this->Site->saveAssociated($this->request->data, array('validate'=>false))) {
                $this->Session->setFlash('Success! The site has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
    
    /*
     * Delete a site
     */
    function delete($id) {
        $this->Site->id = $id;
        if (!$this->Site->exists()) {
            throw new NotFoundException('Invalid site');
        }
        
        if ($this->Site->delete()) {
            // Cleanup any files associated with this site
            $this->Upload->deleteAll('Site', $id);
            $this->Session->setFlash('Site deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Error!  Site was not deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }

    /*
     * Edit a site
     */
    function edit($id = null) {
        $this->Site->id = $id;
        
        // get a list of zones, etc. the site may belong to
        $this->getZones();
        $this->getOrganizations();
        $this->getSiteStates();
        $this->getPowerTypes();
        $this->getTowerTypes();
        $this->getTowerMembers();
        $this->getEquipmentSpace();
        $this->getTowerMounts();
        $this->getNetworkSwitches();
        $this->getNetworkRouters();
        $this->getNetworkRadios();
        $this->getRadioTypes();
        $this->getAntennaTypes();
        $this->getInstallTeams();

        if (!$this->Site->exists()) {
            throw new NotFoundException('Invalid site');
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            // see comments in add
            unset($this->Site->NetworkRadio->validate['site_id']);
            $this->data = Set::filter($this->data);
//            echo '<pre>';print_r($this->request->data);echo '</pre>';
//            die;
            // it appears that on edit, we should save the related data using
            // the right controller
            // http://book.cakephp.org/2.0/en/models/saving-your-data.html
            //echo '<pre>';print_r($this->request->data);echo '</pre>';
            // The ID of the newly created user has been set
            // as $this->User->id.
            //$this->request->data['NetworkRadio'][0]['name'] = 'foo';
            //$this->request->data['NetworkRadio'][0]['site_id'] = $this->Site->id;
            
            // before we can save any radios on this site we have to set the site_id
            // so walk throug that array here and save that
            
            // compute the declination then save it back to the request object
  
            $this->request->data['Site']['declination'] = $this->Site->getDeclination($this->request->data['Site']['lat'],$this->request->data['Site']['lon']);
//            echo '<pre>';
//            print_r($this->request->data);
//            echo '</pre>';
//            die;
            if ($this->Site->saveAll($this->request->data, array('deep' => true))) {
                // with the HTML5 multiple file upload we seem to have to move
                // each file into place manually
                $n = 0;
                $upload_dir = WWW_ROOT.'files/Site/'.$id.'/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir);
                }
                
                foreach ( $this->request->data['Site']['file'] as $f ) {
//                    echo '<pre>';
//                    print_r($f);
//                    echo'</pre>';
                    if ( isset($f['tmp_name'])) {
                        move_uploaded_file($f['tmp_name'],$upload_dir.$f['name']);
                        $n++;
                    }
                }
                
                // if the specified a new switch
                if (isset($this->request->data['NetworkSwitch'])) {
                    // thre is only 1 switch per site, so set the site_id before save
                    $this->request->data['NetworkSwitch']['site_id'] = $this->Site->id;
                    $this->Site->NetworkSwitch->save($this->request->data['NetworkSwitch']);
                }
                
                // if they specified a router
                if (isset($this->request->data['NetworkRouter'])) {
                    // as above
                    $this->request->data['NetworkRouter']['site_id'] = $this->Site->id;
                    $this->Site->NetworkRouter->save($this->request->data['NetworkRouter']);
                }
                
                // keeping this loop code for now -- previously we had the ability to add many
                // radios on the add/edit page
                if (isset($this->request->data['NetworkRadio'])) {
                    foreach ($this->request->data['NetworkRadio'] as $key => $value) {
                        //echo "Key: $key; Value: $value<br />\n";
                        //$this->request->data['NetworkRadio'][$key]['site_id'] = $this->Site->id;
                        $this->request->data['NetworkRadio']['site_id'] = $this->Site->id;
                    }
                    $this->Site->NetworkRadio->saveAll($this->request->data['NetworkRadio']);
                }
                
                $this->Session->setFlash('The site has been saved.');
                // keep the user on the edit page so they can continue
                // adding radios to the site
                $this->redirect(array('action' => 'edit', $this->Site->id));


            } else {
                $this->Session->setFlash('Error!  The site could not be saved.');
            }
        } else {
            // show edit page
            $this->request->data = $this->Site->read(null, $id);
            
            // don't go any further if the user is not in the same project as this site
            if (!$this->isAllowed($this->Site->data['Site']['project_id'], $this->Auth->user('id')) ) {
                $this->redirect(array('action' => 'index'));
            }
        }
    }
    
    /*
     * Generates an Excel XML workorder
     */
    public function workorder($id) {
        $conditions = '';
        
        $site = $this->Site->findById($id);
        $conditions = array (
            "Contact.contact_type_id" => "2", // 2 is the primary key of the technical contact
            "Contact.priority" => "1", // 1 is the base priority level
            "Contact.organization_id" => $site['Site']['organization_id']
        );        
        $towercontacts = $this->Site->Organization->Contact->find('all',array('conditions' => $conditions));      
        $router_type_id = $site['NetworkRouter']['router_type_id'];
        $router = $this->Site->NetworkRouter->findByRouterTypeId( $router_type_id );
        
        $switch = $this->Site->NetworkSwitch->findBySwitchTypeId($site['NetworkSwitch']['switch_type_id']);        
        $radios = $this->Site->NetworkRadios->findAllBySiteId($id,array(),array('NetworkRadios.switch_port' => 'ASC'));
        
        // this seems kind of crazy -- and it is -- but since I'm not saving the link distance or bearing on the
        // NetworkRadio object (they are computed at view time), and really I can't do that since
        // link_id isn't set until after save (by a trigger), now I have to go compute those values again and save them
        // back to my array
        $n = 0;
        foreach ($radios as $radio) {
            //echo $radio['NetworkRadio']['name']."<br>";
            $this->loadModel('NetworkRadio');
            $this->NetworkRadio->recursive = 2;
            // get all the radios this radio may be linked to
            $query = 'call sp_get_remote_links('.$radio['NetworkRadios']['id'].')';
            $links = $this->NetworkRadio->query( $query );       
//            echo '<pre>';
//            print_r($links);
//            echo '</pre>';
            
            // this is pretty much usually going to be an array of one, except if
            // it's a multipoint radio, in which case distance and true_azimuth
            // will be set to the value of the final
            // item in the link array
            // guessing the person editing the work order will tweak that anyhow
            foreach ($links as $link) {
                $radio['NetworkRadios']['distance'] = 'N/A';
                // if it's not a sector radio, then calculate link distance and bearing
                if ($radio['NetworkRadios']['sector'] == 0 ) {
                    $link_id = $link['radios_radios']['dest_radio_id'];
                    $d = $this->NetworkRadio->getLinkDistance($radio['NetworkRadios']['id'],$link_id);
                    $radio['NetworkRadios']['distance'] = $d;

                    $b = $this->NetworkRadio->getRadioBearing($radio['NetworkRadios']['id'],$link_id);
                    $radio['NetworkRadios']['true_azimuth'] = $b;
                }
            }            
            
            $ip_address = '';
            $gw_address = '';
            $cidr = '';
            $gw_cidr = '';
            
            // legacy support for addrpool on the HRBN project (project_id = 1)
            if ( $this->Session->read('project_id') === 1 ) {
                $ip_address = $this->getAddrpoolIPAddress($radio['NetworkRadios']['name']);
                $gw_address = $this->getAddrpoolGatewayAddress($radio['NetworkRadios']['name']);
            } else {
                // get the ip address from IP Spaces
                $this->loadModel('NetworkInterfaceIpSpaces');
                $conditions = array(
                    'network_radio_id' => $radio['NetworkRadios']['id'],
                    'if_primary' => 1 // primary interface only
                );
                $x = $this->NetworkInterfaceIpSpaces->find('all',array('conditions' => $conditions));
                // if there is an IpSpace mapped to this radio
                if (isset($x[0])) {
                    $this->loadModel('IpSpace');
                    $this->IpSpace->read(null,$x[0]['NetworkInterfaceIpSpaces']['ip_space_id'] );
                    $ip_address = $this->IpSpace->field('ip_address');
                    $gw_address = $this->IpSpace->field('gw_address');
                    $cidr = $this->IpSpace->field('parent_cidr');
                    // now get the cidr of the gateway
                    $this->IpSpace->read(null,$this->IpSpace->field('gateway_id') );
                    $gw_cidr = $this->IpSpace->field('parent_cidr');
                }
            }
            
            $radio['NetworkRadios']['ip_address'] = $ip_address;
            $radio['NetworkRadios']['gw_address'] = $gw_address;
            $radio['NetworkRadios']['cidr'] = $cidr;
            $radio['NetworkRadios']['gw_cidr'] = $gw_cidr;
            
//            echo '<pre>';
//            print_r($radio['NetworkRadios']);
//            die;
            
            $antenna_type_id = $radio['NetworkRadios']['antenna_type_id'];
            $antenna_type = $this->NetworkRadio->RadioType->AntennaType->findById( $antenna_type_id );
            $radio['AntennaType']['name'] = $antenna_type['AntennaType']['name'];
            
            $radios[$n] = $radio;
            $n++;
        }

        // sort the radios by switch and router port (they can only be connected to one or the other)
        usort( $radios, function ($a, $b) { return $a['NetworkRadios']['switch_port'] - $b['NetworkRadios']['switch_port']; });
        usort( $radios, function ($a, $b) { return $a['NetworkRadios']['router_port'] - $b['NetworkRadios']['router_port']; });
        
        // the title on a work order is part of a project's meta-data
        if (isset($sites['Project']['workorder_title'])) {
            $title = $sites['Project']['workorder_title'];
        } else {
            $title = 'Inveneo Work Order';
        }
        
        // HRBN spefific mods
        if ( $this->Session->read('project_id') == 1 ) {
            $router_ips = array(
                'VLAN2 IP' => '',
                'VLAN11 IP' => '',
                'VLAN12 IP' => '',
                'VLAN13 IP' => '',
                'VLAN14 IP' => '',
                'VLAN15 IP' => '',
                'VLAN16 IP' => '',
                'VLAN17 IP' => '',
                'VLAN18 IP' => '',
                'VLAN99 IP' => ''
            );
        } else {
            // not sure if these are standard for every router?
             $router_ips = array(
                'loopback' => '',
                'LAN-bridge' => '',
            );
             // we need to know both 1) the type and 2) number of interfaces on the router
            $this->loadModel('RouterTypeNetworkInterfaceTypes');
            $rs = $this->RouterTypeNetworkInterfaceTypes->findAllByRouterTypeId( $router_type_id );
            foreach ( $rs as $p ) {
                $number =  $p['RouterTypeNetworkInterfaceTypes']['number'];
                $name = $p['NetworkInterfaceType']['name'];
                for ( $i = 0; $i < $number; $i++ ) {
                    $router_ips = array_merge($router_ips, array( $name.$i => '' ));
                }
            }
        }
        
        $this->set(compact('site','title','towercontacts','router','switch','radios','router_ips'));
        // generate the Excel file but without all the other stuff
        // in the layout -- so set the layout to null then set it back
        //$layout = $this->layout;
        $this->layout = 'blank';
        $this->render('workorder');
        $this->layout = 'default';
    }
    
    public function getSiteStatus() {
        $this->layout = 'blank';
        
    }
    
    /*
     * Query the monitoring system for site status and update the db -- this is
     * a back-end function meant to be called from cron.  It produces no output
     * and is outside Auth authentication.  Currently it is OpenNMS specific
     * and needs to be generalized.
     */
    public function cron( $project_id, $debug = false ) {
        // there is no view
        $this->autoRender = false;
        $this->layout = 'blank';
                
        $debug = true;
        
        if ( $project_id > 0 ) {
//            echo $project_id;

            // we could get this off each site, but let's try to keep it faster
            // by getting project info first, then making recurive -1 below
            $this->loadModel( 'Project', $project_id );
            $project = $this->Project->read();
            $ms_url = $this->Project->field( 'monitoring_system_url' );
            $ms_user = $this->Project->field( 'monitoring_system_username' );
            $ms_pass = $this->Project->field( 'monitoring_system_password' );
            
            if ( isset( $ms_url ) && isset( $ms_user ) && isset( $ms_pass ) ) {
                
                $HttpSocket = parent::getMonitoringSystemSocket( $ms_user, $ms_pass );

                if ( !is_null( $HttpSocket ) && ( self::validateURL( $ms_url )) ) {
                    $this->loadModel('Site');
                    // $this->Site->recursive = -1;
                    $sites = $this->Site->findAllByProjectId( $project_id );
                    foreach ( $sites as $site ) {
                        $down = 0;
                        $site_down = 0;
                        
                        // get the count of the total number of devices
                        // attached to this site
                        $n_devices = $this->getDeviceCount( $site['Site']['id'] );
                        
                        // get foreign ids for all the radios on this site
                        foreach( $site['NetworkRadios'] as $radio ) {
                            if (isset($radio['foreign_id'])) {
                                $this->loadModel('RadioType');
                                $radio_type = $this->RadioType->read(null, $radio['radio_type_id']);
                                $down += $this->checkAlarm( $HttpSocket, $ms_url, $radio_type['RadioType']['manufacturer'], $radio['foreign_id'], 'NetworkRadio' ); 
                            }
                        }
                        // at the moment there is only one switch, router at the site
                        if (isset($site['NetworkRouter']['foreign_id'])) {
                            $this->loadModel('RouterType');
                            $router_type = $this->RouterType->read(null, $site['NetworkRouter']['router_type_id']);
                            $down += $this->checkAlarm( $HttpSocket, $ms_url, $router_type['RouterType']['manufacturer'], $site['NetworkRouter']['foreign_id'], 'NetworkRouter' );                            
                        }
                        
                        if (isset($site['NetworkSwitch']['foreign_id'])) {
                            $this->loadModel('SwitchType');
                            $switch_type = $this->SwitchType->read(null, $site['NetworkSwitch']['switch_type_id']);
                            $down += $this->checkAlarm( $HttpSocket, $ms_url, $switch_type['SwitchType']['manufacturer'], $site['NetworkSwitch']['foreign_id'], 'NetworkSwitch' );
                        }
                        
                        if ( $down > 0 ) {
                            // if the down count is equal to the number of devices, then all
                            // nodes are down, and the site is down, so set site_down to 1
                            if ( $down == $n_devices ) {
                                $site_down = 1;
                            } else {
                                // otherwise, some nodes are down, and site_down is > 0 and < 1
                                $site_down = $down / $n_devices;
                            }
                        }

                        // this is a little sloppy, but is_down should be null there 
                        // are no devices on the site
                        if ( $n_devices == 0 ) {
                            $site_down = null;
                        }
                                             
                        if ( self::CRON_DEBUG ) {
                            echo '<pre>Counting Devices:<BR>';
                            print_r("<LI>Total Devices: $n_devices</LI>");
                            print_r("<LI>Down Count: $down</LI>");
                            print_r("<LI>Site Down: $site_down</LI>");
                            echo '</pre>';
                        }
                        
                        // finally, save the site's is_down calculation back to the Site model
                        $this->Site->id = $site['Site']['id'];
                        $this->Site->saveField( 'is_down', $site_down );                        
                    }
                }
            }
        }
    }
    
    
    private function checkAlarm( $HttpSocket, $ms_url, $node_foreign_source, $node_foreign_id, $model ) {
        $is_down = 0;   
        // $node_foreign_source = 'XXX';
        if ( self::CRON_DEBUG ) {
            echo '<pre>';
            echo "<LI>URI: $ms_url";
            echo "<LI>Node Foreign Source: $node_foreign_source";
            echo "<LI>Node Foreign Id: $node_foreign_id";
            echo "<LI>Application Model: $model";
            echo "<LI>".$ms_url.'/alarms?node.foreignSource='.$node_foreign_source.'&node.foreignId='.$node_foreign_id.'&uei=uei.opennms.org/nodes/nodeDown';
            echo '</pre>';
        }

        /*
        if ( $node_foreign_id == 1321398919152 ) {
            echo "Found it!<BR>";
            die;
        }
        */

        // query for a nodeDown alarm
        $response = $HttpSocket->request(
            array(
                'method' => 'GET',
                'uri' => $ms_url.'/alarms?node.foreignSource='.$node_foreign_source.'&node.foreignId='.$node_foreign_id.'&uei=uei.opennms.org/nodes/nodeDown',
                'header' => array('Content-Type' => 'application/xml')
            )
        );

        if ( self::CRON_DEBUG ) {
            echo '<pre><b>uei.opennms.org/nodes/nodeDown</b><BR>';
            print_r( $response->code );
            print_r( $response->body );
            echo '</pre>';
        }

        $xmlIterator = new SimpleXMLIterator( $response->body );
        for( $xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next() ) {
            if( $xmlIterator->hasChildren() ) {
//                debug( $xmlIterator->current() );
                $severity = (string)$xmlIterator->current()->attributes()->severity;
                // $node_id = $xmlIterator->current()->attributes()->id;
                // if there is a result but the severity is "CLEARED" then
                // there is still an alarm but the node is up
                $is_down = 1;
                if ( $severity === "CLEARED" ) {
                    if ( self::CRON_DEBUG ) {
                        echo( "<BR>There is still an alarm on the node with the foreignId $node_foreign_id, but it is cleared" );
                    }
                    $is_down = 0;
                }
            }
        }

        $this->loadModel( $model );
        $device = $this->$model->findByForeignId( $node_foreign_id );
        if ( $device != null ) {
            // set the flags
            $device[ $model ]['is_down'] = $is_down;
            // $device[ $model ]['node_id'] = $node_id; I think I was just saving this for convenience sake?
            $device[ $model ]['checked'] = date("Y-m-d H:i:s"); // last checked
            $this->$model->id = $device[ $model ]['id'];

            if ( self::CRON_DEBUG ) {
                echo '<pre>';
                print_r( $device[ $model ] );
                echo '</pre>';
            }
            $this->$model->save( $device[ $model ] );
            $found = true;
        }
        
        return $is_down;
    }
    
    /*
     * 
     */    
    public function linker() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Site->recursive = -1;
            $id1 = $this->request->data['Site']['site1-id'];
            $id2 = $this->request->data['Site']['site2-id'];
            
            if ( $id1 != $id2 ) {
                $this->Site->id = $id1;
                $site1 = $this->Site->read();
                $site1code = $site1['Site']['code'];

                $this->Site->id = $id2;
                $site2 = $this->Site->read();
                $site2code = $site2['Site']['code'];

                // we should probably allow an admin define default RadioType
                // for now let's just assume these 2 radios will be the first
                // in the list
                $this->loadModel('RadioType');
                $radio_type_tmp = $this->RadioType->find('first');
                $radio_type_id = $radio_type_tmp['RadioType']['id'];
                $antenna_type_id = $radio_type_tmp['AntennaType'][0]['id']; // get the first compatible antenna
                
//                echo '<pre>';
//                print_r($radio_type_id);
//                echo '<BR>';
//                print_r($antenna_type_id);
//                echo '</pre>';
//                die;
                
                // as above
                $this->loadModel('RadioMode');
                $radio_modes_tmp = $this->RadioMode->find('first');
                $radio_mode_id_1 = $radio_modes_tmp['RadioMode']['id'];
                $radio_mode_id_2 = $radio_modes_tmp['RadioMode']['inverse_mode_id'];
    //            var_dump($radio_mode_id);
    //            die;

                $this->loadModel('NetworkRadio');
                $this->NetworkRadio->create();


                // 1st radio
                $data1 = array(
                    'site_id' => $site1['Site']['id'],
                    'name' => $site1code.'-'.$site2code,
                    'ssid' => $site1code.'-'.$site2code,
                    'radio_type_id' => $radio_type_id,
                    'antenna_type_id' => $antenna_type_id,
                    'radio_mode_id' => $radio_mode_id_1
                );

                // 2nd radio
                $data2 = array(
                    'site_id' => $site2['Site']['id'],
                    'name' => $site2code.'-'.$site1code, // opposite 1st radio
                    'ssid' => $site1code.'-'.$site2code, // same as 1st radio
                    'radio_type_id' => $radio_type_id, // same as 1st radio
                    'antenna_type_id' => $antenna_type_id, // same as 1st radio
                    'radio_mode_id' => $radio_mode_id_2
                );

                // both radios!
                $data = array(
                    array('NetworkRadio' => $data1),
                    array('NetworkRadio' => $data2)
                );
                // save both at the same time
                $this->NetworkRadio->saveMany($data, array('deep' => true));
                $this->Session->setFlash('Success! Radios created and sites linked.');
                // $this->redirect(array('controller'=>'network_radios','action' => 'index'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Error! Cannot link a site to itself.');
                // $this->redirect(array('controller'=>'network_radios','action' => 'index'));
                $this->redirect(array('action' => 'index'));
            }
        }
    
        $conditions = array(
            'AND' => array(
                'Site.project_id' => $this->Session->read('project_id') // only show sites for the current project
            )
        );
        
        $this->Site->recursive = -1;
        $sites = $this->Site->find('list',
                array(
                    //'fields' => array('Site.id', 'Site.name_vf'),
                    'conditions' => $conditions,                    
            ));
        
//        echo '<pre>';
//        var_dump($sites);
//        echo '</pre>';
        $this->set(compact('sites'));
    }
    
    /*
     * 
     */
    public function sitesurvey() {
        if ($this->request->is('post') || $this->request->is('put')) {
             if (is_uploaded_file( $this->request->data['Site']['File']['tmp_name'] )) {
                $tmpname = $this->request->data['Site']['File']['tmp_name'];
                foreach( str_getcsv ( file_get_contents( $tmpname ), $line = "\n" ) as $row ) 
                    $csv[] = str_getcsv( $row, $delim = ',', $enc = '"' );
                echo '<pre>';
                print_r( $csv );
                echo '</pre>';
                die;
             }
        }
    }
    
    /*
     * Returns the total number of devices (radios, routers, switches) on a site
     */
    private function getDeviceCount( $id ) {
        $this->Site->read(null, $id );
        $n_radio = $this->Site->NetworkRadios->find(
                'count',
                array('conditions' => array('NetworkRadios.site_id' => $id )
        ));
        
        // is there a better way to count attached routers and switches?
        $n_router = 0;
        if ( $this->Site->field('network_router_id') > 0 ) {
            $n_router++;
        }
        
        $n_switch = 0;
        if ( $this->Site->field('network_switch_id') > 0 ) {
            $n_switch++;
        }
        
        if ( self::CRON_DEBUG ) {
            echo '<pre>Counting Devices:<BR>';
            print_r("<LI>Site ID: $id</LI>");
            print_r("<LI>Radios: $n_radio</LI>");
            print_r("<LI>Routers: $n_router</LI>");
            print_r("<LI>Switches: $n_switch</LI>");
            echo '</pre>';
        }
        
        return $n_radio + $n_router + $n_switch;
    }
    
    /*
     * Ensure unique site codes
     */
    private function getSiteCode( $name ) {
        // this is our default site code -- remove all special characters,
        // whitespace, grab the first 6 characters and make it
        // uppercase
        $code = $name;
        preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $code);
        $code = str_replace(' ', '', $code);
        $code = strtoupper( substr($code, 0, 6) );
        //var_dump($code);
        $unique = false;
        $n = 0;
        // this may go out of control if all permutations of the site code are
        // not available!
        while (( !$unique ) && ( $n <= 999999 )) {
            // look for other sites that have this site code
            $count = $this->Site->find('count', array(
                'conditions' => array(
                    'code' => $code,
                    'Site.project_id' => $this->Session->read('project_id')
            )));
            
//            echo '<pre>';
//            var_dump( $code );
//            var_dump( $count );        
//            echo '</pre>';
                       
            if ( $count == 0 ) {
                $unique = true;
            } else {
                // if any are found, cut off some characters and replace them with
                // an integer - should result like this:
                // AAAAAA -- matches existing site code AAAAAAA
                // AAAAA0 -- next attempt, then
                // AAAAA1, AAAAA2... AAAAA9, then AAAA10..AAAA99, up to 999999
                $lob = strlen( $n );
                $code = substr($code, 0, -$lob) . $n;                
                $n++;
            }
        }
        
        return $code;
    }
    
    public function foobar( $id, $file, $ext = null ) {
        // echo $file;
        unlink( WWW_ROOT.'files/Site/'.$id.'/'.$file );
    }
    
    /*
     * Check the user's role to determine if sufficient permission to perform
     * the intended action.
     */
    public function isAuthorized($user) {
        // pages that anyone (basically with the view rolealias) can access
        $allowed = array( "index", "view", "overview", "topology", "workorder", "cron" );
        if ( in_array( $this->action, $allowed )) {
            return true;
        }
        
        // pages that editors can access
        $allowed = array( "add", "edit", "delete", "export", "foobar" );
        if ( in_array( $this->action, $allowed )) {
            // maybe this is duplicative to check role here...
            if ( $this->Session->read('role') === 'edit') {
                return true;
            }
        }
                
        return parent::isAuthorized($user);
    }   
}
?>