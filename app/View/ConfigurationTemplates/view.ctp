<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Configuration Templates', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Configuration Template</h2>
    <dl>
    <dt>Name</dt>
    <dd><?php echo $configuration_template['ConfigurationTemplate']['name']; ?></dd>
    <dt>Contacts</dt>
        <?php
            //echo print_r($configuration_template);
            //echo "<pre>";
            //echo print_r($configuration_template);
            //echo "</pre>";
            $c = count($configuration_template['Contact']);
            //echo "c is".$c;
            if ($c == 0) {
                echo "None";
            } else {
                foreach ($configuration_template['Contact'] as $contact) {
                    echo "<dd>";
                    echo $this->Html->link($contact['name_vf'], array(
                        'controller' => 'contacts',
                        'action' => 'view',
                        $contact['id']));
                    echo "</dd>";
                }
            }
        ?>
    </dl>
</div> <!-- /.span9 -->
</div> <!-- /.row -->

