<?php

class Admin_Form_User_UpdatePassword extends Application_Form_Abstract{

    
    protected $_oldPassword;
    protected $_username;
    
    public function __construct($oldPassword, $username, $options = null){
        $this->_oldPassword = $oldPassword;
        $this->_username = $username;
        parent::__construct($options);
    }
    
    public function init(){
        parent::init();
        
        /**
         * Ancien password
         */
        $validator = new Zend_Validate_Identical($this->_oldPassword);
        $validator->setMessage("Vous devez renseigner l'ancien mot de passe", Zend_Validate_Identical::MISSING_TOKEN);
        $validator->setMessage("Vous avez saisi un mauvais mot de passe", Zend_Validate_Identical::NOT_SAME);
        $this->addElement('password', 'oldPassword', array(
            'required' => true,
            'validators' => array(
                $validator,
            ),
            'filters' => array(
                new My_Filter_PasswordSalt($this->_username),
            )
        ));
        
        /**
         * password
         * 
         * Ajoute un validateur de mot de passe
         * @return type
         */
        $this->addElement('password', 'password', array(
            'required' => true,
            'validators' => array(
                new Zend_Validate_StringLength(array('min' => 4)),
            ),
        ));
    
        /**
         * Check password
         */
        $validator2 = new Zend_Validate_Identical(array('token' => 'password'));
        $validator2->setMessage("Les deux mot de passe ne correspondent pas", Zend_Validate_Identical::NOT_SAME);
        $this->addElement( 'password', 'checkPassword', array(
            'required' => true,
            'validators' => array(
                $validator2,
            ),
        ));
        
        
        $this->addElement('hidden', 'triggerUpdatePassword', array(
            'value' => 1
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
    }


}

