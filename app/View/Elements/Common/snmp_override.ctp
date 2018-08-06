<?php
    // View snipped used on NetworkSwitch, NetworkRouter, NetworkRadio
    // add/edit pages -- is for allowing the user to override project-level
    // SNMP data

    // fields default to disabled
    $disabled = true;
    
    // but if we're on an edit page
    if ( $this->params['action'] === 'edit' ) {
        // search for the snmp_override field and if it's > 0 then it's set
        // and the fields should be editable
        foreach ($this->data as $key => $val) {
           if ( isset($val['snmp_override']) && ( $val['snmp_override'] > 0 ) ) {
               $disabled = false;
           }
        }
    }
    
    // checkbox to override SNMP information for this item's project
    // making this a class of snmp is used by jQuery, not CSS
    echo $this->Form->input('snmp_override', array('label'=>'Specify SNMP Setup','class' => 'snmp'));
    
    // snmp_override will default to being un-checked, so we can disable this field by default
    echo $this->Form->input('snmp_type_id', array(
        'type'=>'select',
        'options' => $snmptypes,
        'label' => 'SNMP Version',
        'empty' => true,
        'disabled' => $disabled
    ));
    
    echo $this->Form->input('snmp_community_name', array(
        'label' => 'SNMP Community Name',
        'disabled' => $disabled,
        'class' => 'snmp_override'
    ));
?>
