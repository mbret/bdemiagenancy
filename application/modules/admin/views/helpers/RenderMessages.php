<?php

/**
 * Render message according to predefined template
 */
class Admin_View_Helper_RenderMessages extends Zend_View_Helper_Abstract
{
    
    private $_templates = array(
        'error' => '<div class="alert alert-block alert-error">
                                    <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                                    <h4 class="alert-heading">Une erreur est survenue !</h4>%s
                                </div>',
        'warning' => '<div class="alert alert-block alert-warning">
                                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                                <h4 class="alert-heading">Attention !</h4>%s
                            </div>',
        'alertAdmin' => '<div class="alert alert-block alert-info">
                                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                                <h4 class="alert-heading">Important !</h4>%s
                            </div>',
        'success' => '<div class="alert alert-block alert-success">
                                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                                <h4 class="alert-heading">Succès !</h4>%s
                            </div>'
    );

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function renderMessages($lib){
        $helper = $this->view->getHelper('flashMessenger');
        if($helper->hasLabel( $lib )){
            $content = '';
            foreach ( $helper->getContent( $lib ) as $message) {
                $content .= sprintf('<p>%s</p>',$message);
            }
            return sprintf($this->_templates[$lib], $content);
        }
        return null;
    }
}