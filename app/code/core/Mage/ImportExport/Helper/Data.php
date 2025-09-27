<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * ImportExport data helper
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * XML path for config data
     */
    public const XML_PATH_EXPORT_LOCAL_VALID_PATH       = 'general/file/importexport_local_valid_paths';
    public const XML_PATH_BUNCH_SIZE                    = 'general/file/bunch_size';
    public const XML_PATH_IMPORT_CONFIGURABLE_PAGE_SIZE = 'system/import_csv/configurable_page_size';

    protected $_moduleName = 'Mage_ImportExport';

    /**
     * Maximum size of uploaded files.
     *
     * @return int
     */
    public function getMaxUploadSize()
    {
        $postMaxSizeBytes = ini_parse_quantity(ini_get('post_max_size'));
        $uploadMaxSizeBytes = ini_parse_quantity(ini_get('upload_max_filesize'));
        return min($postMaxSizeBytes, $uploadMaxSizeBytes);
    }

    /**
     * Get valid path masks to files for importing/exporting
     *
     * @return array
     */
    public function getLocalValidPaths()
    {
        return Mage::getStoreConfig(self::XML_PATH_EXPORT_LOCAL_VALID_PATH);
    }

    /**
     * Retrieve size of bunch (how much products should be involved in one import iteration)
     *
     * @return int
     */
    public function getBunchSize()
    {
        return Mage::getStoreConfigAsInt(self::XML_PATH_BUNCH_SIZE);
    }

    /**
     * Get page size for import configurable products
     *
     * @return int
     */
    public function getImportConfigurablePageSize()
    {
        return Mage::getStoreConfigAsInt(self::XML_PATH_IMPORT_CONFIGURABLE_PAGE_SIZE);
    }
}
