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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->run("
CREATE TABLE {$this->getTable('bundle/selection_price')} (
    `selection_id` int(10) unsigned NOT NULL,
    `website_id` smallint(5) unsigned NOT NULL,
    `selection_price_type` tinyint(1) unsigned NOT NULL default '0',
    `selection_price_value` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`selection_id`, `website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_BUNDLE_PRICE_SELECTION_ID',
    $this->getTable('bundle/selection_price'),
    'selection_id',
    $this->getTable('bundle/selection'),
    'selection_id'
);

$installer->getConnection()->addConstraint(
    'FK_BUNDLE_PRICE_SELECTION_WEBSITE',
    $this->getTable('bundle/selection_price'),
    'website_id',
    $this->getTable('core_website'),
    'website_id'
);
