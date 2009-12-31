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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist item collection
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Mysql4_Item_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    public function _construct()
    {
        $this->_init('wishlist/item', 'catalog/product');
    }

    public function useProductItem()
    {
        $this->setObject(Mage::getModel('catalog/product'));
        return $this;
    }

    public function addWishlistFilter(Mage_Wishlist_Model_Wishlist    $wishlist)
    {
        $this->_joinFields['e_id'] = array('table'=>'e','field'=>'entity_id');
        $this->joinTable('wishlist/item',
            'product_id=e_id',
            array(
                'wishlist_item_id'  => 'wishlist_item_id',
                'product_id'        => 'product_id',
                'store_id'          => 'store_id',
                'wishlist_id'       => 'wishlist_id',
                'added_at'          => 'added_at',
                'description'       => 'description'
            ),
            array('wishlist_id'=>$wishlist->getId())
        );

        return $this;
    }

    public function addStoreData()
    {
        if(!isset($this->_joinFields['e_id'])) {
            return $this;
        }

        $dayTable = $this->_getAttributeTableAlias('days_in_wishlist');
        $this->joinField('store_name', 'core/store', 'name', 'store_id=store_id')
            ->joinField('days_in_wishlist',
                'wishlist/item',
                "(TO_DAYS('" . Mage::getSingleton('core/date')->date() . "') - TO_DAYS(DATE_ADD(".$dayTable.".added_at, INTERVAL " .(int) Mage::getSingleton('core/date')->getGmtOffset() . " SECOND)))",
                'wishlist_item_id=wishlist_item_id');

        return $this;
    }

    protected function _getAttributeFieldName($attributeCode)
    {
        if($attributeCode == 'days_in_wishlist') {
            return $this->_joinFields[$attributeCode]['field'];
        }
        return parent::_getAttributeFieldName($attributeCode);
    }

    public function load($p=false, $l=false)
    {
        return parent::load($p, $l);
    }

}
