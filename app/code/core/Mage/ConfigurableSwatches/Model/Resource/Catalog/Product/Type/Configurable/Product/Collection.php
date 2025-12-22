<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection extends Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
{
    /**
     * Filter by parent product set
     *
     * @return $this
     */
    public function addProductSetFilter(array $productIds)
    {
        $this->getSelect()->where('link_table.parent_id in (?)', $productIds);
        return $this;
    }

    /**
     * Load unique entities records into items
     *
     * @param  bool      $printQuery
     * @param  bool      $logQuery
     * @return $this
     * @throws Exception
     */
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        if ($this->_pageSize) {
            $this->getSelect()->limitPage($this->getCurPage(), $this->_pageSize);
        }

        $this->printLogQuery($printQuery, $logQuery);

        try {
            /**
             * Prepare select query
             */
            $query = $this->_prepareSelect($this->getSelect());
            $rows = $this->_fetchAll($query);
        } catch (Exception $exception) {
            Mage::printException($exception, $query);
            $this->printLogQuery(true, true, $query);
            throw $exception;
        }

        foreach ($rows as $row) {
            if (!isset($this->_items[$row['entity_id']])) {
                $object = $this->getNewEmptyItem()
                    ->setData($row)
                    ->setParentIds([$row['parent_id']]);
                $this->addItem($object);
                if (isset($this->_itemsById[$object->getId()])) {
                    $this->_itemsById[$object->getId()][] = $object;
                } else {
                    $this->_itemsById[$object->getId()] = [$object];
                }
            } else {
                $parents = $this->_items[$row['entity_id']]->getParentIds();
                $parents[] = $row['parent_id'];
                $this->_items[$row['entity_id']]->setParentIds($parents);
            }
        }

        return $this;
    }
}
