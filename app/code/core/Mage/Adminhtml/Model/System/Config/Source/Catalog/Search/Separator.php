<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog search separator
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Separator
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'OR',
                'label' => 'OR (default)',
            ], [
                'value' => 'AND',
                'label' => 'AND',
            ],
        ];
    }
}
