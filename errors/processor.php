<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Errors
 */

/**
 * Error processor
 */
class Error_Processor
{
    public const MAGE_ERRORS_LOCAL_XML = 'local.xml';

    public const MAGE_ERRORS_DESIGN_XML = 'design.xml';

    public const DEFAULT_SKIN = 'default';

    public const DEFAULT_TRASH_MODE = 'leave';

    public const ERROR_DIR = 'errors';

    /** @var string */
    public $pageTitle;

    /** @var string */
    public $baseUrl;

    /** @var array */
    public $postData;

    /** @var array */
    public $reportData;

    /** @var string */
    public $reportAction;

    /** @var int */
    public $reportId;

    /** @var string */
    public $reportUrl;

    /** @var string report file name */
    protected $_reportFile;

    /** @var bool */
    public $showErrorMsg;

    /**
     * Show message after sending email
     *
     * @var bool
     */
    public $showSentMsg;

    /** @var bool */
    public $showSendForm;

    /**
     * Server script name
     *
     * @var string
     */
    protected $_scriptName;

    /** @var bool */
    protected $_root;

    /** @var string */
    protected $_errorDir;

    /** @var string */
    protected $_reportDir;

    /** @var string */
    protected $_indexDir;

    /**
     * Internal config object
     *
     * @var stdClass
     */
    protected $_config;

    public function __construct()
    {
        $this->_errorDir  = __DIR__ . '/';
        $this->_reportDir = dirname($this->_errorDir) . '/var/report/';

        if (!empty($_SERVER['SCRIPT_NAME'])) {
            if (in_array(basename($_SERVER['SCRIPT_NAME'], '.php'), ['404','503','report'])) {
                $this->_scriptName = dirname($_SERVER['SCRIPT_NAME']);
            } else {
                $this->_scriptName = $_SERVER['SCRIPT_NAME'];
            }
        }

        $reportId = (isset($_GET['id'])) ? (int) $_GET['id'] : null;
        if ($reportId) {
            $this->loadReport($reportId);
        }

        $this->_indexDir = $this->_getIndexDir();
        $this->_root  = is_dir($this->_indexDir . 'app');

        $this->_prepareConfig();
        if (isset($_SERVER['MAGE_ERRORS_SKIN']) || isset($_GET['skin'])) {
            $this->_setSkin($_SERVER['MAGE_ERRORS_SKIN'] ?? $_GET['skin']);
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

        if ($this->reportAction === 'email') {
            $this->showSendForm = true;
            $this->sendReport();
        }

        $this->_renderPage('report.phtml');
    }

    public function getSkinUrl(): string
    {
        return $this->getBaseUrl() . self::ERROR_DIR . '/' . $this->_config->skin . '/';
    }

    /**
     * Retrieve base host URL without path
     */
    public function getHostUrl(): string
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

        $isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off');
        $url = ($isSecure ? 'https://' : 'http://')
            . htmlspecialchars($host, ENT_COMPAT | ENT_HTML401, 'UTF-8');

        if (!empty($_SERVER['SERVER_PORT'])
            && preg_match('/\d+/', $_SERVER['SERVER_PORT'])
            && !in_array($_SERVER['SERVER_PORT'], [80, 433])
            && !str_ends_with($host, ':' . $_SERVER['SERVER_PORT'])
        ) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        return  $url;
    }

    public function getBaseUrl(bool $param = false): string
    {
        $path = $this->_scriptName;

        if ($param && !$this->_root) {
            $path = dirname($path);
        }

        $basePath = str_replace('\\', '/', dirname($path));
        return $this->getHostUrl() . ($basePath === '/' ? '' : $basePath) . '/';
    }

    /**
     * Retrieve client IP address
     */
    protected function _getClientIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'undefined';
    }

    protected function _getIndexDir(): string
    {
        $documentRoot = '';
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
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
        $config->trash          = self::DEFAULT_TRASH_MODE;
        $config->skin           = self::DEFAULT_SKIN;

        //combine xml data to one object
        if ($design !== null && ($skin = (string) $design->skin)) {
            $this->_setSkin($skin, $config);
        }

        if ($local !== null) {
            if ($action = (string) $local->report->action) {
                $config->action = $action;
            }

            if ($subject = (string) $local->report->subject) {
                $config->subject = $subject;
            }

            if ($emailAddress = (string) $local->report->email_address) {
                $config->email_address = $emailAddress;
            }

            if ($trash = (string) $local->report->trash) {
                $config->trash = $trash;
            }

            if ($localSkin = (string) $local->skin) {
                $this->_setSkin($localSkin, $config);
            }
        }

        if ($config->email_address === '' && $config->action === 'email') {
            $config->action = '';
        }

        $this->_config = $config;
    }

    /**
     * Load xml file
     *
     * @param string $xmlFile file name
     * @return null|SimpleXMLElement
     */
    protected function _loadXml(string $xmlFile)
    {
        $configPath = $this->_getFilePath($xmlFile);
        return ($configPath) ? simplexml_load_file($configPath) : null;
    }

    /**
     * Send error headers
     */
    protected function _sendHeaders(int $statusCode)
    {
        $serverProtocol = !empty($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        $description = match ($statusCode) {
            404 => 'Not Found',
            503 => 'Service Unavailable',
            default => '',
        };

        header(sprintf('%s %s %s', $serverProtocol, $statusCode, $description), true, $statusCode);
        header(sprintf('Status: %s %s', $statusCode, $description), true, $statusCode);
    }

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
     * @param null|array $directories
     * @return null|string
     */
    protected function _getFilePath(string $file, $directories = null)
    {
        if ($directories === null) {
            $directories = [];

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

        return null;
    }

    /**
     * Find template path
     *
     * @return null|string
     */
    protected function _getTemplatePath(string $template)
    {
        $directories = [];

        if (!$this->_root) {
            $directories[] = $this->_indexDir . self::ERROR_DIR . '/' . $this->_config->skin . '/';

            if ($this->_config->skin !== self::DEFAULT_SKIN) {
                $directories[] = $this->_indexDir . self::ERROR_DIR . '/' . self::DEFAULT_SKIN . '/';
            }
        }

        $directories[] = $this->_errorDir . $this->_config->skin . '/';

        if ($this->_config->skin !== self::DEFAULT_SKIN) {
            $directories[] = $this->_errorDir . self::DEFAULT_SKIN . '/';
        }

        return $this->_getFilePath($template, $directories);
    }

    protected function _setReportData(array $reportData)
    {
        $this->reportData = $reportData;

        if (isset($reportData['url'])) {
            $this->reportData['url'] = $this->getHostUrl()
                . htmlspecialchars($reportData['url'], ENT_COMPAT | ENT_HTML401, 'UTF-8');
        } else {
            $this->reportData['url'] = '';
        }

        if (isset($this->reportData['script_name'])) {
            $this->_scriptName = $this->reportData['script_name'];
        }
    }

    /**
     * @throws Exception
     */
    public function saveReport(array $reportData)
    {
        $this->reportData = $reportData;
        $this->reportId   = abs((int) (microtime(true) * random_int(100, 1000)));
        $this->_reportFile = $this->_reportDir . '/' . $this->reportId;
        $this->_setReportData($reportData);

        if (!file_exists($this->_reportDir)) {
            @mkdir($this->_reportDir, 0750, true);
        }

        $reportData = array_map(strip_tags(...), $reportData);
        @file_put_contents($this->_reportFile, serialize($reportData));
        @chmod($this->_reportFile, 0640);

        if (isset($reportData['skin']) && self::DEFAULT_SKIN !== $reportData['skin']) {
            $this->_setSkin($reportData['skin']);
        }

        $this->_setReportUrl();

        if (headers_sent()) {
            echo '<script type="text/javascript">';
            echo "window.location.href = encodeURI('{$this->reportUrl}');";
            echo '</script>';
            exit();
        }
    }

    /**
     * @return no-return|void
     */
    public function loadReport(int $reportId)
    {
        $reportData = false;
        $this->reportId = $reportId;
        $this->_reportFile = $this->_reportDir . '/' . $reportId;

        if (!file_exists($this->_reportFile) || !is_readable($this->_reportFile)) {
            header('Location: ' . $this->getBaseUrl());
            exit();
        }

        $reportContent = file_get_contents($this->_reportFile);
        if (!preg_match('/[oc]:[+\-]?\d+:"/i', $reportContent)) {
            $reportData = unserialize($reportContent, ['allowed_classes' => false]);
        }

        if (is_array($reportData)) {
            $this->_setReportData($reportData);
        }
    }

    /**
     * @return void
     */
    public function sendReport()
    {
        $this->pageTitle = 'Error Submission Form';

        $this->postData['firstName'] = (isset($_POST['firstname'])) ? trim(htmlspecialchars($_POST['firstname'])) : '';
        $this->postData['lastName']  = (isset($_POST['lastname'])) ? trim(htmlspecialchars($_POST['lastname'])) : '';
        $this->postData['email']     = (isset($_POST['email'])) ? trim(htmlspecialchars($_POST['email'])) : '';
        $this->postData['telephone'] = (isset($_POST['telephone'])) ? trim(htmlspecialchars($_POST['telephone'])) : '';
        $this->postData['comment']   = (isset($_POST['comment'])) ? trim(htmlspecialchars($_POST['comment'])) : '';
        $url = htmlspecialchars($this->reportData['url'], ENT_COMPAT | ENT_HTML401);

        if (isset($_POST['submit'])) {
            if ($this->_validate()) {
                $msg  = "URL: {$url}\n"
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

                $subject = sprintf('%s [%s]', $this->_config->subject, $this->reportId);
                @mail($this->_config->email_address, $subject, $msg);

                $this->showSendForm = false;
                $this->showSentMsg  = true;
            } else {
                $this->showErrorMsg = true;
            }
        } else {
            $time = gmdate('Y-m-d H:i:s \G\M\T');

            $msg = "URL: {$url}\n"
                . "IP Address: {$this->_getClientIp()}\n"
                . "Time: {$time}\n"
                . "Error:\n{$this->reportData[0]}\n\n"
                . "Trace:\n{$this->reportData[1]}";

            $subject = sprintf('%s [%s]', $this->_config->subject, $this->reportId);
            @mail($this->_config->email_address, $subject, $msg);

            if ($this->_config->trash === 'delete') {
                @unlink($this->_reportFile);
            }
        }
    }

    /**
     * Validate submitted post data
     */
    protected function _validate(): bool
    {
        $email = preg_match(
            '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
            $this->postData['email'],
        );
        return ($this->postData['firstName'] && $this->postData['lastName'] && $email);
    }

    /**
     * @return void
     */
    protected function _setSkin(string $value, ?stdClass $config = null)
    {
        if (preg_match('/^[a-z0-9_]+$/i', $value) && is_dir($this->_errorDir . $value)) {
            if (!$config && $this->_config) {
                $config = $this->_config;
            }

            if ($config) {
                $config->skin = $value;
            }
        }
    }

    /**
     * Set current report URL from current params
     * @return void
     */
    protected function _setReportUrl()
    {
        if ($this->reportId && $this->_config && isset($this->_config->skin)) {
            $this->reportUrl = sprintf(
                '%serrors/report.php?%s',
                $this->getBaseUrl(true),
                http_build_query(['id' => $this->reportId, 'skin' => $this->_config->skin]),
            );
        }
    }
}
