<?php
/**
 * Plugin qui récupère les paramètre de configuration de l'application en bdd et qui initialise certains elements
 */
class My_Controller_Plugin_Setting extends Zend_Controller_Plugin_Abstract{
    
    protected $_setting;
    
    /**
     * Récupère la configuration de l'application et la met dans le registre
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request){
        $application = new Application_Model_DbTable_Setting();
        $results = $application->fetchAll();
        foreach($results as $result){
            $this->_setting[$result['key']] =  $result['content'];
        }
        Zend_Registry::set('setting', $this->_setting);
        
        // Définit le bon module pour le error handler
        if('admin' == $request->getModuleName()){
            $errorHandler = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ErrorHandler');
            $errorHandler->setErrorHandlerModule('admin');
        }
        
        /**
         * Get special alert setting messages and put in flashmessenger
         */
        $setting = Zend_Registry::get('setting');
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        if(isset($setting['alertAdmin']) && '' != $setting['alertAdmin'] && 'admin' == $request->getModuleName()){
            $flashMessenger->addMessage(array('alertAdmin'=> $setting['alertAdmin']));
        }
        
        if(isset($setting['alertPublic']) && '' != $setting['alertPublic'] && 'default' == $request->getModuleName()){
            $flashMessenger->addMessage(array('alertPublic'=> $setting['alertPublic']));
        }
    }
    
}
