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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * #5043 fix: Customer email - can't be changed in admin interface
 * @see mysql4-upgrade-0.7.2-0.7.3.php
 */

$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$attributeId = $installer->getAttributeId('customer', 'email');

$installer->run("
    DELETE FROM {$this->getTable('customer_entity_varchar')}
    WHERE attribute_id={$attributeId};
");

$installer->endSetup();
