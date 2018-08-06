<ul class="pull-right">  
  <div class="btn-group">
        <a class="btn btn-success" href="#"><i class="icon-user icon-white"></i>
        <?php echo $user['username'];?></a>
        <!-- button height hack -- see PC-56 -->
        <a class="btn btn-success dropdown-toggle" style="height:20px;" data-toggle="dropdown" href="#">
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><?php echo $this->Html->link('Change Password', array('controller' => 'users', 'action' => 'password', $user['id'])); ?></li>
            <li><?php echo $this->Html->link('Switch Project', array('controller' => 'users', 'action' => 'project', $user['id'])); ?></li>
            <li><?php echo $this->Html->link('Project Summary', array('controller' => '/projects', 'action' => 'summary', $this->Session->read('project_id'))); ?></li>
            <li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?></li>
        </ul>
        </div>
</ul><!--/.nav .pull-right -->
