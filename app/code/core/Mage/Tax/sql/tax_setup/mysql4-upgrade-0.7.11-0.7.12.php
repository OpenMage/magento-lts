<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('tax_calculation_rate')} CHANGE `zip_from` `zip_from` INT(11) UNSIGNED NULL DEFAULT NULL;
    ALTER TABLE {$this->getTable('tax_calculation_rate')} CHANGE `zip_to` `zip_to` INT(11) UNSIGNED NULL DEFAULT NULL;
");

$installer->endSetup();
