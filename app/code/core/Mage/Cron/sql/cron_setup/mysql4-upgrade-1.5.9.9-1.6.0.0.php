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
 * @package     Mage_Cron
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('cron/schedule'),
    'TASK_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('cron/schedule'),
    'SCHEDULED_AT'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('cron/schedule') => array(
        'columns' => array(
            'schedule_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Schedule Id'
            ),
            'job_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Job Code'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'pending',
                'comment'   => 'Status'
            ),
            'messages' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Messages'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'scheduled_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Scheduled At'
            ),
            'executed_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Executed At'
            ),
            'finished_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Finished At'
            )
        ),
        'comment' => 'Cron Schedule'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('cron/schedule'),
    $installer->getIdxName('cron/schedule', array('job_code')),
    array('job_code')
);

$installer->getConnection()->addIndex(
    $installer->getTable('cron/schedule'),
    $installer->getIdxName('cron/schedule', array('scheduled_at', 'status')),
    array('scheduled_at', 'status')
);

$installer->endSetup();
