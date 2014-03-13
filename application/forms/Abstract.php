<?php

abstract class Application_Form_Abstract extends Zend_Form{

    /**
     * Génère un tableau pour un <option> de type :
     * cat1
     * — cat3 (child of cat1)
     * cat2
     * — cat4 (child ofcat2)
     * —— cat8 (child of cat4)
     * 
     * @param type $ordonnedCategories
     * @return type
     */
    public function buildOrdonnedCategoriesSelectOption( $ordonnedCategories, $separator = '-', $lvl = 0 ){
        $options = array();
        $indent = '';
        // Get the correct indent according to the lvl
        for( $i = 0; $i < $lvl; $i++ ){
            $indent .= $separator;
        }
        
        foreach( $ordonnedCategories as $category ){
            $options[ $category['category']->getId() ] = $indent . ' ' . $category['category']->getTitle();
            
            // get the options array of children
            if( isset($category['children']) ){
                $children = $this->buildOrdonnedCategoriesSelectOption( $category['children'], $separator, $lvl+1 );
                // merge children with current category
                foreach ($children as $key => $value){
                   $options[$key] = $value;
                }
            }
        }
        
        return $options;
    }
}

