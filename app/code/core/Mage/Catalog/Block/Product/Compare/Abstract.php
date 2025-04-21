<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Catalog Compare Products Abstract Block
 *
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Block_Product_Compare_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve Product Compare Helper
     *
     * @return Mage_Catalog_Helper_Product_Compare
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog/product_compare');
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param Mage_Catalog_Model_Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
