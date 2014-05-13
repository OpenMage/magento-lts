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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_XmlConnect_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$appTableName = $installer->getTable('xmlconnect/application');
$configTableName = $installer->getTable('xmlconnect/configData');
$historyTableName = $installer->getTable('xmlconnect/history');
$templateTableName = $installer->getTable('xmlconnect/template');
$queueTableName = $installer->getTable('xmlconnect/queue');

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $appTableName,
    'FK_XMLCONNECT_APPLICAION_STORE'
);

$installer->getConnection()->dropForeignKey(
    $historyTableName,
    'FK_XMLCONNECT_HISTORY_APPLICATION'
);

$installer->getConnection()->dropForeignKey(
    $templateTableName,
    'FK_APP_CODE'
);

$installer->getConnection()->dropForeignKey(
    $queueTableName,
    'FK_TEMPLATE_ID'
);

$installer->getConnection()->dropForeignKey(
    $configTableName,
    'FK_31EE36D814216200D7C0723145AC510E'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $appTableName,
    'FK_XMLCONNECT_APPLICAION_STORE'
);

$installer->getConnection()->dropIndex(
    $historyTableName,
    'FK_XMLCONNECT_HISTORY_APPLICATION'
);

$installer->getConnection()->dropIndex(
    $appTableName,
    'UNQ_XMLCONNECT_APPLICATION_CODE'
);

$installer->getConnection()->dropIndex(
    $configTableName,
    'UNQ_XMLCONNECT_CONFIG_DATA_APPLICATION_ID_CATEGORY_PATH'
);

/**
 * Modify fields for 'xmlconnect_notification_template'
 */
$installer->getConnection()->changeColumn(
    $templateTableName,
    'id',
    'template_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'History Id'
    )
);

$installer->getConnection()->addColumn($templateTableName, 'application_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'Application Id'
    )
);

/**
 * Modify fields for 'xmlconnect_queue'
 */
$installer->getConnection()->changeColumn(
    $queueTableName,
    'id',
    'queue_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Queue Id'
    )
);

$installer->getConnection()->dropColumn($queueTableName, 'app_code');

/**
 * Change columns
 */
$tables = array(
    /**
     * Modify table 'xmlconnect_application'
     */
    $appTableName => array(
        'columns' => array(
            'application_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Application Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Application Name'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Application Code'
            ),
            'type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Device Type'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'active_from' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Active From'
            ),
            'active_to'     => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
                'comment'   => 'Active To'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Updated At'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ),
            'browsing_mode' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment' => 'Browsing Mode'
            )
        ),
        'comment' => 'Xmlconnect Application'
    ),
    /**
     * Modify table 'xmlconnect_config_data'
     */
    $configTableName => array(
        'columns' => array(
            'application_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Application Id'
            ),
            'category' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 60,
                'nullable'  => false,
                'default'   => 'default',
                'comment'   => 'Category'
            ),
            'path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 250,
                'nullable'  => false,
                'comment'   => 'Path'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => false,
                'comment'   => 'Value'
            ),
        ),
        'comment' => 'Xmlconnect Configuration Data'
    ),
    /**
     * Modify table 'xmlconnect_history'
     */
    $historyTableName => array(
        'columns' => array(
            'history_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'History Id'
            ),
            'application_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Application Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Created At'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'params' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BLOB,
                'length'    => '64k',
                'comment'   => 'Params'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 200,
                'nullable'  => false,
                'comment'   => 'Title'
            ),
            'activation_key' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Activation Key'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Application Name'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Application Code'
            ),
        ),
        'comment' => 'Xmlconnect History'
    ),
    /**
     * Modify table 'xmlconnect_notification_template'
     */
    $templateTableName => array(
        'columns' => array(
            'template_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Template Id'
            ),
            'application_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Application Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Template Name'
            ),
            'push_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 140,
                'nullable'  => false,
                'comment'   => 'Push Notification Title'
            ),
            'message_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Message Title'
            ),
            'content' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => false,
                'comment'   => 'Message Content'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Created At'
            ),
            'modified_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Modified At'
            ),
        ),
        'comment' => 'Xmlconnect Notification Template'
    ),
    /**
     * Modify table 'xmlconnect_queue'
     */
    $queueTableName => array(
        'columns' => array(
            'queue_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Queue Id'
            ),
            'create_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Created At'
            ),
            'exec_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Scheduled Execution Time'
            ),
            'template_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Template Id'
            ),
            'push_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 140,
                'nullable'  => false,
                'comment'   => 'Push Notification Title'
            ),
            'message_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Message Title'
            ),
            'content' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64k',
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Message Content'
            ),
            'push_title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 140,
                'nullable'  => false,
                'comment'   => 'Push Notification Title'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => 0,
                'comment'   => 'Status'
            ),
            'type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 12,
                'nullable'  => false,
                'comment'   => 'Type of Notification'
            ),
        ),
        'comment' => 'Xmlconnect Notification Queue'
    )
);

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $appTableName,
    $installer->getIdxName($appTableName, array('code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $configTableName,
    $installer->getIdxName(
        $configTableName,
        array('application_id', 'category', 'path'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('application_id', 'category', 'path'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName($appTableName, 'store_id', $installer->getTable('core/store'), 'store_id'),
    $appTableName,
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName($configTableName, 'application_id', $appTableName, 'application_id'),
    $configTableName,
    'application_id',
    $appTableName,
    'application_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName($historyTableName, 'application_id', $appTableName, 'application_id'),
    $historyTableName,
    'application_id',
    $appTableName,
    'application_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName($templateTableName, 'application_id', $appTableName, 'application_id'),
    $templateTableName,
    'application_id',
    $appTableName,
    'application_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName($queueTableName, 'template_id', $templateTableName, 'template_id'),
    $queueTableName,
    'template_id',
    $templateTableName,
    'template_id'
);

$installer->endSetup();
