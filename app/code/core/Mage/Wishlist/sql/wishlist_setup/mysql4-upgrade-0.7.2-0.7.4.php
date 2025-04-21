<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$tableWishlist  = $this->getTable('wishlist');
$tableCustomers = $this->getTable('customer/entity');

$installer->run("DELETE FROM {$tableWishlist} WHERE customer_id NOT IN (SELECT entity_id FROM {$tableCustomers})");

$installer->run("
ALTER TABLE {$tableWishlist}
    ADD CONSTRAINT `FK_CUSTOMER` FOREIGN KEY (`customer_id`)
    REFERENCES {$tableCustomers} (`entity_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");

$installer->endSetup();
