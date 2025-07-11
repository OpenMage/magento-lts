<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Product  Samples resource model
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Link extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/link', 'link_id');
    }

    /**
     * Save title and price of link item
     *
     * @param Mage_Downloadable_Model_Link $linkObject
     * @return $this
     */
    public function saveItemTitleAndPrice($linkObject)
    {
        $writeAdapter   = $this->_getWriteAdapter();
        $linkTitleTable = $this->getTable('downloadable/link_title');
        $linkPriceTable = $this->getTable('downloadable/link_price');

        $select = $writeAdapter->select()
            ->from($this->getTable('downloadable/link_title'))
            ->where('link_id=:link_id AND store_id=:store_id');
        $bind = [
            ':link_id'   => $linkObject->getId(),
            ':store_id'  => (int) $linkObject->getStoreId(),
        ];

        if ($writeAdapter->fetchOne($select, $bind)) {
            $where = [
                'link_id = ?'  => $linkObject->getId(),
                'store_id = ?' => (int) $linkObject->getStoreId(),
            ];
            if ($linkObject->getUseDefaultTitle()) {
                $writeAdapter->delete(
                    $linkTitleTable,
                    $where,
                );
            } else {
                $insertData = ['title' => $linkObject->getTitle()];
                $writeAdapter->update(
                    $linkTitleTable,
                    $insertData,
                    $where,
                );
            }
        } elseif (!$linkObject->getUseDefaultTitle()) {
            $writeAdapter->insert(
                $linkTitleTable,
                [
                    'link_id'   => $linkObject->getId(),
                    'store_id'  => (int) $linkObject->getStoreId(),
                    'title'     => $linkObject->getTitle(),
                ],
            );
        }

        $select = $writeAdapter->select()
            ->from($linkPriceTable)
            ->where('link_id=:link_id AND website_id=:website_id');
        $bind = [
            ':link_id'       => $linkObject->getId(),
            ':website_id'    => (int) $linkObject->getWebsiteId(),
        ];
        if ($writeAdapter->fetchOne($select, $bind)) {
            $where = [
                'link_id = ?'    => $linkObject->getId(),
                'website_id = ?' => $linkObject->getWebsiteId(),
            ];
            if ($linkObject->getUseDefaultPrice()) {
                $writeAdapter->delete(
                    $linkPriceTable,
                    $where,
                );
            } else {
                $writeAdapter->update(
                    $linkPriceTable,
                    ['price' => $linkObject->getPrice()],
                    $where,
                );
            }
        } elseif (!$linkObject->getUseDefaultPrice()) {
            $dataToInsert[] = [
                'link_id'    => $linkObject->getId(),
                'website_id' => (int) $linkObject->getWebsiteId(),
                'price'      => (float) $linkObject->getPrice(),
            ];
            if ($linkObject->getOrigData('link_id') != $linkObject->getLinkId()) {
                $_isNew = true;
            } else {
                $_isNew = false;
            }
            if ($linkObject->getWebsiteId() == 0 && $_isNew && !Mage::helper('catalog')->isPriceGlobal()) {
                $websiteIds = $linkObject->getProductWebsiteIds();
                foreach ($websiteIds as $websiteId) {
                    $baseCurrency = Mage::app()->getBaseCurrencyCode();
                    $websiteCurrency = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
                    if ($websiteCurrency == $baseCurrency) {
                        continue;
                    }
                    $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($websiteCurrency);
                    if (!$rate) {
                        $rate = 1;
                    }
                    $newPrice = $linkObject->getPrice() * $rate;
                    $dataToInsert[] = [
                        'link_id'       => $linkObject->getId(),
                        'website_id'    => (int) $websiteId,
                        'price'         => $newPrice,
                    ];
                }
            }
            $writeAdapter->insertMultiple($linkPriceTable, $dataToInsert);
        }
        return $this;
    }

    /**
     * Delete data by item(s)
     *
     * @param Mage_Downloadable_Model_Link|array|int $items
     * @return $this
     */
    public function deleteItems($items)
    {
        $writeAdapter   = $this->_getWriteAdapter();
        $where = [];
        if ($items instanceof Mage_Downloadable_Model_Link) {
            $where = ['link_id = ?'    => $items->getId()];
        } elseif (is_array($items)) {
            $where = ['link_id in (?)' => $items];
        } else {
            $where = ['sample_id = ?'  => $items];
        }
        if ($where) {
            $writeAdapter->delete(
                $this->getMainTable(),
                $where,
            );
            $writeAdapter->delete(
                $this->getTable('downloadable/link_title'),
                $where,
            );
            $writeAdapter->delete(
                $this->getTable('downloadable/link_price'),
                $where,
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
        $adapter    = $this->_getReadAdapter();
        $ifNullDefaultTitle = $adapter->getIfNullSql('st.title', 's.title');
        $select = $adapter->select()
            ->from(['m' => $this->getMainTable()], null)
            ->join(
                ['s' => $this->getTable('downloadable/link_title')],
                's.link_id=m.link_id AND s.store_id=0',
                [],
            )
            ->joinLeft(
                ['st' => $this->getTable('downloadable/link_title')],
                'st.link_id=m.link_id AND st.store_id=:store_id',
                ['title' => $ifNullDefaultTitle],
            )
            ->where('m.product_id=:product_id');
        $bind = [
            ':store_id'   => (int) $storeId,
            ':product_id' => $productId,
        ];

        return $adapter->fetchCol($select, $bind);
    }
}
