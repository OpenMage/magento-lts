<?php

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
                'label' => 'OR (default)'
            ], [
                'value' => 'AND',
                'label' => 'AND'
            ]
        ];
    }
}
