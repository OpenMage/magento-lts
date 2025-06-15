<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('eav/attribute'), 'is_visible_in_advanced_search', "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'");

$installer->endSetup();
