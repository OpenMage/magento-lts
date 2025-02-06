<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
