<?php

require_once 'AbstractController.php';

class Admin_ArticleController extends Admin_AbstractController{

    public function indexAction(){
        $this->_helper->_redirector('list', 'article', 'admin');
    }
    
    
    public function listAction(){
        $viewArticles = array();
        $user = new Application_Model_User();
        $articleMapper = new Application_Model_Mapper_Article();
        $tagMapper = new Application_Model_Mapper_Tag();
        $userMapper = new Application_Model_Mapper_User();
        
        foreach($articleMapper->fetchAllWithTrash() as $article){
            $viewArticle = $article->toView();
            $userMapper->find($article->getUserId(), $user);
            $viewArticle->author = $user->toView();
            $viewArticle->tags = array(); // set tags
            foreach ($tagMapper->fetchAllByArticle($article->getId()) as $tag) $viewArticle->tags[] = $tag->toView();
            $viewArticle->editionsDates = $articleMapper->getEditionsDates($article->getId(), 'date DESC');
            if(!empty( $viewArticle->editionsDates ) ){
                $viewArticle->lastEditionDate = $viewArticle->editionsDates[0]; 
            }
            else $viewArticle->lastEditionDate = null;
            $viewArticles[] = $viewArticle;
        }
        
        $this->view->articles = $viewArticles;
    }

    public function writeAction()
    {
        $form = new Admin_Form_Article_Write();
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $articleMapper = new Application_Model_Mapper_Article();
        
        if ($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){

                $article = new Application_Model_Article(array(
                    'title' => $form->getValue('title'),
                    'content' => $form->getValue('articleContent'),
                    'isPublished' => $form->getValue('isPublished'),
                    'categoryId' => $form->getValue('categoryId'),
                    'putForward' => $form->getValue('putForward'),
                    'forwardTitle' => $form->getValue('forwardTitle'),
                    'forwardContent' => $form->getValue('forwardContent'),
                    'userId' => $identity->id
                ));

                $articleMapper->beginTransaction();
                $articleMapper->save($article);
                // Sauvegarde des tags de l'article
                $articleMapper->saveTag($form->getElement('tags')->getValue(), $article->getId());
                
                $articleMapper->commit();
                
                $this->_helper->flashMessenger(array('success' => 'Article crée !'));
                $this->_helper->_redirector('update', 'article', 'admin', array('id' => $article->getId()));
            }
            else{
                $this->_helper->flashMessenger(array('error' => 'Article non valide !'));
                $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
            }
        }
        
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $form = new Admin_Form_Article_Update();

        $identity = Zend_Auth::getInstance()->getIdentity();
        $tagMapper = new Application_Model_Mapper_Tag();
        $mapper = new Application_Model_Mapper_Article();
        
        if($this->_hasParam('id')){
            
            // Chargement de l'utilisateur
            $article = new Application_Model_Article();

            if(!$mapper->find($this->_getParam('id'), $article)){
                $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                $this->_helper->_redirector('index', 'article', 'admin');
            }
            else{

                // Sécurisation
                if($identity->id != $article->getUserId() && !Zend_Registry::get('acl')->isAllowed($identity->role['label'], 'admin_article', 'updateOther')){
                    $this->_helper->_redirector('index', 'article', 'admin');
                }
                else{
                    
                    if($article->getInTrash()){
                        $this->_helper->flashMessenger(array('warning' => "L'article demandé est dans la corbeille, restaurez le d'abord pour l'éditer !"));
                        $this->_helper->_redirector('index', 'article', 'admin');
                    }
                    else{
                        /**
                         * Formulaire de modification 
                         */
                        if ($this->getRequest()->isPost()){
                            if($form->isValid($this->getRequest()->getPost())){

                                $mapper->beginTransaction();

                                $newArticle = clone $article;
                                $newArticle->setOptions(array(
                                    'title' => $form->getValue('title'),
                                    'content' => $form->getValue('articleContent'),
                                    'isPublished' => $form->getValue('isPublished'),
                                    'categoryId' => $form->getValue('categoryId'),
                                    'putForward' => $form->getValue('putForward'),
                                    'forwardTitle' => $form->getValue('forwardTitle'),
                                    'forwardContent' => $form->getValue('forwardContent'),
                                ));
  
                                $mapper->save($newArticle);
                                
                                // Sauvegarde des tags de l'article + récup des id de tags inseré
                                $mapper->saveTag($form->getValue('tags'), $article->getId());

                                // Sauvegarde l'edition de l'article
                                $mapper->saveEdition($article->getId(), $identity->id);
                                var_dump($newArticle);
                                $mapper->commit();
                                $article = clone $newArticle;
                                $this->_helper->flashMessenger(array('success' => 'Article mis à jour !'));
                            }
                            else{
                                $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                                $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                            }
                        }
                        
                        // Chargement de ses tags
                        $articleTag = $tagMapper->fetchAllByArticle($article->getId());
                        
                        $populate = $article->toArray();
                        $populate['articleContent'] = $populate['content'];
                        
                        $form->populate($populate);
                        // Construction de la population de tags
                        $tags = array();
                        foreach($articleTag as $tag){
                            $tags['tags'][] = $tag->getId();
                        }
                        $form->populate($tags);
                        $this->view->form = $form;
                        $this->view->article = $article->toView();
                    }
                }
            }
        }
        else{
            $this->_helper->_redirector('index', 'article', 'admin');
        }
    }

    public function removeAction(){
        $articleMapper = new Application_Model_Mapper_Article();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $article = new Application_Model_Article();
        
        $id = $this->_getParam('id', null);
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucun article selectioné'));
            $this->_helper->_redirector('list', 'article', 'admin');
        }
        else{
            if(!$articleMapper->find($id, $article)){
                $this->_helper->flashMessenger(array('error' => "L'article demandé n'existe pas"));
                $this->_helper->_redirector('list', 'article', 'admin');
            }
            else{
                if(  ($identity->id != $article->getUserId() && !$this->view->isAllowed('admin_article', 'removeOther'))     // pas auteur et pas le droit de suppression
                      || ( $identity->id === $article->getUserId() && !$this->view->isAllowed('admin_article', 'remove'))){    // OU auteur mais pas le droit de suppression                                                                           
                        $this->_helper->flashMessenger(array('error' => "Vous n'êtes pas autorisé à faire ça"));
                        $this->_helper->_redirector('list', 'article', 'admin');
                }
                else{
                
                    $articleMapper->beginTransaction();
                    $articleMapper->putInTrash($id);
                    $articleMapper->commit();
                    $this->_helper->flashMessenger(array('success' => 'Article mis à la corbeille !'));
                    $this->_helper->_redirector('list', 'article', 'admin');
                }
            }
        }
    }
    
    /**
     * 
     */
    public function restoreAction(){
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        if($this->_hasParam('id') || $this->_hasParam('ids')){
            
            $mapper = new Application_Model_Mapper_Article();
            $article = new Application_Model_Article();
            $ids = array();
            if($this->_hasParam('id')){
                $ids[] = $this->_getParam('id');
            }
            else{
                foreach($this->_getParam('ids') as $id){
                    $ids[] = $id;
                }
            }
            
            $db = $this->getInvokeArg( 'bootstrap' )->getPluginResource('db')->getDbAdapter();
            $db->beginTransaction();
            
            $rows = array();
            $format = '';
            foreach($ids as $id){
                $mapper->find($id, $article);
                
                // Si ce n'est pas l'auteur et qu'il n'a pas les autorisation suffisante
                if($identity->id != $article->getUserId() && !Zend_Registry::get('acl')->isAllowed($identity->role['label'], 'admin_article', 'restoreOther')){
                    $this->_helper->_redirector('list', 'article', 'admin');
                }
                
                $result = $mapper->restore($id);
                if($result != 0){
                    $rows[] = $id;
                    $format = $format . ' #' . $id;
                }
            }
            
            if(empty($rows)){
                if($this->_hasParam('ids')){
                    $this->_helper->flashMessenger(array('warning' => "Les articles demandés n'existent pas !"));
                }
                else{
                    $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                }
            }
            else{
                $this->_helper->flashMessenger(array('success' => 'Article(s)' . $format . ' restauré(s) !'));
            }
            
            $db->commit();
        }
        
        $this->_helper->_redirector('list', 'article', 'admin');
    }
}



