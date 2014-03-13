<?php

class ExtraController extends Zend_Controller_Action{

    
    public function postDispatch() {
        parent::postDispatch();
        
        $this->_helper->viewRenderer->setRender('default-container');
    }
    
    
    /**
     * Redirige vers accueil
     */
    public function indexAction(){
        $this->_helper->_redirector('index', 'index', 'default');
    }
    
    
    /**
     * Méthode qui gère la creation et la gestion du formulaire
     */
    public function contactAction(){
        $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>Nous contacter</a></li>');
        $form = new Default_Form_Contact();

        // Une fois le formulaire posté on redirige vers la page de contact
        if ($this->getRequest()->isPost()){
            
            $formData = $this->getRequest()->getPost();
            
            if ( ! $form->isValid($formData)){
                $this->_helper->flashMessenger(array('warning' => "Il y'a des erreurs dans le formulaire"));
            }
            else{    
                /**
                 * Configuration MAIL
                 */
                $config = $this->getInvokeArg('bootstrap')->getResource('config');
                $mailConfig = $config->mail;
                $values = $form->getValues();
                $subject = ($values['subject'] == "") ? "Demande de contact" : $values['subject'];

                // Mail from
                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyText($values['content'], 'UTF-8', Zend_Mime::ENCODING_8BIT )
                     ->setFrom($values['email'], $values['name'])
                     ->addTo($mailConfig->contactAdress, $mailConfig->contactName)
                     ->setSubject($subject)
                     ->send();

                // Mail reply
                $reply = new Zend_Mail('UTF-8');
                $reply->setBodyText("Merci de nous avoir contacté.", 'UTF-8', Zend_Mime::ENCODING_8BIT )
                     ->setFrom($mailConfig->contactAdress, $mailConfig->contactName)
                     ->addTo($values['email'], $values['name'])
                     ->setSubject($subject)
                     ->send();
                
                $this->_helper->flashMessenger(array('success' => "Votre message a bien été pris en compte. Merci de nous avoir contacté."));
                $form->reset();
            }
        }
        
        $this->view->form = $form;
        $this->view->placeholder('articleTitle')->set('Vous cherchez à nous joindre ?');
    }
    
    /**
     * Information about BDE
     */
    public function aboutAction(){
        $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>A propos de nous</a></li>');
        $this->view->placeholder('articleTitle')->set('Et si nous vous parlions de nous !');
    }
    
    /**
     * Mentions légales
     */
    public function legalAction(){
        $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>Mentions légales</a></li>');
        $this->view->placeholder('articleTitle')->set('Mentions légales');
    }
    
    /**
     * Status
     */
    public function statutsAction(){
        $this->view->placeholder('breadcrumb')->append('<li><span class="divider">/</span>Statuts</a></li>');
        $this->view->placeholder('articleTitle')->set('Statuts');
    }
    
}

