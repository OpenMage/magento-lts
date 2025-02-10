<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Config_Source_Summary
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => Mage::helper('checkout')->__('Display number of items in wishlist')],
            ['value' => 1, 'label' => Mage::helper('checkout')->__('Display item quantities')],
        ];
    }
}
