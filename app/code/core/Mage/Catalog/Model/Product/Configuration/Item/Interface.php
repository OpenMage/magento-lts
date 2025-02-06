<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Product configurational item interface
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Product_Configuration_Item_Interface
{
    /**
     * Retrieve associated product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct();

    /**
     * Get item option by code
     *
     * @param   string $code
     * @return  Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
     */
    public function getOptionByCode($code);

    /**
     * Returns special download params (if needed) for custom option with type = 'file''
     * Return null, if not special params needed'
     * Or return Varien_Object with any of the following indexes:
     *  - 'url' - url of controller to give the file
     *  - 'urlParams' - additional parameters for url (custom option id, or item id, for example)
     *
     * @return null|Varien_Object
     */
    public function getFileDownloadParams();
}
