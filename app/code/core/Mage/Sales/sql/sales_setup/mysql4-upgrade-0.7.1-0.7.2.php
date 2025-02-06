<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->installEntities();
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_varchar')};
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_int')};
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_decimal')};
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_datetime')};
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity_text')};
    DROP TABLE IF EXISTS {$this->getTable('sales_invoice_entity')};
");
$installer->endSetup();
