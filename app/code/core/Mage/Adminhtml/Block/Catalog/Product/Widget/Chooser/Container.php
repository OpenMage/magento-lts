<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
