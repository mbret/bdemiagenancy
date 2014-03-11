<?php

class My_Form_Decorator_Html extends Zend_Form_Decorator_Abstract
{
  public function render($content) {
    $placement = $this->getPlacement();
    $output = $this->_options['html'];
    //var_dump($content);
    switch ($placement) {
      case self::APPEND:
        return $content . $output;
        break;
      case self::PREPEND:
        return $output . $content;
        break;
    }
  }
}

?>
