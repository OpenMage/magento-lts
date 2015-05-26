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
 * @package     Mage_Widget
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget model for different purposes
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Widget extends Varien_Object
{
    /**
     * Load Widgets XML config from widget.xml files and cache it
     *
     * @return Varien_Simplexml_Config
     */
    public function getXmlConfig()
    {
        $cachedXml = Mage::app()->loadCache('widget_config');
        if ($cachedXml) {
            $xmlConfig = new Varien_Simplexml_Config($cachedXml);
        } else {
            $config = new Varien_Simplexml_Config();
            $config->loadString('<?xml version="1.0"?><widgets></widgets>');
            Mage::getConfig()->loadModulesConfiguration('widget.xml', $config);
            $xmlConfig = $config;
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($config->getXmlString(), 'widget_config',
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
        return $xmlConfig;
    }

    /**
     * Return widget XML config element based on its type
     *
     * @param string $type Widget type
     * @return null|Varien_Simplexml_Element
     */
    public function getXmlElementByType($type)
    {
        $elements = $this->getXmlConfig()->getXpath('*[@type="' . $type . '"]');
        if (is_array($elements) && isset($elements[0]) && $elements[0] instanceof Varien_Simplexml_Element) {
            return $elements[0];
        }
        return null;
    }

    /**
     * Wrapper for getXmlElementByType method
     *
     * @param string $type Widget type
     * @return null|Varien_Simplexml_Element
     */
    public function getConfigAsXml($type)
    {
        return $this->getXmlElementByType($type);
    }

    /**
     * Return widget XML configuration as Varien_Object and makes some data preparations
     *
     * @param string $type Widget type
     * @return Varien_Object
     */
    public function getConfigAsObject($type)
    {
        $xml = $this->getConfigAsXml($type);

        $object = new Varien_Object();
        if ($xml === null) {
            return $object;
        }

        // Save all nodes to object data
        $object->setType($type);
        $object->setData($xml->asCanonicalArray());

        // Set module for translations etc.
        $module = $object->getData('@/module');
        if ($module) {
            $object->setModule($module);
        }

        // Correct widget parameters and convert its data to objects
        $params = $object->getData('parameters');
        $newParams = array();
        if (is_array($params)) {
            $sortOrder = 0;
            foreach ($params as $key => $data) {
                if (is_array($data)) {
                    $data['key'] = $key;
                    $data['sort_order'] = isset($data['sort_order']) ? (int)$data['sort_order'] : $sortOrder;

                    // prepare values (for drop-dawns) specified directly in configuration
                    $values = array();
                    if (isset($data['values']) && is_array($data['values'])) {
                        foreach ($data['values'] as $value) {
                            if (isset($value['label']) && isset($value['value'])) {
                                $values[] = $value;
                            }
                        }
                    }
                    $data['values'] = $values;

                    // prepare helper block object
                    if (isset($data['helper_block'])) {
                        $helper = new Varien_Object();
                        if (isset($data['helper_block']['data']) && is_array($data['helper_block']['data'])) {
                            $helper->addData($data['helper_block']['data']);
                        }
                        if (isset($data['helper_block']['type'])) {
                            $helper->setType($data['helper_block']['type']);
                        }
                        $data['helper_block'] = $helper;
                    }

                    $newParams[$key] = new Varien_Object($data);
                    $sortOrder++;
                }
            }
        }
        uasort($newParams, array($this, '_sortParameters'));
        $object->setData('parameters', $newParams);

        return $object;
    }

    /**
     * Return filtered list of widgets as SimpleXml object
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return Varien_Simplexml_Element
     */
    public function getWidgetsXml($filters = array())
    {
        $widgets = $this->getXmlConfig()->getNode();
        $result = clone $widgets;

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0) {
            foreach ($widgets as $code => $widget) {
                try {
                    $reflection = new ReflectionObject($widget);
                    foreach ($filters as $field => $value) {
                        if (!$reflection->hasProperty($field) || (string)$widget->{$field} != $value) {
                            throw new Exception();
                        }
                    }
                } catch (Exception $e) {
                    unset($result->{$code});
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Return list of widgets as array
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     */
    public function getWidgetsArray($filters = array())
    {
        if (!$this->_getData('widgets_array')) {
            $result = array();
            foreach ($this->getWidgetsXml($filters) as $widget) {
                $helper = $widget->getAttribute('module') ? $widget->getAttribute('module') : 'widget';
                $helper = Mage::helper($helper);
                $result[$widget->getName()] = array(
                    'name'          => $helper->__((string)$widget->name),
                    'code'          => $widget->getName(),
                    'type'          => $widget->getAttribute('type'),
                    'description'   => $helper->__((string)$widget->description)
                );
            }
            usort($result, array($this, "_sortWidgets"));
            $this->setData('widgets_array', $result);
        }
        return $this->_getData('widgets_array');
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
        $directive = '{{widget type="' . $type . '"';

        foreach ($params as $name => $value) {
            // Retrieve default option value if pre-configured
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif (trim($value) == '') {
                $widget = $this->getConfigAsObject($type);
                $parameters = $widget->getParameters();
                if (isset($parameters[$name]) && is_object($parameters[$name])) {
                    $value = $parameters[$name]->getValue();
                }
            }
            if ($value) {
                $directive .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $directive .= '}}';

        if ($asIs) {
            return $directive;
        }

        $config = Mage::getSingleton('widget/widget_config');
        $imageName = str_replace('/', '__', $type) . '.gif';
        if (is_file($config->getPlaceholderImagesBaseDir() . DS . $imageName)) {
            $image = $config->getPlaceholderImagesBaseUrl() . $imageName;
        } else {
            $image = $config->getPlaceholderImagesBaseUrl() . 'default.gif';
        }
        $html = sprintf('<img id="%s" src="%s" title="%s">',
            $this->_idEncode($directive),
            $image,
            Mage::helper('core')->urlEscape($directive)
        );
        return $html;
    }

    /**
     * Return list of required JS files to be included on the top of the page before insertion plugin loaded
     *
     * @return array
     */
    public function getWidgetsRequiredJsFiles()
    {
        $result = array();
        foreach ($this->getWidgetsXml() as $widget) {
            if ($widget->js) {
                foreach (explode(',', (string)$widget->js) as $js) {
                    $result[] = $js;
                }
            }
       }
       return $result;
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

    /**
     * User-defined widgets sorting by Name
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortWidgets($a, $b)
    {
        return strcmp($a["name"], $b["name"]);
    }

    /**
     * Widget parameters sort callback
     *
     * @param Varien_Object $a
     * @param Varien_Object $b
     * @return int
     */
    protected function _sortParameters($a, $b)
    {
        $aOrder = (int)$a->getData('sort_order');
        $bOrder = (int)$b->getData('sort_order');
        return $aOrder < $bOrder ? -1 : ($aOrder > $bOrder ? 1 : 0);
    }
}
