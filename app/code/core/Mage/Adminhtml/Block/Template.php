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
 * Adminhtml abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Template extends Mage_Core_Block_Template
{
    public const BUTTON_ADD                         = 'add_button';
    public const BUTTON_BACK                        = 'back_button';
    public const BUTTON_CANCEL                      = 'cancel_button';
    public const BUTTON_CLOSE                       = 'close_button';
    public const BUTTON_DELETE                      = 'delete_button';
    public const BUTTON_DUPLICATE                   = 'duplicate_button';
    public const BUTTON_RESET                       = 'reset_button';
    public const BUTTON_REFRESH                     = 'refresh_button';
    public const BUTTON_SAVE                        = 'save_button';
    public const BUTTON_SAVE_AND_CONTINUE           = 'save_and_edit_button';
    public const BUTTON_SUBMIT                      = 'submit_button';
    public const BUTTON_UPDATE                      = 'update_button';
    public const BUTTON_UPLOAD                      = 'upload_button';

    public const BUTTON__CLASS_ADD                  = 'add';
    public const BUTTON__CLASS_BACK                 = 'back';
    public const BUTTON__CLASS_CANCEL               = 'cancel';
    public const BUTTON__CLASS_DELETE               = 'delete';
    public const BUTTON__CLASS_RESET                = 'reset';
    public const BUTTON__CLASS_SAVE                 = 'save';
    public const BUTTON__CLASS_TASK                 = 'task';
    public const BUTTON__CLASS_UPDATE               = 'update-button';

    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * @param string $moduleName Full module name
     * @return bool
     * @deprecated
     * @see Mage_Core_Block_Template::isModuleOutputEnabled()
     */
    public function isOutputEnabled($moduleName = null)
    {
        return $this->isModuleOutputEnabled($moduleName);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', ['block' => $this]);
        return parent::_toHtml();
    }

    /**
     * Deleting script tags from string
     *
     * @param string $html
     * @return string
     */
    public function maliciousCodeFilter($html)
    {
        return Mage::getSingleton('core/input_filter_maliciousCode')->filter($html);
    }

    private function createWidgetButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        /** @var Mage_Adminhtml_Block_Widget_Button $block */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button', $name, $attributes);
        return $block;
    }

    public function getButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes);
    }

    private function createWidgetButtonBlockByType(string $type, string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        $button = $this->createWidgetButtonBlock($name, $attributes);

        switch ($type) {
            case static::BUTTON_ADD:
                $button->setLabel(Mage::helper('adminhtml')->__('Add'));
                $button->setClass(static::BUTTON__CLASS_ADD);
                break;
            case static::BUTTON_BACK:
                $button->setLabel(Mage::helper('adminhtml')->__('Back'));
                $button->setOnClick("window.location.href='" . $this->getUrl('*/*') . "'");
                $button->setClass(static::BUTTON__CLASS_BACK);
                break;
            case static::BUTTON_CANCEL:
                $button->setLabel(Mage::helper('adminhtml')->__('Cancel'));
                $button->setOnClick('window.close()');
                $button->setClass(static::BUTTON__CLASS_CANCEL);
                break;
            case static::BUTTON_CLOSE:
                $button->setLabel(Mage::helper('adminhtml')->__('Close Window'));
                $button->setOnClick('window.close()');
                break;
            case static::BUTTON_DELETE:
                $button->setLabel(Mage::helper('adminhtml')->__('Delete'));
                $button->setClass(static::BUTTON__CLASS_DELETE);
                break;
            case static::BUTTON_REFRESH:
                $button->setLabel(Mage::helper('adminhtml')->__('Refresh'));
                $button->setClass(static::BUTTON__CLASS_TASK);
                break;
            case static::BUTTON_RESET:
                $button->setLabel(Mage::helper('adminhtml')->__('Reset'));
                $button->setOnClick('window.location.href = window.location.href');
                $button->setClass(static::BUTTON__CLASS_RESET);
                break;
            case static::BUTTON_SAVE:
                $button->setLabel(Mage::helper('adminhtml')->__('Save'));
                $button->setClass(static::BUTTON__CLASS_SAVE);
                break;
            case static::BUTTON_SAVE_AND_CONTINUE:
                $button->setLabel(Mage::helper('adminhtml')->__('Save and Continue Edit'));
                $button->setClass(static::BUTTON__CLASS_SAVE);
                break;
            case static::BUTTON_UPDATE:
                $button->setClass(static::BUTTON__CLASS_UPDATE);
                break;
            case static::BUTTON_UPLOAD:
                $button->setLabel(Mage::helper('adminhtml')->__('Upload Files'));
                break;
        }

        return $button;
    }

    public function getButtonBlockByType(string $type, string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlockByType($type, $name, $attributes);
    }

    /**
     * Retrieve Add Button HTML
     *
     *  @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_ADD);
    }

    /**
     * Retrieve Back Button HTML
     *
     *  @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_BACK);
    }

    /**
     * Retrieve Cancel Button HTML
     *
     *  @return string
     */
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_RESET);
    }

    /**
     * Retrieve Close Button HTML
     *
     *  @return string
     */
    public function getCloseButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_CLOSE);
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_DELETE);
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_DUPLICATE);
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_RESET);
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_SAVE);
    }

    public function getSaveAndEditButtonHtml(): string
    {
        return $this->getChildHtml(static::BUTTON_SAVE_AND_CONTINUE);
    }

    /**
     * Retrieve Update Button HTML
     *
     * @return string
     */
    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_UPDATE);
    }

    /**
     * Retrieve Upload Button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_UPLOAD);
    }
}
