<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product url rewrite helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Product_Url_Rewrite implements Mage_Catalog_Helper_Product_Url_Rewrite_Interface
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
     * Prepare and return select
     *
     * @param  int              $categoryId
     * @param  int              $storeId
     * @return Varien_Db_Select
     */
    public function getTableSelect(array $productIds, $categoryId, $storeId)
    {
        return $this->_connection->select()
            ->from($this->_resource->getTableName('core/url_rewrite'), ['product_id', 'request_path'])
            ->where('store_id = ?', (int) $storeId)
            ->where('is_system = ?', 1)
            ->where('category_id = ? OR category_id IS NULL', (int) $categoryId)
            ->where('product_id IN(?)', $productIds)
            ->order('category_id ' . Varien_Data_Collection::SORT_ORDER_DESC);
    }

    /**
     * Prepare url rewrite left join statement for given select instance and store_id parameter.
     *
     * @param  int                                               $storeId
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
        $select->joinLeft(
            ['url_rewrite' => $this->_resource->getTableName('core/url_rewrite')],
            'url_rewrite.product_id = main_table.entity_id AND url_rewrite.is_system = 1 AND '
                . $this->_connection->quoteInto(
                    'url_rewrite.category_id IS NULL AND url_rewrite.store_id = ? AND ',
                    (int) $storeId,
                )
                . $this->_connection->prepareSqlCondition('url_rewrite.id_path', ['like' => 'product/%']),
            ['request_path' => 'url_rewrite.request_path'],
        );
        return $this;
    }
}
