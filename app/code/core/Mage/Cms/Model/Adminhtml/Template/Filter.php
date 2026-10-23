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

    /**
     * Resolve a WYSIWYG image directive to a file inside the media directory
     *
     * The directive must be exactly one {{media url="..."}} construction. Every other
     * directive, and any text around the construction, is rejected so that the result
     * can never be a URL (SSRF) or a path produced by another directive.
     *
     * @throws Mage_Core_Exception
     */
    public function getMediaFilePath(string $directive): string
    {
        if (!preg_match(Varien_Filter_Template::CONSTRUCTION_PATTERN, $directive, $construction)
            || $construction[0] !== $directive
            || $construction[1] !== 'media'
        ) {
            Mage::throwException(Mage::helper('core')->__('Invalid media directive.'));
        }

        return $this->mediaDirective($construction);
    }
}
