<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache observer model
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return Mage::helper('pagecache')->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Mage_Core_Controller_Front_Action $action */
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            Mage::helper('pagecache')->setNoCacheCookie();
        }

        return $this;
    }
}
