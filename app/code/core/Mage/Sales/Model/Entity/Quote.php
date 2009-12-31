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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Entity_Quote extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('quote')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = parent::_getLoadRowSelect($object, $rowId);
        if ($object->getSharedStoreIds()) {
            $select->where('store_id IN (?)', $object->getSharedStoreIds());
        }
        return $select;
    }

    /**
     * Loading quote by customer identifier
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $collection = Mage::getResourceModel('sales/quote_collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('is_active', 1);

        if ($quote->getSharedStoreIds()) {
            $collection->addAttributeToFilter('store_id', array('in', $quote->getSharedStoreIds()));
        }

        $collection->setOrder('updated_at', 'desc')
            ->setPageSize(1)
            ->load();

        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $this->load($quote, $item->getId());
                return $this;
            }
        }
        return $this;
    }

    public function getReservedOrderId($quote)
    {
        return Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($quote->getStoreId());
    }
}
