<?php

require_once 'AbstractController.php';

class Admin_UserController extends Admin_AbstractController{

    public function indexAction(){
        $this->_helper->viewRenderer('users'); 
        $this->usersAction();
    }
   
    /**
     * Update my profil
     * - update current profil
     * - update preferences
     * - update password
     */
    public function updateAction(){
        $user = new Application_Model_User();
        $mapper = new Application_Model_Mapper_User();
        
        // Load user
        if(!$mapper->find(Zend_Auth::getInstance()->getIdentity()->id, $user)){
            $this->_helper->_redirector('index', 'index', 'default');
        }
        
        $form = new Admin_Form_User_Update(); // Formulaire de modification standard
        $formPassword = new Admin_Form_User_UpdatePassword($user->getPassword(), $user->getUsername()); // Formulaire de modification de password
        $formPref = new Admin_Form_User_UpdatePref($user); // Formulaire de modification des préférences
        
        if ($this->getRequest()->isPost()){
            
            // Modification standard
            if($this->_request->getPost('triggerUpdate')){
                
                $post = $this->getRequest()->getPost();
                
                // Si le mail est different de celui d'origine alors on test si il existe pas pour un autre user
                if($post['mail'] != $user->getMail()){
                    $form->getElement('mail')->addValidator(new Zend_Validate_Db_NoRecordExists('user', 'mail'));
                }

                if($form->isValid($post)){

                    $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::SIMPLE_DATE, My_Filter_ConvertDate::MYSQL_DATE) );

                    $mapper->beginTransaction();

                    // Mise à jour de l'utilisateur
                    $newUser = clone $user;
                    $newUser->setOptions(array(
                        'mail' => $form->getValue('mail'),
                        'mailGravatar' => $form->getValue('mailGravatar'),
                        'firstname' => $form->getValue('firstname'),
                        'name' => $form->getValue('name'),
                        'birthday' => $form->getValue('birthday'),
                        'website' => $form->getValue('website'),
                        'about' => $form->getValue('about'),
                    ));

                    $mapper->save($newUser);

                    // Mise à jour des données en sessions et de l'utilisateur actuel
                    Zend_Auth::getInstance()->getStorage()->write((object)$newUser->toStorage());
                    $user = clone $newUser;

                    $mapper->commit();

                    $this->_helper->flashMessenger(array('success' => 'Modifications enregistrées !'));
                }
                else{
                    $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                    $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                }
            
            }
            // Modification préférences
            else if($this->_request->getPost('triggerUpdatePref')){
                if($formPref->isValid($this->getRequest()->getPost())){
                    
                    $mapper->beginTransaction();
                    // Mise à jour de l'utilisateur
                    $newUser = clone $user;
                    $newUser->setOptions(array(
                        'preference_authorDisplayName' => $formPref->getValue('preference_authorDisplayName'),
                    ));
                    $mapper->save($newUser);
                    // Mise à jour des données en sessions et de l'utilisateur actuel
                    Zend_Auth::getInstance()->getStorage()->write((object)$newUser->toStorage());
                    $user = clone $newUser;
                    $mapper->commit();

                    $this->_helper->flashMessenger(array('success' => 'Préférences mis à jour !'));
                }
                else{
                    $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                    $this->appendFormErrorMessages($this->_helper->flashMessenger, $formPref);
                }
            }
            // Modification password
            else if($this->_request->getPost('triggerUpdatePassword')){
                
                if($formPassword->isValid($this->getRequest()->getPost())){
                    
                    $formPassword->getElement("password")->addFilter( new My_Filter_PasswordSalt($user->getUsername()) );

                    var_dump($formPassword->getElement("oldPassword"));
                    var_dump($user->getPassword());
                    $mapper->beginTransaction();

                    // Mise à jour de l'utilisateur
                    $newUser = clone $user;
                    $newUser->setOptions(array(
                        'password' => $formPassword->getValue('password'),
                    ));

                    $mapper->save($newUser);

                    // Mise à jour des données en sessions et de l'utilisateur actuel
                    Zend_Auth::getInstance()->getStorage()->write((object)$newUser->toStorage());
                    $user = clone $newUser;

                    $mapper->commit();

                    $this->_helper->flashMessenger(array('success' => 'Mot de passe mis à jour !'));
                 }
                 else{
                     $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                     $this->appendFormErrorMessages($this->_helper->flashMessenger, $formPassword);
                 }
            }
            
        }

        // On récupère une date sql 1990-03-08 que l'on change en 03/08/1990
        $form->populate($user->toArray());
        $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::DATE, My_Filter_ConvertDate::SIMPLE_DATE) );
        
        $userRole_mapper = new Application_Model_Mapper_UserRole();
        $userRole = new Application_Model_UserRole();
        $userRole_mapper->find($user->getRoleId(), $userRole);
        $this->view->user = $user->toView();
        $this->view->user->role = $userRole->toView();
        $this->view->form = $form;
        $this->view->formPassword = $formPassword;
        $this->view->formPref = $formPref;
    }

    /**
     * Liste tous les utilisateurs 
     *
     */
    public function usersAction(){
        $mapper = new Application_Model_Mapper_User();
        $roleMapper = new Application_Model_Mapper_UserRole();
        $role = new Application_Model_UserRole();
        
        $users = $mapper->fetchAll();
        $this->view->users = array();
        foreach($users as $user){
            $roleMapper->find($user->getRoleId(), $role);
            $item = $user->toView();
            $item->role = $role->toView();
            $this->view->users[] = $item;
        }
    }

    /**
     * Supprime l'utilisateur passé en paramètre 
     * 
     * + attribution articles à un autre utilisateur
     */
    public function removeuserAction(){
        $mapper = new Application_Model_Mapper_User();
        $articleMapper = new Application_Model_Mapper_Article();
        $user = new Application_Model_User();
        
        
        $id = $this->_getParam('id', null);
                
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucun utilisateur selectioné'));
            $this->_helper->_redirector('users', 'user', 'admin');
        }
        else{

            if(!$mapper->find($id, $user)){
                $this->_helper->flashMessenger(array("error' => 'L'utilisateur demandé n'existe pas"));
                $this->_helper->_redirector('users', 'user', 'admin');
            }
            else{
                
                $form = new Admin_Form_User_RemoveOther($user->getId());
                
                if ($this->getRequest()->isPost()){

                    if($form->isValid($this->getRequest()->getPost())){
                        $mapper->beginTransaction();
                            $articleMapper->changeUser($user->getId(), $form->getValue('user')); // change article owner (user to remove give article to chosen user)
                            $mapper->remove($user->getId());
                        $mapper->commit();
                        $this->_helper->flashMessenger(array('success' => 'Utilisateur supprimé !'));
                        $this->_helper->_redirector('users', 'user', 'admin');
                    }
                    else{
                        $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                        $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                    }
                }
                
                $this->view->form = $form;
                $this->view->user = $user->toView();
            }
            
        }
    }

    /**
     * Met à jour l'utilisateur passé en paramètre 
     *
     */
    public function updateuserAction(){
        $mapper = new Application_Model_Mapper_User();
        $user = new Application_Model_User();
        $form = new Admin_Form_User_UpdateOther();
        
        $id = $this->_getParam('id', null);
                
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucun utilisateur selectioné'));
            $this->_helper->_redirector('users', 'user', 'admin');
        }
        else{

            if(!$mapper->find($id, $user)){
                $this->_helper->flashMessenger(array("error' => 'L'utilisateur demandé n'existe pas"));
                $this->_helper->_redirector('users', 'user', 'admin');
            }
            else{
             
                if ($this->getRequest()->isPost()){
 
                    $post = $this->getRequest()->getPost();

                    // Si le mail est different de celui d'origine alors on test si il existe pas pour un autre user
                    if($post['mail'] != $user->getMail()){
                        $form->getElement('mail')->addValidator(new Zend_Validate_Db_NoRecordExists('user', 'mail'));
                    }

                    if($form->isValid($post)){

                        // Conversion date bdd
                        $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::SIMPLE_DATE, My_Filter_ConvertDate::MYSQL_DATE) );

                        $mapper->beginTransaction();

                        // On récupère les options de l'user à modifier
                        $updateUser = clone $user;
                        $updateUser->setOptions(array(
                            'mail' => $form->getValue('mail'),
                            'mailGravatar' => $form->getValue('mailGravatar'),
                            'firstname' => $form->getValue('firstname'),
                            'name' => $form->getValue('name'),
                            'birthday' => $form->getValue('birthday'),
                            'website' => $form->getValue('website'),
                            'about' => $form->getValue('about'),
                            'roleId' => $form->getValue('roleId'), // acl admin
                        ));
                        $mapper->save($updateUser);

                        $user = clone $updateUser;

                        $mapper->commit();
                        $this->_helper->flashMessenger(array('success' => 'Modifications enregistrées !'));
                    }
                    else{
                        $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                        $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                    }
                }
                
                // Conversion date bdd -> code
                $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::MYSQL_DATE, My_Filter_ConvertDate::SIMPLE_DATE) );
                $form->populate($user->toArray());
                
                $this->view->form = $form;
                $this->view->user = $user->toView();
            }
        }
    }

    /**
     * Interface d'ajout d'utilisateur
     */
    public function adduserAction(){
        
        $form = new Admin_Form_User_Add();
        $mapper = new Application_Model_Mapper_User();

        if ($this->getRequest()->isPost()){

            if($form->isValid($this->getRequest()->getPost())){
                
                // Hachage mdp
                $form->getElement('password')->addFilter(new My_Filter_PasswordSalt($form->getValue('username')));
                
                // Conversion date pour format sql
                $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::FRENCH_SIMPLE_DATE, My_Filter_ConvertDate::MYSQL_DATE) );
                
                $mapper->beginTransaction();

                $user = new Application_Model_User(array(
                    'username' => $form->getValue('username'),
                    'password' => $form->getValue('password'),
                    'birthday' => $form->getValue('birthday'),
                    'roleId' => $form->getValue('roleId'),
                    'mail' => $form->getValue('mail')
                ));
                $mapper->save($user);
                $mapper->commit();
                $this->_helper->flashMessenger(array('success' => 'Utilisateur enregistré !'));
                $this->_helper->_redirector('adduser', 'user', 'admin');
            }
            else
            {
                $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
            }
        }
        
        $this->view->form = $form;
    }


}









