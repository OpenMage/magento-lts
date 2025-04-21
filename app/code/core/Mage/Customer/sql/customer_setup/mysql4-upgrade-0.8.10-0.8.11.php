<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropKey($installer->getTable('customer_address_entity_text'), 'IDX_VALUE');
$installer->getConnection()->dropKey($installer->getTable('customer_entity_text'), 'IDX_VALUE');

$installer->endSetup();
