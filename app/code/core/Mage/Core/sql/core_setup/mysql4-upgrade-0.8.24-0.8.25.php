<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $this->getTable('core/flag'),
    'flag_id',
    'INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT',
);

$installer->endSetup();
