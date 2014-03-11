<?php

class Application_Model_Category extends Application_Model_Abstract
{

    protected $_id;
    protected $_parentId;
    protected $_label;
    protected $_title;
    protected $_description;

    //protected $_categories = array();
    protected $_lvl;
    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = (int)$id;
        return $this;
    }

    public function getLabel() {
        return $this->_label;
    }

    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }

    public function getParentId() {
        return $this->_parentId;
    }

    public function setParentId($parentId) {
        if(is_null($parentId)){
            $this->_parentId = null;
        }
        else{
            $this->_parentId = (int)$parentId;
        }
        return $this;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    /*public function addCategory(Application_Model_Category $category){
        $this->_categories[] = $category;
    }*/
    
    public function toArray(){
        return array(
            'id' => $this->_id,
            'parentId' => $this->_parentId,
            'label' => $this->_label,
            'title' => $this->_title,
            'description' => $this->_description,
        );
    }
    
    public function toView(){
        $class =  new stdClass();
        $class->id = $this->_id;
        $class->parentId = $this->_parentId;
        $class->label = $this->_label;
        $class->title = $this->_title;
        $class->description = $this->_description;
        return $class;
    }
    
    /*public function getCategories(){
        foreach($this->_categories as $category){
            $category->getCategories();
        }
    }*/
    
    /**
     * Retourne toutes les categories enfants sous un unique tableau mais ordonné
     * Un champs 'lvl' est enregistré pour reconnaitre les enfants des enfants
     * ex :
     * [0][news][0]
     * [1][news_1][1]
     * [0][news_1_1][2]
     * [2][news_2][1]
     * [3][other][1]
     * 
     * @param type $ar
     * @param type $lvl 
     */
    public static function getOrdonnedCategory(&$ar, $lvl){
        foreach($this->_categories as $category){
            $current = $category->toArray();
            $current['lvl'] = $lvl;
            $ar[] = $current;
            $category->getCategoriesOnOneArray($ar, $lvl+1);
        }
        return $ar;
    }
    
    /**
     * Génère un tableau pour un <option> de type :
     * cat1
     * — cat3 (enfant de cat1)
     * cat2
     * — cat4 (enfant de cat2)
     * —— cat8 (enfant de cat4)
     * 
     * @param type $ordonnedCategories
     * @return type
     */
    public static function selectOptionFactory($ordonnedCategories = array(), $default = false){
        $options = array();
        if($default){
            $options[-1] = 'Aucune';
        }
        $indent = '';
        foreach($ordonnedCategories as $entry){
            for($i=0;$i<$entry['lvl'];$i++) $indent .= '—';
            $options[$entry['category']->getId()] = $indent . ' ' . $entry['category']->getTitle();
            $other = Application_Model_Category::selectOptionFactory($entry['childs']);
            foreach ($other as $key => $value){
               $options[$key] = $value;
            }
        }
        
        return $options;
    }
}

