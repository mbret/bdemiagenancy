<?php

class IndexController extends Zend_Controller_Action{
    
    public function init(){
        parent::init();
        $this->_settings = $this->getInvokeArg('bootstrap')->getResource('config')->settings;
    }

    
    /**
     * Accueil
     *  * Liste les articles mis en avant et ou les derniers articles rédigé 
     */
    public function indexAction(){
        $userMapper = new Application_Model_Mapper_User();
        $categoryMapper = new Application_Model_Mapper_Category();
        $articleMapper = new Application_Model_Mapper_Article();
        $tagMapper = new Application_Model_Mapper_Tag();
        $category = new Application_Model_Category();
        $user = new Application_Model_User();
        $articles = $articleMapper->fetchLastForward( (int)$this->_settings->nbArticlesForward ); // get last forwarded articles
        $viewArticles = array();
        
        foreach($articles as $article){
            $userMapper->find($article->getUserId(), $user); // search user
            $viewArticle = $article->toView();
            $viewArticle->author = $user->toView(); // set author
            $viewArticle->tags = array(); // set tags
            foreach ($tagMapper->fetchAllByArticle($article->getId()) as $tag) {
                $viewArticle->tags[] = $tag->toView();
            };
            $dates = $articleMapper->getEditionsDates($article->getId(), 'date DESC'); // set last edition date
            if(!empty( $dates ) ){
                $viewArticle->lastEditionDate = $dates[0]; 
            }
            $categoryMapper->find($article->getCategoryId(), $category);
            $viewArticle->category = $category->toView();
            $viewArticles[] = $viewArticle;
        }

        $this->view->articles = $viewArticles;
    }

    
    /**
     * Affiche la listes d'articles en fonction de la demande
     */
    public function articlesAction(){
        
        $userMapper = new Application_Model_Mapper_User();
        $mapper = new Application_Model_Mapper_Article();
        $user = new Application_Model_User();
        $categoryMapper = new Application_Model_Mapper_Category();
        $category = new Application_Model_Category();
        $tagMapper = new Application_Model_Mapper_Tag();
        $tag = new Application_Model_Tag();
        $articles = array();
        
        // Récupération des paramètres
        $offset = $this->_getParam('o', 0);
        $count = $this->_getParam('c', 3);
        $order = 'date DESC';
        
        /**
         * Recherche selective
         */
        // Recherche par tag
        if($this->_hasParam('tag')){
            $section = 'Tag';
            if(!$tagMapper->findByLabel($this->_getParam('tag'), $tag)){
                $this->_helper->flashMessenger(array('warning' => "Aucun article !"));
                $title = $this->_getParam('tag');
            }
            else{
                $articles = $mapper->fetchAllPublishedByTag($tag->getId(), $order, $count, $offset);
                $title = $tag->getLabel();
            }
            $this->view->search = "/tag/" . $this->_getParam('tag');
        }
        // Recherche par date
        else if($this->_hasParam('year') && $this->_hasParam('month')){
            $section = 'Date';
            $articles = $mapper->fetchAllPublishedByDate($this->_getParam('year'), $this->_getParam('month'), $order, $count, $offset);
            $this->view->search = "?year=" . $this->_getParam('year') . "&month=" . $this->_getParam('month');
            $title = $this->_getParam('month') . '-' . $this->_getParam('year');
        }
        // categories
        else if($this->_hasParam('cat')){
            
            $section = 'Catégories';
            if(!$categoryMapper->findByLabel($this->_getParam('cat'), $category)){
                $this->_helper->flashMessenger(array('warning' => "Aucun article !"));
                $title = $this->_getParam('cat');
            }
            else{
                $articles = $mapper->fetchAllPublishedByCategory($category->getId(), $order, $count, $offset);
                $title = $category->getTitle();
            }
            $this->view->search = "/cat/" . $this->_getParam('cat');
        }
        // Recherche sans critere
        else{
            $articles = $mapper->fetchAllPublished($order, $count, $offset);
        }

        // Remplissages articles
        $this->view->articles = array();
        $nbArticles = 0;
        foreach($articles as $article){
            $userMapper->find($article->getUserId(), $user);
            $categoryMapper->find($article->getCategoryId(), $category);
            $entry = $article->toArray();
            $entry['category'] = $category->toArray();
            $entry['author'] = $user->toView();
            $entry['tags'] = $tagMapper->fetchAllByArticle($article->getId());
            $entry['url'] = $this->view->url(array('controller'=> 'index', 'action' => 'article', 'id' => $article->getId()),'default',true);
            $this->view->articles[] = $entry;
            $nbArticles++;
        }
        
        $this->view->offset = $offset + $nbArticles;
        $this->view->count = $count;
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setRender('articlespost'); 
        }
        
        // Breadcrumb
        if(isset($section)){
            $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span><a href="' . $this->view->url(array(), 'articles') . '">Articles</a></li>');
            $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>' . $section . '</a></li>');
            $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>' . $title . '</a></li>');
        }
        else{
            $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>Articles</li>');
        }
    }
    
    
    /**
     * Affiche le détail d'un article 
     * 
     * param:  
     *  - id: id de l'article
     *  - preview: (true) l'article doit être un brouillon et est alors affiché aux ayant droit
     */
    public function articleAction(){
        
        $userMapper = new Application_Model_Mapper_User();
        $categoryMapper = new Application_Model_Mapper_Category();
        $tagMapper = new Application_Model_Mapper_Tag();
        $category = new Application_Model_Category();
        $user = new Application_Model_User();
        $article = new Application_Model_Article();
        $mapper = new Application_Model_Mapper_Article();
        $identity = Zend_Auth::getInstance();
        $preview = $this->_getParam('preview', false);
        $viewPreview = false;
        
        if( ! $this->_hasParam('id') ){
            $this->_helper->_redirector('index', 'index', 'default');
        }
        else{
            
            // EXIT : Chargement + On verifie l'existence
            if( !$mapper->find((int)$this->_getParam('id'), $article) ){
                $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                $this->_helper->_redirector('index', 'index', 'default');
            }
            else{
            
                // EXIT : Verification de l'affichage de l'article
                if( $article->getInTrash()){ // Si l'article est à la corbeille
                    $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                    $this->_helper->_redirector('index', 'index', 'default');
                    
                }
                else{
                    // EXIT : Si l'article n'est pas publié et que l'on est pas en preview
                    if(!$article->getIsPublished() && !$preview){
                        $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                        $this->_helper->_redirector('index', 'index', 'default');
                    }
                    else{
                        // Si l'article est en preview
                        if( $preview && !$article->getIsPublished() ){
                            if( Zend_Registry::get('acl')->isAllowed($identity->getIdentity()->role['label'], 'admin_article', 'seeOther')
                                || ( ($article->getUserId() !== $identity->getIdentity()->id) && Zend_Registry::get('acl')->isAllowed($identity->getIdentity()->role['label'], 'admin_article', 'see')) ){
                            
                                $this->_helper->flashMessenger->addMessage(array('warning' => "Cet article est en attente de publication !")); 
                                $viewPreview = true;
                            }
                            else{
                                $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                                $this->_helper->_redirector('index', 'index', 'default');
                            }
                        }

                        // Load rest of data and format article for view
                        $userMapper->find($article->getUserId(), $user);
                        $categoryMapper->find($article->getCategoryId(), $category);
                        $viewArticle = $article->toView();
                        $viewArticle->user = $user->toView();
                        $viewArticle->category = $category->toView();
                        $viewArticle->author = $user->toView();
                        $viewArticle->tags = array(); // set tags
                        foreach ($tagMapper->fetchAllByArticle($article->getId()) as $tag) {
                            $viewArticle->tags[] = $tag->toView();
                        };
                        
                        $this->view->article = $viewArticle;
                        $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>' . $article->getTitle() . '</li>'); // Breadcrumb
                    }
                }
            }
        }
        
        
        $this->view->preview = $viewPreview;
        
    }
    
    /**
     * Affiche la page demandé.
     * Si la key existe dans la bdd on affiche la page de la base de donnée
     * Si la key n'existe pas mais que la vue existe on affiche la page fixe
     */
    public function pageAction()
    {
        $placeHolder = $this->view->placeholder('breadcrumb');
        $placeHolder->append('<li><span class="divider">/</span>' . $this->_getParam('label', '') . '</li>');
        
        // Si la page existe en bdd
        $page = new Application_Model_Page();
        $mapper = new Application_Model_Mapper_Page();
        
        $label = $this->_getParam('label', '');
        
        $this->view->placeholder('nav_default_index_page_' . $label)->set('active');
        
        if($mapper->find($label, $page)){
            
            $this->view->page = $page->toArray();
            $this->render('page/pagedynamic', null, true);
            
        }
        else{
            
            $script = 'page/' . $label . '.phtml';
            $path = $this->view->getScriptPaths();
            $path = $path[0];
            if(file_exists($path . $script)){
                $method = '_' . $label;
                $this->$method();
                $this->render('page/' . $label, null, true);
            }
            else{
                throw new Zend_Controller_Action_Exception("Le script de vue " . $path . $script . " n'existe pas",404);
            }

        }
    }
    
}

