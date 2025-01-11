<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review summery collection
 *
 * @category   Mage
 * @package    Mage_Review
 */
class Mage_Review_Model_Resource_Review_Summary_Collection extends Varien_Data_Collection_Db
{
    /**
     * @var string
     */
    protected $_summaryTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        $this->_setIdFieldName('primary_id');

        parent::__construct($resources->getConnection('review_read'));
        $this->_summaryTable = $resources->getTableName('review/review_aggregate');

        $this->_select->from($this->_summaryTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('review/review_summary'));
    }

    /**
     * Add entity filter
     *
     * @param int|array $entityId
     * @param int $entityType
     * @return $this
     */
    public function addEntityFilter($entityId, $entityType = 1)
    {
        $this->_select->where('entity_pk_value IN(?)', $entityId)
            ->where('entity_type = ?', $entityType);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->_select->where('store_id = ?', $storeId);
        return $this;
    }
}
