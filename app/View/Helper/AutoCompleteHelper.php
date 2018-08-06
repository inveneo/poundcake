<?php 
/* 
 * Creates a text input field that triggers an autocomplete on keyup. 
 *  
 * http://bakery.cakephp.org/articles/matt_1/2011/08/07/yet_another_jquery_autocomplete_helper_2
 * 
 * autocompleterequestitem = attribute equal to name of variable to send in ajax request 
 *         retrieve with $this->params['url']['yourvariable'] 
 * autocompleteurl = request url to get the autocomplete terms via getJSON 
 * autocompletetext = attribute to identify the text input in jquery 
 * update = id of div to receive autocomplete terms 
 *  
 * Example: 
 *  
 * View file: 
 *             echo $this->AutoComplete->input( 
 *                'Term.name', 
 *                array( 
 *                    'autoCompleteUrl'=>$this->Html->url(  
 *                        array( 
 *                            'controller'=>'terms', 
 *                            'action'=>'auto_complete', 
 *                        ) 
 *                    ), 
 *                    'autoCompleteRequestItem'=>'autoCompleteText', 
 *                ) 
 *            ); 
 * 
 * Controller: 
 *  
 *     function auto_complete() { 
 *        $terms = $this->Term->find('all', array( 
 *            'conditions' => array( 
 *                'Term.name LIKE' => $this->params['url']['autoCompleteText'].'%' 
 *            ), 
 *            'fields' => array('name'), 
 *            'limit' => 3, 
 *            'recursive'=>-1, 
 *        )); 
 *        $terms = Set::Extract($terms,'{n}.Term.name'); 
 *        $this->set('terms', $terms); 
 *        $this->layout = 'ajax';     
 *    } 
 * 
 */ 
    class AutoCompleteHelper extends AppHelper { 
        var $helpers = array('Html','Form'); 
         
    /** 
     * Makes an input field with a helper 
     * @author cmwoody 
     * 
     */ 
        public $update='autoCompleteDiv'; 
        public $scriptPath = 'auto_complete.js'; 
        private $jsIncluded = false; 
         
        function input($name, $options) { 
            #-- Identify the div to show the results 
            $baseOptions = array( 
                'update'=>$this->update, 
                'label'=>false, 
                'autoCompleteText'=>1, 
            ); 
            $options = array_replace($baseOptions,$options); 
            $html = ''; 
             
            #-- Add the javascript 
            if(!$this->jsIncluded) { 
                $html .= $this->Html->script($this->scriptPath,array('inline'=>true)); 
            } 
            $this->jsIncluded=true; 
             
            #-- Return the html 
            $html .= $this->Form->input( $name, $options ); 
            $html .= $this->Html->div('','',array('id'=>$options['update'],'class'=>'autoCompleteDiv')); 
            //debug($options['update']); 
            return $html; 
        } 
    } 
?> 