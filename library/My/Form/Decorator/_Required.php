<?php

class My_Form_Decorator_Required extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<span class="help-inline required">(obligatoire)</span>';
 
    public function render($content)
    {
 
        //$markup  = sprintf($this->_format, $content);

        return $this->_format;
    }
}