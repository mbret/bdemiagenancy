<?php

class My_Form_Decorator_FormAction extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="form-actions">%s</div>';
 
    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = htmlentities($element->getLabel());
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue());
 
        $markup  = sprintf($this->_format, $content);

        return $markup;
    }
}