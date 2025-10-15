<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Store Contact Information source model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Source_Email_Variables
{
    /**
     * Assoc array of configuration variables
     *
     * @var array
     */
    protected $_configVariables = [];

    public function __construct()
    {
        $this->_configVariables = [
            [
                'value' => Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL,
                'label' => Mage::helper('core')->__('Base Unsecure URL'),
            ],
            [
                'value' => Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL,
                'label' => Mage::helper('core')->__('Base Secure URL'),
            ],
            [
                'value' => 'trans_email/ident_general/name',
                'label' => Mage::helper('core')->__('General Contact Name'),
            ],
            [
                'value' => 'trans_email/ident_general/email',
                'label' => Mage::helper('core')->__('General Contact Email'),
            ],
            [
                'value' => 'trans_email/ident_sales/name',
                'label' => Mage::helper('core')->__('Sales Representative Contact Name'),
            ],
            [
                'value' => 'trans_email/ident_sales/email',
                'label' => Mage::helper('core')->__('Sales Representative Contact Email'),
            ],
            [
                'value' => 'trans_email/ident_custom1/name',
                'label' => Mage::helper('core')->__('Custom1 Contact Name'),
            ],
            [
                'value' => 'trans_email/ident_custom1/email',
                'label' => Mage::helper('core')->__('Custom1 Contact Email'),
            ],
            [
                'value' => 'trans_email/ident_custom2/name',
                'label' => Mage::helper('core')->__('Custom2 Contact Name'),
            ],
            [
                'value' => 'trans_email/ident_custom2/email',
                'label' => Mage::helper('core')->__('Custom2 Contact Email'),
            ],
            [
                'value' => Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME,
                'label' => Mage::helper('core')->__('Store Name'),
            ],
            [
                'value' => Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
                'label' => Mage::helper('core')->__('Store Contact Telephone'),
            ],
            [
                'value' => 'general/store_information/address',
                'label' => Mage::helper('core')->__('Store Contact Address'),
            ],
        ];
    }

    /**
     * Retrieve option array of store contact variables
     *
     * @param bool $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = [];
        foreach ($this->_configVariables as $variable) {
            $optionArray[] = [
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => $variable['label'],
            ];
        }

        if ($withGroup && $optionArray) {
            $optionArray = [
                'label' => Mage::helper('core')->__('Store Contact Information'),
                'value' => $optionArray,
            ];
        }

        return $optionArray;
    }
}
