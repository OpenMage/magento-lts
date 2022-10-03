<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * Currect comments collection
     *
     * @var Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
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

            $this->_commentCollection = Mage::getResourceModel($collectionClass);
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
