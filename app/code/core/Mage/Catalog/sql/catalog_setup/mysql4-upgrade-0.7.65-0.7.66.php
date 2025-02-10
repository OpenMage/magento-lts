<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

Mage::getModel('catalog/product_flat_flag')
    ->loadSelf()
    ->setIsBuilt(false)
    ->save();

$installer->startSetup();
$installer->run("
    UPDATE `{$installer->getTable('core/config_data')}` SET `value`=0
        WHERE `path` LIKE '" . Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT . "';
");

$installer->endSetup();
