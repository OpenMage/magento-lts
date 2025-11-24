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
class Mage_Adminhtml_Model_System_Config_Source_Email_Identity
{
    protected $_options = null;

    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = [];
            $config = Mage::getSingleton('adminhtml/config')->getSection('trans_email')->groups->children();
            foreach ($config as $node) {
                $nodeName   = $node->getName();
                $label      = (string) $node->label;
                $sortOrder  = (int) $node->sort_order;
                $this->_options[$sortOrder] = [
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $nodeName),
                    'label' => Mage::helper('adminhtml')->__($label),
                ];
            }

            ksort($this->_options);
        }

        return $this->_options;
    }
}
