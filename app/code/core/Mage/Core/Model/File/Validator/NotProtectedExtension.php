<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Validator for check not protected file extensions
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_File_Validator_NotProtectedExtension extends Mage_Core_Helper_Validate_Abstract
{
    public const PROTECTED_EXTENSION = 'protectedExtension';

    /**
     * The file extension
     *
     * @var string
     */
    protected $_value;

    /**
     * Protected file types
     *
     * @var array
     */
    protected $_protectedFileExtensions = [];

    public function __construct()
    {
        $this->_initMessageTemplates();
        $this->_initProtectedFileExtensions();
    }

    /**
     * Initialize message templates with translating
     *
     * @return $this
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = [
                self::PROTECTED_EXTENSION => Mage::helper('core')->__('File with an extension "%value%" is protected and cannot be uploaded'),
            ];
        }

        return $this;
    }

    /**
     * Initialize protected file extensions
     *
     * @return $this
     */
    protected function _initProtectedFileExtensions()
    {
        if (!$this->_protectedFileExtensions) {
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');
            $extensions = $helper->getProtectedFileExtensions();
            if (is_string($extensions)) {
                $extensions = explode(',', $extensions);
            }

            foreach ($extensions as &$ext) {
                $ext = strtolower(trim($ext));
            }

            $this->_protectedFileExtensions = (array) $extensions;
        }

        return $this;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  string $value Extension of file
     * @return bool
     */
    public function isValid($value)
    {
        $value = strtolower(trim($value));
        $this->_setValue($value);

        if (in_array($this->_value, $this->_protectedFileExtensions)) {
            $this->_error(self::PROTECTED_EXTENSION, $this->_value);
            return false;
        }

        return true;
    }
}
