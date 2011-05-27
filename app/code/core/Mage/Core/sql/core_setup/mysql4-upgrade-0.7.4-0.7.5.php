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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('core_store_group')};
CREATE TABLE {$this->getTable('core_store_group')} (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `root_category_id` int(10) unsigned NOT NULL default '0',
  `default_store_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `FK_STORE_GROUP_WEBSITE` (`website_id`),
  KEY (`default_store_id`),
  CONSTRAINT `FK_STORE_GROUP_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO {$this->getTable('core_store_group')} VALUES (0,0,'Default',0,0);
ALTER TABLE {$this->getTable('core_store')}
    DROP FOREIGN KEY `FK_STORE_LANGUAGE`;
ALTER TABLE {$this->getTable('core_store')}
    DROP INDEX `FK_STORE_LANGUAGE`;
DROP TABLE IF EXISTS {$this->getTable('core_language')};
ALTER TABLE {$this->getTable('core_store')}
    DROP `language_code`;
ALTER TABLE {$this->getTable('core_store')}
    ADD `group_id` smallint(5) unsigned NOT NULL AFTER `website_id`;
ALTER TABLE {$this->getTable('core_store')}
    ADD INDEX `FK_STORE_GROUP` (`group_id`);
ALTER TABLE {$this->getTable('core_store')}
    ADD CONSTRAINT `FK_STORE_GROUP_STORE` FOREIGN KEY (`group_id`)
    REFERENCES {$this->getTable('core_store_group')} (`group_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('core_website')}
    DROP INDEX `is_active`,
    ADD INDEX (`sort_order`);
ALTER TABLE {$this->getTable('core_website')}
    DROP `is_active`;
ALTER TABLE {$this->getTable('core_website')}
    ADD `default_group_id` smallint(5) unsigned NOT NULL default '0';
ALTER TABLE {$this->getTable('core_website')}
    ADD INDEX (`default_group_id`);
UPDATE {$this->getTable('core_website')}
    SET `default_group_id`='0'
    WHERE `website_id`=0;
");

$websiteRows = $installer->getConnection()
    ->fetchAll($installer->getConnection()
        ->select()
        ->from($this->getTable('core_website'))
        ->where($installer->getConnection()->quoteInto('website_id>?', 0)));

$rows = $installer->getConnection()
    ->fetchAll($installer->getConnection()
        ->select()
        ->from($this->getTable('core_config_data'))
        ->where($installer->getConnection()->quoteInto('path LIKE ?', 'catalog/category/root_id')));
$rootCategoryIds = array();
foreach ($rows as $row) {
    $rootCategoryIds[$row['scope']][$row['scope_id']] = $row['value'];
}

foreach ($websiteRows as $websiteRow) {
    $rootCategoryId = 2;
    if (isset($rootCategoryIds['website'][$websiteRow['website_id']])) {
        $rootCategoryId = $rootCategoryIds['website'][$websiteRow['website_id']];
    }
    elseif (isset($rootCategoryIds['default'][0])) {
        $rootCategoryId = $rootCategoryIds['default'][0];
    }
    $defaultStoreId = (int)$installer->getConnection()
        ->fetchOne($installer->getConnection()
            ->select()
            ->from($this->getTable('core_store'))
            ->where($installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id']))
            ->limit(0, 1), 'store_id');

    // create group for website
    $installer->getConnection()->insert($this->getTable('core_store_group'), array(
        'website_id'        => $websiteRow['website_id'],
        'name'              => $websiteRow['name'] . ' Store',
        'root_category_id'  => $rootCategoryId,
        'default_store_id'  => $defaultStoreId
    ));
    $groupId = $installer->getConnection()->lastInsertId();
    // set group for store(s)
    $installer->getConnection()
        ->update($this->getTable('core_store'),
            array('group_id'=>$groupId),
            $installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id'])
        );
    // set created group as default for website
    $installer->getConnection()
        ->update($this->getTable('core_website'),
            array('default_group_id'=>$groupId),
            $installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id'])
        );
}

$installer->endSetup();
