<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

// Delete non-existent and unofficial iso-3166-1 codes
$installer->run("
    DELETE FROM {$installer->getTable('directory/country')}
    WHERE country_id IN('FX','CS')
");

$installer->endSetup();
