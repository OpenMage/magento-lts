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
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist resource model
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Resource_Wishlist extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store wishlist items count
     *
     * @var null|int
     */
    protected $_itemsCount = null;

    /**
     * Store customer ID field name
     *
     * @var string
     */
    protected $_customerIdFieldName = 'customer_id';

    /**
     * Set main entity table name and primary key field name
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist', 'wishlist_id');
    }

    /**
     * Prepare wishlist load select query
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == $this->_customerIdFieldName) {
            $select->order('wishlist_id ' . Zend_Db_Select::SQL_ASC)
                ->limit(1);
        }
        return $select;
    }

    /**
     * Getter for customer ID field name
     *
     * @return string
     */
    public function getCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * Setter for customer ID field name
     *
     * @param $fieldName
     *
     * @return Mage_Wishlist_Model_Resource_Wishlist
     */
    public function setCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }

    /**
     * Retrieve wishlist items count
     *
     * @deprecated after 1.6.0.0-rc2
     * @see Mage_Wishlist_Model_Wishlist::getItemsCount()
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     *
     * @return int
     */
    public function fetchItemsCount(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        if (is_null($this->_itemsCount)) {
            $this->_itemsCount = $wishlist->getItemsCount();
        }

        return $this->_itemsCount;
    }

}
