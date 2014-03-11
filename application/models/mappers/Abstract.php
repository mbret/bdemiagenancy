<?php

abstract class Application_Model_Mapper_Abstract
{

    protected $_dbTable;
    protected $_dbTableName;
    
    public function __construct(){
        
    }
    
    public function beginTransaction(){
        $this->getDbTable()->getDefaultAdapter()->beginTransaction();
    }
    public function commit(){
        $this->getDbTable()->getDefaultAdapter()->commit();
    }
    public function rollback(){
        $this->getDbTable()->getDefaultAdapter()->rollback();
    }
    
    public function setDbTableName($name)
    {
        $this->_dbTableName = $name;
    }
    
    public function getDbTableName() {
        return $this->_dbTableName;
    }

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbTableName);
        }
        return $this->_dbTable;
    }
    
    
}

