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
 * @deprecated Use Mage_Validation_Helper_Data::validateUrl() instead
 * @see Mage_Validation_Helper_Data::validateUrl()
 */
class Mage_Core_Model_Url_Validator extends Mage_Validation_Helper_Abstract
{
    /**
     * Error keys
     */
    public const INVALID_URL = 'invalidUrl';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID_URL => "Invalid URL '%value%'.",
    ];

    /**
     * Object constructor
     * @throws Exception
     */
    public function __construct()
    {
        // set translated message template
        $this->setMessage(
            Mage::helper('core')->__($this->_messageTemplates[self::INVALID_URL]),
            self::INVALID_URL,
        );
    }

    /**
     * Validate value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        /** @var Mage_Validation_Helper_Data $validator */
        $validator = Mage::helper('validation');

        //check valid URL
        if ($validator->validateUrl(value: $value)->count() > 0) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
