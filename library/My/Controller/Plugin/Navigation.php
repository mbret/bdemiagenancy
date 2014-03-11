<?php
/**
 * Automatise l'activation des bon menu dans la barre de navigation
 * 
 */
class My_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract{
    
    private $view;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request){
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $this->view = $view;
        $this->buildActiveNavigation($request, $view);
        $this->buildCategoryDropDown($view);
    }

    
    
    //avant que le contrôleur d’action soit appelé mais après le routage de la requête
    private function buildActiveNavigation($request , $view)
    {   
        $module = $request->getModuleName();   
        $controller = $request->getControllerName();   
        $action = $request->getActionName();   
        $category = (null != $request->getParam('cat')) ? '_' . $request->getParam('cat') : '';

        // Active le bon menu dans la sidebar (Les placeholders doivent être dans la vue de la sidebar)
        $placeHolder = $view->placeholder('nav_' . $module . '_' . $controller . '_' . $action . $category);
        $placeHolder->set('active');
    } 
    
    
    
    
    
    
    
    private function _buildSubMenuRecursive($page){
        if(!empty($page->pages)){
            ?>
            <ul class="dropdown-menu sub-menu">
            <?php foreach ($page->pages as $page_n): ?>
                <li>
                    <a href="<?php echo $page_n->uri; ?>" ><?php echo $page_n->title; ?> <?php if(!empty($page_n->pages)): ?><i class="icon-arrow-right"></i><?php endif; ?></a>
                    <?php echo $this->_buildSubMenuRecursive($page_n); ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php
        }
    }
     
    // On parcours toutes les categories
    // Si on a un enfant on relance la recherche sur lui puis on l'ajoute au parent
    private function findChilds($categories, &$page){
        foreach($categories as $category){
            
            // On a un enfant
            if($category->getParentId() === (int)$page->id){
                
                $newPage = Zend_Navigation_Page::factory(array(
                    'label' => $category->getLabel(),
                    'title' => $category->getTitle(),
                    'id' => $category->getId(),
                    'uri' => $this->view->url(array('controller' => 'index', 'action' => 'articles', 'cat' => $category->getLabel())),
                ));
                
                $this->findChilds($categories, $newPage);
                $page->addPage($newPage);
            }
        }
    }
    
    /**
     * Crée recursivement la liste de category pour la barre de menu
     * * Crée une liste de puces avec leurs sous puces
     * * clear de la liste à chaque demarrage pour eviter la duplication en cas d'exception du front
     * @param type $view
     */
    private function buildCategoryDropDown($view){
        
        $categoryDropdown = $view->placeholder('categoryDropdown');
        $categoryDropdown->set('');
        $mapper = new Application_Model_Mapper_Category();
        $categories = $mapper->fetchAllPublished();

        $container = new Zend_Navigation();
        foreach($categories as $category){
            
            // On a un header
            if(is_null($category->getParentId())){
                
                // On crée une page
                $page = Zend_Navigation_Page::factory(array(
                    'label' => $category->getLabel(),
                    'title' => $category->getTitle(),
                    'id' => $category->getId(),
                    'uri' => $this->view->url(array('cat' => $category->getLabel(), 'module' => 'default', 'action' => 'articles', 'controller' => 'index'), null, true),
                ));
                
                $this->findChilds($categories, $page);
                $container->addPage($page);
            }
        }
        
        $categoryDropdown->captureStart();
        // CREATION DU MENU
        // On crée ici la menu + le premier sous menu
        foreach ($container as $page): 
        ?>
            <!-- categorie -->
            <li class="dropdown .visible-desktop" >
                <a class="dropdown-toggle" dropdown-toggle-throw="false" href="<?php echo $page->uri; ?>">— <?php echo $page->title; ?> <?php if(!empty($page->pages)): ?><b class="caret"></b><?php endif; ?></a>
                <!-- sous categorie -->
                <?php if(!empty($page->pages)): ?>
                    <ul class="dropdown-menu sub-menu">
                    <?php foreach ($page->pages as $page_n): ?>
                        <li>
                            <a href="<?php echo $page_n->uri; ?>" ><?php echo $page_n->title; ?> <?php if(!empty($page_n->pages)): ?><i class="icon-arrow-right"></i><?php endif; ?></a>
                            <!-- sous categories n + 1 -->
                            <?php echo $this->_buildSubMenuRecursive($page_n); ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <!-- end: sous categorie -->
            </li>
            <!-- end: categorie -->
        <?php 
        endforeach;
        $categoryDropdown->captureEnd();
    }
    
}
