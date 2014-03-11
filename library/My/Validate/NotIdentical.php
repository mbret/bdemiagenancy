<?php
/**
 * Simple Zend validator which make the inverse of Zend_Validate_Identical
 */
class My_Validate_NotIdentical extends Zend_Validate_Abstract{
    
    protected $_different;

    const SAME = 'same';

    protected $_messageTemplates = array(
        self::SAME => "'%value%' doit être different de '%different%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'different' => '_different'
    );
    
    public function __construct($different, $strict = false){
        $this->setDifferent($different);
        $this->strict = $strict;
    }


    public function isValid($value)
    {
        if (($this->strict && ($value === $this->getDifferent())) || (!$this->strict && ($value == $this->getDifferent()))) {
            $this->_error(self::SAME);
            return false;
        }

        return true;
    }
    
    public function getDifferent() {
        return $this->_different;
    }

    public function setDifferent($different) {
        $this->_different = $different;
    }


}
