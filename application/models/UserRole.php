<?php

class Application_Model_UserRole extends Application_Model_Abstract
{

    protected $_id;
    protected $_label;
    protected $_name;

    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getLabel() {
        return $this->_label;
    }

    public function setLabel($label) {
        $this->_label = $label;
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = $name;
    }
   
    public function toView(){
        $class =  new stdClass();
        $class->label = $this->_label;
        $class->name = $this->_name;
        return $class;
    }
    
}

