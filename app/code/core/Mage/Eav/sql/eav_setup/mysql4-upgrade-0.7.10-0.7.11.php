<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
