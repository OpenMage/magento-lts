<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("UPDATE `{$installer->getTable('eav_attribute')}` SET `position` = 1 WHERE `position` = 0 AND `attribute_code` != 'price';");
