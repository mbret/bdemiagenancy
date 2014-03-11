<?php

class Application_View_Helper_Messages extends Zend_View_Helper_Abstract{
    
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
    
    public function messages($lib){
        if(isset($this->view->messages[$lib])){
            return $this->view->messages[$lib];
        }
        else{
            return false;
        }
    }
}