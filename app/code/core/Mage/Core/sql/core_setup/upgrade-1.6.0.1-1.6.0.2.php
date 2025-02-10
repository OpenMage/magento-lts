<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
    $installer->getTable('core/cache_tag'),
    $installer->getFkName('core/cache_tag', 'cache_id', 'core/cache', 'id'),
);

$installer->endSetup();
