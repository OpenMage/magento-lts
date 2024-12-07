<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
