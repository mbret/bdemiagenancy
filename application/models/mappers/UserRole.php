<?php

class Application_Model_Mapper_UserRole extends Application_Model_Mapper_Abstract{

    // Label
    private static $_defaultRole = "member";
    
    public function __construct() {
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_UserRole');
    }
 
    public function fetchAllToArray($where = null) {
        $resultSet = $this->getDbTable()->fetchAll($where);
        return $resultSet;
    }
    
    public function findToArray($id){
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        
        return $result->current()->toArray();
    }
    
    public function find($id, Application_Model_UserRole $userRole){
        $result = $this->getDbTable()->find((int)$id);
        if (0 == count($result))  return false;
        $row = $result->current();
        $userRole->setOptions($row->toArray());
        return true;
    }
    
    public function findByLabel($label, Application_Model_UserRole $userRole){
        $select = $this->getDbTable()->select()->where('label = ?', $label);
        $row = $this->getDbTable()->fetchRow($select);
        if(is_null($row)){
            return false;
        }
        $userRole->setOptions($row->toArray());
        return true;
    }
    
    public static function getDefaultRole(){
        $role = new Application_Model_UserRole();
        $mapper = new Application_Model_Mapper_UserRole();
        if( ! $mapper->findByLabel(self::$_defaultRole, $role)){
            throw new Exception('Aucun role défini par défaut');
        }
        return $role;
    }
}

