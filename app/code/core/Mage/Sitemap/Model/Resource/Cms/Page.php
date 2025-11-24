<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap cms page collection model
 *
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Cms_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     * Retrieve cms page collection array
     *
     * @param int $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $pages = [];

        $select = $this->_getWriteAdapter()->select()
            ->from(['main_table' => $this->getMainTable()], [$this->getIdFieldName(), 'identifier AS url'])
            ->join(
                ['store_table' => $this->getTable('cms/page_store')],
                'main_table.page_id=store_table.page_id',
                [],
            )
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', [0, $storeId]);
        $query = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            if ($row['url'] == Mage_Cms_Model_Page::NOROUTE_PAGE_ID) {
                continue;
            }

            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @return Varien_Object
     */
    protected function _prepareObject(array $data)
    {
        $object = new Varien_Object();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);

        return $object;
    }
}
