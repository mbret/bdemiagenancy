<?php

class Admin_Form_Article_Write extends Application_Form_Abstract
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
        ));
        
        
        
        $mapper = new Application_Model_Mapper_Category();
        $options = $this->buildOrdonnedCategoriesSelectOption($mapper->fetchAllOrdered());
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
//            'label' => 'Mots-clefs',
            'required' => false,
            'class' => 'input-xlarge',
            'data-rel' => 'chosen',
            'data-placeholder' => 'Choisissez les mots-clefs',
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

