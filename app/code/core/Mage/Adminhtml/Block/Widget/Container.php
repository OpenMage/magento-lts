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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Container extends Mage_Adminhtml_Block_Template
{
    /**
     * So-called "container controller" to specify group of blocks participating in some action
     *
     * @var string
     */
    protected $_controller = 'empty';

    /**
     * Array of buttons
     *
     *
     * @var array
     */
    protected $_buttons = [
        -1  => [],
        0   => [],
        1   => [],
    ];

    /**
     * Header text
     *
     * @var string
     */
    protected $_headerText = 'Container Widget Header';

    /**
     * Add a button
     *
     * @param string $id
     * @param array $data
     * @param int $level
     * @param int $sortOrder
     * @param string|null $area area, that button should be displayed in ('header', 'footer', null)
     * @return $this
     */
    protected function _addButton($id, $data, $level = 0, $sortOrder = 0, $area = 'header')
    {
        if (!isset($this->_buttons[$level])) {
            $this->_buttons[$level] = [];
        }
        $this->_buttons[$level][$id] = $data;
        $this->_buttons[$level][$id]['area'] = $area;
        if ($sortOrder) {
            $this->_buttons[$level][$id]['sort_order'] = $sortOrder;
        } else {
            $this->_buttons[$level][$id]['sort_order'] = count($this->_buttons[$level]) * 10;
        }
        return $this;
    }

    /**
     * Public wrapper for protected _addButton method
     *
     * @param string $id
     * @param array $data
     * @param int $level
     * @param int $sortOrder
     * @param string|null $area area, that button should be displayed in ('header', 'footer', null)
     * @return $this
     */
    public function addButton($id, $data, $level = 0, $sortOrder = 0, $area = 'header')
    {
        return $this->_addButton($id, $data, $level, $sortOrder, $area);
    }

    /**
     * Remove existing button
     *
     * @param string $id
     * @return $this
     */
    protected function _removeButton($id)
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                unset($this->_buttons[$level][$id]);
            }
        }
        return $this;
    }

    /**
     * Public wrapper for the _removeButton() method
     *
     * @param string $id
     * @return $this
     */
    public function removeButton($id)
    {
        return $this->_removeButton($id);
    }

    /**
     * Update specified button property
     *
     * @param string $id
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    protected function _updateButton($id, $key, $data)
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                if (!empty($key)) {
                    if ($child = $this->getChild($id . '_button')) {
                        $child->setData($key, $data);
                    }
                    if ($key == 'level') {
                        $this->_buttons[$data][$id] = $this->_buttons[$level][$id];
                        unset($this->_buttons[$level][$id]);
                    } else {
                        $this->_buttons[$level][$id][$key] = $data;
                    }
                } else {
                    $this->_buttons[$level][$id] = $data;
                }
                break;
            }
        }
        return $this;
    }

    /**
     * Public wrapper for protected _updateButton method
     *
     * @param string $id
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    public function updateButton($id, $key, $data)
    {
        return $this->_updateButton($id, $key, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $childId = $this->_prepareButtonBlockId($id);
                $this->_addButtonChildBlock($childId);
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare block id for button's id
     *
     * @param string $id
     * @return string
     */
    protected function _prepareButtonBlockId($id)
    {
        return $id . '_button';
    }

    /**
     * Adding child block with specified child's id.
     *
     * @param string $childId
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function _addButtonChildBlock($childId)
    {
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $this->setChild($childId, $block);
        return $block;
    }

    /**
     * Produce buttons HTML
     *
     * @param string $area
     * @return string
     */
    public function getButtonsHtml($area = null)
    {
        $out = '';
        foreach ($this->_buttons as $cachedButtons) {
            $buttons = [];
            foreach ($cachedButtons as $id => $data) {
                $buttons[$data['sort_order']]['id'] = $id;
                $buttons[$data['sort_order']]['data'] = $data;
            }
            ksort($buttons);
            foreach ($buttons as $button) {
                $id = $button['id'];
                $data = $button['data'];
                if ($area && isset($data['area']) && ($area != $data['area'])) {
                    continue;
                }
                $childId = $this->_prepareButtonBlockId($id);
                $child = $this->getChild($childId);

                if (!$child) {
                    $child = $this->_addButtonChildBlock($childId);
                }
                if (isset($data['name'])) {
                    $data['element_name'] = $data['name'];
                }
                $child->setData($data);

                $out .= $this->getChildHtml($childId);
            }
        }
        return $out;
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->_headerText;
    }

    /**
     * Get header CSS class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-' . strtr($this->_controller, '_', '-');
    }

    /**
     * Get header HTML
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    /**
     * Check if there's anything to display in footer
     *
     * @return bool
     */
    public function hasFooterButtons()
    {
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                if (isset($data['area']) && ($data['area'] == 'footer')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_widget_container_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
