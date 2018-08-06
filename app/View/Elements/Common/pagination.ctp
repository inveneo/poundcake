<?php
    // Paging Element for Twitter Bootstrap and CakePHP 2.0+
    // 
    // this pagination solution taken from:
    // https://gist.github.com/2137554


    /* Does not work!
    $this->Paginator->options(array('url' => $this->Paginator->data ));
    */
?>

<?php $span = isset($span) ? $span : 8; ?>
<?php $page = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1; ?>

<div class="pagination pagination-centered">
    <ul>
    <?php echo $this->Paginator->prev(
        '&larr; ' . __('Previous'),
        array(
            'escape' => false,
            'tag' => 'li',
            //'url' =>  $this->Paginator->data // <-- Does not work!
        ),
        '<a onclick="return false;">&larr; Previous</a>',
        array(
                'class'=>'disabled prev',
                'escape' => false,
                'tag' => 'li'
        )
    );?>

    <?php $count = $page + $span; ?>
    <?php $i = $page - $span; ?>
    <?php while ($i < $count): ?>
            <?php $options = ''; ?>
            <?php if ($i == $page): ?>
                    <?php $options = ' class="active"'; ?>
            <?php endif; ?>
            <?php if ($this->Paginator->hasPage($i) && $i > 0): ?>
                    <li<?php echo $options; ?>><?php echo $this->Html->link($i, array("page" => $i)); ?></li>
            <?php endif; ?>
            <?php $i += 1; ?>
    <?php endwhile; ?>

    <?php echo $this->Paginator->next(
        // title
        'Next' . ' &rarr;',
        // options
        array(
            'escape' => false,
            'tag' => 'li',
            //'url' =>  $this->Paginator->data // <-- Does not work!
        ),
        // disabled title
        '<a onclick="return false;">Next &rarr;</a>',
        // disabled options
        array(
                'class' => 'disabled next',
                'escape' => false,
                'tag' => 'li'
        )
    );?>
    </ul>
</div><!-- /.pagination .pagination-centered -->
<div align="center">
    <?php
        echo $this->Paginator->counter(array(
            'format' => 'Page {:page} of {:pages}<BR>{:count} records total'
        ));
    ?>
</div>



