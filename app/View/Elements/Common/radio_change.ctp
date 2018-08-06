<?php
    //$this->Js->get('#NetworkRadio0RadioTypeId')->event('change', $this->Js->alert('changed'));

    // update the list of available frequencies when the user picks a radio from
    // the drop down of radio types
    $this->Js->get('#NetworkRadioRadioTypeId');    
    $this->Js->event('change',
        $this->Js->request(
                array(
                    'controller' => 'radioTypes',
                    'action' => 'getFrequenciesForRadioType'
                ),
                array(
                    'async' => true,
                    'update' => '#NetworkRadioFrequency',
                    'dataExpression' => true,
                    'data'=> $this->Js->serializeForm (
                            array(
                                'isForm' => true,
                                'inline'=> true
                                )
                            ),
                    'method' => 'post'
                    )
                )
    );
    
    // as above, but update the list of available antenna types
    $this->Js->get('#NetworkRadioRadioTypeId');    
    $this->Js->event('change',
        $this->Js->request(
                array(
                    'controller' => 'radioTypes',
                    'action' => 'getAntennasForRadioType'
                ),
                array(
                    'async' => true,
                    'update' => '#NetworkRadioAntennaTypeId',
                    'dataExpression' => true,
                    'data'=> $this->Js->serializeForm (
                            array(
                                'isForm' => true,
                                'inline'=> true
                                )
                            ),
                    'method' => 'post'
                    )
                )
    );
    
    echo $this->Js->writeBuffer(); // Write cached scripts
?>
