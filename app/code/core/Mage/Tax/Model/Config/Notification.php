<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Config Notification
 *
 * @category   Mage
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item _getResource()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item getResource()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item_Collection getCollection()
 */
class Mage_Tax_Model_Config_Notification extends Mage_Core_Model_Config_Data
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize class instance
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        parent::__construct($args);
    }

    /**
     * Get config model
     *
     * @return Mage_Core_Model_Config_Data
     */
    protected function _getConfig()
    {
        return $this->_factory->getModel('core/config_data');
    }

    /**
     * Prepare and store cron settings after save
     *
     * @inheritDoc
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_resetNotificationFlag(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_DISCOUNT);
            $this->_resetNotificationFlag(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY);
        }
        return parent::_afterSave();
    }

    /**
     * Reset flag for showing tax notifications
     *
     * @param string $path
     * @return $this
     */
    protected function _resetNotificationFlag($path)
    {
        Mage::helper('tax')->setIsIgnored($path, false);
        return $this;
    }
}
