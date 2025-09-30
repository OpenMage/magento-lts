<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote entity resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('quote')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write'),
        );
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object|Mage_Sales_Model_Quote $object
     * @param   int $rowId
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
     * @return $this
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $collection = Mage::getResourceModel('sales/quote_collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('is_active', 1);

        if ($quote->getSharedStoreIds()) {
            $collection->addAttributeToFilter('store_id', ['in', $quote->getSharedStoreIds()]);
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

    /**
     * Loading quote by identifier
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $quoteId
     * @return $this
     */
    public function loadByIdWithoutStore($quote, $quoteId)
    {
        $collection = Mage::getResourceModel('sales/quote_collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('entity_id', $quoteId);

        $collection->setPageSize(1)
            ->load();

        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $this->load($quote, $item->getId());
                return $this;
            }
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return string
     * @throws Exception
     */
    public function getReservedOrderId($quote)
    {
        return Mage::getSingleton('eav/config')->getEntityType(Mage_Sales_Model_Order::ENTITY)->fetchNewIncrementId($quote->getStoreId());
    }
}
