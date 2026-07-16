<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms Adminhtml Template Filter Model
 *
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
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['url'])) {
            Mage::throwException('Undefined url parameter for media directive.');
        }

        $mediaRoot = realpath(Mage::getBaseDir('media'));
        $mediaPath = realpath(Mage::getBaseDir('media') . DS . $params['url']);
        if ($mediaRoot === false
            || $mediaPath === false
            || !str_starts_with($mediaPath, $mediaRoot . DS)
            || !is_file($mediaPath)
        ) {
            Mage::throwException('Invalid media path.');
        }

        return $mediaPath;
    }
}
