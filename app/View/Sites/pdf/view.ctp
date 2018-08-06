<head>
<style type="text/css">
    hr {color:sienna;}
    h1 { font-family:"Arial Black", Gadget, sans-serif; }
    h2 { font-family:"Arial Black", Gadget, sans-serif; }
    h3 { font-family:"Arial Black", Gadget, sans-serif; }
    
    ul li {
        margin-left:20px;
        font-family:"Courier New", Courier, monospace
    }
</style>
</head>
    
<?php
    // https://groups.google.com/forum/?fromgroups=#!topic/dompdf/oZ3lNpYG2hs
    //echo $this->Html->image("inveneo.gif");
?>

<h1>
<?php echo $site['Site']['site_vf']; ?>
</h1>
<hr>

<h3>
RADIOS
</h3>
<?php
    //print_r($radio_counts);
    $la = 0; // keep track of the number of lightening arrestors
    echo '<UL>';
    foreach ($radio_counts as $key => $value) {
        echo '<LI>';
        //echo $key . " - " . print_r($value);
        echo '('.$radio_counts[$key][0]['count'].')  ';
        echo $radio_counts[$key]['radio_types']['name'];
        //echo $radios[$key]['radio_types'][0]['count'];
        echo '</LI>';

        $la += $radio_counts[$key][0]['count'];
    }
    echo '</UL>';
?>

<h3>
LIGHTENING ARRESTORS
</h3>
    <UL><LI>
    <?php echo '('.$la.')'; ?> Lightening arrestors
    </LI></UL>

<h3>
ANTENNAS
</h3>
<?php
    //print_r($radio_counts);
    echo '<UL>';
    foreach ($antenna_counts as $key => $value) {
        echo '<LI>';
        //echo $key . " - " . print_r($value);
        echo '('.$antenna_counts[$key][0]['count'].')  ';
        echo $antenna_counts[$key]['antenna_types']['name'];
        //echo $radios[$key]['radio_types'][0]['count'];
        echo '</LI>';
    }
    echo '</UL>';
?>
<h3>

BOARD
</h3>
<UL>
    <LI><?php echo '('.$board['quantity'].')&nbsp;'.$board['name']; ?>
</UL>

<h3>
OTHER
</h3>
<?php
    $type = '';
    echo '<UL>';
    foreach ($builditems as $key => $value) {

        if ($type != $builditems[$key]['BuildItemType']['name']) {
            $type = $builditems[$key]['BuildItemType']['name'];
            echo '<H4>'.$type.'</H4>';
        }
        echo '<LI>';
        echo $builditems[$key]['BuildItem']['quantity'];
        echo '&nbsp;';
        echo $builditems[$key]['BuildItem']['name'];
        echo '&nbsp;';
        echo '</LI>';
    }
    echo '</UL>';
?>