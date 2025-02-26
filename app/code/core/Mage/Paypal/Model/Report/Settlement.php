<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Settlement Report model
 *
 * Perform fetching reports from remote servers with following saving them to database
 * Prepare report rows for Mage_Paypal_Model_Report_Settlement_Row model
 *
 * @category   Mage
 * @package    Mage_Paypal
 *
 * @method Mage_Paypal_Model_Resource_Report_Settlement _getResource()
 * @method Mage_Paypal_Model_Resource_Report_Settlement getResource()
 * @method string getReportDate()
 * @method $this setReportDate(string $value)
 * @method string getAccountId()
 * @method $this setAccountId(string $value)
 * @method string getFilename()
 * @method $this setFilename(string $value)
 * @method string getLastModified()
 * @method $this setLastModified(string $value)
 */
class Mage_Paypal_Model_Report_Settlement extends Mage_Core_Model_Abstract
{
    /**
     * Default PayPal SFTP host
     * @var string
     */
    public const REPORTS_HOSTNAME = 'reports.paypal.com';

    /**
     * Default PayPal SFTP host for sandbox mode
     * @var string
     */
    public const SANDBOX_REPORTS_HOSTNAME = 'reports.sandbox.paypal.com';

    /**
     * PayPal SFTP path
     * @var string
     */
    public const REPORTS_PATH = '/ppreports/outgoing';

    /**
     * Original charset of old report files
     * @var string
     */
    public const FILES_IN_CHARSET = 'UTF-16';

    /**
     * Target charset of report files to be parsed
     * @var string
     */
    public const FILES_OUT_CHARSET = 'UTF-8';

    /**
     * Reports rows storage
     * @var array
     */
    protected $_rows = [];

    protected $_csvColumns = [
        'old' => [
            'section_columns' => [
                '' => 0,
                'TransactionID' => 1,
                'InvoiceID' => 2,
                'PayPalReferenceID' => 3,
                'PayPalReferenceIDType' => 4,
                'TransactionEventCode' => 5,
                'TransactionInitiationDate' => 6,
                'TransactionCompletionDate' => 7,
                'TransactionDebitOrCredit' => 8,
                'GrossTransactionAmount' => 9,
                'GrossTransactionCurrency' => 10,
                'FeeDebitOrCredit' => 11,
                'FeeAmount' => 12,
                'FeeCurrency' => 13,
                'CustomField' => 14,
                'ConsumerID' => 15,
            ],
            'rowmap' => [
                'TransactionID' => 'transaction_id',
                'InvoiceID' => 'invoice_id',
                'PayPalReferenceID' => 'paypal_reference_id',
                'PayPalReferenceIDType' => 'paypal_reference_id_type',
                'TransactionEventCode' => 'transaction_event_code',
                'TransactionInitiationDate' => 'transaction_initiation_date',
                'TransactionCompletionDate' => 'transaction_completion_date',
                'TransactionDebitOrCredit' => 'transaction_debit_or_credit',
                'GrossTransactionAmount' => 'gross_transaction_amount',
                'GrossTransactionCurrency' => 'gross_transaction_currency',
                'FeeDebitOrCredit' => 'fee_debit_or_credit',
                'FeeAmount' => 'fee_amount',
                'FeeCurrency' => 'fee_currency',
                'CustomField' => 'custom_field',
                'ConsumerID' => 'consumer_id',
            ],
        ],
        'new' => [
            'section_columns' => [
                '' => 0,
                'Transaction ID' => 1,
                'Invoice ID' => 2,
                'PayPal Reference ID' => 3,
                'PayPal Reference ID Type' => 4,
                'Transaction Event Code' => 5,
                'Transaction Initiation Date' => 6,
                'Transaction Completion Date' => 7,
                'Transaction Debit or Credit' => 8,
                'Gross Transaction Amount' => 9,
                'Gross Transaction Currency' => 10,
                'Fee Debit or Credit' => 11,
                'Fee Amount' => 12,
                'Fee Currency' => 13,
                'Custom Field' => 14,
                'Consumer ID' => 15,
                'Payment Tracking ID' => 16,
                'Store ID' => 17,
            ],
            'rowmap' => [
                'Transaction ID' => 'transaction_id',
                'Invoice ID' => 'invoice_id',
                'PayPal Reference ID' => 'paypal_reference_id',
                'PayPal Reference ID Type' => 'paypal_reference_id_type',
                'Transaction Event Code' => 'transaction_event_code',
                'Transaction Initiation Date' => 'transaction_initiation_date',
                'Transaction Completion Date' => 'transaction_completion_date',
                'Transaction Debit or Credit' => 'transaction_debit_or_credit',
                'Gross Transaction Amount' => 'gross_transaction_amount',
                'Gross Transaction Currency' => 'gross_transaction_currency',
                'Fee Debit or Credit' => 'fee_debit_or_credit',
                'Fee Amount' => 'fee_amount',
                'Fee Currency' => 'fee_currency',
                'Custom Field' => 'custom_field',
                'Consumer ID' => 'consumer_id',
                'Payment Tracking ID' => 'payment_tracking_id',
                'Store ID' => 'store_id',
            ],
        ],
    ];

    protected function _construct()
    {
        $this->_init('paypal/report_settlement');
    }

    /**
     * Stop saving process if file with same report date, account ID and last modified date was already ferched
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->_dataSaveAllowed = true;
        if ($this->getId()) {
            if ($this->getLastModified() == $this->getReportLastModified()) {
                $this->_dataSaveAllowed = false;
            }
        }
        $this->setLastModified($this->getReportLastModified());
        return parent::_beforeSave();
    }

    /**
     * Goes to specified host/path and fetches reports from there.
     * Save reports to database.
     *
     * @param array $config SFTP credentials
     * @return int Number of report rows that were fetched and saved successfully
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function fetchAndSave($config)
    {
        $connection = new Varien_Io_Sftp();
        $connection->open([
            'host'     => $config['hostname'],
            'username' => $config['username'],
            'password' => $config['password'],
        ]);
        $connection->cd($config['path']);
        $fetched = 0;
        $listing = $this->_filterReportsList($connection->rawls());
        foreach ($listing as $filename => $attributes) {
            $localCsv = tempnam(Mage::getConfig()->getOptions()->getTmpDir(), 'PayPal_STL');
            if ($connection->read($filename, $localCsv)) {
                if (!is_writable($localCsv)) {
                    Mage::throwException(Mage::helper('paypal')->__('Cannot create target file for reading reports.'));
                }

                $encoded = file_get_contents($localCsv);
                $csvFormat = 'new';

                $fileEncoding = mb_detect_encoding($encoded);

                if (self::FILES_OUT_CHARSET != $fileEncoding) {
                    $decoded = @iconv($fileEncoding, self::FILES_OUT_CHARSET . '//IGNORE', $encoded);
                    file_put_contents($localCsv, $decoded);
                    $csvFormat = 'old';
                }

                // Set last modified date, this value will be overwritten during parsing
                if (isset($attributes['mtime'])) {
                    $lastModified = new Zend_Date($attributes['mtime']);
                    $this->setReportLastModified($lastModified->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                }

                $this->setReportDate($this->_fileNameToDate($filename))
                    ->setFilename($filename)
                    ->parseCsv($localCsv, $csvFormat);

                if ($this->getAccountId()) {
                    $this->save();
                }

                if ($this->_dataSaveAllowed) {
                    $fetched += count($this->_rows);
                }
                // clean object and remove parsed file
                $this->unsetData();
                unlink($localCsv);
            }
        }
        return $fetched;
    }

    /**
     * Parse CSV file and collect report rows
     *
     * @param string $localCsv Path to CSV file
     * @param string $format CSV format(column names)
     * @return $this
     */
    public function parseCsv($localCsv, $format = 'new')
    {
        $this->_rows = [];

        $sectionColumns = $this->_csvColumns[$format]['section_columns'];
        $rowMap = $this->_csvColumns[$format]['rowmap'];

        $flippedSectionColumns = array_flip($sectionColumns);
        $fp = fopen($localCsv, 'r');
        while ($line = fgetcsv($fp)) {
            if (empty($line)) { // The line was empty, so skip it.
                continue;
            }
            $lineType = $line[0];
            switch ($lineType) {
                case 'RH': // Report header.
                    $lastModified = new Zend_Date($line[1]);
                    $this->setReportLastModified($lastModified->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                    //$this->setAccountId($columns[2]); -- probably we'll just take that from the section header...
                    break;
                case 'FH': // File header.
                    // Nothing interesting here, move along
                    break;
                case 'SH': // Section header.
                    $this->setAccountId($line[3]);
                    $this->loadByAccountAndDate();
                    break;
                case 'CH': // Section columns.
                    // In case ever the column order is changed, we will have the items recorded properly
                    // anyway. We have named, not numbered columns.
                    for ($i = 1; $i < count($line); $i++) {
                        $sectionColumns[$line[$i]] = $i;
                    }
                    $flippedSectionColumns = array_flip($sectionColumns);
                    break;
                case 'SB': // Section body.
                    $bodyItem = [];
                    for ($i = 1; $i < count($line); $i++) {
                        $bodyItem[$rowMap[$flippedSectionColumns[$i]]] = $line[$i];
                    }
                    $this->_rows[] = $bodyItem;
                    break;
                case 'SC': // Section records count.
                case 'RC': // Report records count.
                case 'SF': // Section footer.
                case 'FF': // File footer.
                case 'RF': // Report footer.
                    // Nothing to see here, move along
                    break;
            }
        }
        return $this;
    }

    /**
     * Load report by unique key (account + report date)
     *
     * @return $this
     */
    public function loadByAccountAndDate()
    {
        $this->getResource()->loadByAccountAndDate($this, $this->getAccountId(), $this->getReportDate());
        return $this;
    }

    /**
     * Return collected rows for further processing.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Return name for row column
     *
     * @param string $field Field name in row model
     * @return string
     */
    public function getFieldLabel($field)
    {
        switch ($field) {
            case 'report_date':
                return Mage::helper('paypal')->__('Report Date');
            case 'account_id':
                return Mage::helper('paypal')->__('Merchant Account');
            case 'transaction_id':
                return Mage::helper('paypal')->__('Transaction ID');
            case 'invoice_id':
                return Mage::helper('paypal')->__('Invoice ID');
            case 'paypal_reference_id':
                return Mage::helper('paypal')->__('PayPal Reference ID');
            case 'paypal_reference_id_type':
                return Mage::helper('paypal')->__('PayPal Reference ID Type');
            case 'transaction_event_code':
                return Mage::helper('paypal')->__('Event Code');
            case 'transaction_event':
                return Mage::helper('paypal')->__('Event');
            case 'transaction_initiation_date':
                return Mage::helper('paypal')->__('Initiation Date');
            case 'transaction_completion_date':
                return Mage::helper('paypal')->__('Completion Date');
            case 'transaction_debit_or_credit':
                return Mage::helper('paypal')->__('Debit or Credit');
            case 'gross_transaction_amount':
                return Mage::helper('paypal')->__('Gross Amount');
            case 'fee_debit_or_credit':
                return Mage::helper('paypal')->__('Fee Debit or Credit');
            case 'fee_amount':
                return Mage::helper('paypal')->__('Fee Amount');
            case 'custom_field':
                return Mage::helper('paypal')->__('Custom');
            default:
                return $field;
        }
    }

    /**
     * Iterate through website configurations and collect all SFTP configurations
     * Filter config values if necessary
     *
     * @param bool $automaticMode Whether to skip settings with disabled Automatic Fetching or not
     * @return array
     */
    public function getSftpCredentials($automaticMode = false)
    {
        $configs = [];
        $uniques = [];
        foreach (Mage::app()->getStores() as $store) {
            /*@var $store Mage_Core_Model_Store */
            $active = (bool) $store->getConfig('paypal/fetch_reports/active');
            if (!$active && $automaticMode) {
                continue;
            }
            $cfg = [
                'hostname'  => $store->getConfig('paypal/fetch_reports/ftp_ip'),
                'path'      => $store->getConfig('paypal/fetch_reports/ftp_path'),
                'username'  => $store->getConfig('paypal/fetch_reports/ftp_login'),
                'password'  => $store->getConfig('paypal/fetch_reports/ftp_password'),
                'sandbox'   => $store->getConfig('paypal/fetch_reports/ftp_sandbox'),
            ];
            if (empty($cfg['username']) || empty($cfg['password'])) {
                continue;
            }
            if (empty($cfg['hostname']) || $cfg['sandbox']) {
                $cfg['hostname'] = $cfg['sandbox'] ? self::SANDBOX_REPORTS_HOSTNAME : self::REPORTS_HOSTNAME;
            }
            if (empty($cfg['path']) || $cfg['sandbox']) {
                $cfg['path'] = self::REPORTS_PATH;
            }
            // avoid duplicates
            if (in_array(serialize($cfg), $uniques)) {
                continue;
            }
            $uniques[] = serialize($cfg);
            $configs[] = $cfg;
        }
        return $configs;
    }

    /**
     * Converts a filename to date of report.
     *
     * @param string $filename
     * @return string
     */
    protected function _fileNameToDate($filename)
    {
        // Currently filenames look like STL-YYYYMMDD, so that is what we care about.
        $dateSnippet = substr(basename($filename), 4, 8);
        return substr($dateSnippet, 0, 4) . '-' . substr($dateSnippet, 4, 2) . '-' . substr($dateSnippet, 6, 2);
    }

    /**
     * Filter SFTP file list by filename format
     *
     * @param array $list List of files as per $connection->rawls()
     * @return array Trimmed down list of files
     */
    protected function _filterReportsList($list)
    {
        $result = [];
        $pattern = '/^STL-(\d{8,8})\.(\d{2,2})\.(.{3,3})\.CSV$/';
        foreach ($list as $filename => $data) {
            if (preg_match($pattern, $filename)) {
                $result[$filename] = $data;
            }
        }
        return $result;
    }
}
