<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product url rewrite helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
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
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->_resource = Mage::getSingleton('core/resource');
        $this->_connection = !empty($args['connection']) ? $args['connection'] : $this->_resource
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * Prepare and return select
     *
     * @param array $productIds
     * @param int $categoryId
     * @param int $storeId
     * @return Varien_Db_Select
     */
    public function getTableSelect(array $productIds, $categoryId, $storeId)
    {
        return $this->_connection->select()
            ->from($this->_resource->getTableName('core/url_rewrite'), ['product_id', 'request_path'])
            ->where('store_id = ?', (int)$storeId)
            ->where('is_system = ?', 1)
            ->where('category_id = ? OR category_id IS NULL', (int)$categoryId)
            ->where('product_id IN(?)', $productIds)
            ->order('category_id ' . Varien_Data_Collection::SORT_ORDER_DESC);
    }

    /**
     * Prepare url rewrite left join statement for given select instance and store_id parameter.
     *
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
        $select->joinLeft(
            ['url_rewrite' => $this->_resource->getTableName('core/url_rewrite')],
            'url_rewrite.product_id = main_table.entity_id AND url_rewrite.is_system = 1 AND ' .
                $this->_connection->quoteInto(
                    'url_rewrite.category_id IS NULL AND url_rewrite.store_id = ? AND ',
                    (int)$storeId
                ) .
                $this->_connection->prepareSqlCondition('url_rewrite.id_path', ['like' => 'product/%']),
            ['request_path' => 'url_rewrite.request_path']
        );
        return $this;
    }
}
