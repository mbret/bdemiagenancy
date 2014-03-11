<?php
/**
 * 
 * Rappel:
 *  -   $this->getPluginResource('foo') renvoi resource.foo si celui ci est 
 *      présent dans un des fichier .ini chargé dans index.php. Seul les resources
 *      officiel ZF peuvent être chargé
 * 
 * 
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{
 
    /**
     * Initialise le fichier de configuration que l'application pourras réutiliser
     * Rend accessible une resource "config" de type Zend_Config au reste du bootstrap
     *      
     *      Retrieve the config as below
     *      - $this->getInvokeArg('bootstrap')->getResource('config'); in a controller
     *      - Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('config'); in a view helper
     * 
     * @return \Zend_Config_Ini 
     */
    protected function _initConfig(){
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
        return $config;
    }
    
    
    /**
     * Database initialised by db resources
     */
    protected function _initDb(){
        $this->bootstrap('config'); // load config first
        
        try{
            Zend_Db_Table::setDefaultAdapter( $this->getPluginResource('db')->getDbAdapter() );
            Zend_Db_Table::getDefaultAdapter()->getConnection(); // test connexion
        }
        catch (Exception $e){
            
            // debug mode (we display error directly on the page
            if ( $this->getResource('config')->settings->alwaysThrowException ){
                echo $e;
                exit(1);
            }
            else{
                $this->bootstrap('logDated'); // init log dated
                Zend_Registry::get('log')->err( $e ); // insert error log in log
                include APPLICATION_PATH . '/views/scripts/error/nobdd.html'; // display special front page
                exit(1);
            }
        }
    }
    
    protected function _initActionHelpers(){
        Zend_Controller_Action_HelperBroker::addPath('My/Controllers/Action/Helpers');
        Zend_Controller_Action_HelperBroker::addHelper(new My_Controller_Action_Helper_LayoutLoader());
//        Zend_Controller_Action_HelperBroker::addHelper(new My_Controller_Action_Helper_GetMessagesInFlashMessenger());
    }
    
    protected function _initControllerPlugin(){
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new My_Controller_Plugin_Acl());
        $front->registerPlugin(new My_Controller_Plugin_Navigation());
        $front->registerPlugin(new My_Controller_Plugin_Setting());
    }
    
    /**
     * Initialise le dossier de cache par défaut
     */
    protected function _initCache(){
        
        if(!is_dir(DATA_PATH.'/tmp')){
            mkdir(DATA_PATH.'/tmp');
        }
        
        // Usually cache
        $frontend = array (
            'lifetime' => 345600, 
            'automatic_seralization' => true
        );
        $backend = array (
            'cache_dir' => DATA_PATH . '/tmp'
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontend,$backend);
        Zend_Registry::set('cache', $cache);
        
        // Cache for Zend_Locale
        Zend_Locale::setCache($cache);
    }
    
    /**
     * Affecte La langue FR aux messages d'erreurs des Zend_Validate
     * Utilise un fichier qui s'occupe de gérer la conversion
     */
    protected function _initTranslation(){
        $translator = new Zend_Translate(
            array(
                'adapter' => 'array',
                'content' => APPLICATION_PATH . '/../resources/languages',
                'locale'  => 'fr',
                'scan' => Zend_Translate::LOCALE_DIRECTORY
            ));
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
    
    
    /**
     * Init log for debug 
     * TLogs are prefixed by current date.
     * Ici on utilise un mix des resources .ini pour écraser l'initialisation du log
     * On le fait car on a du code dynamique (date) impossible à mettre dans le ini
     */
    protected function _initLogDated()
    {
        // General logs
        $generalLogs = Zend_Log::factory(array(
            array(
                'writerName' => 'Stream',
                'writerParams' => array(
                    'stream' => APPLICATION_PATH . sprintf('/../data/logs/%s_application.log', Zend_Date::now()->toString('dd-MM-yyyy'))
                )
            )
        ));
        Zend_Registry::set('log', $generalLogs ); // store static
        
        /**
         * Keep log of memory used
         */
        $memoryLogs = Zend_Log::factory(array(
            array(
                'writerName' => 'Stream',
                'writerParams' => array(
                    'stream' => APPLICATION_PATH . sprintf('/../data/logs/%s_application-memory-usage.log', Zend_Date::now()->toString('dd-MM-yyyy'))
                )
            )
        ));
        // Use a high stack index to delay execution until other plugins are finished, and their memory can also be accounted for.               
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new My_Controller_Plugin_MemoryPeakUsageLog( $memoryLogs ), 101);
    }
    
    
    /**
     * Initialisations html communes à toutes les vues
     * Set all constante useful for the program based on baseUrl
     */
    protected function _initHtmlView(){
        // get and init resources
        $this->bootstrap('frontcontroller');
        $controller = Zend_Controller_Front::getInstance();
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        defined('RESOURCES_BASEURL')
            || define('RESOURCES_BASEURL', $controller->getBaseUrl() . '/resources');
        
        $view->doctype('HTML5'); // doctype
        $view->headMeta()->setCharset('UTF-8'); // Charset HTML5
        
        // Google Font
        $view->headLink()->prependStylesheet("http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600");
        $view->headLink()->prependStylesheet("http://fonts.googleapis.com/css?family=The+Girl+Next+Door");
        
        $view->headMeta()->setCharset('UTF-8'); // HTML5

        // Favicon
        $view->headLink(array(
            'rel' => 'icon',
            'type' => "image/png",
            'href' => RESOURCES_BASEURL . '/images/favicon.png' )
                ,'PREPEND');
        
        $view->headLink()->appendStylesheet(RESOURCES_BASEURL . '/css/plugins.css'); // plugins redisign
    }
    
    /**
     * Init global view helper for all modules
     */
    protected function _initViewHelpers(){
        $view = $this->getResource('view');
        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Application_View_Helper'); // makes global view helpers accessible through any module
        $view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers', 'Default_View_Helper'); // makes global view helpers accessible through any module
    }

}

