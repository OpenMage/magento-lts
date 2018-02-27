<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Uploader
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Uploader Config Instance Abstract Model
 *
 * @category    Mage
 * @package     Mage_Uploader
 */
/**
 * @method Mage_Uploader_Model_Config_Browsebutton setDomNodes(array $domNodesIds)
 *      Array of element browse buttons ids
 * @method Mage_Uploader_Model_Config_Browsebutton setIsDirectory(bool $isDirectory)
 *      Pass in true to allow directories to be selected (Google Chrome only)
 * @method Mage_Uploader_Model_Config_Browsebutton setSingleFile(bool $isSingleFile)
 *      To prevent multiple file uploads set this to true.
 *      Also look at config parameter singleFile (Mage_Uploader_Model_Config_Uploader setSingleFile())
 * @method Mage_Uploader_Model_Config_Browsebutton setAttributes(array $attributes)
 *      Pass object of keys and values to set custom attributes on input fields.
 *      @see http://www.w3.org/TR/html-markup/input.file.html#input.file-attributes
 *
 * Class Mage_Uploader_Model_Config_Browsebutton
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
