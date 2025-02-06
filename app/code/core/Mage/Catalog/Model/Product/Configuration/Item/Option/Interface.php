<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Interface of product configurational item option
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * Retrieve value associated with this option
     *
     * @return mixed
     */
    public function getValue();
}
