<?php
namespace PHPSTORM_META {
    /** @noinspection PhpUnusedLocalVariableInspection */
    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpLanguageLevelInspection */
    $STATIC_METHOD_TYPES = [
        \Mage::getResourceHelper('') => [
            'catalogsearch' instanceof \Mage_CatalogSearch_Model_Resource_Helper_Mysql4,
            'catalog' instanceof \Mage_Catalog_Model_Resource_Helper_Mysql4,
            'core' instanceof \Mage_Core_Model_Resource_Helper_Mysql4,
            'eav' instanceof \Mage_Eav_Model_Resource_Helper_Mysql4,
            'importexport' instanceof \Mage_ImportExport_Model_Resource_Helper_Mysql4,
            'index' instanceof \Mage_Index_Model_Resource_Helper_Mysql4,
            'reports' instanceof \Mage_Reports_Model_Resource_Helper_Mysql4,
            'sales' instanceof \Mage_Sales_Model_Resource_Helper_Mysql4,
        ], 
    ];
}