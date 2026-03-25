<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV entity attribute exception
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Exception extends Exception
{
    /**
     * Eav entity attribute
     *
     * @var string
     */
    protected $_attributeCode;

    /**
     * Eav entity attribute part
     * attribute|backend|frontend|source
     *
     * @var string
     */
    protected $_part;

    /**
     * Set Eav entity attribute
     *
     * @param  string $attribute
     * @return $this
     */
    public function setAttributeCode($attribute)
    {
        $this->_attributeCode = $attribute;
        return $this;
    }

    /**
     * Set Eav entity attribute type
     *
     * @param  string $part
     * @return $this
     */
    public function setPart($part)
    {
        $this->_part = $part;
        return $this;
    }

    /**
     * Retrieve Eav entity attribute
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }

    /**
     * Retrieve Eav entity attribute part
     *
     * @return string
     */
    public function getPart()
    {
        return $this->_part;
    }
}
