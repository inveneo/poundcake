<table>
<?php

    $n = 0;
    foreach ( $networkInterfaceTypes as $networkInterfaceType ) {
        $checked = false;
        $number = 0;
        
        // true if we're on the edit page
        if ( isset($existing_network_interface_types) ) {
            foreach( $existing_network_interface_types as $e ) {
                if ( $networkInterfaceType['NetworkInterfaceType']['id'] == $e['network_interface_type_id'] ) {
                    $checked = true;
                    $number = $e['number'];
                }
            }
        }
//        echo '<pre>';print_r($existing_network_interface_types);echo'</pre>';
        // strtolower because Cake will capitalize the first letter,
        // and in this case we don't want eth to be Eth
        // strtolower doesn't seem t work on the first argument
        $id = $networkInterfaceType['NetworkInterfaceType']['id'];
        $label = strtolower( $networkInterfaceType['NetworkInterfaceType']['name'] );
        echo '<tr><td>';
        echo $this->Form->input(
                $modelClass.'NetworkInterfaceTypes.'.$id.'][network_interface_type_id]',
                array(
                    'value' => $id,
                    'label' => $label,
                    'type' => 'checkbox',
                    'class' => 'networkinterfacetype',
                    'checked' => $checked
                )
        );
        echo '</td><td>';
        
        // disable the number field unless the box is checked
        $disabled = false;
        if ( $number == 0 ) { 
            $disabled = true;
        }
        
        echo $this->Form->input( $modelClass.'NetworkInterfaceTypes.'.$id.'][number]',
                array(
                    'label' => '',
                    'value' => $number,
                    'style' => 'width:30px;',
                    'class' => 'networkinterfacetype_number',
                    'disabled' => $disabled
            )
        );        
        echo '</td></tr>';
        
        $n++;        
    }
 ?>
</table>