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
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch Fulltext Index Engine resource model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Fulltext_Engine extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
    }

    /**
     * Add entity data to fulltext search table
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entity 'product'|'cms'
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product')
    {
        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
            'product_id'    => $entityId,
            'store_id'      => $storeId,
            'data_index'    => $index
        ));
        return $this;
    }

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product')
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => &$index) {
            $data[] = array(
                'product_id'    => (int)$entityId,
                'store_id'      => $storeId,
                'data_index'    => $index
            );
        }

        if ($data) {
            $adapter->insertOnDuplicate($this->getMainTable(), $data, array('data_index'));
        }

        return $this;
    }

    /**
     * Remove entity data from fulltext search table
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'|'cms'
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($entityId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('product_id IN(?)', $entityId);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), join(' AND ', $where));

        return $this;
    }

    /**
     * Prepare index array as a string glued by separator
     *
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return Mage::helper('catalogsearch')->prepareIndexdata($index, $separator);
    }

    /**
     * Retrieve fulltext search result data collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    public function getResultCollection()
    {
        return Mage::getResourceModel('catalogsearch/fulltext_collection');
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Define if engine is avaliable
     *
     * @return bool
     */
    public function test()
    {
        return true;
    }
}
