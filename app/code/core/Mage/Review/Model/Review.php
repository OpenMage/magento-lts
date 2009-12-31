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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review model
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Review extends Mage_Core_Model_Abstract
{
    const ENTITY_PRODUCT = 1;

    const STATUS_APPROVED       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_NOT_APPROVED   = 3;

    protected function _construct()
    {
        $this->_init('review/review');
    }

    public function getProductCollection()
    {
        return Mage::getResourceModel('review/review_product_collection');
    }

    public function getStatusCollection()
    {
        return Mage::getResourceModel('review/review_status_collection');
    }

    public function getTotalReviews($entityPkValue, $approvedOnly=false, $storeId=0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    public function getEntitySummary($product, $storeId=0)
    {
        $summaryData = Mage::getModel('review/review_summary')
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = new Varien_Object();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getReviewUrl()
    {
        return Mage::getUrl('review/product/view', array('id' => $this->getReviewId()));
    }

    public function validate()
    {
        $errors = array();

        $helper = Mage::helper('customer');

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = $helper->__('Review summary can\'t be empty');
        }

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = $helper->__('Nickname can\'t be empty');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = $helper->__('Review can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Append review summary to product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Review_Model_Review
     */
    public function appendSummary($collection)
    {
        $entityIds = array();
        foreach ($collection->getItems() as $_itemId => $_item) {
            $entityIds[] = $_item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }

        $summaryData = Mage::getResourceModel('review/review_summary_collection')
            ->addEntityFilter($entityIds)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->load();

        foreach ($collection->getItems() as $_item ) {
            foreach ($summaryData as $_summary) {
                if ($_summary->getEntityPkValue() == $_item->getEntityId()) {
                    $_item->setRatingSummary($_summary);
                }
            }
        }

        return $this;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}