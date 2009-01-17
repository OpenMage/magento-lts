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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule collection
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/rule');
    }

    public function setClassTypeFilter($classType, $classTypeId)
    {
        $sqlJoin = $classType == 'CUSTOMER'
            ? 'main_table.tax_customer_class_id = class_table.class_id'
            : 'main_table.tax_product_class_id = class_table.class_id';

        $sqlWhere = $classType == 'CUSTOMER'
            ? 'main_table.tax_customer_class_id = ?'
            : 'main_table.tax_product_class_id = ?';

        $this->_select->joinLeft(
            array('class_table' => $this->getTable('tax/tax_class')),
            $sqlJoin
        );
        $this->_select->where($sqlWhere, $classTypeId);

        return $this;
    }

    public function joinClassTable()
    {
        $this->_select->joinLeft(
            array('class_customer_table' => $this->getTable('tax/tax_class')),
            'main_table.tax_customer_class_id = class_customer_table.class_id',
            array('class_customer_name' => 'class_name')
        );
        $this->_select->joinLeft(
            array('class_product_table' => $this->getTable('tax/tax_class')),
            'main_table.tax_product_class_id = class_product_table.class_id',
            array('class_product_name' => 'class_name')
        );

        return $this;
    }

    public function joinRateTypeTable()
    {
        $this->_select->joinLeft(
            array('rate_type_table' => $this->getTable('tax/tax_rate_type')),
            'main_table.tax_rate_type_id = rate_type_table.type_id',
            array('rate_type_name' => 'type_name')
        );

        return $this;
    }

    protected function _getConditionFieldName($fieldName)
    {
        switch ($fieldName) {
            case 'class_customer_name':
                $fieldName = 'class_customer_table.class_name';
                break;
            case 'class_product_name':
                $fieldName = 'class_product_table.class_name';
                break;
            case 'rate_type_name':
                $fieldName = 'rate_type_table.type_name';
                break;
            default:
                break;
        }
        return $fieldName;
    }
}