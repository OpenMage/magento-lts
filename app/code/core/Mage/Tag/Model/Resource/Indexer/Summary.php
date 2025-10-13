<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tag Indexer Model
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Resource_Indexer_Summary extends Mage_Catalog_Model_Resource_Product_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/summary', 'tag_id');
    }

    /**
     * Process tag save
     *
     * @return $this
     */
    public function tagSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_tag_id'])) {
            return $this;
        }

        return $this->aggregate($data['tag_reindex_tag_id']);
    }

    /**
     * Process tag relation save
     *
     * @return $this
     */
    public function tagRelationSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_tag_id'])) {
            return $this;
        }

        return $this->aggregate($data['tag_reindex_tag_id']);
    }

    /**
     * Process product save.
     * Method is responsible for index support when product was saved.
     *
     * @return $this
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_required'])) {
            return $this;
        }

        $tagIds = Mage::getModel('tag/tag_relation')
            ->setProductId($event->getEntityPk())
            ->getRelatedTagIds();

        return $this->aggregate($tagIds);
    }

    /**
     * Process product delete.
     * Method is responsible for index support when product was deleted
     *
     * @return $this
     */
    public function catalogProductDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_tag_ids'])) {
            return $this;
        }

        return $this->aggregate($data['tag_reindex_tag_ids']);
    }

    /**
     * Process product massaction
     *
     * @return $this
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['tag_reindex_tag_ids'])) {
            return $this;
        }

        return $this->aggregate($data['tag_reindex_tag_ids']);
    }

    /**
     * Reindex all tags
     *
     * @return $this
     */
    public function reindexAll()
    {
        return $this->aggregate();
    }

    /**
     * Aggregate tags by specified ids
     *
     * @param null|int|array $tagIds
     * @return $this
     */
    public function aggregate($tagIds = null)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $this->beginTransaction();

        try {
            if (!empty($tagIds)) {
                $writeAdapter->delete(
                    $this->getTable('tag/summary'),
                    ['tag_id IN(?)' => $tagIds],
                );
            } else {
                $writeAdapter->delete($this->getTable('tag/summary'));
            }

            $select = $writeAdapter->select()
                ->from(
                    ['tr' => $this->getTable('tag/relation')],
                    [
                        'tr.tag_id',
                        'tr.store_id',
                        'customers'         => 'COUNT(DISTINCT tr.customer_id)',
                        'products'          => 'COUNT(DISTINCT tr.product_id)',
                        'popularity'        => new Zend_Db_Expr(
                            'COUNT(tr.customer_id) + MIN('
                            . $writeAdapter->getCheckSql(
                                'tp.base_popularity IS NOT NULL',
                                'tp.base_popularity',
                                '0',
                            ) . ')',
                        ),
                        'uses'              => new Zend_Db_Expr('0'), // deprecated since 1.4.0.1
                        'historical_uses'   => new Zend_Db_Expr('0'), // deprecated since 1.4.0.1
                        'base_popularity'   => new Zend_Db_Expr('0'),  // deprecated since 1.4.0.1
                    ],
                )
                ->joinInner(
                    ['cs' => $this->getTable('core/store')],
                    'cs.store_id = tr.store_id',
                    [],
                )
                ->joinInner(
                    ['pw' => $this->getTable('catalog/product_website')],
                    'cs.website_id = pw.website_id AND tr.product_id = pw.product_id',
                    [],
                )
                ->joinInner(
                    ['e' => $this->getTable('catalog/product')],
                    'tr.product_id = e.entity_id',
                    [],
                )
                ->joinLeft(
                    ['tp' => $this->getTable('tag/properties')],
                    'tp.tag_id = tr.tag_id AND tp.store_id = tr.store_id',
                    [],
                )
                ->group([
                    'tr.tag_id',
                    'tr.store_id',
                ])
                ->where('tr.active = 1');

            $statusCond = $writeAdapter->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond);

            $visibilityCond = $writeAdapter
                ->quoteInto('!=?', Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            $this->_addAttributeToSelect($select, 'visibility', 'e.entity_id', 'cs.store_id', $visibilityCond);

            if (!empty($tagIds)) {
                $select->where('tr.tag_id IN(?)', $tagIds);
            }

            Mage::dispatchEvent('prepare_catalog_product_index_select', [
                'select'        => $select,
                'entity_field'  => new Zend_Db_Expr('e.entity_id'),
                'website_field' => new Zend_Db_Expr('cs.website_id'),
                'store_field'   => new Zend_Db_Expr('cs.store_id'),
            ]);

            $writeAdapter->query(
                $select->insertFromSelect($this->getTable('tag/summary'), [
                    'tag_id',
                    'store_id',
                    'customers',
                    'products',
                    'popularity',
                    'uses',            // deprecated since 1.4.0.1
                    'historical_uses', // deprecated since 1.4.0.1
                    'base_popularity',  // deprecated since 1.4.0.1
                ]),
            );

            $selectedFields = [
                'tag_id'            => 'tag_id',
                'store_id'          => new Zend_Db_Expr('0'),
                'customers'         => 'COUNT(DISTINCT customer_id)',
                'products'          => 'COUNT(DISTINCT product_id)',
                'popularity'        => 'COUNT(customer_id)',
                'uses'              => new Zend_Db_Expr('0'), // deprecated since 1.4.0.1
                'historical_uses'   => new Zend_Db_Expr('0'), // deprecated since 1.4.0.1
                'base_popularity'   => new Zend_Db_Expr('0'),  // deprecated since 1.4.0.1
            ];

            $agregateSelect = $writeAdapter->select();
            $agregateSelect->from($this->getTable('tag/relation'), $selectedFields)
                ->group('tag_id')
                ->where('active = 1');

            if (!empty($tagIds)) {
                $agregateSelect->where('tag_id IN(?)', $tagIds);
            }

            $writeAdapter->query(
                $agregateSelect->insertFromSelect($this->getTable('tag/summary'), array_keys($selectedFields)),
            );
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
