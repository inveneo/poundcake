<div class="row">

    <h2>Summary for <?php echo $project_name; ?></h2>
    <div class="span12">
    <table class="table table-condensed table-striped table-hover">
        <?php
//            setlocale(LC_MONETARY, 'en_US');
//            echo money_format('%i', $number) . "\n";
            foreach ($counts as $count ) {
                    echo '<tr>';
                    echo '<th>';
                    echo $count['zone_name'];
                    echo '</th>';
                    echo '<th></th><th></th><th></th>';
                    echo '</tr>';
                    echo '<tr>';
                    //echo '<dl class="dl-horizontal">';
                    // overall stuff
                    echo '<td><dl class="dl-horizontal">';
                    echo '<h4>Sites</h4>';
                    echo '<dt>Count</dt><dd>'.$count['site_count'].'</dd>';
                    echo '</dl></td>';
                    
                    // radios
                    echo '<td><dl class="dl-horizontal">';
                    echo '<h4>Radios</h4>';
                    echo '<dt>Count</dt><dd>'.$count['radio_count'].'</dd>';
                    echo '<dt>Watts</dt><dd>'.$count['radio_watts'].'</dd>';
                    echo '<dt>Value USD $</dt><dd>'.number_format($count['radio_value']).'</dd>';

                    if (isset($count['radio_counts'])) {
                         echo '<h4>By Type</h4>';
                        foreach ($count['radio_counts'] as $key => $val ) {
                            echo '<dt>'.$key.'</dt>';
                            echo '<dd>'.$val.'</dd>';
                        }
                    }
                    echo '</dl></td>';
                    
                    // routers
                    echo '<td><dl class="dl-horizontal">';
                    echo '<h4>Routers</h4>';
                    echo '<dt>Count</dt><dd>'.$count['router_count'].'</dd>';
                    echo '<dt>Watts</dt><dd>'.$count['router_watts'].'</dd>';
                    echo '<dt>Value USD $</dt><dd>'.number_format($count['router_value']).'</dd>';

                    if (isset($count['router_counts'])) {
                        echo '<h4>By Type</h4>';
                        foreach ($count['router_counts'] as $key => $val ) {
                            echo '<dt>'.$key.'</dt>';
                            echo '<dd>'.$val.'</dd>';
                        }
                    }
                    echo '</dl></td>';
                    
                    // switches
                    echo '<td><dl class="dl-horizontal">';
                    echo '<h4>Switches</h4>';
                    echo '<dt>Count</dt><dd>'.$count['switch_count'].'</dd>';
                    echo '<dt>Watts</dt><dd>'.$count['switch_watts'].'</dd>';
                    echo '<dt>Value USD $</dt><dd>'.number_format($count['switch_value']).'</dd>';

                    if (isset($count['switch_counts'])) {
                        echo '<h4>By Type</h4>';
                        foreach ($count['switch_counts'] as $key => $val ) {
                            echo '<dt>'.$key.'</dt>';
                            echo '<dd>'.$val.'</dd>';
                        }
                    }
                    echo '</dl></td>';
                 echo '</dl></tr>';
            }
        ?>
    </table>
    </div>
</div> <!-- /.row -->
