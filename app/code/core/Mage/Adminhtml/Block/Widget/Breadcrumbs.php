<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml page breadcrumbs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Breadcrumbs extends Mage_Adminhtml_Block_Template
{
    /**
     * breadcrumbs links
     *
     * @var array
     */
    protected $_links = [];

    /**
     * Mage_Adminhtml_Block_Widget_Breadcrumbs constructor.
     */
    public function __construct()
    {
        $this->setTemplate('widget/breadcrumbs.phtml');
        $this->addLink(Mage::helper('adminhtml')->__('Home'), Mage::helper('adminhtml')->__('Home'), $this->getUrl('*'));
    }

    /**
     * @param string $label
     * @param string|null $title
     * @param string|null $url
     * @return $this
     */
    public function addLink($label, $title = null, $url = null)
    {
        if (empty($title)) {
            $title = $label;
        }
        $this->_links[] = [
            'label' => $label,
            'title' => $title,
            'url'   => $url,
        ];
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        // TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
        // $this->assign('links', $this->_links);
        return parent::_beforeToHtml();
    }
}
