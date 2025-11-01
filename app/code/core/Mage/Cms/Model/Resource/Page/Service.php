<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms page service resource model
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Resource_Page_Service extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init cms page service model
     */
    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     * Unlinks from $fromStoreId store pages that have same identifiers as pages in $byStoreId
     *
     * Routine is intended to be used before linking pages of some store ($byStoreId) to other store ($fromStoreId)
     * to prevent duplication of url keys
     *
     * Resolved $byLinkTable can be provided when restoring links from some backup table
     *
     * @param int $fromStoreId
     * @param int $byStoreId
     * @param string $byLinkTable
     * @return $this
     */
    public function unlinkConflicts($fromStoreId, $byStoreId, $byLinkTable = null)
    {
        $readAdapter = $this->_getReadAdapter();

        $linkTable = $this->getTable('cms/page_store');
        $mainTable = $this->getMainTable();
        $byLinkTable = $byLinkTable ? $byLinkTable : $linkTable;

        // Select all page ids of $fromStoreId that have identifiers as some pages in $byStoreId
        $select = $readAdapter->select()
            ->from(['from_link' => $linkTable], 'page_id')
            ->join(
                ['from_entity' => $mainTable],
                $readAdapter->quoteInto(
                    'from_entity.page_id = from_link.page_id AND from_link.store_id = ?',
                    $fromStoreId,
                ),
                [],
            )->join(
                ['by_entity' => $mainTable],
                'from_entity.identifier = by_entity.identifier AND from_entity.page_id != by_entity.page_id',
                [],
            )->join(
                ['by_link' => $byLinkTable],
                $readAdapter->quoteInto('by_link.page_id = by_entity.page_id AND by_link.store_id = ?', $byStoreId),
                [],
            );

        $pageIds = $readAdapter->fetchCol($select);

        // Unlink found pages
        if ($pageIds) {
            $writeAdapter = $this->_getWriteAdapter();
            $where = [
                'page_id IN (?)'   => $pageIds,
                'store_id = ?' => $fromStoreId,
            ];
            $writeAdapter->delete($linkTable, $where);
        }

        return $this;
    }
}
