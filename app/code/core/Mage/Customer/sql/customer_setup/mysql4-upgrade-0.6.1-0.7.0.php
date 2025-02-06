<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

$this->startSetup()->run("
alter table {$this->getTable('customer_entity')} drop foreign key  `FK_CUSTOMER_ENTITY_PARENT_ENTITY` ;
")->endSetup();
