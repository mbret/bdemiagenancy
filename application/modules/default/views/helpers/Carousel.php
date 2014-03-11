<?php
/**
 * Helper du carousel
 * 
 */
class Default_View_Helper_Carousel extends Zend_View_Helper_Abstract{
    public function carousel(){
        $front = Zend_Controller_Front::getInstance();
        $dir = PUBLIC_PATH . '/' . $front->getParam('bootstrap')->getResource('config')->settings->carouselDir;
        $files = array();
        //  si le dossier pointe existe
        if (is_dir($dir)){

        // si il contient quelque chose
        if ($dh = opendir($dir)) {

            // boucler tant que quelque chose est trouve
            while (($file = readdir($dh)) !== false) {

                // affiche le nom et le type si ce n'est pas un element du systeme
                if( $file != '.' && $file != '..') {
                //echo "fichier : $file : type : " . filetype($dir . $file) . "<br />\n";
                    $files[] = $file;
                }
            }
            // on ferme la connection
            closedir($dh);
        }
        }
        
        return $files;
    }
}