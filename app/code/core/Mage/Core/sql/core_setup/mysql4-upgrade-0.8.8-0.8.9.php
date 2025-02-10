<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $this->getTable('core_session'),
    'session_data',
    'session_data',
    'MEDIUMBLOB NOT NULL',
);

$installer->endSetup();
