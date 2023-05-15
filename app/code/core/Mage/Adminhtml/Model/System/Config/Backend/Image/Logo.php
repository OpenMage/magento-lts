<?php
/**
OpenMage

This source file is subject to the Academic Free License (AFL 3.0)
that is bundled with this package in the file LICENSE_AFL.txt.
It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image_Logo extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
	* The tail part of directory path for uploading
	*/
	const UPLOAD_DIR				= 'header/logo';

	/**
	* Token for the root part of directory path for uploading
	*/
	const UPLOAD_ROOT_TOKEN			= 'system/filesystem/media';

	/**
	* Upload max file size in kilobytes
	*
	* @var int
	*/
	protected $_maxFileSize			= 2048;

	/**
	* Return path to directory for upload file
	*
	* @return string
	*/
	protected function _getUploadDir() {
		$uploadDir  = $this->_appendScopeInfo(self::UPLOAD_DIR);
		$uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN);
		$uploadDir  = $uploadRoot . DS . $uploadDir;
		return $uploadDir;
	}

	/**
	* Makes a decision about whether to add info about the scope
	*
	* @return boolean
	*/
	protected function _addWhetherScopeInfo() {
		return true;
	}
}