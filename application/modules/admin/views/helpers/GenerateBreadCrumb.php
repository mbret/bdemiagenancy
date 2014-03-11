<?php

/**
 * View helper which generate html admin breacrumb
 */
class Admin_View_Helper_GenerateBreadCrumb extends Zend_View_Helper_Abstract
{

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
    
    public function generateBreadCrumb(){
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        ?>
        <div>
            <hr>
                <ul class="breadcrumb">
                    <li><a href="<?php echo $this->view->url(array(), 'admin'); ?>">Accueil</a></li>
        <?php
        // Construit le breadcrumb de base
        // Permet de garder un breadcrumb propre en cas de re passage à cause d'une erreur (error handler) qui rappel les plugins
        switch($controllerName){
            case 'index' :
                switch($actionName){
                    default:
                        break;
                }
                break;
            
            case 'category':
                ?>
                <li><span class="divider">/</span><a href="<?php echo $this->view->url(array('action'=>'index', 'controller'=>'category', 'module'=>'admin'), null, true); ?>">Catégories</a></li>
                <?php
                switch($actionName){
                    case 'update':
                        ?>
                        <li><span class="divider">/</span>Modification</li>
                        <?php
                        break;
                    default:
                        break;
                }
                break;
            
            case 'user':
                ?>
                <li><span class="divider">/</span><a href="<?php echo $this->view->url(array('action'=>'users', 'controller'=>'user', 'module'=>'admin'), null, true); ?>">Utilisateurs</a></li>
                <?php
                switch($actionName){
                    case 'users':
                        ?>
                        <li><span class="divider">/</span>Liste</li>
                        <?php
                        break;
                    case 'removeuser':
                        ?>
                        <li><span class="divider">/</span>Suppression d'un utilisateur</li>
                        <?php
                        break;
                    case 'adduser':
                        ?>
                        <li><span class="divider">/</span>Ajouter un utilisateur</li>
                        <?php
                        break;
                    case 'update':
                        ?>
                        <li><span class="divider">/</span>Mon profil</li>
                        <?php
                        break;
                    case 'updateuser':
                        ?>
                        <li><span class="divider">/</span>Modifier un utilisateur</li>
                        <?php
                        break;
                    default:
                        break;
                }
                break;
            
            case 'tag':
                ?>
                <li><span class="divider">/</span><a href="<?php echo $this->view->url(array('action'=>'index', 'controller'=>'tag', 'module'=>'admin'), null, true); ?>">Mots-clefs</a></li>
                <?php
                switch($actionName){
                    case 'index':
                        ?>
                        <li><span class="divider">/</span>Gestion</li>
                        <?php
                        break;
                    case 'update':
                        ?>
                        <li><span class="divider">/</span>Modifier un mot-clef</li>
                        <?php
                        break;
                }
                break;
            case 'article':
                ?>
                <li>
                    <span class="divider">/</span>
                    <a href="////<?php echo $this->view->url(array('action'=>'list', 'controller'=>'article', 'module'=>'admin'), null, true); ?>">Articles</a>
                </li>
                <?php
                switch($actionName){
                    case 'list':
                        ?>
                        <li><span class="divider">/</span>Liste des articles</li>
                        <?php
                        break;
                    case 'update':
                        ?>
                        <li><span class="divider">/</span>Modifier un article</li>
                        <?php
                        break;
                    case 'write':
                        ?>
                        <li><span class="divider">/</span>Rédiger un article</li>
                        <?php
                        break;
                }
                break;
            case 'setting':
                switch($actionName){    
                    case 'filemanager':
                        ?>
                        <li>
                            <span class="divider">/</span>
                            <a href="<?php echo $this->view->url(array(), 'filemanager'); ?>">Gestion des fichiers</a>
                        </li>
                        <?php
                        break;
                    case 'settings':
                        ?>
                        <li>
                            <span class="divider">/</span>
                            <a href="<?php echo $this->view->url(array(), 'settings'); ?>">Paramètres</a>
                        </li>
                        <?php
                        break;
                }
                break;  
            default :
                break;
        }
        ?>
                </ul>
            <hr>
        </div>
        <?php
    }
}