<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
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
$rootCategoryIds = [];
foreach ($rows as $row) {
    $rootCategoryIds[$row['scope']][$row['scope_id']] = $row['value'];
}

foreach ($websiteRows as $websiteRow) {
    $rootCategoryId = 2;
    if (isset($rootCategoryIds['website'][$websiteRow['website_id']])) {
        $rootCategoryId = $rootCategoryIds['website'][$websiteRow['website_id']];
    } elseif (isset($rootCategoryIds['default'][0])) {
        $rootCategoryId = $rootCategoryIds['default'][0];
    }

    $defaultStoreId = (int) $installer->getConnection()
        ->fetchOne($installer->getConnection()
            ->select()
            ->from($this->getTable('core_store'))
            ->where($installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id']))
            ->limit(0, 1), 'store_id');

    // create group for website
    $installer->getConnection()->insert($this->getTable('core_store_group'), [
        'website_id'        => $websiteRow['website_id'],
        'name'              => $websiteRow['name'] . ' Store',
        'root_category_id'  => $rootCategoryId,
        'default_store_id'  => $defaultStoreId,
    ]);
    $groupId = $installer->getConnection()->lastInsertId();
    // set group for store(s)
    $installer->getConnection()
        ->update(
            $this->getTable('core_store'),
            ['group_id' => $groupId],
            $installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id']),
        );
    // set created group as default for website
    $installer->getConnection()
        ->update(
            $this->getTable('core_website'),
            ['default_group_id' => $groupId],
            $installer->getConnection()->quoteInto('website_id=?', $websiteRow['website_id']),
        );
}

$installer->endSetup();
