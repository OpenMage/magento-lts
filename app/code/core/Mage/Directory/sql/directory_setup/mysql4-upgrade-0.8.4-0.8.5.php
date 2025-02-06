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
    UPDATE {$installer->getTable('directory/country_region')}
    SET code = 'GR'
    WHERE region_id = 113
");

$installer->endSetup();
