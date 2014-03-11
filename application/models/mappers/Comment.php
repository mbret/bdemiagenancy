<?php

class Application_Model_Mapper_Comment extends Application_Model_Mapper_Abstract
{

    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_Comment');
    }
    
    public function save(Application_Model_Comment $comment)
    {
        $data = array(
            'content' => $comment->getContent(),
            'isPublished' => $comment->getIsPublished(),
            'articleId' => $comment->getArticleId(),
            'userId' => $comment->getUserId(),
        );
 
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $comment->getId())) {
            $data['date'] = new Zend_Db_Expr('CURRENT_TIMESTAMP()');
            // On ajoute la date d'inscription
            $id = $this->getDbTable()->insert($data);
            $comment->setId($id);
            return $id;
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function fetchAll($select = null){
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Comment($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAllByArticle($idArticle){
        $where = $this->getDbTable()->getAdapter()->quoteInto('articleId = ?', $idArticle);
        $resultSet = $this->getDbTable()->fetchAll($where);
        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Comment($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAllRecentComments($dayAgo = 30){
        $select  = $this->getDbTable()->select()->where('TO_DAYS(NOW()) - TO_DAYS(date) <= ?', (int)$dayAgo)
                                                ->order('date DESC');
        return $this->fetchAll($select);
    }
}

