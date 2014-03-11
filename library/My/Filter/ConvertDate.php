<?php


class My_Filter_ConvertDate implements Zend_Filter_Interface
{
    /**
     * format d'entrée
     *
     * @var string
     */
    public $fromFormat ;
 
    /**
     * format de sortie
     *
     * @var string
     */
    public $toFormat ;
 
    /**
     * contantes de formats de date 
     * 
     * Il est bien sûr possible d'en créer d'autre à loisir
     */
    const DATETIME = 'yyyy-MM-dd HH:mm:ss';
    const DATE = 'yyyy-MM-dd';
    const MYSQL_DATE = 'yyyy-MM-dd';
    const SIMPLE_DATE = 'dd/MM/yyyy';
    const FRENCH_SIMPLE_DATE = 'dd/MM/yyyy';
    const ENGLISH_SIMPLE_DATE = 'MM/dd/yyyy';
    const FULL_DATE = 'dd/MM/yyyy HH:mm:ss';
    const FULL_DATE_NO_SEC = 'dd/MM/yyyy HH:mm';
    const TIME = 'HH:mm:ss' ;
    const HOUR = 'HH:mm' ;
    const YEAR = 'yyyy' ;
 
    /**
     * constructeur
     *
     * @param string|array $fromFormat
     * @param string $toFormat
     */
    public function __construct ( $fromFormat , $toFormat = NULL )
    {
 
    /**
     * cas d'appel dans un fichier ini 
     */
    if ( is_array ( $fromFormat ) ) {
 
        $this -> fromFormat = $fromFormat[ 'from' ] ;
        $this -> toFormat = $fromFormat[ 'to' ] ;
    }
 
    /**
     * cas d'appel aux constantes 
     */ else {
 
        $this -> fromFormat = $fromFormat ;
        $this -> toFormat = $toFormat ;
    }
 
    }
 
    /**
     * formate la date
     *
     * @param string|integer $date
     * @return string|integer
     */
    public function formatConverter ( $date )
    {
    /**
     * instanciation d'un objet date 
     */
    $dateObject = new Zend_Date() ;
 
    /**
     * on lui passe éventuellement une locale du registre 
     */
    if ( Zend_Registry::isRegistered ( 'Zend_Locale' ) ) {
 
        $dateObject -> setLocale ( Zend_Registry::get ( 'Zend_Locale' ) ) ;
    }
 
    /**
     * la date n'est pas valide, on retourne NULL
     * (on pourrait lancer une exception à la place)
     */
    if ( ! $dateObject -> isDate ( $date , $this -> fromFormat ) 
               && ! $dateObject -> isDate ( $date , $this -> toFormat ) ) {
 
        return NULL ;
    }
 
    /**
     * elle est valide 
     */
 
    /**
     * on assigne la date à l'objet Zend_Date 
     */
 
    /**
     * dans le cas où la date est déjà dans le format de sortie 
     */
    if ( $dateObject -> isDate ( $date , $this -> toFormat ) ) {
 
        $dateObject -> setDate ( $date , $this -> toFormat ) ;
    }
 
    /**
     * sinon, nous avons déjà vérifié que la date était soit au format d'entrée, soit au format de sortie 
     */ else {
 
        $dateObject -> setDate ( $date , $this -> fromFormat ) ;
    }
 
    /**
     * et on retourne la chaîne date formatée 
     */
    return $dateObject -> toString ( $this -> toFormat ) ;
 
    }
 
    /**
     * filtre
     *
     * @param mixed $date
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     * @see Zend_Filter_Interface::filter()
     */
    public function filter ( $date )
    {
 
    return $this -> formatConverter ( $date ) ;
 
    }
  
}