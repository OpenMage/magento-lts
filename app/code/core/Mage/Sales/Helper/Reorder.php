<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales module base helper
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Helper_Reorder extends Mage_Core_Helper_Data
{
    public const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

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
