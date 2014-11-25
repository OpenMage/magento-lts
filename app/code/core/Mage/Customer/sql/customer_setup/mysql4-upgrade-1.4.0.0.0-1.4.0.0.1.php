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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->addAttribute('customer', 'gender', array(
    'label'        => 'Gender',
    'visible'      => true,
    'required'     => false,
    'type'         => 'int',
    'input'        => 'select',
    'source'        => 'eav/entity_attribute_source_table',
));


$tableOptions        = $installer->getTable('eav_attribute_option');
$tableOptionValues   = $installer->getTable('eav_attribute_option_value');

// add options for level of politeness
$attributeId = (int)$installer->getAttribute('customer', 'gender', 'attribute_id');
foreach (array('Male', 'Female') as $sortOrder => $label) {

    // add option
    $data = array(
        'attribute_id' => $attributeId,
        'sort_order'   => $sortOrder,
    );
    $installer->getConnection()->insert($tableOptions, $data);

    // add option label
    $optionId = (int)$installer->getConnection()->lastInsertId($tableOptions, 'option_id');
    $data = array(
        'option_id' => $optionId,
        'store_id'  => 0,
        'value'     => $label,
    );
    $installer->getConnection()->insert($tableOptionValues, $data);

}

$installer->endSetup();
