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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system template edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Email_Template_Edit extends Mage_Adminhtml_Block_Widget
{


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/email/template/edit.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class'   => 'back'
                    )
                )
        );


        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Reset'),
                        'onclick' => 'window.location.href = window.location.href'
                    )
                )
        );


        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Delete Template'),
                        'onclick' => 'templateControl.deleteTemplate();',
                        'class'   => 'delete'
                    )
                )
        );

        $this->setChild('to_plain_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Convert to Plain Text'),
                        'onclick' => 'templateControl.stripTags();',
                        'id'      => 'convert_button'
                    )
                )
        );


        $this->setChild('to_html_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Return Html Version'),
                        'onclick' => 'templateControl.unStripTags();',
                        'id'      => 'convert_button_back',
                        'style'   => 'display:none'
                    )
                )
        );

        $this->setChild('toggle_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Toggle Editor'),
                        'onclick' => 'templateControl.toggleEditor();',
                        'id'      => 'toggle_button'
                    )
                )
        );


        $this->setChild('preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Preview Template'),
                        'onclick' => 'templateControl.preview();'
                    )
                )
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Save Template'),
                        'onclick' => 'templateControl.save();',
                        'class'   => 'save'
                    )
                )
        );

        $this->setChild('load_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Load Template'),
                        'onclick' => 'templateControl.load();',
                        'type'    => 'button',
                        'class'   => 'save'
                    )
                )
        );


        $this->setChild('form',
            $this->getLayout()->createBlock('adminhtml/system_email_template_edit_form')
        );
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getToggleButtonHtml()
    {
        return $this->getChildHtml('toggle_button');
    }


    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getToPlainButtonHtml()
    {
        return $this->getChildHtml('to_plain_button');
    }

    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml('to_html_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getLoadButtonHtml()
    {
        return $this->getChildHtml('load_button');
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
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
        if($this->getEditMode()) {
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
        return $this->getChildHtml('form');
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
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
        return $this->getUrlSecure('*/*/delete', array('_current' => true));
    }

    /**
     * Retrive email template model
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
     * @return string
     */
    public function getUsedDefaultForPaths($asJSON = true)
    {
        $paths = $this->getEmailTemplate()->getSystemConfigPathsWhereUsedAsDefault();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if($asJSON){
            return Mage::helper('core')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Get paths of where current template is currently used
     *
     * @param bool $asJSON
     * @return string
     */
    public function getUsedCurrentlyForPaths($asJSON = true)
    {
        $paths = $this->getEmailTemplate()->getSystemConfigPathsWhereUsedCurrently();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if($asJSON){
            return Mage::helper('core')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Convert xml config pathes to decorated names
     *
     * @param array $paths
     * @return array
     */
    protected function _getSystemConfigPathsParts($paths)
    {
        $result = $urlParams = $prefixParts = array();
        $scopeLabel = Mage::helper('adminhtml')->__('GLOBAL');
        if ($paths) {
            // create prefix path parts
            $prefixParts[] = array(
                'title' => Mage::getSingleton('admin/config')->getMenuItemLabel('system'),
            );
            $prefixParts[] = array(
                'title' => Mage::getSingleton('admin/config')->getMenuItemLabel('system/config'),
                'url' => $this->getUrl('adminhtml/system_config/'),
            );

            $pathParts = $prefixParts;
            foreach ($paths as $id => $pathData) {
                list($sectionName, $groupName, $fieldName) = explode('/', $pathData['path']);
                $urlParams = array('section' => $sectionName);
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
                $pathParts[] = array(
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName),
                    'url' => $this->getUrl('adminhtml/system_config/edit', $urlParams),
                );
                $pathParts[] = array(
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName, $groupName)
                );
                $pathParts[] = array(
                    'title' => Mage::getSingleton('adminhtml/config')->getSystemConfigNodeLabel($sectionName, $groupName, $fieldName),
                    'scope' => $scopeLabel
                );
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
