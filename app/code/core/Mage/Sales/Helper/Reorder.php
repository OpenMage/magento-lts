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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales module base helper
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Helper_Reorder extends Mage_Core_Helper_Data
{
    const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

    protected $_moduleName = 'Mage_Sales';

    /**
     * @return bool
     */
    public function isAllow()
    {
        return $this->isAllowed();
    }

    /**
     * Check if reorder is allowed for given store
     *
     * @param Mage_Core_Model_Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if (Mage::getStoreConfig(self::XML_PATH_SALES_REORDER_ALLOW, $store)) {
            return true;
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function canReorder(Mage_Sales_Model_Order $order)
    {
        if (!$this->isAllowed($order->getStore())) {
            return false;
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $order->canReorder();
        } else {
            return true;
        }
    }
}
