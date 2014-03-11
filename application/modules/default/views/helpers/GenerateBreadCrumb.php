<?php

/**
 * View helper which generate html admin breacrumb
 */
class Default_View_Helper_GenerateBreadCrumb extends Zend_View_Helper_Abstract
{

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
    
    
    public function generateBreadCrumb(){
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        ?>
        <div>
            <hr>
                <ul class="breadcrumb">
                    <li><a href="<?php echo $this->view->url(array(), 'home'); ?>">Accueil</a></li>
        <?php
        // Construit le breadcrumb de base
        // Permet de garder un breadcrumb propre en cas de re passage à cause d'une erreur (error handler) qui rappel les plugins
        switch($controllerName){
            case 'index' :
                switch($actionName){
                    default:
                        break;
                    
                    case 'articles':
                        
                        if(isset($params['cat'])){
                            ?><li><span class="divider">/</span><a href="<?php echo $this->view->url(array(), 'articles', true); ?>">Actualités</a></li><?php
                            ?><li><span class="divider">/</span>Par catégorie</li><?php
                        }
                        else if(isset($params['year']) || isset($params['month'])){
                            ?><li><span class="divider">/</span><a href="<?php echo $this->view->url(array(), 'articles', true); ?>">Actualités</a></li><?php
                            ?><li><span class="divider">/</span>Par date</li><?php
                        }
                        else{
                            ?><li><span class="divider">/</span>Actualités</li><?php
                        }
                        break;
                    
                    case 'article':
                    ?><li><span class="divider">/</span><a href="<?php echo $this->view->url(array(), 'articles'); ?>">Articles</a><span class="divider">/</span>Détail</li><?php
                    break;
                }
                break;
            
            case 'page':
                ?>
                <li><span class="divider">/</span>Page</li>
                <?php
                break;
            
            case 'extra':
                switch($actionName){
                
                    default:
                        
                        break;
                    
                    case 'about':
                        ?><li><span class="divider">/</span>A propos de nous</li><?php
                        break;
                    
                    case 'contact':
                        ?><li><span class="divider">/</span>Nous contacter</li><?php
                        break;
                    
                    case 'statuts':
                        ?><li><span class="divider">/</span>Statuts</li><?php
                        break;
                    
                    case 'legal':
                        ?><li><span class="divider">/</span>Mention légales</li><?php
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