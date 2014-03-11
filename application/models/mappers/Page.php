<?php

class Application_Model_Mapper_Page extends Application_Model_Mapper_Abstract
{
    
    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_Page');
    }
    
    public function save(Application_Model_Page $page)
    {

        $data = array(
            'label'   => $page->getLabel(),
            'title' => $page->getTitle(),
            'content' => $page->getContent(),
        );
 
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $page->getId())) {
            // On ajoute la date d'inscription
            $data['date'] = new Zend_Db_Expr('CURRENT_TIMESTAMP()');
            $id = $this->getDbTable()->insert($data);
            $page->setId($id);
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($label, Application_Model_Page $page)
    {
        $where = $this->getDbTable()->select()->where('label LIKE ?', $label);
        $result = $this->getDbTable()->fetchRow($where);

        if (0 == count($result)) {
            return false;
        }
        $page->setOptions($result->toArray());
        return true;
    }
    
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Page($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }

}

