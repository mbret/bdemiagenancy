<?php

class Default_Form_Contact extends Application_Form_Abstract
{
    
    public function init(){
        
        $this->addElement('text', 'name', array(
            'required' => true,
        ));
        
        $this->addElement('text', 'email', array(
            'required' => true,
            'validators' => array(
                'EmailAddress',
            ),
        ));
        
        
        $this->addElement('text', 'subject', array(
            'required' => false,
            'value' => '',
            'rows' => 5,
        ));
        
        
        // Main content
        $this->addElement('textarea', 'content', array(
            'required' => true,
        ));
        
        
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

