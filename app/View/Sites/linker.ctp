<?php
    // JQuery for the datepicker
    echo $this->Html->script('poundcake/poundcake-datepicker');
    // disable mouse scroll in the lat/lon fields
    echo $this->Html->script('poundcake/poundcake-scroll');
?>

<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Sites', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php echo $this->Form->create('Site'); ?>
    <h3>Site Linker</h3>
    <P>
        The Site linker is a basic way to quickly link 2 sites.  What it does:
    <UL>
        <LI>Create and attach two radios:
            <UL>
                <LI>Radio attached to Site 1, named: site1-site2</LI>
                <LI>Radio attached to Site 2, named: site2-site1</LI>
            </UL>
        </LI>
        <LI>Set the SSID of both radios: site1-site2</LI>
        <LI>Set the radio type</LI>
        <LI>Set the antenna type</LI>
        <LI>Set the radio mode</LI>
    </UL>
    </P>
    <hr>
    <div class="span9">
    <div class="span4">
       <?php
            echo $this->Form->label('Site 1');
            echo $this->Form->input('site1-id',array(
                'legend' => false,
                'type' => 'radio',
                'options' => $sites,
            ));
        ?>
        <BR><BR>
    </div> <!-- ./span4 -->
    <div class="span1"></div>
    <div class="span4">
        <?php
            echo $this->Form->label('Site 2');
            echo $this->Form->input('site2-id',array(
                'legend' => false,
                'type' => 'radio',
                'options' => $sites,
            ));
        ?>
    </div> <!-- ./span4 -->
    </div> <!-- ./span9 -->

    <?php
        
        echo $this->Form->submit('Link', array('div' => false,'class'=>'btn btn-primary'));
        echo $this->Form->end(); 
    ?>
</div> <!-- ./span9 -->

</div> <!-- ./row -->
