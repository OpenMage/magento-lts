<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
