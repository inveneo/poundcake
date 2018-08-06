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
    echo $this->Html->script('jquery-ui-map/jquery.ui.map.overlays'); // for polyLine support
    echo $this->Html->script('poundcake/poundcake-map');
    
    //$this->Session->write( 'random', rand(5, 15) );
    
?>
<div class="row">
<div class="span12">
<h2>Topology</h2>
    <dl>
        <dt>Project</dt><dd><?php echo $this->Session->read('project_name'); ?></dd>
    </dl>
    <?php //echo $this->element('Common/disclaimer'); ?>
    
    <div class="map-frame-large">
    <div id="map_canvas" style="width:960px;height:600px"></div>
    <div id="radios" class="item gradient rounded shadow" style="margin:5px;padding:5px 5px 5px 10px;"></div>
    
        <?php
        echo $this->Form->create('google_map');
        echo $this->Form->input( 'default_lat', array( 'type' => 'hidden', 'value' => $default_lat));
        echo $this->Form->input( 'default_lon', array( 'type' => 'hidden', 'value' => $default_lon));
        echo $this->Form->input( 'fit_bounds', array( 'type' => 'hidden', 'value' => 1 ));
        
        $sitestates = array(
            0 => 'Up',
            1 => 'Partial Down',
            2 => 'Down',
            3 => 'Unknown',
        );
        
        $n = 0;
        foreach ( $sitestates as $key => $val ) {
            echo $this->Form->input( 'sitestate_'.$n, array('type'=>'hidden','value'=>$val ));
            $n++;
        }
        
        echo $this->Form->end();
        
        $u = 0;
        echo "<div style='visibility:hidden; position:absolute;'>";
        echo '<ul>';
        foreach ( $sites as $site ) {
            // var_dump($site);            
            $status = $site['is_down'];
            if (isset($status)) {
                if ( $status == 0 ) {
                    $state = $sitestates[0];
                    $icon = '/img/sites/green.png';
                } elseif ( ( $status > 0 ) && ( $status < 1 )) {
                    $state = $sitestates[1];
                    $icon = '/img/sites/yellow.png';
                } elseif ( $status == 1 ) {
                    $state = $sitestates[2];
                    $icon = '/img/sites/red.png';
                }
            }
            else {
                $state = $sitestates[3];
                $icon = '/img/sites/grey.png';
            }
//            echo "Putting icon down for ".$site['name'];
            // $state = $site['SiteState']['name'];
            $item = array( 
                'id' => 'm_'.$u,
                'icon' => $icon,
                // see this as to why this needs to be an array
                // http://stackoverflow.com/questions/9881949/filterable-jquery-ui-map-google-map
                'tags' => array( $state ),
                'latlng' => array(
                    'lat' => $site['src_lat'], // $site['Site']['lat'],
                    'lng' => $site['src_lon'], // $site['Site']['lon'],
                )
            );
            
            echo "<li data-gmapping='".json_encode($item)."'>";
            echo '<p class="info-box"><a href="/sites/view/'.$site['id'].'">'.$site['site_vf'].'</a></p><br>';
            echo "</li>"; 
            
            // this is our array of lat/long pairs that define both endpoints of a link
            $item2 = array( 
                'src_lat' => $site['src_lat'],
                'src_lon' => $site['src_lon'],
                'dest_lat' => $site['dest_lat'],
                'dest_lon' => $site['dest_lon']
            );        
            echo "<li data-gmaplines='".json_encode($item2)."'></li>";
            
            $u++;
        }
        echo '</ul></div>';
        ?>
    </div>  

</div> <!-- /.span12 -->
</div> <!-- /.row -->

<BR>

<div class="row" align="center">
    <div class="span3" align="center"> <?php echo $this->Html->image('/img/sites/green.png'); ?> All nodes up</div>
    <div class="span3" align="center"> <?php echo $this->Html->image('/img/sites/yellow.png'); ?> Some nodes down</div>
    <div class="span3" align="center"> <?php echo $this->Html->image('/img/sites/red.png'); ?> All nodes down</div>
    <div class="span3" align="center"> <?php echo $this->Html->image('/img/sites/grey.png'); ?> Node status unknown</div>
</div>
<BR><BR>
<?php
    // this refresh is just blatantly copied from
    // http://cyberschool.fateback.com/how/countdown-refresh.html
    // can probably be improved upon...
?>
<center>
Next page refresh: <span id=counter> </span>
<br>
<a href="javascript:self.location.reload()">Click</a> to refresh now
<BR>

</center>

<SCRIPT LANGUAGE="JavaScript">
<!--
var counterobj = document.all ? counter : document.getElementById("counter");

var countdownfrom = 300; // countdown period in seconds
var currentsecond = counterobj.innerHTML = countdownfrom+1; 

function countdown() {
    if (currentsecond != 1) {
        currentsecond -= 1;
        
        hours = parseInt( currentsecond / 3600 ) % 24;
        minutes = parseInt( currentsecond / 60 ) % 60;
        seconds = currentsecond % 60;
        if ( seconds < 10)
            seconds = "0" + seconds;
        counterobj.innerHTML = minutes + ":" + seconds;
        //counterobj.innerHTML = currentsecond;
        
    } else {
        self.location.reload();
        return;
    }
    setTimeout("countdown()",1000)
}

countdown()
//-->
</script>
