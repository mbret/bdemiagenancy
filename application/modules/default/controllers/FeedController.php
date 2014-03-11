<?php

class FeedController extends Zend_Controller_Action {

    public function init() {
        // REDIRECT 
        $this->_helper->flashMessenger(array('warning' => "Page en construction."));
        $this->_helper->_redirector('index', 'index', 'default');
        
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        // On prend les 20 derniers items
        $this->nbEntries = $this->_request->getParam('nbEntries',20);
        // Flux par defaut, rss
        $this->feedFormat = $this->_request->getParam('format','rss');
        
        // Création du flux
        $this->_prepareEntries();
    }
    
    
    private function _prepareEntries() {
        $articleMapper = new Application_Model_Mapper_Article();
        $items = $articleMapper->fetchAllPublished('date DESC', $this->nbEntries);
        
        //$postsTmp = $this->blog->getLastContents($this->nbEntries);
        
        $this->entries = array();
        /*foreach ($items as $post) {
            $urltitle = explode('-',$this->view->urlize($post->title));
            $oldUrltags = explode('-',$this->view->urlize($post->tags));
            $urltags = array();
            $i = 0;
            foreach($oldUrltags as $w) {
                if (!in_array($w,$urltitle)) {
                    $urltags[] = $oldUrltags[$i];
                }
                $i++;
            }
            $url = $this->view->url(
                array(
                    'id' => $post->id,
                    'tags' => implode('-',$urltitle).'+'.implode('-',$urltags)
                ),
                'billetDeBlog',
                true
            );
            $this->entries[] = array(
                'title' => $post->title,
                'link' => 'http://mr.moox.fr'.$url,
                'description' => $this->view->bbcode($this->view->content($this->view->contentIntro($post->content))),
                'lastUpdate' => strtotime($post->date),
                'content' => $this->view->bbcode($this->view->content($post->content))
            );
        }*/
    }
    
    public function indexAction() {
        $entries = array();
        // prepare an array that our feed is based on
        $this->feedArray = array(
            'title' => 'BDE Miage Nancy',
            'link' => '_URL',
            'lastUpdate' => date(DATE_RSS),
            'charset' => 'utf-8',
            'description' => "Des actualités en tous genres et sur les technologies web (XHTML, CSS, Javascript, AJAX, PHP, Zend Framework...). Et bien sur, tout ça avec une touche d'humour!",
            'author' => 'Maxime Thirouin aka Mr.MoOx',
            'email' => 'contact@moox.fr',
            'copyright' => 'Mr. MoOx, all rights reserved',
            'generator' => 'Rewix (using Zend Framework Zend_Feed)',
            'language' => 'fr',
            'entries' => $entries
        );
       
        // create feed document
        $feed = Zend_Feed::importArray($this->feedArray, $this->feedFormat);

        // adjust created DOM document (je sais pas trop à quoi ca sert... mais apparement c pas vital)
        /*
        foreach ($feed as $entry) {
            $element = $entry->summary->getDOM();
            // modify summary DOM node
        }*/

        // envoie les en-têtes et décharge le flux
        $feed->saveXml();
        //$feed->send();
    }
}

