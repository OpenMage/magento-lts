<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();
$installer->updateAttribute($installer->getEntityTypeId('catalog_category'), 'is_active', 'is_required', true);
$installer->endSetup();
