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
 * @package     Errors
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 /**
 * Error processor
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Error_Processor
{
    const MAGE_ERRORS_LOCAL_XML = 'local.xml';
    const MAGE_ERRORS_DESIGN_XML = 'design.xml';
    const DEFAULT_SKIN = 'default';
    const ERROR_DIR = 'errors';

    /**
     * Page title
     *
     * @var string
    */
    public $pageTitle;

    /**
     * Skin URL
     *
     * @var string
    */
    public $skinUrl;

    /**
     * Base URL
     *
     * @var string
    */
    public $baseUrl;

    /**
     * Post data
     *
     * @var array
    */
    public $postData;

    /**
     * Report data
     *
     * @var array
    */
    public $reportData;

    /**
     * Report action
     *
     * @var string
    */
    public $reportAction;

    /**
     * Report ID
     *
     * @var int
     */
    public $reportId;

    /**
     * Report file
     *
     * @var string
    */
    protected $_reportFile;

    /**
     * Show error message
     *
     * @var bool
    */
    public $showErrorMsg;

    /**
     * Show message after sending email
     *
     * @var bool
    */
    public $showSentMsg;

    /**
     * Show form for sending
     *
     * @var bool
    */
    public $showSendForm;

    /**
     * Server script name
     *
     * @var string
    */
    protected $_scriptName;

    /**
     * Is root
     *
     * @var bool
    */
    protected $_root;

    /**
     * Internal config object
     *
     * @var stdClass
    */
    protected $_config;

    public function __construct()
    {
        $this->_errorDir  = dirname(__FILE__) . '/';
        $this->_reportDir = dirname($this->_errorDir) . '/var/report/';

        if (!empty($_SERVER['SCRIPT_NAME'])) {
            if (in_array(basename($_SERVER['SCRIPT_NAME'],'.php'), array('404','503','report'))) {
                $this->_scriptName = dirname($_SERVER['SCRIPT_NAME']);
            }
            else {
                $this->_scriptName = $_SERVER['SCRIPT_NAME'];
            }
        }

        $reportId = (isset($_GET['id'])) ? (int)$_GET['id'] : null;
        if ($reportId) {
            $this->loadReport($reportId);
        }

        $this->_indexDir = $this->_getIndexDir();
        $this->_root  = is_dir($this->_indexDir.'app');

        $this->_prepareConfig();
        if (isset($_GET['skin'])) {
            $this->_setSkin($_GET['skin']);
        }
    }

    /**
     * Process 404 error
    */
    public function process404()
    {
        $this->pageTitle = 'Error 404: Not Found';
        $this->_sendHeaders(404);
        $this->_renderPage('404.phtml');
    }

    /**
     * Process 503 error
    */
    public function process503()
    {
        $this->pageTitle = 'Error 503: Service Unavailable';
        $this->_sendHeaders(503);
        $this->_renderPage('503.phtml');
    }

    /**
     * Process report
    */
    public function processReport()
    {
        $this->pageTitle = 'There has been an error processing your request';
        $this->_sendHeaders(503);

        $this->showErrorMsg = false;
        $this->showSentMsg  = false;
        $this->showSendForm = false;
        $this->reportAction = $this->_config->action;
        $this->_setReportUrl();

        if($this->reportAction == 'email') {
            $this->showSendForm = true;
            $this->sendReport();
        }
        $this->_renderPage('report.phtml');
    }

    /**
     * Retrieve skin URL
     *
     * @return string
     */
    public function getSkinUrl()
    {
        return $this->getBaseUrl() . self::ERROR_DIR. '/' . $this->_config->skin . '/';
    }

    /**
     * Retrieve base host URL without path
     *
     * @return string
     */
    public function getHostUrl()
    {
        /**
         * Define server http host
         */
        if (!empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else {
            $host = 'localhost';
        }

        $isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off');
        $url = ($isSecure ? 'https://' : 'http://') . $host;

        if (!empty($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], array(80, 433))) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }
        return  $url;
    }

    /**
     * Retrieve base URL
     *
     * @return string
     */
    public function getBaseUrl($param = false)
    {
        $path = $this->_scriptName;

        if($param && !$this->_root) {
            $path = dirname($path);
        }

        $basePath = str_replace('\\', '/', dirname($path));
        return $this->getHostUrl() . ('/' == $basePath ? '' : $basePath) . '/';
    }

    /**
     * Retrieve client IP address
     *
     * @return string
     */
    protected function _getClientIp()
    {
        return (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'undefined';
    }

    /**
     * Get index dir
     *
     * @return string
     */
    protected function _getIndexDir()
    {
        $documentRoot = '';
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'],'/');
        }
        return dirname($documentRoot . $this->_scriptName) . '/';
    }

    /**
     * Prepare config data
     */
    protected function _prepareConfig()
    {
        $local  = $this->_loadXml(self::MAGE_ERRORS_LOCAL_XML);
        $design = $this->_loadXml(self::MAGE_ERRORS_DESIGN_XML);

        //initial settings
        $config = new stdClass();
        $config->action         = '';
        $config->subject        = 'Store Debug Information';
        $config->email_address  = '';
        $config->trash          = 'leave';
        $config->skin           = self::DEFAULT_SKIN;

        //combine xml data to one object
        if (!is_null($design) && (string)$design->skin) {
            $this->_setSkin((string)$design->skin, $config);
        }
        if (!is_null($local)) {
            if ((string)$local->report->action) {
                $config->action = $local->report->action;
            }
            if ((string)$local->report->subject) {
                $config->subject = $local->report->subject;
            }
            if ((string)$local->report->email_address) {
                $config->email_address = $local->report->email_address;
            }
            if ((string)$local->report->trash) {
                $config->trash = $local->report->trash;
            }
            if ((string)$local->skin) {
                $this->_setSkin((string)$local->skin, $config);
            }
        }
        if ((string)$config->email_address == '' && (string)$config->action == 'email') {
            $config->action = '';
        }

        $this->_config = $config;
    }

    /**
     * Load xml file
     *
     * @param string $config
     * return SimpleXMLElement
     */
    protected function _loadXml($xmlFile)
    {
        $configPath = $this->_getFilePath($xmlFile);
        return ($configPath) ? simplexml_load_file($configPath) : null;
    }

    /**
     * Send error headers
     *
     * @param int $statusCode
     */
    protected function _sendHeaders($statusCode)
    {
        $serverProtocol = !empty($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        switch ($statusCode) {
            case 404:
                $description = 'Not Found';
                break;
            case 503:
                $description = 'Service Unavailable';
                break;
            default:
                $description = '';
                break;
        }

        header(sprintf('%s %s %s', $serverProtocol, $statusCode, $description), true, $statusCode);
        header(sprintf('Status: %s %s', $statusCode, $description), true, $statusCode);
    }

    /**
     * Render page
     */
    protected function _renderPage($template)
    {
        $baseTemplate = $this->_getTemplatePath('page.phtml');
        $contentTemplate = $this->_getTemplatePath($template);

        if ($baseTemplate && $contentTemplate) {
            require_once $baseTemplate;
        }
    }

    /**
     * Find file path
     *
     * @param string $file
     * @param array $directories
     * return $string
     */
    protected function _getFilePath($file, $directories = null)
    {
        if (is_null($directories)) {
            $directories = array();

            if (!$this->_root) {
                $directories[] = $this->_indexDir . self::ERROR_DIR . '/';
            }
            $directories[] = $this->_errorDir;
        }

        foreach ($directories as $directory) {
            if (file_exists($directory . $file)) {
                return $directory . $file;
            }
        }
    }

    /**
     * Find template path
     *
     * @param string $template
     * return $string
     */
    protected function _getTemplatePath($template)
    {
        $directories = array();

        if (!$this->_root) {
            $directories[] = $this->_indexDir . self::ERROR_DIR. '/'. $this->_config->skin . '/';

            if ($this->_config->skin != self::DEFAULT_SKIN) {
                $directories[] = $this->_indexDir . self::ERROR_DIR . '/'. self::DEFAULT_SKIN . '/';
            }
        }

        $directories[] = $this->_errorDir . $this->_config->skin . '/';

        if ($this->_config->skin != self::DEFAULT_SKIN) {
            $directories[] = $this->_errorDir . self::DEFAULT_SKIN . '/';
        }

        return $this->_getFilePath($template, $directories);
    }

    /**
     * Set report data
     *
     * @param array $reportData
     */
    protected function _setReportData($reportData)
    {
        $this->reportData = $reportData;

        if (!isset($reportData['url'])) {
            $this->reportData['url'] = '';
        }
        else {
            $this->reportData['url'] = $this->getHostUrl() . $reportData['url'];
        }

        if ($this->reportData['script_name']) {
            $this->_scriptName = $this->reportData['script_name'];
        }
    }

    /**
     * Create report
     *
     * @param array $reportData
     */
    public function saveReport($reportData)
    {
        $this->reportData = $reportData;
        $this->reportId   = abs(intval(microtime(true) * rand(100, 1000)));
        $this->_reportFile = $this->_reportDir . '/' . $this->reportId;
        $this->_setReportData($reportData);

        if (!file_exists($this->_reportDir)) {
            @mkdir($this->_reportDir, 0750, true);
        }

        @file_put_contents($this->_reportFile, serialize($reportData));
        @chmod($this->_reportFile, 0640);

        if (isset($reportData['skin']) && self::DEFAULT_SKIN != $reportData['skin']) {
            $this->_setSkin($reportData['skin']);
        }
        $this->_setReportUrl();

        if (headers_sent()) {
            print '<script type="text/javascript">';
            print "window.location.href = '{$this->reportUrl}';";
            print '</script>';
            exit;
        }
    }

    /**
     * Get report
     *
     * @param int $reportId
     */
    public function loadReport($reportId)
    {
        $this->reportId = $reportId;
        $this->_reportFile = $this->_reportDir . '/' . $reportId;

        if (!file_exists($this->_reportFile) || !is_readable($this->_reportFile)) {
            header("Location: " . $this->getBaseUrl());
            die();
        }
        $this->_setReportData(unserialize(file_get_contents($this->_reportFile)));
    }

    /**
     * Send report
     *
     */
    public function sendReport()
    {
        $this->pageTitle = 'Error Submission Form';

        $this->postData['firstName'] = (isset($_POST['firstname'])) ? trim(htmlspecialchars($_POST['firstname'])) : '';
        $this->postData['lastName']  = (isset($_POST['lastname'])) ? trim(htmlspecialchars($_POST['lastname'])) : '';
        $this->postData['email']     = (isset($_POST['email'])) ? trim(htmlspecialchars($_POST['email'])) : '';
        $this->postData['telephone'] = (isset($_POST['telephone'])) ? trim(htmlspecialchars($_POST['telephone'])) : '';
        $this->postData['comment']   = (isset($_POST['comment'])) ? trim(htmlspecialchars($_POST['comment'])) : '';

        if (isset($_POST['submit'])) {
            if ($this->_validate()) {

                $msg  = "URL: {$this->reportData['url']}\n"
                    . "IP Address: {$this->_getClientIp()}\n"
                    . "First Name: {$this->postData['firstName']}\n"
                    . "Last Name: {$this->postData['lastName']}\n"
                    . "E-mail Address: {$this->postData['email']}\n";
                if ($this->postData['telephone']) {
                    $msg .= "Telephone: {$this->postData['telephone']}\n";
                }
                if ($this->postData['comment']) {
                    $msg .= "Comment: {$this->postData['comment']}\n";
                }

                $subject = sprintf('%s [%s]', (string)$this->_config->subject, $this->reportId);
                @mail((string)$this->_config->email_address, $subject, $msg);

                $this->showSendForm = false;
                $this->showSentMsg  = true;
            } else {
                $this->showErrorMsg = true;
            }
        } else {
            $time = gmdate('Y-m-d H:i:s \G\M\T');

            $msg = "URL: {$this->reportData['url']}\n"
                . "IP Address: {$this->_getClientIp()}\n"
                . "Time: {$time}\n"
                . "Error:\n{$this->reportData[0]}\n\n"
                . "Trace:\n{$this->reportData[1]}";

            $subject = sprintf('%s [%s]', (string)$this->_config->subject, $this->reportId);
            @mail((string)$this->_config->email_address, $subject, $msg);

            if ($this->_config->trash == 'delete') {
                @unlink($this->_reportFile);
            }
        }
    }

    /**
     * Validate submitted post data
     *
     * @return bool
     */
    protected function _validate()
    {
        $email = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
            $this->postData['email']);
        return ($this->postData['firstName'] && $this->postData['lastName'] && $email);
    }

    /**
     * Skin setter
     *
     * @param string $value
     * @param stdClass $config
     */
    protected function _setSkin($value, stdClass $config = null)
    {
        if (preg_match('/^[a-z0-9_]+$/i', $value) && is_dir($this->_indexDir . self::ERROR_DIR . '/' . $value)) {
            if (!$config) {
                if ($this->_config) {
                    $config = $this->_config;
                }
            }
            if ($config) {
                $config->skin = $value;
            }
        }
    }

    /**
     * Set current report URL from current params
     */
    protected function _setReportUrl()
    {
        if ($this->reportId && $this->_config && isset($this->_config->skin)) {
            $this->reportUrl = "{$this->getBaseUrl(true)}errors/report.php?" . http_build_query(array(
                'id' => $this->reportId, 'skin' => $this->_config->skin
            ));
        }
    }
}
