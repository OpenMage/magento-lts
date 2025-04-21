<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

$this->startSetup();
$this->getConnection()->query("DROP TABLE IF EXISTS `{$this->getTable('catalog/category_flat')}`;");
$this->endSetup();
