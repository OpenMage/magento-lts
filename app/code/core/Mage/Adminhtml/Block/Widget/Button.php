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
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget
{
    public const DATA_AFTER_HTML    = 'after_html';
    public const DATA_BEFORE_HTML   = 'before_html';
    public const DATA_CLASS         = 'class';
    public const DATA_DISABLED      = 'disabled';
    public const DATA_ELEMENT_NAME  = 'element_name';
    public const DATA_LABEL         = 'label';
    public const DATA_LEVEL         = 'level';
    public const DATA_NAME          = 'name';
    public const DATA_ON_CLICK      = 'on_click';
    public const DATA_STYLE         = 'style';
    public const DATA_TITLE         = 'title';
    public const DATA_TYPE          = 'type';
    public const DATA_VALUE         = 'value';

    public const TYPE_BUTTON = 'button';
    public const TYPE_SUBMIT = 'submit';

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getBeforeHtml() . '<button '
            . ($this->getId() ? ' id="' . $this->getId() . '"' : '')
            . ($this->getElementName() ? ' name="' . $this->getElementName() . '"' : '')
            . ' title="'
            . Mage::helper('core')->quoteEscape($this->getTitle() ? $this->getTitle() : $this->getLabel())
            . '"'
            . ' type="' . $this->getType() . '"'
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' onclick="' . $this->getOnClick() . '"'
            . ' style="' . $this->getStyle() . '"'
            . ($this->getValue() ? ' value="' . $this->getValue() . '"' : '')
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
            . '><span><span><span>' . $this->getLabel() . '</span></span></span></button>' . $this->getAfterHtml();
    }

    public function getAfterHtml(): ?string
    {
        return $this->getDataByKey(self::DATA_AFTER_HTML);
    }

    /**
     * @return $this
     */
    public function setAfterHtml(string $html)
    {
        return $this->setData(self::DATA_AFTER_HTML, $html);
    }

    public function getBeforeHtml(): ?string
    {
        return $this->getDataByKey(self::DATA_BEFORE_HTML);
    }

    /**
     * @return $this
     */
    public function setBeforeHtml(string $html)
    {
        return $this->setData(self::DATA_BEFORE_HTML, $html);
    }

    public function getClass(): ?string
    {
        return $this->getDataByKey(self::DATA_CLASS);
    }

    /**
     * @param Mage_Adminhtml_Block_Template::BUTTON__CLASS_* $class
     * @return $this
     */
    public function setClass(string $class)
    {
        return $this->setData(self::DATA_CLASS, $class);
    }

    /**
     * Add custmom html class
     *
     * @return $this
     */
    public function addClass(string $class)
    {
        $curentClass = $this->getDataByKey(self::DATA_CLASS);
        return $this->setData(self::DATA_CLASS, trim($curentClass) . ' ' . trim($class));
    }

    /**
     * Reset html class to empty
     *
     * @return $this
     */
    public function resetClass()
    {
        return $this->setData(self::DATA_CLASS, '');
    }

    public function getDisabled(): ?bool
    {
        return $this->getDataByKey(self::DATA_DISABLED);
    }

    /**
     * @return $this
     */
    public function setDisabled(bool $value)
    {
        return $this->setData(self::DATA_DISABLED, $value);
    }

    public function getElementName(): ?string
    {
        return $this->getDataByKey(self::DATA_ELEMENT_NAME);
    }

    /**
     * @return $this
     */
    public function setElementName(string $name)
    {
        return $this->setData(self::DATA_ELEMENT_NAME, $name);
    }

    public function getLabel(): ?string
    {
        return $this->getDataByKey(self::DATA_LABEL);
    }

    /**
     * @return $this
     */
    public function setLabel(string $label)
    {
        return $this->setData(self::DATA_LABEL, $label);
    }

    public function getLevel(): int
    {
        return $this->getDataByKey(self::DATA_LEVEL);
    }

    /**
     * @return $this
     */
    public function setLevel(int $level)
    {
        return $this->setData(self::DATA_LEVEL, $level);
    }

    public function getName(): ?string
    {
        return $this->getDataByKey(self::DATA_NAME);
    }

    /**
     * @return $this
     */
    public function setName(string $name)
    {
        return $this->setData(self::DATA_NAME, $name);
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        if (!$this->getDataByKey(self::DATA_ON_CLICK)) {
            return $this->getDataByKey('onclick');
        }
        return $this->getDataByKey(self::DATA_ON_CLICK);
    }

    /**
     * @return $this
     */
    public function setOnClick(string $string)
    {
        return $this->setData(self::DATA_ON_CLICK, $string);
    }

    /**
     * @return $this
     */
    public function setOnClickSetLocationJsUrl(string $route = '*/*/', array $params = [])
    {
        $url = Mage::helper('core/js')->getSetLocationJs($this->getUrl($route, $params));
        return $this->setOnClick($url);
    }

    /**
     * @return $this
     */
    public function setOnClickSetLocationJsFullUrl(string $url)
    {
        $url = Mage::helper('core/js')->getSetLocationJs($url);
        return $this->setOnClick($url);
    }

    public function getStyle(): ?string
    {
        return $this->getDataByKey(self::DATA_STYLE);
    }

    /**
     * @return $this
     */
    public function setStyle(string $style)
    {
        return $this->setData(self::DATA_STYLE, $style);
    }

    public function getTitle(): ?string
    {
        return $this->getDataByKey(self::DATA_TITLE);
    }

    /**
     * @return $this
     */
    public function setTitle(string $title)
    {
        return $this->setData(self::DATA_TITLE, $title);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ($type = $this->getDataByKey(self::DATA_TYPE)) ? $type : self::TYPE_BUTTON;
    }

    /**
     * @return $this
     */
    public function setType(string $type)
    {
        return $this->setData(self::DATA_TYPE, $type);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getDataByKey(self::DATA_VALUE);
    }

    /**
     * @return $this
     */
    public function setValue(string $value)
    {
        return $this->setData(self::DATA_VALUE, $value);
    }
}
