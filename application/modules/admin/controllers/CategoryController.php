<?php

require_once 'AbstractController.php';

class Admin_CategoryController extends Admin_AbstractController{

    /**
     * Page d'index des tags
     *  - affiche un formulaire d'insertion
     *  - affiche la liste des tags
     */
    public function indexAction(){
        
        $mapper = new Application_Model_Mapper_Category();
        $form = new Admin_Form_Category_Add();
        $this->view->form = $form;
        $this->view->categories = array();
        
        // Formulaire
        if ($this->_request->isPost()){
            
            // Ajout
            if($this->_request->getPost('add')){
                
                if($form->isValid($this->_request->getPost())){
                
                    $mapper->beginTransaction();
                        $data = $form->getValues();
                        $category = new Application_Model_Category(array(
                            'title' => $data['title'],
                            'label' => $data['label'],
                            'description' => $data['description'],
                            'parentId' => ($data['parentId'] == -1) ? null : $data['parentId'],
                        ));
                        $mapper->save($category);
                    $mapper->commit();

                    $this->_helper->flashMessenger(array('success' => '#' . $category->getLabel() . ' ajouté !'));
                    $this->_helper->_redirector('index', 'category', 'admin');
                }
                else{
                    $this->_helper->flashMessenger(array('error' => 'Catégorie non valide !'));
                    $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                }
                
            }
        }
        
        $categories = $mapper->fetchAll();
        foreach($categories as $category){
            $cat = $category->toView();
            $cat->parentLabel = $mapper->getLabelById( $category->getParentId() );
            $this->view->categories[] = $cat;
        }
    }

    public function updateAction(){
        $category_mapper = new Application_Model_Mapper_Category();
        $category = new Application_Model_Category();
        
        $id = $this->_getParam('id', null);
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucune catégorie selectionée'));
            $this->_helper->_redirector('index', 'category', 'admin');
        }
        else{
            if(!$category_mapper->find($id, $category)){
                $this->_helper->flashMessenger(array('error' => 'La catégorie demandée n\'existe pas'));
                $this->_helper->_redirector('index', 'category', 'admin');
            }
            else{
                $form = new Admin_Form_Category_Update($category->getTitle(), $category->getId());
                
                // Contrôle formulaire
                if ($this->getRequest()->isPost()){
                    if($form->isValid($this->getRequest()->getPost())){

                        $category_mapper->beginTransaction();

                            // On met à jour seulement les données voulu
                            $data = $form->getValues();
                            $categoryTemp = clone $category;
                            $categoryTemp->setOptions(array(
                                'title' => $data['title'],
                                'description' => $data['description'],
                                'parentId' => ($data['parentId'] == -1) ? null : $data['parentId'],
                            ));
                            $category_mapper->save($categoryTemp);

                        $category_mapper->commit();

                        $this->_helper->flashMessenger(array('success' => 'Catégorie modifiée'));
                        $this->_helper->_redirector('index', 'category', 'admin');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array('error' => 'Formulaire non valide !'));
                        $this->appendFormErrorMessages($this->_helper->flashMessenger, $form);
                    }
                }
                
                $form->populate($category->toArray());
                $this->view->form = $form;
                $this->view->category = $category->toView();
            }
        }
    }
    
    public function removeAction(){
        $article_mapper = new Application_Model_Mapper_Article();
        $category_mapper = new Application_Model_Mapper_Category();
        $category = new Application_Model_Category();
        $defaultCategory = Application_Model_Mapper_Category::getDefaultCategory();
        
        $id = $this->_getParam('id', null);
        // Mauvais paramètre
        if(!is_numeric($id)){
            $this->_helper->flashMessenger(array('error' => 'Aucune catégorie selectionée'));
            $this->_helper->_redirector('index', 'category', 'admin');
        }
        else{
            // Mauvaise catégorie
            if(!$category_mapper->find($id, $category)){
                $this->_helper->flashMessenger(array('error' => 'La catégorie demandée n\'existe pas'));
                $this->_helper->_redirector('index', 'category', 'admin');
            }
            else{
                // Suppréssion categorie par defaut
                if($defaultCategory->getId() == $category->getId()){
                    $this->_helper->flashMessenger(array('error' => 'La catégorie par défaut ne peut pas être supprimé'));
                    $this->_helper->_redirector('index', 'category', 'admin');
                }
                else{
                    $category_mapper->beginTransaction();
                        $article_mapper->changeCategory($category->getId(), Application_Model_Mapper_Category::getDefaultCategory()->getId());
                        $category_mapper->remove($category->getId());
                    $category_mapper->commit();
                    $this->_helper->flashMessenger(array('success' => 'Catégorie supprimée'));
                    $this->_helper->_redirector('index', 'category', 'admin');
                }
                
            }
        }
    }
}



