<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export extends Mage_ImportExport_Model_Abstract
{
    const FILTER_ELEMENT_GROUP = 'export_filter';
    const FILTER_ELEMENT_SKIP  = 'skip_attr';

    /**
     * Filter fields types.
     */
    const FILTER_TYPE_SELECT = 'select';
    const FILTER_TYPE_INPUT  = 'input';
    const FILTER_TYPE_DATE   = 'date';
    const FILTER_TYPE_NUMBER = 'number';

    /**
     * Config keys.
     */
    const CONFIG_KEY_ENTITIES = 'global/importexport/export_entities';
    const CONFIG_KEY_FORMATS  = 'global/importexport/export_file_formats';

    /**
     * Entity adapter.
     *
     * @var Mage_ImportExport_Model_Export_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Writer object instance.
     *
     * @var Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * Create instance of entity adapter and returns it.
     *
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Entity_Abstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $validTypes = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($validTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = Mage::getModel($validTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('importexport')->__('Invalid entity model')
                    );
                }
                if (! $this->_entityAdapter instanceof Mage_ImportExport_Model_Export_Entity_Abstract) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Entity adapter obejct must be an instance of Mage_ImportExport_Model_Export_Entity_Abstract')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('importexport')->__('Invalid entity'));
            }
            // check for entity codes integrity
            if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                Mage::throwException(
                    Mage::helper('importexport')->__('Input entity code is not equal to entity adapter code')
                );
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    /**
     * Get writer object.
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $validWriters = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_FORMATS);

            if (isset($validWriters[$this->getFileFormat()])) {
                try {
                    $this->_writer = Mage::getModel($validWriters[$this->getFileFormat()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('importexport')->__('Invalid entity model')
                    );
                }
                if (! $this->_writer instanceof Mage_ImportExport_Model_Export_Adapter_Abstract) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Adapter object must be an instance of %s', 'Mage_ImportExport_Model_Export_Adapter_Abstract')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('importexport')->__('Invalid file format'));
            }
        }
        return $this->_writer;
    }

    /**
     * Export data and return contents of temporary file.
     *
     * @deprecated after ver 1.9.2.4 use $this->exportFile() instead
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function export()
    {
        if (isset($this->_data[self::FILTER_ELEMENT_GROUP])) {
            $this->addLogComment(Mage::helper('importexport')->__('Begin export of %s', $this->getEntity()));
            $result = $this->_getEntityAdapter()
                ->setWriter($this->_getWriter())
                ->export();
            $countRows = substr_count(trim($result), "\n");
            if (!$countRows) {
                Mage::throwException(
                    Mage::helper('importexport')->__('There is no data for export')
                );
            }
            if ($result) {
                $this->addLogComment(array(
                    Mage::helper('importexport')->__('Exported %s rows.', $countRows),
                    Mage::helper('importexport')->__('Export has been done.')
                ));
            }
            return $result;
        } else {
            Mage::throwException(
                Mage::helper('importexport')->__('No filter data provided')
            );
        }
    }

    /**
     * Export data and return temporary file through array.
     *
     * This method will return following array:
     *
     * array(
     *     'rows'  => count of written rows,
     *     'value' => path to created file,
     *     'type'  => 'file'
     * )
     *
     * @throws Mage_Core_Exception
     * @return array
     */
    public function exportFile()
    {
        if (isset($this->_data[self::FILTER_ELEMENT_GROUP])) {
            $this->addLogComment(Mage::helper('importexport')->__('Begin export of %s', $this->getEntity()));
            $result = $this->_getEntityAdapter()
                ->setWriter($this->_getWriter())
                ->exportFile();

            if (isset($result['rows'])) {
                if (!$result['rows']) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('There is no data for export')
                    );
                }
                if ($result['rows']) {
                    $this->addLogComment(array(
                        Mage::helper('importexport')->__('Exported %s rows.', $result['rows']),
                        Mage::helper('importexport')->__('Export has been done.')
                    ));
                }
            }

            return $result;
        } else {
            Mage::throwException(
                Mage::helper('importexport')->__('No filter data provided')
            );
        }
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function filterAttributeCollection(Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection)
    {
        return $this->_getEntityAdapter()->filterAttributeCollection($collection);
    }

    /**
     * Determine filter type for specified attribute.
     *
     * @static
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @throws Exception
     * @return string
     */
    public static function getAttributeFilterType(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->usesSource() || $attribute->getFilterOptions()) {
            return self::FILTER_TYPE_SELECT;
        } elseif ('datetime' == $attribute->getBackendType()) {
            return self::FILTER_TYPE_DATE;
        } elseif ('decimal' == $attribute->getBackendType() || 'int' == $attribute->getBackendType()) {
            return self::FILTER_TYPE_NUMBER;
        } elseif ($attribute->isStatic()
                  || 'varchar' == $attribute->getBackendType()
                  || 'text' == $attribute->getBackendType()
        ) {
            return self::FILTER_TYPE_INPUT;
        } else {
            Mage::throwException(
                Mage::helper('importexport')->__('Can not determine attribute filter type')
            );
        }
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_getWriter()->getContentType();
    }

    /**
     * Override standard entity getter.
     *
     * @throw Exception
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
     * Entity attributes collection getter.
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getEntityAttributeCollection()
    {
        return $this->_getEntityAdapter()->getAttributeCollection();
    }

    /**
     * Override standard entity getter.
     *
     * @throw Exception
     * @return string
     */
    public function getFileFormat()
    {
        if (empty($this->_data['file_format'])) {
            Mage::throwException(Mage::helper('importexport')->__('File format is unknown'));
        }
        return $this->_data['file_format'];
    }

    /**
     * Return file name for downloading.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getEntity() . '_' . date('Ymd_His') .  '.' . $this->_getWriter()->getFileExtension();
    }
}
