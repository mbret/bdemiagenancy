<?php

class Application_Model_Tag extends Application_Model_Abstract
{

    protected $_id;
    protected $_label;
    protected $_description;

    
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

    public function toArray(){
        return array(
            'id' => $this->_id,
            'label' => $this->_label,
            'description' => $this->_description,
        );
    }
    
    public function toView(){
        $class =  new stdClass();
        $class->id = $this->_id;
        $class->description = $this->_description;
        $class->label = $this->_label;
        return $class;
    }
    
}

