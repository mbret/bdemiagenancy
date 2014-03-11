<?php
/**
 * @source : http://www.wowww.ch/2008/11/20/zend-framework-gestion-des-acces-avec-zend-auth-et-zend-acl/ 
 * 
 * @todo : Faire en sorte que la verification acl se fasse apres la verification de l'existance du controller , action etc
 */
class My_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    // Dois être identifié
    const FAIL_AUTH_MODULE = 'admin';
    const FAIL_AUTH_CONTROLLER = 'auth';
    const FAIL_AUTH_ACTION = 'login';
    
    // Mauvais privilèges (redirection interne vers l'action noallowed du module courant)
    const FAIL_ACL_CONTROLLER = 'error';
    const FAIL_ACL_ACTION = 'noallowed'; 
                
    protected $_auth;
    protected $_acl;
    
    //avant que le contrôleur d’action soit appelé mais après le routage de la requête
    public function preDispatch(Zend_Controller_Request_Abstract $request){
        
        $this->_acl = new My_Acl();
        $this->_auth = Zend_Auth::getInstance();
    
        // is the user authenticated   
        if ($this->_auth->hasIdentity()){ 
            // yes ! we get his role     
            $user = $this->_auth->getStorage()->read();   
            $role = $user->role['label'];   
        }
        else{
            // no = guest user     
            $role = 'guest';   
        }

        $module = $request->getModuleName();   
        $controller = $request->getControllerName();   
        $action = $request->getActionName();   
        $front = Zend_Controller_Front::getInstance();   
        $default = $front->getDefaultModule(); 
        
        // compose le nom de la ressource   
        if ($module == $default){
            $resource = $controller ;   
        }
        else{
            $resource = $module.'_'.$controller ;
        }

        // est-ce que la ressource existe ?   
        if (!$this->_acl->has($resource)) {     
            $resource = null;
        }

        // contrôle si l'utilisateur est autorisé. Pas de control si la resource n'est pas reconnu (erreur 404)
        if (!is_null($resource) && !$this->_acl->isAllowed($role, $resource, $action)){
            
            // l'utilisateur n'est pas autorisé à accéder à cette ressource     
            // on va le rediriger  
            if (!$this->_auth->hasIdentity()){
                
                $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
                $flashMessenger->addMessage(array('warning' => 'Vous devez vous connecter pour continuer !'));

                // il n'est pas identifié -> module de login 
                $module  = self::FAIL_AUTH_MODULE ;       
                $controller = self::FAIL_AUTH_CONTROLLER ;
                $action  = self::FAIL_AUTH_ACTION ;   
            }
            else {

                // il est identifié -> error de privilèges       
                $module = $module;
                $controller = self::FAIL_ACL_CONTROLLER;       
                $action = self::FAIL_ACL_ACTION;
            }
        }

        
        $request->setModuleName($module) ;   
        $request->setControllerName($controller) ;  
        $request->setActionName($action) ; 

    } 
    
}
