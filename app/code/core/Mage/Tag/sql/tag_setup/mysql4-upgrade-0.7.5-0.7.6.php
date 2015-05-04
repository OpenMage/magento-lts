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
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$deprecatedComment = 'deprecated since 1.4.0.1';

$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'), 'uses', "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'"
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'), 'historical_uses',
    "int(11) unsigned NOT NULL default '0' COMMENT '{$deprecatedComment}'"
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('tag/summary'), 'base_popularity',
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
