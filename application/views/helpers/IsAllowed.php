<?php
class Application_View_Helper_IsAllowed extends Zend_View_Helper_Abstract 
{ 
   public function isAllowed($resource = null, $privilege = null) 
   { 
       $acl = Zend_Registry::get('acl');
       $auth = Zend_Auth::getInstance(); 
       $role = ($auth->hasIdentity()) ? $auth->getIdentity()->role['label'] : 'guest'; 
       return $acl->isAllowed($role, $resource, $privilege); 
   } 
} 
