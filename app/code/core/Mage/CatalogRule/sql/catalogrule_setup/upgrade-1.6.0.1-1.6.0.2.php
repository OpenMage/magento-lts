<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer           = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('catalogrule/rule');
$websitesTable       = $installer->getTable('core/website');
$customerGroupsTable = $installer->getTable('customer/customer_group');
$rulesWebsitesTable  = $installer->getTable('catalogrule/website');
$rulesCustomerGroupsTable  = $installer->getTable('catalogrule/customer_group');

$installer->startSetup();
/**
 * Create table 'catalogrule/website' if not exists. This table will be used instead of
 * column website_ids of main catalog rules table
 */
if (!$connection->isTableExists($rulesWebsitesTable)) {
    $table = $connection->newTable($rulesWebsitesTable)
        ->addColumn(
            'rule_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ],
            'Rule Id',
        )
        ->addColumn(
            'website_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ],
            'Website Id',
        )
        ->addIndex(
            $installer->getIdxName('catalogrule/website', ['rule_id']),
            ['rule_id'],
        )
        ->addIndex(
            $installer->getIdxName('catalogrule/website', ['website_id']),
            ['website_id'],
        )
        ->addForeignKey(
            $installer->getFkName('catalogrule/website', 'rule_id', 'catalogrule/rule', 'rule_id'),
            'rule_id',
            $rulesTable,
            'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->addForeignKey(
            $installer->getFkName('catalogrule/website', 'website_id', 'core/website', 'website_id'),
            'website_id',
            $websitesTable,
            'website_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->setComment('Catalog Rules To Websites Relations');

    $connection->createTable($table);
}

/**
 * Create table 'catalogrule/customer_group' if not exists. This table will be used instead of
 * column customer_group_ids of main catalog rules table
 */
if (!$connection->isTableExists($rulesCustomerGroupsTable)) {
    $table = $connection->newTable($rulesCustomerGroupsTable)
        ->addColumn(
            'rule_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ],
            'Rule Id',
        )
        ->addColumn(
            'customer_group_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ],
            'Customer Group Id',
        )
        ->addIndex(
            $installer->getIdxName('catalogrule/customer_group', ['rule_id']),
            ['rule_id'],
        )
        ->addIndex(
            $installer->getIdxName('catalogrule/customer_group', ['customer_group_id']),
            ['customer_group_id'],
        )
        ->addForeignKey(
            $installer->getFkName('catalogrule/customer_group', 'rule_id', 'catalogrule/rule', 'rule_id'),
            'rule_id',
            $rulesTable,
            'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->addForeignKey(
            $installer->getFkName(
                'catalogrule/customer_group',
                'customer_group_id',
                'customer/customer_group',
                'customer_group_id',
            ),
            'customer_group_id',
            $customerGroupsTable,
            'customer_group_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE,
        )
        ->setComment('Catalog Rules To Customer Groups Relations');

    $connection->createTable($table);
}

/**
 * Fill out relation table 'catalogrule/website' with website Ids
 */
if ($connection->tableColumnExists($rulesTable, 'website_ids')) {
    $select = $connection->select()
        ->from(['sr' => $rulesTable], ['sr.rule_id', 'cw.website_id'])
        ->join(
            ['cw' => $websitesTable],
            $connection->prepareSqlCondition(
                'sr.website_ids',
                ['finset' =>  new Zend_Db_Expr('cw.website_id')],
            ),
            [],
        );
    $query = $select->insertFromSelect($rulesWebsitesTable, ['rule_id', 'website_id']);
    $connection->query($query);
}

/**
 * Fill out relation table 'catalogrule/customer_group' with customer group Ids
 */
if ($connection->tableColumnExists($rulesTable, 'customer_group_ids')) {
    $select = $connection->select()
        ->from(['sr' => $rulesTable], ['sr.rule_id', 'cg.customer_group_id'])
        ->join(
            ['cg' => $customerGroupsTable],
            $connection->prepareSqlCondition(
                'sr.customer_group_ids',
                ['finset' =>  new Zend_Db_Expr('cg.customer_group_id')],
            ),
            [],
        );
    $query = $select->insertFromSelect($rulesCustomerGroupsTable, ['rule_id', 'customer_group_id']);
    $connection->query($query);
}

/**
 * Eliminate obsolete columns
 */
$connection->dropColumn($rulesTable, 'website_ids');
$connection->dropColumn($rulesTable, 'customer_group_ids');

$installer->endSetup();
