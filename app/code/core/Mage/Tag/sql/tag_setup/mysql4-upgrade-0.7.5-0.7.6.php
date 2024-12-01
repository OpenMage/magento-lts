<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$deprecatedComment = 'deprecated since 1.4.0.1';

$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'uses',
    "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'"
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'historical_uses',
    "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'"
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'),
    'base_popularity',
    "int(11) UNSIGNED DEFAULT '0' NOT NULL COMMENT '{$deprecatedComment}'"
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
    'tag_id'
);

$installer->getConnection()->addConstraint(
    'TAG_PROPERTIES_STORE',
    $installer->getTable('tag/properties'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);
