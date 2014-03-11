<?php

/**
 * UserIdentity View Helper.
 * Regarde si une identité existe et si oui la renvoi, sinon retourne un objet null
 * 
 * @author : Maxime Bret
 */
class Application_View_Helper_UserIdentity extends Zend_View_Helper_Abstract 
{
    function userIdentity(){
        if (!Zend_Auth::getInstance()->hasIdentity()){
                return null;
        }
        return Zend_Auth::getInstance()->getIdentity();
    }
}
