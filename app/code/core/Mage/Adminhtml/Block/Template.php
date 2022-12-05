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
 * Adminhtml abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Template extends Mage_Core_Block_Template
{
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
     * Check whether or not the module output is enabled
     *
     * Because many module blocks belong to Adminhtml module,
     * the feature "Disable module output" doesn't cover Admin area
     *
     * @param string $moduleName Full module name
     * @return bool
     */
    public function isOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }
        return !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
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

    /**
     * Helper for "onclick.deleteConfirm"
     *
     * @param string $url
     * @param string|null $message null for default message, do not use jsQuoteEscape() before
     * @return string
     * @uses Mage_Core_Helper_Abstract::jsQuoteEscape()
     */
    protected function getDeleteConfirmJs(string $url, ?string $message = null): string
    {
        if (is_null($message)) {
            $message = Mage::helper('adminhtml')->__('Are you sure you want to do this?');
        }

        $message = Mage::helper('core')->jsQuoteEscape($message);
        return 'deleteConfirm(\'' . $message . '\', \'' . $url . '\')';
    }

    /**
     * Helper for "onclick.confirmSetLocation"
     *
     * @param string $url
     * @param string|null $message null for default message, do not use jsQuoteEscape() before
     * @return string
     * @uses Mage_Core_Helper_Abstract::jsQuoteEscape()
     */
    protected function getConfirmSetLocationJs(string $url, ?string $message = null): string
    {
        if (is_null($message)) {
            $message = Mage::helper('adminhtml')->__('Are you sure you want to do this?');
        }

        $message = Mage::helper('core')->jsQuoteEscape($message);
        return "confirmSetLocation('{$message}', '{$url}')";
    }

    /**
     * Helper for "onclick.setLocation"
     *
     * @param string $url
     * @return string
     */
    protected function getSetLocationJs(string $url): string
    {
        return 'setLocation(\'' . $url . '\')';
    }

    /**
     * Helper for "onclick.saveAndContinueEdit"
     *
     * @param string $url
     * @return string
     */
    protected function getSaveAndContinueEditJs(string $url): string
    {
        return 'saveAndContinueEdit(\'' . $url . '\')';
    }
}
