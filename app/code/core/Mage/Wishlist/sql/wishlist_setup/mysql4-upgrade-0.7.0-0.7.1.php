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

$installer->run("

alter table {$this->getTable('wishlist_item')} add constraint `FK_WISHLIST_PRODUCT` foreign key (`product_id`) references {$this->getTable('catalog_product_entity')} (`entity_id`) on delete cascade  on update cascade
;

");

$installer->endSetup();
