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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog compare item resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('catalog/compare_item', 'catalog_compare_item_id');
    }

    public function loadByProduct(Mage_Core_Model_Abstract $object, $product)
    {
        $read = $this->_getReadAdapter();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        }
        else {
            $productId = (int) $product;
        }

        $select = $read->select()->from($this->getMainTable())
            ->where('product_id=?',  $productId)
            ->where('visitor_id=?',  $object->getVisitorId());
        if ($object->getCustomerId()) {
            $select->where('customer_id=?', $object->getCustomerId());
        }

        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    public function getCount($customerId, $visitorId)
    {
        $select = $this->_getReadAdapter()->select()->from($this->getMainTable(), 'COUNT(*)')
            ->where('visitor_id=?',  $visitorId);
        if ($customerId) {
            $select->where('customer_id=?', $customerId);
        }
        return $this->_getReadAdapter()->fetchOne($select);
    }
}
