<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Chooser Container for "Product Link" Cms Widget Plugin
 *
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
