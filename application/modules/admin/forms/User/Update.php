<?php

class Admin_Form_User_Update extends Application_Form_Abstract{

    public function init(){
        parent::init();
        
        $this->addElement('hidden', 'triggerUpdate', array(
            'value' => 1
        ));
        
        $this->addElement( new Zend_Form_Element_Text('firstname', array(
                'required' => false,
                'filters'    => array('StringTrim'),
        )) );
        
        $this->addElement( new Zend_Form_Element_Text('name', array(
            'required' => false,
            'filters'    => array('StringTrim'),
        )) );
        
        $this->addElement( new Zend_Form_Element_Text('birthday', array(
            'required' => true,
            'validators' => array(new Zend_Validate_Date(My_Filter_ConvertDate::FRENCH_SIMPLE_DATE)),
        )) );

        $this->addElement( new Zend_Form_Element_Text('username', array(
            'required' => false,
        )) );

        $this->addElement( new Zend_Form_Element_Text('mail', array(
            'required' => true,
            'validators' => array( 'EmailAddress' ),
        )) );
        
        $this->addElement( new Zend_Form_Element_Text('mailGravatar', array(
            'validators' => array('EmailAddress'),
        )) );
        
        $this->addElement( new Zend_Form_Element_Text('website', array(
            'required' => false,
        )) );
        
        $this->addElement( new Zend_Form_Element_Textarea('about', array(
            'required' => false,
        )) );
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
    }


}

