<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Template extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('newsletter/template/list.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/newsletter_template_grid', 'newsletter.template.grid'));
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('newsletter')->__('Newsletter Templates');
    }
}
