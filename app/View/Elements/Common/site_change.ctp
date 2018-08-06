<?php
    //$this->Js->get('#NetworkRadioSiteId')->event('change', $this->Js->alert('changed'));    
    $this->Js->get('#NetworkRadioSiteId');    
    $this->Js->event('change',
        $this->Js->request(
                array(
                    'controller' => 'networkSwitches',
                    'action' => 'getSwitchForSite'
                ),
                array(
                    'async' => true,
                    'update' => '#NetworkRadioSwitchPort',
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
