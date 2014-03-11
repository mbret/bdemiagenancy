<?php

class Admin_Form_Settings extends Application_Form_Abstract
{

    public function init(){
       
        
        /**
         * Alert admin
         * - 500 char max
         * - not required
         */
        $this->addElement('textarea', 'alertAdmin', array(
            'required'   => false,
        ));
 
        /**
         * Alert public
         * - 500 char max
         * - not required
         */
        $this->addElement('textarea', 'alertPublic', array(
            'required'   => false,
        ));
        
        /**
         * nb Articles forwarded
         * - digit
         * - between 1 and 10
         * - not required
         */
        $this->addElement('text', 'nbArticlesForward', array(
            'required'   => false,
            'validators' => array(
                new Zend_Validate_Digits(),
                new Zend_Validate_Between(array('min' => 1, 'max' => 10)),
            ),
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
        
    }


}

