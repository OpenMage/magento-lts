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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Options Resource Collection
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_selectionsAppended = false;
    protected function _construct()
    {
        $this->_init('bundle/option');
    }

    public function joinValues($storeId)
    {
        $this->getSelect()->joinLeft(array('option_value_default' => $this->getTable('bundle/option_value')),
                '`main_table`.`option_id` = `option_value_default`.`option_id` and `option_value_default`.`store_id` = "0"',
                array())
            ->from('', array('default_title' => 'option_value_default.title'));

        if ($storeId !== null) {
            $this->getSelect()
                ->from('', array('title' => 'IFNULL(`option_value`.`title`, `option_value_default`.`title`)'))
                ->joinLeft(array('option_value' => $this->getTable('bundle/option_value')),
                    '`main_table`.`option_id` = `option_value`.`option_id` and `option_value`.`store_id` = "' . $storeId . '"',
                    array());
        }
        return $this;
    }

    public function setProductIdFilter($productId)
    {
        $this->addFieldToFilter('`main_table`.`parent_id`', $productId);
        return $this;
    }

    public function setPositionOrder()
    {
        $this->getSelect()->order('main_table.position asc')
            ->order('main_table.option_id asc');
        return $this;
    }

    /**
     * Append selection to options
     * stripBefore - indicates to reload
     * appendAll - indecates do we need to filter by saleable and required custom options
     *
     * @param Mage_Bundle_Model_Mysql4_Selection_Collection $selectionsCollection
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
            foreach ($selectionsCollection->getItems() as $key=>$_selection) {
                if ($_option = $this->getItemById($_selection->getOptionId())) {
                    if ((!$appendAll && $_selection->isSalable() && !$_selection->getRequiredOptions()) || $appendAll) {
                        $_selection->setOption($_option);
                        $_option->addSelection($_selection);
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
     * @return Mage_Bundle_Model_Mysql4_Option_Collection
     */
    protected function _stripSelections()
    {
        foreach ($this->getItems() as $option) {
            $option->setSelections(array());
        }
        $this->_selectionsAppended = false;
        return $this;
    }


    public function setIdFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('`main_table`.`option_id`', array('in' => $ids));
        } else if ($ids != '') {
            $this->addFieldToFilter('`main_table`.`option_id`', $ids);
        }
        return $this;
    }

}