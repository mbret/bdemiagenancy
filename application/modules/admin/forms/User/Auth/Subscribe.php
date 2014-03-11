<?php

class Admin_Form_User_Auth_Subscribe extends Application_Form_Abstract{

    public function init(){
        
        
        $this->addElement( new Zend_Form_Element_Text('birthday', array(
            'required' => true,
            'validators' => array(new Zend_Validate_Date(My_Filter_ConvertDate::FRENCH_SIMPLE_DATE)),
        )));
        
        $this->addElement( new Zend_Form_Element_Text('username', array(
            'required' => true,
            'validators' => array(
                new Zend_Validate_Db_NoRecordExists('user', 'username'), 
                new My_Validate_Pseudo()
            )
        )) );
        
         $this->addElement( new Zend_Form_Element_Password('password', array(
            'required' => true,
            'validators' => array(
                new Zend_Validate_StringLength(array('min' => 4))
            ),
        )) );
        
        $validator = new Zend_Validate_Identical(array('token' => 'password'));
        $validator->setMessage("Les deux mot de passe ne correspondent pas", Zend_Validate_Identical::NOT_SAME);
        $this->addElement( new Zend_Form_Element_Password('checkPassword', array(
            'required' => true,
            'validators' => array(
                $validator
            )
        )) );
        
        $this->addElement( new Zend_Form_Element_Text('mail', array(
            'required' => true,
            'validators' => array(
                'EmailAddress',
                new Zend_Validate_Db_NoRecordExists('user', 'mail')
            )
        )));
        
        $recaptchaKeys = Zend_Registry::get('config')->settings->reCaptcha;
        $recaptcha = new Zend_Service_ReCaptcha($recaptchaKeys->publicKey, $recaptchaKeys->privateKey, NULL, array('theme' => 'clean'));
        $this->addElement('captcha','captcha', array(
            'captcha' => array(
                'captcha'       => 'ReCaptcha',
                'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                'ignore' => true,
                'pubkey' => $recaptchaKeys->publicKey,
                'privkey' => $recaptchaKeys->privateKey
            ),
        ));
        $this->getElement('captcha')->removeDecorator( 'label' )
                                    ->removeDecorator( 'description' )
                                    ->removeDecorator( 'errors' )
                                    ->getDecorator( 'HtmlTag' )->setOption( 'tag' , 'div');
        
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
        ));
        
    }

    
}



