<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Notifications about not correct tax settings
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Initialize block instance
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
        unset($args['factory'], $args['app']);
        parent::__construct($args);
    }

    /**
     * Return list of store names which have not compatible tax calculation type and price display settings.
     * Return true if settings are wrong for default store.
     *
     * @return bool|array
     */
    public function getStoresWithWrongDisplaySettings()
    {
        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        //check default store first
        /** @var Mage_Tax_Model_Config $model */
        $model = $this->_factory->getSingleton('tax/config');
        if (!$model->checkDisplaySettings($defaultStoreId)) {
            return true;
        }
        $storeNames = [];
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            if (!$this->checkDisplaySettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Return list of store names which have not compatible tax calculation type and price display settings.
     * Return true if settings are wrong for default store.
     *
     * @return array
     */
    public function getStoresWithConflictingFptTaxConfigurationSettings()
    {
        /** @var Mage_Weee_Helper_Data $weeeTaxHelper */
        $weeeTaxHelper = $this->_factory->getHelper('weee');

        $storeNames = [];
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            if ($weeeTaxHelper->validateCatalogPricesAndFptConfiguration($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Return boolean determining if FPT/ Catalog Price settings is conflicting or not.
     *
     * @return bool
     */
    public function isDefaultStoreWithConflictingFptTaxConfigurationSettings()
    {
        /** @var Mage_Weee_Helper_Data $weeeTaxHelper */
        $weeeTaxHelper = $this->_factory->getHelper('weee');
        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;

        //check default store first
        return $weeeTaxHelper->validateCatalogPricesAndFptConfiguration($defaultStoreId);
    }

    /**
     * Check if tax calculation type and price display settings are compatible
     *
     * @param mixed $store
     * @return bool
     */
    public function checkDisplaySettings($store = null)
    {
        /** @var Mage_Tax_Model_Config $model */
        $model = $this->_factory->getSingleton('tax/config');
        return $model->checkDisplaySettings($store);
    }

    /**
     * Return list of store names where tax discount settings are compatible.
     * Return true if settings are wrong for default store.
     *
     * @return bool|array
     */
    public function getWebsitesWithWrongDiscountSettings()
    {
        /** @var Mage_Tax_Model_Config $model */
        $model = $this->_factory->getSingleton('tax/config');

        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        //check default store first
        if (!$model->checkDiscountSettings($defaultStoreId)) {
            return true;
        }
        $storeNames = [];
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            if (!$model->checkDiscountSettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Get URL to ignore tax notifications
     *
     * @param string $section
     * @return string
     */
    public function getIgnoreTaxNotificationUrl($section)
    {
        return $this->getUrl('adminhtml/tax/ignoreTaxNotification', ['section' => $section]);
    }

    /**
     * Get tax management url
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getInfoUrl()
    {
        return $this->_app->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_URL);
    }

    /**
     * Get tax management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/tax');
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var Mage_Admin_Model_Session $model */
        $model = $this->_factory->getSingleton('admin/session');
        if ($model->isAllowed('system/config/tax')) {
            return parent::_toHtml();
        }
        return '';
    }
}
