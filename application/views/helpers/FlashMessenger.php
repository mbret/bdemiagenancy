<?php

class Application_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    
    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger = null;

    /**
     * Contain all messages containing inside flashmessenger helper
     * @var type 
     */
    private $_messages;
    
    /**
     * 
     * @param boolean toString return the first occurence of messages to string
     * @return array array of messages or messages from given key
     */
    public function flashMessenger( $specificKey = null , $toString = false, $toJS = false)
    {
        return $this->getContent($specificKey, $toString, $toJS);
    }
    
    public function getContent( $specificKey = null , $toString = false, $toJS = false)
    {
        
        $return = null;
        
        $this->loadMessages();
        
        if( empty($this->_messages) ){
            return ($toString) ? '' : array();
        }
        
        // Specific key
        if( isset($specificKey) ){
            if( isset($this->_messages[$specificKey])){
                $return = ($toString) ? $this->_messages[$specificKey][0] : $this->_messages[$specificKey];
            }
            else{
                $return = ($toString) ? '' : array();
            }
        }
        // entire messages
        else{
            $return = ($toString) ? $this->_messages[0][0] : $this->_messages;
        }
        
        return ( $toJS ) ? $this->addSlashes( $return ) : $return;
    }
    
    private function loadMessages(){
        if( ! isset($this->_messages) ){
            $flashMessenger = $this->_getFlashMessenger();

            //get messages from previous requests
            $messages = $flashMessenger->getMessages();

            // add any messages from this request (everything before postDispatch)
            if ($flashMessenger->hasCurrentMessages()) {
                $messages = array_merge( $messages, $flashMessenger->getCurrentMessages() );

                //we don't need to display them twice.
                $flashMessenger->clearCurrentMessages();
            }

            // Cleas all meessages
            $flashMessenger->clearMessages();

            /**
             *     0 => 
             *       array (size=1)
             *         'alertPublic' => string 'sdfsd fsdf' (length=10)
             *     1 => 
             *       array (size=1)
             *         'warning' => string 'Aucun article !' (length=15)
             *     2 => 
             *       array (size=1)
             *         'warning' => string 'warning 2'
             * 
             *          INTO
             * 
             *    'alertPublic' => 
             *       array (size=1)
             *         0 => string 'sdfsd fsdf' (length=10)
             *     'warning' => 
             *       array (size=2)
             *         0 => string 'Aucun article !' (length=15)
             *         1 => string 'warning 2' (length=9)
             */
            $this->_messages = array();
            if(isset($messages[0])){
                foreach ($messages as $submessage){
                    foreach ($submessage as $key => $value) {
                        if( ! isset( $this->_messages[$key] )){
                            $this->_messages[$key] = array($value);
                        }
                        else{
                            $this->_messages[$key][] = $value;
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 
     * @param mixed $entry
     */
    private function addSlashes( $entry ){
        if( is_string($entry) ){
            return addslashes ( $entry );
        }
        if( is_array($entry) ){
            return array_map( "addSlashes", $entry );
        }
        return $entry;
    }

    public function hasLabel( $label ){
        $this->loadMessages();
        if( isset( $this->_messages[$label] ) ){
            return true;
        }
        return false;
    }
    
    /**
     * Lazily fetches FlashMessenger Instance.
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function _getFlashMessenger()
    {
        if (null === $this->_flashMessenger) {
            $this->_flashMessenger =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'FlashMessenger');
        }
        return $this->_flashMessenger;
    }
}