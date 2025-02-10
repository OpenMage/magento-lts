<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
$this->startSetup()->run("
alter table {$this->getTable('customer_entity')} drop foreign key  `FK_CUSTOMER_ENTITY_PARENT_ENTITY` ;
")->endSetup();
