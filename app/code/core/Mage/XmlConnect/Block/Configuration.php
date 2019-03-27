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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application configuration renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Configuration extends Mage_Core_Block_Abstract
{
    /**
     * XmlConnect application model
     *
     * @var Mage_XmlConnect_Model_Application
     */
    protected $_connectApp;

    /**
     * Retrieve initialized instance of XmlConnect application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _getConnectApp()
    {
        if (!$this->_connectApp) {
            $this->_connectApp = Mage::helper('xmlconnect')->getApplication();
            if (!$this->_connectApp) {
                $this->_connectApp = Mage::getModel('xmlconnect/application');
                $this->_connectApp->loadDefaultConfiguration();
            }
        }
        return $this->_connectApp;
    }

    /**
     * Recursively build XML configuration tree
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $section
     * @param array $subTree
     * @return Mage_XmlConnect_Block_Configuration
     */
    protected function _buildRecursive($section, $subTree)
    {
        Mage::helper('xmlconnect')->getDeviceHelper()->checkRequiredConfigFields($subTree);

        foreach ($subTree as $key => $value) {
            if (is_array($value)) {
                if ($key == 'fonts') {
                    $subSection = $section->addChild('fonts');
                    foreach ($value as $label => $val) {
                        if (empty($val['name']) || empty($val['size']) || empty($val['color'])) {
                            continue;
                        }
                        $font = $subSection->addChild('font');
                        $font->addAttribute('label', $label);
                        $font->addAttribute('name', $val['name']);
                        $font->addAttribute('size', $val['size']);
                        $font->addAttribute('color', $val['color']);
                    }
                } elseif ($key == 'pages') {
                    $subSection = $section->addChild('content');
                    foreach ($value as $page) {
                        $this->_buildRecursive($subSection->addChild('page'), $page);
                    }
                } else {
                    $subSection = $section->addChild($key);
                    $this->_buildRecursive($subSection, $value);
                }
            } elseif ($value instanceof Mage_XmlConnect_Model_Tabs) {
                foreach ($value->getRenderTabs() as $tab) {
                    $subSection = $section->addChild('tab');
                    $this->_buildRecursive($subSection, $tab);
                }
            } else {
                $value = (string)$value;
                if ($value != '') {
                    $section->addChild($key, Mage::helper('core')->escapeHtml($value));
                }
            }
        }
        return $this;
    }

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xml Mage_XmlConnect_Model_Simplexml_Element */
        $xml = Mage::getModel('xmlconnect/simplexml_element', '<configuration></configuration>');
        $conf = $this->_getConnectApp()->getRenderConf();
        $this->_buildRecursive($xml, Mage::helper('xmlconnect')->excludeXmlConfigKeys($conf))
            ->_addLocalization($xml);
        return $xml->asNiceXml();
    }

    /**
     * Add localization data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xml
     * @return Mage_XmlConnect_Block_Configuration
     */
    protected function _addLocalization(Mage_XmlConnect_Model_Simplexml_Element $xml)
    {
        /** @var $translateHelper Mage_XmlConnect_Helper_Translate */
        $translateHelper = Mage::helper('xmlconnect/translate');
        $xml->addCustomChild('localization', Mage::helper('xmlconnect')->getActionUrl('xmlconnect/localization'), array(
            'hash' => $translateHelper->getHash()
        ));
        return $this;
    }
}
