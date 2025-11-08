<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import extends Mage_ImportExport_Model_Abstract
{
    /**
     * Key in config with entities.
     */
    public const CONFIG_KEY_ENTITIES  = 'global/importexport/import_entities';

    /**
     * Import behavior.
     */
    public const BEHAVIOR_APPEND  = 'append';

    public const BEHAVIOR_REPLACE = 'replace';

    public const BEHAVIOR_DELETE  = 'delete';

    /**
     * Form field names (and IDs)
     */
    public const FIELD_NAME_SOURCE_FILE = 'import_file';

    public const FIELD_NAME_IMG_ARCHIVE_FILE = 'import_image_archive';

    /**
     * Import constants
     */
    public const DEFAULT_SIZE      = 50;

    public const MAX_IMPORT_CHUNKS = 4;

    /**
     * Entity adapter.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Entity invalidated indexes.
     *
     * @var array<string, array<int, string>>
     */
    protected static $_entityInvalidatedIndexes = [
        'catalog_product' => [
            'catalog_product_price',
            'catalog_category_product',
            'catalogsearch_fulltext',
            'catalog_product_flat',
        ],
    ];

    /**
     * Create instance of entity adapter and returns it.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $validTypes = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($validTypes[$this->getEntity()])) {
                try {
                    /** @var Mage_ImportExport_Model_Import_Entity_Abstract $_entityAdapter */
                    $_entityAdapter = Mage::getModel($validTypes[$this->getEntity()]['model']);
                    $this->_entityAdapter = $_entityAdapter;
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('importexport')->__('Invalid entity model'),
                    );
                }

                if (!($this->_entityAdapter instanceof Mage_ImportExport_Model_Import_Entity_Abstract)) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Entity adapter object must be an instance of Mage_ImportExport_Model_Import_Entity_Abstract'),
                    );
                }
            } else {
                Mage::throwException(Mage::helper('importexport')->__('Invalid entity'));
            }

            // check for entity codes integrity
            if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                Mage::throwException(
                    Mage::helper('importexport')->__('Input entity code is not equal to entity adapter code'),
                );
            }

            $this->_entityAdapter->setParameters($this->getData());
        }

        return $this->_entityAdapter;
    }

    /**
     * Returns source adapter object.
     *
     * @param string $sourceFile Full path to source file
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected function _getSourceAdapter($sourceFile)
    {
        return Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile);
    }

    /**
     * Return operation result messages
     *
     * @param bool $validationResult
     * @return array
     */
    public function getOperationResultMessages($validationResult)
    {
        $messages = [];
        if ($this->getProcessedRowsCount()) {
            if (!$validationResult) {
                if ($this->getProcessedRowsCount() == $this->getInvalidRowsCount()) {
                    $messages[] = Mage::helper('importexport')->__('File is totally invalid. Please fix errors and re-upload file');
                } elseif ($this->getErrorsCount() >= $this->getErrorsLimit()) {
                    $messages[] = Mage::helper('importexport')->__('Errors limit (%d) reached. Please fix errors and re-upload file', $this->getErrorsLimit());
                } elseif ($this->isImportAllowed()) {
                    $messages[] = Mage::helper('importexport')->__('Please fix errors and re-upload file');
                } else {
                    $messages[] = Mage::helper('importexport')->__('File is partially valid, but import is not possible');
                }

                // errors info
                foreach ($this->getErrors() as $errorCode => $rows) {
                    $error = $errorCode . ' '
                        . Mage::helper('importexport')->__('in rows') . ': '
                        . implode(', ', $rows);
                    $messages[] = $error;
                }
            } elseif ($this->isImportAllowed()) {
                $messages[] = Mage::helper('importexport')->__('Validation finished successfully');
            } else {
                $messages[] = Mage::helper('importexport')->__('File is valid, but import is not possible');
            }

            $notices = $this->getNotices();
            if (is_array($notices)) {
                $messages = array_merge($messages, $notices);
            }

            $messages[] = Mage::helper('importexport')->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d', $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(), $this->getInvalidRowsCount(), $this->getErrorsCount());
        } else {
            $messages[] = Mage::helper('importexport')->__('File does not contain data.');
        }

        return $messages;
    }

    /**
     * Get attribute type for upcoming validation.
     *
     * @return string
     */
    public static function getAttributeType(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->usesSource()) {
            return $attribute->getFrontendInput() == 'multiselect'
                ? 'multiselect' : 'select';
        } elseif ($attribute->isStatic()) {
            return $attribute->getFrontendInput() == 'date' ? 'datetime' : 'varchar';
        } else {
            return $attribute->getBackendType();
        }
    }

    /**
     * DB data source model getter.
     *
     * @static
     * @return Mage_ImportExport_Model_Resource_Import_Data
     */
    public static function getDataSourceModel()
    {
        return Mage::getResourceSingleton('importexport/import_data');
    }

    /**
     * Default import behavior getter.
     *
     * @static
     * @return string
     */
    public static function getDefaultBehavior()
    {
        return self::BEHAVIOR_APPEND;
    }

    /**
     * Override standard entity getter.
     *
     * @throw Mage_Core_Exception
     * @return string
     */
    public function getEntity()
    {
        if (empty($this->_data['entity'])) {
            Mage::throwException(Mage::helper('importexport')->__('Entity is unknown'));
        }

        return $this->_data['entity'];
    }

    /**
     * Get entity adapter errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getEntityAdapter()->getErrorMessages();
    }

    /**
     * Returns error counter.
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_getEntityAdapter()->getErrorsCount();
    }

    /**
     * Returns error limit value.
     *
     * @return int
     */
    public function getErrorsLimit()
    {
        return $this->_getEntityAdapter()->getErrorsLimit();
    }

    /**
     * Returns invalid rows count.
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return $this->_getEntityAdapter()->getInvalidRowsCount();
    }

    /**
     * Returns entity model noticees.
     *
     * @return array
     */
    public function getNotices()
    {
        return $this->_getEntityAdapter()->getNotices();
    }

    /**
     * Returns number of checked entities.
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_getEntityAdapter()->getProcessedEntitiesCount();
    }

    /**
     * Returns number of checked rows.
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_getEntityAdapter()->getProcessedRowsCount();
    }

    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    public static function getWorkingDir()
    {
        return Mage::getBaseDir('var') . DS . 'importexport' . DS;
    }

    /**
     * Import source file structure to DB.
     *
     * @return bool
     */
    public function importSource()
    {
        $this->setData([
            'entity'   => self::getDataSourceModel()->getEntityTypeCode(),
            'behavior' => self::getDataSourceModel()->getBehavior(),
        ]);
        $this->addLogComment(Mage::helper('importexport')->__('Begin import of "%s" with "%s" behavior', $this->getEntity(), $this->getBehavior()));
        $result = $this->_getEntityAdapter()->importData();
        $this->addLogComment([
            Mage::helper('importexport')->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d', $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(), $this->getInvalidRowsCount(), $this->getErrorsCount()),
            Mage::helper('importexport')->__('Import has been done successfuly.'),
        ]);
        return $result;
    }

    /**
     * Import possibility getter.
     *
     * @return bool
     */
    public function isImportAllowed()
    {
        return $this->_getEntityAdapter()->isImportAllowed();
    }

    /**
     * Import source file structure to DB.
     */
    public function expandSource()
    {
        $writer  = Mage::getModel('importexport/export_adapter_csv', self::getWorkingDir() . 'big0.csv');
        $regExps = ['last' => '/(.*?)(\d+)$/', 'middle' => '/(.*?)(\d+)(.*)$/'];
        $colReg  = [
            'sku' => 'last', 'name' => 'last', 'description' => 'last', 'short_description' => 'last',
            'url_key' => 'middle', 'meta_title' => 'last', 'meta_keyword' => 'last', 'meta_description' => 'last',
            '_links_related_sku' => 'last', '_links_crosssell_sku' => 'last', '_links_upsell_sku' => 'last',
            '_custom_option_sku' => 'middle', '_custom_option_row_sku' => 'middle', '_super_products_sku' => 'last',
            '_associated_sku' => 'last',
        ];
        $size = self::DEFAULT_SIZE;

        $filename = 'catalog_product.csv';
        $filenameFormat = 'big%s.csv';
        foreach ($this->_getSourceAdapter(self::getWorkingDir() . $filename) as $row) {
            $writer->writeRow($row);
        }

        $count = self::MAX_IMPORT_CHUNKS;
        for ($i = 1; $i < $count; $i++) {
            $writer = Mage::getModel(
                'importexport/export_adapter_csv',
                self::getWorkingDir() . sprintf($filenameFormat, $i),
            );

            $adapter = $this->_getSourceAdapter(self::getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                $writer->writeRow($row);
            }

            $adapter = $this->_getSourceAdapter(self::getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                foreach ($colReg as $colName => $regExpType) {
                    if (!empty($row[$colName])) {
                        preg_match($regExps[$regExpType], $row[$colName], $m);

                        $row[$colName] = $m[1] . ((int) $m[2] + $size) . ($regExpType == 'middle' ? $m[3] : '');
                    }
                }

                $writer->writeRow($row);
            }

            $size *= 2;
        }
    }

    /**
     * Move uploaded file and create source adapter instance.
     *
     * @return string Source file path
     * @throws Mage_Core_Exception
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function uploadSource()
    {
        $entity    = $this->getEntity();
        if (!array_key_exists($entity, Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES))) {
            Mage::throwException(Mage::helper('importexport')->__('Incorrect entity type'));
        }

        $uploader  = Mage::getModel('core/file_uploader', self::FIELD_NAME_SOURCE_FILE);
        $uploader->skipDbProcessing(true);

        $result    = $uploader->save(self::getWorkingDir());
        $extension = pathinfo($result['file'], PATHINFO_EXTENSION);

        $uploadedFile = $result['path'] . $result['file'];
        if (!$extension) {
            unlink($uploadedFile);
            Mage::throwException(Mage::helper('importexport')->__('Uploaded file has no extension'));
        }

        $sourceFile = self::getWorkingDir() . $entity;

        $sourceFile .= '.' . $extension;

        if (strtolower($uploadedFile) != strtolower($sourceFile)) {
            if (file_exists($sourceFile)) {
                unlink($sourceFile);
            }

            if (!@rename($uploadedFile, $sourceFile)) {
                Mage::throwException(Mage::helper('importexport')->__('Source file moving failed'));
            }
        }

        // trying to create source adapter for file and catch possible exception to be convinced in its adequacy
        try {
            $this->_getSourceAdapter($sourceFile);
        } catch (Exception $exception) {
            unlink($sourceFile);
            Mage::throwException($exception->getMessage());
        }

        return $sourceFile;
    }

    /**
     * Validates source file and returns validation result.
     *
     * @param string $sourceFile Full path to source file
     * @return bool
     */
    public function validateSource($sourceFile)
    {
        $this->addLogComment(Mage::helper('importexport')->__('Begin data validation'));
        $result = $this->_getEntityAdapter()
            ->setSource($this->_getSourceAdapter($sourceFile))
            ->isDataValid();

        $messages = $this->getOperationResultMessages($result);
        $this->addLogComment($messages);
        if ($result) {
            $this->addLogComment(Mage::helper('importexport')->__('Done import data validation'));
        }

        return $result;
    }

    /**
     * Invalidate indexes by process codes.
     *
     * @return $this
     */
    public function invalidateIndex()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }

        return $this;
    }
}
