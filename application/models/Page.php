<?php

class Application_Model_Page extends Application_Model_Abstract
{

    protected $_id;
    protected $_label;
    protected $_title;
    protected $_content;
    protected $_date;
    
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

    public function getTitle() {
        return $this->_title;
    }

    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    public function getContent() {
        return $this->_content;
    }

    public function setContent($content) {
        $this->_content = $content;
        return $this;
    }

    public function getDate() {
        return $this->_date;
    }

    public function setDate($date) {
        $this->_date = $date;
        return $this;
    }

    public function toArray(){
        return array(
            'id' => $this->_id,
            'title' => $this->_title,
            'content' => $this->_content,
            'label' => $this->_label,
            'date' => $this->_date,
        );
    }
    
}

