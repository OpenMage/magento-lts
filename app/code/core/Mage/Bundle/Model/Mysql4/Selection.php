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
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Selection Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Selection extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('bundle/selection', 'selection_id');
    }
/*
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $condition .= ' and ' . $this->_getWriteAdapter()->quoteInto('store_id = ?', $object->getStoreId());

        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        $data = new Varien_Object();
        $data->setOptionId($object->getId())
            ->setStoreId($object->getStoreId())
            ->setTitle($object->getTitle());

        $this->_getWriteAdapter()->insert($this->getTable('option_value'), $data->getData());

        return $this;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_afterDelete($object);

        $condition = $this->_getWriteAdapter()->quoteInto('option_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('option_value'), $condition);

        return $this;
    }
*/

    public function getPriceFromIndex($productId, $qty, $storeId, $groupId) {
        $select = clone $this->_getReadAdapter()->select();
        $select->reset();

        $attrPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
        $attrTierPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price');

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        $select->from(array("price_index" => $this->getTable('catalogindex/price')), array('price' => 'SUM(value)'))
            ->where('entity_id in (?)', $productId)
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $groupId)
            ->where('attribute_id in (?)', array($attrPriceId, $attrTierPriceId))
            ->where('qty <= ?', $qty)
            ->group('entity_id');

        $price = $this->_getReadAdapter()->fetchCol($select);
        if (!empty($price)) {
            return array_shift($price);
        } else {
            return 0;
        }
    }

}