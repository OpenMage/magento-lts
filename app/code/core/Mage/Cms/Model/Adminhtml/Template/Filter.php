<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cms
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
