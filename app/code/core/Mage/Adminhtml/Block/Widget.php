<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base widget class
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method $this setHeaderCss(string $value)
 * @method $this setTitle(string $value)
 */
class Mage_Adminhtml_Block_Widget extends Mage_Adminhtml_Block_Template
{
    /**
     * @return string
     */
    public function getId()
    {
        if ($this->getData('id') === null) {
            $this->setData('id', Mage::helper('core')->uniqHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getId();
    }

    /**
     * Get current url
     *
     * @param array $params url parameters
     * @return string current url
     */
    public function getCurrentUrl($params = [])
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/*', $params);
    }

    protected function _addBreadcrumb($label, $title = null, $link = null)
    {
        /** @var Mage_Adminhtml_Block_Widget_Breadcrumbs $block */
        $block = $this->getLayout()->getBlock('breadcrumbs');
        $block->addLink($label, $title, $link);
    }

    /**
     * Create button and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $id
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class = '', $id = null)
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label'     => $label,
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $id,
            ])
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getGlobalIcon()
    {
        return '<img src="' . $this->getSkinUrl('images/fam_link.gif') . '" alt="' . $this->__('Global Attribute') . '" title="' . $this->__('This attribute shares the same value in all the stores') . '" class="attribute-global"/>';
    }
}
