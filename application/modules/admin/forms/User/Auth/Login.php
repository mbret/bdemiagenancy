<?php

class Admin_Form_User_Auth_Login extends Application_Form_Abstract{

    public function init(){
        
        $this->addElement( new Zend_Form_Element_Text('username', array(
            'required' => true,
        )) );
        
        $this->addElement('password', 'password', array(
            'required' => true,
        ));
       
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
    }
}

