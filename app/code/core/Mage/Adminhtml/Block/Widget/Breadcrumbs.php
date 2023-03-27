<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml page breadcrumbs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'url'   => $url
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
