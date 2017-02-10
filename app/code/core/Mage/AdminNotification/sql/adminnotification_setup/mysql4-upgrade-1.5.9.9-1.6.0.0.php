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
 * @package     Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_SEVERITY'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_READ'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('adminnotification/inbox'),
    'IDX_IS_REMOVE'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('adminnotification/inbox') => array(
        'columns' => array(
            'notification_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Notification id'
            ),
            'severity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Problem type'
            ),
            'date_added' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Create date'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Description'
            ),
            'url' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Url'
            ),
            'is_read' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification read'
            ),
            'is_remove' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Flag if notification might be removed'
            )
        ),
        'comment' => 'Adminnotification Inbox'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', array('severity')),
    array('severity')
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', array('is_read')),
    array('is_read')
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('adminnotification/inbox'),
    $installer->getIdxName('adminnotification/inbox', array('is_remove')),
    array('is_remove')
);

$installer->endSetup();
