<?php

class IndexController extends Zend_Controller_Action{

    protected $_userMapper;
    protected $_articleMapper;
    protected $_categoryMapper;
    protected $_tagMapper;
    protected $_settings;
    
    public function init(){
        //var_dump( get_magic_quotes_gpc());exit();
        parent::init();
        $this->_userMapper = new Application_Model_Mapper_User();
        $this->_categoryMapper = new Application_Model_Mapper_Category();
        $this->_articleMapper = new Application_Model_Mapper_Article();
        $this->_tagMapper = new Application_Model_Mapper_Tag();
        $this->_settings = $this->getInvokeArg('bootstrap')->getResource('config')->settings;
    }

    
    /**
     * Accueil
     *  * Liste les articles mis en avant et ou les derniers articles rédigé 
     */
    public function indexAction(){
        $category = new Application_Model_Category();
        $user = new Application_Model_User();
        $articles = $this->_articleMapper->fetchLastForward( (int)$this->_settings->nbArticlesForward ); // get last forwarded articles
        $viewArticles = array();
        
        foreach($articles as $article){
            $this->_userMapper->find($article->getUserId(), $user); // search user
            $viewArticle = $article->toView();
            $viewArticle->author = $user->toView(); // set author
            $viewArticle->tags = array(); // set tags
            foreach ($this->_tagMapper->fetchAllByArticle($article->getId()) as $tag) {
                $viewArticle->tags[] = $tag->toView();
            };
            $dates = $this->_articleMapper->getEditionsDates($article->getId(), 'date DESC'); // set last edition date
            if(!empty( $dates ) ){
                $viewArticle->lastEditionDate = $dates[0]; 
            }
            $this->_categoryMapper->find($article->getCategoryId(), $category);
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
            if( !is_integer($this->_getParam('id')) || !$mapper->find((int)$this->_getParam('id'), $article) ){
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
    
    /**
     * Méthode qui gère la creation et la gestion du formulaire
     */
    protected function _contact(){
        $form = new Default_Form_Contact();
        $this->view->form = $form;
        // Une fois le formulaire posté on redirige vers la page de contact
        if ($this->getRequest()->isPost()){
            
            $this->_helper->_redirector('page', 'index', 'default', array('label' => 'contact'));
        }
    }
    
    /**
     * Enregistre un commentaire pour un article et redirige vers cette article.
     */
    public function addcommentAction()
    {
        if($this->_hasParam('articleid')){
            
            // Verification connexion 
            $identity = Zend_Auth::getInstance();
            if($identity->hasIdentity()){
                
                $articleMapper = new Application_Model_Mapper_Article();
                $article = new Application_Model_Article();
                if($articleMapper->find($this->_getParam('articleid'), $article)
                        && $article->getIsPublished()){ // L'article dois être publié pour avoir des commentaires
                        
                    $identity = Zend_Auth::getInstance()->getIdentity();
                    $mapper = new Application_Model_Mapper_Comment();
                    $form = new Default_Form_Comment();
                    $request = $this->getRequest();
                    if ($request->isPost()){
                        if($form->isValid($request->getPost())){
                            
                            $data = $form->getValues();
                            
                            $mapper->beginTransaction();

                            $comment = new Application_Model_Comment();
                            $comment->setOptions(array(
                                'content' => $data['content'],
                                'userId' => $identity->id,
                                'articleId' => $article->getId(),
                                'isPublished' => true,));
                            $mapper->save($comment);

                            $mapper->commit();

                            $this->_helper->flashMessenger(array('success' => "Commentaire ajouté."));

                        }
                        else{
                            $this->_helper->flashMessenger(array('error' => "Commentaire non valide !"));
                        }
                    }
                    else{

                    }
                }
                else{
                    $this->_helper->flashMessenger(array('warning' => "L'article demandé n'existe pas !"));
                }
                        
            }
            else{
                $this->_helper->flashMessenger(array('warning' => "Vous devez vous connecter pour poster un commentaire !"));
                $this->_helper->_redirector('login', 'auth', 'admin');
            }
            // Redirect every times
            $this->_helper->_redirector('article', 'index', 'default', array('id' => $this->_getParam('articleid')));
        }
        else{
            $this->_helper->_redirector('index', 'index', 'default');
        }
    }
    
    
    public function searchAction(){
        
    }
}

