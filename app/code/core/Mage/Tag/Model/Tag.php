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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Tag extends Mage_Core_Model_Abstract
{
    const STATUS_DISABLED = -1;
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'tag';

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    /**
     * Product event tags collection getter
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Tag_Model_Mysql4_Tag_Collection
     */
    protected function _getProductEventTagsCollection(Varien_Event_Observer $observer)
    {
        return $this->getResourceCollection()
                        ->joinRel()
                        ->addProductFilter($observer->getEvent()->getProduct()->getId())
                        ->addTagGroup()
                        ->load();
    }

    public function getPopularity()
    {
        return $this->_getData('popularity');
    }

    public function getName()
    {
        return $this->_getData('name');
    }

    public function getTagId()
    {
        return $this->_getData('tag_id');
    }

    public function getRatio()
    {
        return $this->_getData('ratio');
    }

    public function setRatio($ratio)
    {
        $this->setData('ratio', $ratio);
        return $this;
    }

    public function loadByName($name)
    {
        $this->_getResource()->loadByName($this, $name);
        return $this;
    }

    public function aggregate()
    {
        $this->_getResource()->aggregate($this);
        return $this;
    }

    public function productEventAggregate($observer)
    {
        $this->_getProductEventTagsCollection($observer)->walk('aggregate');
        return $this;
    }

    /**
     * Product delete event action
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Tag_Model_Tag
     */
    public function productDeleteEventAction($observer)
    {
        $this->_getResource()->decrementProducts($this->_getProductEventTagsCollection($observer)->getAllIds());
        return $this;
    }

    public function addSummary($storeId)
    {
        $this->setStoreId($storeId);
        $this->_getResource()->addSummary($this);
        return $this;
    }

    public function getApprovedStatus()
    {
        return self::STATUS_APPROVED;
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getEntityCollection()
    {
        return Mage::getResourceModel('tag/product_collection');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('tag/customer_collection');
    }

    public function getTaggedProductsUrl()
    {
        return Mage::getUrl('tag/product/list', array('tagId' => $this->getTagId()));
    }

    public function getViewTagUrl()
    {
        return Mage::getUrl('tag/customer/view', array('tagId' => $this->getTagId()));
    }

    public function getEditTagUrl()
    {
        return Mage::getUrl('tag/customer/edit', array('tagId' => $this->getTagId()));
    }

    public function getRemoveTagUrl()
    {
        return Mage::getUrl('tag/customer/remove', array('tagId' => $this->getTagId()));
    }

    public function getPopularCollection()
    {
        return Mage::getResourceModel('tag/popular_collection');
    }

    /**
     * Retrieves array of related product IDs
     *
     * @return array
     */
    public function getRelatedProductIds()
    {
        return Mage::getModel('tag/tag_relation')
            ->setTagId($this->getTagId())
            ->setStoreId($this->getStoreId())
            ->setCustomerId(null)
            ->getProductIds();
    }

    /**
     * Checks is available current tag in specified store
     *
     * @param int $storeId
     * @return bool
     */
    public function isAvailableInStore($storeId = null)
    {
        $storeId = (is_null($storeId)) ? Mage::app()->getStore()->getId() : $storeId;
        return in_array($storeId, $this->getVisibleInStoreIds());
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}
