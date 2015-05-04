<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$this->startSetup();
$this->run("
ALTER TABLE `{$installer->getTable('sales_quote_item')}`
    MODIFY COLUMN `weight` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `discount_percent` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `discount_amount` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `tax_percent` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `tax_amount` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `row_total_with_discount` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `base_discount_amount` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `base_tax_amount` DECIMAL(12,4) DEFAULT '0.0000',
    MODIFY COLUMN `row_weight` DECIMAL(12,4) DEFAULT '0.0000';
");
$this->endSetup();
$this->installEntities();
