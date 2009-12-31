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
 * Wishlist item model resource
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{

    protected $_productIdFieldName = 'product_id';

    protected function _construct()
    {
        $this->_init('wishlist/item', 'wishlist_item_id');
    }

    public function loadByProductWishlist(Mage_Wishlist_Model_Item $item, $wishlistId, $productId, array $sharedStores)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table'=>$this->getTable('item')))
            ->where('main_table.wishlist_id = ?',  $wishlistId)
            ->where('main_table.product_id = ?',  $productId)
            ->where('main_table.store_id in (?)',  $sharedStores);

        if($_data = $this->_getReadAdapter()->fetchRow($select)) {
            $item->setData($_data);
        }

        return $item;
    }

}
