<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$connection->addKey($installer->getTable('catalogrule/rule_product'), 'IDX_FROM_TIME', 'from_time');
$connection->addKey($installer->getTable('catalogrule/rule_product'), 'IDX_TO_TIME', 'to_time');
