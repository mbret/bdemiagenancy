<?php

require_once 'AbstractController.php';

/**
 * Class qui gère l'authentification et l'inscription du site
 * 
 * @author Maxime Bret
 */
class Admin_AuthController extends Admin_AbstractController
{

    public function preDispatch(){
        parent::preDispatch();
        
        // Error page on auth controller must be public for guest (we redirect in front section)
        $errorHandler = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $errorHandler->setErrorHandlerModule('default');
    }

    /**
     * Page de login unique à tous le site.
     * Stockage persistant de l'utilisateur connecté grâce à Zend_Auth
     * 
     * @author Maxime Bret
     * @version 1.0 (03-10-2012)
     * @todo traiter et vérifier la previousUrl
     */
    public function loginAction(){
        
        $auth = Zend_Auth::getInstance();
        
        $previousUrl = $this->getRequest()->getParam('previousurl', $this->view->url(array(), 'home'));
        if(substr($previousUrl, 0, 9) === "/bdemiage" ){
            $previousUrl = substr($previousUrl, 9); // on enleve le /bdemiage du debut
        }

        // On test si l'utilisateur est deja logé
        if($auth->hasIdentity()){
            $this->_helper->flashMessenger(array('success' => 'Vous êtes déjà connecté !'));
            $this->_helper->_redirector->gotoUrl($previousUrl);
        }
        else{
            
            $form = new Admin_Form_User_Auth_Login();
            
            $request = $this->getRequest();
            if ($request->isPost()){
                
                // si une valeur est absente, les autres validateurs ne seront pas interrogés
                if($form->isValid($request->getPost())){
                    // salt perso
                    $form->getElement('password')->addFilter(new My_Filter_PasswordSalt($form->getValue('username')));

                    $authAdapter = new Zend_Auth_Adapter_DbTable(
                            $this->getInvokeArg( 'bootstrap' )->getPluginResource('db')->getDbAdapter()
                    );
                    $authAdapter->setTableName('user')
                                ->setIdentityColumn('username')
                                ->setCredentialColumn('password');
                    $authAdapter->setIdentity($form->getValue('username'))
                                ->setCredential($form->getValue('password'));

                    // Réalise la requête d'authentification et sauvegarde le résultat
                    $result = $auth->authenticate($authAdapter);

                    // Test l'identité de l'utilisateur
                    if (!$result->isValid()){
                        // Echec de l'authentification ; afficher pourquoi
                        foreach ($result->getMessages() as $message) {
                            switch($message){
                                case 'A record with the supplied identity could not be found.' : 
                                    $message = "Aucun compte associé à ce nom d'utilisateur.";
                                    break;
                                case 'Supplied credential is invalid.' : 
                                    $message = "Mauvaise combinaison.";
                                    break;
                                default :  break;
                            }
                            $this->_helper->flashMessenger(array('error'=> $message));
                            
                        }
                        
                    }else{
                        
                        // Authentification réussie 
                        // l'identité ($identifiant) est stockée dans la session
                        $user = new Application_Model_User((array)$authAdapter->getResultRowObject());
                        $auth->getStorage()->write($user->toStorage());

                        // On supprime le message enregistré par le plugin qui nous dit de nous connecter pour continuer
                        // car si la connexion réussi il s'affichera encore une fois
                        $this->_helper->flashMessenger()->clearCurrentMessages();
                        $this->_helper->flashMessenger(array('success'=>'Connexion  réussi !'));
                        
                        $this->_helper->_redirector->gotoUrl($previousUrl);
                    }
                }
                else{
                    $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                }
                
            }

            $this->view->form = $form;
        }
    }
    
    /**
     * Page de logout
     * Supprime l'utilisateur en session
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $auth->clearIdentity();
            $this->_helper->flashMessenger(array('success' => 'Déconnexion réussi !'));
        }
        $this->_helper->_redirector('index', 'index', 'default');
    }

    
    
    /**
     * Page de login
     * Stockage persistant de l'utilisateur inscris grâce à Zend_Auth
     * 
     * @author Maxime Bret
     * @version 1.0 (03-10-2012)
     */
    public function subscribeAction(){
        $form = new Admin_Form_User_Auth_Subscribe();
        $mapper = new Application_Model_Mapper_User();
        $roleMapper = new Application_Model_Mapper_UserRole();
        
        $request = $this->getRequest();
        if ($request->isPost()){
            
            // si une valeur est absente, les autres validateurs ne seront pas interrogés
            if($form->isValid($request->getPost())){

                $mapper->beginTransaction();
                
                // Application du salt personalisé
                $form->getElement('password')->addFilter(new My_Filter_PasswordSalt($form->getValue('username')));
                // Conversion date pour format sql
                $form->getElement('birthday')->addFilter( new My_Filter_ConvertDate(My_Filter_ConvertDate::SIMPLE_DATE, My_Filter_ConvertDate::MYSQL_DATE) );
                
                // Création d'un objet complet
                $user = new Application_Model_User(array(
                    'username' => $form->getValue('username'),
                    'password' => $form->getValue('password'),
                    'mail' => $form->getValue('mail'),
                    'birthday' => $form->getValue('birthday'),
                    'roleId' => $roleMapper->getDefaultRole()->getId(),
                    ));
                $mapper->save($user);
                
                // Mise à jour des données en sessions
                $auth = Zend_Auth::getInstance();
                $auth->getStorage()->write($user->toStorage());

                $mapper->commit();
                
                $this->_helper->flashMessenger(array('success' => "Inscription réussi ! Vous êtes ici sur la page de configuration de votre profil, n'hésitez pas à le completer"));
                $this->_helper->_redirector('update', 'user', 'admin');
                
            }
            else{
                $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
            }
        }
        $this->view->form = $form;
        
                
    }

}

