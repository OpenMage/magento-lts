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
 * Adminhtml system template edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template_Edit extends Mage_Adminhtml_Block_Widget
{
    public const BLOCK_FORM         = 'form';
    public const BUTTON_LOAD        = 'load_button';
    public const BUTTON_PREVIEW     = 'preview_button';
    public const BUTTON_TO_HTML     = 'to_html_button';
    public const BUTTON_TO_PLAIN    = 'to_plain_button';
    public const BUTTON_TOGGLE      = 'toggle_button';

    protected $_template = 'system/email/template/edit.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_FORM,
            $this->getLayout()->createBlock('adminhtml/system_email_template_edit_form')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
        $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
        $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
        $this->setChild(self::BUTTON_TO_PLAIN, $this->getButtonToPlainBlock());
        $this->setChild(self::BUTTON_TO_HTML, $this->getButtonToHtmllock());
        $this->setChild(self::BUTTON_TOGGLE, $this->getButtonToggleBlock());
        $this->setChild(self::BUTTON_PREVIEW, $this->getButtonPreviewBlock());
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        $this->setChild(self::BUTTON_LOAD, $this->getButtonLoadBlock());
    }

    public function getButtonBackBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_BACK);
    }

    public function getButtonDeleteBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_DELETE)
            ->setLabel(Mage::helper('adminhtml')->__('Delete Template'))
            ->setOnClick('templateControl.deleteTemplate();');
    }

    public function getButtonLoadBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_SAVE)
            ->setLabel(Mage::helper('adminhtml')->__('Load Template'))
            ->setOnClick('templateControl.load();')
            ->setType('button');
    }

    public function getButtonPreviewBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Preview Template'))
            ->setOnClick('templateControl.preview();');
    }

    public function getButtonResetBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_RESET)
            ->resetClass();
    }

    public function getButtonSaveBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_SAVE)
            ->setLabel(Mage::helper('adminhtml')->__('Save Template'))
            ->setOnClick('templateControl.save();');
    }

    public function getButtonToHtmllock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock()
            ->setId('convert_button_back')
            ->setLabel(Mage::helper('adminhtml')->__('Return Html Version'))
            ->setOnClick('templateControl.unStripTags();')
            ->setStyle('display:none');
    }

    public function getButtonToPlainBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock()
            ->setId('convert_button')
            ->setLabel(Mage::helper('adminhtml')->__('Convert to Plain Text'))
            ->setOnClick('templateControl.stripTags();');
    }

    public function getButtonToggleBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock()
            ->setId('toggle_button')
            ->setLabel(Mage::helper('adminhtml')->__('Toggle Editor'))
            ->setOnClick('templateControl.toggleEditor();');
    }

    /**
     * @return string
     */
    public function getToggleButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_TOGGLE);
    }

    /**
     * @return string
     */
    public function getToPlainButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_TO_PLAIN);
    }

    /**
     * @return string
     */
    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_TO_HTML);
    }

    /**
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_PREVIEW);
    }

    /**
     * @return string
     */
    public function getLoadButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_LOAD);
    }

    /**
     * Return edit flag for block
     *
     * @return int|string
     */
    public function getEditMode()
    {
        return $this->getEmailTemplate()->getId();
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return Mage::helper('adminhtml')->__('Edit Email Template');
        }
        return  Mage::helper('adminhtml')->__('New Email Template');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml(self::BLOCK_FORM);
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true]);
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * @return bool
     */
    public function isTextType()
    {
        return $this->getEmailTemplate()->isPlain();
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrlSecure('*/*/delete', ['_current' => true]);
    }

    /**
     * Retrieve email template model
     *
     * @return Mage_Core_Model_Email_Template
     */
    public function getEmailTemplate()
    {
        return Mage::registry('current_email_template');
    }

    public function getLocaleOptions()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }

    public function getTemplateOptions()
    {
        return Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
    }

    public function getCurrentLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Load template url
     *
     * @return string
     */
    public function getLoadUrl()
    {
        return $this->getUrl('*/*/defaultTemplate');
    }

    /**
     * Get paths of where current template is used as default
     *
     * @param bool $asJSON
     * @return string|array
     */
    public function getUsedDefaultForPaths($asJSON = true)
    {
        $paths = $this->getEmailTemplate()->getSystemConfigPathsWhereUsedAsDefault();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if ($asJSON) {
            return Mage::helper('core')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Get paths of where current template is currently used
     *
     * @param bool $asJSON
     * @return string|array
     */
    public function getUsedCurrentlyForPaths($asJSON = true)
    {
        $paths = $this->getEmailTemplate()->getSystemConfigPathsWhereUsedCurrently();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if ($asJSON) {
            return Mage::helper('core')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Convert xml config paths to decorated names
     *
     * @param array $paths
     * @return array
     */
    protected function _getSystemConfigPathsParts($paths)
    {
        $result = $urlParams = $prefixParts = [];
        $scopeLabel = Mage::helper('adminhtml')->__('GLOBAL');
        if ($paths) {
            // create prefix path parts
            $prefixParts[] = [
                'title' => Mage::getSingleton('admin/config')->getMenuItemLabel('system'),
            ];
            $prefixParts[] = [
                'title' => Mage::getSingleton('admin/config')->getMenuItemLabel('system/config'),
                'url' => $this->getUrl('adminhtml/system_config/'),
            ];

            $pathParts = $prefixParts;
            foreach ($paths as $id => $pathData) {
                list($sectionName, $groupName, $fieldName) = explode('/', $pathData['path']);
                $urlParams = ['section' => $sectionName];
                if (isset($pathData['scope']) && isset($pathData['scope_id'])) {
                    switch ($pathData['scope']) {
                        case 'stores':
                            $store = Mage::app()->getStore($pathData['scope_id']);
                            if ($store) {
                                $urlParams['website'] = $store->getWebsite()->getCode();
                                $urlParams['store'] = $store->getCode();
                                $scopeLabel = $store->getWebsite()->getName() . '/' . $store->getName();
                            }
                            break;
                        case 'websites':
                            $website = Mage::app()->getWebsite($pathData['scope_id']);
                            if ($website) {
                                $urlParams['website'] = $website->getCode();
                                $scopeLabel = $website->getName();
                            }
                            break;
                        default:
                            break;
                    }
                }
                $pathParts[] = [
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName),
                    'url' => $this->getUrl('adminhtml/system_config/edit', $urlParams),
                ];
                $pathParts[] = [
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName, $groupName)
                ];
                $pathParts[] = [
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName, $groupName, $fieldName),
                    'scope' => $scopeLabel
                ];
                $result[] = $pathParts;
                $pathParts = $prefixParts;
            }
        }
        return $result;
    }

    /**
     * Return original template code of current template
     *
     * @return string
     */
    public function getOrigTemplateCode()
    {
        return $this->getEmailTemplate()->getOrigTemplateCode();
    }
}
