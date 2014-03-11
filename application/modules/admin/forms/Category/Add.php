<?php

class Admin_Form_Category_Add extends Application_Form_Abstract{

    public function init(){
        
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'title', array(
                'label' => 'Nom',
                'required' => true,
                'validators' => array(new Zend_Validate_Db_NoRecordExists('category', 'title')),
        ));

        $this->addElement('text', 'label', array(
                'label' => 'label',
                'required' => true,
                'validators' => array(new Zend_Validate_Db_NoRecordExists('category', 'label')),
        ));
        
        // DESCRIPTION
        $this->addElement('textarea', 'description', array(
            'required' => false,
        ));
   
        
        // Categorie
        $mapper = new Application_Model_Mapper_Category();
        $options = Application_Model_Category::selectOptionFactory( $mapper->fetchAllOrdered() , true);
        $this->addElement('select', 'parentId', array(
            'MultiOptions' => $options,
            'label' => 'Catégorie parente',
        ));
        
        
        $this->addElement('hidden', 'add', array(
            'value' => 1
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => "Ajouter",
            'value' => "submit",
        ));
    }


    
}



