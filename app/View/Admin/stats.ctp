<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Stats</h2>
        
        <P>
            I can haz random Tower DB statistics!
        </P>
                    
            <div class="row">
            <div class="span4">
                <dl class="dl-horizontal">
                    <h4>Totals</h4>
                    <hr>

                    <dt>Projects</dt>
                    <dd><?php echo $project_count; ?></dt>

                    <dt>Sites</dt>
                    <dd><?php echo $site_count; ?></dt>

                    <dt>Radios</dt>
                    <dd><?php echo $radio_count; ?></dt>

                    <dt>Routers</dt>
                    <dd><?php echo $router_count; ?></dt>

                    <dt>Switches</dt>
                    <dd><?php echo $switch_count; ?></dt>

                    <dt>Users</dt>
                    <dd><?php echo $user_count; ?></dt>
                </dl>
            </div>
                
            <div class="span4"> 
                
                <dl class="dl-horizontal">
                    <h4>Errata</h4>
                    <hr>

                    <dt>Avg. Radios per Site</dt>
                    <dd><?php echo sprintf("%.2f",$avg_radio_count); ?></dt>

                    <dt>Most Radios at a Site</dt>
                    <dd><?php echo $max_radio_count; ?></dt>

                    <dt>No. Multipoint Radios</dt>
                    <dd><?php echo $mp_radio_count; ?></dt>

                    <dt>Most Recent Login</dt>
                    <dd><?php echo $last_logged_in_user; ?></dt>

                    <dt>Number of Releases</dt>
                    <dd><?php echo $release_count; ?></dt>  
                        
                    <dt>Last Code Update</dt>
                    <dd><?php echo date_format(new DateTime($last_update), 'l, F jS, Y'); ?></dt>      
                </dl>
                
            </div>
            </div> <!-- ./ row -->
            
            <div class="row">
                <div class="span4">
                    <dl class="dl-horizontal">
                        <h4>Radio Counts, by Type</h4>
                        <hr>

                        <?php foreach ($radio_types as $key => $val): ?>
                        <dt><?php echo $key;?></dt>
                        <dd><?php echo $val; ?></dd>
                        <?php endforeach; ?>
                    </dl>

                </div>

                <div class="span4">
                    <dl class="dl-horizontal">
                        <h4>Antenna Counts, by Type</h4>
                        <hr>

                        <?php foreach ($antenna_types as $key => $val): ?>
                        <dt><?php echo $key;?></dt>
                        <dd><?php echo $val; ?></dd>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </div> <!-- ./ row -->
            
            <div class="row">
                <div class="span4">
                    <dl class="dl-horizontal">
                        <h4>Power Type Counts</h4>
                        <hr>

                        <?php foreach ($power_types as $key => $val): ?>
                        <dt><?php echo $key;?></dt>
                        <dd><?php echo $val; ?></dd>
                        <?php endforeach; ?>
                    </dl>

                </div>
            </div> <!-- ./ row -->
            
            <div class="row">
                <div class="span9">
                    <dl class="dl-horizontal">
                        <h4>Site Name Distribution, by Letter</h4>
                        <hr>
                        <?php foreach ($distribution as $key => $val):
                                $percent = ( $val / $site_count ) * 100;
                                //echo $percent."<BR>";
                            ?>
                            <dt><?php echo strtoupper($key);?></dt>
                            <dd>
                                <div class="progress progress-info">
                                    <div class="bar" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </dd>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </div> <!-- ./ row -->
        
</div> <!-- /.span9 -->
</div> <!-- /.row -->