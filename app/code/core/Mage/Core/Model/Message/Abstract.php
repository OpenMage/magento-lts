<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract message model
 *
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Message_Abstract
{
    protected $_type;

    protected $_code;

    protected $_class;

    protected $_method;

    protected $_identifier;

    protected $_isSticky = false;

    /**
     * Mage_Core_Model_Message_Abstract constructor.
     * @param string $type
     * @param string $code
     */
    public function __construct($type, $code = '')
    {
        $this->_type = $type;
        $this->_code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->getCode();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param  string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * @param  string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->getType() . ': ' . $this->getText();
    }

    /**
     * Set message identifier
     *
     * @param  string                           $id
     * @return Mage_Core_Model_Message_Abstract
     */
    public function setIdentifier($id)
    {
        $this->_identifier = $id;
        return $this;
    }

    /**
     * Get message identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Set message sticky status
     *
     * @param  bool                             $isSticky
     * @return Mage_Core_Model_Message_Abstract
     */
    public function setIsSticky($isSticky = true)
    {
        $this->_isSticky = $isSticky;
        return $this;
    }

    /**
     * Get whether message is sticky
     *
     * @return bool
     */
    public function getIsSticky()
    {
        return $this->_isSticky;
    }

    /**
     * Set code
     *
     * @param  string                           $code
     * @return Mage_Core_Model_Message_Abstract
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }
}
