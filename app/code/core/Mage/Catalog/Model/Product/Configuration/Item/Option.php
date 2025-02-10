<?php
/**
 * Configuration item option model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
