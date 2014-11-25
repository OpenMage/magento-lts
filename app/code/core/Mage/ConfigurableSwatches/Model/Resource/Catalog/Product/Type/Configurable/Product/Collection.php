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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
{
    /**
     * Filter by parent product set
     *
     * @param array $productIds
     */
    public function addProductSetFilter(array $productIds) {
        $this->getSelect()->where('link_table.parent_id in (?)', $productIds);
    }

    /**
     * Load unique entities records into items
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @throws Exception
     * @return Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection
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
             * @var string $query
             */
            $query = $this->_prepareSelect($this->getSelect());
            $rows = $this->_fetchAll($query);
        } catch (Exception $e) {
            Mage::printException($e, $query);
            $this->printLogQuery(true, true, $query);
            throw $e;
        }

        foreach ($rows as $v) {
            if (!isset($this->_items[$v['entity_id']])) {
                $object = $this->getNewEmptyItem()
                    ->setData($v)
                    ->setParentIds(array($v['parent_id']));
                $this->addItem($object);
                if (isset($this->_itemsById[$object->getId()])) {
                    $this->_itemsById[$object->getId()][] = $object;
                } else {
                    $this->_itemsById[$object->getId()] = array($object);
                }
            } else {
                $parents = $this->_items[$v['entity_id']]->getParentIds();
                $parents[] = $v['parent_id'];
                $this->_items[$v['entity_id']]->setParentIds($parents);
            }
        }

        return $this;
    }
}
