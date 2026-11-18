<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Custom variable model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Variable            _getResource()
 * @method string                                       getCode()
 * @method Mage_Core_Model_Resource_Variable_Collection getCollection()
 * @method string                                       getHtmlValue()
 * @method string                                       getName()
 * @method string                                       getPlainValue()
 * @method Mage_Core_Model_Resource_Variable            getResource()
 * @method Mage_Core_Model_Resource_Variable_Collection getResourceCollection()
 * @method bool                                         getUseDefaultValue()
 * @method $this                                        setCode(string $value)
 * @method $this                                        setName(string $value)
 */
class Mage_Core_Model_Variable extends Mage_Core_Model_Abstract
{
    public const TYPE_TEXT = 'text';

    public const TYPE_HTML = 'html';

    /**
     * Allowed variable code: the rule the admin form has always applied (validate-xml-identifier)
     */
    public const CODE_PATTERN = '/^[a-z][a-z0-9_\/-]*$/iD';

    protected $_storeId = 0;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('core/variable');
    }

    /**
     * Setter
     *
     * @param  int   $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Load variable by code
     *
     * @param  string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Return variable value depend on given type
     *
     * @param  string $type
     * @return string
     */
    public function getValue($type = null)
    {
        if ($type === null) {
            $type = self::TYPE_HTML;
        }

        if ($type == self::TYPE_TEXT || !(strlen((string) $this->getDataByKey('html_value')))) {
            $value = $this->getDataByKey('plain_value');
            //escape html if type is html, but html value is not defined
            if ($type == self::TYPE_HTML) {
                return nl2br(Mage::helper('core')->escapeHtml($value));
            }

            return $value;
        }

        return $this->getDataByKey('html_value');
    }

    /**
     * Validation of object data. Checking for unique variable code
     *
     * @return string|true
     */
    public function validate()
    {
        if ($this->getCode() && $this->getName()) {
            if (!$this->_isCodeAllowed()) {
                return $this->_getInvalidCodeMessage();
            }

            $variable = $this->getResource()->getVariableByCode($this->getCode());
            if (!empty($variable) && $variable['variable_id'] != $this->getId()) {
                return Mage::helper('core')->__('Variable Code must be unique.');
            }

            return true;
        }

        return Mage::helper('core')->__('Validation has failed.');
    }

    /**
     * Check whether a string is a valid variable code
     *
     * @param  mixed $code
     * @return bool
     */
    public static function isValidCode($code)
    {
        return is_string($code) && preg_match(self::CODE_PATTERN, $code) === 1;
    }

    /**
     * Reject a new or changed code that is not a valid variable code
     *
     * Codes stored before this check are kept when they are not changed.
     *
     * @inheritDoc
     */
    #[Override]
    protected function _beforeSave()
    {
        if (!$this->_isCodeAllowed()) {
            Mage::throwException($this->_getInvalidCodeMessage());
        }

        return parent::_beforeSave();
    }

    /**
     * The code is valid, or it is an unchanged code of an existing variable
     *
     * @return bool
     */
    protected function _isCodeAllowed()
    {
        if (self::isValidCode($this->getCode())) {
            return true;
        }

        return $this->getId() !== null && $this->getOrigData('code') !== null
            && (string) $this->getOrigData('code') === (string) $this->getCode();
    }

    /**
     * @return string
     */
    protected function _getInvalidCodeMessage()
    {
        return Mage::helper('core')->__(
            'Variable Code must start with a letter and contain only letters, digits, underscores, hyphens and slashes.',
        );
    }

    /**
     * Retrieve variables option array
     *
     * @param  bool  $withGroup
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false)
    {
        $collection = $this->getCollection();
        $variables = [];
        foreach ($collection->toOptionArray() as $variable) {
            $variables[] = [
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => Mage::helper('core')->__(
                    '%s',
                    Mage::helper('core')->escapeHtml($variable['label']),
                ),
            ];
        }

        if ($withGroup && $variables) {
            return [
                'label' => Mage::helper('core')->__('Custom Variables'),
                'value' => $variables,
            ];
        }

        return $variables;
    }
}
