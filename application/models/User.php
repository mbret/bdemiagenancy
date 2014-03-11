<?php
/**
 * @warning : Toutes les valeurs sensibles doivent être initialisées dans le constructeur. (lié à la structure de l'application) Seule les variables optionnels peuvent être omises (about, ..)
 */
class Application_Model_User extends Application_Model_Abstract{
    
    protected $_id;
    protected $_firstname;
    protected $_name;
    protected $_username;
    protected $_password;
    protected $_birthday;
    protected $_joindate;
    protected $_mail;
    protected $_mailGravatar = "";
    protected $_website;
    protected $_about;
    protected $_roleId;
    
    protected $_preference_authorDisplayName;
    
    /**
     * Forcage de valeurs par défaut : joindate
     * 
     * @param array $options
     */
    public function __construct(array $options = null) {
        parent::__construct($options);
        
        if(is_null($this->_joindate)) $this->setJoindate(date('Y-m-d'));

    }
    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = (int)$id;
        return $this;
    }

    public function setBirthday($birthday) {
        $this->_birthday = $birthday;
        return $this;
    }

    public function setWebsite($website) {
        $this->_website = $website;
        return $this;
    }

    public function setAbout($about) {
        $this->_about = $about;
        return $this;
    }

        public function getFirstname() {
        return $this->_firstname;
    }
    
    public function getBirthday() {
        return $this->_birthday;
    }
    
    public function getWebsite() {
        return $this->_website;
    }

    public function getAbout() {
        return $this->_about;
    }
    
    public function setFirstname($firstname) {
        $this->_firstname = $firstname;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    public function getMail() {
        return $this->_mail;
    }

    public function setMail($mail) {
        $this->_mail = $mail;
        return $this;
    }

    public function getJoindate(){
        return $this->_joindate;
    }

    public function getRoleId(){
        return $this->_roleId;
    }
    
    public function setJoindate($joindate) {
        $this->_joindate = $joindate;
        return $this;
    }
    
    public function getMailGravatar() {
        return $this->_mailGravatar;
    }

    public function setMailGravatar($mailGravatar) {
        $this->_mailGravatar = $mailGravatar;
        return $this;
    }

    
    public function setRoleId($roleId) {
        $this->_roleId = (int)$roleId;
        return $this;
    }

    public function getRole(){
        $mapper = new Application_Model_Mapper_UserRole();
        return $mapper->findToArray($this->_roleId);
    }

    public function getGravatar(){
        return ("" === $this->_mailGravatar) ? $this->_mail : $this->_mailGravatar;
    }
    
    public function getPreference_authorDisplayName() {
        return $this->_preference_authorDisplayName;
    }

    public function setPreference_authorDisplayName($preference_authorDisplayName) {
        $this->_preference_authorDisplayName = $preference_authorDisplayName;
    }

    
    public function getAuthorDisplayName(){
        switch( $this->_preference_authorDisplayName ){
            case 0 : return $this->_username;
                break;
            case 1 : return $this->_name;
                break;
            case 2 : return $this->_firstname;
                break;
            case 3 : return $this->_name . ' ' . $this->_firstname;
                break;
            case 4 : return $this->_firstname . ' ' . $this->_name;
                break;
            default : return $this->_username;
                break;
        }
    }
    
    /**
     * Retourne l'objet sous forme de tableau pour le stockage persistant
     */
    public function toStorage(){
        return (object)$this->toArray();
    }
    
    public function toArray(){
        $role = $this->getRole();

        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'firstname' => $this->getFirstname(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'about' => $this->getAbout(),
            'birthday' => $this->getBirthday(),
            'joinDate' => $this->getJoinDate(),
            'website' => $this->getWebsite(),
            'gravatar' => $this->getGravatar(),
            'mailGravatar' => $this->_mailGravatar,
            'mail' => $this->getMail(),
            'role' => $role, // Utile ici pour éviter des futurs requêtes inutiles
            'roleId' => $role['id'],
        );
    }

    public function toView(){
        $class =  new stdClass();
        $class->id = $this->_id;
        $class->username = $this->_username;
        $class->gravatar = $this->getGravatar();
        $class->name = $this->_name;
        $class->firstname = $this->_firstname;
        $class->mail = $this->_mail;
        $class->role = null;
        $class->authorDisplayName = $this->getAuthorDisplayName();
        return $class;
    }
}

