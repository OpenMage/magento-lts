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


$installer  = $this;
$connection = $installer->getConnection();
/* @var $installer Mage_Core_Model_Resource_Setup */
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('catalogsearch_fulltext')}`;
CREATE TABLE `{$installer->getTable('catalogsearch_fulltext')}` (
  `product_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `data_index` longtext NOT NULL,
  PRIMARY KEY (`product_id`,`store_id`),
  FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('catalogsearch_result')}` (
  `query_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `relevance` decimal(6,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`query_id`,`product_id`),
  KEY `IDX_QUERY` (`query_id`),
  KEY `IDX_PRODUCT` (`product_id`),
  KEY `IDX_RELEVANCE` (`query_id`, `relevance`),
  CONSTRAINT `FK_CATALOGSEARCH_RESULT_QUERY` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOGSEARCH_RESULT_CATALOG_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$connection->dropForeignKey($installer->getTable('catalogsearch_query'), 'FK_catalogsearch_query');
$connection->dropKey($installer->getTable('catalogsearch_query'), 'FK_catalogsearch_query');
$connection->addConstraint('FK_CATALOGSEARCH_QUERY_STORE',
    $installer->getTable('catalogsearch_query'), 'store_id',
    $installer->getTable('core_store'), 'store_id'
);
$connection->addColumn($installer->getTable('catalogsearch_query'), 'is_active', 'tinyint(1) DEFAULT 1 AFTER `display_in_terms`');
$connection->addColumn($installer->getTable('catalogsearch_query'), 'is_processed', 'tinyint(1) DEFAULT 0 AFTER `is_active`');

$connection->dropKey($installer->getTable('catalogsearch_query'), 'search_query');
$connection->addKey($installer->getTable('catalogsearch_query'), 'IDX_SEARCH_QUERY', array(
    'query_text', 'store_id', 'popularity'
));

$installer->endSetup();

//Mage::app()->reinitStores();
//Mage::getModel('catalogsearch/fulltext')->rebuildIndex();
