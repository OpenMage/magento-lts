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
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Widget model for different purposes
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Widget extends Varien_Object
{
    /**
     * Load Widgets XML config from widget.xml files and cache it
     *
     * @return Varien_Simplexml_Config
     */
    public function getXmlConfig()
    {
        $cachedXml = Mage::app()->loadCache('cms_widget_config');
        if ($cachedXml) {
            $xmlConfig = new Varien_Simplexml_Config($cachedXml);
        } else {
            $config = new Varien_Simplexml_Config;
            $config->loadString('<?xml version="1.0"?><config><widgets></widgets></config>');
            Mage::getConfig()->loadModulesConfiguration('widget.xml', $config);
            $xmlConfig = $config;
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($config->getXmlString(), 'cms_widget_config',
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
        return $xmlConfig;
    }

    /**
     * Return widget XML config element based on its type
     *
     * @param string $type Widget type
     * @return Varien_Simplexml_Element
     */
    public function getXmlElementByType($type)
    {
        $elements = $this->getXmlConfig()->getNode('widgets')->xpath('*[@type="' . $type . '"]');
        if (is_array($elements) && isset($elements[0]) && $elements[0] instanceof Varien_Simplexml_Element) {
            return $elements[0];
        }
        return null;
    }

    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     */
    public function getWidgetDeclaration($type, $params = array(), $asIs = true)
    {
        $widget = $this->getXmlElementByType($type);

        $directive = '{{widget type="' . $type . '"';
        foreach ($params as $name => $value) {
            // Retrieve default option value if pre-configured
            if (trim($value) == '' && $widget->parameters) {
                $value = (string)$widget->parameters->{$name}->value;
            }
            if ($value) {
                $directive .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $directive .= '}}';

        if ($asIs) {
            return $directive;
        }

        $imageName = str_replace('/', '__', $type) . '.gif';
        if (is_file($this->getPlaceholderImagesBaseDir() . DS . $imageName)) {
            $image = $this->getPlaceholderImagesBaseUrl() . $imageName;
        } else {
            $image = $this->getPlaceholderImagesBaseUrl() . 'default.gif';
        }
        $html = sprintf('<img id="%s" src="%s" class="widget" title="%s">',
            $this->_idEncode($directive),
            $image,
            Mage::helper('core')->urlEscape($directive)
        );
        return $html;
    }

    /**
     * Return Widget placeholders images URL
     *
     * @return string
     */
    public function getPlaceholderImagesBaseUrl()
    {
        return Mage::getDesign()->getSkinUrl('images/widget/');
    }

    /**
     * Return Widget placeholders images dir
     *
     * @return string
     */
    public function getPlaceholderImagesBaseDir()
    {
        return Mage::getDesign()->getSkinBaseDir() . DS . 'images' . DS . 'widget';
    }

    /**
     * Encode string to valid HTML id element, based on base64 encoding
     *
     * @param string $string
     * @return string
     */
    protected function _idEncode($string)
    {
        return strtr(base64_encode($string), '+/=', ':_-');
    }
}
