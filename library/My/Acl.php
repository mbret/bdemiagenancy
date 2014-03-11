<?php
/**
 * Class ACL
 * Personal class to manage ACL
 * 
 */
class My_Acl extends Zend_Acl{

    public function __construct(){

        /**
         * User's role
         * 
         * - guest
         * - member
         * - writer
         * - editor
         * - admin
         */
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('member'), 'guest'); // see
        $this->addRole(new Zend_Acl_Role('writer'), 'member');
        $this->addRole(new Zend_Acl_Role('editor'), 'writer');
        $this->addRole(new Zend_Acl_Role('admin'));

        /**
         * Front resources
         */
        $this->add(new Zend_Acl_Resource('error'));
        $this->add(new Zend_Acl_Resource('index'));
        $this->add(new Zend_Acl_Resource('feed'));
        $this->add(new Zend_Acl_Resource('extra'));
        
        /**
         * Admin resources
         */
        $this->add(new Zend_Acl_Resource('admin_index'));
        $this->add(new Zend_Acl_Resource('admin_user'));
        $this->add(new Zend_Acl_Resource('admin_error'));
        $this->add(new Zend_Acl_Resource('admin_auth'));
        $this->add(new Zend_Acl_Resource('admin_article'));
        $this->add(new Zend_Acl_Resource('admin_page'));
        $this->add(new Zend_Acl_Resource('admin_tag'));
        $this->add(new Zend_Acl_Resource('admin_category'));
        $this->add(new Zend_Acl_Resource('admin_setting')); // setting application
        
        /**
         * Right attributions
         * - guest:
         *      errors, rss, extra pages, front index, authentification/logout/subscribe, articles
         * - 
         */
        $this->allow('guest', 'error'); // errors
        $this->allow('guest', 'feed'); // rss ...
        $this->allow('guest', 'extra');
        $this->allow('guest', 'index');
        $this->allow('guest', 'admin_auth'); // auth
        $this->allow('guest', 'index', array('articles')); // seeing articles
        $this->allow('member', 'admin_error'); // admin errors
        $this->allow('member', 'admin_index', array('index')); // admin home
        $this->allow('member', 'admin_user', array('update')); // admin profil (update)
        $this->allow('writer', 'admin_article', array('write', 'index', 'update', 'remove', 'restore', 'see')); // administrer ses articles
        $this->allow('writer', 'admin_tag', array('index')); // voir et ajouter des tags
        $this->allow('editor', 'admin_article', array('write', 'index', 'update', 'remove', 'restore', 'see',// administrer les autres articles
                                                      'updateOther', 'removeOther', 'restoreOther', 'seeOther'));
        $this->allow('editor', 'admin_tag'); // administrer les tags
        $this->allow('admin');
        
        
        
        Zend_Registry::set('acl',$this);
    }
    
}
