<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('New Site State Icon', array('action' => 'add')); ?></li>
        <li><?php echo $this->PoundcakeHTML->link('Setup',array('controller'=>'admin','action' => 'setup')); ?></li>
    </ul>
    </div>
    
    <P>
        Get icons from <a href="http://mapicons.nicolasmollet.com/markers/restaurants-bars/wi-fi/" target="_blank">here</a>.    
    </p>
    <P>
        Generally "wi-fi unsecured" - first row, 3rd icon over.  Pick a color then <I>Save As</I>.
    </P>
    
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Site State Icons</h2>
	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="index-item">Icon</th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($siteStateIcons as $siteStateIcon): ?>
	<tr>
            <td class="index-item">
                <?php
                    echo '<img src="data:'.$siteStateIcon['SiteStateIcon']['img_type'].';base64,'.base64_encode( $siteStateIcon['SiteStateIcon']['img_data'] ) . '" />';
                ?>
            </td>
            <td class="index-action">
            <?php
                echo $this->PoundcakeHTML->link('Edit', array('action' => 'edit', $siteStateIcon['SiteStateIcon']['id']));
                echo '&nbsp;';
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                            array('controller'=>'siteStateIcons','action'=>'delete', $siteStateIcon['SiteStateIcon']['id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of icon'),
                            null
                        );
            ?>
            </td>
	</tr>
        <?php endforeach; ?>
            </tbody>
        </table>

	
	<?php
            // include pagination
            // file is in ./View/Elements/Common/pagination.ctp
            echo $this->element('Common/pagination');
        ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->