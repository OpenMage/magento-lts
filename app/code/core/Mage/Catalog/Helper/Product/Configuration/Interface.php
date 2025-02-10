<?php
/**
 * Interface for product configuration helpers
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Retrieves product options list
     *
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item);
}
