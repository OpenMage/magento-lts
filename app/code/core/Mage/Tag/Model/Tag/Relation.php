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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag relation model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Tag_Model_Resource_Tag_Relation _getResource()
 * @method Mage_Tag_Model_Resource_Tag_Relation getResource()
 * @method int getActive()
 * @method $this setActive(int $value)
 * @method array getAddedProductIds()
 * @method $this setAddedProductIds(array $value)
 * @method $this setCreatedAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method $this setProductIds(array $value)
 * @method $this setRelatedTagIds(array $value)
 * @method string getStatusFilter()
 * @method $this setStatusFilter(string $value)
 * @method bool hasStoreId()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getTagId()
 * @method $this setTagId(int $value)
 */
class Mage_Tag_Model_Tag_Relation extends Mage_Core_Model_Abstract
{
    /**
     * Relation statuses
     */
    public const STATUS_ACTIVE     = 1;
    public const STATUS_NOT_ACTIVE = 0;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    public const ENTITY = 'tag_relation';

    protected function _construct()
    {
        $this->_init('tag/tag_relation');
    }

    /**
     * Init indexing process after tag data commit
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            self::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Load relation by Product (optional), tag, customer and store
     *
     * @param int|null $productId
     * @param int $tagId
     * @param int $customerId
     * @param int|null $storeId
     * @return $this
     */
    public function loadByTagCustomer($productId, $tagId, $customerId, $storeId = null)
    {
        $this->setProductId($productId);
        $this->setTagId($tagId);
        $this->setCustomerId($customerId);
        if (!is_null($storeId)) {
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
     * @return $this
     */
    public function deactivate()
    {
        $this->_getResource()->deactivate($this->getTagId(), $this->getCustomerId());
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Tag_Model_Tag $model
     * @param array $productIds
     * @return $this
     */
    public function addRelations(Mage_Tag_Model_Tag $model, $productIds = [])
    {
        $this->setAddedProductIds($productIds);
        $this->setTagId($model->getTagId());
        $this->setCustomerId(null);
        $this->setStoreId($model->getStore());
        $this->_getResource()->addRelations($this);
        return $this;
    }
}
