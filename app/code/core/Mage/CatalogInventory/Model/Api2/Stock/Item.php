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
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for stock item
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Api2_Stock_Item extends Mage_Api2_Model_Resource
{
    /**
     * Load stock item by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _loadStockItemById($id)
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->load($id);
        if (!$stockItem->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $stockItem;
    }
}
