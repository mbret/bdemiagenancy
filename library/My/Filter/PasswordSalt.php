<?php

/**
 * My_Filter_PasswordSalt est un filtre qui applique à une chaine donnée un salage avec un element donnée en parametre et une chaine maison
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   
 * @package    
 * @copyright  
 * @license    
 * @version    
 */

class My_Filter_PasswordSalt implements Zend_Filter_Interface
{
    const APPEND = "Salter c'est bien mais saler c'est mieux !";
    private $_prepend;
            
    public function __construct($value = '') {
        $this->_prepend = $value;
    }
    
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns (int) $value
     *
     * @param  string $value
     * @return integer
     */
    public function filter($value)
    {
        //var_dump($this->_prepend . $value . self::APPEND);
        return md5($value . self::APPEND);
    }
}