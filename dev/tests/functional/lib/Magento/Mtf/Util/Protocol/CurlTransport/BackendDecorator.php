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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Magento\Mtf\Util\Protocol\CurlTransport;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Curl transport on backend.
 */
class BackendDecorator implements CurlInterface
{
    /**
     * Event Manager.
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Curl transport protocol.
     *
     * @var CurlTransport
     */
    protected $transport;

    /**
     * Form key.
     *
     * @var string
     */
    protected $formKey = null;

    /**
     * Response data.
     *
     * @var string
     */
    protected $response;

    /**
     * System config.
     *
     * @var DataInterface
     */
    protected $configuration;

    /**
     * @constructor
     * @param CurlTransport $transport
     * @param DataInterface $configuration
     */
    public function __construct(CurlTransport $transport, DataInterface $configuration)
    {
        $this->transport = $transport;
        $this->configuration = $configuration;
        $this->authorize();
    }

    /**
     * Authorize customer on backend.
     *
     * @throws \Exception
     * @return void
     */
    protected function authorize()
    {
        // Perform GET to backend url so form_key is set
        $url = $_ENV['app_backend_url'];
        $this->transport->write($url, [], CurlInterface::GET);
        $this->read();

        $data = [
            'login[username]' => $this->configuration->get('application/0/backendLogin/0/value'),
            'login[password]' => $this->configuration->get('application/0/backendPassword/0/value'),
            'form_key' => $this->formKey,
        ];
        $this->transport->write($url, $data, CurlInterface::POST, []);
        $response = $this->read();

        if (!strpos($response, 'link-logout')) {
            throw new \Exception(
                "Admin user cannot be logged in by curl handler!"
            );
        }
    }

    /**
     * Init Form Key from response.
     *
     * @return void
     */
    protected function initFormKey()
    {
        preg_match('!var FORM_KEY = \'(\w+)\';!', $this->response, $matches);

        if (!empty($matches[1])) {
            $this->formKey = $matches[1];
        } else {
            preg_match('!input name="form_key" type="hidden" value="(\w+)"!', $this->response, $matches);
            if (!empty($matches[1])) {
                $this->formKey = $matches[1];
            }
        }
    }

    /**
     * Send request to the remote server.
     *
     * @param string $url
     * @param mixed $params
     * @param string $method
     * @param mixed $headers
     * @return void
     * @throws \Exception
     */
    public function write($url, $params = [], $method = CurlInterface::POST, $headers = [])
    {
        if ($this->formKey) {
            $params['form_key'] = $this->formKey;
        } else {
            throw new \Exception(sprintf('Form key is absent! Url: "%s" Response: "%s"', $url, $this->response));
        }
        $this->transport->write($url, http_build_query($params), $method, $headers);
    }

    /**
     * Read response from server.
     *
     * @return string
     */
    public function read()
    {
        $this->response = $this->transport->read();
        $this->initFormKey();
        return $this->response;
    }

    /**
     * Add additional option to cURL.
     *
     * @param int $option the CURLOPT_* constants
     * @param mixed $value
     * @return void
     */
    public function addOption($option, $value)
    {
        $this->transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close()
    {
        $this->transport->close();
    }
}
