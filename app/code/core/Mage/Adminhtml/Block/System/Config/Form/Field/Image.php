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
