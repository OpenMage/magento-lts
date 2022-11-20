<?php
namespace PHPSTORM_META {
    override( \Mage::getResourceHelper(0),
        map( [
            'catalogsearch' => \Mage_CatalogSearch_Model_Resource_Helper_Mysql4::class,
            'catalog' => \Mage_Catalog_Model_Resource_Helper_Mysql4::class,
            'core' => \Mage_Core_Model_Resource_Helper_Mysql4::class,
            'eav' => \Mage_Eav_Model_Resource_Helper_Mysql4::class,
            'importexport' => \Mage_ImportExport_Model_Resource_Helper_Mysql4::class,
            'index' => \Mage_Index_Model_Resource_Helper_Mysql4::class,
            'reports' => \Mage_Reports_Model_Resource_Helper_Mysql4::class,
            'sales' => \Mage_Sales_Model_Resource_Helper_Mysql4::class,
        ])
    );
}