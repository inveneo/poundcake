<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->Html->link('List Sites', array('action'=>'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <div class="row">
        <h2>Confirm Import</h2>        
        <div class="span9">
        <P>Un-checked sites will not be imported.</p>
        <div class="controls span9">
            
            <form action="/sites/import_sites" id="SitesImportSitesForm" method="post">
<!--                <input type="checkbox" value="true" checked name="chk0[0]">
                <input type="checkbox" value="false" name="chk0[1]">
                <input type="checkbox" value="false" name="chk0[2]">
                <input type="checkbox" value="true" checked name="chk0[3]">

                <input type="checkbox" value="true" checked name="chk1[0]">
                <input type="checkbox" value="false" name="chk1[1]">
                <input type="checkbox" value="true" checked name="chk1[2]">
                <input type="checkbox" value="false" name="chk1[3]">-->
            <?php
                // debug( $sites );
                // echo $this->Form->create('Sites', array('action' => 'import_sites'));
                $u = 0;
                foreach( $sites as $site ) {
                    //debug( $site );
                    $site_i = implode( '|', $site['Site'] );
                    //echo $site_i;
                    $code = $site['Site']['code'];
                    $name = $site['Site']['name'];
                    $lat = sprintf( '%.5f', $site['Site']['lat'] );
                    $lon = sprintf( '%.5f', $site['Site']['lon'] );
                    echo '<label class="checkbox">';
                    echo '<input type="checkbox" value="'.$site_i.'" checked name="Site['.$u.']">';
                    echo '('.$code.') '.$name . ' at  '.$lat.', '.$lon;
                    echo '</label>';
                    //echo $this->Form->checkbox('name', array('hiddenField' => false));
                    $u++;
                }
                //echo $this->Form->submit('Import', array('div' => false,'class'=>'btn btn-primary'));
                //echo $this->Form->end();
            ?>
                <input class="btn" type="submit" value="Import"/>
                <input class="btn" type="button" name="cancel" value="Cancel" onclick="window.location='/sites/index'" />
            </form>
        </div>
    </div> <!-- ./row -->
 </div> <!-- ./span9 -->