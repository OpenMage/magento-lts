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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category url rewrite helper
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Category_Url_Rewrite
    implements Mage_Catalog_Helper_Category_Url_Rewrite_Interface
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
    public function __construct(array $args = array())
    {
        $this->_resource = Mage::getSingleton('core/resource');
        $this->_connection = !empty($args['connection']) ? $args['connection'] : $this->_resource
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * Join url rewrite table to eav collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite
     */
    public function joinTableToEavCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection, $storeId)
    {
        $collection->joinTable(
            'core/url_rewrite',
            'category_id=entity_id',
            array('request_path'),
            "{{table}}.is_system=1 AND " .
                "{{table}}.store_id='{$storeId}' AND " .
                "{{table}}.id_path LIKE 'category/%'",
            'left'
        );
        return $this;
    }

    /**
     * Join url rewrite table to collection
     *
     * @param Mage_Catalog_Model_Resource_Category_Flat_Collection $collection
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite|Mage_Catalog_Helper_Category_Url_Rewrite_Interface
     */
    public function joinTableToCollection(Mage_Catalog_Model_Resource_Category_Flat_Collection $collection, $storeId)
    {
        $collection->getSelect()->joinLeft(
            array('url_rewrite' => $collection->getTable('core/url_rewrite')),
            'url_rewrite.category_id = main_table.entity_id AND url_rewrite.is_system = 1 '.
                ' AND ' . $collection->getConnection()->quoteInto('url_rewrite.store_id = ?', $storeId).
                ' AND ' . $collection->getConnection()->quoteInto('url_rewrite.id_path LIKE ?', 'category/%'),
            array('request_path')
        );
        return $this;
    }

    /**
     * Join url rewrite to select
     *
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @return Mage_Catalog_Helper_Category_Url_Rewrite
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
        $select->joinLeft(
            array('url_rewrite' => $this->_resource->getTableName('core/url_rewrite')),
            'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND ' .
                $this->_connection->quoteInto('url_rewrite.store_id = ? AND ',
                    (int)$storeId) .
                $this->_connection->prepareSqlCondition('url_rewrite.id_path', array('like' => 'category/%')),
            array('request_path' => 'url_rewrite.request_path'));
        return $this;
    }
}
