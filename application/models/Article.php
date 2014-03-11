<?php

class Application_Model_Article extends Application_Model_Abstract{

    protected $_id;
    protected $_userId;
    protected $_categoryId;
    protected $_title;
    protected $_content;
    protected $_date;
    protected $_isPublished;
    protected $_putForward;
    protected $_inTrash;
    protected $_forwardTitle;
    protected $_forwardContent;
    
    protected $_tags = null;
    protected $_comments = null;
    protected $_category = null;
    protected $_author = null;
    protected $_editionsDates = array();
    
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
    public function getForwardTitle() {
        return $this->_forwardTitle;
    }

    public function setForwardTitle($forwardTitle) {
        $this->_forwardTitle = $forwardTitle;
    }

    public function getForwardContent() {
        return $this->_forwardContent;
    }

    public function setForwardContent($forwardContent) {
        $this->_forwardContent = $forwardContent;
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

    public function getIsPublished() {
        return $this->_isPublished;
    }

    public function setIsPublished($isPublished) {
        $this->_isPublished = (bool)$isPublished;
        return $this;
    }

    public function getPutForward() {
        return $this->_putForward;
    }

    public function setPutForward($putForward) {
        $this->_putForward = (bool)$putForward;
        return $this;
    }

    public function getInTrash() {
        return $this->_inTrash;
    }

    public function setInTrash($inTrash) {
        $this->_inTrash = $inTrash;
        return $this;
    }

    public function getCategoryId() {
        return $this->_categoryId;
    }

    public function setCategoryId($categoryId) {
        $this->_categoryId = (int)$categoryId;
        return $this;
    }

            
    
    public function getEditionsDates() {
        return $this->_editionsDates;
    }

    public function setEditionsDates($editionsDates) {
        $this->_editionsDates = $editionsDates;
    }

    public function setTags($tags){
        $this->_tags = $tags;
    }

    public function setComments($comments) {
        $this->_comments = $comments;
    }

    public function setCategory(Application_Model_Category $category) {
        $this->_category = $category;
    }

    public function setAuthor(Application_Model_User $author) {
        $this->_author = $author;
    }

    public function isAuthor($userId){
        if($this->_userId === $userId) return true;
        return false;
    }
    
    
    
    public function toArray(){
        return array(
            'id' => $this->_id,
            'title' => $this->_title,
            'content' => $this->_content,
            'date' => $this->_date,
            'isPublished' => $this->_isPublished,
            'putForward' => $this->_putForward,
            'categoryId' => $this->getCategoryId(),
            'month' => substr($this->_date, 5, 2),
            'day' => substr($this->_date, 8, 2),
            'forwardContent' => $this->_forwardContent,
            'forwardTitle' => $this->_forwardTitle,
        );
    }
    
    public function toView(){
        $class =  new stdClass();
        $class->title = $this->_title;
        $class->id = $this->_id;
        $class->content = $this->_content;
        $class->date = $this->_date;
        $class->month = substr($this->_date, 5, 2);
        $class->day = substr($this->_date, 8, 2);
        $class->isPublished = $this->_isPublished;
        $class->putForward = $this->_putForward;
        $class->inTrash = $this->_inTrash;
        $class->forwardTitle = $this->_forwardTitle;
        $class->forwardContent = $this->_forwardContent;


        if(!is_null($this->_comments)){
            $class->comments = array();
            foreach ($this->_comments as $comment) {
                $class->comments[] = $comment->toView();
            }
        }
        return $class;
    }
    
}

