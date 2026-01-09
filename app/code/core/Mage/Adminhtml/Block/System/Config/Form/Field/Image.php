<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Image config field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Image extends Varien_Data_Form_Element_Image
{
    /**
     * @inheritDoc
     */
    protected function _getUrl()
    {
        $url = parent::_getUrl();

        /** @var Varien_Simplexml_Element $config */
        $config = $this->getFieldConfig();
        if (!empty($config->base_url)) {
            $element = $config->descend('base_url');
            $urlType = empty($element['type']) ? 'link' : (string) $element['type'];
            $url = Mage::getBaseUrl($urlType) . $config->base_url . '/' . $url;
        }

        return $url;
    }
}
