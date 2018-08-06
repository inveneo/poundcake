<?php
    // jQuery to enable/disable permissions of project is un/checked
    echo $this->Html->script('poundcake/poundcake-project-acl');
?>


<div class="row">
<div class="span3">
    <H3>Actions</H3>
    <div class="well">
    <ul>
        <li><?php echo $this->PoundcakeHTML->link('List Users', array('action' => 'index')); ?></li>
    </ul>
    </div>
</div><!-- /.span3 .sb-fixed -->

<div class="span9">
    <?php
        // echo $this->Form->create('User');
        echo $this->Form->create('ProjectMembership');
    ?>
    <h2>Permissions</h2>
    <?php
        // var_dump( $assigned_projects );
        // var_dump( $this->Form->data );
    
        // echo "Username:  ". $this->Form->data['User']['username']; // $this->Form->value('username'); //,array('disabled' => true));
        echo "<P><B>Username</B>:  ". $username.'</P>';
        
//        echo $this->Form->input('User.role_id', array('type'=>'select','options' => $roles));
//        echo $this->Form->input('Project',array(
//            'label' => 'Projects',
//            'type' => 'select',
//            'multiple' => 'checkbox',
//            'options' => $projects,
//            'selected' => $this->Html->value('Project.Project'),
//        ));

////        echo $this->Form->input( 'Project.project_id', array( 'multiple'=>'checkbox', 'label' => 'Projects' ));
////        echo $this->Form->input('User.user_id',array( 'type' => 'hidden', 'value' => $user['id']));
////        echo $this->Form->input('ProjectMembership.role_id',array( 'type' => 'hidden', 'value' => 2 ));
//        
//        //echo $this->Form->input( 'Project.project_id', array( 'multiple'=>'checkbox', 'label' => 'Projects' ));
        // echo $this->Form->input( 'ProjectMembership.project_id',array('options' => $projects, 'label'=>'Employee', 'multiple' => 'checkbox'));        
        
        //echo $this->Form->input( 'Project.project_id', array( 'value' => $projects, 'multiple'=>'checkbox', 'label' => 'Projects' ));
        
        // echo $this->Form->input( 'user_id' ,array( 'type' => 'hidden', 'value' => $this->Form->data['User']['id'] ));
        echo $this->Form->input( 'user_id' ,array( 'type' => 'hidden', 'value' => $id ));
        
        echo "<table class=\"table table-condensed table-striped\">";
        echo "<tr><th>Project Access</th>";
        echo "<th colspan=\"2\">Permission</th>";
        echo "</tr>";
        
        $i = 0;
        foreach ( $projects as $p ) {
            echo "<tr><td>";
            
            // the ID of the record in the ProjectMembership table -- we need this
            // so that we can clear out projects (with delete) before saving or
            // re-saving the updated data
            //echo $this->Form->input( 'ProjectMembership.'.$i.'.id' ,array( 'type' => 'hidden', 'value' => $p['Project']['id'] ));
            
            
            
            // the checkboxes for the project -- we manually verify if the user
            // is already assigned one of the projects by comparing it against
            // the assigned_projects array, and if so mark the box as checked
            // as I understand it, we have to do this manually because of the
            // hasMany through (The Join Model) relation -- HABTM would otherwise
            // take care of this for us
            echo '<label class="checkbox">';
            $name = "ProjectMembership[$i][project_id]";
            echo '<input type="checkbox" class="checkbox" name="'.$name.'" value="'.$p['Project']['id'].'"';
            
            $role = 0;
            $project_is_checked = false;
            foreach ( $assigned_projects as $ap ) {
//                echo "Looking for Project ID of ".$p['Project']['id'];
//                var_dump( $ap );
                if ( $p['Project']['id'] == $ap['project_id'] ) {
                // if( in_array( $p['Project']['id'], $ap ) ) {
                    echo ' checked';
                    $role = $ap['role_id'];
                    $project_is_checked = true;
                }
            }
            echo '>' . $p['Project']['name'];
            echo '</label>';
            
            echo "</td><td>";
            
            echo '<label class="radio">';
            echo '<input type="radio" name="ProjectMembership['.$i.'][role_id]" id="edit" value="'.$roles[1]['Role']['id'].'" ';
            if ( $role == $roles[1]['Role']['id'] ) {
                echo " checked";
            }
            if ( !$project_is_checked ) {
                echo " disabled";
            }

            echo ">";
            echo $roles[1]['Role']['name'];
            echo '</label>';
            
            echo "</td><td>";
            
            //echo '<label class="radio" name="'.$name.'-radio">';
            echo '<label class="radio">';
            echo '<input type="radio" name="ProjectMembership['.$i.'][role_id]" id="view" value="'.$roles[2]['Role']['id'].'" ';
            if ( $role == $roles[2]['Role']['id'] || $role == 0 ) {
                echo " checked";
            }
            if ( !$project_is_checked ) {
                echo " disabled";
            }
            echo ">";
            echo $roles[2]['Role']['name'];
            echo '</label>';
            
            echo "</td></tr>";
            $i++;
        }
        echo "</table>";
        
        
        /*
        $i = 0;
        foreach ( $projects as $key => $value ) {            
            echo '<label class="checkbox">';
            echo '<input type="checkbox" class="checkbox" name="project_id['.$i.']" value="'.$key.'"';
            if( in_array( $key, $assigned_projects) ) {
                echo ' checked';
            }
            echo '>' . $value;
            echo '</label>';
            $i++;
        }
        
        echo $this->Form->input( 'ProjectMembership.user_id',array( 'type' => 'hidden', 'value' => $id ));
        echo $this->Form->input( 'Role.role_id',array( 'type' => 'hidden', 'value' => 2 ));
        */
        
        //echo $this->Form->input( 'Role', array( 'multiple'=>'checkbox' ));
        echo $this->Form->submit( 'Save', array( 'div' => false,'class'=>'btn btn-primary' ));
        echo $this->Form->end(); 
    ?>
</div> <!-- /.span9 -->
</div> <!-- /.row -->