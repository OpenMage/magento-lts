<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Validate URL
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Url_Validator extends Zend_Validate_Abstract
{
    /**
     * Error keys
     */
    public const INVALID_URL = 'invalidUrl';

    /**
     * Object constructor
     */
    public function __construct()
    {
        // set translated message template
        $this->setMessage(Mage::helper('core')->__("Invalid URL '%value%'."), self::INVALID_URL);
    }

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID_URL => "Invalid URL '%value%'.",
    ];

    /**
     * Validate value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        //check valid URL
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
