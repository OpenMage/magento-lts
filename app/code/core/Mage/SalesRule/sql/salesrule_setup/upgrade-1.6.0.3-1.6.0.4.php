<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// Add index to salesrule_coupon for fast lookup, only first 10 bytes
$keyList = $installer->getConnection()->getIndexList($installer->getTable('salesrule/coupon'));
if (!isset($keyList['IDX_SALES_COUPON_CODE'])) {
    $installer->run('
        ALTER TABLE '.$installer->getTable('salesrule/coupon').'
        ADD INDEX `IDX_SALES_COUPON_CODE` (`code` (10));
    ');
}

$installer->endSetup();
