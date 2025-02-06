<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Content Disposition Source
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_System_Config_Source_Contentdisposition
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'attachment',
                'label' => Mage::helper('downloadable')->__('attachment'),
            ],
            [
                'value' => 'inline',
                'label' => Mage::helper('downloadable')->__('inline'),
            ],
        ];
    }
}
