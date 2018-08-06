<?php
/**
 * Super class controller for network devices (radios, routers, switches).
 * 
 * Developed against CakePHP 2.2.5 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.Controller
 * @since         NetworkDeviceController was introduced in Poundcake v2.3
 * @license       GNU General Public License
 */

App::uses('AppController', 'Controller');

class NetworkDeviceController extends AppController {
    
    const OPENNMS = 1;
    
    /*
     * Return the username required to use the monitoring system's API for the current project
     */
    private function getMonitoringSystemUsername() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() ); 
        return $project['Project']['monitoring_system_username'];
    }
    
    /*
     * Return the password required to use the monitoring system's API for the
     * current project
     */
    private function getMonitoringSystemPassword() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() ); 
        // this password is encrypted/decrypted with the CryptableBehavior on
        // the Project
        // addslashes escapes the string properly
        $pass = addslashes( $project['Project']['monitoring_system_password'] );
        return $pass;
    }
    
    
    /*
     * Returns the monitoring system name for the current project
     */
    private function getMonitoringSystemName() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() ); 
        return $project['MonitoringSystemType']['name'];        
    }
    
    /*
     * Return the uniform resource identifier (basically the URL) to the API
     * of the monitoring system for the current project
     */
    private function getMonitoringSystemBaseURI() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() );
        $uri = $project['Project']['monitoring_system_url'];
        
        /*/* append a / if it's missing one
        if ( $uri[strlen($uri)-1] != '/' ) {
            $uri = $uri.'/';
        }
        */
        
        // remove the slash if it has one
        if ( $uri[strlen($uri)-1] === '/' ) {
            $uri = substr($uri, 0, -1);
        }
        return $uri;
    }
    
    /*
     * Return the SNMP community string for the current project
     */
    private function getSnmpCommunity() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() ); 
        return $project['Project']['snmp_community_name'];
    }
    
    /*
     * Return the SNMP version for the current project
     */
    private function getSnmpVersion() {
        $project = ClassRegistry::init('Project')->findById( $this->Session->read('project_id') , array() ); 
        return $project['SnmpType']['name'];
    }
    
    /*
     * Temporary function...
     * 
     * @see This now exists as a model of its own -- NetworkService -- but at
     * the moment we're not using it since it clogs up the UI, and ultimately
     * we'll have a more complex UI for provisioning nodes
     */
    private function getMonitoredServices() {
        $services = array (
            'ICMP',
            'SNMP',
            'StrafePing'
        );
        return $services;
    }
    
    /*
     * Called from a sub-class to provision a node (switch, router, radio) into
     * the project's monitoring system
     */
    protected function provisionNode( $name, $ip_addr, $debug ) {
        $model = $this->modelClass;
        
        $system = $this->getMonitoringSystemType();
        $ret = null;

        // filter_var returns success when the IP is 0.0.0.0
        // ip2long returns false in the case of an invalid IP
        if( filter_var($ip_addr, FILTER_VALIDATE_IP) && ip2long($ip_addr) ) {
            if ( $system == self::OPENNMS ) {
                $ret = $this->provisionNodeOpenNMS( $name, $ip_addr, $debug );
            }
        }
        
        return $ret;
    }
    
    /*
     * Returns true or false if a string ends with a specific character
     */
    private function endsWith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, -$testlen) === 0;
    }

    /*
     * 
     */
    protected function getMonitoringSystemLink( $id ) {
        $url = null;
        if ( $id > 0 ) {           
            $system = $this->getMonitoringSystemType();
            
            if ( $system == self::OPENNMS ) {
                // http://lab.inveneo.org:8980/opennms/element/node.jsp?node=50               
                $baseURI = $this->getMonitoringSystemBaseURI();
                //$baseURI = preg_replace( $pattern, $baseURI );
//                $info = parse_url($baseURI);
//                //var_dump($info);                
//                $info["path"]=dirname($info["path"]);
//                $new_url = $info["scheme"]."://".$info["host"].':'.$info["port"];
//                $new_url .= $info["path"];
//                if(!empty($info["query"])) $new_url .= "?".$info["query"];
//                if(!empty($info["fragment"])) $new_url .= "#".$info["fragment"];
//                // append a slash if it's not already there
//                if ( !$this->endsWith( '/', $new_url) )
//                    $new_url .= '/';
                $url = $this->removeRestFromURL( $this->getMonitoringSystemBaseURI() );
                $url .= 'element/node.jsp?node='.$id;
                //debug($new_url);
            }
        }
        $this->set('node_detail_url', $url );
    }
    
    /*
     * Remove "/rest" from the end of the OpenNMS url
     */
    private function removeRestFromURL( $url ) {
        $info = parse_url( $url );
        //var_dump($info);                
        $info["path"]=dirname($info["path"]);
        $new_url = $info["scheme"]."://".$info["host"].':'.$info["port"];
        $new_url .= $info["path"];
        if(!empty($info["query"])) $new_url .= "?".$info["query"];
        if(!empty($info["fragment"])) $new_url .= "#".$info["fragment"];
        // append a slash if it's not already there
        if ( !$this->endsWith( '/', $new_url) )
            $new_url .= '/';
        return $new_url;
    }
    
    protected function getAllNetworkAddresses( $ip_space_id ) {
        $this->loadModel( 'IpSpace' );
        $this->IpSpace->recursive = -1;
        $this->IpSpace->id = $ip_space_id;
        $ip_space = $this->IpSpace->read();
        // we can get both gateway and subnet off this IpSpace
        $gw_address = $ip_space['IpSpace']['gw_address']; // gw_address is a virtual field on the IpSpace model
        $subnet_mask = $this->cidr2NetmaskAddr( long2ip($ip_space['IpSpace']['ip_address']).'/'. $ip_space['IpSpace']['parent_cidr'] );  // parent_cidr is a virtual field on the IpSpace model
        
        // but we need to load the parent to get the network address
        $this->IpSpace->id = $ip_space['IpSpace']['parent_id'];
        $parent_ip_space = $this->IpSpace->read();
        $network_address = $parent_ip_space['IpSpace']['ip_address'];
        
        $this->set(compact('gw_address','network_address','subnet_mask'));
    }
    
    /*
     * This should turn 192.168.1.3/24 into 255.255.255.0
     */
    private function cidr2NetmaskAddr($cidr) {
        $ta = substr($cidr, strpos($cidr, '/') + 1) * 1;
        $netmask = str_split(str_pad(str_pad('', $ta, '1'), 32, '0'), 8);
        foreach ($netmask as &$element) $element = bindec($element);
        return join('.', $netmask);
    }
    
    /*
     * Return alarms for a given node
     */
    protected function getAlarms() {
        // revisit - need to add caluses for other network monitoring systems
        $alarms = array();        
        $model = $this->modelClass;
        $type = $this->$model->getForeignSource();
        $baseURI = $this->getMonitoringSystemBaseURI();
        $id = $this->$model->data[ $model ]['foreign_id'];
        // http://localhost:8980/opennms/rest/alarms?node.foreignSource=Routers&node.foreignId=1001
        $url = $baseURI.'/alarms?node.foreignSource='.$type.'&node.foreignId='.$id;
        $HttpSocket = parent::getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
        if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
            $response = $HttpSocket->request(
                        array(
                            'method' => 'GET',
                            'uri' => $url
                        )
                );
//            var_dump( $response );
//            die;
            if ( $response->body != null ) {
                $xmlIterator = new SimpleXMLIterator( $response->body );            
                for( $xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next() ) {
                    if( $xmlIterator->hasChildren() ) {
                        // $attrs = $xmlIterator->current()->attributes();
                        // can filter by alarms of major severity here
    //                    var_dump( $xmlIterator->current() );
                        // iterate through all alarms 
                        foreach( $xmlIterator->children() as $alarm ) { 
    //                        var_dump( $alarm );
    //                        echo "<BR>";
                            $node_alarm = array();
                            $severity = (string)$alarm->attributes()->severity;
                            $description = (string)$alarm->description;
                            $firstEventTime = (string)$alarm->firstEventTime;
                            array_push( $node_alarm, $severity );
                            array_push( $node_alarm, $description ); 
                            array_push( $node_alarm, $firstEventTime );                        
                            array_push( $alarms, $node_alarm );
                        }
                    }
                }
            }
        }        
        // debug( $alarms );
        return $alarms;
    }
    
    /*
     * Return events for a given node
     */
    protected function getEvents() {
        // revisit - need to add caluses for other network monitoring systems
        $events = array();        
        $model = $this->modelClass;
        $type = $this->$model->getForeignSource();
        $baseURI = $this->getMonitoringSystemBaseURI();
        //$id = $this->$model->data[ $model ]['foreign_id'];
        $id = $this->$model->data[ $model ]['node_id'];
        // http://localhost:8980/opennms/rest/alarms?node.foreignSource=Routers&node.foreignId=1001
        $url = $baseURI.'/events?node.id='.$id;
//        debug($url);
        $HttpSocket = parent::getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
        if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
            $response = $HttpSocket->request(
                        array(
                            'method' => 'GET',
                            'uri' => $url
                        )
                );
//            var_dump( $response );
//            die;
            if ( $response->body != null ) {
                $xmlIterator = new SimpleXMLIterator( $response->body );            
                for( $xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next() ) {
                    if( $xmlIterator->hasChildren() ) {
                        // $attrs = $xmlIterator->current()->attributes();
                        // can filter by alarms of major severity here
    //                    var_dump( $xmlIterator->current() );
                        // iterate through all alarms 
                        foreach( $xmlIterator->children() as $event ) { 
    //                        var_dump( $event );
    //                        echo "<BR>";
                            $node_event = array();
                            $severity = (string)$event->attributes()->severity;
                            $description = (string)$event->description;
                            $createTime = (string)$event->createTime;
                            array_push( $node_event, $severity );
                            array_push( $node_event, $description ); 
                            array_push( $node_event, $createTime );                        
                            array_push( $events, $node_event );
                        }
                    }
                }
            }
        }        
//        debug( $events );
        return $events;
    }
    
    /*
     * Provision a node into OpenNMS
     */
    private function provisionNodeOpenNMS( $name, $ip_addr, $debug ) {
        // $this->autoRender = false;        
        $model = $this->modelClass;
        
        // getForeignSource is a function on the model for each of NetworkRadio/NetworkSwitch/NetworkRouter
        $type = $this->$model->getForeignSource();
                
        if (isset($type)) {
            
            // creating XML from a data array -- I could not get attributes to work
            // using Cake's XML class, so I am doing it this way:
            // http://www.viper007bond.com/2011/06/29/easily-create-xml-in-php-using-a-data-array/

            // random number for the foreign-id field
            $foreign_id = $this->generateRandomString( 15 );

            $data = array(
                'name' => 'node', // "name" required, all else optional
                'attributes' => array(
                    'node-label' => $name,
                    'foreign-id' => $foreign_id,
                    'building' => $type
                ),
                array(
                    'name' => 'interface',
                    'attributes' => array(
                        'snmp-primary' => 'P', // N?
                        'status' => 1,
                        'ip-addr' => $ip_addr,
                        'descr' => 'Provisioned by Tower DB'
                     ),
    //                Commenting this out but saving it for posterity.  The rest of
    //                the requisite array/XML values is now added by an array of
    //                monitored services, see below -- getMonitoredServices
    //                
    //                array(
    //                    'name' => 'monitored-service',
    //                    'attributes' => array(
    //                        'service-name' => 'ICMP'
    //                     ),
    //                    'value'=> '',
    //                ),
    //                array(
    //                    'name' => 'monitored-service',
    //                    'attributes' => array(
    //                        'service-name' => 'SNMP'
    //                     ),
    //                    'value'=> '',
    //                )               
                )
            );

            $services = $this->getMonitoredServices();
            foreach ($services as $service ) {
                array_push( $data[0], array (
                    'name' => 'monitored-service',
                    'attributes' => array(
                        'service-name' => $service
                    ),
                    'value'=> '',
                ));
            }

            $xml_string = $this->getXMLStringFromArray( $data );
            
            if ( $debug ) {
                echo 'L/P: '.$this->getMonitoringSystemUsername().' '. $this->getMonitoringSystemPassword().'<BR><pre>';
                echo "Sending this XML:<BR><pre>";
                debug( $xml_string );
                echo "</pre><BR>";
            }

            // send it on to OpenNMS
            // for future reference, JSON HttpSocket example:  http://ask.cakephp.org/questions/view/how_to_post_json_with_httpsocket
            // $HttpSocket = new HttpSocket();
            // $HttpSocket->configAuth('Basic', 'admin', 'xx');        
            // $HttpSocket = $this->getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
            $HttpSocket = parent::getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
            $baseURI = $this->getMonitoringSystemBaseURI();
            
            if ( !is_null( $HttpSocket ) && ( self::validateURL( $baseURI )) ) {
                $response = $HttpSocket->request(
                        array(
                            'method' => 'POST',
                            //'uri' => 'http://lab.inveneo.org:8980/opennms/rest/requisitions/'.$type.'/nodes',
                            'uri' => $baseURI.'/requisitions/'.$type.'/nodes',
                            'header' => array('Content-Type' => 'application/xml'),
                            'body' => $xml_string,
        //                    'header' => array('content-type' => 'application/json'),
        //                    'body' => json_encode( $data )
                        )
                );


                if ( $debug ) {
                    echo "Response 1:<BR><pre>";
                    debug( $response );
                    echo "</pre><BR>";
                }

                // now create the XML for SNMP monitoring
                $data = $this->getSnmpConfig_opennms( $this->getSnmpVersion(), $this->getSnmpCommunity() );
                $xml_string = $this->getXMLStringFromArray( $data );
                
                if ( $xml_string != null ) {

                    if ( $debug ) {
                        echo "Sending this XML:<BR><pre>";
                        debug( $xml_string );
                        echo "</pre><BR>";
                    }

                    $response = $HttpSocket->request(
                            array(
                                'method' => 'PUT',
                                'uri' => $baseURI.'/snmpConfig/'.$ip_addr,
                                'header' => array('Content-Type' => 'application/xml'),
                                'body' => $xml_string,
                            )
                    );

                    if ( $debug ) {
                        echo "Response 2:<BR><pre>";
                        debug( $response );
                        echo "</pre><BR>";
                    }
                }

                // now run the actual import
                $response = $HttpSocket->request(
                        array(
                            'method' => 'PUT',
                            'uri' => $baseURI.'/requisitions/'.$type.'/import',
                            'header' => array('Content-Type' => 'application/xml'),
                            //'body' => $xml_string
                        )
                );

                if ( $debug ) {
                    echo "Response 3:<BR><pre>";
                    debug( $baseURI.'/requisitions/'.$type.'/import' );
                    debug( $response );
                    echo "</pre><BR>";
                }
                
                // Get the status code for the response.
                // OpenNMS seems to return HTTP303 -- because it's an asynchronous call?
                $code = $response->code;
                if ( $code == 303 ) {
                    return $foreign_id;
                }
            }
        }
        
        // otherwise, problem
        return null;
    }
    
    /*
     * Return a class constant (enumarated value) for the monitoring system type
     */
    private function getMonitoringSystemType() {
        $ret = null;
        $system = $this->getMonitoringSystemName();
        if (preg_match( "/opennms/i", $system )) {
            $ret = self::OPENNMS;
        }
        return $ret;
    }
    
    /*
    // this assumes you already have found the nodeId via a previous REST call or some other means.  Provided more as an example than what you might want.
    private function getNodeInterfaces( $node_id ) {
        $baseURI = $this->getMonitoringSystemBaseURI();
        
        $url = $baseURI."/nodes/".$node_id."/snmpinterfaces";
        
        $HttpSocket = parent::getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
        if ( !is_null( $HttpSocket ) && ( isset( $url ))  ) {
            $response = $HttpSocket->request(
                        array(
                            'method' => 'GET',
                            'uri' => $url
                        )
                );
        }
        return simplexml_load_string( $response );
    }
    */
    
    protected function getPerformanceGraphs( $node_id ) {
        // $ints = $this->getNodeInterfaces( $node_id );
        $performance_graphs = array();
        
        $HttpSocket = parent::getMonitoringSystemSocket( $this->getMonitoringSystemUsername(), $this->getMonitoringSystemPassword() );
        $model = $this->modelClass;
        $ip_address = $this->$model->data[$model]['ip_address'];
            
        // BEGIN Ping response time
        $days = array( 1, 7 ); // days response time
        foreach ( $days as $day ) {
            $chars = array('/','.',':','-',' ');
            $endtime = time();
            $starttime = (string)(time() - ($day * 24 * 60 * 60)) ;
            $endtime = $endtime . '000';
            $starttime = $starttime . '000';          

            // http://lab.inveneo.org:8980/opennms/graph/graph.png?resourceId=node[86].responseTime[10.50.0.20]&report=icmp&start=1359544402267&end=1360149202267
            $url = $this->removeRestFromURL( $this->getMonitoringSystemBaseURI() );
            $url .= "graph/graph.png?reports=all&resourceId=node[$node_id].responseTime[$ip_address]";
            $url .= "&report=icmp&start=$starttime&end=$endtime";
            
            if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
                $response = $HttpSocket->request(
                    array(
                        'method' => 'GET',
                        'uri' => $url
                    )
                );
            }

            if ( $response->body != null ) {
                array_push( $performance_graphs, array( $response->body, "$day-Day ICMP Response Time" ));
            }
            
            $response = null;
        }
        // END Ping response time
        
        // BEGIN Negotiated link rate
        $days = array( 1, 7 ); // days response time
        foreach ( $days as $day ) {
            // we have to query mtxrWlStatIndex[5] and mtxrWlStatIndex[5]
            $mtxrWlStatIndex = array( 5, 7 );
            foreach ( $mtxrWlStatIndex as $mtxr ) {
                $chars = array('/','.',':','-',' ');
                $endtime = time();
                $starttime = (string)(time() - ($day * 24 * 60 * 60)) ;
                $endtime = $endtime . '000';
                $starttime = $starttime . '000';          

                // http://192.168.15.38:8980/opennms/graph/graph.png?resourceId=node%5b41%5d
                // .mtxrWlStatIndex%5b7%5d&report=mikrotik.wlstatbps
                // &start=1360080834448&end=1360081479225&graph_width=&graph_height=

                $url = $this->removeRestFromURL( $this->getMonitoringSystemBaseURI() );
                $url .= "graph/graph.png?resourceId=node[$node_id]";
                $url .= ".mtxrWlStatIndex[$mtxr]&report=mikrotik.wlstatbps";
                $url .= "&start=$starttime&end=$endtime";
                // echo "<a href=\"$url\">$url</a><br>";
                // die;

                if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
                    $response = $HttpSocket->request(
                        array(
                            'method' => 'GET',
                            'uri' => $url
                        )
                    );
                }

                if ( $response->body != null ) {
                    array_push( $performance_graphs, array( $response->body, "$day-Day Negotiated Link Rate" ));
                }

                $response = null;
            }
        }
        // END Negotiated link rate
        
        // BEGIN RSSI
        $days = array( 1, 7 ); // days response time
        foreach ( $days as $day ) {
            // we have to query mtxrWlStatIndex[5] and mtxrWlStatIndex[5]
            $mtxrWlStatIndex = array( 5, 7 );
            foreach ( $mtxrWlStatIndex as $mtxr ) {
                
                $chars = array('/','.',':','-',' ');
                $endtime = time();
                $starttime = (string)(time() - ($day * 24 * 60 * 60)) ;
                $endtime = $endtime . '000';
                $starttime = $starttime . '000';          

                // http://192.168.15.38:8980/opennms/graph/graph.png?resourceId=node%5b41%5d
                // .mtxrWlStatIndex%5b7%5d&report=mikrotik.wlstatbps
                // &start=1360080834448&end=1360081479225&graph_width=&graph_height=

                $url = $this->removeRestFromURL( $this->getMonitoringSystemBaseURI() );
                $url .= "graph/graph.png?resourceId=node[$node_id]";            
                $url .= ".mtxrWlStatIndex[$mtxr]&report=mikrotik.wlstatrssi";
                $url .= "&start=$starttime&end=$endtime";

                //echo ">> <a href=\"$url\">$url</a><br>";
                //die;

                if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
                    $response = $HttpSocket->request(
                        array(
                            'method' => 'GET',
                            'uri' => $url
                        )
                    );
                }

                if ( $response->body != null ) {
                    array_push( $performance_graphs, array( $response->body, "$day-Day RSSI" ));
                }

                $response = null;
            }
        }
        // END RSSI
        
        // BEGIN Throughput
        $baseURI = $this->getMonitoringSystemBaseURI();
        
        // get all the interfaces -- we need to query each one
        if ( !is_null( $HttpSocket ) && ( self::validateURL( $baseURI )) ) {
            $response = $HttpSocket->request(
                    array(
                        'method' => 'GET',
                        'uri' => $baseURI.'/nodes/'.$node_id.'/snmpinterfaces',
                    )
            );
        }
        
        foreach ( $days as $day ) {
            $chars = array('/','.',':','-',' ');
            $endtime = time();
            $starttime = (string)(time() - ($day * 24 * 60 * 60)) ;
            $endtime = $endtime . '000';
            $starttime = $starttime . '000';          
            
            if ( $response != null ) {
                $ints = simplexml_load_string( $response->body );
                // var_dump($ints);
                if ( $ints != null ) {
                    for ( $i = 0; $i < $ints->attributes()->count; $i++ ) { // count or totalCount?
                        // var_dump( $ints->snmpInterface[$i] );
                        $ifname = $ints->snmpInterface[$i]->ifDescr;
                        // only look at eth interfaces
                        if (preg_match( "/eth/i", $ifname )) {
                            $mac = $ints->snmpInterface[$i]->physAddr;
                            if ( $mac != '' ) { // l0 has no MAC address
                                $mac_and_if = $ifname .'-'. $mac;
                                $if = str_replace($chars, "_", $ifname);
                                if ( strlen(trim($mac)) < 12 ) { $mac_and_if = $if; } else { $mac_and_if = $if .'-'. $mac; };
                                // debug($mac_and_if);

                                // http://lab.inveneo.org:8980/opennms/graph/graph.png?resourceId=node%5b50%5d.interfaceSnmp%5bath0-00156deeaa78%5d&report=mib2.bits&start=1360070293212&end=1360156693212
                                $url = $this->removeRestFromURL( $this->getMonitoringSystemBaseURI() );
                                $url .= "graph/graph.png?resourceId=node[$node_id].interfaceSnmp[$mac_and_if]&report=mib2.bits";
                                $url .= "&start=$starttime&end=$endtime";
                                //echo "<a href=\"$url\">$url</a>";

                                if ( !is_null( $HttpSocket ) && ( self::validateURL( $url )) ) {
                                    $response2 = $HttpSocket->request(
                                        array(
                                            'method' => 'GET',
                                            'uri' => $url
                                        )
                                    );
                                }

                                if ( $response2->body != null ) {
                                    array_push( $performance_graphs, array( $response2->body, "$day-Day Throughput, $ifname" ));
                                }
                                
                                $response2 = null;
                            }
                        }
                    }
                }
            }
        }
        // END Throughput
        
        //die;
        $this->set(compact('performance_graphs')); 
    }
    
    public function config( $device_id ) {
        
        $MIN_ACKTIMEOUT = 27; # microseconds
                
        $model = $this->modelClass;
        $this->$model->id = $device_id;
        $configuration_template_id = $this->$model->field('configuration_template_id');
        
        $name = $this->$model->field('name');
        
        if ( $model == 'NetworkRadio' ) {
            $query = 'call sp_get_remote_links('.$device_id.')';
            $links = $this->NetworkRadio->query( $query );
            $link_id = $links[0]['radios_radios']['dest_radio_id'];
            $distance = floor($this->$model->getLinkDistance( $device_id, $link_id ) * 1000); // convert back to meters
            // convert meters to acktimeout microseconds, from ubiquiti javascript'''
            $acktimeout = round($distance / 150 + $MIN_ACKTIMEOUT);      
            $frequency = $this->$model->field('frequency');
        }
        
        // get the SSID off the model
        $ssid = $this->$model->field('ssid');
        
        // this won't work for routers or switches
        $site_id = $this->$model->field('site_id');
        $this->loadModel('Site');
        $site = $this->Site->read(null, $site_id);
        $lat = $site['Site']['lat'];
        $lon = $site['Site']['lon'];
        $code = $site['Site']['code'];
        
        // get the IP address and related IP stuff from IP Spaces
        $this->loadModel('NetworkInterfaceIpSpaces');
        $t = $this->NetworkInterfaceIpSpaces->findByNetworkRadioId( $device_id );
        $ip_space_id = $t['NetworkInterfaceIpSpaces']['ip_space_id'];
        $this->loadModel('IpSpace');
        $this->IpSpace->id = $ip_space_id; //read(null,$ip_space_id);
        $ip_space = $this->IpSpace->read( null, $ip_space_id );
        $ip_address = $ip_space['IpSpace']['ip_address'];
        $subnet_mask = $this->cidr2NetmaskAddr( $ip_address.'/'. $ip_space['IpSpace']['parent_cidr'] );
        $gw_address = $ip_space['IpSpace']['gw_address'];
        
//        echo '<pre>';
//        print_r( $t['NetworkInterfaceIpSpaces'] );
//        echo '</pre>';
//        die;
        
        // get other info from the project
        $this->loadModel('Project');
        $project = $this->Project->read(null, $this->Session->read('project_id'));
        $dns1 = $project['Project']['dns1'];
        $dns2 = $project['Project']['dns2'];
        $secure_password = $project['Project']['secure_password'];
        $insecure_password = $project['Project']['insecure_password'];
        $snmp_community = $project['Project']['snmp_community_name'];
        $snmp_contact = $project['Project']['snmp_contact'];
        
//        echo '<pre>';
//        print_r($t);
//        print_r($ip_space);
//        echo '</pre>';
//        die;
        
        $this->loadModel('ConfigurationTemplate');
        $ct = $this->ConfigurationTemplate->read( null, $configuration_template_id );
        $body = $ct['ConfigurationTemplate']['body'];
        
        $body = str_replace( '%SSID%', $ssid, $body );
        $body = str_replace( '%IPADDRESS%', $ip_address, $body );
        $body = str_replace( '%SUBNETMASK%', $subnet_mask, $body );
        $body = str_replace( '%GATEWAY%', $gw_address, $body );
        $body = str_replace( '%SNMPCOMMUNITY%', $snmp_community, $body );
        $body = str_replace( '%SNMPCONTACT%', $snmp_contact, $body );
        $body = str_replace( '%SITECODE%', $code, $body );
        $body = str_replace( '%LAT%', $lat, $body );
        $body = str_replace( '%LON%', $lon, $body );
        $body = str_replace( '%SECUREPASSWORDHASH%', crypt( $secure_password,'salt' ), $body ); // yes, 'salt' is the salt!!!
        $body = str_replace( '%SECUREPASSWORD%', $secure_password, $body );
        $body = str_replace( '%INSECUREPASSWORD%', $insecure_password, $body );
        $body = str_replace( '%DNS1%', $dns1, $body );
        $body = str_replace( '%DNS2%', $dns2, $body );
        $body = str_replace( '%HOSTNAME%', $name, $body );
        $body = str_replace( '%LINKDISTANCE%', $distance, $body );
        $body = str_replace( '%ACKTIMEOUT%', $acktimeout, $body );
        $body = str_replace( '%FREQUENCY%', $frequency, $body );
//        echo "<pre>"; print_r($body); die;

        // output the file to the browser
        $this->layout = 'blank';
        $this->set( 'data', $body );
        $this->set( 'filename', $name );
        $this->render( 'config' );
        $this->layout = 'default';
        
    }
    
    /*
     * Return the datetime format for the current project
     */
    protected function getDateTimeFormat() {
        $model = $this->modelClass;
        // of course this will error if the item doesn't have a relation to project
        return $this->$model->Site->Project->field('datetime_format');
    }
    
    /*
     * Return proper XML from a given data array
     */
    private function getXMLStringFromArray( $data ) {        
        if ( $data != null ) {
            // generate the XML for adding a node
            $doc = new DOMDocument( '1.0', 'utf-8' );
            $doc->xmlStandalone = true; // adds attribute: standalone="yes"
            $child = parent::generateXMLElement( $doc, $data );
            if ( $child )
               $doc->appendChild( $child );
            $doc->formatOutput = true; // add whitespace to make easier to read XML
            return $doc->saveXML();
        }
    }
    
    /*
     * Return a listing of /32 address in the IpSpace for the project that are marked
     * as Primary IPs
     */
    protected function getIpSpaces( $project_id ) {
        /*
        $this->loadModel('IpSpace');
        $ip_spaces = $this->IpSpace->find('list',array(
            'conditions' => array (
                'IpSpace.project_id' => $project_id,
                'cidr' => 32 // we only want /32 IPs
                ),
            // this returns the IP addresses correctly sorted -- calling
            // sort($ip_spaces) would put it in this order:
            // 192.168.88.1, 192.168.88.10, 192.168.88.2...
            'order' => array('ip_address' => 'ASC')
        ));
        */
        $this->loadModel('IpSpace');
        $conditions = array(
            'IpSpace.project_id' => $project_id,
            //'IpSpace.cidr' => 32 // we only want /32 IPs                
        );
        // the condition to limit this to /32s doesn't return a heirarchical listing
        // so we manally filter the results to /32s with the filterToHosts model function
        $ip_spaces = $this->IpSpace->filterToHosts( $this->IpSpace->generateTreeList($conditions, "{n}.IpSpace.id", "{n}.IpSpace.label", ' ',null) );
        
        
        $this->set(compact('ip_spaces'));
    }
    
    
    /*
     * Returns a data array for the SNMP configuration (format is specific to OpenNMS)
     */
    private function getSnmpConfig_opennms( $snmp_version, $snmp_community ) {
        $data = null;
        if ( (isset($snmp_version)) && (isset($snmp_community)) ) {
                $data = array(
                    'name' => 'snmp-info', // "name" required, all else optional
                    array(
                        'name' => 'community',
                        'value' => $snmp_community
                        ),
                    array(
                        'name' => 'port',
                        'value' => '161'
                        ),
                     array(
                        'name' => 'retries',
                        'value' => '1'
                        ),
                    array(
                        'name' => 'timeout',
                        'value' => '1600'
                        ),
                    array(
                        'name' => 'version',
                        'value' => $snmp_version
                        ),
               );
        }
        return $data;
    }
    
    /*
     * Return a random string of length $length (currently used for the foreignID
     * field when provisioning a node into OpenNMS)
     */
    private function generateRandomString( $length = 20 ) {    
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}
?>
