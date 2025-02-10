<?php
/**
 * Catalog search separator
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
