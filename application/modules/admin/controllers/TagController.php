<?php

require_once 'AbstractController.php';

class Admin_TagController extends Admin_AbstractController{
   

    /**
     * Page d'index des tags
     *  - affiche un formulaire d'insertion
     *  - affiche la liste des tags
     */
    public function indexAction(){
        
        $mapper = new Application_Model_Mapper_Tag();
        $form = new Admin_Form_Tag_Add();
        $this->view->form = $form;
        $this->view->tags = array();
        
        // FORMULAIRE D'AJOUT
        $request = $this->getRequest();
        if ($request->isPost()){
            if($form->isValid($request->getPost())){
                
                $mapper->beginTransaction();
                
                $tag = new Application_Model_Tag($form->getValues());
                $mapper->save($tag);
                
                $mapper->commit();
                
                $this->_helper->flashMessenger(array('success' => '#' . $tag->getLabel() . ' ajouté !'));
                $this->_helper->_redirector('index', 'tag', 'admin');
            }
            else{
                $this->_helper->flashMessenger(array('error' => 'Tag non valide !'));
                $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
            }
        }
        
        // CHARGEMENT DES TAGS POUR LA VUE
        $tags = $mapper->fetchAll();
        foreach($tags as $tag){
            $this->view->tags[] = $tag->toView();
        }
    }

    public function updateAction(){
        $mapper = new Application_Model_Mapper_Tag();
        $tag = new Application_Model_Tag();
        $form = new Admin_Form_Tag_Update();
                
        $id = $this->_getParam('id', null);
        
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucun tag selectioné'));
            $this->_helper->_redirector('index', 'tag', 'admin');
        }
        else{
            if(!$mapper->find($id, $tag)){
                $this->_helper->flashMessenger(array('error' => 'Le tag demandé n\'existe pas'));
                $this->_helper->_redirector('index', 'tag', 'admin');
            }
            else{
                
                if ($this->getRequest()->isPost()){
                    $post = $this->getRequest()->getPost();
                    
                    // L'utilisateur souhaite modifier le label
                    if($post['label'] != $tag->getLabel()){
                        // On ajoute le validateur qui vérifie que le label n'est pas déjà pris
                        $form->getElement('label')->addValidator( new Zend_Validate_Db_NoRecordExists('tag', 'label') );
                    }
                    
                    if($form->isValid($post)){
                        
                        $mapper->beginTransaction();

                        // On met à jour seulement les données voulu
                        $updateTag = clone $tag;
                        $updateTag->setOptions(array(
                            'label' => $form->getValue('label'),
                            'description' => $form->getValue('description'),));
                        $mapper->save($updateTag);
                        $tag = clone $updateTag;
                        
                        $mapper->commit();
                        $this->_helper->flashMessenger(array('success' => $tag->getLabel() . ' mis à jour !'));
                        $this->_helper->_redirector('index', 'tag', 'admin');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                        $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                    }
                }
                
                $form->populate($tag->toArray());
                $this->view->form = $form;
                $this->view->tag = $tag->toView();
            }
        }
    }
    
    public function removeAction(){
        $tag = new Application_Model_Tag();
        $tagMapper = new Application_Model_Mapper_Tag();
        
        $id = $this->_getParam('id', null);
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucun tag selectioné'));
            $this->_helper->_redirector('index', 'tag', 'admin');
        }
        else{

            if(!$tagMapper->find($id, $tag)){
                $this->_helper->flashMessenger(array("error' => 'Le tag demandé n'existe pas"));
                $this->_helper->_redirector('index', 'tag', 'admin');
            }
            else{
                
                $tagMapper->beginTransaction();
                $tagMapper->remove($tag->getId());
                $tagMapper->commit();
                
                $this->_helper->flashMessenger(array('success' => 'Tag supprimé !'));
                $this->_helper->_redirector('index', 'tag', 'admin');
            }
        }
    }
}



