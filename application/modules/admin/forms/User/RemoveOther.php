<?php

class Admin_Form_User_RemoveOther extends Application_Form_Abstract{

    private $_userId;
    
    public function __construct($userId, $options = null){
        $this->_userId = $userId;
        parent::__construct($options);
    }
    
    public function init(){
        parent::init();

        $mapper = new Application_Model_Mapper_User();
        $options = array();
        foreach($mapper->fetchAll() as $user){
            $options[$user->getId()] = $user->getUsername();
        }
        unset($options[$this->_userId]); // remove same user
        $this->addElement('select', 'user', array(
            'MultiOptions' => $options,
            'label' => 'Attribuer les articles à',
        ));
        
        $this->addElement('checkbox', 'valid', array(
            'required' => true,
            'uncheckedValue' => null,
        ));

        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => "Supprimer l'utilisateur",
            'value' => "Supprimer l'utilisateur",
        ));
    }


}

