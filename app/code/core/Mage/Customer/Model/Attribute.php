<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer attribute model
 *
 * @category   Mage
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Attribute _getResource()
 * @method Mage_Customer_Model_Resource_Attribute getResource()
 * @method Mage_Customer_Model_Resource_Attribute_Collection getCollection()
 *
 * @method $this setScopeIsVisible(string $value)
 * @method $this setScopeIsRequired(string $value)
 * @method int getScopeMultilineCount()
 * @method $this setScopeMultilineCount(int $value)
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
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('customer/attribute');
    }
}
