<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rss data helper
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config path to RSS field
     */
    public const XML_PATH_RSS_ACTIVE                    = 'rss/config/active';
    public const XML_PATH_RSS_ADMIN_CATALOG_NOTIFYSTOCK = 'rss/admin_catalog/notifystock';
    public const XML_PATH_RSS_ADMIN_CATALOG_REVIEW      = 'rss/admin_catalog/review';
    public const XML_PATH_RSS_ADMIN_ORDER_NEW           = 'rss/admin_order/new';
    public const XML_PATH_RSS_ADMIN_ORDER_NEW_PERIOD    = 'rss/admin_order/new_period';

    protected $_moduleName = 'Mage_Rss';

    protected $_rssSession;

    protected $_adminSession;

    public function __construct(array $params = [])
    {
        $this->_rssSession = $params['rss_session'] ?? Mage::getSingleton('rss/session');
        $this->_adminSession = $params['admin_session'] ?? Mage::getSingleton('admin/session');
    }

    /**
     * Authenticate customer on frontend
     *
     */
    public function authFrontend()
    {
        if (!$this->_rssSession->isCustomerLoggedIn()) {
            [$username, $password] = $this->authValidate();
            $customer = Mage::getModel('customer/customer')->authenticate($username, $password);
            if ($customer && $customer->getId()) {
                $this->_rssSession->settCustomer($customer);
            } else {
                $this->authFailed();
            }
        }
    }

    /**
     * Authenticate admin and check ACL
     *
     * @param string $path
     */
    public function authAdmin($path)
    {
        if (!$this->_rssSession->isAdminLoggedIn() || !$this->_adminSession->isLoggedIn()) {
            [$username, $password] = $this->authValidate();
            Mage::getSingleton('adminhtml/url')->setNoSecret(true);
            $user = $this->_adminSession->login($username, $password);
        } else {
            $user = $this->_rssSession->getAdmin();
        }
        if ($user && $user->getId() && $user->getIsActive() == '1' && $this->_adminSession->isAllowed($path)) {
            $adminUserExtra = $user->getExtra();
            if ($adminUserExtra && !is_array($adminUserExtra)) {
                $adminUserExtra = Mage::helper('core/unserializeArray')->unserialize($user->getExtra());
            }
            if (!isset($adminUserExtra['indirect_login'])) {
                $adminUserExtra = array_merge($adminUserExtra, ['indirect_login' => true]);
                $user->saveExtra($adminUserExtra);
            }
            $this->_adminSession->setIndirectLogin(true);
            $this->_rssSession->setAdmin($user);
        } else {
            $this->authFailed();
        }
    }

    /**
     * Validate Authenticate
     *
     * @param array $headers
     * @return array
     */
    public function authValidate($headers = null)
    {
        return Mage::helper('core/http')->authValidate($headers);
    }

    /**
     * Send authenticate failed headers
     *
     */
    public function authFailed()
    {
        Mage::helper('core/http')->authFailed();
    }

    /**
     * Disable using of flat catalog and/or product model to prevent limiting results to single store. Probably won't
     * work inside a controller.
     */
    public function disableFlat()
    {
        /** @var Mage_Catalog_Helper_Product_Flat $flatHelper */
        $flatHelper = Mage::helper('catalog/product_flat');
        if ($flatHelper->isAvailable()) {
            /** @var Mage_Core_Model_App_Emulation $emulationModel */
            $emulationModel = Mage::getModel('core/app_emulation');
            // Emulate admin environment to disable using flat model - otherwise we won't get global stats
            // for all stores
            $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }
    }

    /**
     * Check if module was activated in system configurations
     *
     * @return bool
     */
    public function isRssEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_RSS_ACTIVE);
    }

    public function isRssAdminCatalogNotifyStockEnabled(): bool
    {
        return $this->isRssEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_RSS_ADMIN_CATALOG_NOTIFYSTOCK);
    }

    public function isRssAdminCatalogReviewEnabled(): bool
    {
        return $this->isRssEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_RSS_ADMIN_CATALOG_REVIEW);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public function isRssAdminOrderNewEnabled($store = null): bool
    {
        return $this->isRssEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_RSS_ADMIN_ORDER_NEW, $store);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public function getRssAdminOrderNewPeriod($store = null): int
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_RSS_ADMIN_ORDER_NEW_PERIOD, $store);
    }
}
