<?php
class My_Form_Decorator_Submit extends Zend_Form_Decorator_Abstract
{
    
    protected $_format = '<button type="submit" class="btn btn-primary">%s</button>';
 
    public function render($content)
    {
        $element = $this->getElement();
        $value   = htmlentities($element->getValue());
 
        $markup  = sprintf($this->_format, $value);
        
        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        switch ($placement) {
            case self::PREPEND:
                return $markup . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $markup;
        }
        
    }
}