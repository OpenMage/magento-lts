<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Content Disposition Source
 *
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
