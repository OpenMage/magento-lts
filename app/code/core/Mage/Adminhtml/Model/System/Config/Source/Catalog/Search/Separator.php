<?php

class Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Separator
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $types = [
            ' OR ' => 'OR (default)',
            ' AND ' => 'AND'
        ];
        $options = [];
        foreach ($types as $k => $v) {
            $options[] = [
                'value' => $k,
                'label' => $v
            ];
        }
        return $options;
    }
}
