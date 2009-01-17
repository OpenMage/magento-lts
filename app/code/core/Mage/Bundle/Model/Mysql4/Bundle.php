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
 * Bundle Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Mysql4_Bundle extends Mage_CatalogIndex_Model_Mysql4_Data_Abstract
{
    protected function _getSelect($productId, $columns = array())
    {
        return $this->_getReadAdapter()->select()
            ->from(array("bundle_option" => $this->getTable('bundle/option')), array("type", "option_id"))
            ->where("bundle_option.parent_id = ?", $productId)
            ->where("bundle_option.required = 1")
            ->joinLeft(array(
                "bundle_selection" => $this->getTable('bundle/selection')),
                "bundle_selection.option_id = bundle_option.option_id", $columns);
    }

    public function getSelectionsData($productId)
    {
        return $this->_getReadAdapter()->fetchAll($this->_getSelect(
            $productId,
            array("*")
        ));
    }

    public function dropAllQuoteChildItems($productId)
    {
        $result = $this->_getReadAdapter()->fetchRow(
            $this->_getReadAdapter()->select()
                ->from($this->getTable('sales/quote_item'), "GROUP_CONCAT(`item_id`) as items")
                ->where("product_id = ?", $productId));

        if ($result['items'] != '') {
            $this->_getWriteAdapter()
                ->query("DELETE FROM ".$this->getTable('sales/quote_item')."
                        WHERE `parent_item_id` in (". $result['items'] .")");
        }
    }

    public function dropAllUnneededSelections($productId, $ids)
    {
        $this->_getWriteAdapter()
            ->query("DELETE FROM ".$this->getTable('bundle/selection')."
                    WHERE `parent_product_id` = ". $productId . ( count($ids) > 0 ? " and selection_id not in (" . implode(',', $ids) . ")": ''));
    }
}