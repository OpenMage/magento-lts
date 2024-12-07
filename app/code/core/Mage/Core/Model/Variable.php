<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom variable model
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Variable _getResource()
 * @method Mage_Core_Model_Resource_Variable getResource()
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getName()
 * @method $this setName(string $value)
 * @method bool getUseDefaultValue()
 * @method string getHtmlValue()
 * @method string getPlainValue()
 */
class Mage_Core_Model_Variable extends Mage_Core_Model_Abstract
{
    public const TYPE_TEXT = 'text';
    public const TYPE_HTML = 'html';

    protected $_storeId = 0;

    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('core/variable');
    }

    /**
     * Setter
     *
     * @param int $storeId
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
     * @param string $code
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
     * @param string $type
     * @return string
     */
    public function getValue($type = null)
    {
        if ($type === null) {
            $type = self::TYPE_HTML;
        }
        if ($type == self::TYPE_TEXT || !(strlen((string)$this->getData('html_value')))) {
            $value = $this->getData('plain_value');
            //escape html if type is html, but html value is not defined
            if ($type == self::TYPE_HTML) {
                $value = nl2br(Mage::helper('core')->escapeHtml($value));
            }
            return $value;
        }
        return $this->getData('html_value');
    }

    /**
     * Validation of object data. Checking for unique variable code
     *
     * @return bool | string
     */
    public function validate()
    {
        if ($this->getCode() && $this->getName()) {
            $variable = $this->getResource()->getVariableByCode($this->getCode());
            if (!empty($variable) && $variable['variable_id'] != $this->getId()) {
                return Mage::helper('core')->__('Variable Code must be unique.');
            }
            return true;
        }
        return Mage::helper('core')->__('Validation has failed.');
    }

    /**
     * Retrieve variables option array
     *
     * @param bool $withGroup
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false)
    {
        /** @var Mage_Core_Model_Resource_Variable_Collection $collection */
        $collection = $this->getCollection();
        $variables = [];
        foreach ($collection->toOptionArray() as $variable) {
            $variables[] = [
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => Mage::helper('core')->__(
                    '%s',
                    Mage::helper('core')->escapeHtml($variable['label'])
                )
            ];
        }
        if ($withGroup && $variables) {
            $variables = [
                'label' => Mage::helper('core')->__('Custom Variables'),
                'value' => $variables
            ];
        }
        return $variables;
    }
}
