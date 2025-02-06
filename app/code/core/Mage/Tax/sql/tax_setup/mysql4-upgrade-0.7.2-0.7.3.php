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

if (!$installer->getConnection()->fetchOne("select * from {$this->getTable('tax_class')} where `class_name`='Shipping' and `class_type`='PRODUCT'")) {
    $installer->run("
        insert  into {$this->getTable('tax_class')} (`class_name`,`class_type`) values ('Shipping','PRODUCT');
    ");
}

$installer->endSetup();
