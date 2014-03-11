<?php

class Admin_View_Helper_IsAuthContext extends Zend_View_Helper_Abstract
{
    
    public function isAuthContext(){
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        return $controllerName == 'auth';
    }
}