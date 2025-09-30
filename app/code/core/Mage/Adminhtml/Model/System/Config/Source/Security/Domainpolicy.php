<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Security_Domainpolicy
{
    /**
     * @var Mage_Adminhtml_Helper_Data
     */
    protected $_helper;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->_helper = $options['helper'] ?? Mage::helper('adminhtml');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Core_Model_Domainpolicy::FRAME_POLICY_ALLOW,
                'label' => $this->_helper->__('Enabled'),
            ],
            [
                'value' => Mage_Core_Model_Domainpolicy::FRAME_POLICY_ORIGIN,
                'label' => $this->_helper->__('Only from same domain'),
            ],
        ];
    }
}
