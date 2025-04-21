<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review summery collection
 *
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
