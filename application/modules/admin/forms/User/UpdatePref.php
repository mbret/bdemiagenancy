<?php

class Admin_Form_User_UpdatePref extends Application_Form_Abstract{

    protected $_user;
    
    public function __construct(Application_Model_User $user, $options = null){
        $this->_user = $user;
        parent::__construct($options);
    }
    
    public function init(){
        parent::init();
        
        $options = array(
            0 => 'Pseudo',
            1 => 'Nom',
            2 => 'Prénom',
            3 => 'Nom Prénom',
            4 => 'Prénom Nom'
        );
        $this->addElement( new Zend_Form_Element_Select('preference_authorDisplayName', array(
            'MultiOptions' => $options,
            'value' => $this->_user->getPreference_authorDisplayName(),
            'validators' => array( new Zend_Validate_InArray(array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4)) ),
        )));

        $this->addElement('hidden', 'triggerUpdatePref', array(
            'value' => 1
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
    }


}

