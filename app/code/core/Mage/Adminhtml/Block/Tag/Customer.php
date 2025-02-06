<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers tagged with tag
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Mage_Adminhtml_Block_Tag_Customer constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        switch ($this->getRequest()->getParam('ret')) {
            case 'all':
                $url = $this->getUrl('*/*/');
                break;

            case 'pending':
                $url = $this->getUrl('*/*/pending');
                break;

            default:
                $url = $this->getUrl('*/*/');
        }

        $this->_block = 'tag_customer';
        $this->_controller = 'tag_customer';
        $this->_removeButton('add');
        $this->setBackUrl($url);
        $this->_addBackButton();

        $tagInfo = Mage::getModel('tag/tag')
            ->load(Mage::registry('tagId'));

        $this->_headerText = Mage::helper('tag')->__("Customers Tagged '%s'", $this->escapeHtml($tagInfo->getName()));
    }
}
