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
 * @package     Mage_Widget
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('widget/widget_instance_page'),
    'FK_WIDGET_WIDGET_INSTANCE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('widget/widget_instance_page_layout'),
    'FK_WIDGET_WIDGET_INSTANCE_LAYOUT_UPDATE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('widget/widget_instance_page_layout'),
    'FK_WIDGET_WIDGET_INSTANCE_PAGE_ID'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('widget/widget'),
    'IDX_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('widget/widget_instance_page'),
    'IDX_WIDGET_WIDGET_INSTANCE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    'PAGE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    'IDX_WIDGET_WIDGET_INSTANCE_PAGE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    'IDX_WIDGET_WIDGET_INSTANCE_LAYOUT_UPDATE_ID'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('widget/widget') => array(
        'columns' => array(
            'widget_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Widget Id'
            ),
            'parameters' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Parameters'
            )
        ),
        'comment' => 'Preconfigured Widgets'
    ),
    $installer->getTable('widget/widget_instance') => array(
        'columns' => array(
            'instance_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Instance Id'
            ),
            'package_theme' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Package Theme'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Widget Title'
            ),
            'store_ids' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ids'
            ),
            'widget_parameters' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Widget parameters'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort order'
            )
        ),
        'comment' => 'Instances of Widget for Package Theme'
    ),
    $installer->getTable('widget/widget_instance_page') => array(
        'columns' => array(
            'page_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Page Id'
            ),
            'instance_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Instance Id'
            ),
            'layout_handle' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Layout Handle'
            ),
            'block_reference' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Block Reference'
            ),
            'entities' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Catalog entities (comma separated)'
            )
        ),
        'comment' => 'Instance of Widget on Page'
    ),
    $installer->getTable('widget/widget_instance_page_layout') => array(
        'columns' => array(
            'page_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Page Id'
            ),
            'layout_update_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Layout Update Id'
            )
        ),
        'comment' => 'Layout updates'
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget'),
    'code',
    'widget_code',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Widget code for template directive'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget'),
    'type',
    'widget_type',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Widget Type'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance'),
    'type',
    'instance_type',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Instance Type'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'group',
    'page_group',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 25,
        'comment'   => 'Block Group Type'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'for',
    'page_for',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 25,
        'comment'   => 'For instance entities'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'template',
    'page_template',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Path to widget template'
    )
);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('widget/widget'),
    $installer->getIdxName('widget/widget', array('widget_code')),
    array('widget_code')
);

$installer->getConnection()->addIndex(
    $installer->getTable('widget/widget_instance_page'),
    $installer->getIdxName('widget/widget_instance_page', array('instance_id')),
    array('instance_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    $installer->getIdxName(
        'widget/widget_instance_page_layout',
        array('layout_update_id', 'page_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('layout_update_id', 'page_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    $installer->getIdxName('widget/widget_instance_page_layout', array('page_id')),
    array('page_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('widget/widget_instance_page_layout'),
    $installer->getIdxName('widget/widget_instance_page_layout', array('layout_update_id')),
    array('layout_update_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'widget/widget_instance_page',
        'instance_id',
        'widget/widget_instance',
        'instance_id'
    ),
    $installer->getTable('widget/widget_instance_page'),
    'instance_id',
    $installer->getTable('widget/widget_instance'),
    'instance_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'widget/widget_instance_page_layout',
        'page_id',
        'widget/widget_instance_page',
        'page_id'
    ),
    $installer->getTable('widget/widget_instance_page_layout'),
    'page_id',
    $installer->getTable('widget/widget_instance_page'),
    'page_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'widget/widget_instance_page_layout',
        'layout_update_id',
        'core/layout_update',
        'layout_update_id'
    ),
    $installer->getTable('widget/widget_instance_page_layout'),
    'layout_update_id',
    $installer->getTable('core/layout_update'),
    'layout_update_id'
);

$installer->endSetup();
