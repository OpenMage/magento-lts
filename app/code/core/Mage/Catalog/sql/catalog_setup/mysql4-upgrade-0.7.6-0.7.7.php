<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer  = $this;
$installer->startSetup();
$installer->installEntities();
$installer->endSetup();
