<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('eav/attribute');
$installer->getConnection()->addColumn(
    $table,
    'is_filterable_in_search',
    "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1'",
);
$installer->run("
    UPDATE `{$table}` SET is_filterable_in_search=(is_filterable!=0)
");

$installer->endSetup();
