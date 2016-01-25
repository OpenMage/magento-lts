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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Util\Protocol;

/**
 * Curl Interface.
 */
interface CurlInterface
{
    /**
     * HTTP request methods.
     */
    const GET   = 'GET';

    /**
     * Protocol type.
     */
    const POST  = 'POST';

    /**
     * Add additional option to cURL.
     *
     * @param  int $option
     * @param  mixed $value
     * @return void
     */
    public function addOption($option, $value);

    /**
     * Send request to the remote server.
     *
     * @param string $method
     * @param string $url
     * @param string $http_ver
     * @param array  $headers
     * @param array  $params
     * @return void
     */
    public function write($method, $url, $http_ver = '1.1', $headers = [], $params = []);

    /**
     * Read response from server.
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close();
}
