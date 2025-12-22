<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Validator for check input type value
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator extends Mage_Core_Helper_Validate_Abstract
{
    public const NOT_IN_ARRAY = 'notInArray';

    /**
     * @inheritDoc
     */
    protected $_messageTemplates;

    protected array $haystack = [];


    public function __construct()
    {
        //set data haystack
        /** @var Mage_Eav_Helper_Data $helper */
        $helper = Mage::helper('eav');
        $this->haystack = $helper->getInputTypesValidatorData();

        //reset message template and set custom
        $this->_messageTemplates = [];
        $this->_initMessageTemplates();
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
                self::NOT_IN_ARRAY
                    => Mage::helper('core')->__('Input type "%value%" not found in the input types list.'),
            ];
        }

        return $this;
    }

    public function isValid($value)
    {
        $this->_setValue($value);

        if (!in_array((string) $value, $this->haystack, true)) {
            $this->_error(self::NOT_IN_ARRAY);
            return false;
        }

        return true;
    }

    /**
     * Add input type to haystack
     *
     * @param  string $type
     * @return $this
     */
    public function addInputType($type)
    {
        if (!in_array((string) $type, $this->haystack, true)) {
            $this->haystack[] = (string) $type;
        }

        return $this;
    }
}
