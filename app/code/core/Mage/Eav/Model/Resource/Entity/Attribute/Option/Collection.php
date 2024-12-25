<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity attribute option collection
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Option value table
     *
     * @var string
     */
    protected $_optionValueTable;

    protected function _construct()
    {
        $this->_init('eav/entity_attribute_option');
        $this->_optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
    }

    /**
     * Set attribute filter
     *
     * @param int $setId
     * @return $this
     */
    public function setAttributeFilter($setId)
    {
        return $this->addFieldToFilter('attribute_id', $setId);
    }

    /**
     * Add store filter to collection
     *
     * @param int $storeId
     * @param bool $useDefaultValue
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function setStoreFilter($storeId = null, $useDefaultValue = true)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $adapter = $this->getConnection();

        $joinCondition = $adapter->quoteInto('tsv.option_id = main_table.option_id AND tsv.store_id = ?', $storeId);

        if ($useDefaultValue) {
            $this->getSelect()
                ->join(
                    ['tdv' => $this->_optionValueTable],
                    'tdv.option_id = main_table.option_id',
                    ['default_value' => 'value'],
                )
                ->joinLeft(
                    ['tsv' => $this->_optionValueTable],
                    $joinCondition,
                    [
                        'store_default_value' => 'value',
                        'value'               => $adapter->getCheckSql('tsv.value_id > 0', 'tsv.value', 'tdv.value'),
                    ],
                )
                ->where('tdv.store_id = ?', 0);
        } else {
            $this->getSelect()
                ->joinLeft(
                    ['tsv' => $this->_optionValueTable],
                    $joinCondition,
                    'value',
                )
                ->where('tsv.store_id = ?', $storeId);
        }

        $this->setOrder('value', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Add option id(s) frilter to collection
     *
     * @param int|array $optionId
     * @return $this
     */
    public function setIdFilter($optionId)
    {
        return $this->addFieldToFilter('option_id', ['in' => $optionId]);
    }

    /**
     * Convert collection items to select options array
     *
     * @param string $valueKey
     * @return array
     */
    public function toOptionArray($valueKey = 'value')
    {
        return $this->_toOptionArray('option_id', $valueKey);
    }

    /**
     * Set order by position or alphabetically by values in admin
     *
     * @param string $dir direction
     * @param bool $sortAlpha sort alphabetically by values in admin
     * @return $this
     */
    public function setPositionOrder($dir = self::SORT_ORDER_ASC, $sortAlpha = false)
    {
        $this->setOrder('main_table.sort_order', $dir);
        // sort alphabetically by values in admin
        if ($sortAlpha) {
            $this->getSelect()
                ->joinLeft(
                    ['sort_alpha_value' => $this->_optionValueTable],
                    'sort_alpha_value.option_id = main_table.option_id AND sort_alpha_value.store_id = 0',
                    ['value'],
                );
            $this->setOrder('sort_alpha_value.value', $dir);
        }

        return $this;
    }
}
