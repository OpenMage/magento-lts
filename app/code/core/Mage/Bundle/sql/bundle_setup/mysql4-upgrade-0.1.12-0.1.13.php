<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
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
    'selection_id',
);

$installer->getConnection()->addConstraint(
    'FK_BUNDLE_PRICE_SELECTION_WEBSITE',
    $this->getTable('bundle/selection_price'),
    'website_id',
    $this->getTable('core_website'),
    'website_id',
);
