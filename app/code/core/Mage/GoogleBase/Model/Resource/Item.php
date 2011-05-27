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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Google Base Item resource model
 *
 * @deprecated after 1.5.1.0
 * @category    Mage
 * @package     Mage_GoogleBase
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('googlebase/items', 'item_id');
    }

    /**
     * Load Item model by product
     *
     * @param Mage_GoogleBase_Model_Item $model
     * @return Mage_GoogleBase_Model_Resource_Item
     */
    public function loadByProduct($model)
    {
        if (!($model->getProduct() instanceof Varien_Object)) {
            return $this;
        }

        $product    = $model->getProduct();
        $productId  = $product->getId();
        $storeId    = $model->getStoreId() ? $model->getStoreId() : $product->getStoreId();

        $adapter    = $this->_getReadAdapter();
        $select     = $adapter->select();

        if ($productId !== null) {
            $select->from($this->getMainTable())
                ->where('product_id = ?', $productId)
                ->where('store_id = ?', (int)$storeId);

            $data = $adapter->fetchRow($select);
            $data = is_array($data) ? $data : array();
            $model->addData($data);
        }
        return $this;
    }
}
