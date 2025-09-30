<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml products tagged by tag
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $route = match ($this->getRequest()->getParam('ret')) {
            'pending' => '*/*/pending',
            default => '*/*/',
        };

        $this->_block = 'tag_product';
        $this->_controller = 'tag_product';
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl($route));
        $this->_addBackButton();

        $tagInfo = Mage::getModel('tag/tag')
            ->load(Mage::registry('tagId'));

        $this->_headerText = Mage::helper('tag')->__("Products Tagged with '%s'", $this->escapeHtml($tagInfo->getName()));
    }
}
