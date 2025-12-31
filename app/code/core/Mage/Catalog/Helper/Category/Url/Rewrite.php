<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Category url rewrite helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Category_Url_Rewrite implements Mage_Catalog_Helper_Category_Url_Rewrite_Interface
{
    /**
     * Adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

    /**
     * Initialize resource and connection instances
     */
    public function __construct(array $args = [])
    {
        $this->_resource = Mage::getSingleton('core/resource');
        $this->_connection = empty($args['connection']) ? $this->_resource
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE) : $args['connection'];
    }

    /**
     * Join url rewrite table to eav collection
     *
     * @param  int   $storeId
     * @return $this
     */
    public function joinTableToEavCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection, $storeId)
    {
        $collection->joinTable(
            'core/url_rewrite',
            'category_id=entity_id',
            ['request_path'],
            '{{table}}.is_system=1 AND '
                . "{{table}}.store_id='{$storeId}' AND "
                . '{{table}}.category_id IS NOT NULL AND '
                . "{{table}}.id_path LIKE 'category/%'",
            'left',
        );
        return $this;
    }

    /**
     * Join url rewrite table to collection
     *
     * @param  int                                                      $storeId
     * @return $this|Mage_Catalog_Helper_Category_Url_Rewrite_Interface
     */
    public function joinTableToCollection(Mage_Catalog_Model_Resource_Category_Flat_Collection $collection, $storeId)
    {
        $collection->getSelect()->joinLeft(
            ['url_rewrite' => $collection->getTable('core/url_rewrite')],
            'url_rewrite.category_id = main_table.entity_id AND url_rewrite.is_system = 1 '
                . ' AND ' . $collection->getConnection()->quoteInto('url_rewrite.store_id = ?', $storeId)
                . ' AND url_rewrite.category_id IS NOT NULL'
                . ' AND ' . $collection->getConnection()->quoteInto('url_rewrite.id_path LIKE ?', 'category/%'),
            ['request_path'],
        );
        return $this;
    }

    /**
     * Join url rewrite to select
     *
     * @param  int   $storeId
     * @return $this
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
        $select->joinLeft(
            ['url_rewrite' => $this->_resource->getTableName('core/url_rewrite')],
            'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND '
                . $this->_connection->quoteInto(
                    'url_rewrite.store_id = ? AND ',
                    (int) $storeId,
                )
                . 'url_rewrite.category_id IS NOT NULL AND '
                . $this->_connection->prepareSqlCondition('url_rewrite.id_path', ['like' => 'category/%']),
            ['request_path' => 'url_rewrite.request_path'],
        );
        return $this;
    }
}
