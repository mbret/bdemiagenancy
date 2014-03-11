<?php
/**
 * Necessite les lignes suivantes de configuration dans application.ini
 * "moduleName".resources.layout.layout = layout
 * "moduleName".resources.layout.layoutPath = APPLICATION_PATH "/modules/"moduleName"/layouts/scripts/" 
 * "moduleOtherName".resources.layout.layout = layout
 * "moduleOtherName".resources.layout.layoutPath = APPLICATION_PATH "/modules/"moduleOtherName"/layouts/scripts/" 
 * ...
 */
class My_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract{

    public function preDispatch()
    {
        // Récupération du bootstrap
        $bootstrap = $this->getActionController()
                          ->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();

        $module = $this->getRequest()->getModuleName();
        if (isset($config[$module]['resources']['layout']['layout'])
                && isset($config[$module]['resources']['layout']['layoutPath'])) {
            $layoutScript = $config[$module]['resources']['layout']['layout'];
            $layoutPath = $config[$module]['resources']['layout']['layoutPath'];

            $this->getActionController()
                 ->getHelper('layout')
                 ->setLayoutPath($layoutPath)
                 ->setLayout($layoutScript);
                    
        }
    }
    
    public function resetLayout(){
        // Récupération du bootstrap
        $bootstrap = $this->getActionController()
                          ->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();

        $module = $this->getRequest()->getModuleName();
        if (isset($config[$module]['resources']['layout']['layout'])
                && isset($config[$module]['resources']['layout']['layoutPath'])) {
            $layoutScript = $config[$module]['resources']['layout']['layout'];
            $layoutPath = $config[$module]['resources']['layout']['layoutPath'];

            $this->getActionController()
                 ->getHelper('layout')
                 ->enableLayout()
                 ->setLayoutPath($layoutPath)
                 ->setLayout($layoutScript);
                    
        }
    }
    
}