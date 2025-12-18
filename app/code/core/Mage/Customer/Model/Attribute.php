<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer attribute model
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Attribute            _getResource()
 * @method Mage_Customer_Model_Resource_Attribute_Collection getCollection()
 * @method Mage_Customer_Model_Resource_Attribute            getResource()
 * @method Mage_Customer_Model_Resource_Attribute_Collection getResourceCollection()
 * @method int                                               getScopeMultilineCount()
 * @method $this                                             setScopeIsRequired(string $value)
 * @method $this                                             setScopeIsVisible(string $value)
 * @method $this                                             setScopeMultilineCount(int $value)
 */
class Mage_Customer_Model_Attribute extends Mage_Eav_Model_Attribute
{
    /**
     * Name of the module
     */
    public const MODULE_NAME = 'Mage_Customer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('customer/attribute');
    }
}
