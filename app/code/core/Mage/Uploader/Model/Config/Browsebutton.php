<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 */

/**
 * @package    Mage_Uploader
 *
 * @method $this setIsDirectory(bool $isDirectory)
 *      Pass in true to allow directories to be selected (Google Chrome only)
 * @method $this setAttributes(array $attributes)
 *      Pass object of keys and values to set custom attributes on input fields.
 * @method $this setDomNodes(array $domNodesIds)
 *      Array of element browse buttons ids
 * @method $this setSingleFile(bool $isSingleFile)
 *      To prevent multiple file uploads set this to true.
 *      Also look at config parameter singleFile (Mage_Uploader_Model_Config_Uploader setSingleFile())
 *      @see http://www.w3.org/TR/html-markup/input.file.html#input.file-attributes
 */
class Mage_Uploader_Model_Config_Browsebutton extends Mage_Uploader_Model_Config_Abstract
{
    /**
     * Set params for browse button
     */
    protected function _construct()
    {
        $this->setIsDirectory(false);
    }

    /**
     * Get MIME types from files extensions
     *
     * @param array|string $exts
     * @return string
     */
    public function getMimeTypesByExtensions($exts)
    {
        $mimes = array_unique($this->_getHelper()->getMimeTypeFromExtensionList($exts));

        // Not include general file type
        unset($mimes['application/octet-stream']);

        return implode(',', $mimes);
    }
}
