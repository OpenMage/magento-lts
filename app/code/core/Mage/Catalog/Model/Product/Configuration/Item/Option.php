<?php

/**
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration item option model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Configuration_Item_Option extends Varien_Object implements Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_getData('value');
    }
}
