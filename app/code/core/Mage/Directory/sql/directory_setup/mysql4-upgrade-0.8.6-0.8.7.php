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

// Delete non-existent and unofficial iso-3166-1 codes
$installer->run("
    DELETE FROM {$installer->getTable('directory/country')}
    WHERE country_id IN('FX','CS')
");

$installer->endSetup();
