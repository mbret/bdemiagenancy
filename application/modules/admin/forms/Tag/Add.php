<?php

class Admin_Form_Tag_Add extends Application_Form_Abstract{

    public function init(){
        
        parent::init();
        
        $this->addElement('text', 'label', array(
            'required' => true,
            'validators' => array(
                new Zend_Validate_Db_NoRecordExists('tag', 'label')
            )
        ));
//        $this->getElement('label')->addValidator( new Zend_Validate_Db_NoRecordExists('tag', 'label') );
        
        $this->addElement('textarea', 'description', array(
            'required' => false,
        )); 
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'value' => "submit",
        ));
        
    }


    
}



