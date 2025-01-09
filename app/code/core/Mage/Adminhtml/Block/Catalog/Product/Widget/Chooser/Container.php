<?php

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Chooser Container for "Product Link" Cms Widget Plugin
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser_Container extends Mage_Adminhtml_Block_Template
{
    /**
     * Block construction
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        $this->setTemplate('catalog/product/widget/chooser/container.phtml');
    }
}
