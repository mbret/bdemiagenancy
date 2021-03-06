<?php

class Application_Model_Mapper_Category extends Application_Model_Mapper_Abstract{

    private static $defaultCategory = 'undefined';
    
    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_Category');
    }
    
    // Save
    public function save(Application_Model_Category $category){
        
        $data = array(
            'title'   => $category->getTitle(),
            'label'   => $category->getLabel(),
            'description' => $category->getDescription(),
            'parentId' => $category->getParentId()
        );
 
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $category->getId())) {
            // On ajoute la date d'inscription
            $id = $this->getDbTable()->insert($data);
            $category->setId($id);
            return $id;
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function getLabelById($id){
        if($id === null) return null;
        $row = $this->getDbTable()->fetchRow( $this->getDbTable()->select()
                ->from($this->getDbTable(), 'label')
                ->where('id = ?', (int)$id) );
        if(is_null($row)) return null;
        return $row->label;
    }

    public function find($id, Application_Model_Category $category){
        $result = $this->getDbTable()->find((int)$id);
        if (0 == count($result)){
            return false;
        }
        $row = $result->current();
        $category->setOptions($row->toArray());
        return true;
    }
    
    public function findByLabel($label, Application_Model_Category $category){
        $select  = $this->getDbTable()->select()->where('label = ?', $label);
        $row = $this->getDbTable()->fetchRow($select);
        if(is_null($row)){
            return false;
        }
        $category->setOptions($row->toArray());
        return true;
    }
    
    
    public function fetchAllToArray($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row){
            $entries[] = $row->toArray();
        }
        return $entries;
    }
    
    public function fetchAll($where = null, $order = null, $count = null, $offset = null){
        
        if($where instanceof Zend_Db_Select) $resultSet = $this->getDbTable()->fetchAll($where);
        else $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);

        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Category($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    /**
     * Requete toutes les categories qui ont un ou plusieurs articles
     * 
     * @todo Récuperer également les parents si ceux ci n'ont pas de post mais que leurs enfants si.
     * Sinon les enfants vont s'afficher en tant que parents
     * 
     * @param type $order
     * @param type $count
     * @param type $offset
     * @return type 
     */
    public function fetchAllPublished($order = null, $count = null, $offset = null){
        $select = $this->getDbTable()->select()->setIntegrityCheck(false);
        $select->from('category', array("*"));
        $select->join('article', 'article.categoryId = category.id', array());
        $select->group('category.id')
               ->order($order)
               ->limit($count , $offset);
        return $this->fetchAll($select);
    }
    
    /**
     * Return an array of category with a sub array of category for each category which has children
     * 
     * @param type $where
     * @param type $order
     * @param type $count
     * @param type $offset
     * @return type
     */
    public function fetchAllOrdered($where = null, $order = null, $count = null, $offset = null){
        return $this->buildOrdonnedCategoriesTree( $this->fetchAll($where, $order, $count, $offset) , null);
    }
    

    
    /**
     * 
     * @return \Application_Model_Category
     * @throws Exception
     */
    public static function getDefaultCategory(){
        $category = new Application_Model_Category();
        $mapper = new Application_Model_Mapper_Category();
        if(!$mapper->findByLabel(self::$defaultCategory, $category)){
            throw new Exception('Aucune catégorie par défaut spécifiée');
        }
        return $category;
    }
    
    /**
     * 
     * @param type $id
     * @return int number of row deleted
     */
    public function remove($id){
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', (int)$id);
        return $this->getDbTable()->delete($where);
    }
    
    
    /*====================================================================================================================================
     * 
     *                                          Private functions
     * 
     =====================================================================================================================================*/
    
    /**
     * Return fetched categories with their children as subarray
     * 
     * @param array $categories
     * @param type $parentId
     * @return array
     */
    private function buildOrdonnedCategoriesTree(array $categories, $parentId = null, $lvl = 0) {
        $branch = array();
        $i = 0;
        foreach ($categories as $category) {
            // We process only if this category should be added in the current branch
            if ($category->getParentId() === $parentId) {
                $children = $this->buildOrdonnedCategoriesTree($categories, $category->getId(), $lvl+1 ); // build the children tree of this category
                $branch[$i] = array(
                        'category' => $category,
                        'lvl' => $lvl
                    );
                if ($children) {
                    $branch[$i]['children'] = $children;
                }
                $i++;
            }
        }
        return $branch;
    }
}

