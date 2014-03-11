<?php

class Admin_AbstractController extends Zend_Controller_Action
{
    
    public function postDispatch(){
        parent::postDispatch();
        
        $this->view->messages = $this->retrieveAndFormatMessages($this->_helper->flashMessenger);
    }
    
    /**
     * Retrieve and format in an array the messages contained in Flashmessenger
     * 
     * @param Zend_Controller_Action_Helper_FlashMessenger $flashMessenger
     * @return array
     */
    public function retrieveAndFormatMessages(Zend_Controller_Action_Helper_FlashMessenger $flashMessenger){
        // get messages from previous requests
        $messages = $flashMessenger->getMessages();
        
        // add any messages from this request (everything before postDispatch)
        if ($flashMessenger->hasCurrentMessages()) {
            $messages = array_merge( $messages, $flashMessenger->getCurrentMessages() );
            
            //we don't need to display them twice.
            $flashMessenger->clearCurrentMessages();
        }

        $flashMessenger->clearMessages();
        
        $newArray = array();
        /**
         * this variable contain messages from previous request and current message
         * array
         *   0 => 
         *     array
         *       'error' => string 'Article non valide !'
         *     ...
         *   1 => 
         *     array
         *       'warning' => ...
         *   ...
         */
        if(isset($messages[0])){
            foreach ($messages as $index){
                foreach ($index as $key => $value) {
                    if( ! isset($newArray[$key])){
                        $newArray[$key] = array($value);
                    }
                    else{
                        $newArray[$key][] = $value;
                    }
                }
            }
        }
        return $newArray;
    }
    
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

