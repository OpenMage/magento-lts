<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
