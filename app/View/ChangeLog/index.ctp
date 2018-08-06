<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->linkIfAllowed('New Change', array('action' => 'add')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
	<h2>Changes</h2>
        <p>
        <h4>What is in a version?</h4>
        <p>Tower DB uses <a href="http://semver.org/">semantic versioning</a>, e.g. (Major).(Minor).(Patch)</p>

        <ul>
        <li>A <b>major version</b> change is a release that introduces several significant new pieces of functionality.  For example, the 2.0.0 release of Tower DB added support for Point-to-Multipoint radios.</li>
        <li>A <b>minor version</b> change is a release with roughly 10 minor changes, generally at least one new feature and possibly some bug fixes.</li>
        <li>A change in the <b>patch number</b> is a release with fewer than 10 changes, almost entirely bug fixes.</li>
        </ul>
        <p>We use JIRA to track issues.  If you think you found a bug, please create an issue by emailing towerdb@inveneo.org.  If you need access to JIRA, please contact towerdb-access@inveneo.org.
        </p>

	<table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: auto;"><?php echo $this->Paginator->sort('version'); ?></th>
                    <th style="width: auto;" nowrap><?php echo $this->Paginator->sort('release_date'); ?></th>
                    <th class="index-item"><?php echo $this->Paginator->sort('descrption','Release Notes'); ?></th>
                    <th class="index-action"><?php echo 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
	<?php
	foreach ($changeLogs as $change): ?>
	<tr>
            <td><?php echo $change['ChangeLog']['version'];?></td>
            <td nowrap>
            <?php
                $date = new DateTime($change['ChangeLog']['release_date']);
                echo $date->format('Y-m-d');
                ?>
            </td>
            <td nowrap>
                <?php
//                    $max = 80;
//                    $desc = strip_tags(substr($change['ChangeLog']['description'], 0, $max));
//                    if (strlen($change['ChangeLog']['description']) > $max-3 ) {
//                        $desc .= "...";
//                    }
//                    echo $this->Html->link($desc, array('action' => 'view', $change['ChangeLog']['id']));
                    $label = "Release Notes for ".$change['ChangeLog']['version'];
                    echo $this->Html->link( $label, array('action' => 'view', $change['ChangeLog']['id']));
                ?>
            </td>
            <td class="index-action">
            <?php
                echo $this->PoundcakeHTML->linkIfAdmin('Edit', array('action' => 'edit', $change['ChangeLog']['id']));
                echo '&nbsp;';
                echo $this->PoundcakeHTML->postLinkIfAllowed('Delete',
                            array('controller'=>'changelog','action'=>'delete', $change['ChangeLog']['id']),
                            array('method' => 'post','class'=>'confirm','data-dialog_msg'=>'Confirm delete of '.$change['ChangeLog']['version']),
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
            echo $this->element('Common/pagination');
        ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->