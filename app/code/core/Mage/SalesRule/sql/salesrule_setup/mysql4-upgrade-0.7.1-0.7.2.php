<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('salesrule')}`
    ADD COLUMN `times_used` int (11) unsigned DEFAULT '0' NOT NULL
        AFTER `simple_free_shipping`;
");

$installer->endSetup();
