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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('core/variable_value'), 'plain_value', 'TEXT NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('core/variable_value'), 'html_value', 'TEXT NOT NULL');

$select = $installer->getConnection()->select()
    ->from(array('main_table' => $installer->getTable('core/variable')), array())
    ->join(array('value_table' => $installer->getTable('core/variable_value')),
        'value_table.variable_id = main_table.variable_id', array())
    ->columns(array('main_table.variable_id', 'main_table.is_html', 'value_table.value'));

$data = array();
foreach ($installer->getConnection()->fetchAll($select) as $row) {
    if ($row['is_html']) {
        $value = array('html_value' => $row['value']);
    } else {
        $value = array('plain_value' => $row['value']);
    }
    $data[$row['variable_id']] = $value;
}

foreach ($data as $variableId => $value) {
    $installer->getConnection()->update($installer->getTable('core/variable_value'), $value,
        array('variable_id = ?' => $variableId));
}

$installer->getConnection()->dropColumn($installer->getTable('core/variable'), 'is_html');
$installer->getConnection()->dropColumn($installer->getTable('core/variable_value'), 'value');

$installer->endSetup();
