<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Config installer
 *
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Config extends Mage_Install_Model_Installer_Abstract
{
    public const TMP_INSTALL_DATE_VALUE = 'd-d-d-d-d';

    public const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

    /**
     * Path to local configuration file
     *
     * @var string
     */
    protected $_localConfigFile;

    protected $_configData = [];

    public function __construct()
    {
        $this->_localConfigFile = Mage::getBaseDir('etc') . DS . 'local.xml';
    }

    public function setConfigData($data)
    {
        if (is_array($data)) {
            $this->_configData = $data;
        }

        return $this;
    }

    public function getConfigData()
    {
        return $this->_configData;
    }

    public function install()
    {
        $data = $this->getConfigData();
        foreach (Mage::getModel('core/config')->getDistroServerVars() as $index => $value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }

        if (isset($data['unsecure_base_url'])) {
            $data['unsecure_base_url'] .= !str_ends_with($data['unsecure_base_url'], '/') ? '/' : '';
            if (!str_starts_with($data['unsecure_base_url'], 'http')) {
                $data['unsecure_base_url'] = 'http://' . $data['unsecure_base_url'];
            }

            if (!$this->_getInstaller()->getDataModel()->getSkipBaseUrlValidation()) {
                $this->_checkUrl($data['unsecure_base_url']);
            }
        }

        if (isset($data['secure_base_url'])) {
            $data['secure_base_url'] .= !str_ends_with($data['secure_base_url'], '/') ? '/' : '';
            if (!str_starts_with($data['secure_base_url'], 'http')) {
                $data['secure_base_url'] = 'https://' . $data['secure_base_url'];
            }

            if (!empty($data['use_secure'])
                && !$this->_getInstaller()->getDataModel()->getSkipUrlValidation()
            ) {
                $this->_checkUrl($data['secure_base_url']);
            }
        }

        $data['date']   = self::TMP_INSTALL_DATE_VALUE;
        $data['key']    = self::TMP_ENCRYPT_KEY_VALUE;
        $data['var_dir'] = $data['root_dir'] . '/var';

        $data['use_script_name'] = isset($data['use_script_name']) ? 'true' : 'false';

        $this->_getInstaller()->getDataModel()->setConfigData($data);

        $template = file_get_contents(Mage::getBaseDir('etc') . DS . 'local.xml.template');
        foreach ($data as $index => $value) {
            $template = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $template);
        }

        file_put_contents($this->_localConfigFile, $template);
        chmod($this->_localConfigFile, 0777);
    }

    public function getFormData()
    {
        $baseUrl = Mage::helper('core/url')->decodePunycode(Mage::getBaseUrl('web'));
        $uri    = explode(':', $baseUrl, 2);
        $scheme = strtolower($uri[0]);
        $baseSecureUrl = ($scheme !== 'https') ? str_replace('http://', 'https://', $baseUrl) : $baseUrl;

        $connectDefault = Mage::getConfig()
                ->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);

        return Mage::getModel('varien/object')
            ->setDbHost($connectDefault->host)
            ->setDbName($connectDefault->dbname)
            ->setDbUser($connectDefault->username)
            ->setDbModel($connectDefault->model)
            ->setDbPass('')
            ->setSecureBaseUrl($baseSecureUrl)
            ->setUnsecureBaseUrl($baseUrl)
            ->setAdminFrontname('admin')
            ->setEnableCharts('1');
    }

    /**
     * @param array $data
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Http_Client_Exception
     */
    protected function _checkHostsInfo($data)
    {
        $url  = $data['protocol'] . '://' . $data['host'] . ':' . $data['port'] . $data['base_path'];
        $surl = $data['secure_protocol'] . '://' . $data['secure_host'] . ':' . $data['secure_port']
            . $data['secure_base_path'];

        $this->_checkUrl($url);
        $this->_checkUrl($surl, true);

        return $this;
    }

    /**
     * @param string $url
     * @param bool $secure
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Http_Client_Exception
     */
    protected function _checkUrl($url, $secure = false)
    {
        $prefix = $secure ? 'install/wizard/checkSecureHost/' : 'install/wizard/checkHost/';
        try {
            $client = new Varien_Http_Client($url . 'index.php/' . $prefix);
            $response = $client->request('GET');
            $body = $response->getBody();
        } catch (Exception $exception) {
            $this->_getInstaller()->getDataModel()
                ->addError(Mage::helper('install')->__('The URL "%s" is not accessible.', $url));
            throw $exception;
        }

        if ($body != Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE) {
            $this->_getInstaller()->getDataModel()
                ->addError(Mage::helper('install')->__('The URL "%s" is invalid.', $url));
            Mage::throwException(Mage::helper('install')->__("Response from server isn't valid."));
        }

        return $this;
    }

    public function replaceTmpInstallDate($date = null)
    {
        $stamp    = strtotime((string) $date);
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_INSTALL_DATE_VALUE, date('r', $stamp ? $stamp : time()), $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }

    /**
     * @param null|string $key
     * @return $this
     */
    public function replaceTmpEncryptKey($key = null)
    {
        if (!$key) {
            $key = md5(Mage::helper('core')->getRandomString(10));
        }

        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_ENCRYPT_KEY_VALUE, $key, $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }
}
