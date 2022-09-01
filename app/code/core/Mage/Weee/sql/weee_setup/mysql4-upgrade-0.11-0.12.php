<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Weee_Model_Resource_Setup $installer */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('weee_tax'), 'entity_type_id', "smallint (5) unsigned not null");
$installer->run("
UPDATE
    `{$installer->getTable('weee_tax')}`
SET
    `entity_type_id` = (
        SELECT
            `entity_type_id`
        FROM
            `{$installer->getTable('eav_entity_type')}`
        WHERE
            `entity_type_code` = 'catalog_product'
    );
");

$installer->endSetup();
