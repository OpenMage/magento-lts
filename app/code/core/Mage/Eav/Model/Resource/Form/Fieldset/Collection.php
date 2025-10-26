<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Fieldset Resource Collection
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Form_Fieldset_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store scope ID
     *
     * @var int|null
     */
    protected $_storeId;

    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('eav/form_fieldset');
    }

    /**
     * Add Form Type filter to collection
     *
     * @param Mage_Eav_Model_Form_Type|int $type
     * @return $this
     */
    public function addTypeFilter($type)
    {
        if ($type instanceof Mage_Eav_Model_Form_Type) {
            $type = $type->getId();
        }

        return $this->addFieldToFilter('type_id', $type);
    }

    /**
     * Set order by fieldset sort order
     *
     * @return $this
     */
    public function setSortOrder()
    {
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Retrieve label store scope
     *
     * @return int|null
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return Mage::app()->getStore()->getId();
        }

        return $this->_storeId;
    }

    /**
     * Set store scope ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $select = $this->getSelect();
        $select->join(
            ['default_label' => $this->getTable('eav/form_fieldset_label')],
            'main_table.fieldset_id = default_label.fieldset_id AND default_label.store_id = 0',
            [],
        );
        if ($this->getStoreId() == 0) {
            $select->columns('label', 'default_label');
        } else {
            $labelExpr = $select->getAdapter()
                ->getIfNullSql('store_label.label', 'default_label.label');
            $joinCondition = $this->getConnection()
                ->quoteInto(
                    'main_table.fieldset_id = store_label.fieldset_id AND store_label.store_id = ?',
                    (int) $this->getStoreId(),
                );
            $select->joinLeft(
                ['store_label' => $this->getTable('eav/form_fieldset_label')],
                $joinCondition,
                ['label' => $labelExpr],
            );
        }

        return $this;
    }
}
