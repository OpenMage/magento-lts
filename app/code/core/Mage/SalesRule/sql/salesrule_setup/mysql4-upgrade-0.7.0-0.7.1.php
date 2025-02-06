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
ALTER TABLE {$this->getTable('salesrule')}
    CHANGE `uses_per_coupon` `uses_per_coupon` int (11) DEFAULT '0' NOT NULL ,
    CHANGE `uses_per_customer` `uses_per_customer` int (11) DEFAULT '0' NOT NULL;
");

$installer->endSetup();
