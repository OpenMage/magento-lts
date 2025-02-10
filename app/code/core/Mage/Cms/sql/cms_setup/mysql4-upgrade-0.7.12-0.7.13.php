<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('cms/page'),
    'content_heading',
    "VARCHAR(255) NOT NULL DEFAULT '' AFTER `identifier`",
);

$installer->endSetup();
