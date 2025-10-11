<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
UPDATE `{$installer->getTable('sales_order')}` AS `s`
    LEFT JOIN `{$installer->getTable('customer_entity')}` AS `c`
        ON `s`.`customer_id`=`c`.`entity_id`
    SET `s`.`customer_id`=NULL
WHERE `c`.`entity_id` IS NULL;
");
$installer->getConnection()->modifyColumn($installer->getTable('sales_order'), 'customer_id', 'INT UNSIGNED NULL DEFAULT NULL');
$installer->getConnection()->addConstraint(
    'FK_SALES_ORDER_CUSTOMER',
    $installer->getTable('sales_order'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    'set null',
    'cascade',
);

$installer->endSetup();
