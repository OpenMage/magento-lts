<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()
    ->modifyColumn(
        $this->getTable('catalogrule'),
        'customer_group_ids',
        'TEXT',
    );

$installer->endSetup();
