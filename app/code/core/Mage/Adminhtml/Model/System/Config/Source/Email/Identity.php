<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
