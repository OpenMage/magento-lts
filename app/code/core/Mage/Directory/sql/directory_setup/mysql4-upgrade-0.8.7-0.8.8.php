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

//Entries for 'Newfoundland' should be corrected because of the province changed its name and code

$installer->run("
    UPDATE {$installer->getTable('directory/country_region')}
    SET code = 'NL', default_name = 'Newfoundland and Labrador'
    WHERE region_id = 69
");

$installer->run("
    UPDATE {$installer->getTable('directory/country_region_name')}
    SET `name` = 'Newfoundland and Labrador'
    WHERE `region_id` = 69 AND `name` = 'Newfoundland'
");

$installer->endSetup();
