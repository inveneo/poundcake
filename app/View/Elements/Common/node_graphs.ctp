<div class="span9">
    <h2>View Graphs: <?php echo $name; ?> </h2>
    <?php
        foreach ( $performance_graphs as $graph ) {

            if ( $graph[0] != null ) {
                $title = $graph[1];
                echo "<h4>$title</h4>";

                $base64   = base64_encode( $graph[0] ); 
                echo "<img class=\"img-polaroid\" src=\"";
                echo 'data:image/png;base64,' . $base64;
                echo "\"><BR><BR>";
            }
        }
    ?>
</div>