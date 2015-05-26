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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Validator for check not protected/available path
 *
 * Mask symbols from path:
 * "?" - something directory with any name
 * "*" - something directory structure, which can not exist
 * Note: For set directory structure which must be exist, need to set mask "/?/{@*}"
 * Mask symbols from filename:
 * "*" - something symbols in file name
 * Example:
 * <code>
 * //set available path
 * $validator->setAvailablePath(array('/path/to/?/*fileMask.xml'));
 * $validator->isValid('/path/to/MyDir/Some-fileMask.xml'); //return true
 * $validator->setAvailablePath(array('/path/to/{@*}*.xml'));
 * $validator->isValid('/path/to/my.xml'); //return true, because directory structure can't exist
 * </code>
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Validator_AvailablePath extends Zend_Validate_Abstract
{
    const PROTECTED_PATH     = 'protectedPath';
    const NOT_AVAILABLE_PATH = 'notAvailablePath';
    const PROTECTED_LFI      = 'protectedLfi';

    /**
     * The path
     *
     * @var string
     */
    protected $_value;

    /**
     * Protected paths
     *
     * @var array
     */
    protected $_protectedPaths = array();

    /**
     * Available paths
     *
     * @var array
     */
    protected $_availablePaths = array();

    /**
     * Cache of made regular expressions from path masks
     *
     * @var array
     */
    protected $_pathsData;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_initMessageTemplates();
    }

    /**
     * Initialize message templates with translating
     *
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::PROTECTED_PATH =>
                    Mage::helper('core')->__('Path "%value%" is protected and cannot be used.'),
                self::NOT_AVAILABLE_PATH =>
                    Mage::helper('core')->__('Path "%value%" is not available and cannot be used.'),
                self::PROTECTED_LFI =>
                    Mage::helper('core')->__('Path "%value%" may not include parent directory traversal ("../", "..\\").'),
            );
        }
        return $this;
    }

    /**
     * Set paths masks
     *
     * @param array $paths  All paths masks types.
     *                      E.g.: array('available' => array(...), 'protected' => array(...))
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    public function setPaths(array $paths)
    {
        if (isset($paths['available']) && is_array($paths['available'])) {
            $this->_availablePaths = $paths['available'];
        }
        if (isset($paths['protected']) && is_array($paths['protected'])) {
            $this->_protectedPaths = $paths['protected'];
        }
        return $this;
    }

    /**
     * Set protected paths masks
     *
     * @param array $paths
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    public function setProtectedPaths(array $paths)
    {
        $this->_protectedPaths = $paths;
        return $this;
    }

    /**
     * Add protected paths masks
     *
     * @param string|array $path
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    public function addProtectedPath($path)
    {
        if (is_array($path)) {
            $this->_protectedPaths = array_merge($this->_protectedPaths, $path);
        } else {
            $this->_protectedPaths[] = $path;
        }
        return $this;
    }

    /**
     * Get protected paths masks
     *
     * @return array
     */
    public function getProtectedPaths()
    {
        return $this->_protectedPaths;
    }

    /**
     * Set available paths masks
     *
     * @param array $paths
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    public function setAvailablePaths(array $paths)
    {
        $this->_availablePaths = $paths;
        return $this;
    }

    /**
     * Add available paths mask
     *
     * @param string|array $path
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    public function addAvailablePath($path)
    {
        if (is_array($path)) {
            $this->_availablePaths = array_merge($this->_availablePaths, $path);
        } else {
            $this->_availablePaths[] = $path;
        }
        return $this;
    }

    /**
     * Get available paths masks
     *
     * @return array
     */
    public function getAvailablePaths()
    {
        return $this->_availablePaths;
    }


    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @throws Exception        Throw exception on empty both paths masks types
     * @param string $value     File/dir path
     * @return bool
     */
    public function isValid($value)
    {
        $value = trim($value);
        $this->_setValue($value);

        if (!$this->_availablePaths && !$this->_protectedPaths) {
            throw new Exception(Mage::helper('core')->__('Please set available and/or protected paths list(s) before validation.'));
        }

        if (preg_match('#\.\.[\\\/]#', $this->_value)) {
            $this->_error(self::PROTECTED_LFI, $this->_value);
            return false;
        }

        //validation
        $value = str_replace(array('/', '\\'), DS, $this->_value);
        $valuePathInfo = pathinfo(ltrim($value, '\\/'));
        if ($valuePathInfo['dirname'] == '.' || $valuePathInfo['dirname'] == DS) {
            $valuePathInfo['dirname'] = '';
        }

        if ($this->_protectedPaths && !$this->_isValidByPaths($valuePathInfo, $this->_protectedPaths, true)) {
            $this->_error(self::PROTECTED_PATH, $this->_value);
            return false;
        }
        if ($this->_availablePaths && !$this->_isValidByPaths($valuePathInfo, $this->_availablePaths, false)) {
            $this->_error(self::NOT_AVAILABLE_PATH, $this->_value);
            return false;
        }

        return true;
    }

    /**
     * Validate value by path masks
     *
     * @param array $valuePathInfo  Path info from value path
     * @param array $paths          Protected/available paths masks
     * @param bool $protected       Paths masks is protected?
     * @return bool
     */
    protected function _isValidByPaths($valuePathInfo, $paths, $protected)
    {
        foreach ($paths as $path) {
            $path = ltrim($path, '\\/');
            if (!isset($this->_pathsData[$path]['regFilename'])) {
                $pathInfo = pathinfo($path);
                $options['file_mask'] = $pathInfo['basename'];
                if ($pathInfo['dirname'] == '.' || $pathInfo['dirname'] == DS) {
                    $pathInfo['dirname'] = '';
                } else {
                    $pathInfo['dirname'] = str_replace(array('/', '\\'), DS, $pathInfo['dirname']);
                }
                $options['dir_mask'] = $pathInfo['dirname'];
                $this->_pathsData[$path]['options'] = $options;
            } else {
                $options = $this->_pathsData[$path]['options'];
            }

            //file mask
            if (false !== (strpos($options['file_mask'], '*'))) {
                if (!isset($this->_pathsData[$path]['regFilename'])) {
                    //make regular
                    $reg = $options['file_mask'];
                    $reg = str_replace('.', '\.', $reg);
                    $reg = str_replace('*', '.*?', $reg);
                    $reg = "/^($reg)$/";
                } else {
                    $reg = $this->_pathsData[$path]['regFilename'];
                }
                $resultFile = preg_match($reg, $valuePathInfo['basename']);
            } else {
                $resultFile = ($options['file_mask'] == $valuePathInfo['basename']);
            }

            //directory mask
            $reg = $options['dir_mask'] . DS;
            if (!isset($this->_pathsData[$path]['regDir'])) {
                //make regular
                $reg = str_replace('.', '\.', $reg);
                $reg = str_replace('*\\', '||', $reg);
                $reg = str_replace('*/', '||', $reg);
                //$reg = str_replace('*', '||', $reg);
                $reg = str_replace(DS, '[\\' . DS . ']', $reg);
                $reg = str_replace('?', '([^\\' . DS . ']+)', $reg);
                $reg = str_replace('||', '(.*[\\' . DS . '])?', $reg);
                $reg = "/^$reg$/";
            } else {
                $reg = $this->_pathsData[$path]['regDir'];
            }
            $resultDir = preg_match($reg, $valuePathInfo['dirname'] . DS);

            if ($protected && ($resultDir && $resultFile)) {
                return false;
            } elseif (!$protected && ($resultDir && $resultFile)) {
                //return true because one match with available path mask
                return true;
            }
        }
        if ($protected) {
            return true;
        } else {
            //return false because no one match with available path mask
            return false;
        }
    }
}
