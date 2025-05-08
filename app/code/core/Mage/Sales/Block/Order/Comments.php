<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Comments extends Mage_Core_Block_Template
{
    /**
     * Current entity (model instance) with getCommentsCollection() method
     *
     * @var Mage_Sales_Model_Abstract
     */
    protected $_entity;

    /**
     * Current comments collection
     *
     * @var Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract|null
     */
    protected $_commentCollection;

    /**
     * Sets comments parent model instance
     *
     * @param Mage_Sales_Model_Abstract $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        $this->_commentCollection = null; // Changing model and resource model can lead to change of comment collection
        return $this;
    }

    /**
     * Gets comments parent model instance
     *
     * @return Mage_Sales_Model_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Initialize model comments and return comment collection
     *
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function getComments()
    {
        if (is_null($this->_commentCollection)) {
            $entity = $this->getEntity();
            if ($entity instanceof Mage_Sales_Model_Order_Invoice) {
                $collectionClass = 'sales/order_invoice_comment_collection';
            } elseif ($entity instanceof Mage_Sales_Model_Order_Creditmemo) {
                $collectionClass = 'sales/order_creditmemo_comment_collection';
            } elseif ($entity instanceof Mage_Sales_Model_Order_Shipment) {
                $collectionClass = 'sales/order_shipment_comment_collection';
            } else {
                Mage::throwException(Mage::helper('sales')->__('Invalid entity model'));
            }

            /** @var Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract $commentCollection */
            $commentCollection = Mage::getResourceModel($collectionClass);
            $this->_commentCollection = $commentCollection;
            $this->_commentCollection->setParentFilter($entity)
               ->setCreatedAtOrder()
               ->addVisibleOnFrontFilter();
        }

        return $this->_commentCollection;
    }

    /**
     * Returns whether there are comments to show on frontend
     *
     * @return bool
     */
    public function hasComments()
    {
        return $this->getComments()->count() > 0;
    }
}
