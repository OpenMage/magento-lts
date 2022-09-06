<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store Contact Information source model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Source_Email_Variables
{
    /**
     * Assoc array of configuration variables
     *
     * @var array
     */
    protected $_configVariables = [];

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_configVariables = [
            [
                'value' => Mage_Core_Model_Url::XML_PATH_UNSECURE_URL,
                'label' => Mage::helper('core')->__('Base Unsecure URL')
            ],
            [
                'value' => Mage_Core_Model_Url::XML_PATH_SECURE_URL,
                'label' => Mage::helper('core')->__('Base Secure URL')
            ],
            [
                'value' => 'trans_email/ident_general/name',
                'label' => Mage::helper('core')->__('General Contact Name')
            ],
            [
                'value' => 'trans_email/ident_general/email',
                'label' => Mage::helper('core')->__('General Contact Email')
            ],
            [
                'value' => 'trans_email/ident_sales/name',
                'label' => Mage::helper('core')->__('Sales Representative Contact Name')
            ],
            [
                'value' => 'trans_email/ident_sales/email',
                'label' => Mage::helper('core')->__('Sales Representative Contact Email')
            ],
            [
                'value' => 'trans_email/ident_custom1/name',
                'label' => Mage::helper('core')->__('Custom1 Contact Name')
            ],
            [
                'value' => 'trans_email/ident_custom1/email',
                'label' => Mage::helper('core')->__('Custom1 Contact Email')
            ],
            [
                'value' => 'trans_email/ident_custom2/name',
                'label' => Mage::helper('core')->__('Custom2 Contact Name')
            ],
            [
                'value' => 'trans_email/ident_custom2/email',
                'label' => Mage::helper('core')->__('Custom2 Contact Email')
            ],
            [
                'value' => 'general/store_information/name',
                'label' => Mage::helper('core')->__('Store Name')
            ],
            [
                'value' => 'general/store_information/phone',
                'label' => Mage::helper('core')->__('Store Contact Telephone')
            ],
            [
                'value' => 'general/store_information/address',
                'label' => Mage::helper('core')->__('Store Contact Address')
            ]
        ];
    }

    /**
     * Retrieve option array of store contact variables
     *
     * @param boolean $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = [];
        foreach ($this->_configVariables as $variable) {
            $optionArray[] = [
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => $variable['label']
            ];
        }
        if ($withGroup && $optionArray) {
            $optionArray = [
                'label' => Mage::helper('core')->__('Store Contact Information'),
                'value' => $optionArray
            ];
        }
        return $optionArray;
    }
}
