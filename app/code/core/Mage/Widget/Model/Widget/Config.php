<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widgets Insertion Plugin Config for Editor HTML Element
 *
 * @package    Mage_Widget
 */
class Mage_Widget_Model_Widget_Config extends Varien_Object
{
    /**
     * Return config settings for widgets insertion plugin based on editor element config
     *
     * @param  Varien_Object                               $config
     * @return array<string, array<string, string>|string>
     * @throws Exception
     */
    public function getPluginSettings($config)
    {
        return [
            'widget_plugin_src'   => Mage::getBaseUrl('js') . 'mage/adminhtml/wysiwyg/tinymce/plugins/openmagewidget.js',
            'widget_placeholders' => $this->getPlaceholderImages(),
            'widget_window_url'   => $this->getWidgetWindowUrl($config),
        ];
    }

    /**
     * @return array<string, string>
     * @throws Exception
     */
    public function getPlaceholderImages(): array
    {
        // i want glob able path
        $dir = Mage::getBaseDir('skin') . DS . Mage::getDesign()->getArea()
            . DS . "*" . DS . "*" . DS . 'images' . DS . 'widget' . DS . "*.gif";
        $files = array_unique(array_map(basename(...), glob($dir)));
        $result = [];
        foreach ($files as $file) {
            $result[$file] = Mage::getDesign()->getSkinUrl('images/widget/' . $file);
        }
        return $result;
    }

    /**
     * Return Widgets Insertion Plugin Window URL
     *
     * @param  Varien_Object $config Editor element config
     * @return string
     */
    public function getWidgetWindowUrl($config)
    {
        $params = [];

        $skipped = is_array($config->getDataByKey('skip_widgets')) ? $config->getDataByKey('skip_widgets') : [];
        if ($config->hasData('widget_filters')) {
            $all = Mage::getModel('widget/widget')->getWidgetsXml();
            $filtered = Mage::getModel('widget/widget')->getWidgetsXml($config->getDataByKey('widget_filters'));
            $reflection = new ReflectionObject($filtered);
            foreach ($all as $code => $widget) {
                if (!$reflection->hasProperty($code)) {
                    $skipped[] = $widget->getAttribute('type');
                }
            }
        }

        if ($skipped !== []) {
            $params['skip_widgets'] = $this->encodeWidgetsToQuery($skipped);
        }

        return Mage::getSingleton('adminhtml/url')->getUrl('*/widget/index', $params);
    }

    /**
     * Encode list of widget types into query param
     *
     * @param  array  $widgets List of widgets
     * @return string Query param value
     */
    public function encodeWidgetsToQuery($widgets)
    {
        $widgets = is_array($widgets) ? $widgets : [$widgets];
        $param = implode(',', $widgets);
        return Mage::helper('core')->urlEncode($param);
    }

    /**
     * Decode URL query param and return list of widgets
     *
     * @param  string $queryParam Query param value to decode
     * @return array  Array of widget types
     */
    public function decodeWidgetsFromQuery($queryParam)
    {
        $param = Mage::helper('core')->urlDecode($queryParam);
        return preg_split('/\s*\,\s*/', $param, 0, PREG_SPLIT_NO_EMPTY);
    }
}
