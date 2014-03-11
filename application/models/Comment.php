<?php

class Application_Model_Comment extends Application_Model_Abstract
{

    protected $_id;
    protected $_userId;
    protected $_articleId;
    protected $_content;
    protected $_date;
    protected $_isPublished;
    
    
    protected $_author = null;
    
    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = (int)$id;
        return $this;
    }

    public function getUserId() {
        return $this->_userId;
    }

    public function setUserId($userId) {
        $this->_userId = (int)$userId;
        return $this;
    }

    public function getArticleId() {
        return $this->_articleId;
    }

    public function setArticleId($articleId) {
        $this->_articleId = $articleId;
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

    public function getIsPublished() {
        return $this->_isPublished;
    }

    public function setIsPublished($isPublished) {
        $this->_isPublished = (bool)$isPublished;
        return $this;
    }
        
    
    public function setAuthor(Application_Model_User $author){
        $this->_author = $author;
        return $this;
    }
    
    public function getAuthor(){
        return $this->_author;
    }
    
    public function toArray(){
        return array(
            'id' => $this->_id,
            'content' => $this->_content,
            'date' => $this->_date,
        );
    }
    
    
    public function toView(){
        $class =  new stdClass();
        $class->id = $this->_id;
        $class->content = $this->_content;
        $class->date = $this->_date;
        if(!is_null($this->_author)){
            $class->author = $this->_author->toView();
        }
        return $class;
    }
    
}

