<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Customer Widget Form Boolean Element Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Form_Element_Boolean extends Varien_Data_Form_Element_Select
{
    /**
     * Prepare default SELECT values
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setValues([
            [
                'label' => Mage::helper('adminhtml')->__('No'),
                'value' => '0',
            ],
            [
                'label' => Mage::helper('adminhtml')->__('Yes'),
                'value' => 1,
            ],
        ]);
    }
}
