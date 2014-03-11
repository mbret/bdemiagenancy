<?php
require_once 'AbstractController.php';

class Admin_SettingController extends Admin_AbstractController
{

    public function settingsAction()
    {
        $setting = Zend_Registry::get('setting');
        $form = new Admin_Form_Settings();
        $form->populate($setting);
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                
                $settingDbTable  = new Application_Model_DbTable_Setting();
                foreach ($form->getValues() as $key => $value){
                    $settingDbTable->update(array('content' => $value),  array('setting.key LIKE ?' => (string)$key));
                    $setting[$key] = $value;
                }
                $this->_helper->flashMessenger(array('success' => 'Paramètres mis à jour !'));
            }
        }
        
        Zend_Registry::set('setting', $setting);
        $this->view->form = $form;
    }

    /**
     * Vue qui permet de manager les fichiers interne à l'application
     */
    public function filemanagerAction(){}
}




