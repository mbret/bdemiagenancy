<?php

class Admin_Form_User_UpdateOther extends Application_Form_Abstract{

    public function init(){
                
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

        /**
         * Username
         * - disabled
         */
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

        $mapper = new Application_Model_Mapper_UserRole();
        $options = array();
        foreach($mapper->fetchAllToArray() as $value){
            $options[$value['id']] = $value['name'];
        }
        $this->addElement( new Zend_Form_Element_Select('roleId', array(
            'MultiOptions' => $options,
            'validators' => array(new Zend_Validate_Db_RecordExists('user_role', 'id')),
        )) );
        
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
    }


}

