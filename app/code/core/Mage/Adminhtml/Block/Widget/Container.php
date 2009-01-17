<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Container extends Mage_Adminhtml_Block_Template
{
    protected $_controller = 'empty';
    protected $_buttons = array(0 => array());
    protected $_headerText = 'Container Widget Header';

    /**
     * Enter description here...
     *
     * @param string $id
     * @param array $data
     * @param integer $level
     * @return Mage_Adminhtml_Block_Widget_Container
     */
    protected function _addButton($id, $data, $level = 0, $sortOrder = 100)
    {
        if (!isset($this->_buttons[$level])) {
            $this->_buttons[$level] = array();
        }
        $this->_buttons[$level][$id] = $data;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param string $id
     * @return Mage_Adminhtml_Block_Widget_Container
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
     * Enter description here...
     *
     * @param string $id
     * @param string|null $key
     * @param mixed $data
     * @return Mage_Adminhtml_Block_Widget_Container
     */
    protected function _updateButton($id, $key=null, $data)
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                if (!empty($key)) {
                    if ($child = $this->getChild($id . '_button')) {
                    	$child->setData($key, $data);
                    }
                    $this->_buttons[$level][$id][$key] = $data;
                } else {
                    $this->_buttons[$level][$id] = $data;
                }
                break;
            }
        }
        return $this;
    }

    protected function _prepareLayout()
    {
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $this->setChild($id . '_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data));
            }
        }
        return parent::_prepareLayout();
    }

    public function getButtonsHtml()
    {
        $out = '';
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $out .= $this->getChildHtml($id . '_button');
            }
        }
        return $out;
    }

    public function getHeaderText()
    {
        return $this->_headerText;
    }

    public function getHeaderCssClass()
    {
        return 'head-' . strtr($this->_controller, '_', '-');
    }

    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

}
