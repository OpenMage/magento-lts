<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms Adminhtml Template Filter Model
 *
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Adminhtml_Template_Filter extends Mage_Cms_Model_Template_Filter
{
    /**
     * Retrieve media file local path directive
     *
     * @internal to avoid usage of urls at functions sensitive to "allow_url_fopen" php setting at GD2 adapter
     *
     * @param array $construction
     *
     * @return string
     *
     * @throws Mage_Core_Exception
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['url'])) {
            Mage::throwException('Undefined url parameter for media directive.');
        }

        return Mage::getBaseDir('media') . DS . $params['url'];
    }
}
