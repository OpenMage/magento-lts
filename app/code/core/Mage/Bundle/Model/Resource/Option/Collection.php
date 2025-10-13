<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Options Resource Collection
 *
 * @package    Mage_Bundle
 *
 * @method Mage_Bundle_Model_Option[] getItems()
 * @method Mage_Bundle_Model_Option getItemById($idValue)
 */
class Mage_Bundle_Model_Resource_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * All item ids cache
     *
     * @var array|null
     */
    protected $_itemIds;

    /**
     * True when selections a
     *
     * @var bool
     */
    protected $_selectionsAppended   = false;

    /**
     * Init model and resource model
     *
     */
    protected function _construct()
    {
        $this->_init('bundle/option');
    }

    /**
     * Joins values to options
     *
     * @param int $storeId
     * @return $this
     */
    public function joinValues($storeId)
    {
        $this->getSelect()
            ->joinLeft(
                ['option_value_default' => $this->getTable('bundle/option_value')],
                'main_table.option_id = option_value_default.option_id and option_value_default.store_id = 0',
                [],
            )
            ->columns(['default_title' => 'option_value_default.title']);

        $title = $this->getConnection()->getCheckSql(
            'option_value.title IS NOT NULL',
            'option_value.title',
            'option_value_default.title',
        );
        if ($storeId !== null) {
            $this->getSelect()
                ->columns(['title' => $title])
                ->joinLeft(
                    ['option_value' => $this->getTable('bundle/option_value')],
                    $this->getConnection()->quoteInto(
                        'main_table.option_id = option_value.option_id and option_value.store_id = ?',
                        $storeId,
                    ),
                    [],
                );
        }

        return $this;
    }

    /**
     * Sets product id filter
     *
     * @param int $productId
     * @return $this
     */
    public function setProductIdFilter($productId)
    {
        $this->addFieldToFilter('main_table.parent_id', $productId);
        return $this;
    }

    /**
     * Sets order by position
     *
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('main_table.position asc')
            ->order('main_table.option_id asc');
        return $this;
    }

    /**
     * Append selection to options
     * stripBefore - indicates to reload
     * appendAll - indicates do we need to filter by saleable and required custom options
     *
     * @param Mage_Bundle_Model_Resource_Selection_Collection $selectionsCollection
     * @param bool $stripBefore
     * @param bool $appendAll
     * @return array
     */
    public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
    {
        if ($stripBefore) {
            $this->_stripSelections();
        }

        if (!$this->_selectionsAppended) {
            foreach ($selectionsCollection->getItems() as $key => $selection) {
                if ($option = $this->getItemById($selection->getOptionId())) {
                    if ($appendAll || ($selection->isSalable() && !$selection->getRequiredOptions())) {
                        $selection->setOption($option);
                        $option->addSelection($selection);
                    } else {
                        $selectionsCollection->removeItemByKey($key);
                    }
                }
            }

            $this->_selectionsAppended = true;
        }

        return $this->getItems();
    }

    /**
     * Removes appended selections before
     *
     * @return $this
     */
    protected function _stripSelections()
    {
        foreach ($this->getItems() as $option) {
            $option->setSelections([]);
        }

        $this->_selectionsAppended = false;
        return $this;
    }

    /**
     * Sets filter by option id
     *
     * @param array|int $ids
     * @return $this
     */
    public function setIdFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('main_table.option_id', ['in' => $ids]);
        } elseif ($ids != '') {
            $this->addFieldToFilter('main_table.option_id', $ids);
        }

        return $this;
    }

    /**
     * Reset all item ids cache
     *
     * @return $this
     */
    public function resetAllIds()
    {
        $this->_itemIds = null;
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_itemIds)) {
            $this->_itemIds = parent::getAllIds();
        }

        return $this->_itemIds;
    }
}
