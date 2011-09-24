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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

/** @var $eavResource Mage_Catalog_Model_Resource_Eav_Attribute */
$eavResource = Mage::getResourceModel('catalog/eav_attribute');

$multiSelectAttributeCodes = $eavResource->getAttributeCodesByFrontendType('multiselect');

foreach($multiSelectAttributeCodes as $attributeCode) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    if ($attribute = $installer->getAttribute('catalog_product', $attributeCode)) {
        $attributeTable = $installer->getAttributeTable('catalog_product', $attributeCode);
        $select = $installer->getConnection()->select()
            ->from(array('e' => $attributeTable))
            ->where("e.attribute_id=?", $attribute['attribute_id']);

        if ($result = $installer->getConnection()->fetchAll($select)) {
            foreach ($result as $row) {
                $value = explode(',', $row['value']);
                if (is_array($value) && count($value) > 1) {
                    foreach ($value as $optionKey => $optionValue) {
                        if ($optionValue === '') {
                            unset($value[$optionKey]);
                        }
                    }
                    $value = implode(',', $value);
                    $installer->getConnection()
                        ->update($attributeTable, array('value'=>$value), "value_id=" . $row['value_id']);
                } else {
                    unset($value);
                }
            }
        }
    }
}
