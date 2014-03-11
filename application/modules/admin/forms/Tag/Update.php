<?php

class Admin_Form_Tag_Update extends Application_Form_Abstract{

    public function init(){
        
        parent::init();
        
        $this->addElement('text', 'label', array(
            'required' => true,
        )); 
        
        $this->addElement('textarea', 'description', array(
            'required' => false,
        )); 
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'value' => "submit",
        ));
        
    }


    
}



