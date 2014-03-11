<?php

class Default_Form_Comment extends Application_Form_Abstract
{
    
    public function init()
    {
        $this->setMethod('post');
        $this->setDecorators($this->getElementStandardDecorator('form'));
        
        $this->addElement('textarea', 'content', array(
            //'label' => 'Votre commentaire',
            'placeholder' => 'Contenu du commentaire (Requis)',
            'class' => 'span12',
            'required' => true,
            'rows' => 5,
            'decorators' => $this->getElementStandardDecorator('textarea'),
        ));
                
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Commenter',
            'value' => 'submit',
            'class' => 'btn btn-success btn-submit btn-large',
            'decorators' => $this->getElementStandardDecorator('submit'),
        ));
        
        
    }
}

