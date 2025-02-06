<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tags page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tag/index.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', $this->getUrl('*/tag/new'));
        $this->setChild('tag_frame', $this->getLayout()->createBlock('adminhtml/tag_tab_all', 'tag.frame'));
        return parent::_beforeToHtml();
    }
}
