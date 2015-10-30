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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Util\Protocol\CurlTransport;

use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Config\DataInterface;

/**
 * Backend decorator.
 */
class BackendDecorator implements CurlInterface
{
    /**
     * @var \Magento\Mtf\Util\Protocol\CurlTransport
     */
    protected $_transport;

    /**
     * @var DataInterface
     */
    protected $_configuration;

    /**
     * @var string
     */
    protected $_formKey = null;

    /**
     * @var string
     */
    protected $_response;

    /**
     * @constructor
     * @param CurlTransport $transport
     * @param DataInterface $configuration
     */
    public function __construct(CurlTransport $transport, DataInterface $configuration)
    {
        $this->_transport = $transport;
        $this->_configuration = $configuration;
        $this->_authorize();
    }

    /**
     * Authorize customer on backend.
     *
     * @throws \Exception
     * @return void
     */
    protected function _authorize()
    {
        $url = $_ENV['app_backend_url'];
        $data = [
            'login[username]' => $this->_configuration->get('application/0/backendLogin/0/value'),
            'login[password]' => $this->_configuration->get('application/0/backendPassword/0/value')
        ];
        $this->_transport->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $this->read();
        if (!strpos($response, 'link-logout')) {
            throw new \Exception("Admin user cannot be logged in by curl handler!\n Post url: $url");
        }
    }

    /**
     * Init Form Key from response.
     *
     * @return void
     */
    protected function _initFormKey()
    {
        preg_match('!var FORM_KEY = \'(\w+)\';!', $this->_response, $matches);
        if (!empty($matches[1])) {
            $this->_formKey = $matches[1];
        }
    }

    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param string $http_ver
     * @param array $headers
     * @param array $params
     * @return void
     *
     * @throws \Exception
     */
    public function write($method, $url, $http_ver = '1.1', $headers = [], $params = [])
    {
        if ($this->_formKey) {
            $params['form_key'] = $this->_formKey;
            isset($params['data'])
                ? $params['data'] = preg_replace('!formKey!', $this->_formKey, $params['data'])
                : null;
        } else {
            throw new \Exception('Form key is absent! Response: \n'
                . "Url:" . $url
                . "Response:" . $this->_response);
        }
        $this->_transport->write($method, $url, $http_ver, $headers, http_build_query($params));
    }

    /**
     * Read response from server.
     *
     * @return string
     */
    public function read()
    {
        $this->_response = $this->_transport->read();
        $this->_initFormKey();
        return $this->_response;
    }

    /**
     * Add additional option to cURL.
     *
     * @param  int $option
     * @param  mixed $value
     * @return void
     */
    public function addOption($option, $value)
    {
        $this->_transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close()
    {
        $this->_transport->close();
    }
}
