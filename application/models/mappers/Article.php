<?php

class Application_Model_Mapper_Article extends Application_Model_Mapper_Abstract{

    protected $_tableArticleHasTag = 'article_has_tag';
    protected $_tableArticleEdited = 'article_edited';
    
    public function __construct(){
        parent::__construct();
        $this->setDbTableName('Application_Model_DbTable_Article');
    }
    
    public function save(Application_Model_Article $article){

        $data = array(
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'isPublished' => $article->getIsPublished(),
            'putForward' => $article->getPutForward(),
            'userId' => $article->getUserId(),
            'categoryId' => $article->getCategoryId(),
            'forwardTitle' => $article->getForwardTitle(),
            'forwardContent' => $article->getForwardContent(),
        );
 
        // Si il n'ya pas d'id c'est que l'on veux ajouter une entrée
        if (null === ($id = $article->getId())){
            // On ajoute la date d'inscription
            $data['date'] = new Zend_Db_Expr('CURRENT_TIMESTAMP()');
            $id = $this->getDbTable()->insert($data);
            $article->setId($id);
        }
        else{
            return $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function saveEdition($articleId, $userId){
        $data = array(
            'articleId'   => $articleId,
            'userId' => $userId,
            'date' => new Zend_Db_Expr('CURRENT_TIMESTAMP()'),
        );
 
        return $this->getDbTable()->getAdapter()->insert($this->_tableArticleEdited, $data);
    }
    
    public function getEditionsDates($articleId, $order = 'date ASC'){
        $select = $this->getDbTable()->select()->setIntegrityCheck(false)
                                               ->from($this->_tableArticleEdited, array("date"))
                                               ->where('articleId = ?', (int)$articleId)
                                               ->order($order);
        $entries = array();
        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($select);
        foreach ($resultSet as $row){
            $entries[] = $row['date'];
        }
        return $entries;
    }
    
    public function changeUser($userId, $newUserId){
        $data = array(
            'userId' => (int)$newUserId
        );
        return $this->getDbTable()->update($data, array('userId = ?' => (int)$userId));
    }
    
    /**
     * Sauvegarde la liste de tag correspondant à l'article
     * 
     * Supprime les anciens tags avant l'ajout
     * 
     * @param type $articleId
     * @param type $userId
     * @return array tableau de tags inseré
     */
    public function saveTag($tags, $articleId)
    {
        $result = array();
        // On efface les anciens tags
        $this->getDbTable()->getAdapter()->delete($this->_tableArticleHasTag, 'articleId = ' . $articleId);
        // On ajoute les tags
        if(!is_null($tags)){
            foreach($tags as $tag){

                $data = array(
                    'articleId' => (int)$articleId,
                    'tagId' => (int)$tag,
                );
                $this->getDbTable()->getAdapter()->insert($this->_tableArticleHasTag, $data);
                $result[] = array(
                    'id' => $tag,
                );
            }
        }
        
        return $result;
    }
    

    public function find($id, Application_Model_Article $article)
    {
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $article->setOptions($row->toArray());
        return true;
    }
    
    
    /**
     *
     * @param type $id
     * @return int le nombre de lignes mise à jour 
     */
    public function putInTrash($id){
        $data = array(
            'isPublished' => false,
            'putForward' => false,
            'inTrash'   => true,
        );
        return $this->getDbTable()->update($data, array('id = ?' => $id));
    }
    
    /**
     *
     * @param type $id
     * @return int le nombre de lignes mise à jour 
     */
    public function restore($id){
        $data = array(
            'inTrash'   => false,
        );
        return $this->getDbTable()->update($data, array('id = ?' => $id));
    }
    
    
    
    public function fetchAll($where = null, $order = null, $count = null, $offset = null){
        
        if($where instanceof Zend_Db_Select){
            $resultSet = $this->getDbTable()->fetchAll($where);
        }
        else{
            $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        }
        $entries   = array();
        foreach ($resultSet as $row){
            $entry = new Application_Model_Article($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAllWithTrash(){
        return $this->fetchAll(null, null, null, null, true);
    }
    
    public function fetchAllInTrash(){
        return $this->fetchAll("inTrash = 1");
    }
    
    // Retourne les articles seuelement publiés
    public function fetchAllPublished($order = null, $count = null, $offset = null){
        return $this->fetchAll("isPublished = 1", $order, $count, $offset);
    }
    
    // Sécurisé
    public function fetchAllPublishedByDate($year, $month, $order = null, $count = null, $offset = null){
        return $this->fetchAll("isPublished = 1 AND YEAR(date) = " . (int)$year . " AND MONTH(date) = " . (int)$month, $order, $count, $offset);
    }
    
    // Articles pour une categorie donnée
    public function fetchAllPublishedByCategory($categoryId, $order = null, $count = null, $offset = null){
        return $this->fetchAll("isPublished = 1 AND categoryId = " . (int)$categoryId, $order, $count, $offset);
    }
    
    // Articles pour un tag donné
    public function fetchAllPublishedByTag($tagId, $order = null, $count = null, $offset = null){
        $select = $this->getDbTable()->select()->setIntegrityCheck(false)
                                               ->from('article', array("*"))
                                               ->joinLeft('article_has_tag', 'article_has_tag.articleId = article.id')
                                               ->where('article_has_tag.tagId = ?', (int)$tagId)
                                               ->where('isPublished = ?', '1')
                                               ->order($order)
                                               ->limit($count , $offset);
        return $this->fetchAll($select);
    }
    
    // Retourne les articles publiés dans l'ordre d'ajout
    public function fetchLastPublished($count = null, $offset = null){
        return $this->fetchAllPublished("date DESC", $count, $offset);
    }
    
    // Retourne les derniers articles en prenant en compte seulement les articles mis en avant
    public function fetchLastForward($count = null, $offset = null){
        return $this->fetchAll("isPublished = 1 AND putForward = 1", "date DESC", $count, $offset);
    }
    
    /**
     * Fonction qui retourne tous les mois de publication par année avec le nombre d'articles associés (publiés)
     * month year nb
     * -     -    -
     * -     -    -
     * @return type
     */
    public function getArchives(){
        $select = $this->getDbTable()->select();
        $select->from(array('article'), array('MONTH(date) as month', 'YEAR(date) as year', 'COUNT(id) as nb'))
               ->where("isPublished = 1")
               ->group(array('year', 'month'));
        return $this->getDbTable()->fetchAll($select);
    }
    
    
    public function countAll($where = null, $order = null, $count = null, $offset = null){
        if(is_null($where)){
            $where = $this->getDbTable()->select()->from($this->getDbTable(),
                                                            array("COUNT(id) as count"))
                                                  ->where('inTrash = ?', '0');
        }
        $rows = $this->getDbTable()->fetchAll($where);
        return (int)$rows[0]['count'];
    }
    public function countAllInTrash(){
        $where = $this->getDbTable()->select()->from($this->getDbTable(),
                                                            array("COUNT(id) as count"))
                                              ->where('inTrash = ?', '1');
        return $this->countAll($where);
    }
    
    /**
     * Remplace l'id de categorie $categoryId par $newCategoryId pour les articles concernés
     * 
     * @param type $categoryId
     * @param type $newCategoryId
     * @return type
     */
    public function changeCategory($categoryId, $newCategoryId){
        $data = array(
            'categoryId' => (int)$newCategoryId,
        );
 
        return $this->getDbTable()->update($data, array('categoryId = ?' => (int)$categoryId));
    }
}

