<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('List Contacts', array('action' => 'index')); ?>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <h2>View Contact</h2>
    <dl class="dl-horizontal">
    <dt>Name</dt><dd><?php echo $contact['Contact']['name_vf'] ? : 'Unknown'; ?></dd>
    <dt>Title</dt><dd><?php echo $contact['Contact']['title'] ? : 'Unknown'; ?></dd>
    <dt>Number</dt><dd><?php echo $contact['Contact']['phone'] ? : 'Unknown'; ?></dd>
    <dt>Skype</dt><dd><?php echo $contact['Contact']['skype'] ? : 'Unknown'; ?></dd>
    <dt>Email</dt><dd><?php echo $contact['Contact']['email'] ? : 'Unknown'; ?></dd>
    <dt>Priority</dt><dd><?php echo $contact['Contact']['priority'] ? : 'Unknown'; ?></dd>
    <dt>Organization</dt><dd><?php echo $contact['Organization']['name'] ? : 'Unknown'; ?></dd>
    <dt>Contact Type</dt><dd><?php echo $contact['ContactType']['name'] ? : 'Unknown'; ?></dd>
    </dl>
    
</div> <!-- /.span9 -->
</div> <!-- /.row -->
