<?php

class Application_View_Helper_MonthString extends Zend_View_Helper_Abstract
{
    public function monthString($number, $reduce = false)
    {
        $month = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
        $monthReduce = array('Jan', 'Fév', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Aout', 'Sept', 'Oct', 'Nov', 'Dec');
        if($reduce){
            return $monthReduce[$number-1];
        }
        return $month[$number-1];
    }
}