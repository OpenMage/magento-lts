<?php

/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 * @method $this setDomNodes(array $domNodesIds)
 * @method $this setIsDirectory(bool $isDirectory)
 * @method $this setSingleFile(bool $isSingleFile)
 * @method $this setAttributes(array $attributes)
 * @see http://www.w3.org/TR/html-markup/input.file.html#input.file-attributes
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
     * @param string|array $exts
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
