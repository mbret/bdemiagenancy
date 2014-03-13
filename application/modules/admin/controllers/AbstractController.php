<?php

class Admin_AbstractController extends Zend_Controller_Action
{
    
    
    /**
     * Retrieve error message in the given form and put them into flashmessenger
     * @param Zend_Controller_Action_Helper_FlashMessenger $flashMessenger
     * @param type $form
     */
    public function appendFormErrorMessages(Zend_Controller_Action_Helper_FlashMessenger $flashMessenger, $form){
        foreach ( $form->getMessages() as $key => $element){
            foreach ( $element as $error){
                $flashMessenger->addMessage(array('error' => '('.$key.') ' . $error) );
            }
        }
    }

}

