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
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer           = $this;
$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('salesrule/rule');
$websitesTable       = $installer->getTable('core/website');
$customerGroupsTable = $installer->getTable('customer/customer_group');
$rulesWebsitesTable  = $installer->getTable('salesrule/website');
$rulesCustomerGroupsTable  = $installer->getTable('salesrule/customer_group');

$installer->startSetup();
/**
 * Create table 'salesrule/website' if not exists. This table will be used instead of
 * column website_ids of main catalog rules table
 */
$table = $connection->newTable($rulesWebsitesTable)
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Rule Id'
        )
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Website Id'
    )
    ->addIndex(
        $installer->getIdxName('salesrule/website', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/website', array('website_id')),
        array('website_id')
    )
    ->addForeignKey($installer->getFkName('salesrule/website', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id', $rulesTable, 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('salesrule/website', 'website_id', 'core/website', 'website_id'),
        'website_id', $websitesTable, 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Rules To Websites Relations');

$connection->createTable($table);


/**
 * Create table 'salesrule/customer_group' if not exists. This table will be used instead of
 * column customer_group_ids of main catalog rules table
 */
$table = $connection->newTable($rulesCustomerGroupsTable)
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Rule Id'
    )
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Customer Group Id'
    )
    ->addIndex(
        $installer->getIdxName('salesrule/customer_group', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule/customer_group', array('customer_group_id')),
        array('customer_group_id')
    )
    ->addForeignKey($installer->getFkName('salesrule/customer_group', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id', $rulesTable, 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('salesrule/customer_group', 'customer_group_id',
            'customer/customer_group', 'customer_group_id'
        ),
        'customer_group_id', $customerGroupsTable, 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Rules To Customer Groups Relations');

$connection->createTable($table);


/**
 * Fill out relation table 'salesrule/website' with website Ids
 */
$select = $connection->select()
    ->from(array('sr' => $rulesTable), array('sr.rule_id', 'cw.website_id'))
    ->join(
        array('cw' => $websitesTable),
        $connection->prepareSqlCondition(
           'sr.website_ids', array('finset' =>  new Zend_Db_Expr('cw.website_id'))
        ),
        array()
    );
$query = $select->insertFromSelect($rulesWebsitesTable, array('rule_id', 'website_id'));
$connection->query($query);


/**
 * Fill out relation table 'salesrule/customer_group' with customer group Ids
 */

$select = $connection->select()
    ->from(array('sr' => $rulesTable), array('sr.rule_id', 'cg.customer_group_id'))
    ->join(
        array('cg' => $customerGroupsTable),
        $connection->prepareSqlCondition(
            'sr.customer_group_ids', array('finset' =>  new Zend_Db_Expr('cg.customer_group_id'))
        ),
        array()
    );
$query = $select->insertFromSelect($rulesCustomerGroupsTable, array('rule_id', 'customer_group_id'));
$connection->query($query);

/**
 * Eliminate obsolete columns
 */
$connection->dropColumn($rulesTable, 'website_ids');
$connection->dropColumn($rulesTable, 'customer_group_ids');

/**
 * Change default value to "null" for "from" and "to" dates columns
 */
$connection->modifyColumn(
    $rulesTable,
    'from_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$connection->modifyColumn(
    $rulesTable,
    'to_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$installer->endSetup();
