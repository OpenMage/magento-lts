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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tag relation model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Tag_Relation extends Mage_Core_Model_Abstract
{
    const STATUS_ACTIVE = 1;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'tag_relation';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('tag/tag_relation');
    }

    /**
     * Retrieve Resource Instance wrapper
     *
     * @return Mage_Tag_Model_Mysql4_Tag_Relation
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Init indexing process after tag data commit
     *
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Load relation by Product (optional), tag, customer and store
     *
     * @param int $productId
     * @param int $tagId
     * @param int $customerId
     * @param int $storeId
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function loadByTagCustomer($productId=null, $tagId, $customerId, $storeId=null)
    {
        $this->setProductId($productId);
        $this->setTagId($tagId);
        $this->setCustomerId($customerId);
        if(!is_null($storeId)) {
            $this->setStoreId($storeId);
        }
        $this->_getResource()->loadByTagCustomer($this);
        return $this;
    }

    /**
     * Retrieve Relation Product Ids
     *
     * @return array
     */
    public function getProductIds()
    {
        $ids = $this->getData('product_ids');
        if (is_null($ids)) {
            $ids = $this->_getResource()->getProductIds($this);
            $this->setProductIds($ids);
        }
        return $ids;
    }

    /**
     * Retrieve list of related tag ids for products specified in current object
     *
     * @return array
     */
    public function getRelatedTagIds()
    {
        if (is_null($this->getData('related_tag_ids'))) {
            $this->setRelatedTagIds($this->_getResource()->getRelatedTagIds($this));
        }
        return $this->getData('related_tag_ids');
    }

    /**
     * Deactivate tag relations (using current settings)
     *
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function deactivate()
    {
        $this->_getResource()->deactivate($this->getTagId(),  $this->getCustomerId());
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Tag_Model_Tag $model
     * @param array $productIds
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function addRelations(Mage_Tag_Model_Tag $model, $productIds = array())
    {
        $this->setAddedProductIds($productIds);
        $this->setTagId($model->getTagId());
        $this->setCustomerId(null);
        $this->setStoreId($model->getStore());
        $this->_getResource()->addRelations($this);
        return $this;
    }
}
