<?php
/**
 * Validateur classique de pseudo
 * 
 * Le pseudo :
 *  - ne peut être de type numerique ou ne contenir que des nombres
 *  - dois être >= 2 et <= 20caractères
 * 
 */
class My_Validate_Pseudo extends Zend_Validate_Abstract{
    const MIN_LENGTH = 'min_length';
    const MAX_LENGTH = 'max_length';
    const NUMERIC = 'numeric';
    const CONFORME = 'conforme';
    
    public $options ;
    
    protected $_messageTemplates = array(
        self::MIN_LENGTH => "'%value%' doit avoir une longueur d'au moins 2 caractères",
        self::MAX_LENGTH => "'%value%' doit avoir une longueur maximum de 20 caractères",
        self::NUMERIC => "'%value%' ne peut être de type numerique ou ne contenir que des nombres",
        self::CONFORME => "'%value%' contient des caractères non conforme"
    );
    
    
    public function isValid($value)
    {
        $this->_setValue($value);

        $isValid = true;

        // Test entier
        if(is_numeric($value)){
            $this->_error(self::NUMERIC);
            $isValid = false;
        }
        
        if (strlen($value) < 2) {
            $this->_error(self::MIN_LENGTH);
            $isValid = false;
        }
        
        if (strlen($value) > 20) {
            $this->_error(self::MAX_LENGTH);
            $isValid = false;
        }
        
        if(!preg_match('`^([a-zA-Z0-9-_]{2,36})$`', $value)){
            $this->_error(self::CONFORME);
            $isValid = false;
        }

        return $isValid;
    }
}