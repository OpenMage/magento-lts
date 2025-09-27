<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Configuration item option model
 *
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
