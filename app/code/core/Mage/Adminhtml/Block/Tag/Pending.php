<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml pending tags grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Pending extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tag/index.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('tagsGrid', $this->getLayout()->createBlock('adminhtml/tag_grid_pending'));
        return parent::_prepareLayout();
    }

    public function getCreateButtonHtml()
    {
        return '';
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('tagsGrid');
    }

    public function getHeaderHtml()
    {
        return Mage::helper('tag')->__('Pending Tags');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-tag';
    }
}
