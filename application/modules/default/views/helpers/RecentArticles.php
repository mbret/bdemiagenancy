<?php
/**
 * Retourne un tableau comprenant les n derniers articles
 */
class Default_View_Helper_RecentArticles extends Zend_View_Helper_Abstract
{
    public function recentArticles($n = 5){
        if(Zend_Registry::isRegistered('recentsArticles')){
            return Zend_Registry::get('recentsArticles');
        }
        else{
            $recentArticles = array();
            $mapper = new Application_Model_Mapper_Article();
            $order = 'date DESC';
            $count = $n;
            $articles = $mapper->fetchAllPublished($order, $count);
            foreach($articles as $article){
                $recentArticles[] = $article->toArray();
            }
            Zend_Registry::set('recentsArticles', $recentArticles);
            return $recentArticles;
        }
    }
}