<?php
/**
 * Validateur de mot de passe.
 * 
 * @link http://blog.web82.net/2011/09/zend-framework-validateur-de-mot-de.html
 * 'uppercase' => majuscule obligatoire
 * 'lowercase' => minuscule obligatoire
 * 'digit' => chiffre obligatoire
 * 'min' => nombre de caractères minimum
 */
class My_Validate_Password extends Zend_Validate_Abstract{
    /**
     * constantes
     *
     */
    const NOUPPERCASE = 'noUppercase' ;
    const NOLOWERCASE = 'noLowercase' ;
    const NODIGIT = 'noDigit' ;
    const TOOSHORT = 'tooShort' ;
 
    /**
     * options
     *
     * @var array|Zend_Config|null
     */
    public $options ;
    /**
     * @var array
     */
    protected $_messageVariables = array (
    'min' => '_min' ,
        ) ;
    /**
     *
     * @var integer
     */
    protected $_min ;
    /**
     * variable des messages
     *
     * @var array
     */
    protected $_messageTemplates = array (
    self :: NOUPPERCASE => 'Votre mot de passe doit contenir au moins une majuscule' ,
    self :: NOLOWERCASE => 'Votre mot de passe doit contenir au moins une minuscule' ,
    self :: NODIGIT => 'Votre mot de passe doit contenir au moins un chiffre' ,
    self :: TOOSHORT => 'Votre mot de passe doit faire plus de %min% caractères'
        ) ;
 
    /**
     * constructeur
     *
     * clés possibles pour les options (activées avec la valeur 1) :
     *
     * 'uppercase' => majuscule obligatoire
     * 'lowercase' => minuscule obligatoire
     * 'digit' => chiffre obligatoire
     * 'min' => nombre de caractères minimum
     *
     * @param array|Zend_Config|null $options
     */
    public function __construct ( $options=NULL )
    {
    if ( $options instanceof Zend_Config ) {
 
        $options = $options -> toArray () ;
    }
 
    if ( is_array ( $options ) ) {
 
        $this -> setOptions ( $options ) ;
    }
 
    }
 
    /**
     * validation
     *
     * @param string $string
     * @return bool
     */
    public function isValid ( $string )
    {
    $return = array ( ) ;
 
    if ( isset ( $this -> options[ 'lowercase' ] ) && $this -> options[ 'lowercase' ] == 1 ) {
        if ( ! preg_match ( '`^.*[a-z]+.*$`' , $string ) ) {
            $this -> _error ( self :: NOLOWERCASE ) ;
            $return[ ] = FALSE ;
        }
    }
 
    if ( isset ( $this -> options[ 'uppercase' ] ) && $this -> options[ 'uppercase' ] == 1 ) {
        if ( ! preg_match ( '`^.*[A-Z]+.*$`' , $string ) ) {
            $this -> _error ( self :: NOUPPERCASE ) ;
            $return[ ] = FALSE ;
        }
    }
 
    if ( isset ( $this -> options[ 'digit' ] ) && $this -> options[ 'digit' ] == 1 ) {
 
        if ( ! preg_match ( '`^.*[0-9]+.*$`' , $string ) ) {
            $this -> _error ( self :: NODIGIT ) ;
            $return[ ] = FALSE ;
        }
    }
 
    if ( isset ( $this -> options[ 'min' ] ) && is_numeric ( $this -> options[ 'min' ] ) ) {
 
        $this -> _min = $this -> options[ 'min' ] ;
 
        $strlenValidator = new Zend_Validate_StringLength ( array ( 'min' => $this -> options[ 'min' ] ) ) ;
 
        if ( ! $strlenValidator -> isValid ( $string ) ) {
            $this -> _error ( self :: TOOSHORT , $this -> options[ 'min' ] ) ;
            $return[ ] = FALSE ;
        }
    }
 
    if ( in_array ( FALSE , $return ) ) {
 
        return FALSE ;
    }
 
    return TRUE ;
 
    }
 
    /**
     *
     * @param array $options
     */
    public function setOptions ( $options )
    {
    $this -> options = $options ;
 
    }
}