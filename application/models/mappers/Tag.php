<?php

class Application_Model_Mapper_Tag extends Application_Model_Mapper_Abstract{

    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_Tag');
    }
    
    public function save(Application_Model_Tag $tag)
    {
        $data = array(
            'label'   => $tag->getLabel(),
            'description' => $tag->getDescription(),
        );
 
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $tag->getId())) {
            // On ajoute la date d'inscription
            $id = $this->getDbTable()->insert($data);
            $tag->setId($id);
            return $id;
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($id, Application_Model_Tag $tag){
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))  return false;
        $row = $result->current();
        $tag->setOptions($row->toArray());
        return true;
    }
    public function findByLabel($label, Application_Model_Tag $tag){
        $select  = $this->getDbTable()->select()->where('label = ?', $label);
        $row = $this->getDbTable()->fetchRow($select);
        if(is_null($row)) return false;
        $tag->setOptions($row->toArray());
        return true;
    }
    
    public function fetchAll($where = null){
        $resultSet = $this->getDbTable()->fetchAll($where);
        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Tag($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAllToArray()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        return $resultSet;
    }
    
    public function fetchAllByArticle($idArticle){
        $select = $this->getDbTable()->select()->setIntegrityCheck(false);
        $select->from('tag', array("*"));
        $select->joinLeft('article_has_tag', 'article_has_tag.tagId = tag.id');
        $select->group('tag.id');
        $select->where("article_has_tag.articleId = " . $idArticle . "");
        return $this->fetchAll($select);
    }
    
    public function remove($id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
        return $this->getDbTable()->delete($where);
    }

    
}

