<?php

/**
 * 
 * Converti tous les messages contenu dans flashMessenger dans un bloc jquery comprenant des objets noty
 * noty est un plugin jquery.
 * 
 */
class Application_View_Helper_FlashMessengerToNoty extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger = null;

    // Bloc jquery
    private $_template = "$(document).ready(function() { %s });";
    
    // Objet noty comprenant un message d'erreur
    private $_body = "noty({text: '%s',type: '%s',dismissQueue: false,layout: 'top',timeout: 6000});";

    public function flashMessengerToNoty(){
        
        $flashMessenger = $this->_getFlashMessenger();

        //get messages from previous requests
        $messages = array_merge(
                    $flashMessenger->getMessages(),
                    $flashMessenger->getCurrentMessages()
                );
        $flashMessenger->clearCurrentMessages();
        
        if(!empty($messages)){
            
            //initialise return string
            $output = '';

            // Boucle sur tous les messages et les retournes
            foreach ($messages as $message)
            {
                if (is_array($message)) {
                    list($key,$message) = each($message);
                }
                switch($key){
                    case 'success' :
                        $type = 'success';
                        break;
                    case 'error' :
                        $type = 'error';
                        break;
                    case 'warning' :
                        $type = 'warning';
                        break;
                    default :
                        $type = 'information';
                        break;
                }
                $output .= sprintf($this->_body,  addslashes($message),$type);
            }
            $output = sprintf($this->_template,$output);
            return $output;
        }
        return '';
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