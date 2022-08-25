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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Sales_Model_Entity_Setup $installer */

$conn = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $conn */

$conn->addColumn($installer->getTable('sales_flat_quote'), 'customer_dob', 'datetime after customer_suffix');
$installer->addAttribute('quote', 'customer_dob', ['type'=>'static', 'backend'=>'eav/entity_attribute_backend_datetime']);

$installer->addAttribute('order', 'customer_dob', ['type'=>'datetime', 'backend'=>'eav/entity_attribute_backend_datetime']);
