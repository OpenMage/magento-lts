<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Product  Samples resource model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Resource_Sample extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/sample', 'sample_id');
    }

    /**
     * Save title of sample item in store scope
     *
     * @param Mage_Downloadable_Model_Sample $sampleObject
     * @return $this
     */
    public function saveItemTitle($sampleObject)
    {
        $writeAdapter   = $this->_getWriteAdapter();
        $sampleTitleTable = $this->getTable('downloadable/sample_title');
        $bind = [
            ':sample_id' => $sampleObject->getId(),
            ':store_id'  => (int)$sampleObject->getStoreId()
        ];
        $select = $writeAdapter->select()
            ->from($sampleTitleTable)
            ->where('sample_id=:sample_id AND store_id=:store_id');
        if ($writeAdapter->fetchOne($select, $bind)) {
            $where = [
                'sample_id = ?' => $sampleObject->getId(),
                'store_id = ?'  => (int)$sampleObject->getStoreId()
            ];
            if ($sampleObject->getUseDefaultTitle()) {
                $writeAdapter->delete(
                    $sampleTitleTable,
                    $where
                );
            } else {
                $writeAdapter->update(
                    $sampleTitleTable,
                    ['title' => $sampleObject->getTitle()],
                    $where
                );
            }
        } else {
            if (!$sampleObject->getUseDefaultTitle()) {
                $writeAdapter->insert(
                    $sampleTitleTable,
                    [
                        'sample_id' => $sampleObject->getId(),
                        'store_id'  => (int)$sampleObject->getStoreId(),
                        'title'     => $sampleObject->getTitle(),
                    ]
                );
            }
        }
        return $this;
    }

    /**
     * Delete data by item(s)
     *
     * @param Mage_Downloadable_Model_Sample|array|int $items
     * @return $this
     */
    public function deleteItems($items)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $where = '';
        if ($items instanceof Mage_Downloadable_Model_Sample) {
            $where = ['sample_id = ?'    => $items->getId()];
        } else {
            $where = ['sample_id in (?)' => $items];
        }
        if ($where) {
            $writeAdapter->delete(
                $this->getMainTable(),
                $where
            );
            $writeAdapter->delete(
                $this->getTable('downloadable/sample_title'),
                $where
            );
        }
        return $this;
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $ifNullDefaultTitle = $adapter->getIfNullSql('st.title', 'd.title');
        $select = $adapter->select()
            ->from(['m' => $this->getMainTable()], null)
            ->join(
                ['d' => $this->getTable('downloadable/sample_title')],
                'd.sample_id=m.sample_id AND d.store_id=0',
                []
            )
            ->joinLeft(
                ['st' => $this->getTable('downloadable/sample_title')],
                'st.sample_id=m.sample_id AND st.store_id=:store_id',
                ['title' => $ifNullDefaultTitle]
            )
            ->where('m.product_id=:product_id', $productId);
        $bind = [
            ':store_id'   => (int)$storeId,
            ':product_id' => $productId
        ];

        return $adapter->fetchCol($select, $bind);
    }
}
