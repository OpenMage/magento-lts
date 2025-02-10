<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($this->getTable('wishlist'), 'updated_at', 'datetime NULL DEFAULT NULL');
$installer->endSetup();
