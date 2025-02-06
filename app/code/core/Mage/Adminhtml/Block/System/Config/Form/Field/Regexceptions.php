<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system config array field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Regexceptions extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('regexp', [
            'label' => Mage::helper('adminhtml')->__('Matched Expression'),
            'style' => 'width:120px',
        ]);
        $this->addColumn('value', [
            'label' => Mage::helper('adminhtml')->__('Value'),
            'style' => 'width:120px',
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Exception');
        parent::__construct();
    }
}
