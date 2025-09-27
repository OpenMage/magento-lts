<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Resource Model
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Resource_Bundle extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Preparing select for getting selection's raw data by product id
     * also can be specified extra parameter for limit which columns should be selected
     *
     * @param int $productId
     * @param array $columns
     * @return Zend_Db_Select
     */
    protected function _getSelect($productId, $columns = [])
    {
        return $this->_getReadAdapter()->select()
            ->from(['bundle_option' => $this->getTable('bundle/option')], ['type', 'option_id'])
            ->where('bundle_option.parent_id = ?', $productId)
            ->where('bundle_option.required = 1')
            ->joinLeft(
                [
                    'bundle_selection' => $this->getTable('bundle/selection')],
                'bundle_selection.option_id = bundle_option.option_id',
                $columns,
            );
    }

    /**
     * Retrieve selection data for specified product id
     *
     * @param int $productId
     * @return array
     */
    public function getSelectionsData($productId)
    {
        return $this->_getReadAdapter()->fetchAll($this->_getSelect(
            $productId,
            ['*'],
        ));
    }

    /**
     * Removing all quote items for specified product
     *
     * @param int $productId
     */
    public function dropAllQuoteChildItems($productId)
    {
        $quoteItemIds = $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
            ->from($this->getTable('sales/quote_item'), ['item_id'])
            ->where('product_id = :product_id'),
            ['product_id' => $productId],
        );

        if ($quoteItemIds) {
            $this->_getWriteAdapter()->delete(
                $this->getTable('sales/quote_item'),
                ['parent_item_id IN(?)' => $quoteItemIds],
            );
        }
    }

    /**
     * Removes specified selections by ids for specified product id
     *
     * @param int $productId
     * @param array $ids
     */
    public function dropAllUnneededSelections($productId, $ids)
    {
        $where = [
            'parent_product_id = ?' => $productId,
        ];
        if (!empty($ids)) {
            $where['selection_id NOT IN (?) '] = $ids;
        }
        $this->_getWriteAdapter()
            ->delete($this->getTable('bundle/selection'), $where);
    }

    /**
     * Save product relations
     *
     * @param int $parentId
     * @param array $childIds
     * @return $this
     */
    public function saveProductRelations($parentId, $childIds)
    {
        Mage::getResourceSingleton('catalog/product_relation')
            ->processRelations($parentId, $childIds);

        return $this;
    }
}
