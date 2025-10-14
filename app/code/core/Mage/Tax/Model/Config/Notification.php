<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Config Notification
 *
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
