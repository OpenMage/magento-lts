<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
