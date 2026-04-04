<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

use Carbon\Carbon;

/**
 * Convert profile
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Profile            _getResource()
 * @method string                                          getActionsXml()
 * @method int                                             getAdminUserId()
 * @method Mage_Dataflow_Model_Resource_Profile_Collection getCollection()
 * @method string                                          getDataTransfer()
 * @method string                                          getDirection()
 * @method string                                          getEntityType()
 * @method array|string                                    getGuiData()
 * @method string                                          getName()
 * @method Mage_Dataflow_Model_Resource_Profile            getResource()
 * @method Mage_Dataflow_Model_Resource_Profile_Collection getResourceCollection()
 * @method int                                             getStoreId()
 * @method $this                                           setActionsXml(string $value)
 * @method $this                                           setAdminUserId(int $value)
 * @method $this                                           setDataTransfer(string $value)
 * @method $this                                           setDirection(string $value)
 * @method $this                                           setEntityType(string $value)
 * @method $this                                           setGuiData(array|string $value)
 * @method $this                                           setName(string $value)
 * @method $this                                           setStoreId(int $value)
 */
class Mage_Dataflow_Model_Profile extends Mage_Core_Model_Abstract
{
    public const DEFAULT_EXPORT_PATH = 'var/export';

    public const DEFAULT_EXPORT_FILENAME = 'export_';

    /**
     * Product table permanent attributes
     *
     * @var array
     */
    protected $_productTablePermanentAttributes = ['sku'];

    /**
     * Customer table permanent attributes
     *
     * @var array
     */
    protected $_customerTablePermanentAttributes = ['email', 'website'];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/profile');
    }

    protected function _afterLoad()
    {
        $guiData = '';
        if (is_string($this->getGuiData())) {
            try {
                $guiData = Mage::helper('core/unserializeArray')
                    ->unserialize($this->getGuiData());
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
        }

        $this->setGuiData($guiData);

        return parent::_afterLoad();
    }

    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $actionsXML = $this->getData('actions_xml');
        // @phpstan-ignore-next-line because of https://github.com/phpstan/phpstan/issues/10570
        if ($actionsXML !== null && strlen($actionsXML) < 0
            && @simplexml_load_string('<data>' . $actionsXML . '</data>', null, LIBXML_NOERROR) === false
        ) {
            Mage::throwException(Mage::helper('dataflow')->__('Actions XML is not valid.'));
        }

        if (is_array($this->getGuiData())) {
            $data = $this->getData();
            $guiData = $this->getGuiData();
            $charSingleList = ['\\', '/', '.', '!', '@', '#', '$', '%', '&', '*', '~', '^'];
            if (isset($guiData['file']['type']) && $guiData['file']['type'] == 'file') {
                if (empty($guiData['file']['path'])
                    || (strlen($guiData['file']['path']) == 1
                    && in_array($guiData['file']['path'], $charSingleList))
                ) {
                    $guiData['file']['path'] = self::DEFAULT_EXPORT_PATH;
                }

                if (empty($guiData['file']['filename'])) {
                    $guiData['file']['filename'] = self::DEFAULT_EXPORT_FILENAME . $data['entity_type']
                        . '.' . ($guiData['parse']['type'] == 'csv' ? $guiData['parse']['type'] : 'xml');
                }

                //validate export available path
                $path = rtrim($guiData['file']['path'], '\\/')
                      . DS . $guiData['file']['filename'];
                /** @var Mage_Core_Model_File_Validator_AvailablePath $validator */
                $validator = Mage::getModel('core/file_validator_availablePath');
                /** @var Mage_ImportExport_Helper_Data $helperImportExport */
                $helperImportExport = Mage::helper('importexport');
                $validator->setPaths($helperImportExport->getLocalValidPaths());
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        Mage::throwException($message);
                    }
                }

                $this->setGuiData($guiData);
            }

            $this->_parseGuiData();

            $this->setGuiData(serialize($this->getGuiData()));
        }

        if ($this->_getResource()->isProfileExists($this->getName(), $this->getId())) {
            Mage::throwException(Mage::helper('dataflow')->__('Profile with the same name already exists.'));
        }

        return $this;
    }

    /**
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _afterSave()
    {
        if ($this->getGuiData() && is_string($this->getGuiData())) {
            try {
                $guiData = Mage::helper('core/unserializeArray')
                    ->unserialize($this->getGuiData());
                $this->setGuiData($guiData);
            } catch (Exception $exception) {
                Mage::logException($exception);
            }
        }

        $profileHistory = Mage::getModel('dataflow/profile_history');

        $adminUserId = $this->getAdminUserId();
        if ($adminUserId) {
            $profileHistory->setUserId($adminUserId);
        }

        $profileHistory
            ->setProfileId($this->getId())
            ->setActionCode($this->getOrigData('profile_id') ? 'update' : 'create')
            ->save();
        $csvParser = new Varien_File_Csv();
        $delimiter = trim($this->getData('gui_data/parse/delimiter') ?? '');
        if ($delimiter) {
            $csvParser->setDelimiter($delimiter);
        }

        $xmlParser = new DOMDocument();
        $newUploadedFilenames = [];

        if (isset($_FILES['file_1']['tmp_name']) || isset($_FILES['file_2']['tmp_name'])
            || isset($_FILES['file_3']['tmp_name'])
        ) {
            for ($index = 0; $index < 3; $index++) {
                if ($_FILES['file_' . ($index + 1)]['tmp_name']) {
                    $uploader = Mage::getModel('core/file_uploader', 'file_' . ($index + 1));
                    $uploader->setAllowedExtensions(['csv','xml']);
                    $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
                    $uploader->save($path);
                    $uploadFile = $uploader->getUploadedFileName();

                    if ($_FILES['file_' . ($index + 1)]['type'] == 'text/csv'
                        || $_FILES['file_' . ($index + 1)]['type'] == 'application/vnd.ms-excel'
                    ) {
                        $fileData = $csvParser->getData($path . $uploadFile);
                        $fileData = array_shift($fileData);
                    } else {
                        try {
                            $xmlParser->loadXML(file_get_contents($path . $uploadFile));
                            $cells = $this->getNode($xmlParser, 'Worksheet')->item(0);
                            $cells = $this->getNode($cells, 'Row')->item(0);
                            $cells = $this->getNode($cells, 'Cell');
                            $fileData = [];
                            foreach ($cells as $cell) {
                                $fileData[] = $this->getNode($cell, 'Data')->item(0)->nodeValue;
                            }
                        } catch (Exception) {
                            foreach ($newUploadedFilenames as $newUploadedFilename) {
                                unlink($path . $newUploadedFilename);
                            }

                            unlink($path . $uploadFile);
                            Mage::throwException(
                                Mage::helper('Dataflow')->__(
                                    'Upload failed. Wrong data format in file: %s.',
                                    $uploadFile,
                                ),
                            );
                        }
                    }

                    if ($this->_data['entity_type'] == 'customer') {
                        $attributes = $this->_customerTablePermanentAttributes;
                    } else {
                        $attributes = $this->_productTablePermanentAttributes;
                    }

                    $colsAbsent = array_diff($attributes, $fileData);
                    if ($colsAbsent) {
                        foreach ($newUploadedFilenames as $newUploadedFilename) {
                            unlink($path . $newUploadedFilename);
                        }

                        unlink($path . $uploadFile);
                        Mage::throwException(
                            Mage::helper('Dataflow')->__(
                                'Upload failed. Can not find required columns: %s in file %s.',
                                implode(', ', $colsAbsent),
                                $uploadFile,
                            ),
                        );
                    }

                    if ($uploadFile) {
                        $newFilename = 'import-' . Carbon::now()->format('YmdHis') . '-' . ($index + 1) . '_' . $uploadFile;
                        rename($path . $uploadFile, $path . $newFilename);
                        $newUploadedFilenames[] = $newFilename;
                    }
                }

                //BOM deleting for UTF files
                if (isset($path, $newFilename) && $newFilename) {
                    $contents = file_get_contents($path . $newFilename);
                    if (ord($contents[0]) == 0xEF && ord($contents[1]) == 0xBB && ord($contents[2]) == 0xBF) {
                        $contents = substr($contents, 3);
                        file_put_contents($path . $newFilename, $contents);
                    }

                    unset($contents);
                }
            }
        }

        parent::_afterSave();
        return $this;
    }

    /**
     * Run profile
     *
     * @return $this
     */
    public function run()
    {
        /**
         * Save history
         */
        Mage::getModel('dataflow/profile_history')
            ->setProfileId($this->getId())
            ->setActionCode('run')
            ->setUserId($this->getAdminUserId())
            ->save();

        /**
         * Prepare xml convert profile actions data
         */
        $xml = '<convert version="1.0"><profile name="default">' . $this->getActionsXml()
             . '</profile></convert>';
        $profile = Mage::getModel('core/convert')
            ->importXml($xml)
            ->getProfile('default');
        /** @var Mage_Dataflow_Model_Convert_Profile $profile */

        try {
            $batch = Mage::getSingleton('dataflow/batch')
                ->setProfileId($this->getId())
                ->setStoreId($this->getStoreId())
                ->save();
            $this->setBatchId($batch->getId());

            $profile->setDataflowProfile($this->getData());
            $profile->run();
        } catch (Exception $exception) {
            echo $exception;
        }

        $this->setExceptions($profile->getExceptions());
        return $this;
    }

    public function _parseGuiData()
    {
        $newLine = "\r\n";
        $import = $this->getDirection() === 'import';
        $data = $this->getGuiData();

        if ($this->getDataTransfer() === 'interactive') {
            $interactiveXml = '<action type="dataflow/convert_adapter_http" method="'
                . ($import ? 'load' : 'save') . '">' . $newLine;
            $interactiveXml .= '</action>';

            $fileXml = '';
        } else {
            $interactiveXml = '';

            $fileXml = '<action type="dataflow/convert_adapter_io" method="'
                . ($import ? 'load' : 'save') . '">' . $newLine;
            $fileXml .= '    <var name="type">' . $data['file']['type'] . '</var>' . $newLine;
            $fileXml .= '    <var name="path">' . $data['file']['path'] . '</var>' . $newLine;
            $fileXml .= '    <var name="filename"><![CDATA[' . $data['file']['filename'] . ']]></var>' . $newLine;
            if ($data['file']['type'] === 'ftp') {
                $hostArr = explode(':', $data['file']['host']);
                $fileXml .= '    <var name="host"><![CDATA[' . $hostArr[0] . ']]></var>' . $newLine;
                if (isset($hostArr[1])) {
                    $fileXml .= '    <var name="port"><![CDATA[' . $hostArr[1] . ']]></var>' . $newLine;
                }

                if (!empty($data['file']['passive'])) {
                    $fileXml .= '    <var name="passive">true</var>' . $newLine;
                }

                if ((!empty($data['file']['file_mode']))
                        && ($data['file']['file_mode'] == FTP_ASCII || $data['file']['file_mode'] == FTP_BINARY)
                ) {
                    $fileXml .= '    <var name="file_mode">' . $data['file']['file_mode'] . '</var>' . $newLine;
                }

                if (!empty($data['file']['user'])) {
                    $fileXml .= '    <var name="user"><![CDATA[' . $data['file']['user'] . ']]></var>' . $newLine;
                }

                if (!empty($data['file']['password'])) {
                    $fileXml .= '    <var name="password"><![CDATA[' . $data['file']['password'] . ']]></var>' . $newLine;
                }
            }

            if ($import) {
                $fileXml .= '    <var name="format"><![CDATA[' . $data['parse']['type'] . ']]></var>' . $newLine;
            }

            $fileXml .= '</action>' . $newLine . $newLine;
        }

        $parseFileXml = '';
        switch ($data['parse']['type']) {
            case 'excel_xml':
                $parseFileXml = '<action type="dataflow/convert_parser_xml_excel" method="'
                    . ($import ? 'parse' : 'unparse') . '">' . $newLine;
                $parseFileXml .= '    <var name="single_sheet"><![CDATA['
                    . ($data['parse']['single_sheet'])
                    . ']]></var>' . $newLine;
                break;

            case 'csv':
                $parseFileXml = '<action type="dataflow/convert_parser_csv" method="'
                    . ($import ? 'parse' : 'unparse') . '">' . $newLine;
                $parseFileXml .= '    <var name="delimiter"><![CDATA['
                    . $data['parse']['delimiter'] . ']]></var>' . $newLine;
                $parseFileXml .= '    <var name="enclose"><![CDATA['
                    . $data['parse']['enclose'] . ']]></var>' . $newLine;
                break;
        }

        $parseFileXml .= '    <var name="fieldnames">' . $data['parse']['fieldnames'] . '</var>' . $newLine;
        $parseFileXmlInter = $parseFileXml;
        $parseFileXml .= '</action>' . $newLine . $newLine;

        $mapXml = '';

        if (isset($data['map']) && is_array($data['map'])) {
            foreach ($data['map'] as $side => $fields) {
                if (!is_array($fields)) {
                    continue;
                }

                foreach ($fields['db'] as $i => $k) {
                    if ($k == '' || $k == '0') {
                        unset($data['map'][$side]['db'][$i]);
                        unset($data['map'][$side]['file'][$i]);
                    }
                }
            }
        }

        $mapXml .= '<action type="dataflow/convert_mapper_column" method="map">' . $newLine;
        $map = $data['map'][$this->getEntityType()];
        if (count($map['db'])) {
            $importFrom = $map[$import ? 'file' : 'db'];
            $importTo   = $map[$import ? 'db' : 'file'];
            $mapXml .= '    <var name="map">' . $newLine;
            $parseFileXmlInter .= '    <var name="map">' . $newLine;
            foreach ($importFrom as $i => $f) {
                $mapXml .= '        <map name="' . $f . '"><![CDATA[' . $importTo[$i] . ']]></map>' . $newLine;
                $parseFileXmlInter .= '        <map name="' . $f . '"><![CDATA[' . $importTo[$i] . ']]></map>' . $newLine;
            }

            $mapXml .= '    </var>' . $newLine;
            $parseFileXmlInter .= '    </var>' . $newLine;
        }

        if ($data['map']['only_specified']) {
            $mapXml .= '    <var name="_only_specified">' . $data['map']['only_specified'] . '</var>' . $newLine;
            //$mapXml .= '    <var name="map">' . $newLine;
            $parseFileXmlInter .= '    <var name="_only_specified">' . $data['map']['only_specified'] . '</var>' . $newLine;
        }

        $mapXml .= '</action>' . $newLine . $newLine;

        $parsers = [
            'product' => 'catalog/convert_parser_product',
            'customer' => 'customer/convert_parser_customer',
        ];

        if ($import) {
            $parseFileXmlInter .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $newLine;
        } else {
            $parseDataXml = '<action type="' . $parsers[$this->getEntityType()] . '" method="unparse">' . $newLine;
            $parseDataXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $newLine;
            if (isset($data['export']['add_url_field'])) {
                $parseDataXml .= '    <var name="url_field"><![CDATA['
                    . $data['export']['add_url_field'] . ']]></var>' . $newLine;
            }

            $parseDataXml .= '</action>' . $newLine . $newLine;
        }

        $adapters = [
            'product' => 'catalog/convert_adapter_product',
            'customer' => 'customer/convert_adapter_customer',
        ];

        if ($import) {
            $entityXml = '<action type="' . $adapters[$this->getEntityType()] . '" method="save">' . $newLine;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $newLine;
            $entityXml .= '</action>' . $newLine . $newLine;
        } else {
            $entityXml = '<action type="' . $adapters[$this->getEntityType()] . '" method="load">' . $newLine;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $newLine;
            foreach ($data[$this->getEntityType()]['filter'] as $filter => $value) {
                if (empty($value)) {
                    continue;
                }

                if (is_scalar($value)) {
                    $entityXml .= '    <var name="filter/' . $filter . '"><![CDATA[' . $value . ']]></var>' . $newLine;
                    $parseFileXmlInter .= '    <var name="filter/' . $filter . '"><![CDATA[' . $value . ']]></var>' . $newLine;
                } elseif (is_array($value)) {
                    foreach ($value as $a => $b) {
                        if (strlen($b) == 0) {
                            continue;
                        }

                        $entityXml .= '    <var name="filter/' . $filter . '/' . $a
                            . '"><![CDATA[' . $b . ']]></var>' . $newLine;
                        $parseFileXmlInter .= '    <var name="filter/' . $filter . '/'
                            . $a . '"><![CDATA[' . $b . ']]></var>' . $newLine;
                    }
                }
            }

            $entityXml .= '</action>' . $newLine . $newLine;
        }

        // Need to rewrite the whole xml action format
        if ($import) {
            $numberOfRecords = $data['import']['number_of_records'] ?? 1;
            $decimalSeparator = $data['import']['decimal_separator'] ?? ' . ';
            $parseFileXmlInter .= '    <var name="number_of_records">'
                . $numberOfRecords . '</var>' . $newLine;
            $parseFileXmlInter .= '    <var name="decimal_separator"><![CDATA['
                . $decimalSeparator . ']]></var>' . $newLine;
            if ($this->getDataTransfer() === 'interactive') {
                $xml = $parseFileXmlInter;
                $xml .= '    <var name="adapter">' . $adapters[$this->getEntityType()] . '</var>' . $newLine;
                $xml .= '    <var name="method">parse</var>' . $newLine;
                $xml .= '</action>';
            } else {
                $xml = $fileXml;
                $xml .= $parseFileXmlInter;
                $xml .= '    <var name="adapter">' . $adapters[$this->getEntityType()] . '</var>' . $newLine;
                $xml .= '    <var name="method">parse</var>' . $newLine;
                $xml .= '</action>';
            }
        } else {
            $xml = $entityXml . $parseDataXml . $mapXml . $parseFileXml . $fileXml . $interactiveXml;
        }

        $this->setGuiData($data);
        $this->setActionsXml($xml);

        return $this;
    }

    /**
     * Get node from xml object
     *
     * @param  object    $xmlObject
     * @param  string    $nodeName
     * @return object
     * @throws Exception
     */
    protected function getNode($xmlObject, $nodeName)
    {
        if ($xmlObject != null) {
            return $xmlObject->getElementsByTagName($nodeName);
        }

        Mage::throwException(Mage::helper('Dataflow')->__('Invalid node.'));
    }
}
