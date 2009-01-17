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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Config installer
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Config extends Mage_Install_Model_Installer_Abstract
{
    const TMP_INSTALL_DATE_VALUE= 'd-d-d-d-d';
    const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

    /**
     * Path to local configuration file
     *
     * @var string
     */
    protected $_localConfigFile;

    protected $_configData = array();

    public function __construct()
    {
        $this->_localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
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
        foreach (Mage::getModel('core/config')->getDistroServerVars() as $index=>$value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }

        if (isset($data['unsecure_base_url'])) {
            $data['unsecure_base_url'] .= substr($data['unsecure_base_url'],-1) != '/' ? '/' : '';
            if (!$this->_getInstaller()->getDataModel()->getSkipBaseUrlValidation()) {
                $this->_checkUrl($data['unsecure_base_url']);
            }
        }
        if (isset($data['secure_base_url'])) {
            $data['secure_base_url'] .= substr($data['secure_base_url'],-1) != '/' ? '/' : '';

            if (!empty($data['use_secure'])
                && !$this->_getInstaller()->getDataModel()->getSkipUrlValidation()) {
                $this->_checkUrl($data['secure_base_url']);
            }
        }

        $data['date']   = self::TMP_INSTALL_DATE_VALUE;
        $data['key']    = self::TMP_ENCRYPT_KEY_VALUE;
        $data['var_dir'] = $data['root_dir'] . '/var';

        $data['use_script_name'] = isset($data['use_script_name']) ? 'true' : 'false';

        $this->_getInstaller()->getDataModel()->setConfigData($data);

        $template = file_get_contents(Mage::getBaseDir('etc').DS.'local.xml.template');
        foreach ($data as $index=>$value) {
            $template = str_replace('{{'.$index.'}}', '<![CDATA['.$value.']]>', $template);
        }
        file_put_contents($this->_localConfigFile, $template);
        chmod($this->_localConfigFile, 0777);
    }

    public function getFormData()
    {
        $uri = Zend_Uri::factory(Mage::getBaseUrl('web'));

        $baseUrl = $uri->getUri();
        if ($uri->getScheme()!=='https') {
            $uri->setPort(null);
            $baseSecureUrl = str_replace('http://', 'https://', $uri->getUri());
        } else {
            $baseSecureUrl = $uri->getUri();
        }

        $data = Mage::getModel('varien/object')
            ->setDbHost('localhost')
            ->setDbName('magento')
            ->setDbUser('root')
            ->setDbPass('')
            ->setSecureBaseUrl($baseSecureUrl)
            ->setUnsecureBaseUrl($baseUrl)
            ->setAdminFrontname('admin')
        ;
        return $data;
    }

    protected function _checkHostsInfo($data)
    {
        $url = $data['protocol'] . '://' . $data['host'] . ':' . $data['port'] . $data['base_path'];
        $surl= $data['secure_protocol'] . '://' . $data['secure_host'] . ':' . $data['secure_port'] . $data['secure_base_path'];

        $this->_checkUrl($url);
        $this->_checkUrl($surl, true);

        return $this;
    }

    protected function _checkUrl($url, $secure=false)
    {
        $prefix = $secure ? 'install/wizard/checkSecureHost/' : 'install/wizard/checkHost/';
        $client = new Varien_Http_Client($url.'index.php/'.$prefix);
        try {
            $response = $client->request('GET');
            /* @var $responce Zend_Http_Response */
            $body = $response->getBody();
        }
        catch (Exception $e){
            $this->_getInstaller()->getDataModel()->addError(Mage::helper('install')->__('Url "%s" is not accessible', $url));
            throw $e;
        }

        if ($body != Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE) {
            $this->_getInstaller()->getDataModel()->addError(Mage::helper('install')->__('Url "%s" is invalid', $url));
            Mage::throwException(Mage::helper('install')->__('This Url is invalid'));
        }
        return $this;
    }

    public function replaceTmpInstallDate($date = null)
    {
        if (is_null($date)) {
            $date = date('r');
        }
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_INSTALL_DATE_VALUE, date('r'), $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }

    public function replaceTmpEncryptKey($key = null)
    {
        if (!$key) {
            $key = md5(time());
        }
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_ENCRYPT_KEY_VALUE, $key, $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }
}