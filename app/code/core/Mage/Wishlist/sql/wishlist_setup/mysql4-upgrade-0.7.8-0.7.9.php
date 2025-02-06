<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('wishlist/item'), 'qty', 'DECIMAL( 12, 4 ) NOT NULL');

$installer->run("
CREATE TABLE `{$this->getTable('wishlist/item_option')}` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist_item_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Additional options for wishlist item';
");

$installer->getConnection()->addConstraint(
    'FK_WISHLIST_ITEM_OPTION_ITEM_ID',
    $this->getTable('wishlist/item_option'),
    'wishlist_item_id',
    $this->getTable('wishlist/item'),
    'wishlist_item_id',
);

$installer->endSetup();
