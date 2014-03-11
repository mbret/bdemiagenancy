<?php
/**
 * Retourne un tableau comprenant chaque mois et le nombres d'articles associés
 * 
 * @todo gerer par année
 */
class Application_View_Helper_Archives extends Zend_View_Helper_Abstract
{
    public function archives(){
        if(Zend_Registry::isRegistered('archives')){
            return Zend_Registry::get('archives');
        }
        else{
            $mapper = new Application_Model_Mapper_Article();
            $result = $mapper->getArchives();
            // On récupere les années
            $archives = array();
            foreach ($result as $entry){
                $archives[$entry->year][] = array(
                    'month' => $entry->month, 
                    'nb' => $entry->nb
                    );
            }
            krsort($archives);
            Zend_Registry::set('archives', $archives);
            return $archives;
        }
    }
}