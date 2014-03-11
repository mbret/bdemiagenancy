<?php
/**
 * Return the application config wich is initialized in bootstrap
 */
class Application_View_Helper_GetConfig extends Zend_View_Helper_Abstract{
    
    public function getConfig(){
        $front = Zend_Controller_Front::getInstance();
        return $front->getParam('bootstrap')->getResource('config');
    }
    
}
