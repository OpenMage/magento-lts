<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Add foreign key to customers table into wishlists
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */

$installer = $this;
/* @var Mage_Core_Model_Resource_Setup $installer */

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
