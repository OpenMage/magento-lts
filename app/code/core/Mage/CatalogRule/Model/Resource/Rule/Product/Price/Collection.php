<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Resource_Rule_Product_Price_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('catalogrule/rule_product_price');
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.product_id');
        $idsSelect->distinct(true);
        return $this->getConnection()->fetchCol($idsSelect);
    }
}
