<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'page_group',
    'page_group',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
    ],
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'page_for',
    'page_for',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
    ],
);
