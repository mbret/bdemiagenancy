<?php

class Admin_Form_Category_Update extends Application_Form_Abstract{

    private $_title;
    private $_id;
    
    public function __construct($title, $id, $options = array()) {
        $this->_title = $title;
        $this->_id = $id;
//        $options['title'] = $title;
//        $options['id'] = $id;
        parent::__construct($options);   
    }
    
    public function init(){
        
        // Updatable in this form
        $this->addElement('text', 'title', array(
                'label' => 'Nom',
                'required' => true,
        ));
        // On applique le filtre Zend_Validate_Db_NoRecordExists seulement si on veux changer de label
        if( isset($_POST['title']) && $_POST['title'] != $this->_title ){
            $this->getElement('title')->addValidator(new Zend_Validate_Db_NoRecordExists('category', 'title'));
        }

        $this->addElement('text', 'label', array(
                'label' => 'label',
                'disabled' => '',
        ));
        
        $this->addElement('textarea', 'description', array(
            'rows' => '5',
            'required' => false,
            'label' => 'Description',
        ));
        
        // Categorie parente
        $validator = new My_Validate_NotIdentical($this->_id);
        $validator->setMessage("Une catégorie ne peut être parent d'elle même", My_Validate_NotIdentical::SAME);
        $mapper = new Application_Model_Mapper_Category();
        $options = Application_Model_Category::selectOptionFactory( $mapper->fetchAllOrdered() , true);
        $this->addElement('select', 'parentId', array(
            'MultiOptions' => $options,
            'label' => 'Catégorie parente',
            'validators' => array(
                $validator
            )
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => "Enregistrer les modifications",
            'value' => "submit",
        ));
    }
    
}



