<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
