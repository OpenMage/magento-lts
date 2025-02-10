<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$installer->updateAttribute('catalog_category', 'url_path', 'is_unique', 0);
$installer->updateAttribute('catalog_product', 'url_path', 'is_unique', 0);
