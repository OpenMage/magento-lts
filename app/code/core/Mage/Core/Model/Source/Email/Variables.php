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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_configVariables = array();

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_configVariables = array(
            array('value' => Mage_Core_Model_Url::XML_PATH_UNSECURE_URL, 'label' => 'Base Unsecure URL'),
            array('value' => Mage_Core_Model_Url::XML_PATH_SECURE_URL, 'label' => 'Base Secure URL'),
            array('value' => 'trans_email/ident_general/name', 'label' => 'General Contact Name'),
            array('value' => 'trans_email/ident_general/email', 'label' => 'General Contact Email'),
            array('value' => 'trans_email/ident_sales/name', 'label' => 'Sales Representative Contact Name'),
            array('value' => 'trans_email/ident_sales/email', 'label' => 'Sales Representative Contact Email'),
            array('value' => 'trans_email/ident_custom1/name', 'label' => 'Custom1 Contact Name'),
            array('value' => 'trans_email/ident_custom1/email', 'label' => 'Custom1 Contact Email'),
            array('value' => 'trans_email/ident_custom2/name', 'label' => 'Custom2 Contact Name'),
            array('value' => 'trans_email/ident_custom2/email', 'label' => 'Custom2 Contact Email'),
            array('value' => 'general/store_information/name', 'label' => 'Store Name'),
            array('value' => 'general/store_information/phone', 'label' => 'Store Contact Telephone'),
            array('value' => 'general/store_information/address', 'label' => 'Store Contact Address'));
    }

    /**
     * Retrieve option array of store contact variables
     *
     * @param boolean $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = array();
        foreach ($this->_configVariables as $variable) {
            $optionArray[] = array(
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => Mage::helper('core')->__('%s', $variable['label'])
            );
        }
        if ($withGroup && $optionArray) {
            $optionArray = array(
                'label' => Mage::helper('core')->__('Store Contact Information'),
                'value' => $optionArray
            );
        }
        return $optionArray;
    }
}
