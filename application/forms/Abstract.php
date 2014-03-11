<?php

abstract class Application_Form_Abstract extends Zend_Form{

    protected $_noSuccess;
    
    public function __construct($options = null) {
        parent::__construct($options);
        $this->_noSuccess = true;
    }
    
    public function init(){
        $this->addPrefixPath('My_Form_Element','My/Form/Element/',Zend_Form::ELEMENT);
        $this->addElementPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator');
    }
    
    public function getElementStandardDecorator($key, $required = false){
//        trigger_error("METHODE getElementStandardDecorator() DEPRECATED " . $key, E_USER_DEPRECATED);
        $decorators = array();
        //if($required) $decorators[] = new My_Form_Decorator_Required();
        if($required) $key = $key . "_required";
        
        switch($key){
            case 'radio' :
                $decorators2 = array(
                    array('ViewHelper', array('placement' => 'prepend')),
                    array(array('capsule' => 'HtmlTag'),array('tag' => 'div')),
                    array('Errors',array('class' => 'help-block errors')),
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    //array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
            
            case 'multiSelect' :
                // Omission volontaire du break
            case 'select' :
                // Omission volontaire du break
            case 'textarea' :
                // Omission volontaire du break
            case 'password' :
                // Omission volontaire du break
            case 'text' :
                $decorators2 = array(
                    array('ViewHelper', array('placement' => 'prepend')),
                    array(array('capsule' => 'HtmlTag'),array('tag' => 'div')),
                    array('Errors',array('class' => 'help-block errors')),
                    array('Description', array('tag' => 'p', 'class' => 'help-block')), // Précèdé de sa déscription
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
            
            case 'text_required' :
                $decorators2 = array(
                    array(array('icon-star-empty' => 'HtmlTag'),array('tag' => 'i', 'class' => 'icon-star-empty')), // <span class="add-on
                    array(array('add-on' => 'HtmlTag'),array('tag' => 'span', 'class' => 'add-on')), // <span class="add-on
                    array('ViewHelper', array('placement' => 'prepend')),
                    array('Description', array('tag' => 'p', 'class' => 'help-block')), // Précèdé de sa déscription
                    array(array('input-append' => 'HtmlTag'),array('tag' => 'div', 'class' => 'input-append')), // <div class="input-append
                    array('Errors',array('class' => 'help-block errors')),
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
            
            case 'password_required' :
                $decorators2 = array(
                    array(array('icon-star-empty' => 'HtmlTag'),array('tag' => 'i', 'class' => 'icon-star-empty')), // <span class="add-on
                    array(array('add-on' => 'HtmlTag'),array('tag' => 'span', 'class' => 'add-on')), // <span class="add-on
                    array('ViewHelper', array('placement' => 'prepend')),
                    array('Description', array('tag' => 'p', 'class' => 'help-block')), // Précèdé de sa déscription
                    array(array('input-append' => 'HtmlTag'),array('tag' => 'div', 'class' => 'input-append')), // <div class="input-append
                    array('Errors',array('class' => 'help-block errors')),
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
            
            case 'file' :
                exit();
                $decorators2 = array(
                    array('FormFile', array('placement' => 'prepend')),
                    array(array('capsule' => 'HtmlTag'),array('tag' => 'div')),
                    array('Errors',array('class' => 'help-block errors')),
                    array('Description', array('tag' => 'p', 'class' => 'help-block')), // Précèdé de sa déscription
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
                
                
            case 'checkbox' :
                $decorators2 = array(
                    array('ViewHelper', array('placement' => 'prepend')),
                    array(array('capsule' => 'HtmlTag'),array('tag' => 'div')),
                    //array('Errors',array('class' => 'help-block errors')),
                    array('Description', array('tag' => 'p', 'class' => 'help-block')), // Précèdé de sa déscription
                    array(array('labelCheckbox' => 'HtmlTag'),array('tag' => 'label', 'class' => 'checkbox')),
                    array(array('controls' => 'HtmlTag'),array('tag' => 'div', 'class' => 'controls')), // Dans un <div class="controls
                    array('Label', array('class' => 'control-label')), // Ajouté à un label
                    array(array('control-group' => 'HtmlTag'),array('tag' => 'div', 'class' => 'control-group')), // Dans un <div class="control-group
                );
                break;
                
            case 'submit' :
                $decorators2 = array(
                    'ViewHelper',
                    array(array('form-actions' => 'HtmlTag'),array('tag' => 'div', 'class' => 'form-actions')), // Dans un <div class="form-actions
                );
                break;
            
            case 'form' :
                $decorators2 = array(
                    'FormElements', // Les elements
                    array(array('data'=>'HtmlTag'),array('tag' => 'fieldset')), // Dans un fieldset
                    'Form', // Dans le formulaire    
                );
                break;
            default :
                $decorators2 = array();
                break;
        }
        
        return array_merge($decorators, $decorators2);
    }
    
    
    public function isValid($data) {
        $return = parent::isValid($data);
        
        // Ajoute une classe en plus pour les elements en erreur, success etc
        foreach ($this->getElements() as $elt) {
            $decorator = $elt->getDecorator('control-group');
            if(false != $decorator){
                if($elt->hasErrors()){
                    $decorator->setOption('class',$decorator->getOption('class') . ' error');
                }
                else{
                    // Style spécial pour les elements validé
                    if( !($elt instanceof Zend_Form_Element_Password) 
                            && !is_null($elt->getValue()) 
                            && ('' != $elt->getValue()) 
                            && !$this->_noSuccess){
                        $decorator->setOption(
                                'class',
                                $decorator->getOption('class') . ' success');
                        }
                }
            }
        }
        
        return $return;
    }
    
    public function isValidPartial(array $data) {
        $return =  parent::isValidPartial($data);
        
        // Ajoute une classe en plus pour les elements en erreur, success etc
        foreach ($this->getElements() as $elt) {
            $decorator = $elt->getDecorator('control-group');
            if(false != $decorator){
                if($elt->hasErrors()){
                    $decorator->setOption('class',$decorator->getOption('class') . ' error');
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Crée un element html
     * @param type $name
     * @param type $value
     */
    public function addHtml($name, $value){
        $elt = new My_Form_Element_Html($name,array(
            'value' => $value,
            'ignore'   => true,
        ));
        //$elt->getDecorator('ViewHelper')->setEspace(false);
        $this->addElement($elt);
    }

}

