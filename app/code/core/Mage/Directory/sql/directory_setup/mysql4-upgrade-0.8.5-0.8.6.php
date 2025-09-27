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
    UPDATE {$installer->getTable('directory/country')}
    SET iso3_code = 'ROU'
    WHERE country_id = 'RO'
");

$installer->endSetup();
