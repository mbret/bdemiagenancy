<?php

class Application_Model_Mapper_User extends Application_Model_Mapper_Abstract
{

    protected $_default = "member";
    private static $_defaultUser = 'user';
    
    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_User');
    }
    
    // Ajoute l'utilisateur ou le met à jour si il est doté d'un id
    public function save(Application_Model_User $user){
        $data = array(
            'mail'   => $user->getMail(),
            'mailGravatar' => $user->getMailGravatar(),
            'firstname' => $user->getFirstname(),
            'name' => $user->getName(),
            'password' => $user->getPassword(),
            'birthday' => $user->getBirthday(),
            'website' => $user->getWebsite(),
            'about' => $user->getAbout(),
            'joindate' => $user->getJoinDate(),
            'roleId' => $user->getRoleId(),
            'username' => $user->getUsername(),
        );

        if(!is_null($authorDisplayName = $user->getPreference_authorDisplayName())){
            $data['preference_authorDisplayName'] = $authorDisplayName;
        }
        
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $user->getId())){
            $id = $this->getDbTable()->insert($data);
            $user->setId($id);
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    /**
     * Fonction obligatoire pour mettre à jour la valeur roleId d'un utilisateur
     * @param Application_Model_User $user
     * @return type 
     */
    /*public function updateRoleId(Application_Model_User $user)
    {
        $data = array(
            'roleId'   => $user->getRoleId(),
        );
        return $this->getDbTable()->update($data, array('id = ?' => $user->getId()));
    }*/
    
    /**
     * Fonction obligatoire pour mettre à jour le pseudo
     * @param Application_Model_User $user
     * @return type 
     */
    /*public function updateUsername(Application_Model_User $user)
    {
        $data = array(
            'username'   => $user->getUsername(),
        );
        return $this->getDbTable()->update($data, array('id = ?' => $user->getId()));
    }*/
    
    public function find($id, Application_Model_User $user)
    {
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $user->setOptions($row->toArray());
        return true;
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_User($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    /**
     * Remove the given user
     * The user's comment are set to userId -> null
     * 
     * @param type $id
     * @return type le nombre de lignes supprimés
     */
    public function remove($id)
    {
        $table = $this->getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $id);
        return $this->getDbTable()->delete($where);
    }
    
    public function findByUsername($username, Application_Model_User $user){
        $select  = $this->getDbTable()->select()->where('username = ?', $username);
        $row = $this->getDbTable()->fetchRow($select);
        if(is_null($row)){
            return false;
        }
        $user->setOptions($row->toArray());
        return true;
    }
    
    public static function getDefaultUser(){
        $user = new Application_Model_User();
        $mapper = new Application_Model_Mapper_User();
        if(!$mapper->findByUsername(self::$_defaultUser, $user)){
            throw new Exception('Aucun utilisateur par défaut spécifié');
        }
        return $user;
    }
}

