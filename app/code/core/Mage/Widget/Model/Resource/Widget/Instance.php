<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance Resource Model
 *
 * @package    Mage_Widget
 * @phpstan-type LayoutLink array{store_id: int, area: string, package: string, theme: string}
 * @phpstan-type LayoutUpdate array{layout_update_id: int, handle: string, sort_order: int, xml: string, links: list<LayoutLink>}
 * @phpstan-type LayoutUpdateRemoval array{layout_update_id: int, handle: string, sort_order: int, xml: string, links: list<LayoutLink>, instance_id: int, type: string, reason: string}
 */
class Mage_Widget_Model_Resource_Widget_Instance extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('widget/widget_instance', 'instance_id');
    }

    /**
     * Perform actions after object load
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('widget/widget_instance_page'))
            ->where('instance_id = ?', (int) $object->getId());
        $result = $adapter->fetchAll($select);
        $object->setData('page_groups', $result);
        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @inheritDoc
     * @param  Mage_Widget_Model_Widget_Instance $object
     * @throws Zend_Db_Adapter_Exception
     */
    #[Override]
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $pageTable         = $this->getTable('widget/widget_instance_page');
        $pageLayoutTable   = $this->getTable('widget/widget_instance_page_layout');
        $readAdapter       = $this->_getReadAdapter();
        $writeAdapter      = $this->_getWriteAdapter();

        $select = $readAdapter->select()
            ->from($pageTable, 'page_id')
            ->where('instance_id = ?', (int) $object->getId());
        $pageIds = $readAdapter->fetchCol($select);

        $removePageIds = array_diff($pageIds, $object->getDataByKey('page_group_ids'));

        if (is_array($pageIds) && $pageIds !== []) {
            $inCond = $readAdapter->prepareSqlCondition('page_id', ['in' => $pageIds]);

            $select = $readAdapter->select()
                ->from($pageLayoutTable, 'layout_update_id')
                ->where($inCond);
            $removeLayoutUpdateIds = $readAdapter->fetchCol($select);

            $writeAdapter->delete($pageLayoutTable, $inCond);
            $this->_deleteLayoutUpdates($removeLayoutUpdateIds);
        }

        $this->_deleteWidgetInstancePages($removePageIds);

        foreach ($object->getDataByKey('page_groups') as $pageGroup) {
            $pageLayoutUpdateIds = $this->_saveLayoutUpdates($object, $pageGroup);
            $data = [
                'page_group'      => $pageGroup['group'],
                'layout_handle'   => $pageGroup['layout_handle'],
                'block_reference' => $pageGroup['block_reference'],
                'page_for'        => $pageGroup['for'],
                'entities'        => $pageGroup['entities'],
                'page_template'   => $pageGroup['template'],
            ];
            $pageId = $pageGroup['page_id'];
            if (in_array($pageGroup['page_id'], $pageIds)) {
                $writeAdapter->update($pageTable, $data, ['page_id = ?' => (int) $pageId]);
            } else {
                $writeAdapter->insert(
                    $pageTable,
                    array_merge(
                        ['instance_id' => $object->getId()],
                        $data,
                    ),
                );
                $pageId = $writeAdapter->lastInsertId($pageTable);
            }

            foreach ($pageLayoutUpdateIds as $layoutUpdateId) {
                $writeAdapter->insert($pageLayoutTable, [
                    'page_id' => $pageId,
                    'layout_update_id' => $layoutUpdateId,
                ]);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Prepare and save layout updates data
     *
     * @param  Mage_Widget_Model_Widget_Instance $widgetInstance
     * @param  array                             $pageGroupData
     * @return array                             of inserted layout updates ids
     */
    protected function _saveLayoutUpdates($widgetInstance, $pageGroupData)
    {
        $writeAdapter          = $this->_getWriteAdapter();
        $pageLayoutUpdateIds   = [];
        $storeIds              = $this->_prepareStoreIds($widgetInstance->getStoreIds());
        $layoutUpdateTable     = $this->getTable('core/layout_update');
        $layoutUpdateLinkTable = $this->getTable('core/layout_link');

        foreach ($pageGroupData['layout_handle_updates'] as $handle) {
            $xml = $widgetInstance->generateLayoutUpdateXml(
                $pageGroupData['block_reference'],
                $pageGroupData['template'],
            );
            $insert = [
                'handle'     => $handle,
                'xml'        => $xml,
            ];
            if (strlen($widgetInstance->getSortOrder())) {
                $insert['sort_order'] = $widgetInstance->getSortOrder();
            }

            $writeAdapter->insert($layoutUpdateTable, $insert);
            $layoutUpdateId = $writeAdapter->lastInsertId($layoutUpdateTable);
            $pageLayoutUpdateIds[] = $layoutUpdateId;

            $data = [];
            foreach ($storeIds as $storeId) {
                $data[] = [
                    'store_id'         => $storeId,
                    'area'             => $widgetInstance->getArea(),
                    'package'          => $widgetInstance->getPackage(),
                    'theme'            => $widgetInstance->getTheme(),
                    'layout_update_id' => $layoutUpdateId];
            }

            $writeAdapter->insertMultiple($layoutUpdateLinkTable, $data);
        }

        return $pageLayoutUpdateIds;
    }

    /**
     * Prepare store ids.
     * If one of store id is default (0) return all store ids
     *
     * @param  array $storeIds
     * @return array
     */
    protected function _prepareStoreIds($storeIds)
    {
        if (in_array('0', $storeIds)) {
            return [0];
        }

        return $storeIds;
    }

    /**
     * Perform actions before object delete.
     * Collect page ids and layout update ids and set to object for further delete
     *
     * @return $this
     */
    #[Override]
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from(['main_table' => $this->getTable('widget/widget_instance_page')], [])
            ->joinInner(
                ['layout_page_table' => $this->getTable('widget/widget_instance_page_layout')],
                'layout_page_table.page_id = main_table.page_id',
                ['layout_update_id'],
            )
            ->where('main_table.instance_id=?', $object->getId());
        $result = $writeAdapter->fetchCol($select);
        $object->setLayoutUpdateIdsToDelete($result);
        return $this;
    }

    /**
     * Perform actions after object delete.
     * Delete layout updates by layout update ids collected in _beforeSave
     *
     * @inheritDoc
     */
    #[Override]
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_deleteLayoutUpdates($object->getLayoutUpdateIdsToDelete());
        return parent::_afterDelete($object);
    }

    /**
     * Delete widget instance pages by given ids
     *
     * @param  array $pageIds
     * @return $this
     */
    protected function _deleteWidgetInstancePages($pageIds)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($pageIds) {
            $inCond = $writeAdapter->prepareSqlCondition('page_id', [
                'in' => $pageIds,
            ]);
            $writeAdapter->delete(
                $this->getTable('widget/widget_instance_page'),
                $inCond,
            );
        }

        return $this;
    }

    /**
     * Delete layout updates by given ids
     *
     * @param  array $layoutUpdateIds
     * @return $this
     */
    protected function _deleteLayoutUpdates($layoutUpdateIds)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($layoutUpdateIds) {
            $inCond = $writeAdapter->prepareSqlCondition('layout_update_id', [
                'in' => $layoutUpdateIds,
            ]);
            $writeAdapter->delete(
                $this->getTable('core/layout_update'),
                $inCond,
            );
        }

        return $this;
    }

    /**
     * Delete the layout updates of widget instances that the widget generator would not produce
     *
     * A layout update is removed when its widget instance has a type that no enabled module
     * declares in a widget.xml, or when its XML does not have the exact shape that
     * Mage_Widget_Model_Widget_Instance::generateLayoutUpdateXml() produces for the instance's type.
     * The widget instance and its page rows are kept: saving the instance again (once its type is
     * declared) regenerates its layout updates. Running this again removes nothing more.
     *
     * Layout updates are read in batches, in id order. Each batch's removals are passed to
     * $beforeDelete before they are deleted, so a run that stops part way has reported everything
     * it deleted. A layout update is deleted together with its core_layout_link and
     * widget_instance_page_layout rows: setup scripts run with foreign key checks off, so the
     * ON DELETE CASCADE of those tables cannot be relied on.
     *
     * @param  null|(callable(list<LayoutUpdateRemoval>): void) $beforeDelete receives each batch of removals before it is deleted
     * @param  int                                              $batchSize    number of layout updates read per query
     * @return int                                              number of layout updates deleted
     */
    public function deleteUnexpectedLayoutUpdates(?callable $beforeDelete = null, int $batchSize = 200): int
    {
        $adapter   = $this->_getWriteAdapter();
        $batchSize = max(1, $batchSize);
        $deleted   = 0;
        $lastId    = 0;

        /** @var Mage_Widget_Model_Widget_Instance $checker */
        $checker = Mage::getModel('widget/widget_instance');

        do {
            // Layout updates that belong to a widget instance, through its page groups
            $ids = array_map(intval(...), $adapter->fetchCol(
                $adapter->select()
                    ->distinct()
                    ->from(['page_layout' => $this->getTable('widget/widget_instance_page_layout')], ['layout_update_id'])
                    ->join(['page' => $this->getTable('widget/widget_instance_page')], 'page.page_id = page_layout.page_id', [])
                    ->join(['instance' => $this->getMainTable()], 'instance.instance_id = page.instance_id', [])
                    ->where('page_layout.layout_update_id > ?', $lastId)
                    ->order('page_layout.layout_update_id ' . Varien_Db_Select::SQL_ASC)
                    ->limit($batchSize),
            ));
            $fetched = count($ids);
            if ($fetched === 0) {
                break;
            }

            $lastId = max($ids);

            $rows = $adapter->fetchAll(
                $adapter->select()
                    ->from(['layout_update' => $this->getTable('core/layout_update')], ['layout_update_id', 'handle', 'xml', 'sort_order'])
                    ->join(['page_layout' => $this->getTable('widget/widget_instance_page_layout')], 'page_layout.layout_update_id = layout_update.layout_update_id', [])
                    ->join(['page' => $this->getTable('widget/widget_instance_page')], 'page.page_id = page_layout.page_id', [])
                    ->join(['instance' => $this->getMainTable()], 'instance.instance_id = page.instance_id', ['instance_id', 'instance_type'])
                    ->where('layout_update.layout_update_id IN (?)', $ids)
                    ->order(['layout_update.layout_update_id ' . Varien_Db_Select::SQL_ASC, 'instance.instance_id ' . Varien_Db_Select::SQL_ASC]),
            ) ?? [];

            $removals = [];
            foreach ($rows as $row) {
                $layoutUpdateId = (int) $row['layout_update_id'];
                if (isset($removals[$layoutUpdateId])) {
                    continue;
                }

                // A layout update shared by several instances is removed if any of them would not generate it
                $type = (string) $row['instance_type'];
                $reason = $checker->setType($type)->getLayoutUpdateRemovalReason((string) $row['xml']);
                if ($reason === null) {
                    continue;
                }

                $removals[$layoutUpdateId] = [
                    'layout_update_id' => $layoutUpdateId,
                    'handle'           => (string) $row['handle'],
                    'sort_order'       => (int) $row['sort_order'],
                    'xml'              => (string) $row['xml'],
                    'links'            => [],
                    'instance_id'      => (int) $row['instance_id'],
                    'type'             => $type,
                    'reason'           => $reason,
                ];
            }

            if ($removals === []) {
                continue;
            }

            $links = $this->_fetchLayoutLinks(array_keys($removals));
            foreach (array_keys($removals) as $layoutUpdateId) {
                $removals[$layoutUpdateId]['links'] = $links[$layoutUpdateId] ?? [];
            }

            if ($beforeDelete !== null) {
                $beforeDelete(array_values($removals));
            }

            $this->_deleteLayoutUpdatesWithLinks(array_keys($removals));
            $deleted += count($removals);
        } while ($fetched === $batchSize);

        return $deleted;
    }

    /**
     * Find layout updates that are linked to the storefront but belong to no widget instance, and that the widget generator would not produce
     *
     * Such rows still render, but nothing in the admin shows them. They are only reported, not
     * deleted: core_layout_update is also written by other modules, so a row without a widget
     * instance is not known to be a widget's. Empty updates, and updates with the generated shape
     * for a declared widget type (left behind by a widget instance deleted outside the admin), are
     * not reported.
     *
     * @param  callable(list<LayoutUpdate>): void $report    receives each batch of layout updates found
     * @param  int                                $batchSize number of layout updates read per query
     * @return int                                number of layout updates reported
     */
    public function reportUnownedLayoutUpdates(callable $report, int $batchSize = 200): int
    {
        $adapter   = $this->_getWriteAdapter();
        $batchSize = max(1, $batchSize);
        $reported  = 0;
        $lastId    = 0;

        /** @var Mage_Widget_Model_Widget_Instance $checker */
        $checker = Mage::getModel('widget/widget_instance');

        $linked = $adapter->select()
            ->from(['link' => $this->getTable('core/layout_link')], [new Zend_Db_Expr('1')])
            ->where('link.layout_update_id = layout_update.layout_update_id');
        $owned = $adapter->select()
            ->from(['page_layout' => $this->getTable('widget/widget_instance_page_layout')], [new Zend_Db_Expr('1')])
            ->join(['page' => $this->getTable('widget/widget_instance_page')], 'page.page_id = page_layout.page_id', [])
            ->join(['instance' => $this->getMainTable()], 'instance.instance_id = page.instance_id', [])
            ->where('page_layout.layout_update_id = layout_update.layout_update_id');

        do {
            $rows = $adapter->fetchAll(
                $adapter->select()
                    ->from(['layout_update' => $this->getTable('core/layout_update')], ['layout_update_id', 'handle', 'xml', 'sort_order'])
                    ->where('layout_update.layout_update_id > ?', $lastId)
                    ->where('EXISTS (' . $linked . ')')
                    ->where('NOT EXISTS (' . $owned . ')')
                    ->order('layout_update.layout_update_id ' . Varien_Db_Select::SQL_ASC)
                    ->limit($batchSize),
            ) ?? [];
            $fetched = count($rows);
            if ($fetched === 0) {
                break;
            }

            $found = [];
            foreach ($rows as $row) {
                $layoutUpdateId = (int) $row['layout_update_id'];
                $lastId = max($lastId, $layoutUpdateId);

                $xml  = (string) $row['xml'];
                $type = $checker->getGeneratedLayoutUpdateBlockType($xml);
                if (trim($xml) === '' || ($type !== null && $checker->setType($type)->isTypeDeclared())) {
                    continue;
                }

                $found[$layoutUpdateId] = [
                    'layout_update_id' => $layoutUpdateId,
                    'handle'           => (string) $row['handle'],
                    'sort_order'       => (int) $row['sort_order'],
                    'xml'              => $xml,
                    'links'            => [],
                ];
            }

            if ($found === []) {
                continue;
            }

            $links = $this->_fetchLayoutLinks(array_keys($found));
            foreach (array_keys($found) as $layoutUpdateId) {
                $found[$layoutUpdateId]['links'] = $links[$layoutUpdateId] ?? [];
            }

            $report(array_values($found));
            $reported += count($found);
        } while ($fetched === $batchSize);

        return $reported;
    }

    /**
     * Stores and themes that layout updates are linked to, by layout update id
     *
     * @param  list<int>                    $layoutUpdateIds
     * @return array<int, list<LayoutLink>>
     */
    protected function _fetchLayoutLinks(array $layoutUpdateIds): array
    {
        $adapter = $this->_getWriteAdapter();
        $rows = $adapter->fetchAll(
            $adapter->select()
                ->from($this->getTable('core/layout_link'), ['layout_update_id', 'store_id', 'area', 'package', 'theme'])
                ->where('layout_update_id IN (?)', $layoutUpdateIds)
                ->order('layout_link_id ' . Varien_Db_Select::SQL_ASC),
        ) ?? [];

        $links = [];
        foreach ($rows as $row) {
            $links[(int) $row['layout_update_id']][] = [
                'store_id' => (int) $row['store_id'],
                'area'     => (string) $row['area'],
                'package'  => (string) $row['package'],
                'theme'    => (string) $row['theme'],
            ];
        }

        return $links;
    }

    /**
     * Delete layout updates and the rows that link them to stores and widget pages, in one transaction
     *
     * Unlike _deleteLayoutUpdates(), this does not rely on ON DELETE CASCADE, so it is safe with
     * foreign key checks off (as in setup scripts).
     *
     * @param  list<int> $layoutUpdateIds
     * @return $this
     * @throws Throwable
     */
    protected function _deleteLayoutUpdatesWithLinks(array $layoutUpdateIds)
    {
        if ($layoutUpdateIds === []) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $where = ['layout_update_id IN (?)' => $layoutUpdateIds];
        $adapter->beginTransaction();
        try {
            $adapter->delete($this->getTable('core/layout_link'), $where);
            $adapter->delete($this->getTable('widget/widget_instance_page_layout'), $where);
            $adapter->delete($this->getTable('core/layout_update'), $where);
            $adapter->commit();
        } catch (Throwable $throwable) {
            $adapter->rollBack();
            throw $throwable;
        }

        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param  int   $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'store_ids')
            ->where("{$this->getIdFieldName()} = ?", (int) $id);
        $storeIds = $adapter->fetchOne($select);
        return $storeIds ? explode(',', $storeIds) : [];
    }
}
