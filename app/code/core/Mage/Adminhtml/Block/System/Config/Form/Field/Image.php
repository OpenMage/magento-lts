<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Image config field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Get image preview url
     * @return string
     */
    protected function _getUrl()
    {
        $url = parent::_getUrl();

        $config = $this->getFieldConfig();
        /** @var Varien_Simplexml_Element $config */
        if (!empty($config->base_url)) {
            $el = $config->descend('base_url');
            $urlType = empty($el['type']) ? 'link' : (string) $el['type'];
            $url = Mage::getBaseUrl($urlType) . (string) $config->base_url . '/' . $url;
        }

        return $url;
    }
}
