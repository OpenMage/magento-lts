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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend for serialized array data
 *
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Minsaleqty extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (empty($value)) {
            $this->setValue(array());
        } else {
            try {
                parent::_afterLoad();
            } catch(Exception $e) {
                $this->setValue(array(
                    Mage::helper('core')->uniqHash('_') => array(
                        'customer_group_id' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                        'min_sale_qty' => $value,
                    )
                ));
            }
        }
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);

            $foundGroups = array();
            $hasGroupAllOnly = false;
            $groupAllQty = null;
            foreach ($value as $k => $v) {
                if (in_array($v['customer_group_id'], $foundGroups)) {
                    unset($value[$k]);
                    continue;
                }
                $foundGroups[] = $v['customer_group_id'];
                if ($v['customer_group_id'] == Mage_Customer_Model_Group::CUST_GROUP_ALL) {
                    $hasGroupAllOnly = true;
                    $groupAllQty = $v['min_sale_qty'];
                } else {
                    $hasGroupAllOnly = false;
                }
            }
            if ($hasGroupAllOnly) {
                $this->setValue($groupAllQty);
            } else {
                parent::_beforeSave();
            }
        }
    }

    /**
     * Load model by raw value
     */
    public function loadByValue($value)
    {
        return $this->setValue($value)->afterLoad();
    }
}
