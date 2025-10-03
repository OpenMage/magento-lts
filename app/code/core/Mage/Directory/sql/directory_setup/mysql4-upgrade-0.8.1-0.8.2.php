<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
REPLACE INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`)
SELECT 'en_US', `region_id`, `default_name` FROM `{$installer->getTable('directory_country_region')}` WHERE `country_id` = 'FR';
");

$installer->endSetup();
