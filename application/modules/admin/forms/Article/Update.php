<?php

class Admin_Form_Article_Update extends Application_Form_Abstract
{

    public function init(){
        parent::init();

        // Titre de l'article
        $this->addElement('text', 'title', array(
            'required' => true,
            'filters'    => array('StringTrim'),
        ));
        
        // Contenu 
        $this->addElement('textarea', 'articleContent', array(
            'required' => false,
        )); 
        
        // Mettre en avant
        $this->addElement('checkbox', 'isPublished', array(
            'label' => "Publier l'article :",
        ));
        
        $mapper = new Application_Model_Mapper_Category();
        $options = Application_Model_Category::selectOptionFactory( $mapper->fetchAllOrdered());
        $this->addElement('select', 'categoryId', array(
            'required' => false,
            'data-rel' => 'chosen',
            'multiOptions' => $options,
        ));
        
        // Tags
        $mapper = new Application_Model_Mapper_Tag();
        $options = array();
        foreach($mapper->fetchAllToArray() as $value){
            $options[$value['id']] = $value['label'];
        }
        $this->addElement('multiselect', 'tags', array(
            'required' => false,
            'class' => 'input-xlarge',
            'data-rel' => 'chosen',
            'data-placeholder' => 'Choisissez un ou plusieurs mots-clefs',
            'multiOptions' => $options,
        ));
        
        $this->addElement('checkbox','putForward', array(
        ));
        
        $this->addElement('text','forwardTitle', array(
            'required' => false,
        ));
        
        $this->addElement('textarea','forwardContent', array(
            'required' => false,
        ));
        
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'value' => "submit",
        ));

    }


}

