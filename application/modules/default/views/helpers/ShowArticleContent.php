<?php
/**
 * Helper qui récupère un contenu et cherche la balise [preview]
 * La fonction renvoi la chaine précedant la balise en ajoutant un lien pour voir la suite 
 */
class Application_View_Helper_ShowArticleContent extends Zend_View_Helper_Abstract
{
    public function showArticleContent($content, $idArticle, $preview = true){
        $more   = '<!--more-->';
        // On regarde si il y'a une balise more
        if (false !== strpos($content, $more)) { // on vérifie s'il est possible de tronquer l'article
            // Si on affiche que le première partie de l'article
            if($preview){
                $aContent = explode($more, $content); // on explose le contenu
                $content = $aContent[0]; // on récupère la première partie
                /**
                * On crée un lien "Lire la suite". On pointe vers une ancre qui nous placera
                * directement au bon endroit de l'article.
                * À vous de personnaliser le lien comme il faut.
                */
                $content .= '<a href="' . $this->view->baseUrl('index/article/id/' . $idArticle . '#more-'.$idArticle) . '">Lire la suite →</a>';
                return $content;
            }
            else{
                // Affichage complet
                $content = str_replace($more, '<span style=""id="more-'.$idArticle.'"></span>', $content);
                return $content;
            }
        }
        else{
            return $content;
        }
    }

}