<?php

/**
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle option dropdown type renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Select extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('bundle/catalog/product/view/type/bundle/option/select.phtml');
    }
}
