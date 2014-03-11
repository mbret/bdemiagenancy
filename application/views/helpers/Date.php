<?php

class Application_View_Helper_Date extends Zend_View_Helper_Abstract{
    public function date($date, $format = 'dd-MM-yyyy'){
        if(is_null($date)) return;
        $date = new Zend_Date($date, $format);
        return $date->toString($format);
    }
}