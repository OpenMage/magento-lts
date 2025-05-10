<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$deprecatedComment = 'deprecated since 1.4.0.1';

$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'uses',
    "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'",
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'historical_uses',
    "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'",
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'base_popularity',
    "int(11) UNSIGNED DEFAULT '0' NOT NULL COMMENT '{$deprecatedComment}'",
);

$installer->run("
    CREATE TABLE {$this->getTable('tag/properties')} (
       `tag_id` int(11) unsigned NOT NULL default '0',
       `store_id` smallint(5) unsigned NOT NULL default '0',
       `base_popularity` int(11) unsigned NOT NULL default '0',
       PRIMARY KEY (`tag_id`,`store_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'TAG_PROPERTIES_TAG',
    $installer->getTable('tag/properties'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id',
);

$installer->getConnection()->addConstraint(
    'TAG_PROPERTIES_STORE',
    $installer->getTable('tag/properties'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);
